<!--  Model form ticket -->
<div class="modal fade" id="modal-ticket-comment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">    		
            <div class="modal-header">
                <h5 class="modal-title text-color1">Ticket yêu cầu chạy quảng cáo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="body-ticket">
                <form action="#" id="frm-comment" name="frm-comment" method="post">@csrf
                    <input type="hidden" name="ticket-id" id="ticket-id" value="0" />
                    <div><strong>Ticket:</strong> <span id="ticket-title"></span></div>
                    <div class="margin-top">
                        <strong>Video:</strong><br/>
                        <div><a id="video_href" href="#" target="_blank"></a></div>
                    </div>
                    <div class="row margin-top">
                        <div class="col col-6"><strong>Thời gian chạy:</strong> <br/>
                            <span id="ticket-promote-time"></span>
                        </div>
                        <div class="col col-6"><strong>Ngân sách:</strong> 
                            <span id="ticket-budget"></span>
                        </div>
                    </div>
                    <div class="row margin-top">
                        <div class="col col-6"><strong>Giới tính:</strong> <span id="ticket-gender"></span></div>
                        <div class="col col-6"><strong>Độ tuổi:</strong> <span id="ticket-age"></span></div>
                    </div>
                    <div class="margin-top"><strong>Vùng địa lý:</strong> <br/><span id="ticket-location"></span></div>
                    <div class="margin-top">
                        <strong>Tính chất nội dung:</strong> <span id="ticket-kind"></span> &nbsp; &nbsp;
                        <strong>QTK:</strong> <span id="ticket-qtk"></span> &nbsp; &nbsp;
                        <strong>MKT:</strong> <span id="ticket-mkt"></span>
                    </div>
                    <div class="margin-top"><strong>Từ khóa:</strong> <span id="ticket-keyword"></span></div>
                    <div class="margin-top round4">
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-0"></i> Nháp &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-1"></i> Đã gửi MKT &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-2"></i> MKT đang duyệt &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-3"></i> Đang thảo luận &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-4"></i> Đang chạy &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-5"></i> Tạm dừng &nbsp; </span>
                        <span class="d-inline-nowrap"><i class="fas fa-check-circle" id="wf-pos-6"></i> Đã kết thúc</span>
                    </div>                    
                    <div class="margin-top"><strong>Thảo luận:</strong> </div>
                    <div style="height: 380px; border: 1px solid #ccc; border-radius: 4px; padding: 6px 10px; overflow-y: scroll;" id="ticket-command"></div>
                    <textarea id="txt-comment" name="txt-comment" placeholder="Nhập nội dung thảo luận..." style="margin-top: 10px; padding: 4px 8px;  width: 100%; height: 80px; overflow-y: scroll; border: 1px solid #cccccc; border-radius: 4px;"></textarea>
                    <button type="submit" class="btn btn-primary" id="btn-save-comment" style="float: right" >Gửi comment</button>
                </form>
            </div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script type="text/javascript">
// Date range picker with time picker
$(function () {
    $('#modal-ticket-comment').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var modal = $(this);
		var ticketid = button.data('ticketid');	
        
        $('#badge-' + ticketid).hide();
        for(i = 0; i <= 6; i++) {
            $('#wf-pos-' + i) 
                .addClass('text-lt-grey').addClass('far')
                .removeClass('text-success').removeClass('fas');
        }

		$.ajax({
		    url: "/promotion/get-ticket-by-id/" + ticketid,
		    method: "GET",		    
		    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		    success: function (jsResult) {	
				var start_date = (moment(jsResult.ticket.start_date).format('HH:mm DD/MM/YYYY'));
                var end_date = (moment(jsResult.ticket.end_date).format('HH:mm DD/MM/YYYY'));
                var create_date = (moment(jsResult.ticket.created_date).format('HH:mm DD/MM/YYYY'));
                
                modal.find('#ticket-id').val(jsResult.ticket.id);
                modal.find('#ticket-title').html(jsResult.ticket.title);

                modal.find('#video_href').html(jsResult.ticket.video.name);
				modal.find('#video_href').attr('href', jsResult.ticket.video.url);

                modal.find('#ticket-promote-time').html(start_date + ' <i class="fas fa-arrow-right"></i> ' + end_date);						
                modal.find('#ticket-budget').html(jsResult.ticket.budget);                
                modal.find('#ticket-gender').html(jsResult.ticket.gender == 1 ? 'Nam' : (jsResult.ticket.gender == 2 ? 'Nữ' : 'Tất cả'));
                modal.find('#ticket-age').html(jsResult.ticket.age);
                modal.find('#ticket-location').html(jsResult.ticket.location);
                modal.find('#ticket-keyword').html(jsResult.ticket.keyword);
                modal.find('#ticket-kind').html(jsResult.ticket.kind == 0 ? 'Chạy ND mới' : 'ND cũ chạy lại');
                modal.find('#ticket-qtk').html(jsResult.ticket.creator.name);
                modal.find('#ticket-mkt').html(jsResult.ticket.marketer.name);
                $('#wf-pos-' + jsResult.ticket.workflow_position)
                    .removeClass('text-lt-grey').addClass('text-success')
                    .removeClass('far').addClass('fas');
                
                var auth_id = {{ Auth::user()->id }};
                var note = '';
                if (jsResult.ticket.note) {
                    note = '<div class="margin-top comment-box"><div style="float: left; font-weight: bold;">' 
                    + (jsResult.ticket.creator.id == auth_id ? '<span class="text-dorange">You</span>' : jsResult.ticket.creator.name)
                    + '</div> <div style="float: right; font-size: 90%; color: #999999"><i>' + create_date + '</i></div>'
                    + '<div style="clear:both">' + jsResult.ticket.note + '</div></div>';
                }
                if (jsResult.html_comment) {
                    note += jsResult.html_comment.replaceAll('\n', '<br/>');
                }
                
                modal.find('#ticket-command').html(note);

	        }
		});
	});

    $('#frm-comment').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
	        type: "POST",
	        url: '/comment/save',
	        data: $(this).serialize(), // serializes the form's elements.
	        success: function(jsResult) {	        
				if (jsResult.status === 'success') {
					$('#ticket-command').append(jsResult.message.replaceAll('\n', '<br/>'));
                    $('#txt-comment').val('');
                    $("#ticket-command").animate({ scrollTop: $('#ticket-command').prop("scrollHeight")}, 1000);

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