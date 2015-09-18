<?php
function helper_reviews_settings_form_build($data)
{
	$form_id = 'reviews_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки', $form_id, set_url('*/*/*/save_settings'));	
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/*'),
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
	
	$CI->form->add_tab('reviews_settings', 'Настройки модуля "Отзывы"');

	$s_data = FALSE;
	if(isset($data['settings'])) $s_data['settings'] = $data['settings'];
	$CI->form->add_group('reviews_settings', $s_data);
		
	$CI->form->group('reviews_settings')->add_object(
		'text',
		'settings[reviews_count_to_page]',
		'Соличество на страницу :',
		array(
			'option'	=> array('maxlength' => '2')
		)
	);
	$CI->form->group('reviews_settings')->add_object(
		'text',
		 'settings[reviews_admin_name]',
		 'Имя администратора :',
		 array(
			 'option'	=> array('maxlength' => '30')
		 )
	);
	$CI->form->group('reviews_settings')->add_object(
		'text',
		'settings[reviews_admin_email]',
		'Email администратора :',
		array(
			'option'	=> array('maxlength' => '30')
		)
	);
	$CI->form->group('reviews_settings')->add_object(
		'select',
		'settings[reviews_publication_immediately]',
		'Публикация :',
		array(
			'options'	=> array('0' => 'Опубликовать после подтверждения', '1' => 'Опубликовать сразу')
		)
	);
	$CI->form->group('reviews_settings')->add_object(
		'select',
		'settings[reviews_admin_notice]',
		'Уведомление о новых отзывах :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
		
	$CI->form->add_block_to_tab('reviews_settings'	, 'reviews_settings');
	$CI->form->render_form();
}
?>