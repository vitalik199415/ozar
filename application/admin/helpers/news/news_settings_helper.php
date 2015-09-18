<?php
function helper_news_settings_form_build($data)
{
	$form_id = 'news_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки', $form_id, set_url('*/*/*/save_settings'));	
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('site_modules'),
			'options' => array(	)
		));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton'
			)
		));
	
	$CI->form->add_tab('img_settings', 'Настройки фотографий');	
	
	if(!isset($data['img_settings'])) $data['img_settings'] = FALSE;
	$CI->form->add_group('img_settings', $data['img_settings']);
		
		$lid = $CI->form->group('img_settings')->add_object(
					'text',
					'img_settings[img_width]',
					'Максимальная ширина изображения px :',
					array(
						'option'	=> array('maxlength' => '4')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_height]',
					'Максимальная высота изображения px :',
					array(
						'option'	=> array('maxlength' => '4')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_width_thumbs]',
					'Максимальная ширина изображения(превью) px :',
					array(
						'option'	=> array('maxlength' => '3')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_height_thumbs]',
					'Максимальная высота изображения(превью) px :',
					array(
						'option'	=> array('maxlength' => '3')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'select',
					'img_settings[img_wm]', 
					'Водяной знак :',
					array(
						'options'	=> array('0' => 'Нет', '1' => 'Да')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_text]',
					'Текст водяного знака :',
					array(
						'option'	=> array('maxlength' => '50')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_text_size]',
					'Размер шрифта текста px :',
					array(
						'option'	=> array('maxlength' => '3')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_text_color]',
					'Цвет текста :',
					array(
						'option'	=> array('maxlength' => '7', 'id' => 'textcolor', 'class' => 'iColorPicker', 'style' => 'float:right; width:95%;', 'readonly' => '1')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_text_shadow_color]',
					'Цвет тени :',
					array(
						'option'	=> array('maxlength' => '7', 'id' => 'shadowcolor', 'class' => 'iColorPicker', 'style' => 'float:right; width:95%;', 'readonly' => '1')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_text_shadow_padding]',
					'Отступ тени от шрифта px :',
					array(
						'option'	=> array('maxlength' => '1')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'select',
					'img_settings[img_wm_valign]', 
					'Выравнивание по вертикали :',
					array(
						'options'	=> array('T' => 'По верхнему краю', 'M' => 'По центру', 'B' => 'По нижнему краю')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'select',
					'img_settings[img_wm_align]', 
					'Выравнивание по горизонтали :',
					array(
						'options'	=> array('L' => 'По левому краю', 'C' => 'По центру', 'R' => 'По правому краю')
					)
				);
		$lid = $CI->form->group('img_settings')->add_object( 
					'text',
					'img_settings[img_wm_opacity]',
					'Степень прозрачности от 1 до 99(1 -  полная прозрачность, 99 - без прозрачности) :',
					array(
						'option'	=> array('maxlength' => '2')
					)
				);
		
	$CI->form->add_block_to_tab('img_settings'	, 'img_settings');

	$CI->form->render_form();
}
?>