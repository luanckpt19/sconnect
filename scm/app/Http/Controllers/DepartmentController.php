<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentPrefix;
use App\Models\Title;
use App\Models\User;
use App\Utils;
use Illuminate\Http\Request;

class DepartmentController extends Controller {
	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index() {
		$root_dept = Department::where('parent_id', 0)->withCount('staffs')->first();
		$prefixes = DepartmentPrefix::all();
		$title_list = Title::all();
		
		return view('department')->with(compact('root_dept', 'prefixes', 'title_list'));
	}
	
	public function addDepartment(Request $request) {
		$status = "success";
		$body = '';
		$message = $this->addDeptProcess($request);
		if (!empty($message)) {	// Neu co loi phat sinh.
			$status = "failure";
		} else {
			$body = Utils::buildDeptTree(Department::where('parent_id', 0)->first(), 0);
			$message = 'Thêm mới thành công';
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function addSubDepartment(Request $request) {
		$status = "success";
		$body = '';
		$message = $this->addDeptProcess($request);
		if (!empty($message)) {	// Neu co loi phat sinh.
			$status = "failure";
		} else {
			$parent_id = $request->input('parent_id');
			if (!is_numeric($parent_id)) $parent_id = 0;
			
			$body = Utils::buildDeptChildrenList(Department::where('parent_id', $parent_id)->get());
			$message = 'Thêm mới thành công';
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function addDeptProcess(Request $request) {
		$message = null;
		try {
			$parent_id = $request->input('parent_id');
			if (!is_numeric($parent_id)) $parent_id = 0;
			$name = $request->input('name');
			$prefix = $request->input('prefix');
			
			if (!empty($name) && strlen(trim($name)) > 0) {
				$dept = new Department();
				$dept->parent_id = $parent_id;
				$dept->name = $name;
				$dept->prefix = $prefix;
				$dept->save();
				$message = null;
			} else {
				$message = "Tên phòng ban rỗng!";
			}
			
		} catch (\Exception $e) {
			$message = "Thêm phòng ban không thành công! \nLỗi: ".$e->getMessage();
		}
		return $message;
	}
	
	public function updateDepartment(Request $request) {
		$status = "success";
		$body = '';
		$message = 'Cập nhật thành công';
		try {
			$id = $request->input('id');
			if (!is_numeric($id)) $id = 0;
			$name = $request->input('name');
			$prefix = $request->input('prefix');
			
			if (!empty($name) && strlen(trim($name)) > 0) {
				$dept = Department::find($id);
				$dept->name = $name;
				$dept->prefix = $prefix;
				$dept->save();
				$body = Utils::buildDeptTree(Department::where('parent_id', 0)->first(), 0);
			} else {
				$message = "Tên phòng ban rỗng!";
			}
			
		} catch (\Exception $e) {
			$message = "Cập nhật không thành công! \nLỗi: ".$e->getMessage();
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function deleteDepartment($id = 0) {
		$status = "success";
		$message= "Xoá thành công!";
		$body = '';
		try {
			$dept = Department::find($id);
			
			$children = $dept->children;
			if (!empty($children) && count($children) > 0) {
				$status = "failure";
				$message= "Không thể xoá khi còn phòng ban con!";
			} else {				
				if ($dept->total_staffs > 0) {
					$status = "failure";
					$message= "Không thể xoá khi phòng ban này còn nhân viên!";
				} else {
					$dept->delete();
					$body = Utils::buildDeptTree(Department::where('parent_id', 0)->first(), 0);
				}
			}
		} catch (\Exception $e) {
			$status = "failure";
			$message= "Xoá không thành công! \nLỗi: ".$e->getMessage();
		}		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function assignManager($dept_id, $user_id) {
		$status = "failure";
		$message = "Chọn người quản lý không thành công";
		$body = '';
		try {
			$dept = Department::find($dept_id);
			if (!empty($dept)) {
				$user = User::find($user_id);
				if (!empty($user)) {
					$dept->manager_id = $user_id;
					$dept->save();
					
					$status = "success";
					$message = "Chọn người quản lý thành công";
					$body = Utils::buildDeptTree(Department::where('parent_id', 0)->first(), 0);
				}
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Chọn người quản lý không thành công. Lỗi: " . $e->getMessage();
		}
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function loadDeptTreeForTransfer($staff_id) {
		$staff = User::find($staff_id);
		if (empty($staff)) return '';
		
		$in_dept = $staff->parent;
		$in_dept_msg = '';
		if (!empty($in_dept)) {
			$dept_prefix = $in_dept->prefixes;			
			$in_dept_msg = 'Đang thuộc phòng: <strong class="text-color1">' . (!empty($dept_prefix) ? $dept_prefix->name . ' ' : '') . $in_dept->name . '</strong>';
		} else {
			$in_dept_msg = 'Chưa thuộc phòng ban nào.';
		}
		$root_dept = Department::where('parent_id', 0)->first();		
		
		$html = '<div>Chuyển nhân viên <strong class="text-blue">'.$staff->name.'</strong></div>';
		$html .= '<div>'.$in_dept_msg.'</div><br/><div align="center">Tới phòng ban<br/><i class="fas fa-angle-double-down"></i></div><br/>';
		$html .= '<ul>' . Utils::loadDeptTreeForTransfer($root_dept, $staff->department_id) . '</ul>';
		return $html;
	}
}
