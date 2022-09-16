<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentPrefix;
use App\Models\Title;
use App\Utils;
use DB;

class SettingController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
		
		$prefix_list = DepartmentPrefix::all();
		$title_list = Title::all();
        return view('setting')->with(compact('prefix_list', 'title_list'));
    }
	
	/*
	* DEPARTMENT PREFIX
	*/
	public function addDeptPrefix(Request $request) {
		$status = 'success';
		$message = 'Thêm mới thành công';
		$body = '';
		try {
			$name = $request->input('prefix-name');
			if (!empty($name)) {
				$prefix_count = DepartmentPrefix::where(['name'=>$name])->count();
				if ($prefix_count > 0) {
					$status = "failure";
					$message = "Đã tồn tại loại phòng ban: ".str_replace('"', '\\"', $name);
				} else {
					$dept_prefix = new DepartmentPrefix();
					$dept_prefix->name = $name;
					$dept_prefix->save();
				
					$prefix_list = DepartmentPrefix::all();
					$body = Utils::buildDeptPrefixList($prefix_list);
				}
			} else {
				$message = "Tên loại phòng ban rỗng";
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Lỗi: ".$e->getMessage();
		}
		
        return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);		
	}
	
	public function deleteDeptPrefix($id) {
		$status = "success";
		$message= "Xoá thành công!";
		$body = '';
		try {
			DB::table("department_prefixes")->whereIn('id',explode(",",$id))->delete();
			
			$prefix_list = DepartmentPrefix::all();
			$body = Utils::buildDeptPrefixList($prefix_list);
		} catch (\Exception $e) {
			$status = "failure";
			$message= "Xoá không thành công! \nLỗi: ".$e->getMessage();
		}		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);		
	}
	
	public function saveDeptPrefix(Request $request) {
		$status = 'success';
		$message = 'Cập nhật thành công';
		try {
			$id = $request->input('prefix-id');
			$name = $request->input('prefix-name');
			
			if (!empty($name)) {
				$dept_prefix = DepartmentPrefix::where(['name'=>$name])->first();
				if (!empty($dept_prefix) && $dept_prefix->id != $id) {
					$status = "failure";
					$message = "Đã tồn tại loại phòng ban: ".str_replace('"', '\\"', $name);
				} else {
					$dept_prefix = DepartmentPrefix::find($id);
					if (!empty($dept_prefix)) {
						$dept_prefix->name = $name;
						$dept_prefix->save();	
					}
				}
			} else {
				$message = "Tên loại phòng ban rỗng";
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Lỗi: ".$e->getMessage();
		}
		
        return response()->json(['status'=>$status, 'message'=>$message]);
	}
	
	/*
	* TITLE
	*/
	public function addTitle(Request $request) {
		$status = 'success';
		$message = 'Thêm mới thành công';
		$body = '';
		try {
			$name = $request->input('title-name');
			if (!empty($name)) {
				$title_count = Title::where(['name'=>$name])->count();
				if ($title_count > 0) {
					$status = "failure";
					$message = "Đã tồn tại tên chức vụ: ".str_replace('"', '\\"', $name);
				} else {
					$title = new Title();
					$title->name = $name;
					$title->save();
				
					$title_list = Title::all();
					$body = Utils::buildTitleList($title_list);
				}
			} else {
				$message = "Tên chức vụ rỗng";
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Lỗi: ".$e->getMessage();
		}
		
        return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);		
	}
	
	public function saveTitle(Request $request) {
		$status = 'success';
		$message = 'Cập nhật thành công';
		try {
			$id = $request->input('title-id');
			$name = $request->input('title-name');
			
			if (!empty($name)) {
				$title = Title::where(['name'=>$name])->first();
				if (!empty($title) && $title->id != $id) {
					$status = "failure";
					$message = "Đã tồn tại tên chức vụ: ".str_replace('"', '\\"', $name);
				} else {
					$title = Title::find($id);
					if (!empty($title)) {
						$title->name = $name;
						$title->save();	
					}
				}
			} else {
				$message = "Tên chức vụ rỗng";
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Lỗi: ".$e->getMessage();
		}
		
        return response()->json(['status'=>$status, 'message'=>$message]);
	}
	
	public function deleteTitle($id) {
		$status = "success";
		$message= "Xoá thành công!";
		$body = '';
		try {
			DB::table("titles")->whereIn('id',explode(",",$id))->delete();
			
			$title_list = Title::all();
			$body = Utils::buildTitleList($title_list);
		} catch (\Exception $e) {
			$status = "failure";
			$message= "Xoá không thành công! \nLỗi: ".$e->getMessage();
		}		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);	
	}
}
