<?php
if(isset($id) && isset($id_users) && isset($image))
{
if(!isset($values)) $values = FALSE;
?>
<table cellpadding="4" cellspacing="0" width="100%" style="border:2px solid #666666; margin:5px 0 5px 0;">
	<tr>
		<td width="210" valign="middle" align="center">
			<img src="<?=$image?>" style="display:block;">
			<div style="margin:5px 0 0 0">
				<a href="<?=setUrl('*/*/*/delete_photo/id/'.$PID.'/img_id/'.$id);?>" class="icon_detele delete_question" title="Удалить изображение"></a>
				<a href="<?=setUrl('*/*/*/change_position_photo/id/'.$PID.'/img_id/'.$id).'/position/up';?>" class="arrow_up" style="margin:0 0 0 15px;" title="Смена позиции: Поднять"></a>
				<a href="<?=setUrl('*/*/*/change_position_photo/id/'.$PID.'/img_id/'.$id.'/position/down');?>" class="arrow_down" title="Смена позиции: Опустить"></a>
			</div>	
		</td>
		<td>
			<div class="field_block">
			<?php
			$CI = & get_instance();
			$CI->load->library('form');
			$CI->form->add_group('img_desc', $values, $on_langs);
			$CI->form->group('img_desc')->add_object(
				'text',
				'img_desc['.$id.'][$][name]',
				'Название : '
			);
			$CI->form->group('img_desc')->add_object(
				'text',
				'img_desc['.$id.'][$][title]',
				'Title : '
			);
			$CI->form->group('img_desc')->add_object(
				'text',
				'img_desc['.$id.'][$][alt]',
				'Alt : '
			);
			$CI->form->group('img_desc')->add_object(
				'hidden',
				'img_desc['.$id.'][$][id_m_photo_gallery_photos_description]',
				'Alt : '
			);
			
			if(!isset($ajax))
			{
				$ajax = FALSE;
			}
			?>
			<?=$CI->form->group('img_desc')->block_to_HTML($form_id, 'img_block_'.$id, $ajax);?>
			</div>
		</td>
	</tr>
</table>
<?php
}
?>