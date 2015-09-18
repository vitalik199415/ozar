<?php
function helper_users_shipping_methods_grid($grid)
{
	$grid->add_button('Добавить метод доставки', set_url('*/*/add'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
		
	$grid->set_checkbox_actions('ID', 'users_shipping_methods_checkbox',
		array(
			'options' => array(
				'active_on' => 'Активность: Да',
				'active_off' => 'Активность: Нет'
			),
			'name' => 'users_shipping_methods_action'
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
					'href' 			=> set_url(array('*','*','edit_method','id_users_sm','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title' => 'Редактировать')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','delete_method','id_users_sm','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_delete delete_question', 'title' => 'Удалить')
				)
			)
		), 'Действия');
}

function helper_users_shipping_methods_add($data)
{
	$form_id = 'users_shipping_methods_add_form';
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
	
	$CI->form->add_tab('s_m', 'Методы доставки');
	$CI->form->add_group('s_m');
	
	$CI->form->group('s_m')->add_object(
		'select', 
		'shipping_method', 
		'Выберите метод доставки :',
		array(
			'options'	=> $data['shipping_methods']
		)
	);
		
	$CI->form->add_block_to_tab('s_m', 's_m');
	$CI->form->render_form();
}

function helper_shipping_method_add_edit_method($data, $id_users_sm = FALSE)
{
	$form_id = 'shipping_method_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	if($id_users_sm)
	{
		$CI->form->_init('Добавить метод доставки', $form_id, set_url('*/*/save/id_users_sm/'.$id_users_sm));
	}
	else
	{
		$CI->form->_init('Добавить метод доставки', $form_id, set_url('*/*/save'));
	}
	
	$CI->form->add_button(
		array(
		'name' => 'Отменить',
		'href' => set_url('*/*/')
	));
	if($id_users_sm)
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
	
	$CI->form->add_tab('fields_s_m', 'Необходимые данные');
	$CI->form->add_tab('description_s_m', 'Описание метода');
	
	if($id_users_sm)
	{
		$CI->form->add_validation('shipping_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias/id_users_sm/'.$id_users_sm).'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('shipping_method[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	$CI->form->add_validation_massages('shipping_method[alias]', array('remote' => 'Метод доставки с указанным идентификатором уже существует!'));
	
	$CI->form->add_group('fields_s_m', $data['shipping_method']);
	$lid = $CI->form->group('fields_s_m')->add_object(
		'fieldset',
		'shipping_method_base_data',
		'Основные данные'
	);
	$CI->form->group('fields_s_m')->add_object_to($lid,
		'hidden',
		'shipping_method[id_m_shipping_methods]',
		''
	);
	$CI->form->group('fields_s_m')->add_object_to($lid,
		'text',
		'shipping_method[alias]',
		'Идентификатор (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('fields_s_m')->add_object_to($lid,
		'select',
		'shipping_method[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('fields_s_m')->add_object_to($lid,
		'select',
		'shipping_method[default]',
		'Выбран по умолчанию (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['shipping_method']['shipping_method_description'])) $data['shipping_method']['shipping_method_description'] = FALSE;
	$CI->form->add_group('description_s_m', $data['shipping_method'], $data['on_langs']);
	$CI->form->group('description_s_m')->add_object(
		'text',
		'shipping_method_description[$][name]',
		'Название метода доставки :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('description_s_m')->add_object(
		'textarea', 
		'shipping_method_description[$][description]', 
		'Описание метода описание :',
		array(
			'option' => array('rows' => '4')
		)	
	);
	
	$CI->form->add_block_to_tab('fields_s_m', 'fields_s_m');
	$CI->form->add_block_to_tab('description_s_m', 'description_s_m');
	$CI->form->render_form();
}