@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Sản phẩm gốc')) !!}	
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title" style="padding: 5px 0px;">Thư mục</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body" id="panel-folder" style="padding-bottom: 30px;">{!! $folder_list !!}</div>
			</div>			
		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<div style="float: left; padding: 5px 0px;">{!! $folder_breadcrumb !!}</div>
					<div style="float: right; padding: 5px 0px;">
						<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-new-folder" data-parentid="{{$current_folder_id}}" data-id="0" data-name="" data-parentname="{{htmlspecialchars($current_folder_name)}}"><i class="fas fa-folder-plus"></i> Thêm thư mục</span>
						<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-new-file" data-folderid="{{$current_folder_id}}" data-fileid="0" data-filename="" data-location="" data-mime="" data-foldername="{{htmlspecialchars($current_folder_name)}}"><i class="fas fa-file-circle-plus"></i> Thêm file</span>
					</div>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive" id="panel-file">{!! $file_list !!}</div>
			</div>			
		</div>
	</div>
	
</div>

<!--  Model add new folder -->
<div class="modal fade" id="modal-new-folder">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Thêm thư mục</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="id" name="id" value="0" />
				<input type="hidden" id="parent-id" name="parent-id" value="0" />
			</div>
			<div class="modal-body">
				<div>Thư mục cha: <strong><span id="modal-folder-name"></span></strong></div>
				<input id="name" type="text" 
					class="form-control margin-top @error('name') is-invalid @enderror" 
					name="name" value="{{ old('name') }}" required 
					placeholder="Tên thư mục" />
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-save-folder">Lưu lại</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!--  Model add new file -->
<div class="modal fade" id="modal-new-file">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Thêm tệp tin</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="file-id" name="file-id" value="0" />
				<input type="hidden" id="file-folder-id" name="file-folder-id" value="0" />
			</div>
			<div class="modal-body">
				<div>Thuộc thư mục: <strong><span id="modal-folder-name"></span></strong></div>
				<input id="file-name" type="text" 
					class="form-control margin-top @error('file-name') is-invalid @enderror" 
					name="file-name" value="{{ old('file-name') }}" required 
					placeholder="Tên tệp tin" />
				<input id="file-location" type="text" 
					class="form-control margin-top @error('file-location') is-invalid @enderror" 
					name="file-location" value="{{ old('file-location') }}" required 
					placeholder="Đường dẫn lưu trữ" />
				<div class="input-group mb-3 margin-top">
                  	<div class="input-group-prepend">
                    	<span class="input-group-text">Loại tệp tin</span>
                  	</div>
                  	<select class="custom-select rounded-0" id="file-mime-type" name="file-mime-type">
                  	<option value=""> -- Chọn loại tệp tin -- </option>
                    @foreach(\App\Constant::MIME_TYPES as $key=>$mime)
                    	<option value="{{$key}}">{{$mime}}</option>
                    @endforeach
                  	</select>
                </div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
				<button type="button" class="btn btn-primary" id="btn-save-file">Lưu lại</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script type="text/javascript">


function load(folder_id) {
	var ul_folder = $('#ul-' + folder_id);
	if (ul_folder.is(':visible')) {
		ul_folder.hide('fast');
	} else {
		ul_folder.show('fast');
	}
}

function deleteFolder(id) {
	var name = $('#span-' + id).html();
	if (confirm('Chắc chắn xoá thư mục: ' + name + "?")) {
		$.ajax({
		    url: "/origin-product/delete-folder/" + id,
		    method: "GET",
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
		    		$('#panel-folder').html(jsResult.folder_list);
		    		$('#panel-file').html(jsResult.file_list);		    		
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
	}
	
}

function deleteFile(id) {
	var name = $('#sp-file-' + id).html();
	if (confirm('Chắc chắn xoá tệp tin: ' + name + "?")) {
		$.ajax({
		    url: "/origin-product/delete-file/" + id,
		    method: "GET",
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
		    		$('#panel-file').html(jsResult.file_list);		    		
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
	}	
}

$(document).ready(function () {

	/* ============= FOLDER ============== */
	$('#modal-new-folder').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var parent_id = button.data('parentid');
		var parent_name = button.data('parentname');
		var name = button.data('name');
		var modal = $(this);
		modal.find('#id').val(id);
		modal.find('#parent-id').val(parent_id);
		modal.find('#modal-folder-name').html(parent_name);
		modal.find('#name').val(name);
		
	});

	$('#modal-new-folder').on('shown.bs.modal', function (event) {
		$('#name').trigger('focus');		
	});
	
	$('#btn-save-folder').on('click', function() {
		var id = $('#id').val();
		var parent_id = $('#parent-id').val();
		var folder_name = $('#name').val();
		/* jquery post data	*/
		$.ajax({
		    url: "/origin-product/save-folder",
		    method: "POST",
		    data: {
				'id': id,
				'parent_id': parent_id,
				'name': folder_name
			},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
		    		$('#parent-id').val(0);
		    		$('#name').val('');
		    		$('#panel-folder').html(jsResult.folder_list);
		    		$('#panel-file').html(jsResult.file_list);		    		
		    		$('#modal-new-folder').modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
		/**/
	});

	/* ============= FILE ============== */
	$('#modal-new-file').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var folder_id = button.data('folderid');
		var folder_name = button.data('foldername');
		var file_id = button.data('fileid');
		var file_name = button.data('filename');
		var location = button.data('location');
		var mime = button.data('mime');
		var modal = $(this);
		modal.find('#file-folder-id').val(folder_id);
		modal.find('#file-id').val(file_id);
		modal.find('#file-name').val(file_name);
		modal.find('#file-location').val(location);
		modal.find('#file-mime-type').val(mime);
		modal.find('#modal-folder-name').html(folder_name);		
	});
	$('#modal-new-file').on('shown.bs.modal', function (event) {
		$('#file-name').trigger('focus');		
	});
	$('#btn-save-file').on('click', function() {
		var file_id = $('#file-id').val();
		var folder_id = $('#file-folder-id').val();
		var file_name = $('#file-name').val();
		var location_store = $('#file-location').val();
		var mime_type = $('#file-mime-type').val();
		/* jquery post data	*/
		$.ajax({
		    url: "/origin-product/save-file",
		    method: "POST",
		    data: {
			    'id': file_id,
				'folder_id': folder_id,
				'name': file_name,
				'location': location_store,
				'mime_type': mime_type
			},
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
		    		$('#file-folder-i').val(0);
		    		$('#file-name').val('');
		    		$('#file-location').val('');
		    		$('#file-mime-type').val('');		    		
		    		$('#panel-file').html(jsResult.file_list);		    		
		    		$('#modal-new-file').modal('hide');
			    } else {
			    	toastr.error(jsResult.message);
			    }    
	        }
		});
		/**/
	});

});
</script>
@endsection
