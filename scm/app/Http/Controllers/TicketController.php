<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use App\Constant;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Jobs\SendMailToMKT;

class TicketController extends Controller {
    //
    public function saveTicket(Request $request) {
        try {
            $ticket_id = $request->input('ticket-id', 0);
            $video_id = $request->input('ticket-video-id', 0);
            $title = $request->input('ticket-title', '');
            $promote_time = $request->input('ticket-promote-time', '');
            $budget = $request->input('ticket-budget', 0);
            $gender = $request->input('ticket-gender', 0);
            $ages = $request->input('ticket-ages', '');
            $location = $request->input('ticket-location', '');
            $kind = $request->input('ticket-kind', 0);
            $keyword = $request->input('ticket-keyword', '');
            $note = $request->input('ticket-note', '');
            $wf_position = $request->input('ticket-workflow-position', 0);            
            $camp_id = $request->input('ticket-camp-id', '');     
            $select_mkt = $request->input('select_mkt', 0);

            $arr_promote_time = explode('-', $promote_time);
            $start_time = trim($arr_promote_time[0]) . ':00';   // DD/MM/YYYY HH:mm:ss
            $end_time = trim($arr_promote_time[1]) . ':59';     // DD/MM/YYYY HH:mm:ss

            $ticket = Ticket::find($ticket_id);

            $cmd = 'cmd=u';
            $cmd_message = '';

            if (empty($ticket)) {
                $cmd = 'cmd=n';
                $ticket = new Ticket();
                $ticket->video_id = $video_id;
                $ticket->user_id = Auth::user()->id;
                $ticket->mkt_user_id = $select_mkt;

                $old_workflow_position = -1;

                $cmd_message = 'Tạo mới ticket';
            } else {
                $old_workflow_position = $ticket->workflow_position;
            }

            $ticket->title = $title;
            $ticket->start_date = Carbon::createFromFormat('d/m/Y H:i:s', $start_time);            
            $ticket->end_date = Carbon::createFromFormat('d/m/Y H:i:s', $end_time);            
            $ticket->budget = $budget;
            $ticket->gender = $gender;
            $ticket->age = $ages;
            $ticket->location = $location;
            $ticket->kind = $kind;
            $ticket->keyword = $keyword;
            $ticket->note = $note;            
            $ticket->workflow_position = $wf_position;
            $ticket->campaign_id = $camp_id;
            
            $ticket->save();
            $cmd .= '&status=s';
            $new_workflow_position = $ticket->workflow_position;

            if ($old_workflow_position != -1 && $new_workflow_position != $old_workflow_position) {
                // Thay doi trang thai
                $cmd_message = Constant::TICKET_STATUS[$old_workflow_position] . ' => ' . Constant::TICKET_STATUS[$ticket->workflow_position];
            }

            $from_user = Auth::user();
            $to_user = null;
            $created_by = '';            

            // Neu user logged in la QTK, thi user nhan notify la MKT
            if ($from_user->permission === Constant::QTK) {
                $to_user = $ticket->marketer;
                $created_by = $from_user->name;
            } 
            
            if($from_user->permission === Constant::MKT) {
                $to_user = $ticket->creator;
                $created_by = $to_user->name;
            }

            if (!empty($from_user) && !empty($to_user) && $from_user->id != $to_user->id) {                
                $body = 
                    '<div>Bạn có thông báo mới từ Sconnect Content Management: <span style="color: #">'.$cmd_message.'</span></div>'
                    . '<div style="margin-top: 10px; padding: 15px; border: 1px solid #dddddd; border-radius: 5px;">'
                    . '<div><strong>Ticket:</strong><i>'.$ticket->title.'</i></div>'
                    . '<div><strong>Trạng thái:</strong> <i>' . ($old_workflow_position != $new_workflow_position ? Constant::TICKET_STATUS[$old_workflow_position] . ' => ' : '') . Constant::TICKET_STATUS[$ticket->workflow_position].'</i></div>'
                    . '<div><strong>Tạo bởi:</strong> <i>'. $created_by .'</div>'
                    . '<div><strong>Ngân sách:</strong> <i>'. number_format($ticket->budget) .' vnđ</i></div>'
                    . '<div><strong>Thời gian chạy:</strong> <i>'. $start_time . ' => ' . $end_time .'</i></div>'
                    . '<div><strong>Giới tính:</strong> <i>'. Constant::GENDERS[$gender] .'</i></div>'
                    . '<div><strong>Độ tuổi</strong>: <i>'. $ages .'</i></div>'
                    . '<div><strong>Tính chất nội dung:</strong> <i>'. Constant::KINDS[$kind] .'</i></div>'
                    . '<div><strong>Từ khóa:</strong> <i>'. $keyword .'</i></div>'
                    . '</div>';
                // Tao notification
                // ...
                // Gui mail, sms, notification thong bao
                $emailToMKT = new SendMailToMKT(
                    /* from */
                    ['address'=>$from_user->email, 'name'=>$from_user->name], 
                    /* to */
                    ['address'=>$to_user->email, 'name'=>$to_user->name], 
                    /* subject */
                    'Thông báo từ '. strtoupper($from_user->permission).' [Ticket: '.$ticket->title.']', 
                    /* message body */
                    $body);
                $this->dispatch($emailToMKT);
            }            

            return response()->json(['status'=>'success', 'message'=> $cmd]);
        } catch (QueryException $qe) {            
        } catch (Exception $e) {            
        }
        $cmd = $cmd === 'cmd=u' ? 'Cập nhật ticket không thành công' : 'Thêm mới ticket không thành công';
        return response()->json(['status'=>'failure', 'message'=> $cmd]);
    }

    public function getTicket($id) {
        try {
            
            $ticket = Ticket::find($id);
            
            if (empty ($ticket)) {
                return response()->json(['status'=>'failure', 'message'=> 'Không tìm thấy thông tin ticket']);
            } else {             
                $ticket->creator;
                $ticket->marketer;
                $ticket->video;
                $comments = TicketComment::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();
                if (!empty($comments)) {
                    TicketComment::where('ticket_id', $id)
                        ->where('user_id', '<>', Auth::user()->id)
                        ->update(['is_read'=>1]);

                    $html_comment = '';
                    foreach($comments as $comment) {
                        $creator = $comment->creator;
                        $html_comment .= '<div class="margin-top comment-box"><div style="float: left; font-weight: bold;">' 
                            . ($creator->id == Auth::user()->id ? '<span class="text-dorange">You</span>' : $creator->name) 
                            . '</div> <div style="float: right; font-size: 90%; color: #999999"><i>' . $comment->created_at->format('H:i d/m/Y') . '</i></div>'
                            . '<div style="clear:both">' . str_replace('\n', '<br/>', $comment->content) . '</div></div>';
                    }
                }
                return response()->json(['status'=>'success', 'ticket'=> $ticket, 'html_comment' => $html_comment]);
            }
            
        } catch (QueryException $qe) {            
        } catch (Exception $e) {            
        }
        
        return response()->json(['status'=>'failure', 'message'=> 'Lỗi không xác định']);
    }

    public function saveComment(Request $request) {
        try {
            $ticket_id = $request->input('ticket-id', 0);
            $txt_comment = $request->input('txt-comment', '');

            if ($ticket_id > 0 && !empty($txt_comment)) {
                $comment = new TicketComment();
                $comment->ticket_id = $ticket_id;
                $comment->content = $txt_comment;
                $comment->user_id = Auth::user()->id;
                $comment->save();
                
                $message = '<div class="margin-top comment-box"><div style="float: left; font-weight: bold;"><span class="text-dorange">You</span></div> <div style="float: right; font-size: 90%; color: #999999"><i>' . $comment->created_at->format('H:i d/m/Y') . '</i></div>'
                    . '<div style="clear:both">' . str_replace('\n', '<br/>', $comment->content) . '</div></div>';
                
                // Tao notification
                $ticket = $comment->ticket;
                $creator = $ticket->creator;
                $marketer = $ticket->marketer;
                /*
                Neu comment user_id = ticket creator id thi:    from_user = ticket->creator, to_user = ticket->marketer
                Neu comment user_id = ticket mkt id thi:        from_user = ticket->marketer, to_user = ticket->creator
                */
                $from_user = null;
                $to_user = null;
                if ($comment->user_id == $creator->id) {
                    $from_user = $creator;
                    $to_user = $marketer;
                } else if ($comment->user_id == $marketer->id) {
                    $from_user = $marketer;
                    $to_user = $creator;
                }
                // ...
                // Gui mail, sms, notification thong bao
                if (!empty($from_user) && !empty($to_user) && $from_user->id != $to_user->id) {
                    $body = 
                        '<div>Bạn có thông báo mới từ Sconnect Content Management: Có bình luận mới.'
                        . '<div style="margin-top: 10px; padding: 15px; border: 1px solid #dddddd; border-radius: 5px; background-color: #f7fbe7"><i>' . $comment->content . '</i></div>'
                        . '<div style="margin-top: 10px; padding: 15px; border: 1px solid #dddddd; border-radius: 5px;">'
                        . '<div><strong>Ticket:</strong><i>'.$ticket->title.'</i></div>'
                        . '<div><strong>Trạng thái:</strong> <i>' . Constant::TICKET_STATUS[$ticket->workflow_position].'</i></div>'
                        . '<div><strong>Tạo bởi:</strong> <i>'. $creator->name .'</div>'
                        . '<div><strong>Ngân sách:</strong> <i>'. number_format($ticket->budget) .' vnđ</i></div>'
                        . '<div><strong>Thời gian chạy:</strong> <i>'. $ticket->start_date . ' => ' . $ticket->end_date .'</i></div>'
                        . '<div><strong>Giới tính:</strong> <i>'. Constant::GENDERS[$ticket->gender] .'</i></div>'
                        . '<div><strong>Độ tuổi</strong>: <i>'. $ticket->age .'</i></div>'
                        . '<div><strong>Tính chất nội dung:</strong> <i>'. Constant::KINDS[$ticket->kind] .'</i></div>'
                        . '<div><strong>Từ khóa:</strong> <i>'. $ticket->keyword .'</i></div>'
                        . '</div>';
                    // Tao notification
                    // ...
                    // Gui mail, sms, notification thong bao
                    $emailToMKT = new SendMailToMKT(
                        /* from */
                        ['address'=>$from_user->email, 'name'=>$from_user->name], 
                        /* to */
                        ['address'=>$to_user->email, 'name'=>$to_user->name], 
                        /* subject */
                        'Thông báo từ '. strtoupper($from_user->permission).' [Ticket: '.$ticket->title.']', 
                        /* message body */
                        $body);
                    $this->dispatch($emailToMKT);
                }  

                return response()->json(['status'=>'success', 'message'=> $message]);
            } else if (empty($txt_comment)) {
                return response()->json(['status'=>'failure', 'message'=> 'Chưa nhập nội dung thảo luận']);
            }
            
        } catch (QueryException $qe) {            
        } catch (Exception $e) {            
        }        
        return response()->json(['status'=>'failure', 'message'=> 'Gửi không thành công']);
    }
}

