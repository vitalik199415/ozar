<?php
function helper_products_settings_form_build($data)
{
	$form_id = 'products_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки продуктов', $form_id, set_url('*/*/save'));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton',
			)
		));
	
	$CI->form->add_tab('main_block'	, 'Основные настройки');
	$CI->form->add_tab('reviews_block'	, 'Настройки отзывов');
	$CI->form->add_tab('related_block'	, 'Сопутствующие продукты');
	$CI->form->add_tab('similar_block'	, 'Похожие продукты');
	$CI->form->add_tab('img_block'	, 'Настройки изображений');
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('main_block', $data['settings']);
	
	$CI->form->group('main_block')->add_object(
		'select', 
		'settings[products_sort_type]', 
		'Сортировка товаров :',
		array(
			'options'	=> array('0' => 'По "весу" от меньшего к большему', '1' => 'По "весу" от большего к меньшему')
		)
	);
	$CI->form->group('main_block')->add_object(
		'text',
		'settings[products_count_to_page]',
		'Продуктов на страницу :',
		array(
			'option'	=> array('maxlength' => '3')
		)
	);
	/*$CI->form->group('main_block')->add_object(
		'select', 
		'pr[have_price]', 
		'Цена у товаров :',
		array(
			'options'	=> array('1' => 'Товары с ценой', '0' => 'Товары без цены')
		)
	);*/
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('reviews_block', $data['settings']);
	
	$CI->form->group('reviews_block')->add_object(
		'select', 
		'settings[reviews_on]', 
		'Отзывы к товару :',
		array(
			'options'	=> array('0' => 'Выключено', '1' => 'Включено')
		)
	);
	$CI->form->group('reviews_block')->add_object(
		'text',
		'settings[reviews_count_to_page]',
		'Количество отзывов на страницу :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	$CI->form->group('reviews_block')->add_object(
		'select', 
		'settings[reviews_publication_immediately]', 
		'Публикация отзыва :',
		array(
			'options'	=> array('0' => 'Опубликовать после подтверждения', '1' => 'Опубликовать сразу')
		)
	);
	$CI->form->group('reviews_block')->add_object(
		'select', 
		'settings[reviews_admin_notice]', 
		'Уведовление о новых отзывах :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('reviews_block')->add_object(
		'text',
		'settings[reviews_admin_email]',
		'E-Mail администратора для уведомлений :',
		array(
			'option'	=> array('maxlength' => '50')
		)
	);
	$CI->form->group('reviews_block')->add_object(
		'text',
		'settings[reviews_admin_name]',
		'Имя администратора :',
		array(
			'option'	=> array('maxlength' => '50')
		)
	);
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('related_block', $data['settings']);
	
	$CI->form->group('related_block')->add_object(
		'select', 
		'settings[related_on]', 
		'Сопутствующие продукты :',
		array(
			'options'	=> array('0' => 'Выключено', '1' => 'Включено')
		)
	);
	$CI->form->group('related_block')->add_object(
		'text',
		'settings[related_count]',
		'Количество выводимых :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	$CI->form->group('related_block')->add_object(
		'text',
		'settings[related_show_count]',
		'Количество в одном блоке :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	
	$CI->form->group('related_block')->add_object(
		'select', 
		'settings[related_random]', 
		'Сортировка случайным образом :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('similar_block', $data['settings']);
	
	$CI->form->group('similar_block')->add_object(
		'select', 
		'settings[similar_on]', 
		'Похожие продукты :',
		array(
			'options'	=> array('0' => 'Выключено', '1' => 'Включено')
		)
	);
	$CI->form->group('similar_block')->add_object(
		'text',
		'settings[similar_count]',
		'Количество выводимых :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	$CI->form->group('similar_block')->add_object(
		'text',
		'settings[similar_show_count]',
		'Количество в одном блоке :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	
	$CI->form->group('similar_block')->add_object(
		'select', 
		'settings[similar_random]', 
		'Сортировка случайным образом :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	
	$CI->form->add_group('img_block', $data['settings']);
		
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_width]',
		'Максимальная ширина изображения px :',
		array(
			'option'	=> array('maxlength' => '4')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_height]',
		'Максимальная высота изображения px :',
		array(
			'option'	=> array('maxlength' => '4')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_width_thumbs]',
		'Максимальная ширина изображения(превью) px :',
		array(
			'option'	=> array('maxlength' => '3')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_height_thumbs]',
		'Максимальная высота изображения(превью) px :',
		array(
			'option'	=> array('maxlength' => '3')
		)
	);
	$CI->form->group('img_block')->add_object(
		'select',
		'settings[img_wm]', 
		'Водяной знак :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_text]',
		'Текст водяного знака :',
		array(
			'option'	=> array('maxlength' => '50')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_text_size]',
		'Размер шрифта текста px :',
		array(
			'option'	=> array('maxlength' => '3')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_text_color]',
		'Цвет текста :',
		array(
			'option'	=> array('maxlength' => '7', 'id' => 'textcolor', 'class' => 'iColorPicker', 'style' => 'float:right; width:95%;', 'readonly' => '1')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_text_shadow_color]',
		'Цвет тени :',
		array(
			'option'	=> array('maxlength' => '7', 'id' => 'shadowcolor', 'class' => 'iColorPicker', 'style' => 'float:right; width:95%;', 'readonly' => '1')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_text_shadow_padding]',
		'Отступ тени от шрифта px :',
		array(
			'option'	=> array('maxlength' => '1')
		)
	);
	$CI->form->group('img_block')->add_object(
		'select',
		'settings[img_wm_valign]', 
		'Выравнивание по вертикали :',
		array(
			'options'	=> array('T' => 'По верхнему краю', 'M' => 'По центру', 'B' => 'По нижнему краю')
		)
	);
	$CI->form->group('img_block')->add_object(
		'select',
		'settings[img_wm_align]', 
		'Выравнивание по горизонтали :',
		array(
			'options'	=> array('L' => 'По левому краю', 'C' => 'По центру', 'R' => 'По правому краю')
		)
	);
	$CI->form->group('img_block')->add_object(
		'text',
		'settings[img_wm_opacity]',
		'Степень прозрачности от 1 до 99(1 -  полная прозрачность, 99 - без прозрачности) :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
		
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('reviews_block', 'reviews_block');
	$CI->form->add_block_to_tab('related_block', 'related_block');
	$CI->form->add_block_to_tab('similar_block', 'similar_block');
	$CI->form->add_block_to_tab('img_block'	, 'img_block');

	$CI->form->render_form();
}
?>