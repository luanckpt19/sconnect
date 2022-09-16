@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Hệ thống', 'Nhân viên')) !!}
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					@php 
						$dept_prefixes = $dept->prefixes; 
						$dept_prefix_name = (!empty($dept_prefixes) ? $dept_prefixes->name : '') . ' ' . $dept->name;
						
					@endphp
					<h1 class="heading1 text-color1">{{ $dept_prefix_name }}</h1>
					
					@php $parent = $dept->parent @endphp
					@if(!empty($parent))
					<div style="padding-top: 10px"><h2 class="heading3 bold"><i class="fas fa-table"></i> Phòng ban cha</h2></div>
					<div style="padding-bottom: 10px">
						@php $parent_prefixes = $parent->prefixes; @endphp
						<a href="/staff?dept_id={{ $parent->id }}">{{ !empty($parent_prefixes) ? $parent_prefixes->name : '' }} {{ $parent->name }}</a>
						@php 
						$manager = $parent->manager;
						
						$total_staffs = $parent->total_staffs;
						if (!empty($manager) && $manager->id == $parent->manager_id) {
							$total_staffs--;
						}
						@endphp
						 <small>({{ $total_staffs }} {{ config('constant.staff') }}) ({{ config('constant.manager') }} - </small>@if(!empty($manager)) 
						 	<span class="link-underline-hover cursor-hand text-blue"
								data-toggle="modal" data-target="#modal-staff-info" data-staffid="{{ !empty($manager) ? $manager->id : 0 }}">{{ $manager->family_name }} {{ $manager->given_name }}</span> 
						 @endif)
					</div>
					@endif
					
					<div style="padding-top: 20px"><h2 class="heading3 bold"><i class="fas fa-user-tie"></i> {{ config('constant.supervisor') }} 
						<span class="cursor-hand text-blue link-underline-hover" data-toggle="modal" 
							data-target="#modal-delegate" style="font-weight: normal; font-size: 13px;"
							data-whatever="{{ htmlspecialchars($dept_prefix_name) }}" data-whatid="{{ $dept->id }}">
							[{{ config('constant.update') }}]</span></h2></div>
					
					<div class="row" style="padding-top: 10px" id="manager_info">
					@php 
						$manager = $dept->manager;
						if ($manager) {
						$staffController = new App\Http\Controllers\StaffController();
					@endphp
						{!! $staffController->buildManagerArea($manager, $dept) !!}
					@php } else { @endphp
						<i style="padding-left: 10px" >(Chưa phân quyền người giám sát)</i>
					@php } @endphp
					</div>
					
					@php $children = $dept->children @endphp
					<div style="padding-top: 30px"><h2 class="heading3 bold"><i class="fas fa-table"></i> Phòng ban phụ</h2></div>
					<div style="padding: 0px 15px;" id="dept-children">
					@if (!empty($children) && count($children) > 0)
						{!! App\Utils::buildDeptChildrenList($children) !!}
					@else
						<i>(Không có)</i>
					@endif
					</div>
					<div style="padding: 10px 15px;">
						<span class="cursor-hand" data-toggle="modal" data-target="#modal-add-dept" data-whatever="{{ htmlspecialchars($dept_prefix_name) }}" data-whatid="{{ $dept->id }}">
								<i class="fas fa-folder-plus" style="font-size: 12px; margin-right: 6px;"></i> <span class="text-color1" style="text-decoration: underline;">Thêm phòng ban phụ</span></span>
					</div>
					
					<div class="row" style="padding-top: 30px;">					
						<div class="col-sm-4 heading3 bold"><i class="fas fa-users"></i> Nhân viên (<span id="total-staff">{{ count($staff_list) }}</span>)</div>
						<div class="col-sm-8" align="right">
							<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-add-staff" data-whatever="{{ htmlspecialchars($dept_prefix_name) }}" data-whatid="{{ $dept->id }}">
								<i class="fas fa-user-plus"></i> Thêm nhân viên</span>
						</div>
					</div>
					<div id="staff-list">
					@if (!empty($staff_list) && count($staff_list) > 0)
					{!! App\Utils::buildStaffList($staff_list) !!}
					@else
					<i>(Không có nhân viên)</i>
					@endif
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="row" id="staff_list_none_dept">
	{!! App\Utils::buildStaffListNoneDept($staff_list_none_dept) !!}
	</div>	
</div>

<!--  Model add new staff -->
<div class="modal fade" id="modal-add-staff">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Thêm nhân viên mới</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form method="POST" action="#">@csrf
			<div class="modal-body">
				<input type="hidden" id="dept-id" value="0" />
				<input type="hidden" id="staff-id" value="0" />
				<p style="font-weight: 700;">Thuộc: <span id="dept-is"></span></p>
				
				<div class="row">
					<div class="col-4">
						<input id="staff-code" type="text" 
							class="form-control @error('staff_code') is-invalid @enderror" 
							name="staff_code" value="{{ old('staff_code') }}" required
							placeholder="Mã nhân viên" />
					</div>
					<div class="col-4">
						<input id="family_name" type="text" 
							class="form-control @error('family_name') is-invalid @enderror" 
							name="family_name" value="{{ old('family_name') }}" required 
							placeholder="Họ" />
					</div>
					<div class="col-4">
						<input id="given_name" type="text" 
							class="form-control @error('given_name') is-invalid @enderror" 
							name="given_name" value="{{ old('given_name') }}" required 
							placeholder="Tên" />
					</div>					
				</div>
				<div class="row margin-top">
					<div class="col-12 input-group">
						<div class="input-group-prepend" id="email-icon">
							<span class="input-group-text"><i class="fas fa-envelope"></i></span>
						</div>
						<input type="email" id="email" name="email" class="form-control" placeholder="Email">
						<div class="input-group-append" id="email-surfix"><span class="input-group-text">@s-connect.net</span></div>
					</div>
				</div>
				<div class="row margin-top">
					<div class="col-6">Mật khẩu</div>
					<div class="col-6">Nhập lại mật khẩu</div>
				</div>
				<div class="row">
					<div class="col-6">
						<input id="password" type="password" class="form-control" name="password" required  />
					</div>
					<div class="col-6">
						<input id="password_confirm" type="password" class="form-control" name="password_confirm" required  />
					</div>
				</div>
				<div class="row margin-top">
					<div class="col-6">
						Chức vụ
					</div>
					<div class="col-6">
						Nhóm
					</div>
				</div>
				<div class="row margin-top">
					<div class="col-6">
						<select id="title" id="name" class="form-control">
							<option value="0">-- Chọn chức vụ --</option>
							@foreach($title_list as $title)
							<option value="{{ $title->id }}">{{ $title->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-6">
						{!! Form::select('permission', array_merge(['-'=>'-- Chọn nhóm --'], \App\Constant::STAFF_GROUPS), null, ['class'=>'form-control', 'id'=>'permission']) !!}
					</div>
				</div>
				<div class="row margin-top">
					<div class="col-6">
						Giới tính
					</div>
					<div class="col-6">
						Điện thoại liên hệ
					</div>
				</div>
				<div class="row">
					<div class="col-3 margin-top">
						<div class="custom-control custom-radio">
							<input type="radio" class="custom-control-input" id="gender-male" name="radio-gender" value="1" checked>
							<label class="custom-control-label" for="gender-male">Nam</label>
						</div>
					</div>
					<div class="col-3 margin-top">
						<div class="custom-control custom-radio">
							<input type="radio" class="custom-control-input" id="gender-female" name="radio-gender" value="2">
							<label class="custom-control-label" for="gender-female">Nữ</label>
						</div>
					</div>
					<div class="col-6">
						<input id="phone" type="text" 
							class="form-control @error('phone') is-invalid @enderror" 
							name="phone" value="{{ old('phone') }}" required 
							placeholder="Số điện thoại" />
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-save-staff">Lưu thay đổi</button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model delegate staff for management -->
<div class="modal fade" id="modal-delegate">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Chọn quản lý cho phòng ban</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<p style="font-weight: 700;">Thuộc: <span id="dept-is"></span></p>				
				<div class="margin-top" id="staff-choose-list">
					Đang tải danh sách nhân viên...
				</div>
			</div>
			<div class="modal-footer justify-content-right">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model staff info -->
<div class="modal fade" id="modal-staff-info">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Thông tin nhân viên</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>				
			</div>
			<div class="modal-body" id="staff-info-detail">
				Đang tải thông tin thành viên ...
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-save-staff-info">Lưu lại</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model staff transfer -->
<div class="modal fade" id="modal-staff-transfer">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Thuyên chuyển nhân viên</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="modal-staff-id" name="modal-staff-id" value="0" />
			</div>
			<div class="modal-body" id="dept-list-transfer">
				<ul>
					<li>Đang tải thông tin phòng ban</li>
				</ul>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-staff-transfer">Chuyển</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model delete staff -->
<div class="modal fade" id="modal-delete-staff">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Xoá nhân viên</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="delete-staff-id" name="delete-staff-id" value="0" />
			</div>
			<div class="modal-body">
				<p id="delete-msg" class="color-danger">Đang tải thông tin nhân viên ...</p>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-danger" id="btn-delete-agree">Đồng ý xoá</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model add new department -->
<div class="modal fade" id="modal-add-dept">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Thêm phòng ban mới</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>			
			<div class="modal-body">
				<div style="font-weight: 700;">Thuộc: <span id="parent-dept-is"></span></div>
				<input type="hidden" id="parent-id" value="0" />
				<div style="padding-top: 10px;">
					<select id="prefix-list" class="form-control">
						<option value="0">-- Chọn loại phòng ban --</option>
						@foreach($prefixes as $prefix)
						<option value="{{ $prefix->id }}">{{ $prefix->name }}</option>
						@endforeach
					</select>
				</div>
				<div style="padding-top: 10px;"><input id="dept-name" type="text" 
					class="form-control" name="dept-name" value="" required 
					autofocus placeholder="Tên phòng ban"/></div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-save-dept">Lưu thay đổi</button>
			</div>			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script lang="text/javascript">
$(document).ready(function () {
	/* Staff process */
	$('#modal-add-staff').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var recipient = button.data('whatever');
		var modal = $(this);
		modal.find('#dept-is').text(recipient);
		modal.find('#dept-id').val(button.data('whatid'));
		$('#modal-delegate').modal('hide');
	});
	$('#modal-add-staff').on('shown.bs.modal', function () {
		$('#staff-code').trigger('focus');
	});
	$('#email-surfix').on('click', function() {
		$('#email').trigger('focus');
	});
	$('#email-icon').on('click', function() {
		$('#email').trigger('focus');
	});
	$('#btn-save-staff').on('click', function() {
		var modal = $('#modal-add-staff');
		// add new staff ...
		var staff_code = modal.find('#staff-code').val();
		var title_id = modal.find('#title').val();
		var dept_id = modal.find('#dept-id').val();
		var family_name = modal.find('#family_name').val();
		var given_name = modal.find('#given_name').val();
		var email = modal.find('#email').val();
		var password = modal.find('#password').val();
		var password_confirm = modal.find('#password_confirm').val();
		var gender = modal.find('input[name="radio-gender"]:checked').val();
		var phone = modal.find('#phone').val();
		var permission = modal.find('#permission').val();

		/* jquery post data	*/
		$.ajax({
		    url: "/staff/add-in-dept",
		    method: "POST",
		    data: {
				'staff_code': staff_code,
				'title_id': title_id, 
				'dept_id': dept_id, 
				'family_name': family_name, 
				'given_name': given_name, 
				'email': email + '@s-connect.net',
				'password': password,
				'password_confirm': password_confirm,
				'gender': gender,
				'phone': phone,
				'permission' : permission
			},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);	    		
		    		$('#staff-list').html(jsResult.body);
					$('#total-staff').html(jsResult.total_staff);
		    		modal.modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
		/**/
	});
	
	$('#btn-save-staff-info').on('click', function() {
		
		var modal = $('#modal-staff-info');
		// update staff info...
		var staff_code = modal.find('#edit-staff-code').val();
		var title_id = modal.find('#edit-title').val();
		var staff_id = modal.find('#edit-staff-id').val();
		var dept_id = modal.find('#edit-dept-id').val();
		var family_name = modal.find('#edit-family_name').val();
		var given_name = modal.find('#edit-given_name').val();
		var email = modal.find('#edit-email').val();
		var password = modal.find('#edit-password').val();
		var password_confirm = modal.find('#edit-password_confirm').val();
		var gender = modal.find('input[name="edit-radio-gender"]:checked').val();
		var phone = modal.find('#edit-phone').val();
		var permission = modal.find('#edit-permission').val();
		
		/* jquery post data	*/
		$.ajax({
		    url: "/staff/update-in-dept",
		    method: "POST",
		    data: {
				'curr_dept_id': {{ $dept->id }},
				'staff_code': staff_code,
				'staff_id': staff_id, 
				'title_id': title_id, 
				'dept_id': dept_id, 
				'family_name': family_name, 
				'given_name': given_name, 
				'email': email + '@s-connect.net',
				'password': password,
				'password_confirm': password_confirm,
				'gender': gender,
				'phone': phone,
				'permission' : permission
			},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
					if (jsResult.body && jsResult.body.trim() !== '') {
						$('#staff-list').html(jsResult.body);
					}
		    		if (jsResult.total_staff && jsResult.total_staff.trim() !== '') {
						$('#total-staff').html(jsResult.total_staff);
					}
					if (jsResult.manager_body && jsResult.manager_body.trim().length > 10) {
						$('#manager_info').html(jsResult.manager_body);	
					}
					// dept-children					
					if (jsResult.dept_children && jsResult.dept_children.trim().length > 10) {
						$('#dept-children').html(jsResult.dept_children);	
					}
					
		    		modal.modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
		/**/
	});
	
	$('#modal-delegate').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var recipient = button.data('whatever');
		var modal = $(this);
		modal.find('#dept-is').text(recipient);
		// /staff/load-staff/{dept_id}		
		$.ajax({
		    url: "/staff/load-staff/{{$dept->id}}",
		    method: "GET",
		    success: function (result) {	
		    	$('#staff-choose-list').html(result);   
	        }
		});
	});
	
	$('#modal-staff-info').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var staff_id = button.data('staffid');
		$.ajax({
		    url: "/staff/load-staff-info/" + staff_id,
		    method: "GET",
		    success: function (result) {	
		    	$('#staff-info-detail').html(result);
	        }
		});
	});
	
	// staff transfer popup
	$('#modal-staff-transfer').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var staff_id = button.data('staffid');
		$('#modal-staff-id').val(staff_id);
		
		$.ajax({
		    url: "/department/list-for-transfer/" + staff_id,
		    method: "GET",
		    success: function (result) {	
		    	$('#dept-list-transfer').html(result);
	        }
		});
	});
	$('#btn-staff-transfer').on('click', function() {
		var dept_radio = $('#modal-staff-transfer').find('input[name="dept-radio"]:checked');		
		$.ajax({
		    url: "/staff/transfer/"+$('#modal-staff-id').val()+"/to-dept/" + dept_radio.val(),
		    method: "GET",
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);	    		
		    		$('#staff-list').html(jsResult.body);
					$('#total-staff').html(jsResult.total_staff);
					$('#dept-children').html(jsResult.dept_children);
					$('#staff_list_none_dept').html(jsResult.staff_list_none_dept);
		    		$('#modal-staff-transfer').modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    } 
	        }
		});
	});
	
	// delete staff
	$('#modal-delete-staff').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var staffid = button.data('staffid');
		
		var modal = $(this);
		modal.find('#delete-staff-id').val(staffid);

		$.ajax({
		    url: '/staff/get-delete-staff/' + staffid,
		    method: "GET",
		    success: function (result) {	
		    	$('#delete-msg').html(result);
	        }
		});
	});
	
	$('#btn-delete-agree').on('click', function () {
		var modal = $('#modal-delete-staff').modal();
		var staffid = modal.find('#delete-staff-id').val();
		
		$.ajax({
		    url: '/staff/delete-staff/' + staffid,
		    method: "GET",
		    success: function (jsResult) {
				
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
					if (jsResult.staff_list && jsResult.staff_list.trim() !== '') {
			    		$('#staff-list').html(jsResult.staff_list);
						$('#total-staff').html(jsResult.total_staff);
					}
					if (jsResult.manager_body && jsResult.manager_body.trim() !== '') {
						$('#manager_info').html(jsResult.manager_body);	
					}
		    		
		    		$('#modal-delete-staff').modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    } 
	        }
		});
	});
	
	/* Department process */
	$('#modal-add-dept').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);		
		var modal = $(this);
		modal.find('#parent-dept-is').text(button.data('whatever'));
		modal.find('#parent-id').val(button.data('whatid'));
		modal.find('#dept-name').val('');
	});
	$('#modal-add-dept').on('shown.bs.modal', function () {
		$('#dept-name').trigger('focus');
	});
	$('#btn-save-dept').on('click', function() {
		var modal = $('#modal-add-dept');
		// add new department ...
		var p_id = modal.find('#parent-id').val();
		var dept_name = modal.find('#dept-name').val();
		var prefix = modal.find('#prefix-list').val();

		/* jquery post data	*/
		$.ajax({
		    url: "/department/addsub",
		    method: "POST",
		    data: {parent_id: p_id, name: dept_name, 'prefix': prefix},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {		    
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);	    		
		    		$('#dept-children').html(jsResult.body);
		    		modal.modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }            
	        }
		});
		/**/
	});
});

function select_manager(deptid, staffid) {	
	// /staff/assign-manager/{dept_id}/{user_id}
	$.ajax({
	    url: "/staff/assign-manager/"+deptid+"/" + staffid,
	    method: "GET",
	    success: function (jsResult) {
	    	if (jsResult.status === 'success') {
	    		toastr.success(jsResult.message);
	    		$('#manager_info').html(jsResult.body);
				
				$('#total-staff').html(jsResult.total_staff);
				$('#staff-list').html(jsResult.staff_list);
				
				$('#modal-delegate').modal('hide');
				
		    } else {
		    	toastr.error(jsResult.message);
		    }            
        }
	});	
}

function blockStaff(staffid, new_status) {
	$.ajax({
	    url: "/staff/status/"+staffid+"/" + new_status,
	    method: "GET",
	    success: function (jsResult) {
	    	if (jsResult.status === 'success') {
	    		$('#staff-list').html(jsResult.body);
		    } else {
		    	toastr.error(jsResult.message);
		    }            
        }
	});	
}
function blockManager(staffid, new_status) {
	$.ajax({
	    url: "/manager/status/"+staffid+"/" + new_status,
	    method: "GET",
	    success: function (jsResult) {
	    	if (jsResult.status === 'success') {
	    		$('#manager_info').html(jsResult.body);
		    } else {
		    	toastr.error(jsResult.message);
		    }            
        }
	});	
}
</script>
@endsection
