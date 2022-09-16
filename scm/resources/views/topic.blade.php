@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Chủ đề')) !!}
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Thêm chủ đề mới</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<form method="POST" action="/topic/save">@csrf
						<input type="hidden" name="id" value="0" id="id" />
						<div>
							<select id="parent_id" name="parent_id" class="form-control">
        						<option value="0">-- Chủ đề cha --</option>
        						{!! $html_options !!}
        					</select>
						</div>
						<input id="name" type="text" 
							class="form-control margin-top @error('name') is-invalid @enderror" 
							name="name" value="{{ old('name') }}" required 
							autofocus placeholder="Tên chủ đề">
												
						@if($errors->any())
						<div class="is-invalid margin-top">{!! $errors->first() !!}</div>
						@endif
						<div align="center"><br/><button type="submit" class="btn btn-primary">Lưu lại</button></div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-body" style="min-height: 230px;">
				@if(empty($html_treeview_topic))
					<div class="text-grey" align="center" style="padding-top: 90px;">Chưa có chủ đề</div>
				@else
    				<h5>Danh sách chủ đề</h5>
    				{!! $html_treeview_topic !!}
				@endif
									
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
	</div>
</div>

<!--  Model delete -->
<div class="modal fade" id="modal-delete-topic">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Xoá chủ đề</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="delete-topic-id" name="delete-topic-id" value="0" />
			</div>
			<div class="modal-body">
				<p id="delete-msg" class="color-danger">Đang tải thông tin ...</p>
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
<script>
	
	@php
	if (Session::has('msg')) {
		$msg = Session::get('msg');
		if (strpos($msg, 'thành công') > 0) {
			echo 'toastr.success("'.$msg.'");';
		} else {
			echo 'toastr.error("'.$msg.'");';
		}		
	}
	@endphp	

	function editTopic(id, parent_id, name) {
		$('#id').val(id);
		$('#name').val(name);
		$("#parent_id").val("" + parent_id).change();
		$('.card-title').html('Cập nhật chủ đề');
	}

	function deleteTopic(id, name, total_channels, total_videos) {
		$('#modal-delete-topic').modal('show');
		$('#delete-topic-id').val(id);
		$('#delete-msg').html('Có chắc chắn xoá chủ đề: <strong>' + name + '</strong>?'
				+ '<div>Chủ đề đang có <strong>'+total_channels+' kênh</strong> và <strong>'+total_videos+' video</strong></div>');
	}

	$('#btn-delete-agree').on('click', function() {
		$('#modal-delete-topic').modal('hide');
		location.href = '/topic/delete/' + $('#delete-topic-id').val();
	});
	
</script>
@endsection
