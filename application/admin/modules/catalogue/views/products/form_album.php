<?php
if(isset($values) && isset($form_id))
{
$CI = & get_instance();
$CI->load->library('form');
?>
<table cellpadding="4" cellspacing="0" width="100%" style="border:2px solid #666666; margin:5px 0 5px 0;">
	<tr>
		<td width="50%" valign="top">
			<div class="field_block">
			<?php
			$CI->form->add_group('left_alb_block', $values['album_data']);
			$CI->form->group('left_alb_block')->add_object(
				'text',
				'product_album[alias]',
				'Идентификатор :',
				array(
					'option'	=> array('class' => $form_id.'_album_alias', 'maxlength' => '20')
				)
			);
			$CI->form->group('left_alb_block')->add_object(
				'select', 
				'product_album[active]', 
				'Активность :',
				array(
					'options'	=> array('1' => 'Да', '0' => 'Нет')
				)
			);
			$CI->form->group('left_alb_block')->add_object(
				'select', 
				'product_album[type]', 
				'Тип кнопки альбома :',
				array(
					'options'	=> array('COLOR' => 'Цвет', 'TEXT' => 'Текст'),
					'option'	=> array('id' => 'album_select_type')
				)
			);
			
			$lid = $CI->form->group('left_alb_block')->add_object(
				'fieldset',
				'base_fieldset',
				'Цвет иконки',
				array('id' => 'ALBUM_COLOR', 'class' => 'album_type_fieldset')
			);	
			$CI->form->group('left_alb_block')->add_object_to($lid,
				'text',
				'product_album[color]',
				'Выбор цвета :',
				array(
					'option' => array('class' => $form_id.'_album_name album_color_pick', 'value' => 'ffffff', 'maxlength' => '6', 'id' => 'color_pick', 'style' => 'float:right; width:95%;', 'readonly' => NULL)
				)
			);
			
			$lid = $CI->form->group('left_alb_block')->add_object(
				'fieldset',
				'base_fieldset',
				'Описание альбома',
				array('style' => 'display:none;', 'id' => 'ALBUM_TEXT', 'class' => 'album_type_fieldset')
			);
			foreach($on_langs as $key => $ms)
			{
				$CI->form->group('left_alb_block')->add_object_to($lid,
					'text',
					'product_album[desc]['.$key.'][name]',
					'Название альбома ('.$ms.') :',
					array(
						'option' => array('class' => $form_id.'_album_name', 'maxlength' => '50')
					)
				);
			}
			$js = '
			$("#'.$form_id.'").find(".album_color_pick").jPicker();
			$("#'.$form_id.'").find("#album_select_type").each(function()
			{
				change_album_type(this);
			});
			$("#'.$form_id.'").on("change", "#album_select_type", function()
			{
				change_album_type(this);
			});
			function change_album_type($this)
			{
				$("#'.$form_id.'").find(".album_type_fieldset").css("display", "none");
				$("#'.$form_id.'").find("#ALBUM_"+$($this).val()).css("display", "block");
			}
			';
			$CI->form->group('left_alb_block')->add_object(
				'js',
				$js
			);
			?>
			<?=$CI->form->group('left_alb_block')->block_to_HTML($form_id, 'left_alb_block_'.$id);?>
			</div>
		</td>
		<td width="50%" valign="top">
			<div class="field_block" style="overflow:auto; height:300px;">
			<?php
			$CI->form->add_group('attributes_block', $values['album_attributes']);
			$CI->form->group('attributes_block')->add_object(
				'html',
				'
				<div class="def_buttons" align="center"><a href="#" id="cancel_attr">Отменить выбранные</a></div><br />'
			);
			foreach($data['product_attributes'] as $key => $ms)
			{
				$display = 'none';
				if(isset($values['album_attributes']) && $values['album_attributes']['album_attributes'] == $key)
				{
					$display = 'block';
				}
				$CI->form->group('attributes_block')->add_object(
					'radio',
					'album_attributes',
					$ms,
					array(
						'option' => array('class' => 'album_attributes', 'value' => $key)
					)
				);
				if(isset($data['product_attributes_options'][$key]))
				{
					$CI->form->group('attributes_block')->add_object(
						'html',
						'<div style="padding:5px 0 0 30px; display:'.$display.'" id="attributes_options_'.$key.'" class="attributes_options">'
					);
					foreach($data['product_attributes_options'][$key] as $opkey => $opms)
					{
						$CI->form->group('attributes_block')->add_object(
							'radio',
							'album_attributes_options',
							$opms,
							array(
								'option' => array('value' => $opkey)
							)
						);
					}
					$CI->form->group('attributes_block')->add_object(
						'html',
						'</div>'
					);
				}
			}
			$js = '
			$("#'.$form_id.'").on("change", ".album_attributes", function()
			{
				change_attr_visible(this);
			});
			function change_attr_visible($this)
			{
				$("#'.$form_id.'").find(".attributes_options").css("display", "none");
				$("#'.$form_id.'").find("#attributes_options_"+$($this).val()).css("display", "block");
				$("#'.$form_id.'").find("#attributes_options_"+$($this).val()).find("input[type=radio]:first").prop("checked", true);
			}
			$("#'.$form_id.'").on("click", "#cancel_attr", function()
			{
				$("#'.$form_id.'").find(".attributes_options").css("display", "none");
				$("#'.$form_id.'").find("input[type=radio]").prop("checked", false);
				return false;
			});
			';
			$CI->form->group('attributes_block')->add_object(
				'js',
				$js
			);
			?>
			<?=$CI->form->group('attributes_block')->block_to_HTML($form_id, 'right_alb_block_'.$id);?>
			</div>
		</td>
	</tr>
</table>
<?php
}
?>