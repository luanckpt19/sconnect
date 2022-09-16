@extends('layouts.master')
@section('content')
<div class="container-fluid">
	{!! App\Utils::createBreadcrumb(array('Hệ thống', 'Công ty')) !!}
	<div class="row">
		<div class="col-md-12">
			<div class="card">		
				<div class="card-body scroll-x" align="center" style="min-height: 300px;">				
				<ul class="tree" id="department-structure">
				@if (empty($root_dept))
				<li>
    				<div class="text-gray" style="font-size: 24px;">Chưa có phòng ban</div>
    				<div style="font-size: 24px; padding: 20px;"><i class="fas fa-folder-plus"></i> 
    					<span class="link-underline-hover cursor-hand text-color1" id="add-first-dept"
    					data-toggle="modal" data-target="#modal-add-dept" data-deptname="Không có" data-deptid="0" 
    					data-parentname="Không có" data-prefixid="0" data-prefixname="" data-parentprefix="">Thêm phòng ban mới</span>
    				</div>
				</li>
				@else		
				{!! App\Utils::buildDeptTree($root_dept, 0) !!}
				@endif		
				</ul>				
				</div>
			</div>
		</div>
    </div>
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

<!--  Model delegate manager -->
<!--  Model choose staff for management -->
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

<!--  Model edit department -->
<div class="modal fade" id="modal-edit-dept">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Sửa thông tin phòng ban</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>			
			<div class="modal-body">
				<input type="hidden" id="dept-id" value="0" />				
				<div style="font-weight: 700;" id="dept-parent">Thuộc: <span id="parent-dept-is"></span></div>
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
				<button type="button" class="btn btn-primary" id="btn-update-dept">Lưu thay đổi</button>
			</div>			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
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
						{!! Form::select('permission', array_merge(['-'=>'-- Chọn nhóm --'], \App\Constant::STAFF_GROUPS), null, ['class'=>'form-control']) !!}
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
<!--  Model delete department -->
<div class="modal fade" id="modal-delete-warning">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Xoá phòng ban</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<p id="delete-msg" class="color-danger">...</p>
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

<script type="text/javascript">


$(document).ready(function () {
	/* Department process */
	$('#modal-add-dept').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);		
		var modal = $(this);
		modal.find('#parent-dept-is').text(button.data('prefixname') + ' ' + button.data('deptname'));
		modal.find('#parent-id').val(button.data('deptid'));
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
		    url: "/department/add",
		    method: "POST",
		    data: {parent_id: p_id, name: dept_name, 'prefix': prefix},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {		    
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);	    		
		    		$('#department-structure').html(jsResult.body);
		    		modal.modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }            
	        }
		});
		/**/
	});
	
	$('#modal-edit-dept').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);		
		var modal = $(this);
		modal.find('#parent-dept-is').text(button.data('parentprefix') + button.data('parentname'));
		if (button.data('parentname') === '') {
			modal.find('#dept-parent').hide();
		}
		modal.find('#dept-name').val(button.data('deptname'));		
		modal.find('#dept-id').val(button.data('deptid'));
		modal.find('#prefix-list').val(button.data('prefixid'));		

	});
	
	$('#btn-update-dept').on('click', function() {
		var modal = $('#modal-edit-dept');
		// update department ...
		var dept_id = modal.find('#dept-id').val();
		var dept_name = modal.find('#dept-name').val();
		var prefix = modal.find('#prefix-list').val();

		/* jquery post data	*/
		$.ajax({
		    url: "/department/update",
		    method: "POST",
		    data: {id: dept_id, name: dept_name, 'prefix': prefix},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {		    
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);	    		
		    		$('#department-structure').html(jsResult.body);
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
		var modal = $(this);
		modal.find('#dept-is').text(button.data('prefixname') + ' ' + button.data('deptname'));
		// /staff/load-staff/{dept_id}		
		$.ajax({
		    url: "/staff/load-staff/" + button.data('deptid'),
		    method: "GET",
		    success: function (result) {	
		    	$('#staff-choose-list').html(result);   
	        }
		});
	});
	
	/* Staff process */
	$('#modal-add-staff').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var recipient = button.data('prefixname') + ' ' + button.data('deptname');
		var modal = $(this);
		modal.find('#dept-is').text(recipient);
		modal.find('#dept-id').val(button.data('deptid'));
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
		    url: "/staff/add",
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
		    		$('#department-structure').html(jsResult.body);
		    		modal.modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
		/**/
	});

	var deptIdToDelete = 0;

	$('#modal-delete-warning').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var recipient = button.data('prefixname') + ' ' + button.data('deptname');
		deptIdToDelete = button.data('deptid');
		var modal = $(this);
		modal.find('#delete-msg').html("Có chắc chắn xoá phòng ban <strong >#"+deptIdToDelete+": " + recipient + "</strong>?");
	})
	$('#btn-delete-agree').on('click', function() {
		// delete department ...
		$.get( "/department/delete-" + deptIdToDelete, function( jsResult ) {
	    	if (jsResult.status === 'success') {	    		
	    		toastr.success(jsResult.message);	    		
	    		$('#department-structure').html(jsResult.body);    		
		    } else {
		    	toastr.error(jsResult.message);
		    }
		});
		$('#modal-delete-warning').modal('hide');
	});
	$('#modal-delete-warning').on('hide.bs.modal', function (event) {
		deptIdToDelete = 0;
	})
});

function select_manager(deptid, staffid) {	
	// /department/assign-manager/{dept_id}/{user_id}
	$.ajax({
	    url: "/department/assign-manager/"+deptid+"/" + staffid,
	    method: "GET",
	    success: function (jsResult) {
	    	if (jsResult.status === 'success') {
	    		toastr.success(jsResult.message);
	    		$('#department-structure').html(jsResult.body);
	    		$('#modal-delegate').modal('hide');
		    } else {
		    	toastr.error(jsResult.message);
		    }            
        }
	});
	
}
</script>		
				
				
@endsection
