<!--  Model form ticket -->
<div class="modal fade" id="modal-promote-ticket">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
    		<form action="/ticket/add" id="frm-ticket" name="frm-ticket" method="post">@csrf
    			<div class="modal-header">
    				<h5 class="modal-title text-color1">Ticket yêu cầu chạy quảng cáo</h5>
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    					<span aria-hidden="true">×</span>
    				</button>
    			</div>
    			<div class="modal-body" id="body-ticket">    			
    				<input type="hidden" name="ticket-video-id" id="ticket-video-id" value="{{ !empty($selected_video) ? $selected_video->id : 0 }}" />
					<input type="hidden" name="ticket-id" id="ticket-id" value="0" />
    				<div class="input-group">
                    	<div class="input-group-prepend">
                      		<span class="input-group-text">Tiêu đề ticket</span>
                    	</div>
                    	<input type="text" class="form-control" name="ticket-title" id="ticket-title" placeholder="Nhập tiêu đề" autofocus required>
                  	</div>			
    				<div class="margin-top round4"><strong>Video:</strong> <span id="ticket-video-title">{{ !empty($selected_video) ? $selected_video->name : '' }}</span></div>
    				<div class="margin-top ptop-10 round4"><strong>Link:</strong> <span id="ticket-video-url">{{ !empty($selected_video) ? $selected_video->url : '' }}</span></div>
					<div id="qtk-mkt"></div>
    				<div class="row">
    					<div class="col col-md-6 ptop-10">
    						<div class="round4">
        						<label>Thời gian chạy:</label>
            					<div class="input-group" style="width: 330px!important">
                                	<div class="input-group-prepend">
                                  		<span class="input-group-text"><i class="far fa-clock"></i></span>
                                	</div>
                                	<input type="text" class="form-control" name="ticket-promote-time" id="ticket-promote-time">
                              	</div>
                          	</div>
    					</div>
    					<div class="col col-md-6 ptop-10">
    						<div class="round4">
        						<label>Ngân sách hàng ngày:</label>
        						<div class="input-group" style="width: 330px!important">
                                	<div class="input-group-prepend">
                                  		<span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                	</div>
                                	<input type="text" class="form-control" name="ticket-budget" id="ticket-budget" style="text-align: right;" placeholder="0" required>
                                	<div class="input-group-append"><span class="input-group-text">VNĐ</span></div>
                              	</div>
        					</div>
    					</div>					
    				</div>
    				<div class="round4" style="margin-top: 20px;">
        				<div style="float:left; margin-top: -15px; background-color: #fff; font-weight: bold; padding: 0px 10px;">Đối tượng mục tiêu</div>
        				<div class="row" style="clear: both;">
            				<div class="col-6 ptop-10">
            					<strong>Giới tính:</strong>
            					<div class="form-group">
            						<div class="icheck-primary d-inline ptop-10">
                                    	<input type="radio" id="ticket-radio-male" name="ticket-gender" value="1">
                                    	<label for="ticket-radio-male" class="phorz-10">Nam</label>
                                  	</div>
                                  	<div class="icheck-primary d-inline ptop-10">
                                    	<input type="radio" id="ticket-radio-female" name="ticket-gender" value="2">
                                    	<label for="ticket-radio-female" class="phorz-10">Nữ</label>
                                  	</div>
                                  	<div class="icheck-primary d-inline ptop-10">
                                    	<input type="radio" id="ticket-radio-allgender" name="ticket-gender" value="0" checked="checked">
                                    	<label for="ticket-radio-allgender" class="phorz-10">Tất cả</label>
                                  	</div>
                                </div>
            				</div>
            				<div class="col-6 ptop-10">
            					<strong>Độ tuổi:</strong>
            					<input type="text" class="form-control" name="ticket-ages" id="ticket-ages" style="text-align: right;" placeholder="0" required>
            				</div>
        				</div>
        				<div class="ptop-10">
        					<strong>Vùng địa lý</strong>
        					<input type="text" class="form-control" name="ticket-location" id="ticket-location" placeholder="US, Brazil, Anh, ..." required>
        				</div>
    				</div>
    				<div class="row">
						<div class="col col-md-6 ptop-10">
    						<div class="round4">
								<strong>Tính chất nội dung</strong>
								<select id="ticket-kind" name="ticket-kind" class="form-control">
									<option value="0">Chạy ND mới</option>
									<option value="1">ND cũ chạy lại</option>
								</select>
							</div>
						</div>
						<div class="col col-md-6 ptop-10">
    						<div class="round4">
								<strong>Ads Campaign ID</strong>
								<input type="text" class="form-control" name="ticket-camp-id" id="ticket-camp-id" placeholder="Dành cho MKT điền">
							</div>
						</div>
					</div>
    				<div class="margin-top ptop-10 round4">
    					<strong>Từ khoá</strong>
    					<input type="text" class="form-control" name="ticket-keyword" id="ticket-keyword" placeholder="Nhập từ khóa ..." required>
    				</div>
    				<div class="margin-top ptop-10 round4">
    					<strong>Ghi chú</strong>
    					<textarea class="form-control" rows="3" name="ticket-note" id="ticket-note" placeholder="Nội dung ghi chú..."></textarea>
    				</div>
					<div class="ptop-10">
						<div class="form-group">
							@if (\App\Constant::QTK === Auth::user()->permission)
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-draft" name="ticket-workflow-position" value="0" checked="checked">
                        		<label for="ticket-radio-draft" class="phorz-10 margin-top">Nháp</label>
							</div>
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-mkt" name="ticket-workflow-position" value="1">
                        		<label for="ticket-radio-mkt" class="phorz-10 margin-top">Gửi MKT luôn</label>								
							</div>
							<div style="display: inline-block!important;">
								<select class="form-control select2" id="select_mkt" name="select_mkt">
									<option value="0"> -- Chọn Marketer -- </option>
									@foreach($marketers as $marketer)
									<option value="{{ $marketer->id }}">{{ $marketer->name }}</option>
									@endforeach
								</select>
							</div>
							@endif
							@if (\App\Constant::MKT === Auth::user()->permission)
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-review" name="ticket-workflow-position" value="2">
                        		<label for="ticket-radio-review" class="phorz-10 margin-top">MKT đang duyệt</label>
							</div>
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-comment" name="ticket-workflow-position" value="3">
                        		<label for="ticket-radio-comment" class="phorz-10 margin-top">Đang thảo luận</label>
							</div>
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-running" name="ticket-workflow-position" value="4">
                        		<label for="ticket-radio-running" class="phorz-10 margin-top">Đang chạy</label>
							</div>
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-paused" name="ticket-workflow-position" value="5">
                        		<label for="ticket-radio-paused" class="phorz-10 margin-top">Tạm dừng</label>
							</div>
							<div class="icheck-primary d-inline">
								<input type="radio" id="ticket-radio-finish" name="ticket-workflow-position" value="6">
                        		<label for="ticket-radio-finish" class="phorz-10 margin-top">Đã kết thúc</label>
							</div>
							@endif
						</div>
						
    				</div>  								
    			</div>
    			<div class="modal-footer justify-content-between">
    				<button type="reset" class="btn btn-default" data-dismiss="modal">Đóng</button>
    				<button type="submit" class="btn btn-primary" id="btn-save-dept">Tạo Ticket</button>
    			</div>			
    		</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
<script src="/plugins/moment/moment.min.js"></script>
<script src="/plugins/daterangepicker/daterangepicker.js"></script>

<script type="text/javascript">
// Date range picker with time picker
$(function () {

	$('#frm-ticket').on('keyup keypress', function(e) {
  		var keyCode = e.keyCode || e.which;
  		if (keyCode === 13) { 
    		e.preventDefault();
    		return false;
  		}
	});
	$('#modal-promote-ticket').on('shown.bs.modal', function(event) {
		$("#ticket-title").focus();
	});
	
	if ($('#ticket-radio-draft').is(":checked")) {
		$("#select_mkt").prop("disabled", true);
	} else if ($('#ticket-radio-mkt').is(":checked")) {
		$("#select_mkt").prop("disabled", false);
	}
	$('#ticket-radio-draft').on('change', function() {
		if ($(this).is(":checked")) {
			$("#select_mkt").val("0").change();
			$("#select_mkt").prop("disabled", true);
		}
	});
	$('#ticket-radio-mkt').on('change', function() {
		if ($(this).is(":checked")) {
			$("#select_mkt").prop("disabled", false);
		}
	});

	$('#modal-promote-ticket').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var modal = $(this);
		var ticketid = button.data('ticketid');
		var action = button.data('action');

		modal.find("#ticket-radio-review").prop("disabled", false);
		modal.find("#ticket-radio-comment").prop("disabled", false);
		modal.find("#ticket-radio-running").prop("disabled", false);
		modal.find("#ticket-radio-paused").prop("disabled", false);
		modal.find("#ticket-radio-finish").prop("disabled", false);

		if (ticketid > 0) {
			modal.find("#ticket-radio-draft").prop("checked", false);
			modal.find("#ticket-radio-mkt").prop("checked", false);

			$.ajax({
				url: "/promotion/get-ticket-by-id/" + ticketid,
				method: "GET",		    
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				success: function (jsResult) {	
					if(jsResult.status === 'success') {
						var start_date = (moment(jsResult.ticket.start_date).format('DD/MM/YYYY HH:mm'));
						var end_date = (moment(jsResult.ticket.end_date).format('DD/MM/YYYY HH:mm'));
						
						modal.find('#ticket-id').val(jsResult.ticket.id);
						modal.find('#ticket-video-id').val(jsResult.ticket.video_id);
													
						modal.find('#ticket-video-title').html(jsResult.ticket.video.name);
						modal.find('#ticket-video-url').html(jsResult.ticket.video.url);
						
						var qtk_mkt = '<div class="margin-top ptop-10 round4"><strong>QTK:</strong> ' 
							+ jsResult.ticket.creator.name 
							+ ' &nbsp; &nbsp; <strong>MKT:</strong> ' + jsResult.ticket.marketer.name 
							+ '</div>';
						modal.find('#qtk-mkt').html(qtk_mkt);

						modal.find('#ticket-title').val(jsResult.ticket.title);
						modal.find('#ticket-promote-time').val(start_date + " - " + end_date);						
						modal.find('#ticket-promote-time').daterangepicker({ 
							startDate: start_date, 
							endDate: end_date,
							locale: {
								format: 'DD/MM/YYYY HH:mm'
							}
						});
						modal.find('#ticket-budget').val(jsResult.ticket.budget);
						//
						if (jsResult.ticket.gender == 1) {
							modal.find("#ticket-radio-male").prop("checked", true);
						} else if (jsResult.ticket.gender == 2) {
							modal.find("#ticket-radio-female").prop("checked", true);
						} else {
							modal.find("#ticket-radio-allgender").prop("checked", true);
						}
						modal.find('#ticket-ages').val(jsResult.ticket.age);
						modal.find('#ticket-location').val(jsResult.ticket.location);						
						modal.find('#ticket-kind').val(jsResult.ticket.kind).change();
						modal.find('#ticket-keyword').val(jsResult.ticket.keyword);
						modal.find('#ticket-note').val(jsResult.ticket.note);
						
						if (jsResult.ticket.workflow_position == 0) {
							modal.find("#ticket-radio-draft").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 1) {
							modal.find("#ticket-radio-mkt").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 2) {
							modal.find("#ticket-radio-review").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 3) {
							modal.find("#ticket-radio-comment").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 4) {
							modal.find("#ticket-radio-running").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 5) {
							modal.find("#ticket-radio-paused").prop("checked", true);
						} else if (jsResult.ticket.workflow_position == 6) {
							modal.find("#ticket-radio-finish").prop("checked", true);
						}

						var select_mkt = modal.find("#select_mkt");
						select_mkt.val(jsResult.ticket.mkt_user_id).change();
						if (modal.find('#ticket-radio-draft').is(":checked")) {
							select_mkt.prop("disabled", true);
						} else if (modal.find('#ticket-radio-mkt').is(":checked")) {
							select_mkt.prop("disabled", false);
						}

						if (jsResult.ticket.workflow_position > 0) {
							modal.find("#ticket-radio-draft").prop("disabled", true);
						} 
						if (jsResult.ticket.workflow_position > 1) {
							modal.find("#ticket-radio-mkt").prop("disabled", true);
						} 
						modal.find('#ticket-camp-id').val(jsResult.ticket.campaign_id);

						modal.find('#btn-save-dept').html('Cập nhật');
					} else {
						toastr.error(jsResult.message);
					}
				}
			});
		} else {
			modal.find("#ticket-radio-draft").prop("checked", true);
			modal.find("#ticket-radio-review").prop("disabled", true);
			modal.find("#ticket-radio-comment").prop("disabled", true);
			modal.find("#ticket-radio-running").prop("disabled", true);
			modal.find("#ticket-radio-paused").prop("disabled", true);
			modal.find("#ticket-radio-finish").prop("disabled", true);
		}
		
	});

	$('#ticket-budget').keypress(function (e) {
    	if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
	});

	$('#ticket-promote-time').daterangepicker({
		timePicker: true,
		timePickerIncrement: 5,
		locale: {
			format: 'DD/MM/YYYY HH:mm'
		}
	});

	$('#frm-ticket').on('submit', function(e) {
		e.preventDefault();
		
		$.ajax({
	        type: "POST",
	        url: '/ticket/save',
	        data: $(this).serialize(), // serializes the form's elements.
	        success: function(jsResult) {
		        
				if (jsResult.status === 'success') {
					location.href = '/promotion?tab={{$tab}}&video_id=&' + jsResult.message + '&d={{$from_detail}}';
				} else {
					toastr.error(jsResult.message);
				}
	        },
			error: function (jqXHR, exception) {
				toastr.error('Lỗi: ' + jqXHR.responseText);				
			}
	    });
		
	});
});
</script>