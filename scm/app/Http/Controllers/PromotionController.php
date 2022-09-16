<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Exception;
use Config;
use App\Models\User;
use App\Models\Video;
use App\Models\Ticket;
use App\Utils;
use App\Constant;

class PromotionController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        
        try {

            /* TAB */
            $tab = $request->input('tab', 0);

            $own_permission = Auth::user()->permission;
            $own_id = Auth::user()->id;

            /* 
                - Dem so ticket cua cac tab.
                - Neu logged user la QTK thi chi lay ticket do minh tao ra.
                - Neu logged user laf MKT thi lay ticket duoc QTK gui toi.
            */
            // Ticket mới
            $t_new_count = Ticket::where('workflow_position', '<', 2);
            if ($own_permission === Constant::QTK) {
                $t_new_count = $t_new_count->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                $t_new_count = $t_new_count->where('mkt_user_id', $own_id);    
            }
            $t_new_count = $t_new_count->count();
            // Ticket dang review
            $t_review_count = Ticket::whereIn('workflow_position', [2,3]);
            if ($own_permission === Constant::QTK) {
                $t_review_count = $t_review_count->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                $t_review_count = $t_review_count->where('mkt_user_id', $own_id);    
            }
            $t_review_count = $t_review_count->count();
            // Ticket đang chạy
            $t_running_count = Ticket::where('workflow_position', 4);
            if ($own_permission === Constant::QTK) {
                $t_running_count = $t_running_count->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                $t_running_count = $t_running_count->where('mkt_user_id', $own_id);    
            }
            $t_running_count = $t_running_count->count();
            // Ticket tam dung
            $t_paused_count = Ticket::where('workflow_position', 5);
            if ($own_permission === Constant::QTK) {
                $t_paused_count = $t_paused_count->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                $t_paused_count = $t_paused_count->where('mkt_user_id', $own_id);    
            }
            $t_paused_count = $t_paused_count->count();
            // Ticket đã kết thúc
            $t_finish_count = Ticket::where('workflow_position', 6);
            if ($own_permission === Constant::QTK) {
                $t_finish_count = $t_finish_count->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                $t_finish_count = $t_finish_count->where('mkt_user_id', $own_id);    
            }
            $t_finish_count = $t_finish_count->count();

            $tab_item_count = [$t_new_count, $t_review_count, $t_running_count, $t_paused_count, $t_finish_count];

            // Lay tat ca video dang {tab} quang cao
            $wp = '0,1';
            switch($tab) {
                case 1: $wp = '2,3'; break;
                case 2: $wp = '4'; break;
                case 3: $wp = '5'; break;
                case 4: $wp = '6'; break;
            }

            $join_where = 'select video_id from tickets where workflow_position in ('.$wp.')';
            if ($own_permission === Constant::QTK) {
                // Neu logged user la QTK thi lay ticket do user tao                
                $join_where .= ' and user_id = ' . $own_id;
            } else if ($own_permission === Constant::MKT) {
                // Neu logged user la MKT thi lay ticket do QTK gui toi                
                $join_where .= ' and mkt_user_id = ' . $own_id;
            }

            $video_list = Video::whereRaw('id in ('.$join_where.')')
                ->get(['id', 'name', 'url', 'channel_id']);

            // Lay thong tin video 
            $video_id = $request->input('video_id', 0);
            $selected_video = Video::find($video_id);

            $arr_view = array();
            $str_label = '';
            for($d = 1; $d <= 30; $d++) {
                $arr_view[$d] = 0;                
                $str_label .=  ($d < 10 ? '0' : '') . $d . "','";
            }
            if (Utils::endsWith($str_label, "','")) $str_label = substr($str_label, 0, strlen($str_label) - 3);
            $str_label = "'" . $str_label . "'";
            
            $str_view = implode(',', $arr_view);

            // Lay thong tin ticket
            $ticket_list = Ticket::whereIn('workflow_position', explode(',', $wp));
            if ($own_permission === Constant::QTK) {
                // Neu logged user la QTK thi lay ticket do user tao
                $ticket_list = $ticket_list->where('user_id', $own_id);    
            } else if ($own_permission === Constant::MKT) {
                // Neu logged user la MKT thi lay ticket do QTK gui toi
                $ticket_list = $ticket_list->where('mkt_user_id', $own_id);    
            }
            if ($video_id > 0) $ticket_list = $ticket_list->where('video_id', $video_id);
            $ticket_list = $ticket_list->orderBy('id', 'desc')->get();

            // Lay danh sach marketer
            $marketers = User::where('permission', Constant::MKT)->get();           

        } catch (Exception $e) {
        }
        return view('promotion')->with(compact('tab', 'tab_item_count', 'video_id', 'selected_video', 'video_list', 'ticket_list',
            'arr_view', 'str_label', 'str_view', 'marketers'));
    }
}
