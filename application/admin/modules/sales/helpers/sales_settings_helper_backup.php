<?php
function helper_sales_settings_form_build($data)
{
	$form_id = 'sales_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки', $form_id, set_url('*/*/save'));	
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton'
			)
		)
	);
	
	$CI->form->add_tab('orders_settings', 'Настройки уведомлений');
	$CI->form->add_tab('orders_settings_display', 'Настройки полей');

	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('registration_notice_block', $data['settings']);
	$CI->form->add_group('orders_settings', $data);

		$CI->form->group('orders_settings')->add_object(
			'select',
			'settings[mail_send_confirmed]',
			'Тип обработки заказов :',
			array(
				'options'	=> array('0' => 'Уведомлять о всех заказах', '1' => 'Уведомлять только о подтвержденных')
			)
		);
		$CI->form->group('orders_settings')->add_object(
			'text',
			'settings[mail_new_order_email]',
			'E-mail для уведомлений о заказах :',
			array(
				'option'	=> array('maxlength' => '50')
			)
		);
		$CI->form->group('orders_settings')->add_object( 
			'text',
			'settings[mail_shop_name]',
			'Название магазина :',
			array(
				'option'	=> array('maxlength' => '50')
			)
		);

	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('orders_settings_display', $data['settings']);
	$CI->form->add_group('orders_B_settings_display', $data);

	$lid = $CI->form->group('orders_B_settings_display')->add_object(
		'fieldset',
		'base_fieldset',
		'Обязательные поля плательщика'
	);

	$CI->form->group('orders_B_settings_display')->add_object_to($lid,
		'select',
		'settings[address_B_country]',
		'Страна:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_B_settings_display')->add_object_to($lid,
		'select',
		'settings[address_B_city]',
		'Город:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_B_settings_display')->add_object_to($lid,
		'select',
		'settings[address_B_zip]',
		'Индекс:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_B_settings_display')->add_object_to($lid,
		'select',
		'settings[address_B_address]',
		'Адрес:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_B_settings_display')->add_object_to($lid,
		'select',
		'settings[address_B_telephone]',
		'Телефон:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('orders_settings_display', $data['settings']);
	$CI->form->add_group('orders_S_settings_display', $data);

	$lid = $CI->form->group('orders_S_settings_display')->add_object(
		'fieldset',
		'base_fieldset',
		'Обязательные поля получателя'
	);

	$CI->form->group('orders_S_settings_display')->add_object_to($lid,
		'select',
		'settings[address_S_country]',
		'Страна:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_S_settings_display')->add_object_to($lid,
		'select',
		'settings[address_S_city]',
		'Город:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_S_settings_display')->add_object_to($lid,
		'select',
		'settings[address_S_zip]',
		'Индекс:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_S_settings_display')->add_object_to($lid,
		'select',
		'settings[address_S_address]',
		'Адрес:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->group('orders_S_settings_display')->add_object_to($lid,
		'select',
		'settings[address_S_telephone]',
		'Телефон:',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да', '2' => 'Скрыть')
		)
	);

	$CI->form->add_block_to_tab('orders_settings'	, 'orders_settings');
	$CI->form->add_block_to_tab('orders_settings_display'	, 'orders_B_settings_display');
	$CI->form->add_block_to_tab('orders_settings_display'	, 'orders_S_settings_display');


	$CI->form->render_form();
}
?>