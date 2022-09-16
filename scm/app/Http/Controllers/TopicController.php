<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Exception;

use App\Models\Topic;
use App\Utils;

class TopicController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $root_topics = Topic::where('parent_id', 0)->orderBy('name', 'asc')->get();
        $html_options = '';
        $html_treeview_topic = '';
        
        if (!empty($root_topics)) {
            $html_treeview_topic = '<ul class="cat-tree">';
            foreach($root_topics as $topic) {
                $html_options .= Utils::buildTopicOptionList($topic, 0);
                $html_treeview_topic .= $this->buildTreeViewTopic($topic, 0);
            }
            $html_treeview_topic .= '</ul>';
        }
        /*
         * <option value="{{ $topic->id }}">{{ $topic->name }}</option>
         * */
                
        return view('topic')->with(compact('html_options', 'html_treeview_topic'));
    }
    
    private function buildTreeViewTopic($node, $level) {
        
        if (empty($node)) return '';
        $total_channels = $node->total_channels;
        $statistic = '<br/><small class="text-dorange">'
            . 'Số kênh: ' . $total_channels . ' | Số video: 0'
            . '</small>';
        
            $html = '<li><span><a href="/channel?platform=0&topic='.$node->id.'"><strong>'.$node->name . '</strong></a>' . $statistic . ' <span class="action-box">'
            . '<i class="far fa-edit ic24 cursor-hand" onclick="editTopic('.$node->id.', '.$node->parent_id.', \''.htmlspecialchars($node->name).'\')"></i> &nbsp; '
                . '<i class="far fa-trash-alt ic24 cursor-hand" style="color: #ff5648!important" onclick="deleteTopic('.$node->id.', \''.htmlspecialchars($node->name).'\', '.$total_channels.', 0)"></i>'
            .'</span></span>';
        
        $children = $node->children;
        if (!empty($children)) {
            $html .= '<ul>';
            foreach ($children as $item) {
                $html .= $this->buildTreeViewTopic($item, $level + 1);
            }
            $html .= '</ul>';
        }
        
        return $html . '</li>';
    }
    
    public function saveTopic(Request $request) {
        
        try {
            $id = $request->input('id');
            $msg_success = 'Thêm mới thành công';
            
            $messages = array(
                'name.required' => 'Chưa nhập tên chủ đề.',
                'name.unique' => 'Chủ đề này đã tồn tại',
            );
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:topics,name,' . $id
            ], $messages);
            
            if ($validator->fails()) {
                $message = '';
                $messages = $validator->messages();
                foreach ($messages->all() as $msg) {
                    $message .= $msg . '<br/>';
                }
                return redirect()->back()->withInput()->withErrors(['msg' => $message]);
            }
            
            if (is_numeric($id) && $id != 0) {
                $topic = Topic::find($id);
                $msg_success = 'Cập nhật thành công';
            } else {
                $topic = new Topic();
            }
            
            $topic->name = $request->input('name');
            $topic->parent_id = $request->input('parent_id');
            $topic->save();
            
            return redirect('/topic')->with('msg', $msg_success);
            
        } catch (QueryException $qe) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Lỗi: ' . $e->getMessage()]);
        }
        
    }
    
    /*
     * Xoá topic. Chỉ cho phép xoá topic không có kênh và video.
     * Sau khi xoá topic, topic con của topic này sẽ được update về parent_id = 0
     * */
    public function deleteTopic($id) {
        $msg_success = 'Xoá thành công';
        try {
            $topic = Topic::find($id);
            
            $total_channels = $topic->total_channels;
            $total_videos = 0;
            if ($total_channels > 0 || $total_videos > 0) {
                $msg_success ='Topic đang có ' . $total_channels . ' kênh và ' . $total_videos . ' video. Không cho phép xoá.';
            } else {            
                /*
                 * KIỂM TRA XEM CÓ CÁC TOPIC CON THÌ XOÁ TOPIC CON | HAY UPDATE TOPIC CON VỀ TOP
                 * */
                $children = $topic->children;
                if (!empty($children)) {
                    foreach($children as $child) {
                        $child->parent_id = 0;
                        $child->save();
                    }
                }
                
                $topic->delete();
            } 
        } catch (Exception $e) {    
            $msg_success = 'Lỗi: ' . $e->getMessage();
        }        
        return redirect('/topic')->with('msg', $msg_success);        
    }
}
