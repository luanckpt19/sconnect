@extends('layouts.master')
@section('content')
@php
$from_detail = request()->input('d', 'false');
@endphp

<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', 'Video')) !!}
	@if (!empty($channel))
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col col-12">
    						<form method="get" action="/video" name="frm_channel">
            					<input type="hidden" name="page" value="1">
                				<div class="input-group mb-3" style="width: 400px!important;">
            						<div class="input-group-prepend">
                                    	<span class="input-group-text">Kênh </span>
                                  	</div>
                                  	<select name="channel" class="custom-select" onchange="frm_channel.submit()">
                						<option value=""> -- Tất cả các kênh -- </option>
                						@foreach($channel_list as $chn)
                						<option value="{{$chn->id}}"{{ $channel_id==$chn->id ? ' selected' : '' }}>[{{$arr_platform[$chn->platform_id]}}] {{$chn->name}}</option>
                						@endforeach
                					</select>
                                </div>	
            				</form>	
        				</div>        				
					</div>
					
					<div class="row">
						<div class="col col-md-3">
    						<table style="width: 100%; margin-top: 10px;">
    							<tr><td colspan="2"><div class="card-title bold">{{$channel->name}}</div></td></tr>
    							<tr><td colspan="2">&bull; Ngày tạo: {{$channel->joined_date}}</td></tr>
    							<tr>
    								<td valign="top" style="padding-top: 10px; width: 60px;">
    									<div><img src="{{$channel->thumbnail}}" alt="Thumb" width="50" height="50" class="user-image img-circle elevation-2 user-default-avatar" /></div>
    								</td>
    								<td style="padding: 10px; width: 200px;">										
    									<div>&bull; <i class="fab fa-youtube ic24 text-danger"></i> {{ number_format($channel->video_count) }} video</div>
    									<div>&bull; <i class="far fa-eye ic24"></i> {{ number_format($channel->views) }}</div>
    									<div>&bull; <i class="fas fa-user-alt ic24"></i> {{ number_format($channel->subcriber) }} subs</div>
    								</td>
    							</tr>
    							<tr><td colspan="2">
    								<div class="cursor-hand" onclick="updateContentOfChannel({{$channel->id}})">
    									<i class="fas fa-sync-alt"></i> Cập nhật dữ liệu kênh</div>
    								<div><small class="text-grey"><span class="nowrap">Cập nhật cuối:</span> <span class="nowrap" id="span-updated-at">{{$channel->updated_at}}</span></small></div>
    							</td></tr>
    							
    						</table>
						</div>
						<div class="col col-md-9">
							<form method="get" action="/video">
								<input type="hidden" name="channel" value="{{$channel_id}}" />								
    							<div class="bold">Traffic - Tháng 
    								@php $arr_month = ['01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12']; @endphp
    								{!! Form::select('report_month', $arr_month, $report_month, ['class' => 'pad15']) !!}
    								{!! Form::select('report_year', $arr_years, $report_year, ['class' => 'pad15']) !!}
    								<button type="submit" class="btn btn-sm btn-success">&nbsp; &nbsp; Lọc &nbsp; &nbsp;</button>
    							</div>
    							<input type="hidden" name="promotion" value="{{$promotion}}" />
    							<input type="hidden" name="txt_search" value="{{$txt_search}}" />
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
											/*
											{
												label: 'Tổng views',
                                		      	data: [{{$str_view}}],
                                		      	backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                              	borderColor: 'rgba(54, 162, 235, 1)',
                                              	borderWidth: 1,
                                		      	order: 0
                                		    },
											*/
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
                        		/*                                    		
                                const myChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['01', '02', '03', '04', '05', '06', '07','08','09','10',
                                           		 '11', '12', '13', '14', '15', '16', '17','18','19','20',
                                        		 '21', '22', '23', '24', '25', '26', '27','28','29','30'],
                                        datasets: [{
                                            label: 'Views',
                                            data: [123435, 194235, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                                */
                                
                                </script>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	@if (!empty($video_list))
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">					
					<div class="row">
						<div class="col-6"><h5>Danh sách video</h5></div>
						<div class="col-6" align="right"><a href="/promotion"><i class="fas fa-bullhorn"></i> Quản cáo</a></div>
					</div>					
					<form name="frm-video-list" method="get">
						<input type="hidden" name="channel" value="{{ $channel_id }}" />						
						<input type="hidden" name="report_month" value="{{$report_month}}" />
						<input type="hidden" name="report_year" value="{{$report_year}}" />
						<div class="input-group input-group-sm">    					
    						<span style="display: inline-block; padding: 5px 10px 0px 0px;">Chọn: </span>
    						<select class="custom-select rounded-0" name="promotion" id="promotion">
    							<option value="0"> -- Tất cả video -- </option>
    							<option value="1"{{ $promotion == 1 ? ' selected' : '' }}>Video đang chạy quảng cáo</option>							
    						</select> 
                  			<input value="{{ $txt_search }}" style="margin: 0px 10px;" type="text" class="form-control" name="txt_search" id="txt_search" placeholder="Nội dung tìm kiếm">
                  			<span style="display: inline-block; ">
                    			<button type="submit" class="btn btn-info btn-flat" style="height: 31px; font-size: 95%"><i class="fas fa-search" style="color: #ffffff!important"></i> Lọc</button>
                  			</span>                  		
                		</div>
                		<input type="hidden" name="page" value="{{$page}}" />
            		</form>
				</div>
				<!-- /.card-header -->
				<div class="card-body table-responsive p-0">
					<table class="table table-hover" border="0">
					@php $idx = 0; @endphp					
						<tbody>@foreach($video_list as $video)
							<tr>
								<td width="120">
									<a href="/video/detail?video_id={{ $video->id }}"><img src="{{ $video->thumbnail }}" width="120" alt="" /></a>
								</td>
								<td>
									<div><a href="/video/detail?video_id={{ $video->id }}"><strong>{{$video->name}}</strong></a></div>
									<div><a href="{{$video->url}}" target="_blank" class="text-grey">{{$video->url}}</a></div>
									<div>&bull; Ngày up: {{$video->joined_date}}</div>
									<div>&bull; Chủ đề: {{$video->channel->topic->name}}</div>
									<div>
										&bull; Views: {{ number_format($video->view_count)}} &nbsp; 
										&bull; Like: {{ number_format($video->like_count)}} &nbsp; 
										&bull; Share: {{ number_format($video->share_count)}} &nbsp; 
									</div>@php $file = $video->origin_file; @endphp
									<div>&bull; File video gốc: <span class="text-primary" id="file-video-{{$video->id}}"
											data-toggle="modal" data-target="#modal-assign-file" data-videoid="{{$video->id}}">
										@if(empty($file)) <span class="text-danger cursor-hand link-underline-hover">[Chưa gắn]</span> 
										@else
											<span class="cursor-hand link-underline-hover">{{ $file->name }}</span><br/>
											<span class="text-grey">{{ $file->location_store }}</span>
										@endif
										</span>
									</div>
								</td>
								<td width="100" style="vertical-align: middle; text-align: center;">
									<a href="/video/detail?video_id={{ $video->id }}"><i class="fas fa-poll" style="font-size: 20px;"></i></a>
									<a href="/promotion?video_id={{$video->id}}" style="margin-left: 10px;"><i class="fas fa-bullhorn"></i></a>
								</td>
							</tr>
						@endforeach</tbody>
					</table>
				</div>
				<!-- /.card-body -->
				@if(!empty($video_list) && $video_list->lastPage() > 1)
				<div class="card-footer" style="padding-bottom: 0!important">
					{{ $video_list->withQueryString()->onEachSide(5)->links('pagination::bootstrap-5') }}
				</div>
				@endif
			</div>
		</div>
	</div>
	@endif

	@if (empty($video_list) || empty($channel))
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body" align="center">
					<span class="text-grey">Chưa có kênh hoặc video</span>
				</div>
			</div>
		</div>
	</div>
	@endif
</div>

<!--  Model add new department -->
<div class="modal fade" id="modal-assign-file">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-color1">Chọn file gốc</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>			
			<div class="modal-body" id="assign-file-body" style="line-height: 120%;">
				<div style="padding-left: 50px;">
					<div class="loader"></div> <div style="display:inline-block; position:absolute; padding-left: 15px; ">Loading...</div>
				</div>
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
			</div>			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script type="text/javascript">
$(document).ready(function () {
	$('#modal-assign-file').on('show.bs.modal', function (event) {	
		var button = $(event.relatedTarget);
		var videoid = button.data('videoid');
		var modal = $(this);

		loadOriginalFile(videoid, 0);
	});
});

function loadOriginalFile(video_id, curr_folder_id, current_dept_id) {
	$.ajax({
	    url: "/video-file/load/" + video_id + "/" + curr_folder_id + "/" + current_dept_id,
	    method: "GET",
	    success: function (result) {	
		    $('#assign-file-body').html(result);    		
        }
	});
}

function assignFile(video_id, file_id) {
	$('#modal-assign-file').modal('hide');
	$.ajax({
	    url: "/video-file/assign/" + video_id + "/" + file_id,
	    method: "GET",
	    success: function (result) {	
	    	$('#file-video-' + video_id).html(result);    		
        }
	});	
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
	    		$('#span-updated-at').html(jsResult.updated_at); 		
		    } else {
		    	toastr.error(jsResult.message);
		    }
        }
	});
}
</script>


@endsection
