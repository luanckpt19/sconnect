<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Response;

class Utils {
	
	public static function replaceQuot($input) {
		return str_replace("'", "\\'", str_replace('"', '\\"', $input));
	}
	
	public static function nullToEmpty($input) {
		if (empty($input)) $input = '';
		return $input;
	}
	
	public static function createBreadcrumb($arr_item) {
	    $first_item = $arr_item[0];
	    unset($arr_item[0]);
		$html = '<ul class="breadcrumb">'
		    . '<li class="breadcrumb-item"><i class="fas fa-home"></i> '.$first_item.'</li>';
		foreach($arr_item as $item) {
			$html .= '<li class="breadcrumb-item">'.$item.'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	public static function is_youtube_link($url) {
	    return preg_match('/(?:https?:\/\/)?(?:www.)?(?:youtube.com|youtu.be)\//m', $url);
	}
		
	
	/*
	* Hàm này gen ra html danh sách prefix phòng ban trong table
	*/
	public static function buildDeptPrefixList($prefix_list) {
		$html = '';
		if (!empty($prefix_list) && count($prefix_list) > 0) {
			foreach($prefix_list as $prefix) {
				$html .= '<tr>'
					. '<td align="center">'. $prefix->id .'</td>'
					. '<td>'
						. '<span id="prefix-btn-edit-'. $prefix->id .'"><a href="javascript: edit_prefix('. $prefix->id .');"><i class="far fa-edit"></i></a> &nbsp;&nbsp;</span>'
						. '<span id="prefix-name-'. $prefix->id .'">'.$prefix->name .'</span></td>'
					. '<td align="center" id="action-'. $prefix->id .'">'
						. '<input type="checkbox" name="prefix-checkbox[]" value="'.$prefix->id.'"/>'
					. '</td>'
				. '</tr>';
			}
		}
		return $html;
	}
	
	/*
	* Hàm này gen ra html danh sách chức vụ trong table
	*/
	public static function buildTitleList($title_list) {
		$html = '';
		if (!empty($title_list) && count($title_list) > 0) {
			foreach($title_list as $title) {
				$html .= '<tr>'
					. '<td align="center">'. $title->id .'</td>'
					. '<td>'
						. '<span id="title-btn-edit-'. $title->id .'"><a href="javascript: edit_title('. $title->id .');"><i class="far fa-edit"></i></a> &nbsp;&nbsp;</span>'
						. '<span id="title-name-'. $title->id .'">'.$title->name .'</span></td>'
					. '<td align="center" id="action-'. $title->id .'">'
						. '<input type="checkbox" name="title-checkbox[]" value="'.$title->id.'"/>'
					. '</td>'
				. '</tr>';
			}
		} else {
			$html = '<tr><td colspan="3" align="center"><i>Chưa có bản ghi</i></td></tr>';
		}
		return $html;
	}
	
	/*
	* Hàm này gen ra html danh sách phòng ban phụ ở trang Nhân viên
	*/
	public static function buildDeptChildrenList($list) {
		$html = '';
		foreach ($list as $child) {
			$manager = $child->manager;
			$child_prefixes = $child->prefixes;
			$total_staffs = $child->total_staffs;
			if (!empty($manager)) $total_staffs--;
			
			$html .= '<div><i class="fas fa-briefcase" style="font-size: 12px; margin-right: 6px;"></i>'
				. '<a href="/staff?dept_id='.$child->id.'">'. (!empty($child_prefixes) ? $child_prefixes->name : '').' '.$child->name.'</a>'
				. ' <small>('.$total_staffs.' nhân viên) (Người quản lý - </small>' . (!empty($manager) ? '<span class="link-underline-hover cursor-hand text-color1" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$manager->id.'">'.$manager->family_name.' '.$manager->given_name.'</span>' : '') 
				. ')</div>';
		}
		return $html;
	}

	/*
	* Hàm này gen ra html cây thư mục phòng ban ở trang Công ty
	*/
	public static function buildDeptTree($dept, $level) {
		if (empty($dept)) return '';
		
		$manager = $dept->manager;		
		$prefix_name = empty($dept->prefixes) ? '' : htmlspecialchars($dept->prefixes->name) . ' ';
		$prefix_id = empty($dept->prefixes) ? 0 : $dept->prefixes->id;
		$dept_name = htmlspecialchars( $dept->name);
		$parent_name = empty($dept->parent) ? '' : htmlspecialchars($dept->parent->name);
		$parent_prefix = '';
		if (!empty($dept->parent)) {
			$dpp = $dept->parent->prefixes;
			if (!empty($dpp)) {
				$parent_prefix = htmlspecialchars($dpp->name);
			}
		}
		
		$htmlManager = '<div class="structure-boss-block cursor-hand link-underline-hover" data-toggle="modal" data-target="#modal-delegate" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-prefixname="'.$prefix_name.'"><div>Chưa giao quản lý</div><div><i class="fas fa-user-cog"></i> Giao quyền</div></div>';		

		if (!empty($manager)) {
			$title = $manager->title;
			$htmlManager = '<div class="structure-boss-block">'
				. '<span class="cursor-hand structure-avatar"'
				. (!empty($manager->picture) ? ' style="background: url(\''.$manager->picture.'\') no-repeat scroll center center transparent; background-size: cover;"' : '')
				. '></span>'
				. '<span class="cursor-hand structure-boss-name link-underline-hover text-color2">' . $manager->family_name . ' ' . $manager->given_name . '</span>'
				. '<span class="structure-manager">' . (!empty($title) ? $title->name : '') . '</span>'
				. '</div>';
		}
		
		$html = '<li>'
			. '<code>'
			. '<div class="'.($level == 0 ? 'dept-title1' : 'dept-title2').' line">'
			. '<a href="javascript: void(0);" data-toggle="dropdown" aria-expanded="true">'
			. '<i class="fas fa-bars"></i>'
			. '</a>'
			. '<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform;">'
			. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-add-dept" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-parentname="'.$parent_name.'" data-prefixid="'.$prefix_id.'" data-prefixname="'.$prefix_name.'" data-parentprefix="'.$parent_prefix.' "><i class="fas fa-folder-plus ic24"></i> Thêm phòng ban con</div>'
			. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-edit-dept" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-parentname="'.$parent_name.'" data-prefixid="'.$prefix_id.'" data-prefixname="'.$prefix_name.'" data-parentprefix="'.$parent_prefix.' "><i class="far fa-edit ic24"></i> Sửa phòng ban</div>'
			. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-delegate" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-prefixname="'.$prefix_name.'"><i class="fas fa-user-cog ic24"></i> Giao quyền quản lý</div>'
			. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-add-staff" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-prefixname="'.$prefix_name.'"><i class="fas fa-user-plus ic24"></i> Thêm nhân viên</div>'
			. '<div class="dropdown-divider"></div>'
			. '<div class="dropdown-item cursor-hand color-danger" data-toggle="modal" data-target="#modal-delete-warning" data-deptname="'.$dept_name.'" data-deptid="'.$dept->id.'" data-prefixname="'.$prefix_name.'"><i class="far fa-trash-alt ic24" style="color:#ff5648!important"></i> Xoá phòng ban</div>'
			. '</div>'
			. '&nbsp; <span class="cursor-hand" onclick="location.href=\'/staff?dept_id='.$dept->id.'\'">'. $prefix_name . $dept_name . '</span>'
			. '</div>'

			. '<div class="body">'
			. $htmlManager
			. '<div style="font-size: 13px; text-align: center; padding-top: 15px;">'.$dept->total_staffs.' nhân viên</div></div>'
			. '</code>';
		
		$deptChildren = $dept->children;
		if(!empty($deptChildren) && count($deptChildren) > 0) {
			$html .= '<ul>';
			foreach($deptChildren as $deptChild) {
				$html .= Utils::buildDeptTree($deptChild, $level + 1);
			}
			$html .= '</ul>';
		}
		
		$html .= '</li>';
		return $html;
	}
	
	
	/*
	 * Hàm này gen ra html danh sách nhân viên chưa thuộc phòng ban nào ở trang Nhân viên
	 */
	public static function buildStaffListNoneDept($staff_list) {
	    $html = '';
	    if (!empty ($staff_list) && count($staff_list) > 0) {
	        
	        $html .= '<div class="col-md-12"><div class="card"><div class="card-body"><h5>Nhân viên chưa thuộc phòng ban nào</h5>';
	        
	        foreach($staff_list as $staff) {
	            $msg_status = $staff->status == 1 ? 'Đang làm việc' : 'Đang ngừng làm việc';
	            $ic_status = $staff->status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off';
	            $color_status = $staff->status == 1 ? 'style="color: #38B235"' : 'style="color: #666666"';
	            
	            $html .= '<div class="row" style="padding: 24px 0px 10px 0px; border-bottom: 1px solid #cccccc">'
	                . '<div class="col-md-5">'
	                . '<a href="#" class="dept-manager-avatar user-default-avatar"'
                    . (!empty($staff->picture) ? 'style="background-image: url('.$staff->picture.')"' : '') .'></a>'
                    . '<div style="padding-right: 30px;">';
                $staff_title = $staff->title;
                $html .= '<div class="title text-blue">'
                    . '<span class="link-underline-hover cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$staff->id.'">' . $staff->family_name . ' ' . $staff->given_name . '</span></div>'
	                . '<div class="alias">' . (!empty($staff_title) ? $staff_title->name : '') 
					. (!empty($staff->permission) ? ' - ' . Constant::STAFF_GROUPS[$staff->permission] : '')
					. '</div>'
                    . '<div style="alias"><span style="font-size: 80%;"><i class="'.$ic_status.'" '.$color_status.'></i> ' . $msg_status . '</span></div>'
                    . '</div></div>'
                    . '<div class="col-md-4">'
                    . '<div>Mã số: <strong>'.$staff->staff_code.'</strong></div>'
                    . '<div>Điện thoại: <a href="callto:' . $staff->phone . '">' . $staff->phone. '</a></div>'
                    . '<div>Email: <a href="mailto:' . $staff->email . '">' . $staff->email . '</a></div></div>'
                    . '<div class="col-md-3" style="position:relative;">';
                $dept_name = '';
                $dept_id = 0;
	                                                                
                $dept = $staff->parent;
                if (!empty($dept)) {
                    $dept_id = $dept->id;
                    $dept_prefix = $dept->prefixes;
                    if (!empty($dept_prefix)) $dept_name = $dept_prefix->name . ' ' . $dept->name;
                }

                $val_status = $staff->status == 1 ? 0 : 1;
                $action_status = $staff->status == 1 ? 'Tạm khoá' : 'Mở khoá';
	                                                                
                $html .= '<div style="float: left; padding-top: 10px;"><a href="/staff?dept_id=' . $dept_id . '">' . $dept_name . '</a></div>'
                    .'<div style="float: right; padding-top: 6px;">'
                    .'<a href="javascript: void(0);" data-toggle="dropdown" aria-expanded="true" class="circle-vert">'
                    .'<i class="fas fa-ellipsis-v"></i></a>'
                    . '<div class="dropdown-menu" x-placement="top-end" style="position: absolute; transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform;">'
                    . '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$staff->id.'"><i class="far fa-user-circle ic24"></i> Thông tin nhân viên</div>'
                    . '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-transfer" data-staffid="'.$staff->id.'"><i class="fas fa-exchange-alt ic24"></i> Chuyển phòng ban</div>'
                    . '<div class="dropdown-item cursor-hand" onclick="blockStaff('.$staff->id.','.$val_status.')"><i class="'.$ic_status.' ic24"></i> '.$action_status.'</div>'
                    . '<div class="dropdown-divider"></div>'
                    . '<div class="dropdown-item cursor-hand color-danger" data-toggle="modal" data-target="#modal-delete-staff" data-staffid="'.$staff->id.'"><i class="far fa-trash-alt ic24" style="color:#ff5648!important"></i> Xoá nhân viên</div>'
                    . '</div>'
                    .'</div> </div></div>';	                                                                                                            
	        }
	        
	        $html .= '</div></div></div>';
	    }
	    
	    return $html;
	}
	
	/*
	* Hàm này gen ra html danh sách nhân viên ở trang Nhân viên
	*/
	public static function buildStaffList($staff_list) {
		$html = '';
		if (!empty ($staff_list) && count($staff_list) > 0) {
			foreach($staff_list as $staff) {
				$msg_status = $staff->status == 1 ? 'Đang làm việc' : 'Đang ngừng làm việc';
				$ic_status = $staff->status == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off';
				$color_status = $staff->status == 1 ? 'style="color: #38B235"' : 'style="color: #666666"';
				
				$html .= '<div class="row" style="padding: 24px 0px 10px 0px; border-bottom: 1px solid #cccccc">'
					. '<div class="col-md-5">'
					. '<a href="#" class="dept-manager-avatar user-default-avatar"'
					. (!empty($staff->picture) ? 'style="background-image: url('.$staff->picture.')"' : '') .'></a>'
					. '<div style="padding-right: 30px;">';
				$staff_title = $staff->title;
				$html .= '<div class="title text-blue">'
					. '<span class="link-underline-hover cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$staff->id.'">' . $staff->family_name . ' ' . $staff->given_name . '</span></div>'
					. '<div class="alias">' . (!empty($staff_title) ? $staff_title->name : '') 
					. (!empty($staff->permission) ? ' - ' . Constant::STAFF_GROUPS[$staff->permission] : '')
					. '</div>'
					. '<div style="alias"><span style="font-size: 80%;"><i class="'.$ic_status.'" '.$color_status.'></i> ' . $msg_status . '</span></div>'
					. '</div></div>'
					. '<div class="col-md-4">'
					. '<div>Mã số: <strong>'.$staff->staff_code.'</strong></div>'
					. '<div>Điện thoại: <a href="callto:' . $staff->phone . '">' . $staff->phone. '</a></div>'
					. '<div>Email: <a href="mailto:' . $staff->email . '">' . $staff->email . '</a></div></div>'
					. '<div class="col-md-3" style="position:relative;">';
				$dept_name = '';
				$dept_id = 0;
				
				$dept = $staff->parent; 
				if (!empty($dept)) {
					$dept_id = $dept->id;
					$dept_prefix = $dept->prefixes;
					if (!empty($dept_prefix)) $dept_name = $dept_prefix->name . ' ' . $dept->name;
				}
				
				
				$val_status = $staff->status == 1 ? 0 : 1;
				$action_status = $staff->status == 1 ? 'Tạm khoá' : 'Mở khoá';
				
				$html .= 
					'<div style="float: left; padding-top: 10px;"><a href="/staff?dept_id=' . $dept_id . '">' . $dept_name . '</a></div>'
					.'<div style="float: right; padding-top: 6px;">'
					.'<a href="javascript: void(0);" data-toggle="dropdown" aria-expanded="true" class="circle-vert">'
					.'<i class="fas fa-ellipsis-v"></i></a>'
						. '<div class="dropdown-menu" x-placement="top-end" style="position: absolute; transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform;">'
						. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-info" data-staffid="'.$staff->id.'"><i class="far fa-user-circle ic24"></i> Thông tin nhân viên</div>'
						. '<div class="dropdown-item cursor-hand" data-toggle="modal" data-target="#modal-staff-transfer" data-staffid="'.$staff->id.'"><i class="fas fa-exchange-alt ic24"></i> Chuyển phòng ban</div>'
						. '<div class="dropdown-item cursor-hand" onclick="blockStaff('.$staff->id.','.$val_status.')"><i class="'.$ic_status.' ic24"></i> '.$action_status.'</div>'
						. '<div class="dropdown-divider"></div>'
						. '<div class="dropdown-item cursor-hand color-danger" data-toggle="modal" data-target="#modal-delete-staff" data-staffid="'.$staff->id.'"><i class="far fa-trash-alt ic24" style="color:#ff5648!important"></i> Xoá nhân viên</div>'
						. '</div>'
					.'</div> </div></div>';
				
			}
		} else {
			$html = '<i>(Không có nhân viên)</i>';
		}
		
		return $html;
	}
	
	public static function loadDeptTreeForTransfer($dept_node, $staff_dept_id) {
		if (empty($dept_node)) return '';
		$dept_prefixes = $dept_node->prefixes;
		$dept_prefix_name = '';
		if (!empty($dept_prefixes)) $dept_prefix_name = $dept_prefixes->name . ' ';
		
		$checked = $dept_node->id == $staff_dept_id ? ' checked' : '';
				
		$html = '<li><div class="custom-control custom-radio">'
				.'<input class="custom-control-input" data-deptid="'.$dept_node->id.'" data-staffid="'.'" type="radio" id="dept-radio-'.$dept_node->id.'" name="dept-radio" value="'.$dept_node->id.'"'.$checked.'/> ' 
				.'<label for="dept-radio-'.$dept_node->id.'" class="custom-control-label">'. $dept_prefix_name.$dept_node->name . '</label></div>';
		
		$dept_children = $dept_node->children;
		if ($dept_children != null && count($dept_children) > 0) {
			$html .= '<ul>';
			foreach($dept_children as $dept) {
				$html .= Utils::loadDeptTreeForTransfer($dept, $staff_dept_id);
			}
			$html .= '</ul>';
		}
		$html .= '</li>';
		return $html;
	}
	
	/*
	 * 
	 * */
	public static function buildTopicOptionList($node, $level, $selected_value = 0) {
	    
	    if (empty($node)) return '';
	    $pad = ''; $tab = '';
	    if ($level > 0) {
	        for( $i = 0; $i < $level; $i++) {
	            $pad .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	        }
	        $tab = '&#124;--';
	    }
	    
	    $html = '<option value="'.$node->id.'"' . ($node->id == $selected_value ? ' selected' : '') . '>'.$pad.$tab.$node->name.'</option>';
	    
	    $children = $node->children;
	    if (!empty($children)) {
	        foreach ($children as $item) {
	            $html .= Utils::buildTopicOptionList($item, $level + 1, $selected_value);
	        }
	    }
	    
	    return $html;
	}
	
	public static function getDepartmentName() {
	    $dept_name = '';
	    $dept = Auth::user()->parent;
	    if (!empty($dept)) {
	        $dept_name = $dept->name;
	        $dept_prefix = $dept->prefixes;
	        if (!empty($dept_prefix)) {
	            $dept_name = $dept_prefix->name . ' ' . $dept_name;
	        }
	    }
	    return $dept_name;
	}
	public static function getDepartmentId() {
	    $dept = Auth::user()->parent;
	    if (!empty($dept)) {
	        return $dept->id;
	    }
	    return 0;
	}
	
	public static function startsWith ($string, $startString) {
	    $len = strlen($startString);
	    return (substr($string, 0, $len) === $startString);
	}
	
	public static function endsWith($string, $endString) {
	    $len = strlen($endString);
	    if ($len == 0) {
	        return true;
	    }
	    return (substr($string, -$len) === $endString);
	}

	public static function download($url, $file_name) {    
		return Response::download(public_path(). $url, $file_name);
	}
}