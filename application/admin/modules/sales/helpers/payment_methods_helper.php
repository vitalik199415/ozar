<?php
function helper_users_payment_methods_grid($grid)
{
	$grid->add_button('Добавить метод оплаты', set_url('*/*/add'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
		
	$grid->set_checkbox_actions('ID', 'users_payment_methods_checkbox',
		array(
			'options' => array(
				'active_on' => 'Активность: Да',
				'active_off' => 'Активность: Нет'
			),
			'name' => 'users_payment_methods_action'
		));
		
	$grid->add_column(
		array(
			'index'		 => 'alias',
			'type'		 => 'text',
			'tdwidth'	 => '14%',
			'filter'	 => false
		), 'Идентификатор');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'filter'	 => false
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'active',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Активность');
	$grid->add_column(
		array(
			'index'		 => 'default',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'По умолчанию');	
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '16%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','edit_method','id_users_pm','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title' => 'Редактировать')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','delete_method','id_users_pm','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_delete delete_question', 'title' => 'Удалить')
				)
			)
		), 'Действия');
}

function helper_users_payment_methods_add($data)
{
	$form_id = 'users_payment_methods_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить метод оплаты', $form_id, set_url('*/*/add_save'));
	
	$CI->form->add_button(
		array(
		'name' => 'Следующий шаг >>',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('p_m', 'Методы оплаты');
	$CI->form->add_group('p_m');
	
	$CI->form->group('p_m')->add_object(
		'select', 
		'payment_method', 
		'Выберите метод оплаты :',
		array(
			'options'	=> $data['payment_methods']
		)
	);
		
	$CI->form->add_block_to_tab('p_m', 'p_m');
	$CI->form->render_form();
}

function helper_payment_method_when_picked($data, $id_users_pm = FALSE)
{	
	$form_id = 'payment_method_when_picked';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - При получении', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - При получении', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	$CI->form->render_form();
}

function helper_payment_method_privatbank_ekvaring($data, $id_users_pm = FALSE)
{
	$L['fields_signature']			= 'Подпись(Пароль) для операций';
	$L['fields_merchant_id'] 		= 'ID мерчанта';
	
	//$L['settings_max_transaction_sum']			= 'Максимальная сумма транзакции';
	//$L['settings_max_day_transaction_sum']		= 'Максимальная сумма транзакций в день';
	//$L['settings_max_month_transaction_sum']	= 'Максимальная сумма транзакций в месяц';
	
	$form_id = 'payment_method_privatbank_ekvaring';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - LiqPay', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - LiqPay', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	//$CI->form->add_tab('settings_p_m', 'Настройки');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_validation('payment_method_settings[fields_signature]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_merchant_id]', array('required' => 'true'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->add_group('settings_main', $data['settings']);
	$lid = $CI->form->group('settings_main')->add_object(
		'fieldset',
		'payment_method_data',
		'Настройки метода оплаты'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_merchant_id]',
		$L['fields_merchant_id'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_signature]',
		$L['fields_signature'].' (*):'
	);
	
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	/*$CI->form->add_group('settings_p_m', $data['settings']);
	$CI->form->group('settings_p_m')->add_object(
		'text',
		'payment_method_settings[settings_max_transaction_sum]',
		$L['settings_max_transaction_sum'].' :'
	);
	$CI->form->group('settings_p_m')->add_object(
		'text',
		'payment_method_settings[settings_max_day_transaction_sum]',
		$L['settings_max_day_transaction_sum'].' :'
	);
	$CI->form->group('settings_p_m')->add_object(
		'text',
		'payment_method_settings[settings_max_month_transaction_sum]',
		$L['settings_max_month_transaction_sum'].' :'
	);*/
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('fields_p_m', 'settings_main');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	//$CI->form->add_block_to_tab('settings_p_m', 'settings_p_m');
	$CI->form->render_form();
}

function helper_payment_method_kaznachey_ekvaring($data, $id_users_pm = FALSE)
{
	$L['fields_signature']			= 'Подпись(Пароль) для операций';
	$L['fields_merchant_id'] 		= 'ID мерчанта';
	
	$form_id = 'payment_method_kaznachey_ekvaring';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - kaznachey', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - kaznachey', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_validation('payment_method_settings[fields_signature]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_merchant_id]', array('required' => 'true'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->add_group('settings_main', $data['settings']);
	$lid = $CI->form->group('settings_main')->add_object(
		'fieldset',
		'payment_method_data',
		'Настройки метода оплаты'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_merchant_id]',
		$L['fields_merchant_id'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_signature]',
		$L['fields_signature'].' (*):'
	);
	
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('fields_p_m', 'settings_main');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	
	$CI->form->render_form();
}

function helper_payment_method_to_payment_account($data, $id_users_pm = FALSE)
{
	$L['fields_company_name']	= 'Получатель : Название компании';
	$L['fields_bank_name'] 		= 'Получатель : Название банка';
	$L['fields_ERDPOY']			= 'ЄДРПОУ код';
	$L['fields_payment_account']= 'Расчетный счет';
	$L['fields_bank_code']		= 'Код банка(МФО)';
	
	$form_id = 'payment_payment_method_to_payment_account';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - На расчетный счет', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - На расчетный счет', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_validation('payment_method_settings[fields_company_name]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_bank_name]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_ERDPOY]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_payment_account]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_bank_code]', array('required' => 'true'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->add_group('settings_main', $data['settings']);
	$lid = $CI->form->group('settings_main')->add_object(
		'fieldset',
		'payment_method_data',
		'Настройки метода оплаты'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_company_name]',
		$L['fields_company_name'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_bank_name]',
		$L['fields_bank_name'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_ERDPOY]',
		$L['fields_ERDPOY'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_payment_account]',
		$L['fields_payment_account'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_bank_code]',
		$L['fields_bank_code'].' (*):'
	);
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('fields_p_m', 'settings_main');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	$CI->form->render_form();
}

function helper_payment_method_to_account_of_turkey($data, $id_users_pm = FALSE)
{
	$L['fields_recipient']		= 'Получатель : ';
	$L['fields_swift']			= 'Swift : ';
	$L['fields_expense'] 		= 'Счет : ';
	$L['fields_bank']			= 'Название банка : ';

	$form_id = 'payment_payment_method_to_account_of_turkey';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - На расчетный счет Турции', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - На расчетный счет Турции', $form_id, set_url('*/*/save'));
	}

	$CI->form->add_button(
		array(
			'name' => 'Отменить',
			'href' => set_url('*/*/')
		));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
				'name' => 'Назад',
				'href' => set_url('*/*/')
			));
	}
	else
	{
		$CI->form->add_button(
			array(
				'name' => 'Назад',
				'href' => set_url('*/*/add')
			));
	}
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
			)
		));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		));

	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');

	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));

	$CI->form->add_validation('payment_method_settings[fields_recipient]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_swift]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_expense]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_bank]', array('required' => 'true'));

	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);

	$CI->form->add_group('settings_main', $data['settings']);
	$lid = $CI->form->group('settings_main')->add_object(
		'fieldset',
		'payment_method_data',
		'Настройки метода оплаты'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_recipient]',
		$L['fields_recipient'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_swift]',
		$L['fields_swift'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_expense]',
		$L['fields_expense'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_bank]',
		$L['fields_bank'].' (*):'
	);

	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea',
		'payment_method_description[$][description]',
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)
	);

	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('fields_p_m', 'settings_main');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	$CI->form->render_form();
}

function helper_payment_method_to_credit_cart($data, $id_users_pm = FALSE)
{
	$L['fields_name']				= 'фамилия, Имя';
	$L['fields_credit_cart_number'] = 'Номер банковской карты';
	
	$form_id = 'payment_payment_method_to_payment_account';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - На банковскую карту', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - На банковскую карту', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_validation('payment_method_settings[fields_name]', array('required' => 'true'));
	$CI->form->add_validation('payment_method_settings[fields_credit_cart_number]', array('required' => 'true'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->add_group('settings_main', $data['settings']);
	$lid = $CI->form->group('settings_main')->add_object(
		'fieldset',
		'payment_method_data',
		'Настройки метода оплаты'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_name]',
		$L['fields_name'].' (*):'
	);
	$CI->form->group('settings_main')->add_object_to($lid,
		'text',
		'payment_method_settings[fields_credit_cart_number]',
		$L['fields_credit_cart_number'].' (*):'
	);
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)
	);
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('fields_p_m', 'settings_main');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	$CI->form->render_form();
}

function helper_payment_method_other($data, $id_users_pm = FALSE)
{
	$form_id = 'payment_payment_method_other';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_pm)
	{
		$CI->form->_init('Добавить метод оплаты - Другой', $form_id, set_url('*/*/save/id_users_pm/'.$id_users_pm));
	}
	else
	{
		$CI->form->_init('Добавить метод оплаты - Другой', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*')
	));
	if($id_users_pm)
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*')
		));
	}
	else
	{
		$CI->form->add_button(
			array(
			'name' => 'Назад',
			'href' => set_url('*/*/add')
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back'
		)
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('fields_p_m', 'Необходимые данные');
	$CI->form->add_tab('description_p_m', 'Описание метода');
	
	if($id_users_pm)
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_pm/'.$id_users_pm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('payment_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('payment_method[alias]', array('remote' => 'Метод оплаты с указанным идентификатором уже существует!'));
	
	$CI->form->add_group('fields_p_m', $data['payment_method']);
	$lid = $CI->form->group('fields_p_m')->add_object(
		'fieldset',
		'payment_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'hidden',
		'payment_method[id_m_payment_methods]',
		''
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'text',
		'payment_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_p_m')->add_object_to($lid,
		'select',
		'payment_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['payment_method']['payment_method_description'])) $data['payment_method']['payment_method_description'] = FALSE;
	$CI->form->add_group('description_p_m', $data['payment_method'], $data['on_langs']);
	$CI->form->group('description_p_m')->add_object(
		'text',
		'payment_method_description[$][name]',
		'Название метода оплаты :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_p_m')->add_object(
		'textarea', 
		'payment_method_description[$][description]', 
		'Описание метода оплаты :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	$CI->form->add_block_to_tab('fields_p_m', 'fields_p_m');
	$CI->form->add_block_to_tab('description_p_m', 'description_p_m');
	$CI->form->render_form();
}