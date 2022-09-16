<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\DepartmentPrefix;
use App\Models\Department;
use App\Models\Title;
use App\Models\User;
use App\Utils;

//use Illuminate\Http\Request;

class StaffController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) {
    	$dept_id = $request->input('dept_id');
    	$dept = Department::where('id', $dept_id)->first();
    	if (empty($dept)) {
    		$dept = Department::where('parent_id', 0)->first();
    	}
		$prefixes = DepartmentPrefix::all();
		// $staff_list = $dept->staffs;
		$title_list = Title::all();
		$staff_list = User::where(['department_id' => $dept->id])
			->where('id', '<>', $dept->manager_id)
			->get();
		
		$staff_list_none_dept = User::where(['department_id' => 0])->get();
    	
        return view('staff')->with(compact('dept', 'prefixes', 'staff_list', 'title_list', 'staff_list_none_dept'));
    }
	
	/*
	* This medhot called from department page
	*/
	public function addStaff(Request $request) {
				
		try {
			$messages = array(
				'staff_code.required' => 'Chưa nhập "Mã nhân viên".',
				'family_name.required' => 'Chưa nhập Họ nhân viên',
				'given_name.required' => 'Chưa nhập Tên nhân viên',
				'email' => 'Email không hợp lệ',
				'password.required' => 'Chưa nhập Mật khẩu',
				'password_confirm.required' => 'Chưa nhập lại mật khẩu',
				'password_confirm.same' => 'Mật khẩu nhập lại không khớp',
				'password.min' => 'Mật krequesthải có ít nhất 8 ký tự',
				'title_id.gt'=>'Chưa chọn chức vụ'
			);
		
			$validator = Validator::make($request->all(), [
				'staff_code' => 'required|max:100',
				'family_name' => 'required|max:100',
				'given_name' => 'required|max:100',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|min:8|max:100',
				'password_confirm' => 'required|same:password',
				'title_id' => 'required|numeric|gt:0',
			], $messages);
		
			$message = 'Thêm mới nhân viên thành công';
			$body = '';
			$status = 'success';			
		
			if ($validator->fails()) {			
				$status = 'failure';
				$message = '';
				$messages = $validator->messages();
				foreach ($messages->all() as $msg) {
					$message .= $msg . '<br/>';
				}
								
			} else {
				$gender = $request->input('gender');

				$user = new User();
				$user->staff_code = $request->input('staff_code');
				$user->name = $request->input('family_name') . ' ' . $request->input('given_name');
				$user->given_name = $request->input('given_name');
				$user->family_name = $request->input('family_name');
				$user->email = $request->input('email');
				$user->position = $request->input('title_id');
				$user->department_id = $request->input('dept_id');
				$user->password = Hash::make($request->input('password'));
				$user->phone = Utils::nullToEmpty($request->input('phone'));
				$user->gender = empty($gender) ? 0 : $gender;
				$user->permission = $request->input('permission','-');
				$user->save();
				
				$body = Utils::buildDeptTree(Department::where('parent_id', 0)->first(), 0);
			
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	/*
	* This method called from staff page
	*/
	public function addStaffInDept(Request $request) {
		$total_staff = 0;
		try {
			$messages = array(
				'staff_code.required' => 'Chưa nhập "Mã nhân viên".',
				'family_name.required' => 'Chưa nhập Họ nhân viên',
				'given_name.required' => 'Chưa nhập Tên nhân viên',
				'email' => 'Email không hợp lệ',
				'password.required' => 'Chưa nhập Mật khẩu',
				'password_confirm.required' => 'Chưa nhập lại mật khẩu',
				'password_confirm.same' => 'Mật khẩu nhập lại không khớp',
				'password.min' => 'Mật krequesthải có ít nhất 8 ký tự',
				'title_id.gt'=>'Chưa chọn chức vụ'
			);
		
			$validator = Validator::make($request->all(), [
				'staff_code' => 'required|max:100',
				'family_name' => 'required|max:100',
				'given_name' => 'required|max:100',
				'email' => 'required|email|unique:users,email',
				'password' => 'required|min:8|max:100',
				'password_confirm' => 'required|same:password',
				'title_id' => 'required|numeric|gt:0',
			], $messages);
		
			$message = 'Thêm mới nhân viên thành công';
			$body = '';
			$status = 'success';
		
			if ($validator->fails()) {			
				$status = 'failure';
				$message = '';
				$messages = $validator->messages();
				foreach ($messages->all() as $msg) {
					$message .= $msg . '<br/>';
				}
			} else {
				$gender = $request->input('gender');
				$user = new User();
				$user->staff_code = $request->input('staff_code');
				$user->name = $request->input('family_name') . ' ' . $request->input('given_name');
				$user->given_name = $request->input('given_name');
				$user->family_name = $request->input('family_name');
				$user->email = $request->input('email');
				$user->position = $request->input('title_id');
				$user->department_id = $request->input('dept_id');
				$user->password = Hash::make($request->input('password'));
				$user->phone = Utils::nullToEmpty($request->input('phone'));
				$user->gender = empty($gender) ? 0 : $gender;
				$user->permission = $request->input('permission','-');
				$user->save();
				
				$dept = Department::find($user->department_id);
				
				$staff_list = User::where(['department_id' => $user->department_id])
					->where('id', '<>', $dept->manager_id)->get();
				$total_staff = count($staff_list);				
				$body = Utils::buildStaffList($staff_list);
			
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body, 'total_staff'=>$total_staff]);
	}
	
	/* update staff info in staff page */
	public function updateStaffInDept(Request $request) {
		$total_staff = '';
		$manager_body = '';
		$body = '';
		$dept_children = '';
		try {
			
			$user = User::find($request->input('staff_id'));
			if (empty ($user)) {				
				$status = 'failure';
				$message = 'Không tìm thấy thông tin nhân viên cần cập nhật';
			} else {
				
				$messages = array(
					'staff_code.required' => 'Chưa nhập "Mã nhân viên".',
					'family_name.required' => 'Chưa nhập Họ/Tên nhân viên',
					'given_name.required' => 'Chưa nhập Họ/Tên nhân viên',
					'email' => 'Email không hợp lệ',
					'title_id.gt'=>'Chưa chọn chức vụ'
				);
		
				$validator = Validator::make($request->all(), [
					'staff_code' => 'required|max:100',
					'family_name' => 'required|max:100',
					'given_name' => 'required|max:100',
					'email' => 'required|email|unique:users,email,' . $user->id,
					'title_id' => 'required|numeric|gt:0',
				], $messages);
		
				$message = 'Cập nhật thông tin nhân viên thành công';
				$status = 'success';
		
				if ($validator->fails()) {			
					$status = 'failure';
					$message = '';
					$messages = $validator->messages();
					foreach ($messages->all() as $msg) {
						$message .= $msg . '<br/>';
					}
				} else {
				
					$password = $request->input('password');
					$password_confirm = $request->input('password_confirm');
				
					if (!empty($password) || !empty($password_confirm)) {
						if ($password !== $password_confirm) {
							$body = '';
							$status = 'failure';
							$message = 'Mật khẩu và Nhập lại mật khẩu không khớp';
							return response()->json(['status'=>$status, 'message'=>$message]);
						}
					}
				
					$gender = $request->input('gender');
								
					$user->staff_code = $request->input('staff_code');
					$user->name = $request->input('family_name') . ' ' . $request->input('given_name');
					$user->given_name = $request->input('given_name');
					$user->family_name = $request->input('family_name');
					$user->email = $request->input('email');
					$user->position = $request->input('title_id');
					$user->department_id = $request->input('dept_id');
					if (!empty($password)) {
						$user->password = Hash::make($request->input('password'));
					}				
					$user->phone = Utils::nullToEmpty($request->input('phone'));
					$user->gender = empty($gender) ? 0 : $gender;
					$user->permission = $request->input('permission','-');
					$user->save();
					
					Log::info("update user->permission = $user->permission");

					// Nếu nhân viên này là quản lý của phòng đang xem, thì build lại khu vực Người giám sát theo thông tin mới
					$dept = Department::find($request->input('curr_dept_id'));					
					if ($dept->manager_id == $user->id) {
						$manager_body = $this->buildManagerArea($user, $dept);
					}
					// Nếu nhân viên này thuộc phòng ban đang xem, thì load lại danh sách nhân viên phòng ban này.
					if ($user->department_id == $dept->id) {
						$staff_list = User::where(['department_id' => $user->department_id])
							->where('id', '<>', $dept->manager_id)
							->get();
						$total_staff = count($staff_list) . '';
						$body = Utils::buildStaffList($staff_list);
					}
					// load phòng ban phụ
					$children = $dept->children;
					if (!empty($children) && count($children) > 0) {
						$dept_children = Utils::buildDeptChildrenList($children);
					}
				}
			}
		} catch (\Exception $e) {
			$message = $e->getMessage();
		}
		
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body, 
			'manager_body'=>$manager_body, 'total_staff'=>$total_staff, 'dept_children' => $dept_children]);
	}
	
	public function loadStaffList($dept_id) {
		$staff_list = User::where(['department_id' => $dept_id])->get();
		$html = '';
		if (!empty($staff_list) && count($staff_list) > 0) {
			foreach($staff_list as $staff) {
				$title = $staff->title; $title_name = !empty($title) ? $title->name : '';				
				$html .= '<div class="cursor-hand link-underline-hover" onclick="select_manager('.$dept_id.','.$staff->id.')" style="padding-top: 10px;"><i class="fas fa-user-alt"></i> '.$staff->name.'</div>'
					. '<div style="padding-left: 20px; color: #999999; font-size: 90%">'.$title_name.'</div>';
			}	
		} else {
			$dept = Department::find($dept_id);
			$prefix_name = empty($dept->prefixes) ? '' : htmlspecialchars($dept->prefixes->name) . ' ';
			$dept_name = htmlspecialchars( $dept->name);
			
			$html = '<div align="center"><div><i>(Không có nhân viên)</i></div>'
				. '<div class="cursor-hand link-underline-hover" data-toggle="modal" data-target="#modal-add-staff" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-prefixname="'.$prefix_name.'"><i class="fas fa-user-plus ic24"></i> Thêm nhân viên mới</div></div>';
		}
			
		return $html;
	}
	
	public function buildManagerArea($user, $dept) {
		$manager_title = $user->title;
		//$dept_prefix_name = '';
		//$dept_prefixes = $dept->prefixes;
		//if (!empty($dept_prefixes)) $dept_prefix_name = $dept_prefixes->name;
		
		$val_status = ( $user->status == 1) ? 0 : 1;
		$action_status = config('constant.mnu_status')[$user->status];//($user->status == 1) ? 'Tạm khoá' : 'Mở khoá';
		$msg_status = config('constant.status')[$user->status]; // ($user->status == 1) ? 'Đang hoạt động' : 'Đang tạm khoá';
		
		$ic_status = ($user->status == 1) ? 'fas fa-toggle-on' : 'fas fa-toggle-off';
		$color_status = ($user->status == 1) ? 'style="color: #38B235"' : 'style="color: #666666"';							
		
		$body = '<div class="col-md-5"><div class="dept-manager-avatar user-default-avatar link-underline-hover cursor-hand"'
			. ' data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$user->id.'"' 
			. 		(!empty($user->picture) ? 'style="background-image: url('.$user->picture.')"' : '') . '></div>'
			. '<div style="display: inline-block; padding-right: 30px;">'
				. '<div class="title text-blue">'
					. '<span class="link-underline-hover cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$user->id.'">' . $user->family_name . ' ' . $user->given_name . '</span>&nbsp;'
				. '</div>'
				. '<div class="alias">' . (!empty($manager_title) ? $manager_title->name : '') 
				. (!empty($user->permission) ? ' - ' . \App\Constant::STAFF_GROUPS[$user->permission] : '')
				. '</div>'
				. '<div style="alias"><span style="font-size: 80%;"><i class="'.$ic_status.'" '.$color_status.'></i> '.$msg_status.'</span></div>'
			. '</div></div><div class="col-md-7"><div style="float: left;">'
				. '<div>Mã số: <strong>'.$user->staff_code.'</strong></div>'
				. '<div>Điện thoại: <a href="callto:'.$user->phone.'">'.$user->phone.'</a></div>'
				. '<div>Email: <a href="mailto:'.$user->email.'">'.$user->email.'</a></div></div>'
				. '<div style="float: right; padding-top: 6px;">'
					. '<a href="javascript: void(0);" data-toggle="dropdown" aria-expanded="true" class="circle-vert">'
						. '<i class="fas fa-ellipsis-v"></i>'
					. '</a>'
					. '<div class="dropdown-menu" x-placement="top-end" style="position: absolute; transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform;">'
					. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$user->id.'"><i class="far fa-user-circle ic24"></i> Thông tin nhân viên</div>'
					. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-transfer" data-staffid="'.$user->id.'"><i class="fas fa-exchange-alt ic24"></i> Chuyển phòng ban</div>'
					. '<div class="dropdown-item cursor-hand" onclick="blockManager('.$user->id.', '.$val_status.')"><i class="'.$ic_status.' ic24"></i>  '.$action_status.'</div>'
					. '<div class="dropdown-divider"></div>'
					. '<div class="dropdown-item cursor-hand color-danger" data-toggle="modal" data-target="#modal-delete-staff" data-staffid="'.$user->id.'"><i class="far fa-trash-alt ic24" style="color:#ff5648!important"></i> Xoá nhân viên</div>'
					. '</div>'
				. '</div>'
			. '</div>';
		return $body;
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
					
					$body = $this->buildManagerArea($user, $dept);
					
					$staff_list = User::where(['department_id' => $user->department_id])
						->where('id', '<>', $dept->manager_id)
						->get();
					$total_staff = count($staff_list);
					$staff_list = Utils::buildStaffList($staff_list);
				}
			}
			
		} catch (\Exception $e) {
			$status = "failure";
			$message = "Chọn người quản lý không thành công. Lỗi: " . $e->getMessage();
		}
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body, 'total_staff'=>$total_staff, 'staff_list'=>$staff_list]);
	}
	
	public function loadStaffInfo($staff_id) {
		$html = '';
		try {
			$staff = User::find($staff_id);
			if (!empty($staff)) {
				
				$dept = $staff->parent;
				$full_dept_name = '<i>(Chưa thuộc phòng ban nào)</i>';
				if (!empty($dept)) {
					$full_dept_name = $dept->name;
					$dept_prefix = $dept->prefixes;
					if (!empty($dept_prefix)) {
						$full_dept_name = $dept_prefix->name . ' ' . $full_dept_name;
					}
				}
				
				$title_list = Title::all();
				
				$html = 
					 '<input type="hidden" id="edit-dept-id" value="'.$staff->department_id.'" />'
					.'<input type="hidden" id="edit-staff-id" value="'.$staff->id.'" />'
				.'<p style="font-weight: 700;">Thuộc: '.$full_dept_name.'</p>'
				.'<div class="row">'
 					.'<div class="col-4">'
 						.'<input id="edit-staff-code" type="text" class="form-control" '
 							.'name="staff-code" value="'.$staff->staff_code.'" required '
 							.'placeholder="Mã nhân viên" />'
 					.'</div>'
 					.'<div class="col-4">'
 						.'<input id="edit-family_name" type="text" class="form-control" '
 							.'name="family_name" value="'.$staff->family_name.'" required '
 							.'placeholder="Họ" />'
 					.'</div>'
					.'<div class="col-4">'
						.'<input id="edit-given_name" type="text" class="form-control" '
							.'name="given_name" value="'.$staff->given_name.'" required '
							.'placeholder="Tên" />'
					.'</div>'
				.'</div>'
				.'<div class="row margin-top">'
					.'<div class="col-12 input-group">'
						.'<div class="input-group-prepend" id="edit-email-icon">'
							.'<span class="input-group-text"><i class="fas fa-envelope"></i></span>'
						.'</div>'
						.'<input type="email" id="edit-email" name="email" class="form-control" placeholder="Email" value='.explode("@", $staff->email)[0].'>'
						.'<div class="input-group-append" id="email-surfix"><span class="input-group-text">@s-connect.net</span></div>'
					.'</div>'
				.'</div>'
				.'<div class="row margin-top">'
					.'<div class="col-6">Mật khẩu</div>'
					.'<div class="col-6">Nhập lại mật khẩu</div>'
				.'</div>'
				.'<div class="row">'
					.'<div class="col-6">'
						.'<input id="edit-password" type="password" class="form-control" name="password" required  />'
					.'</div>'
					.'<div class="col-6">'
						.'<input id="edit-password_confirm" type="password" class="form-control" name="password_confirm" required  />'
					.'</div>'
				.'</div>'

				.'<div class="row margin-top">'
					.'<div class="col-6">'
						.'Chức vụ'
					.'</div>'
					.'<div class="col-6">'
						.'Nhóm'
					.'</div>'
				.'</div>'
				.'<div class="row margin-top">'
					.'<div class="col-6">'
						.'<select id="edit-title" class="form-control">'
							.'<option value="0">-- Chọn chức vụ --</option>';
							foreach($title_list as $title) {
								$html .= '<option value="'. $title->id .'"'.($title->id == $staff->position ? ' selected' : '').'>'. $title->name .'</option>';
							}
						$html .= '</select>'
					.'</div>'
					.'<div class="col-6">'
						.'<select id="edit-permission" class="form-control">'
							.'<option value="-">-- Chọn nhóm --</option>';
							foreach(\App\Constant::STAFF_GROUPS as $key=>$group) {
								$html .= '<option value="'. $key .'"'.($key == $staff->permission ? ' selected' : '').'>'. $group .'</option>';
							}					
						$html .= '</select></div>'
				.'</div>'
				.'<div class="row margin-top">'
					.'<div class="col-6">Giới tính</div>'
					.'<div class="col-6">Điện thoại liên hệ</div>'
				.'</div>'
				.'<div class="row">'
					.'<div class="col-3 margin-top">'
						.'<div class="custom-control custom-radio">'
							.'<input type="radio" class="custom-control-input" id="edit-gender-male" name="edit-radio-gender" value="1" '.($staff->gender == 1 ? 'checked' : '').'>'
							.'<label class="custom-control-label" for="edit-gender-male">Nam</label>'
						.'</div>'
					.'</div>'
					.'<div class="col-3 margin-top">'
						.'<div class="custom-control custom-radio">'
							.'<input type="radio" class="custom-control-input" id="edit-gender-female" name="edit-radio-gender" value="2"'.($staff->gender == 2 ? 'checked' : '').'>'
							.'<label class="custom-control-label" for="edit-gender-female">Nữ</label>'
						.'</div>'
					.'</div>'
					.'<div class="col-6">'
						.'<input id="edit-phone" type="text" class="form-control" '
							.'name="phone" value="'.$staff->phone.'" required '
							.'placeholder="Số điện thoại" />'
					.'</div>'
				.'</div>';
			}
		} catch (\Exception $e) {
		}
		return $html;
	}
	
	public function updateStaffStatus($staff_id, $new_status) {
		$status = "failure";
		$message = "Cập nhật trạng thái nhân viên không thành công";
		$body = '';
		try {
			$staff = User::find($staff_id);
			if (!empty($staff)) {
				$staff->status = $new_status;
				$staff->save();
				
				$dept = Department::find($staff->department_id);
				
				$staff_list = User::where(['department_id' => $staff->department_id])
					->where('id', '<>', $dept->manager_id)->get();
				$body = Utils::buildStaffList($staff_list);
				
				$status = "success";
				$message = "Cập nhật trạng thái nhân viên thành công";
			}
		} catch (\Exception $e) {
		}
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function updateManagerStatus($staff_id, $new_status) {
		$status = "failure";
		$message = "Cập nhật trạng thái nhân viên không thành công";
		$body = '';
		try {
			$staff = User::find($staff_id);
			if (!empty($staff)) {
				$staff->status = $new_status;
				$staff->save();
				
				$dept = Department::find($staff->department_id);
				
				$body = $this->buildManagerArea($staff, $dept);
				
				$status = "success";
				$message = "Cập nhật trạng thái nhân viên thành công";
			}
		} catch (\Exception $e) {
		}
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body]);
	}
	
	public function transferToDepartment($staff_id, $new_dept_id) {
		$status = "failure";
		$message = "Thuyên chuyển nhân viên không thành công";
		$body = '';
		$total_staff = 0;
		try {
			$staff = User::find($staff_id);
			if (!empty($staff)) {
				$old_dept_id = $staff->department_id;				
				$staff->department_id = $new_dept_id;
				$staff->save();
				
				$staff_list = User::where(['department_id' => $old_dept_id])
					->where('id', '<>', Department::find($old_dept_id)->manager_id)
					->get();
				$body = Utils::buildStaffList($staff_list);
				
				$status = "success";
				$message = "Thuyên chuyển nhân viên thành công";
				$total_staff = count($staff_list);
				
				$dept_children = Utils::buildDeptChildrenList(Department::where('parent_id', $old_dept_id)->get());
				
				$staff_list_none_dept = User::where(['department_id' => 0])->get();
				$staff_list_none_dept = Utils::buildStaffListNoneDept($staff_list_none_dept);
			}
		} catch (\Exception $e) {
		}
		return response()->json(['status'=>$status, 'message'=>$message, 'body'=>$body, 'total_staff'=>$total_staff, 
		    'dept_children'=>$dept_children, 'staff_list_none_dept'=>$staff_list_none_dept]);
	}
	
	public function getDeleteStaffInfo($staff_id) {
		$staff = User::find($staff_id);
		$html = '';
		
		if (!empty($staff)) {
			
			$dept = $staff->parent;
			$dept_name = $dept->prefixes->name . ' ' . $dept->name;
			
			$html = '<div class="row">'
				. '<div class="col-12"><strong>Xác nhận thông tin nhân viên định xoá:</strong><br/>&nbsp;</div>'
				. '<div class="col-4">Mã nhân viên:</div><div class="col-8"><strong>'.$staff->staff_code.'</strong></div>'
				. '<div class="col-4">Tên nhân viên:</div><div class="col-8"><strong>'.$staff->name.'</strong></div>'
				. '<div class="col-4">Chức vụ:</div><div class="col-8"><strong>'.$staff->title->name.'</strong></div>'						
				. '<div class="col-4">Phòng ban:</div><div class="col-8"><strong>'.$dept_name.'</strong></div>'						
				. '</div>';
		}
		return $html;
	}
	
	public function deleteStaff($staff_id) {
		$status = "failure";
		$message = "Xoá nhân viên không thành công";
		$staff_list = '';
		$total_staff = 0;
		$manager_body = '';
		try {
			$staff = User::find($staff_id);
			
			$dept_id = $staff->department_id;			
			
			if (!empty($staff)) {
				
				$staff->delete();
				$dept = Department::find($dept_id);
								
				if ($dept->manager_id == $staff_id) {	// delete manager -> reload manager area
					$manager_body = '<i style="padding-left: 10px" >(Chưa phân quyền người giám sát)</i>';
				} else {	// delete normal staff -> reload staff list
					$staff_list = User::where(['department_id' => $dept_id])
						->where('id', '<>', $dept->manager_id)
						->get();
					$total_staff = count($staff_list) . '';
					$staff_list = Utils::buildStaffList($staff_list);
					
				}
				$status = "success";
				$message = "Xoá nhân viên thành công";
			}
		} catch (\Exception $e) {
			$message .= '. Lỗi: ' . $e->getMessage(); 
		}
		return response()->json(['status'=>$status, 'message'=>$message, 
			'staff_list'=>$staff_list, 'total_staff'=>$total_staff, 'manager_body'=>$manager_body]);
	}
}
