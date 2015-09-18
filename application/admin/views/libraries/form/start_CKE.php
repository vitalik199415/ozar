<?php
$users_domain = $this->musers->get_user();
$users_domain = $users_domain['domain'];
?>
<script>
$(document).ready(function() {
CKEDITOR.config.toolbar = [
								['Source'],
								['Cut','Copy','Paste','PasteText','PasteFromWord','-'],
								['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
								['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak','Iframe'],
								'/',
								['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
								['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
								['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
								['BidiLtr', 'BidiRtl'],
								['Link','Unlink','Anchor'],
								'/',
								['Styles','Format','Font','FontSize'],
								['TextColor','BGColor'],
								['Maximize', 'ShowBlocks']
							];
//CKEDITOR.config.baseHref = '<?='http://'.$users_domain.'/';?>';						
CKEDITOR.replaceAll( '.ckeditor');
});
</script>