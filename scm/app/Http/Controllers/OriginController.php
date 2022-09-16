<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Exception;

use App\Models\Folder;
use App\Models\File;
use App\Constant;
use App\Utils;

class OriginController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
        try {
            $current_folder_id = $request->input('folder');
            if (!is_numeric($current_folder_id)) $current_folder_id = 0;
            
            $dept_name = Utils::getDepartmentName();        
            
            // $folder_breadcrumb, $folder_list, $file_list, $current_folder_name
            $ret_array = $this->buildFolderListAndFileList($current_folder_id, $dept_name);
            
        } catch (QueryException $qe) {
            return view('origin-product')->with(['folder_breadcrumb'=>'', 'folder_list'=>'',
                'file_list'=>'', 'current_folder_id'=>'', 'current_folder_name'=>'']);
        } catch (Exception $e) {
            return view('origin-product')->with(['folder_breadcrumb'=>'', 'folder_list'=>'',
                'file_list'=>'', 'current_folder_id'=>'', 'current_folder_name'=>'']);
        }
    
        return view('origin-product')->with(['folder_breadcrumb'=>$ret_array[0], 'folder_list'=>$ret_array[1], 
            'file_list'=>$ret_array[2], 'current_folder_id'=>$current_folder_id, 'current_folder_name'=>$ret_array[3]]);
    }
    
    /*
     * Build danh sách thư mục và tệp tin.
     * return array(folder_breadcrumb, folder_list_html, file_list_html, current_folder_name)
     * */
    private function buildFolderListAndFileList($current_folder_id, $dept_name, $folder_expand_ids = array()) {
        $folder_list_html = '<div align="center" class="text-grey">Chưa có thư mục nào</div>';
        $file_list_html = '<div align="center" class="text-grey p-10">Chưa có thư mục / file nào</div>';
        
        $current_folder_name = $dept_name;
        
        // Lay folder của phòng ban này có parent_id = 0.
        $folders_level1 = Folder::where(['parent_id' => 0, 'department_id' => Utils::getDepartmentId()])->get();
        
        // html folder ROOT.
        $folder_root = '<div class="'. ($current_folder_id == 0 ? 'folder-selected" style="padding: 5px;"':'') .'"><a href="?folder=0"><i class="'
            . ($current_folder_id == 0 ? 'fas fa-folder-open':'fas fa-folder') . ' text-orange"></i> '.$dept_name.'</a></div>';
            
        /*
         * html cây thư mục cho panel folder
         * ROOT
         *  |--- Thư mục
         *  |--- Thư mục
         *  |    |--- Thư mục con
         *  |--- Thư mục
         *  |...
         * */
        // Lấy id các folder là các cấp trên của $current_folder_id
        $current_folder = Folder::find($current_folder_id);
        $folder_expand_ids = array();
        if (!empty($current_folder)) {
            $folder_expand_ids[] = $current_folder->id;
            $parent = $current_folder->parent;
            while(!empty($parent)) {
                $folder_expand_ids[] = $parent->id;
                $parent = $parent->parent;
            }
        }
        
        if (!empty($folders_level1) && count($folders_level1) > 0) {
            $folder_list_html = $folder_root.'<ul class="folder-tree" style="margin-left: 5px; margin-top: -10px;">';
            foreach($folders_level1 as $folder) {
                $folder_list_html .= $this->buildTreeFolder($folder, $current_folder_id, $folder_expand_ids);
            }
            $folder_list_html .= '</ul>';
        }
        
        // breadcrumb cho panel file        
        $folder_breadcrumb = '<ul class="breadcrumb-none" style="max-width: 400px;">'
            . '<li class="breadcrumb-item"><a href="?folder=0"><i class="fas fa-cloud"></i> '.$dept_name.'</a></li>';
        $item_html = '';
        $current_folder = Folder::find($current_folder_id);
        $children_folder = null;
        
        if (!empty($current_folder)) {
            $children_folder = Folder::where('parent_id', $current_folder->id)->get();
            $current_folder_name = $current_folder->name;
            do {
                $item_html = '<li class="breadcrumb-item"><a href="?folder='.$current_folder->id.'">'.$current_folder->name.'</a></li>'.$item_html;
                $current_folder = $current_folder->parent;
                if (empty($current_folder)) break;
            } while(true);
        } else {
            $children_folder = $folders_level1;
        }
        $folder_breadcrumb .= $item_html.'</ul>';
        
        // list thư mục con của current folder.
        $children_folder_html = '';
        if (!empty($children_folder) && count($children_folder) > 0) {
            $children_folder_html = '<table class="table table-hover table-no-border">';
            foreach ($children_folder as $item) {
                $children_folder_html .= '<tr><td><a href="?folder='.$item->id.'" class="no-underline"><i class="fas fa-folder text-orange"></i> &nbsp; <span id="span-'.$item->id.'">'.$item->name.'</span></a></td>'
                    .'<td align="right" style="white-space: nowrap;">'
                    .'<i class="far fa-edit cursor-hand" data-toggle="modal" data-target="#modal-new-folder" data-parentid="'.$current_folder_id
                        .'" data-id="'.$item->id.'" data-name="'.htmlspecialchars($item->name).'" data-parentname="'.htmlspecialchars($current_folder_name).'"></i> &nbsp; '
                     .'<i class="far fa-trash-alt cursor-hand" style="color: #ff5648" onclick="deleteFolder('.$item->id.')"></i>'
                    .'</td></tr>';
            }            
            $children_folder_html .= '</table>';
        }
        
        // list file trong thu muc
        $children_file_html = '';
        $file_list = File::where('folder_id', $current_folder_id)->get();
        
        if (!empty($file_list) && count($file_list) > 0) {
            $children_file_html = '<table class="table table-hover table-no-border">';
            foreach ($file_list as $file) {                
                $children_file_html .= '<tr><td style="border: 0; padding: 0.25rem 0rem;"><div><i class="'
                        . Constant::MIME_ICONS[$file->mime_type] .' text-gray"></i> &nbsp; <span id="sp-file-'.$file->id.'">'.$file->name.'</span></div>'
                            . '<div style="color: #999999; font-size: 95%" class="text-ellipsis"><i>' . $file->location_store . '</i></div>'
                    . '</td>'
                    . '<td><div>Video: ' . number_format($file->total_videos) . '</div><div>Channel: ' . number_format($file->total_channels) . '</div></td>'
                    . '<td align="right" style="white-space: nowrap;">'
                        . '<a href="javascript: void(0)" title="Xem chi tiết"><i class="fas fa-poll"></i></a> &nbsp; '
                        .'<i class="far fa-edit cursor-hand" data-toggle="modal" data-target="#modal-new-file" data-folderid="'.$current_folder_id
                            .'" data-fileid="'.$file->id.'" data-location="'.$file->location_store.'" data-mime="'.$file->mime_type
                            .'" data-filename="'.htmlspecialchars($file->name).'" data-foldername="'.htmlspecialchars($current_folder_name).'"></i> &nbsp; '
                        .'<i class="far fa-trash-alt cursor-hand" style="color: #ff5648" onclick="deleteFile('.$file->id.')"></i>'
                    .'</td></tr>';
            }
            $children_file_html .= '</table>';
        }
        
        if (strlen($children_folder_html) > 0 || strlen($children_file_html) > 0) {
            $file_list_html = $children_folder_html . $children_file_html;
        }
        
        return array($folder_breadcrumb, $folder_list_html, $file_list_html, $current_folder_name);
    }
    
    /*
     * Build cây thư mục cho panel folder
     * ROOT
     *  |--- Thư mục
     *  |--- Thư mục
     *  |    |--- Thư mục con
     *  |--- Thư mục
     *  |...
     * */ 
    private function buildTreeFolder($folder, $curr_id, $folder_expand_ids = array()) {
        if (empty($folder)) return '';
        $html = '<li>'; 
        $children = $folder->children;
        if (!empty($children) && count($children) > 0) {
            $html = '<li class="container" id="li-'.$folder->id.'">';
        }
        $html .= '<p style="padding: 5px; z-index:999" class="text-ellipsis '.($curr_id == $folder->id ? 'folder-selected':'').'">'
            . '<a href="javascript: load('.$folder->id.')" class="folder">'
            .'<i class="'.($curr_id == $folder->id ? 'fas fa-folder-open':'fas fa-folder').' text-orange"></i></a> &nbsp; <a href="?folder='.$folder->id.'">'.$folder->name.'</a></p>';
        
        if (!empty($children) && count($children) > 0) {
            
            $showOrHide = in_array($folder->id, $folder_expand_ids) ? ' class="show"' : ' class="hide"';
            
            $html .= '<div class="subs"><ul id="ul-'.$folder->id.'"'.$showOrHide.'>';
            foreach($children as $child) {
                $html .= $this->buildTreeFolder($child, $curr_id, $folder_expand_ids);
            }
            $html .= '</ul></div>';
        }        
        $html .= '</li>';
        return $html;
    }
    
    /*
     * Chức năng: Tạo folder mới hoặc update folder.
     * Return: json: status=failure|success; message: Thông báo kết quả thực hiện.
     *          folder_list: reload cây thư mục.
     *          file_list: reload thư mục con & file trong thư mục parent_id
     * */
    public function saveFolder(Request $request) {
        $status = "failure";
        $message = "Cập nhật không thành công";
        
        $id = $request->input('id');
        $parent_id = $request->input('parent_id');
        $name = $request->input('name');
        
        $folder_list = array();
        $file_list = array();
        
        try {
            
            $dept_id = Utils::getDepartmentId();            
            if ($dept_id == 0) {
                return response()->json(['status'=>$status, 'message'=>'Bạn cần phải được phân vào 1 phòng ban']);                
            }
            
            if (!is_numeric($parent_id) || empty($name)) {
                return response()->json(['status'=>$status, 'message'=>$message]);
            }
            
            if (is_numeric($id) && $id > 0) {
                $folder = Folder::find($id);
            } else {
                $folder = new Folder();                
            }
            $folder->department_id = $dept_id;
            $folder->parent_id = $parent_id;
            $folder->name = $name;            
            $folder->save();
            $status = "success";
            $message = "Cập nhật thành công";
            
            // $folder_breadcrumb, $folder_list, $file_list, $current_folder_name
            
            $ret_array = $this->buildFolderListAndFileList($parent_id, Utils::getDepartmentName());
            $folder_list = $ret_array[1];
            $file_list = $ret_array[2];
            
        } catch (QueryException $qe) {
            return response()->json(['status'=>$status, 'message'=>'Lỗi: Thư mục này đã tồn tại']);
        } catch (Exception $e) {
            return response()->json(['status'=>$status, 'message'=>'Lỗi: ' . $e->getMessage()]);
        }
        
        return response()->json(['status'=>$status, 'message'=>$message, 'folder_list'=>$folder_list, 'file_list'=>$file_list]);
    }
    
    /*
     * Chức năng: Tạo file mới hoặc update file.
     * Return: json: status=failure|success; message: Thông báo kết quả thực hiện.
     *          file_list: reload thư mục con & file trong thư mục parent_id
     * */
    public function saveFile(Request $request) {
        $status = "failure";
        $message = "";
        
        $id = $request->input('id');
        $folder_id = $request->input('folder_id');
        $name = $request->input('name');
        $location_store = $request->input('location');
        $mime_type = $request->input('mime_type');
        
        $file_list = array();
        
        try {
            $error = false;
            if (!is_numeric($folder_id) || $folder_id == 0) {
                $error = true;
                $message = 'Chưa chọn một thư mục trong phòng ban.<br/>';
            }
            if (empty($name)) {
                $error = true;
                $message .= 'Chưa nhập tên tệp tin.<br/>';
            }            
            if (empty($location_store)) {
                $error = true;
                $message .= 'Chưa nhập đường dẫn lưu trữ.<br/>';
            }
            if (empty($mime_type)) {
                $error = true;
                $message .= 'Chưa chọn loại tệp tin.<br/>';
            }
            
            if ($error) {
                return response()->json(['status'=>$status, 'message'=>$message]);
            }
            
            if (is_numeric($id) && $id > 0) {
                $file = File::find($id);
            } else {
                $file = new File();
            }
            
            $file->folder_id = $folder_id;
            $file->name = $name;
            $file->location_store = $location_store;
            $file->mime_type = $mime_type;
            $file->save();
            $status = "success";
            $message = "Cập nhật thành công";
            
            // $folder_breadcrumb, $folder_list, $file_list, $current_folder_name
            $ret_array = $this->buildFolderListAndFileList($folder_id, Utils::getDepartmentName());
            $file_list = $ret_array[2];
            
        } catch (Exception $e) {
            return response()->json(['status'=>$status, 'message'=>'Lỗi: ' . $e->getMessage()]);
        }
        
        return response()->json(['status'=>$status, 'message'=>$message, 'file_list'=>$file_list]);
    }
    
    /*
     * Xoá folder.
     * Chỉ cho xoá folder rỗng.
     * */
    public function deleteFolder($id) {
        try {
            $folder = Folder::find($id);
            $children_folder = $folder->children;
            if (!empty($children_folder) && count($children_folder) > 0) {
                return response()->json(['status'=>'failure', 'message'=>'Thư mục đang được sử dụng. Không thể xoá.']);
            }
            
            $files = $folder->files;
            if (!empty($files) && count($files) > 0) {
                return response()->json(['status'=>'failure', 'message'=>'Thư mục đang được sử dụng. Không thể xoá.']);
            }
            
            $parent_folder_id = $folder->parent_id;
            $folder->delete();
            
            // $folder_breadcrumb, $folder_list, $file_list, $current_folder_name
            $ret_array = $this->buildFolderListAndFileList($parent_folder_id, Utils::getDepartmentName());
            $folder_list = $ret_array[1];
            $file_list = $ret_array[2];
            
            return response()->json(['status'=>'success', 'message'=>'Xoá thành công', 'folder_list'=>$folder_list, 'file_list'=>$file_list]);
        } catch (Exception $e) {
            return response()->json(['status'=>'failure', 'message'=>'Lỗi: ' . $e->getMessage()]);
        }
        
    }
    
    /*
     * Xoá file.
     * Chỉ cho xoá file chưa triển khai up lên kênh
     * */
    public function deleteFile($id) {
        try {
            $file = File::find($id);            
            /*
             * Kiểm tra xem file có được triển khai up lên kênh nào không?
             * Nếu không thì mới cho phép xoá.
             * */
            $parent_folder_id = $file->folder_id;
            $file->delete();
            
            // $folder_breadcrumb, $folder_list, $file_list, $current_folder_name
            $ret_array = $this->buildFolderListAndFileList($parent_folder_id, Utils::getDepartmentName());
            $file_list = $ret_array[2];
            return response()->json(['status'=>'success', 'message'=>'Xoá thành công', 'file_list'=>$file_list]);
        } catch (Exception $e) {
            return response()->json(['status'=>'failure', 'message'=>'Lỗi: ' . $e->getMessage()]);
        }
        
    }
}
