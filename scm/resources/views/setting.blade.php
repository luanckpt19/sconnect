@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Hệ thống', 'Thiết lập chung')) !!}
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title text-color1">Loại phòng ban</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<div class="input-group">
						<input type="text" id="dept-prefix" name="dept-prefix" placeholder="Loại phòng, ban, dự án, ..." class="form-control">
						<span class="input-group-append">
							<button type="submit" class="btn btn-success" id="add-prefix">Thêm</button>
						</span>
					</div>
					<div class="setting-table-area">
						<table class="table"><thead>
							<tr style="background-color: #dee2e6">
								<th style="width: 10px">#ID</th>
								<th>Tên loại</th>
								<th style="width: 120px; text-align: center;">
									<span class="btn btn-block btn-default btn-sm" id="delete-prefix">
									<i class="far fa-trash-alt" style="color:#ff5648!important"></i> &nbsp; Xoá
									</span>
								</th>
							</tr></thead>
							<tbody id="tbody-prefix">
							{!! App\Utils::buildDeptPrefixList($prefix_list) !!}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title text-color1">Chức vụ</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<div class="input-group">
						<input type="text" id="title" name="title" placeholder="Tên chức vụ" class="form-control">
						<span class="input-group-append">
							<button type="submit" class="btn btn-success" id="add-title">Thêm</button>
						</span>
					</div>	
					<div class="setting-table-area">
						<table class="table"><thead>
							<tr style="background-color: #dee2e6">
								<th style="width: 10px">#ID</th>
								<th>Tên chức vụ</th>
								<th style="width: 120px; text-align: center;">
									<span class="btn btn-block btn-default btn-sm" id="delete-title">
									<i class="far fa-trash-alt" style="color:#ff5648!important"></i> &nbsp; Xoá
									</span>
								</th>
							</tr></thead>
							<tbody id="tbody-title">
							{!! App\Utils::buildTitleList($title_list) !!}
							</tbody>
						</table>
					</div>				
				</div>				
			</div>
		</div>
    </div>
</div>


<script>
$(document).ready(function () {
	/* PREFIX DEPARTMENT ******************************************** */
	$('#dept-prefix').on('keypress', function(e) {
		if(e.keyCode == 13) {
			//Disable input to prevent multiple submit
			$(this).attr("disabled", "disabled");

			$('#add-prefix').click();

			//Enable the input again
			$(this).removeAttr("disabled");
			
		}
	});
	$('#add-prefix').on('click', function() {
		var prefix_name = $('#dept-prefix').val();
		if(prefix_name.trim() === '') {
			toastr.error('Tên loại phòng ban rỗng');
			return;
		}

		// jquery post data	
		$.ajax({
		    url: "/setting/add-prefix",
		    method: "POST",
		    data: {'prefix-name': prefix_name },
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (result) {	
				console.log(result);
 
		    	//var jsResult = JSON.parse(result);	    	

		    	if (result.status === 'success') {	    		
		    		toastr.success(result.message);	    		
		    		$('#tbody-prefix').html(result.body);
					$('#dept-prefix').val('');					
			    } else {
			    	toastr.error(result.message);
			    }
	        }
		});
		
		$('#dept-prefix').trigger('focus');

	});
	
	$('#delete-prefix').on('click', function() {
		var list = $("input[name='prefix-checkbox[]']:checked").map(function () {
		    return this.value;
		}).get();
		
		if (list.toString().trim() === '') {
			toastr.error('Chưa chọn Loại phòng ban cần xoá!');
			return;
		}
		
		if (confirm("Chắc chắn xoá loại phòng ban đã chọn không?")) {
			$.get( "/setting/delete-prefix-" + list, function( result ) {
		    	if (result.status === 'success') {
		    		toastr.success(result.message);
		    		$('#tbody-prefix').html(result.body);
			    } else {
			    	toastr.error(result.message);
			    }
			});
		}
	});
	
	/* TITLE ******************************************** */
	$('#title').on('keypress', function(e) {
		if(e.keyCode == 13) {
			//Disable input to prevent multiple submit
			$(this).attr("disabled", "disabled");

			$('#add-title').click();

			//Enable the input again
			$(this).removeAttr("disabled");
			
		}
	});
	
	$('#add-title').on('click', function() {
		var title_name = $('#title').val();
		if(title_name.trim() === '') {
			toastr.error('Tên chức vụ rỗng');
			return;
		}

		// jquery post data	
		$.ajax({
		    url: "/setting/add-title",
		    method: "POST",
		    data: {'title-name': title_name },
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (result) {	

		    	if (result.status === 'success') {	    		
		    		toastr.success(result.message);	    		
		    		$('#tbody-title').html(result.body);
					$('#title').val('');
					
			    } else {
			    	toastr.error(result.message);
			    }
	        }
		});		
		$('#title').trigger('focus');
	});
	
	$('#delete-title').on('click', function() {
		var list = $("input[name='title-checkbox[]']:checked").map(function () {
		    return this.value;
		}).get();
		
		if (list.toString().trim() === '') {
			toastr.error('Chưa chọn chức vụ cần xoá!');
			return;
		}
		
		if (confirm("Chắc chắn xoá các chức vụ đã chọn không?")) {
			$.get( "/setting/delete-title-" + list, function( result ) {
		    	if (result.status === 'success') {
		    		toastr.success(result.message);
		    		$('#tbody-title').html(result.body);
			    } else {
			    	toastr.error(result.message);
			    }
			});
		}
	});
	
});

function edit_prefix(id) {
	var prefix_btn_edit = $('#prefix-btn-edit-' + id);
	var span_prefix_name = $('#prefix-name-' + id);
	
	var input = '<div class="input-group">'
		+ '<input type="text" id="prefix-name" value="' + span_prefix_name.html().replace(/"/g, '&quot;') + '" class="form-control" />'
		+ '<div class="input-group-append cursor-hand" id="btn-save">'
		+ '<span class="input-group-text" id="icon-check"><i class="fas fa-check" style="width: 16px;"></i></span>'
		+ '</div></div>';
		
	var spinnerLoading = '<img src="/images/spinner-progress.svg" style="width:16px;" />';
	
	span_prefix_name.html(input);
	prefix_btn_edit.hide();
	
	var saving = false;
	
	$('#btn-save').on('click', function() {
		if (saving) return;
		saving = true;

		$('#icon-check').html(spinnerLoading);
		$(this).prop('disabled', true);

		var prefix_name = $('#prefix-name').val();
		if(prefix_name.trim() === '') {
			toastr.error('Tên loại phòng ban rỗng');
			return;
		}

		// jquery post data	
		$.ajax({
		    url: "/setting/save-prefix",
		    method: "POST",
		    data: {'prefix-name': prefix_name, 'prefix-id': id },
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (result) {	
				saving = false;
				$('#icon-check').html('<i class="fas fa-check" style="width: 16px;"></i>');
		    	if (result.status === 'success') {	    		
		    		toastr.success(result.message);	    		
					span_prefix_name.html(prefix_name);
					prefix_btn_edit.show();
			    } else {
			    	toastr.error(result.message);					
			    }				
	        }
		});

	});

}

function edit_title(id) {
	var title_btn_edit = $('#title-btn-edit-' + id);
	var span_title_name = $('#title-name-' + id);
	
	var input = '<div class="input-group">'
		+ '<input type="text" id="title-name" value="' + span_title_name.html().replace(/"/g, '&quot;') + '" class="form-control" />'
		+ '<div class="input-group-append cursor-hand" id="btn-save">'
		+ '<span class="input-group-text" id="icon-check"><i class="fas fa-check" style="width: 16px;"></i></span>'
		+ '</div></div>';
		
	var spinnerLoading = '<img src="/images/spinner-progress.svg" style="width:16px;" />';
	
	span_title_name.html(input);
	title_btn_edit.hide();
	
	var saving = false;
	
	$('#btn-save').on('click', function() {
		if (saving) return;
		saving = true;

		$('#icon-check').html(spinnerLoading);
		$(this).prop('disabled', true);

		var title_name = $('#title-name').val();
		if(title_name.trim() === '') {
			toastr.error('Tên chức vụ rỗng');
			return;
		}

		// jquery post data	
		$.ajax({
		    url: "/setting/save-title",
		    method: "POST",
		    data: {'title-name': title_name, 'title-id': id },
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (result) {	
				saving = false;
				$('#icon-check').html('<i class="fas fa-check" style="width: 16px;"></i>');
		    	if (result.status === 'success') {	    		
		    		toastr.success(result.message);	    		
					span_title_name.html(title_name);
					title_btn_edit.show();
			    } else {
			    	toastr.error(result.message);					
			    }				
	        }
		});

	});
}

</script>
@endsection
