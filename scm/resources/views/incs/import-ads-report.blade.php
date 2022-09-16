<!--  Model form ticket -->
<div class="modal fade" id="modal-import-ads-report">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">    		
    			<div class="modal-header">
    				<h5 class="modal-title text-color1">Import báo cáo quảng cáo</h5>
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    					<span aria-hidden="true">×</span>
    				</button>
    			</div>
    			<div class="modal-body" id="body-ticket">    			
					<div class="form-group">
                        <label for="exampleInputFile">Chọn file import</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                          </div>
                          <div class="input-group-append">
						  	<button type="submit" class="btn btn-primary col start">
								<i class="fas fa-upload" style="color:#ffffff"></i>
								<span>Start import</span>
							</button>
                          </div>
                        </div>
					</div>
					<div>
						<i>Mẫu định dạng file báo cái: <a href="/dl/Campaign-report-2022-05-21T144456.292.csv">Ten_File.csv</a></i>
					</div>
				</div>
    			<div class="modal-footer justify-content-right">
    				<button type="reset" class="btn btn-default" data-dismiss="modal">Đóng</button>
    			</div>			
    		</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<script type="text/javascript">
$(function () {

});
</script>