@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="container">
@php
$from_detail = request()->input('d', 'false');

$breadcrum = array('Marketing');
if (!empty($selected_video)) {
	$breadcrum[] = '<a href="/video?channel=' . $selected_video->channel_id . '">Video</a>';
}

if ($from_detail === 'true') {
	if (!empty($selected_video)) {
		$breadcrum[] = '<a href="/video/detail?video_id='. $selected_video->id .'">Thông tin chi tiết video</a>';
	}	
	$breadcrum[] = 'Quảng cáo video';
} else {
	$breadcrum[] = 'Quảng cáo video';
}

@endphp
	{!! App\Utils::createBreadcrumb($breadcrum) !!}
	<div class="row">
		<div class="col-12">
			<div class="card card-success card-outline card-outline-tabs">
				<div class="card-header p-0 border-bottom-0">
					<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link{{ $tab == 0 ? ' active' : '' }}" id="custom-tabs-four-0-tab" href="?tab=0&video_id={{$video_id}}&d={{$from_detail}}">Mới ({{ $tab_item_count[0] }})</a>
						</li>
						<li class="nav-item">
							<a class="nav-link{{ $tab == 1 ? ' active' : '' }}" id="custom-tabs-four-1-tab" href="?tab=1&video_id={{$video_id}}&d={{$from_detail}}">Đang duyệt ({{ $tab_item_count[1] }})</a>
						</li>
						<li class="nav-item">
							<a class="nav-link{{ $tab == 2 ? ' active' : '' }}" id="custom-tabs-four-2-tab" href="?tab=2&video_id={{$video_id}}&d={{$from_detail}}">Đang chạy ({{ $tab_item_count[2] }})</a>
						</li>
						<li class="nav-item">
							<a class="nav-link{{ $tab == 3 ? ' active' : '' }}" id="custom-tabs-four-3-tab" href="?tab=3&video_id={{$video_id}}&d={{$from_detail}}">Tạm dừng ({{ $tab_item_count[3] }})</a>
						</li>
						<li class="nav-item">
							<a class="nav-link{{ $tab == 4 ? ' active' : '' }}" id="custom-tabs-four-4-tab" href="?tab=4&video_id={{$video_id}}&d={{$from_detail}}">Đã kết thúc ({{ $tab_item_count[4] }})</a>
						</li>
					</ul>
					<div class="inline" style="position:absolute; right: 15px; top: 10px">
					@if ($video_id > 0 && \App\Constant::QTK === Auth::user()->permission)
						<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-promote-ticket" data-ticketid="0">
							<i class="fas fa-bullhorn"></i> Tạo Ticket</span>
					@endif
					</div>              	
				</div>				
              	<div class="card-body" style="padding:10px 0px 0px 0px;">				  	
				  	@if (!empty($video_list))
					<div class="form-group" style="display: flex; align-items: center; justify-content: center;">
						<select class="form-control select2" style="width: 400px;"
							onchange="location.href='?tab={{$tab}}&video_id=' + this.value + '&d={{$from_detail}}'">
							<option value="0"> --- Chọn video --- </option>
							@foreach($video_list as $video)
							<option value="{{ $video->id }}"{{ $video_id == $video->id ? ' selected' : '' }}>{{ $video->name }}</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="tab-content" id="custom-tabs-four-tabContent">
						<div class="tab-pane table-responsive p-0 fade show active" id="custom-tabs-four-new" role="tabpanel" aria-labelledby="custom-tabs-four-new-tab">							
							<table class="table table-hover text-nowrap" style="margin:0; max-height: 300px;">
								<thead>
									<tr><th>Tên quảng cáo</th><th>Ngân sách ngày</th><th>Thời gian</th><th>Giới tính</th><th>Độ tuổi</th><th>Tính chất</th><th>Keywords</th><th>Note</th><th>Tạo bởi</th><th>Trạng thái</th><th>Thao tác</th></tr>
								</thead>
								<tbody>
								@foreach($ticket_list as $idle_ticket)
								<tr align="center"><td align="left">
									<div class="text-color1 bold">{{ $idle_ticket->title }}</div>
									<div style="font-size: 85%; font-weight: bold;">Campaign ID: {{ $idle_ticket->campaign_id }}</div>
								</td>
								<td>{{ number_format($idle_ticket->budget) }}đ</td>
								<td align="left">{{ $idle_ticket->start_date->format('H:i d/m/Y') }} <br/><i class="fas fa-arrow-right"></i> {{ $idle_ticket->end_date->format('H:i d/m/Y') }}</td>
								<td>{{ \App\Constant::GENDERS[$idle_ticket->gender] }}</td>
								<td>{{ $idle_ticket->age }}</td>								
								<td>{{ \App\Constant::KINDS[$idle_ticket->kind] }}</td>
								<td>
									<i class="cursor-hand far fa-solid fa-tag"
									data-toggle="tooltip" data-placement="top" title="{{ $idle_ticket->keyword }}"></i>
								</td>
								<td>
									<div style="position: relative;" data-toggle="modal" data-target="#modal-ticket-comment" data-ticketid="{{ $idle_ticket->id }}">
										<i class="cursor-hand far fa-comment-alt"></i>@php 
										$unread_count = $idle_ticket->total_unread_comment;										 
										@endphp
										@if ($unread_count > 0)
										<span id="badge-{{ $idle_ticket->id }}" class="badge badge-danger navbar-badge" style="margin-top: -15px;margin-right: -5px;">
										{{ $idle_ticket->total_unread_comment }}
										</span>
										@endif
									</div>
								</td>
								<td>{{ $idle_ticket->creator->name }}</td>
								<td>{{ \App\Constant::TICKET_STATUS[$idle_ticket->workflow_position] }}</td>								
								<td>
									<i class="cursor-hand fas fa-edit" data-toggle="modal" data-target="#modal-promote-ticket" data-ticketid="{{ $idle_ticket->id }}" data-action="edit"></i> &nbsp; 
									@if ($idle_ticket->workflow_position < 2)
									<i class="cursor-hand fas fa-trash-alt" onclick="delete_ticket({{ $idle_ticket->id }}, '{{ $idle_ticket->title }}')"></i>
									@endif
								</td></tr>
								@endforeach
								</tbody>
							</table>												
						</div>
						
					</div>
              	</div>
              <!-- /.card -->
			</div>
		</div>
    </div>
	
	@if(!empty($selected_video))
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col col-3">
							<div><img src="{{ $selected_video->thumbnail }}" style="width: 100%; height:auto;" /></div>
							<div style="padding: 5px 0px;"><a href="{{ $selected_video->url }}" target="_blank"><strong>{{ $selected_video->name }}</strong></a></div>
							<div class="justify-content-between text-grey" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">
								<span class="nowrap margin-top"><i class="far fa-calendar-alt"></i> {{ $selected_video->joined_date }}</span>
								<span class="nowrap margin-top"><i class="far fa-eye"></i> {{ number_format($selected_video->view_count) }}</span>
							</div>
							<div class="justify-content-between text-grey" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">								 
								<span class="nowrap margin-top"><i class="far fa-thumbs-up"></i> {{ number_format($selected_video->like_count) }} &nbsp;</span> 
								<span class="nowrap margin-top"><i class="far fa-thumbs-down"></i> {{ number_format($selected_video->dislike_count) }} &nbsp; </span>
								<span class="nowrap margin-top"><i class="fas fa-share"></i> {{ number_format($selected_video->share_count) }}</span>
							</div>
						</div>
						<div class="col col-9">							 
							<div class="bold">Tháng YY</div>
							<div style="height: 250px; position: relative; border:0;">								
								<canvas id="myChart" style="width: 100%; height: 250px;"></canvas>
								<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" 
									integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" 
									crossorigin="anonymous" referrerpolicy="no-referrer"></script>
								<script>
                                const ctx = document.getElementById('myChart').getContext('2d');
                                const data = {
										labels: [{!!$str_label!!}],
										datasets: [
											{
												label: 'Impressions',
                                		      	data: [{{$str_view}}],
                                		      	backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                              	borderColor: 'rgba(54, 162, 235, 1)',
                                              	borderWidth: 1,
                                		      	order: 0
                                		    },
                                		    /*{
                                		    	label: 'View trong ngày',
                                		      	data: [],
                                		      	backgroundColor: 'rgba(200, 0, 0, 0.5)',
                                              	borderColor: 'rgba(220, 0, 0, 1)',
                                              	borderWidth: 1,
                                		      	type: 'line',
                                		      	order: 1
                                		    }*/]
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
				</div>
			</div>
		</div>
    </div>
    @endif
    <div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col-6"><div class="card-title">Báo cáo</div></div>
						<div class="col-6" style="text-align: right;">
							<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-import-ads-report">
								<i class="fas fa-file-import"></i> Import Report</span>
							
						</div>
					</div>
				</div>
				<div class="card-body table-responsive p-0">
					<table class="table table-hover text-nowrap">
						<thead>
							<tr><th>Ngày</th><th>Chi tiêu</th><th>Hiển thị</th><th>Click</th><th>CTR</th></tr>
						</thead>
						<tbody>
							<tr><td>02/05/2022</td><td>512,000</td><td>3,493,403</td><td>343,709</td><td>9.84%</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
    </div>
</div>
<script src="/plugins/select2/js/select2.full.min.js"></script>

<script lang="text/javascript">
$(document).ready(function () {
	//Initialize Select2 Elements
    $('.select2').select2({
		theme: 'bootstrap4'
	});
	$('[data-toggle="tooltip"]').tooltip();
	//$('#custom-tabs-four-{{$tab}}-tab').click();
});

function delete_ticket(id, title) {
	alert(id);
}
function tab(tab_id) {
	$.ajax({type: "GET", url: '/promote/tab/' + tab_id});
}
</script>
@include('incs.ticket')
@include('incs.ticket-comment')
@include('incs.import-ads-report')
@endsection
