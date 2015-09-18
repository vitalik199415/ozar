<?php
if(isset($PID))
{
$save_img_url = set_url('*/*/photo_save/id/'.$PID);
if(isset($ALB_ID))
{
	$save_img_url = set_url('*/*/photo_in_album_save/id/'.$PID.'/album_id/'.$ALB_ID);
}
?>
<script type="text/javascript">
		var upload1, upload2;

		window.onload = function() {
			upload1 = new SWFUpload({
				// Backend Settings
				upload_url: "<?=$save_img_url?>",
				post_params: {<?=$this->session->get_js_session();?>},

				// File Upload Settings
				file_size_limit : "4 MB",	// 100MB
				file_types : "*.jpg; *.jpeg",
				file_types_description : "Image jpg or jpeg",
				file_upload_limit : 10,
				file_queue_limit : 0,

				// Event Handler Settings (all my handlers are in the Handler.js file)
				swfupload_preload_handler : preLoad,
				swfupload_load_failed_handler : loadFailed,
				file_dialog_start_handler : fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess1,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : "<?=DESIGN_PATH?>swfupload/swf_start_upload.png",
				button_placeholder_id : "spanButtonPlaceholder1",
				button_width: 130,
				button_height: 25,
				button_text_left_padding : 6,
				button_text_top_padding : 1,
				button_text : "<span class='s_upload'>Выбрать файлы</span>",
				button_text_style : ".s_upload { color: #FFFFFF; font-size: 13px; }",
				
				// Flash Settings
				flash_url : "<?=JS_PATH?>swfupload/swfupload.swf",
				flash9_url : "<?=JS_PATH?>swfupload/swfupload_fp9.swf",
			

				custom_settings : {
					progressTarget : "fsUploadProgress1",
					cancelButtonId : "btnCancel1"
				},
				// Debug Settings
				
				debug: false
			});
			function uploadSuccess1(file, data)
			{
				
				try {
					var progress = new FileProgress(file, this.customSettings.progressTarget);
					progress.setComplete();
					progress.setStatus("Complete.");
					progress.toggleCancel(false);
					data = $.parseJSON(data);
					
					$('#photo_info').append(data.html)
					$("#<?=$form_id?> #img_block_"+data.id+" .langs_tabs ul").tabs("#<?=$form_id?> #img_block_"+data.id+" div.langs_tabs_block");

				} catch (ex) {
					this.debug(ex);
				}
			}
	     }
	</script>

<div id="content" class="img_upload_products">
	<table width="100%" border="0">
		<tr valign="top" align="center">
			<td width="100%">
				<div align="center">
					<div class="fieldset flash" id="fsUploadProgress1">
						<span class="legend">Загрузка изображений продукта</span>
					</div>
					<div style="padding-left: 5px;">
						<span id="spanButtonPlaceholder1"></span>
						<input id="btnCancel1" type="button" value="Отменить загрузку" onclick="cancelQueue(upload1);" disabled="disabled" style="margin-left: 2px; height: 24px; font-size: 8pt;" />
						<br />
					</div>
				</div>
				
				<div class="block" style="float:none; width:100%; margin:10px 0 0 0;">
				<div class="block_padding" id="photo_info">
					<?=$img_html?>
				</div>
				</div>
			</td>	
		</tr>
	</table>
</div>

<?php
}
?>