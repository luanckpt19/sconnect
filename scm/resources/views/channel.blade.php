@extends('layouts.master')
@section('content')
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Kênh phân phối')) !!}	
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ $title }}</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<form method="POST" action="/channel/save">@csrf
						<input type="hidden" name="id" value="{{$id}}" id="id" />
						<input type="hidden" name="sl_platform" value="{{$sl_platform}}" id="sl_platform" />
						<input type="hidden" name="sl_topic" value="{{$sl_topic}}" id="sl_topic" />
						<input type="hidden" name="sl_channel_type" value="{{$sl_channel_type}}" id="sl_channel_type" />
						<input type="hidden" name="sl_manager" value="{{$sl_manager}}" id="sl_manager" />
						
						<label>Nền tảng chia sẻ video <span class="is-invalid">(*)</span></label>
						<div>
    						<input type="hidden" name="platform_id" value="{{ old('platform_id', !empty($edit_channel)? $edit_channel->platform_id : '0') }}" id="platform_id" />
    						<input type="hidden" name="selected_platform_name" id="selected_platform_name_hidden" value="{{ $selected_platform_name }}" />
    						
    						<div data-toggle="dropdown" aria-expanded="true" class="form-control cursor-hand">&nbsp;<span id="selected_platform_name">{!! $selected_platform_name !!}</span><span style="display:inline-block; float: right; margin-top: 10px; margin-right: -8px;" class="arrow-down"></span></div>
    						@if (!empty($platform_list))
    						<div class="dropdown-menu" x-placement="top-end" style="position: absolute; width: 90%; 
    						  transform: translate3d(0px, -165px, 0px); top: 0px; left: 0px; will-change: transform; background-color: #eee!important">
    						  <div class="dropdown-item cursor-hand" style="width: 100%" onclick="selectPlatform(0, '&nbsp; -- Tất cả nền tảng -- &nbsp;', '')"> &nbsp; -- Tất cả nền tảng -- &nbsp; </div>
    							@foreach($platform_list as $platform)
    							<div class="dropdown-item cursor-hand" style="width: 100%" onclick="selectPlatform({{ $platform->id }}, '{{ htmlspecialchars($platform->name) }}', '{{ $platform->picture }}')"><img src="{{ $platform->picture }}" width="40" height="40" /> &nbsp; {{ $platform->name }}</div>
    							@endforeach
    						</div>
    						@endif						
						</div>						
						<label class="margin-top">Chủ đề <span class="is-invalid">(*)</span></label>
						<div>
							<select id="topic_id" name="topic_id" class="form-control">
        						<option value="0">-- Tất cả chủ đề --</option>
        						{!! $html_options !!}
        					</select>
        					<div class="arrow-down" style="float: right; margin-top: -21px; margin-right: 4px;"></div>
						</div>
						<label class="margin-top">Loại kênh <span class="is-invalid">(*)</span></label>
						<div>
							<select id="channel_type_id" name="channel_type_id" class="form-control">
        						<option value="0">-- Chọn loại kênh --</option>
        						@foreach ($channel_type_list as $channel_type)
        						<option value="{{ $channel_type->id }}"{{ $channel_type->id == $channel_type_id ? ' selected' : '' }}>{{ $channel_type->name }}</option>
        						@endforeach
        					</select>
        					<div class="arrow-down" style="float: right; margin-top: -21px; margin-right: 4px;"></div>
						</div>
						<label class="margin-top">Quản trị viên <span class="is-invalid">(*)</span></label>
						<div>
							<select id="staff_manager_id" name="staff_manager_id" class="form-control">
        						<option value="0">-- Chọn quản trị viên --</option>
        						@foreach($staff_list as $staff)
        						<option value="{{ $staff->id }}"{{ $staff->id == $staff_manager_id ? ' selected' : '' }}>{{ $staff->name }}</option>
        						@endforeach
        					</select>
        					<div class="arrow-down" style="float: right; margin-top: -21px; margin-right: 4px;"></div>
						</div>
						<input id="name" type="text" 
							class="form-control margin-top @error('name') is-invalid @enderror" 
							name="name" value="{{ old('name', !empty($edit_channel)? $edit_channel->name : 'NONAME') }}" required 
							autofocus placeholder="Tên kênh">							
						<input id="url" type="text" 
							class="form-control margin-top @error('url') is-invalid @enderror" 
							name="url" value="{{ old('url', !empty($edit_channel)? $edit_channel->url : '') }}" required 
							placeholder="Đường link của kênh">
						<input id="schedule" type="text" 
							class="form-control margin-top @error('schedule') is-invalid @enderror" 
							name="schedule" value="{{ old('schedule', !empty($edit_channel)? $edit_channel->schedule : '') }}" 
							placeholder="Lịch up video">
						<div class="form-group margin-top">
                        	<label>Ghi chú</label>
                        	<textarea id="note" name="note" class="form-control" rows="3" placeholder="Enter ...">{{ old('note', !empty($edit_channel)? $edit_channel->note : '') }}</textarea>
                      	</div>
						@if($errors->any())
						<div class="is-invalid margin-top">{!! $errors->first() !!}</div>
						@endif
						<div class="justify-content-between" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">
							<button type="submit" class="btn btn-primary">Lưu lại</button>
							@if (is_numeric($id) && $id > 0)<button type="button" class="btn btn-default" id="btn-cancel">Huỷ sửa</button>@endif
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-body">
					<form method="get" action="/channel">						
						<div class="bold">Traffic tổng các kênh - Tháng 
							@php $arr_month = ['01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12']; @endphp
							{!! Form::select('report_month', $arr_month, $report_month, ['class' => 'pad15']) !!}
							{!! Form::select('report_year', $arr_years, $report_year, ['class' => 'pad15']) !!}
							<button type="submit" class="btn btn-sm btn-success">&nbsp; &nbsp; Lọc &nbsp; &nbsp;</button>
						</div>
						<input type="hidden" name="page" value="{{$page}}" />
					</form>
					<div style="height: 250px; position: relative; border:0;">
						<canvas id="myChart" style="width: 100%; height: 250px;"></canvas>
						<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" 
							integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" 
							crossorigin="anonymous" referrerpolicy="no-referrer"></script>
						<script>
                        const ctx = document.getElementById('myChart').getContext('2d');
                        const data = {
								labels: [{!! $str_label !!}],
								datasets: [
                        		    {
                            		    label: 'View trong ngày',
                        		      	data: [{{$str_view_delta}}],
                        		      	backgroundColor: 'rgba(200, 0, 0, 0.5)',
                                      	borderColor: 'rgba(220, 0, 0, 1)',
                                      	borderWidth: 1,
                        		      	type: 'line',
                        		      	order: 1
                        		    }]
                        		};
						const config = new Chart(ctx, {
							type: 'bar',
							data: data,
							options: {
								responsive: true,
								maintainAspectRatio: false,
								
							},
						});
                        
                        </script>
						
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h5>Danh sách kênh video<div style="float: right; font-weight: normal; font-size: 13px; cursor: pointer; margin-top: 5px;"><i class="fas fa-poll"></i> Báo cáo</div></h5>
					<form method="GET" action="/channel" name="frm_select_qtk" id="frm_select_qtk">
					@php
						$sl_platform = request()->input('sl_platform'); if (empty($sl_platform)) $sl_platform = 0;
						$sl_topic = request()->input('sl_topic'); if (empty($sl_topic)) $sl_topic = 0;
						$sl_channel_type = request()->input('sl_channel_type'); if (empty($sl_channel_type)) $sl_channel_type = 0;
						$sl_manager = request()->input('sl_manager'); if (empty($sl_manager)) $sl_manager = 0;
					@endphp
					<div>
						<span style="white-space: nowrap; padding-top: 10px; display: inline-block;">Nền tảng: <select name="sl_platform" id="sl_platform" style="padding: 4px 15px;" onchange="frm_select_qtk.submit()">
							<option value="0">-- Tất cả nền tảng --</option>
							@foreach($platform_list as $platform)
							<option value="{{$platform->id}}"{{ $sl_platform == $platform->id ? ' selected' : ''}}>{{$platform->name}}</option>
							@endforeach
						</select>
						</span> &nbsp; &nbsp; &nbsp; 
						<span style="white-space: nowrap; padding-top: 10px; display: inline-block;">Chủ đề: <select name="sl_topic" id="sl_topic" style="padding: 4px 15px;" onchange="frm_select_qtk.submit()">
							<option value="0">-- Tất cả chủ đề --</option>
        					{!! $html_sl_topic !!}
						</select></span> 
					</div>
					<div style="padding-bottom: 5px">    					
						<span style="white-space: nowrap; padding-top: 10px; display: inline-block;">Loại kênh:
						<select id="sl_channel_type" name="sl_channel_type" style="padding: 4px 15px;" onchange="frm_select_qtk.submit()">
    						<option value="0">-- Chọn loại kênh --</option>
    						@foreach($channel_type_list as $channel_type)
    						<option value="{{ $channel_type->id }}"{{ $sl_channel_type == $channel_type->id ? ' selected':'' }}>{{ $channel_type->name }}</option>
    						@endforeach
    					</select>
    					</span>  &nbsp; &nbsp; &nbsp; 
    					<span style="white-space: nowrap; padding-top: 10px; display: inline-block;">Quản trị kênh:
						<select id="sl_manager" name="sl_manager" style="padding: 4px 15px;" onchange="frm_select_qtk.submit()">
    						<option value="0">-- Chọn quản trị viên --</option>
    						@foreach($staff_list as $staff)
    						<option value="{{ $staff->id }}"{{ $sl_manager == $staff->id ? ' selected':'' }}>{{ $staff->name }}</option>
    						@endforeach
    					</select>
    					</span>      				
					</div>
					</form>
				</div>
				<div class="card-body table-responsive p-0">
					<table class="table table-hover" border="0">
						<thead>
							<tr style="background-color: #dee2e6">
								<th width="50">ID</th>
								<th>Tên kênh</th>
								<th></th>
								<th width="90"></th>
							</tr>
						</thead>
						@if(!empty($channel_list) && count($channel_list) > 0)
						<tbody>
						@foreach($channel_list as $channel)
							@php
							$platform = $channel->platform;
							$topic = $channel->topic;
							$channel_type = $channel->channel_type;
							@endphp
							<tr>
								<td align="center">
									<div><img src="{{$channel->thumbnail}}" alt="Thumb" width="50" height="50" class="user-image img-circle elevation-2 user-default-avatar" /></div>
									@php 
            						$status = ['Đang hoạt động', 'Bí giới hạn', 'Bị khoá kênh']; 
            						$cls_name = ['text-success', 'text-warning', 'text-danger']; 
            						@endphp
            						<div style="padding-top: 10px; font-size: 12px;" class="{{ $cls_name[$channel->status] }}">
            							<i class="fas fa-circle {{ $cls_name[$channel->status] }}"></i> {{ $status[$channel->status] }}
            						</div>
								</td>
								<td>
									<div>
										<a href="/video?channel={{$channel->id}}"><strong>{{$channel->name}}</strong></a> &nbsp; 
										<span class="cursor-hand" onclick="updateContentOfChannel({{$channel->id}})"><i class="fas fa-sync-alt"></i></span>
									</div>
									<div>
										<a href="{{$channel->url}}" target="_blank">
											<span style="max-width:350px; display:-webkit-box; -webkit-line-clamp: 1;-webkit-box-orient: vertical; overflow: hidden; color: #888888; font-size: 90%; text-overflow:ellipsis;">{{$channel->url}}</span>
										</a>
									</div>
									<div>
									{{ preg_match('/(?:https?:\/\/)?(?:www.)?(?:youtube.com|youtu.be)\//m', $channel->url) ? 'Youtube' : 'other' }}
									</div>
									<div style="color: #888888; font-size: 90%; ">
										@php $manager = $channel->manager; @endphp
										<span class="nowrap">&bull; Nền tảng: <span class="text-color1">{{ $platform->name }}</span></span> 
										<span class="nowrap">&bull; Chủ đề: <span class="text-color1">{{ $topic->name }}</span> </span>
										<span class="nowrap">&bull; Loại kênh: <span class="text-color1">{{ !empty($channel_type) ? $channel_type->name : '' }}</span></span>
										<span class="nowrap">&bull; Quản trị kênh: @if (empty($manager)) 
											<a href="/channel?sl_platform={{$sl_platform}}&sl_topic={{$sl_topic}}&sl_channel_type={{$sl_channel_type}}&sl_manager={{$sl_manager}}&channel={{ $channel->id }}"><i style="color: #ff5648">[Chưa có]</i></a>
										@else <span class="text-color1">{{ $manager->name }}</span>@endif
										</span>
									</div>
								</td>
								<td class="nowrap" style="font-size: 90%; ">
									<div>&bull; Joined date:<br/>{{ $channel->joined_date }}</div>
									<div>&bull; Tổng video: {{ number_format($channel->video_count) }}</div>
									<div>&bull; Tổng view:  {{ number_format($channel->views) }}</div>
									<div>&bull; Subscriber:  {{ number_format($channel->subcriber) }}</div>
								</td>
								<td style="text-align: center; vertical-align: middle; padding: 2px!important;">
    								<div>
    									<a href="javascript: void(0)" title="Xem chi tiết"><i class="far fa-list-alt cursor-hand"></i></a> &nbsp;
    									<a href="/channel?sl_platform={{$sl_platform}}&sl_topic={{$sl_topic}}&sl_channel_type={{$sl_channel_type}}&sl_manager={{$sl_manager}}&channel={{ $channel->id }}" title="Sửa thông tin"><i class="far fa-edit ic24" ></i></a> 
                						<a href="/channel/delete?sl_platform={{$sl_platform}}&sl_topic={{$sl_topic}}&sl_channel_type={{$sl_channel_type}}&sl_manager={{$sl_manager}}&channel={{ $channel->id }}"
                							onclick="return confirm('Chắc chắn xoá kênh: {{htmlspecialchars($channel->name)}}?')" >
                							<i class="far fa-trash-alt ic24 cursor-hand" title="Xoá kênh" style="color: #ff5648!important"></i>
                						</a>
            						</div>
								</td>
							</tr>
						@endforeach
						</tbody>
						@endif
					</table>
				</div>
				<!-- /.card-body -->
				@if(!empty($channel_list) && $channel_list->lastPage() > 1)
				<div class="card-footer" style="padding-bottom: 0!important">
					{{ $channel_list->withQueryString()->onEachSide(5)->links('pagination::bootstrap-5') }}
				</div>
				@endif
			</div>
			<!-- /.card -->
		</div>
	</div>
</div>
<script type="text/javascript">
var channel_id = {{$id}};

$(document).ready(function () {
	$('#btn-cancel').on('click', function() {
		location.href = '/channel?platform=' + $('#platform_id').val() + '&topic=' + $('#topic_id').val();
	});
	@if (!empty(old('selected_platform_name'))) 
		var text = '{!! old('selected_platform_name') !!}';
    	$('#selected_platform_name').html(text);
    	$('#selected_platform_name_hidden').val(text);
	@endif	
	{!! !empty(old('topic_id')) ? '$("select#topic_id option[value=\''.old('topic_id').'\']").attr("selected",true);' : '' !!}
	{!! !empty(old('channel_type_id')) ? '$("select#channel_type_id option[value=\''.old('channel_type_id').'\']").attr("selected",true);' : '' !!}
	{!! !empty(old('staff_manager_id')) ? '$("select#staff_manager_id option[value=\''.old('staff_manager_id').'\']").attr("selected",true);' : '' !!}
	
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

function selectPlatform(platform_id, name, image) {
	$('#platform_id').val(platform_id);
	var selected_platform_name = '<img style="margin-top: -4px;" src="' + image + '" width="30" height="30" /> &nbsp; ' + name;
	$('#selected_platform_name').html(selected_platform_name);
	$('#selected_platform_name_hidden').val(selected_platform_name);
	
}

function updateContentOfChannel(channel_id) {
	// /channel/collect/{channel_id}
	// toastr.info("Thực hiện cập nhật thông tin kênh trong tiến trình ngầm. Quá trình này có thể tốn một chút thời gian.");
	$.ajax({
	    url: "/channel/collect/" + channel_id,
	    method: "GET",
	    success: function (jsResult) {
		    if (jsResult.status === 'success') {	
	    		toastr.info(jsResult.message);    		
		    } else {
		    	toastr.error(jsResult.message);
		    }
        }
	});
}

</script>

@endsection

