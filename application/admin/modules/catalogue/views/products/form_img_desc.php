<?php
if(isset($id) && isset($id_users) && isset($timage))
{
if(!isset($values)) $values = FALSE;
$CI = & get_instance();
$CI->load->library('form');
?>
<table cellpadding="4" cellspacing="0" width="100%" style="border:2px solid #666666; margin:5px 0 5px 0;">
	<tr>
		<td width="210" valign="middle" align="center">
			<a href="<?=$bimage?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$timage?>" style="display:block; max-width:200px;"></a>
			<div style="margin:5px 0 0 0">
				<?php
				if(isset($album_id) && $album_id) 
				{
				?>
				<a href="<?=setUrl('*/*/delete_photo_in_album/id/'.$PID.'/img_id/'.$id.'/album_id/'.$album_id);?>" class="icon_detele delete_question" title="Удалить изображение"></a>
				<a href="<?=setUrl('*/*/change_position_photo_in_album/id/'.$PID.'/img_id/'.$id.'/album_id/'.$album_id.'/position/up');?>" class="arrow_up" style="margin:0 0 0 15px;" title="Смена позиции: Поднять"></a>
				<a href="<?=setUrl('*/*/change_position_photo_in_album/id/'.$PID.'/img_id/'.$id.'/album_id/'.$album_id.'/position/down');?>" class="arrow_down" title="Смена позиции: Опустить"></a>
				<?php
				}
				else
				{
				?>
				<a href="<?=setUrl('*/*/delete_photo/id/'.$PID.'/img_id/'.$id);?>" class="icon_detele delete_question" title="Удалить изображение"></a>
				<a href="<?=setUrl('*/*/change_position_photo/id/'.$PID.'/img_id/'.$id).'/position/up';?>" class="arrow_up" style="margin:0 0 0 15px;" title="Смена позиции: Поднять"></a>
				<a href="<?=setUrl('*/*/change_position_photo/id/'.$PID.'/img_id/'.$id.'/position/down');?>" class="arrow_down" title="Смена позиции: Опустить"></a>
				<?php
				}
				?>
			</div>	
		</td>
		<td width="180" valign="top" align="center">
			<?php
			$alb_ulr = '';
			if(isset($album_id) && $album_id) 
			{
				$alb_ulr = '/album_id/'.$album_id;
			}
			$CI->form->add_group('immg_b_block');
			$CI->form->group('immg_b_block')->add_object(
				'html',
				'
				<div align="center" style="font-size:14px; color:#EEEEEE;">Назначить фото как :</div><BR>'
			);
			if(!$preview['preview'])
			{
				$CI->form->group('immg_b_block')->add_object(
					'html',
					'
					<div class="def_buttons" align="center"><a href="'.set_url('*/*/set_preview/id/'.$PID.'/img_id/'.$id.$alb_ulr).'">Превью продукта</a></div>'
				);
			}
			if(isset($album_id) && $album_id) 
			{
				if(!$preview['album_preview'])
				{
					$CI->form->group('immg_b_block')->add_object(
						'html',
						'
						<br><div class="def_buttons" align="center"><a href="'.set_url('*/*/set_album_preview/id/'.$PID.'/img_id/'.$id.$alb_ulr).'">Превью альбома</a></div>'
					);
				}
			}
			if(!isset($ajax))
			{
				$ajax = FALSE;
			}
			?>
			<?=$CI->form->group('immg_b_block')->block_to_HTML($form_id, 'img_b_block_'.$id, $ajax);?>
		</td>
		<td>
			<div class="field_block">
			<?php
			$CI->form->add_group('immg_block', $values, $on_langs);
			$CI->form->group('immg_block')->add_object(
				'text',
				'img_desc['.$id.'][$][name]',
				'Название : '
			);
			$CI->form->group('immg_block')->add_object(
				'text',
				'img_desc['.$id.'][$][title]',
				'Title : '
			);
			$CI->form->group('immg_block')->add_object(
				'text',
				'img_desc['.$id.'][$][alt]',
				'Alt : '
			);
			
			if(!isset($ajax))
			{
				$ajax = FALSE;
			}
			?>
			<?=$CI->form->group('immg_block')->block_to_HTML($form_id, 'img_block_'.$id, $ajax);?>
			</div>
		</td>
	</tr>
</table>
<?php
}
?>