<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Exception;

use App\Models\Platform;
use App\Models\Topic;
use App\Models\Channel;
use App\Utils;
use App\Models\User;
use App\Models\ChannelType;
use App\Jobs\GetChannelInfo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        
        try {
            $page = $request->input('page', 1);
            $this_year = Carbon::now()->format('Y');
            $this_month = Carbon::now()->format('m');
            // tháng/năm báo cáo
            $report_year = $request->input('report_year', $this_year);
            $report_month = $request->input('report_month', $this_month);
            $min_year = $this_year;
            $min_date_report = DB::table('channel_reports')->select(DB::raw('min(date) as min_date'))->first();
            if (!empty($min_date_report)) {
                $min_year = explode('-', $min_date_report->min_date)[0];
            }
            $arr_years = array();
            for($year = $min_year; $year <= $this_year; $year++) {
                $arr_years[$year] = $year;
            }

            $title = 'Thêm kênh mới';        
            $platform_id = Session::get('platform_id');
            $topic_id = Session::get('topic_id');
            $staff_manager_id = Session::get('staff_manager_id');
            $channel_type_id = Session::get('channel_type_id');
            if (!is_numeric($platform_id)) $platform_id = 0;
            if (!is_numeric($topic_id)) $topic_id = 0;
            if (!is_numeric($staff_manager_id)) $staff_manager_id = 0;
            if (!is_numeric($channel_type_id)) $channel_type_id = 0;
            
            /*
            * Các tham số khi chọn select box trong vùng filter
            * */ 
            $sl_platform = $request->input('sl_platform');
            $sl_topic = $request->input('sl_topic');
            $sl_channel_type = $request->input('sl_channel_type');
            $sl_manager = $request->input('sl_manager');
            if (!is_numeric($sl_platform)) $sl_platform = 0;
            if (!is_numeric($sl_topic)) $sl_topic = 0;
            if (!is_numeric($sl_channel_type)) $sl_channel_type = 0;
            if (!is_numeric($sl_manager)) $sl_manager = 0;
            
            /*
            * Nếu channel id được truyền vào nghĩa là chọn sửa channel.
            * */
            $id = $request->input('channel');
            $edit_channel = null;
            
            if (is_numeric($id) && $id > 0) {
                $edit_channel = Channel::find($id);
                if (!empty($edit_channel)) {
                    $title = 'Sửa thông tin kênh';
                    $platform_id = $edit_channel->platform_id;
                    $topic_id = $edit_channel->topic_id;
                    $staff_manager_id = $edit_channel->staff_manager_id;
                    $channel_type_id = $edit_channel->channel_type_id;
                }
            } else {
                $id = 0;
            }
            
            /*
            * Lấy danh sách nền tảng
            * */ 
            $selected_platform_name = '-- Tất cả nền tảng --';                
            $selected_platform = Platform::find($platform_id);        
            if (!empty($selected_platform)) {
                $selected_platform_name = '<img style="margin-top: -4px;" src="' . $selected_platform->picture . '" width="30" height="30" /> &nbsp; ' . $selected_platform->name;
            }        
            $platform_list = Platform::orderBy('name', 'asc')->get();
            
            /*
            * Lấy danh sách chủ đề.
            * Chủ đề sẽ được build thành select box có phần cấp cha - con 
            * */ 
            $topic = Topic::find($topic_id);
            $root_topics = Topic::where('parent_id', 0)->orderBy('name', 'asc')->get();
            $html_options = '';
            $html_sl_topic = '';
            
            if (!empty($root_topics)) {
                foreach($root_topics as $topic) {
                    $html_options .= Utils::buildTopicOptionList($topic, 0, $topic_id);
                    $html_sl_topic .= Utils::buildTopicOptionList($topic, 0, $sl_topic);
                }
            }
            /*
            * Lấy danh sách phân loại kênh
            * */
            $channel_type_list = ChannelType::orderBy('name', 'asc')->get();
            
            /*
            * Thông tin phòng ban
            * */ 
            $dept = Auth::user()->parent;
            
            /*
            * Lấy danh sách nhân viên QTK của phòng ban cùng với của các phòng ban con
            * */ 
            $staff_list = User::getAllStaffInner($dept->id, \App\Constant::QTK);       
            
            /*
            * Lấy danh sách kênh của phòng ban này, theo các filter sl_xxx
            * */ 
            $channel_list = Channel::where('department_id', Auth::user()->department_id);
            if ($sl_platform > 0) $channel_list = $channel_list->where('platform_id', $sl_platform);
            if ($sl_topic > 0) $channel_list = $channel_list->where('topic_id', $sl_topic);
            if ($sl_channel_type > 0) $channel_list = $channel_list->where('channel_type_id', $sl_channel_type);
            if ($sl_manager > 0) $channel_list = $channel_list->where('staff_manager_id', $sl_manager);
            
            $channel_list = $channel_list->orderBy('created_at', 'desc')->paginate(15);
            
            /*
            * Get channel report data
            * */
            $last_date_of_month = Carbon::now()->endOfMonth()->toDateString();
            $last_date = explode('-', $last_date_of_month)[2];
            $yyyy_mm = substr($last_date_of_month, 0, strrpos($last_date_of_month, '-'));
            
            $arr_view = array();
            $arr_view_delta = array();
            $str_label = '';
            for($d = 1; $d <= $last_date; $d++) {
                $arr_view[$d] = 0;
                $arr_view_delta[$d] = 0;
                $str_label .=  ($d < 10 ? '0' : '') . $d . "','";
            }
            if (Utils::endsWith($str_label, "','")) $str_label = substr($str_label, 0, strlen($str_label) - 3);
            $str_label = "'" . $str_label . "'";
            $str_view_delta = '';
            
            $channel_reports = DB::table('channel_reports') 
                ->join('channels', 'channel_reports.channel_id', '=', 'channels.id')
                ->select('channel_reports.date', DB::raw('sum(channel_reports.view_count) as total'))
                ->where('channels.department_id', '=', Auth::user()->department_id)
                ->where('date', 'like', $yyyy_mm.'-%')
                ->groupBy('channel_reports.date')
                ->orderBy('channel_reports.date')
                ->get();
            
            if (!empty($channel_reports) && count($channel_reports) > 0) {
                foreach ($channel_reports as $channel_report) {
                    $report_date = explode('-', $channel_report->date)[2];
                    $arr_view[$report_date] = $channel_report->total;
                }
            }
            $today = Carbon::now()->format('d');;    // get dd
            for($idx = 2; $idx <= $today; $idx++) {
                $arr_view_delta[$idx] = $arr_view[$idx] - $arr_view[$idx-1];
            }
            
            $str_view_delta = implode(',', $arr_view_delta);
            
            return view('channel')->with(compact('id', 'sl_platform', 'sl_topic', 'sl_manager', 'sl_channel_type', 
                'platform_list', 'html_options', 'html_sl_topic', 'staff_list', 'channel_type_list', 
                'selected_platform_name', 'channel_list', 'title', 'edit_channel', 'staff_manager_id', 'channel_type_id',
                'str_label', 'str_view_delta', 'arr_years', 'report_month', 'report_year', 'page'));

        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    
    public function saveChannel(Request $request) {
        $id = $request->input('id');
        
        $dept_id = Auth::user()->department_id;        
        $platform_id = $request->input('platform_id');
        $topic_id = $request->input('topic_id');
        $channel_type_id = $request->input('channel_type_id');
        $staff_manager_id = $request->input('staff_manager_id');
        $name = $request->input('name');
        $url = $request->input('url');
        $note = $request->input('note');
        $schedule = $request->input('schedule');
        
        try {
            
            $msg_success = 'Thêm mới thành công';
            
            $messages = array(
                'name.required' => 'Chưa nhập tên kênh.',
                'url.required' => 'Chưa nhập link kênh.',
                'url.unique' => 'Link kênh này đã tồn tại. Không thể thêm mới.',
                'platform_id.gt' => 'Chưa chọn nền tảng.',
                'topic_id.gt' => 'Chưa chọn chủ đề.',
                'channel_type_id.gt' => 'Chưa chọn loại kênh.',
                'staff_manager_id.gt' => 'Chưa chọn quản trị viên.',
            );
            
            $validator = Validator::make($request->all(), [
                'platform_id' => 'required|numeric|gt:0',
                'topic_id' => 'required|numeric|gt:0',
                'channel_type_id' => 'required|numeric|gt:0',
                'staff_manager_id' => 'required|numeric|gt:0',
                'name' => 'required',
                'url' => 'required|unique:channels,url,' . $id,                
            ], $messages);
            
            if ($validator->fails()) {
                $message = '';
                $messages = $validator->messages();
                foreach ($messages->all() as $msg) {
                    $message .= $msg . '<br/>';
                }
                return redirect()->back()->withInput()->with(['msg' => $message]);
            }
            
            if (!is_numeric($platform_id)) $platform_id = 0;
            if (!is_numeric($topic_id)) $topic_id = 0;
            
            if (is_numeric($id) && $id != 0) {
                $channel = Channel::find($id);
                $msg_success = 'Cập nhật thành công';
            } else {
                $channel = new Channel();
            }
            $channel->department_id = $dept_id;
            $channel->platform_id = $platform_id;
            $channel->topic_id = $topic_id;
            $channel->staff_manager_id = $staff_manager_id;
            $channel->channel_type_id = $channel_type_id;
            $channel->name = $name;
            $channel->url = $url;
            $channel->note = $note;            
            $channel->schedule = $schedule;
            $channel->save();
            
            /*
             * Put get channel info job into queue.
             * */ 
            Log::info("channelId=$channel->id, url=$channel->url");
            $this->dispatch(new GetChannelInfo($channel->id, $channel->url));
                        
        } catch (QueryException $qe) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $e->getMessage()]);
        }
        $sl_platform = $request->input('sl_platform');
        $sl_topic = $request->input('sl_topic');
        $sl_channel_type = $request->input('sl_channel_type');
        $sl_manager = $request->input('sl_manager');
        
        return redirect("/channel?sl_platform=$sl_platform&sl_topic=$sl_topic&sl_channel_type=$sl_channel_type&sl_manager=$sl_manager")
        ->with(['msg' => $msg_success, 'platform_id'=>$platform_id, 'topic_id'=>$topic_id, 'staff_manager_id'=>$staff_manager_id, 'channel_type_id'=>$channel_type_id]);
    }
    
    public function collectChannelInfo($channel_id) {
        try {
            $channel = Channel::find($channel_id);
            if (!empty($channel)) {
                $this->dispatch(new GetChannelInfo($channel->id, $channel->url));
            } else {
                return response()->json(['status'=>'failure', 'message'=>'Có lỗi thực thi: Không tìm thấy thông tin kênh']);
            }
            return response()->json(['status'=>'success', 'updated_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                'message'=>'Thực hiện cập nhật thông tin kênh trong tiến trình ngầm. Quá trình này có thể tốn một chút thời gian.']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'failure', 'message'=>'Có lỗi thực thi: ' . $e->getMessage()]);
        }
        
    }    
    
    public function deleteChannel(Request $request) {
        $id = $request->input('channel');
        
        $channel = Channel::find($id);
        if (empty($channel)) return redirect()->back()->with(['msg' => 'Lỗi: Không tìm thấy kênh cần xoá']);
        
        if ($channel->total_videos > 0) return redirect()->back()->with(['msg' => 'Lỗi: Kênh đang có video, không thể xoá!']);
        Channel::destroy($id);
        
        return redirect()->back()->with(['msg' => 'Xoá kênh thành công!']);
    }
    
    
    public function channelType(Request $request) {
        
        $title = 'Thêm loại kênh mới';
        $name = '';
        $description = '';
        
        /*
         * Nếu channel id được truyền vào nghĩa là chọn sửa channel.
         * */
        $id = $request->input('ct_id');
        if (is_numeric($id) && $id > 0) {
            $channelType = ChannelType::find($id);
            if (!empty($channelType)) {
                $title = 'Sửa thông tin loại kênh';
                $name = $channelType->name;
                $description = $channelType->description;
            }
        } else {
            $id = 0;
        }
        
        // Lấy danh sách loại kênh
        $channel_type_list = ChannelType::orderBy('name', 'asc')->get();
        
        return view('channel-type')->with(compact('id', 'channel_type_list', 'name', 'description', 'title'));
    }
    
    public function saveChannelType(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $description = $request->input('description');
        
        try {
            $msg_success = 'Thêm mới thành công';
            
            $messages = array(
                'name.required' => 'Chưa nhập tên loại kênh.',
            );
            
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ], $messages);
            
            if ($validator->fails()) {
                $message = '';
                $messages = $validator->messages();
                foreach ($messages->all() as $msg) {
                    $message .= $msg . '<br/>';
                }
                return redirect()->back()->withInput()->withErrors(['msg' => $message])->with(['msg' => $message]);
            }
            
            if (is_numeric($id) && $id != 0) {
                $channel_type = ChannelType::find($id);
                $msg_success = 'Cập nhật thành công';
            } else {
                $channel_type = new ChannelType();
            }
            $channel_type->name = $name;
            $channel_type->description = $description;
            $channel_type->save();
            
        } catch (QueryException $qe) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $e->getMessage()]);
        }
        
        return redirect('/channel-type')->with('msg', $msg_success);
    }
    
    public function deleteChannelType($ct_id) {
        $msg_success = 'Xoá thành công';
        try {
            ChannelType::destroy($ct_id);
        } catch (QueryException $qe) {
            $msg_success = 'Lỗi khi xoá: ' + $qe->getMessage();
        } catch (Exception $e) {
            $msg_success = 'Lỗi khi xoá: ' + $e->getMessage();
        }
        return redirect('/channel-type')->with('msg', $msg_success);
    }
}
