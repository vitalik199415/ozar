<?php
function helper_customers_settings_form_build($data)
{
	$form_id = 'customers_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки пользователя', $form_id, set_url('*/*/save'));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton',
			)
		));
	
	$CI->form->add_tab('registration_notice_block'	, 'Уведомление о регистрации');
	$CI->form->add_tab('register_block'	, 'Настройки регистрации');
	$CI->form->add_tab('mailing_block'	, 'Настройки рассылки');
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('registration_notice_block', $data['settings']);
	
	$CI->form->group('registration_notice_block')->add_object(
		'select',
		'settings[registration_notice_on]', 
		'Уведомлять о регистрации:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('registration_notice_block')->add_object(
		'text',
		'settings[registration_notice_email]', 
		'E-Mail для уведомления:'
	);
	
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('register_block', $data['settings']);
	
	$lid = $CI->form->group('register_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Обязательные поля плательщика'
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_name]', 
		'Имя:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_country]', 
		'Страна:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_city]', 
		'Город:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_zip]', 
		'Индекс:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_address]', 
		'Адрес:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_telephone]', 
		'Телефон:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->group('register_block')->add_object_to($lid,
		'select',
		'settings[address_B_address_email]', 
		'Email адрес:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('mailing_block', $data['settings']);
		
	$CI->form->group('mailing_block')->add_object(
		'text',
		'settings[distribution_email]', 
		'Обратный серверный Email адрес:'
	);
	
	$CI->form->add_block_to_tab('registration_notice_block', 'registration_notice_block');
	$CI->form->add_block_to_tab('register_block', 'register_block');
	$CI->form->add_block_to_tab('mailing_block'	, 'mailing_block');

	$CI->form->render_form();
}
?>