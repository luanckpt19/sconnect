<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Exception;
use App\Models\Video;
use App\Models\Channel;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Platform;
use App\Models\Folder;
use App\Models\User;
use App\Utils;
use App\Models\File;
use App\Constant;
use Carbon\Carbon;
use App\Models\ChannelReport;
use App\Models\VideoReport;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        
        try {
            $this_year = Carbon::now()->format('Y');
            $this_month = Carbon::now()->format('m');
            // tháng/năm báo cáo
            $report_year = $request->input('report_year', $this_year);
            $report_month = $request->input('report_month', $this_month);

            $promotion = $request->input('promotion', 0);
            $txt_search = $request->input('txt_search', '');
            
            // query video   
            $video_list = null;         
            $platform_list = Platform::all();
            $arr_platform = array();
            foreach ($platform_list as $platform) {
                $arr_platform[$platform->id] = $platform->name;
            }
            
            $platform_name = '';
            // Lấy danh sách kênh của phòng ban
            $channel_list = Channel::where('department_id', Auth::user()->department_id)
                ->orderByRaw('platform_id asc, name asc')->get();
            // kênh đang được chọn
            $channel_id = $request->input('channel', 0);
            $channel = null;
            foreach ($channel_list as $channel) {
                if ($channel->id == $channel_id) {
                    // duyệt danh sách kênh tới kênh đang được chọn
                    break;
                }
            }
            // lấy platform của kênh được chọn và danh sách video của kênh được chọn
            if (!empty($channel)) {
                $video_list = Video::where('id', '>', 0);
                
                $channel_id = $channel->id;
                if (is_numeric($channel_id) && $channel_id > 0) {                
                    $platform_name = $arr_platform[$channel->platform_id];
                    $video_list = $video_list->where('channel_id', $channel_id);
                }

                if (!empty($txt_search)) {                
                    $video_list = $video_list->where('name', 'like' , '%' . $txt_search . '%');
                }                                
                
                if ($promotion == 1) {                
                    //$video_list = $video_list->where('promotion', 1);
                    $video_list = $video_list->whereRaw('id in (select video_id from tickets where workflow_position = 4)');
                } else {
                    $promotion = 0;
                }
                
                $video_list = $video_list->orderBy('joined_date', 'desc')->paginate(15); 
            }
                                    
            /*
             * Get channel report data
             * */            
            $min_year = $this_year;
            $min_date_report = DB::table('channel_reports')->select(DB::raw('min(date) as min_date'))->first();
            if (!empty($min_date_report)) {
                $min_year = explode('-', $min_date_report->min_date)[0];
            }
            $arr_years = array();
            for($year = $min_year; $year <= $this_year; $year++) {
                $arr_years[$year] = $year;
            }
            
            $yyyy_mm = $report_year . '-' . $report_month;
            $last_date_of_month = Carbon::createFromFormat('Y-m-d', $yyyy_mm.'-01')->endOfMonth()->toDateString();
            $arr_date = explode('-', $last_date_of_month);
            $last_date = $arr_date[2];
            
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
            
            $channel_reports = ChannelReport::where('channel_id', $channel_id)
                ->where('date', 'like', $yyyy_mm.'-%')
                ->orderBy('date')
                ->get();
            
            if (!empty($channel_reports) && count($channel_reports) > 0) {
                foreach ($channel_reports as $channel_report) {
                    $report_date = explode('-', $channel_report->date)[2];
                    $arr_view[0+$report_date] = $channel_report->view_count;
                }
            }
            
            if ($report_month == Carbon::now()->format('m')) {
                $last_date = Carbon::now()->format('d');    // get dd
            }
            
            for($idx = 2; $idx <= $last_date; $idx++) { // date from 2 - end date of month
                $arr_view_delta[$idx] = $arr_view[$idx] - $arr_view[$idx-1];
            }
            
            $last_day_of_previous_month = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
            $last_dopm_reports = ChannelReport::where('channel_id', $channel_id)->where('date', $last_day_of_previous_month)->first();
            if (!empty($last_dopm_reports)) {
                $arr_view_delta[1] = $arr_view[1] - $last_dopm_reports->view_count;   // first date of month
            }
            
            $str_view = implode(',', $arr_view);
            $str_view_delta = implode(',', $arr_view_delta);            
            
            $page = $request->input('page', 1);
            
            return view('video')->with(compact('arr_platform', 'channel_id', 'channel', 'channel_list', 'platform_name', 'video_list', 'page', 'promotion', 'txt_search',
                'str_label', 'str_view', 'str_view_delta', 'arr_years', 'report_month', 'report_year'));
        } catch (Exception $e) {
            dd($e);
        }
        
        
    }
    
    public function detail(Request $request) {
        $tab = 0;
        try {
            $this_year = Carbon::now()->format('Y');
            $this_month = Carbon::now()->format('m');
            // tháng/năm báo cáo
            $report_year = $request->input('report_year', $this_year);
            $report_month = $request->input('report_month', $this_month);
            
            $video_id = $request->input('video_id');
            if (!is_numeric($video_id)) $video_id = 0;
            
            // get video info
            $video = Video::find($video_id);
            $selected_video = $video;
            $channel = $video->channel;            
            $platform = $channel->platform;
            
            // get video statistic
            $min_year = $this_year;
            $min_date_report = DB::table('video_reports')->select(DB::raw('min(date) as min_date'))->first();
            if (!empty($min_date_report)) {
                $min_year = explode('-', $min_date_report->min_date)[0];
            }
            $arr_years = array();
            for($year = $min_year; $year <= $this_year; $year++) {
                $arr_years[$year] = $year;
            }
            
            $yyyy_mm = $report_year . '-' . $report_month;
            $last_date_of_month = Carbon::createFromFormat('Y-m-d', $yyyy_mm.'-01')->endOfMonth()->toDateString();
            $arr_date = explode('-', $last_date_of_month);
            $last_date = $arr_date[2];
                        
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
            
            $video_reports = VideoReport::where('video_id', $video_id)->where('date', 'like', $yyyy_mm.'-%')->orderBy('date')->get();
            if (!empty($video_reports) && count($video_reports) > 0) {
                foreach ($video_reports as $video_report) {
                    $report_date = explode('-', $video_report->date)[2];
                    $arr_view[0+$report_date] = $video_report->view_count;
                }
            }
            
            if ($report_month == Carbon::now()->format('m')) {
                $last_date = Carbon::now()->format('d');    // get dd
            }
            
            for($idx = 2; $idx <= $last_date; $idx++) {
                $arr_view_delta[$idx] = $arr_view[$idx] - $arr_view[$idx-1];
            }
            
            $last_day_of_previous_month = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
            $last_dopm_reports = VideoReport::where('video_id', $video_id)->where('date', $last_day_of_previous_month)->first();
            if (!empty($last_dopm_reports)) {
                $arr_view_delta[1] = $arr_view[1] - $last_dopm_reports->view_count;   // first date of month
            }
            
            $str_view = implode(',', $arr_view);
            $str_view_delta = implode(',', $arr_view_delta);
            

        } catch (Exception $e) {
        }
        $marketers = User::where('permission', Constant::MKT)->get();

        return view('video-detail')->with(compact('tab', 'marketers', 'video', 'selected_video', 'channel', 'platform', 'str_label', 'str_view', 'str_view_delta', 'arr_years', 'report_month', 'report_year'));
    }
    
    /*
     * Load file for assignment to video
     * Build danh sách thư mục và tệp tin.
     * return body html
     * */
    public function loadFileForVideo($video_id, $current_folder_id=0, $current_dept_id=0) {
        
        $body = '<div align="center" class="text-grey p-10">Chưa có thư mục / file nào</div>';
        
        try {
            $dept_breadcrumb = '';
            $folder_breadcrumb = '';
            $children_folder_html = '';
            $file_list_html = '';
            $dept_name = '';
            $hr = '<hr style="padding: 4px 0px 0px 0px;" />';

            // Lay danh sach cay phong ban cha
            $depts = Department::getDeptTreeParent($current_dept_id);
            if (!empty($depts) && count($depts) > 0) {
                foreach($depts as $dept) {
                    $dept_breadcrumb .= '<li class="breadcrumb-item"><a href="javascript: loadOriginalFile('.$video_id.',0,'.$dept->id.')">' . $dept->name . '</a></li>';
                }
            }

            if ($current_folder_id <= 0) {
                // Lay danh sach phong ban con
                $dept_list = Department::where('parent_id', $current_dept_id)->get();
                if (!empty($dept_list) && count($dept_list) > 0) {
                    foreach($dept_list as $dept) {
                        $prefix = $dept->prefixes;
                        $children_folder_html .= '<div class="ptop-10"><small><i class="fas fa-angle-right"></i></small> <a href="javascript: loadOriginalFile('.$video_id.',0,'.$dept->id.')">'
                            . (!empty($prefix) ? $prefix->name . ' ' : '')
                            . $dept->name . '</a></div>';
                    }
                    $children_folder_html = $children_folder_html;
                }
            }
    
            // Lay folder của phòng ban này có parent_id = $current_folder_id.
            $folders = Folder::where(['parent_id' => $current_folder_id, 'department_id' => $current_dept_id])->get();
            // list thư mục con của current folder.            
            if (!empty($folders) && count($folders) > 0) {
                if (!empty($children_folder_html) && strlen($children_folder_html) > 0) {
                    $children_folder_html = $children_folder_html . $hr;
                }
                
                foreach ($folders as $item) {
                    $children_folder_html .= '<div class="ptop-10"><a href="javascript: loadOriginalFile('.$video_id.','.$item->id.','.$current_dept_id.')" class="no-underline">'
                        .'<i class="fas fa-folder text-orange"></i> &nbsp; <span id="span-'.$item->id.'">'.$item->name.'</span></a></div>';
                }
            }
            
            // list file trong thu muc
            $children_file_html = '';
            $file_list = File::where('folder_id', $current_folder_id)->get();
            
            if (!empty($file_list) && count($file_list) > 0) {
                foreach ($file_list as $file) {
                    $children_file_html .= '<div class="ptop-10"><a href="javascript: assignFile('.$video_id.','.$file->id.')"><div><i class="'
                        . Constant::MIME_ICONS[$file->mime_type] .' text-gray"></i> &nbsp; '.$file->name.'</div>'
                            . '<div style="font-size: 95%; padding-top: 5px;" class="text-ellipsis text-grey"><i>' . $file->location_store . '</i></div></a></div>';
                }
            }
            
            if (strlen($children_folder_html) > 0 || strlen($children_file_html) > 0) {
                $file_list_html = $children_folder_html . $children_file_html;
            }
            
            // breadcrumb cho modal file. breadcrumb duyệt từ thư mục hiện tại trở lên level trên                                
            $top_of_breadcrumb = '<ul class="breadcrumb-none">' . $dept_breadcrumb;
                // . '<li class="breadcrumb-item"><a href="?folder=0"><i class="fas fa-cloud"></i> '.$dept_name.'</a></li>';
                
            $item_html = '';            
            $current_folder = Folder::find($current_folder_id);
            while (!empty($current_folder)) {
                $item_html = '<li class="breadcrumb-item"><a href="javascript: loadOriginalFile('.$video_id.','.$current_folder->id.','.$dept->id.')">'.$current_folder->name.'</a></li>'.$item_html;
                $current_folder = $current_folder->parent;
            }            
            $folder_breadcrumb = $top_of_breadcrumb . $item_html . '</ul>';
            
            $body = '<div>'.$folder_breadcrumb.'</div><div style="padding-top: 10px;">'.$file_list_html.'</div>';
            
        } catch (\Exception $e) {
            dd($e);
        }
        return $body;
        
    }
    
    public function assignFileForVideo($video_id, $file_id) {
        $result = '<span class="cursor-hand link-underline-hover" data-toggle="modal" data-target="#modal-assign-file" data-videoid="'.$video_id.'">';
        try {
            $video = Video::find($video_id);
            $file = File::find($file_id);
            if (!empty($video) && !empty($file)) {
                $video->product_id = $file_id;
                $video->save();                
                $result .= '<span class="cursor-hand link-underline-hover">' . $file->name . '</span><br/>'
                        .'<span class="text-grey">' . $file->location_store . '</span>';
            } else {
                $result .= '<span class="text-danger cursor-hand link-underline-hover">[Chưa gắn]</span>';
            }
        } catch (\Exception $e) {
        }
        $result .= '</span>';
        return $result;
    }
    
}
