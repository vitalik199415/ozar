<?php
function helper_customers_grid_build($grid, $c_groups)
{
	$grid->add_button('Экспорт в Excel', set_url('*/customers_excel_export'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
	$grid->add_button('Добавить покупателя', set_url('*/add'), 
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
		
	$grid->set_checkbox_actions('ID', 'customers_checkbox',
			array(
				'options' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete' => 'Удалить выбраных'
				),
				'name' => 'actions_with_customers'
			));
			
	$grid->add_column(
		array
			(
				'index'		 => 'email',
				'searchtable'=> 'A',
				'type'		 => 'text',
				'filter'	 => true
			), 'E-Mail');
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'filter'	 => true
			),'Фамилия, Имя');
	$grid->add_column(
		array
			(
				'index'		 => 'city',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'tdwidth'	 => '10%',
				'filter'	 => true
			),'Город');		
	$grid->add_column(
		array
			(
				'index'		 => 'create_date',
				'type'		 => 'date',
				'tdwidth'	 => '11%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Создан');
	$grid->add_column(
		array
			(
				'index'		 => 'update_date',
				'type'		 => 'date',
				'tdwidth'	 => '11%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Обновлен');
	$grid->add_column(
		array
			(
				'index'		 => 'active',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			), 'Активность');
	$grid->add_column(
		array
			(
				'index'		 => 'id_m_u_types',
				'type'		 => 'select',
				'options'	 => array('' => '') + $c_groups,
				'tdwidth'	 => '10%',
				'filter'	 => true
			), 'Группы');		
	$grid->add_column(
			array
			(
				'index'		 => 'action',
				'type'		 => 'action',
				'tdwidth'	 => '10%',
				'option_string' => 'align="center"',
				'sortable' 	 => false,
				'filter'	 => false,
				'actions'	 => array(
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/view/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class' => 'icon_view', 'title' => 'Просмотр')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/edit/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class' => 'icon_edit', 'title' => 'Редактировать')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/delete/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class' => 'icon_detele delete_question', 'title' => 'Удалить')
					)
				)
			), 'Actions');	
}

function helper_customers_form_build($data, $save_param = '')
{
	$form_id = 'customers_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление | редактирование покупателя', $form_id, set_url('*/save'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/'),
			'options' => array( ),
		)
	);
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить покупателя',
				'href' => set_url('*/add')
			)
		);
		$CI->form->add_button(
			array(
				'name' => 'Удалить покупателя',
				'href' => set_url('*/delete'.$save_param),
				'options' => array(
					'class' => 'delete_question'
				)
			)
		);
	}	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton'
   			)
		)
	);
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
	
	$CI->form->add_tab('c_b', 'Основные атрибуты');
	$CI->form->add_tab('c_a', 'Адреса покупателя');

	$CI->form->add_validation('customer[name]', array('required' => 'true', 'minlength' => '3'));
	if($save_param == '')
	{
		$CI->form->add_validation('customer[email]', array('required' => 'true', 'email' => 'true', 'remote' => '{url:"'.set_url('*/check_email').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('customer[email]', array('required' => 'true', 'email' => 'true', 'remote' => '{url:"'.set_url('*/check_email'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('customer[email]', array('remote' => 'Пользователь с указанным E-Mail уже существует!'));
	if($save_param == '') $CI->form->add_validation('customer[password]', array('required' => 'true', 'minlength' => '6'));
	
	$edit_data = FALSE;
	if(isset($data['customer'])) $edit_data['customer'] = $data['customer'];
	if(isset($data['customer_types'])) $edit_data['customer_types'] = $data['customer_types'];
	$CI->form->add_group('c_b', $edit_data);
	$CI->form->group('c_b')->add_object(
		'text', 
		'customer[email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('c_b')->add_object(
		'text',
		'customer[name]',
		'Никнейм (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	if($save_param != '')
	{
		$CI->form->group('c_b')->add_object(
			'select',
			'customer[active]',
			'Активность :',
			array(
				'options'	=> array('1' => 'Да', '0' => 'Нет')
			)
		);
	}
	$lid = $CI->form->group('c_b')->add_object(
		'fieldset',
		'customers_type',
		'Группы покупателей'
	);
	$CI->form->group('c_b')->add_object_to($lid,
		'select', 
		'customer[have_m_u_types]',
		'Тип покупателя :',
		array(
			'options'	=> array('0' => 'Обычный покупатель', '1' => 'Выбрать группы покупателей'),
			'option'	=> array('id' => $form_id.'_customers_types_select')
		)
	);
	
	$display = 'none';
	if(isset($data['customer']['have_m_u_types']) && $data['customer']['have_m_u_types'] == 1)
	{
		$display = 'block';
	}
	$CI->form->group('c_b')->add_object_to($lid,
		'html',
		'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_customers_types">'
	);

	$CI->form->group('c_b')->add_object_to($lid,
		'checkbox',
		'types_email',
		'Отправлять уведомление о добавлении в новую группу :',
		array(
			'value' => 1,
			'option' => array('checked' => 'checked')
		)
	);
	$CI->form->group('c_b')->add_object_to($lid,
		'html',
		'<div style="padding:10px 0 0 0;"></div>'
	);

	foreach($data['data_customers_types'] as $key => $ms)
	{
		$CI->form->group('c_b')->add_object_to($lid,
			'checkbox', 
			'customer_types['.$key.']', 
			'Группа '.$ms.' :',
			array(
				'value' => $key
			)
		);
	}
	
	$CI->form->group('c_b')->add_object_to($lid,
		'html',
		'</div>'
	);
	
	$js = "
	$('#".$form_id."_customers_types_select').live('change',function()
		{
			if($(this).val() == 1)
			{
				$('#".$form_id."_customers_types').css('display','block');
			}
			else
			{
				$('#".$form_id."_customers_types').css('display','none');
			}
		}
	);
	";

	$CI->form->group('c_b')->add_object(
		'js',
		$js
	);
	
	//Customer Addresses
	$edit_data = FALSE;
	if(isset($data['customer_address'])) $edit_data['customer_address'] = $data['customer_address'];
	$CI->form->add_group('c_a', $edit_data);
	$lid = $CI->form->group('c_a')->add_object(
		'fieldset',
		'customer_address_b_fieldset',
		'Адрес плательщика',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
		
		if($save_param == '')
		{
			$CI->form->group('c_a')->add_object(
				'checkbox', 
				'same_as_billing',
				'Адрес доставки совпадает с платежным адресом :',
				array(
					'value'	=> 0,
					'option' => array('id' => 'same_as_billing_checkbox')
				)
			);
		}
		
	$lid = $CI->form->group('c_a')->add_object(
		'fieldset',
		'customer_address_s_fieldset',
		'Адрес получателя',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('c_a')->add_object(
		'hidden', 
		'customer_address[S][id_m_u_customers_address]'
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	
	$CI->form->add_js_code('$("#'.$form_id.'").gbc_customers_add()');

	$CI->form->add_block_to_tab('c_b', 'c_b');
	$CI->form->add_block_to_tab('c_a', 'c_a');
	$CI->form->render_form();
}

function helper_customers_view_build($id, $data)
{
	$form_id = 'customers_view_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Просмотр покупателя', $form_id, set_url('*/save'));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*'),
			'options' => array(),
		)
	);	
	$CI->form->add_button(
		array(
			'name' => 'Добавить покупателя',
			'href' => set_url('*/add')
		)
	);
	$CI->form->add_button(
		array(
			'name' => 'Удалить покупателя',
			'href' => set_url('*/delete/id/'.$id),
			'options' => array(
				'class' => 'delete_question'
			)
		)
	);
	$CI->form->add_button(
		array(
			'name' => 'Редактировать покупателя',
			'href' => set_url('*/edit/id/'.$id),
		)
	);
	
	
	$CI->form->add_tab('c_b', 'Основные атрибуты');
	$CI->form->add_tab('c_a', 'Адреса покупателя');
	$CI->form->add_tab('c_o', 'Заказы покупателя');
	
	$view_data = FALSE;
	$view_data['customer'] = $data['customer'];
	$view_data['customer_types'] = $data['customer_types'];
	$CI->form->add_group('c_b', $view_data);
	$CI->form->group('c_b')->add_object(
		'text',
		'customer[email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '50', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_b')->add_object(
		'text',
		'customer[name]',
		'Никнейм (*):',
		array(
			'option'	=> array('maxlenght' => '50', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_b')->add_object(
		'select',
		'customer[active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет'),
			'option'	=> array('readonly' => NULL, 'disabled' => NULL)
		)
	);
	
	$lid = $CI->form->group('c_b')->add_object(
		'fieldset',
		'customers_type',
		'Группы покупателей'
	);
	$CI->form->group('c_b')->add_object_to($lid,
		'select', 
		'customer[have_m_u_types]',
		'Тип покупателя :',
		array(
			'options'	=> array('0' => 'Обычный покупатель', '1' => 'Выбрать группы покупателей'),
			'option'	=> array('readonly' => NULL, 'disabled' => NULL)
		)
	);
	
	$display = 'none';
	if(isset($data['customers']['have_m_u_types']) && $data['customers']['have_m_u_types'] == 1)
	{
		$display = 'block';
	}
	$CI->form->group('c_b')->add_object_to($lid,
		'html',
		'<div style="padding:5px 0 0 30px; display:'.$display.'">'
	);
	
	foreach($data['data_customers_types'] as $key => $ms)
	{
		$CI->form->group('c_b')->add_object_to($lid,
			'checkbox', 
			'customer_types['.$key.']', 
			$ms.' :',
			array(
				'value' => $key,
				'option'	=> array('readonly' => NULL, 'disabled' => NULL)
			)
		);
	}
	
	$CI->form->group('c_b')->add_object_to($lid,
		'html',
		'</div>'
	);
	
	//Customer addresses
	$view_data = FALSE;
	if(isset($data['customer_address'])) $view_data['customer_address'] = $data['customer_address'];
	$CI->form->add_group('c_a', $view_data);
	$lid = $CI->form->group('c_a')->add_object(
		'fieldset',
		'customer_address_b_fieldset',
		'Адрес плательщика'
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[B][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
		
	$lid = $CI->form->group('c_a')->add_object(
		'fieldset',
		'customer_address_s_fieldset',
		'Адрес получателя'
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('c_a')->add_object_to($lid,
		'text', 
		'customer_address[S][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	//------------Блок адреса покупателя----------------
	
	$CI->form->add_group('c_o');
	$lid = $CI->form->group('c_o')->add_object(
		'fieldset',
		'base_fieldset',
		'Заказы пользователя',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	
	$CI->form->group('c_o')->add_html_to($lid, $data['customer_orders']);

	$CI->form->add_block_to_tab('c_b', 'c_b');
	$CI->form->add_block_to_tab('c_a', 'c_a');
	$CI->form->add_block_to_tab('c_o', 'c_o');
	$CI->form->render_form();
}

function helper_customer_orders_grid_build($grid)
{
	$grid->add_column(
		array(
			'index'		 => 'orders_number',
			'searchname' => 'orders_number',
			'searchtable'=> 'A',
			'type'		 => 'number',
			'tdwidth'	 => '16%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер заказа');
	
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text'
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Создан');
	$grid->add_column(
		array(
			'index'		 => 'update_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Обновлен');
	$grid->add_column(
		array(
			'index'		 => 'orders_state',
			'searchname' => 'orders_state',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '') + Morders::get_order_state_collection(),
			'tdwidth'	 => '14%',
			'filter'	 => true
		), 'Состояние');
		
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '10%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('sales','orders','view','ord_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view', 'title' => 'Просмотр', 'target' => '_blank')
				)
			)
		), 'Действия');
}
?>