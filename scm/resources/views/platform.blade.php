@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Nền tảng chia sẻ video')) !!}	
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Thêm nền tảng mới</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<form method="POST" action="/platform/save" enctype="multipart/form-data">@csrf
						<input type="hidden" name="id" value="0" id="id" />
						<input id="name" type="text" 
							class="form-control @error('name') is-invalid @enderror" 
							name="name" value="{{ old('name') }}" required 
							autofocus placeholder="Tên nền tảng">
						<input id="website" type="text" 
							class="form-control margin-top @error('website') is-invalid @enderror" 
							name="website" value="{{ old('website') }}" required 
							placeholder="https://facebook.com">
						<div class="custom-file margin-top">
							<input type="file" class="custom-file-input" name="picture" id="picture">
							<label class="custom-file-label text-grey" for="picture">Chọn ảnh đại diện</label>
						</div>
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
				<div class="card-body" style="min-height: 280px;">
					@if(empty($platform_list) || count($platform_list) == 0)
					<div align="center" style="padding-top: 80px;" class="text-grey">Chưa có nền tảng nào</div>
					@else
					<div class="row">
						@foreach($platform_list as $platform)
						@php
						$total_channels = $platform->total_channels;
						@endphp
						<div class="col col-xs-6 col-md-6 col-sm-6 col-lg-4" align="center" id="platform-{{ $platform->id }}">
							<div class="card">
								<div class="card-body" style="padding: 10px 10px 0px 10px!important;">
									<a href="/channel?platform={{$platform->id}}&topic=0">
    									<div style="background: url({{ $platform->picture }}) center center no-repeat; background-size: contain; width:auto; height: 100px;"></div>
    									<h5 style="padding-top: 15px;">{{ $platform->name }}</h5>
									</a>
									<div><a href="{{ url($platform->website) }}" class="text-grey">{{ $platform->website }}</a></div>
									<div align="right">
										<span><i class="far fa-edit ic24 cursor-hand" onclick="editPlatform({{ $platform->id }}, '{{ htmlspecialchars($platform->name) }}', '{{ $platform->website }}')"></i></span>									
										<span><i class="far fa-trash-alt ic24 cursor-hand" style="color: #ff5648!important" onclick="deletePlatform({{ $platform->id }}, '{{ htmlspecialchars($platform->name) }}', {{$total_channels}}, 0)"></i></span>									
									</div>
								</div>
								<div class="card-footer" style="text-align: left;">
									<div style="white-space: nowrap;">Số kênh: {{$total_channels}}</div>
									<div style="white-space: nowrap;">Số video: 0</div>
								</div>
							</div>							
						</div>
						@endforeach
					</div>
					@endif
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
	</div>
</div>

<!--  Model delete -->
<div class="modal fade" id="modal-delete-platform">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Xoá nền tảng</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<input type="hidden" id="delete-platform-id" name="delete-platform-id" value="0" />
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

<script src="/dist/js/bs-custom-file-input.min.js"></script>
<script>
	$(function () { bsCustomFileInput.init(); });

	function editPlatform(id, name, website) {		
		$('.card-title').html('Cập nhật nền tảng');
		$('#id').val(id);
		$('#name').val(name);
		$('#website').val(website);
		$('#name').trigger('focus');		
	}

	function deletePlatform(id, name, total_channels, total_videos) {
		$('#modal-delete-platform').modal('show');
		$('#delete-platform-id').val(id);
		$('#delete-msg').html('<div>Chắc chắn xoá nền tảng: <strong>' + name + '</strong>?</div>'
				+ '<div>Nền tảng đang có <strong>'+total_channels+' kênh</strong> và <strong>'+total_videos+' video</strong></div>');
	}

	$('#btn-delete-agree').on('click', function() {
		$('#modal-delete-platform').modal('hide');
		var id = $('#delete-platform-id').val();		

		$.ajax({
		    url: "/platform/delete/" + id,
		    method: "GET",
		    success: function (jsResult) {	
		    	if (jsResult.status === 'success') {	    		
		    		toastr.success(jsResult.message);
		    		$('#platform-' + id).remove();    		
			    } else {
			    	toastr.error(jsResult.message);
			    } 
	        }
		});
	});
	
	@php
	if (Session::has('msg')) {
		echo 'toastr.success("'.Session::get('msg').'");';
	}
	@endphp	
	
</script>
@endsection
