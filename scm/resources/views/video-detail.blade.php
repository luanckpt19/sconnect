@extends('layouts.master')
@section('content')
@php
$from_detail = request()->input('d', 'false');
@endphp
<div class="container">
	{!! App\Utils::createBreadcrumb(array('Media', '<a href="/video?channel=' . $channel->id . '">Video</a>', 'Thông tin chi tiết video')) !!}
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col col-3">
							<div><img src="{{ $video->thumbnail }}" style="width: 100%; height:auto;" /></div>
							<div style="padding: 5px 0px;"><a href="{{ $video->url }}" target="_blank"><strong>{{ $video->name }}</strong></a></div>
							<div class="justify-content-between text-grey" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">
								<span class="nowrap margin-top"><i class="far fa-calendar-alt"></i> {{ $video->joined_date }}</span>
								<span class="nowrap margin-top"><i class="far fa-eye"></i> {{ number_format($video->view_count) }}</span>
							</div>
							<div class="justify-content-between text-grey" style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">								 
								<span class="nowrap margin-top"><i class="far fa-thumbs-up"></i> {{ number_format($video->like_count) }} &nbsp;</span> 
								<span class="nowrap margin-top"><i class="far fa-thumbs-down"></i> {{ number_format($video->dislike_count) }} &nbsp; </span>
								<span class="nowrap margin-top"><i class="fas fa-share"></i> {{ number_format($video->share_count) }}</span>
							</div>
						</div>
						<div class="col col-9">							 
							<form method="get" action="/video/detail">
							<input type="hidden" name="video_id" value="{{$video->id}}" />
							<div class="bold">Traffic - Tháng 
								@php $arr_month = ['01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12']; @endphp
								{!! Form::select('report_month', $arr_month, $report_month, ['class' => 'pad15']) !!}
								{!! Form::select('report_year', $arr_years, $report_year, ['class' => 'pad15']) !!}
								<button type="submit" class="btn btn-sm btn-success">&nbsp; &nbsp; Lọc &nbsp; &nbsp;</button>
							</div>
							</form>
							
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
                                </script>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title" style="width: 100%">
						Trạng thái chạy quảng cáo
						<span style="display: inline-block; float: right; font-size: 14px;">
							<a href="/promotion?video_id={{$video->id}}&d=true">Xem thêm <i class="fas fa-angle-right"></i></a>
						</span>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
    					<div class="col-sm-6">
        					Ticket infos
    					</div>
    					<div class="col-sm-6">
    						<span class="inline" style="float:right">
    							<span class="cursor-hand btn-round" data-toggle="modal" data-target="#modal-promote-ticket">
    								<i class="fas fa-bullhorn"></i> Tạo Ticket</span>
							</span>
    					</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title">Trạng thái shortlink</div>
				</div>
				<div class="card-body">
					
				</div>
			</div>
		</div>
    </div>
    <div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<div class="card-title">Trạng thái bình luận</div>
				</div>
				<div class="card-body">
					
				</div>
			</div>
		</div>
    </div>
</div>

@include('incs.ticket')

@endsection
