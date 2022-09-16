@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Loại kênh')) !!}	
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ $title }}</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<form method="POST" action="/channel-type/save">@csrf
						<input type="hidden" name="id" value="{{$id}}" id="id" />
						<label>Tên loại kênh</label>						
						<input id="name" type="text" 
							class="form-control @error('name') is-invalid @enderror" 
							name="name" value="{{ old('name', $name) }}" required 
							autofocus placeholder="Tên loại kênh">							
						<div class="form-group margin-top">
                        	<label>Ghi chú</label>
                        	<textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter ...">{{ old('description', $description) }}</textarea>
                      	</div>
						<div class=" margin-top justify-content-between" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">
							<button type="submit" class="btn btn-primary">Lưu lại</button>
							@if (is_numeric($id) && $id > 0)<button type="button" class="btn btn-default" id="btn-cancel">Huỷ sửa</button>@endif
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h5>Danh sách tên loại kênh</h5>					
				</div>
				<div class="card-body table-responsive p-0">
					<table class="table table-hover" border="0">
						<thead>
							<tr style="background-color: #dee2e6">
								<th width="50">ID</th>
								<th>Tên loại kênh</th>
								<th width="90"></th>
							</tr>
						</thead>
						@if(!empty($channel_type_list) && count($channel_type_list) > 0)
						<tbody>
						@foreach($channel_type_list as $channel_type)
							<tr>
								<td align="center">{{$channel_type->id}}</td>
								<td>
									<div class="bold">{{$channel_type->name}}</div>									
									<div class="text-grey">{{$channel_type->description}}</div>									
								</td>
								<td style="padding: 0!important; text-align: center; vertical-align: middle;">
									<a href="/channel-type?ct_id={{$channel_type->id}}" title="Sửa thông tin"><i class="far fa-edit ic24" ></i></a> 
            						<i class="far fa-trash-alt ic24 cursor-hand" title="Xoá loại kênh" onclick="deleteChannel({{$channel_type->id}},'{{htmlspecialchars($channel_type->name)}}')" style="color: #ff5648!important"></i>
								</td>
							</tr>
						@endforeach
						</tbody>
						@endif
					</table>
				</div>
			</div>
			<!-- /.card -->
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function () {	
	$('#btn-cancel').on('click', function() {
		location.href = '/channel-type';
	});
});

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

function deleteChannel(id, name) {
	if (confirm('Chắc chắn xoá loại kênh: ' + name + "?")) {
		location.href = '/channel-type/delete/' + id;
	}
}

</script>

@endsection

