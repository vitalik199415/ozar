<?php
function helper_invoices_grid_build(Grid $grid)
{
	$grid->add_button('Заказы', set_url('*/orders'));
	
	$grid->add_column(
		array(
			'index'		 => 'invoices_number',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '14%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер инвойса');
	$grid->add_column(
		array(
			'index'		 => 'orders_number',
			'searchtable'=> 'B',
			'type'		 => 'text',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер заказа');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text',
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '11%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Создан');
	$grid->add_column(
		array(
			'index'		 => 'update_date',
			'type'		 => 'date',
			'tdwidth'	 => '11%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Обновлен');
	$grid->add_column(
		array(
			'index'		 => 'invoices_status',
			'type'		 => 'select',
			'options'	 => array('' => '') + Minvoices::get_invoice_state_collection(),
			'tdwidth'	 => '12%',
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
					'href' 			=> set_url('*/*/view_invoice/inv_id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view', 'title' => 'Просмотр')
				)
			)
		), 'Actions');
}

function helper_invoices_create_invoice($ord_id, $order_data)
{
	$form_id = 'invoices_create_invoice';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создание инвойса', $form_id, set_url('*/*/save_invoice/ord_id/'.$ord_id));
	
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$order_data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$ord_id)
	));
	$CI->form->add_button(
		array(
		'name' => 'Создать инвойс',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	$CI->form->add_tab('i_d', 'Данные заказа');
	$CI->form->add_tab('a_d', 'Данные плательщика');
	
	$od['order'] = $order_data['order'];
	$CI->form->add_group('i_d', $od);
	
	$CI->form->group('i_d')->add_object(
		'checkbox', 
		'invoice[send_mail]',
		'Отправить инвойс плательщику :',
		array(
			'value' => 1,
			'option'	=> array('checked' => 'checked')
		)
	);
	$CI->form->group('i_d')->add_object(
		'textarea', 
		'invoice[note]',
		'Сообщение плательщику :',
		array(
			'option'	=> array('rows' => 3)
		)
	);
	
	$CI->form->group('i_d')->add_object(
		'textarea', 
		'invoice[admin_note]',
		'Примечание к инвойсу :',
		array(
			'option'	=> array('rows' => 3)
		)
	);
	
	$lid = $CI->form->group('i_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация о заказе'
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'order[orders_number]',
		'Номер заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[currency_name]',
		'Валюта заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'order[total_qty_string]',
		'Сумарное количество единиц :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'order[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'order[payment_method]',
		'Метод оплаты :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'order[shipping_method]',
		'Метод доставки :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[l_name]',
		'Язык заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'textarea', 
		'order[note]',
		'Примечание к заказу :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'textarea',
		'order[admin_note]',
		'Примечание администратора :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);
	
	$ad['addresses'] = $order_data['addresses'];
	$CI->form->add_group('a_d', $ad);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][name]',
		'Фамидия, Имя :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][city]',
		'Область, Город :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][fax]',
		'Факс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$CI->form->add_block_to_tab('i_d', 'i_d');
	$CI->form->add_block_to_tab('a_d', 'a_d');
	$CI->form->render_form();
}

function helper_invoices_view($data)
{
	$form_id = 'invoices_view_invoice';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Просмотр инвойса', $form_id, set_url('*/*/edit_invoice/inv_id/'.$data['invoice']['id_m_orders_invoices']));
	
	$CI->form->add_button(
		array(
		'name' => 'Список инвойсов',
		'href' => set_url('*/*')
	));
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$data['order']['id_m_orders'])
	));
	if(
			($data['invoice']['invoices_status'] != 'CN' && $data['invoice']['invoices_status'] != 'C' && ($data['order']['orders_state'] != 'COD_S' && $data['order']['orders_state'] != 'COD_S_С'))
		|| 	($data['order']['orders_state'] == 'COD' && $data['invoice']['invoices_status'] == 'COD')
	)
	{
		$CI->form->add_button(
			array(
			'name' => 'Отменить инвойс',
			'href' => set_url('*/*/cancel_invoice/inv_id/'.$data['invoice']['id_m_orders_invoices']),
			'options' => array(
				'class' => 'action_question'
			)
		));
	}
	if(($data['order']['orders_state'] != 'CN' && $data['invoice'] !== FALSE && ($data['invoice']['invoices_status'] == 'C' || $data['invoice']['invoices_status'] == 'COD')) && $data['shipping'] === FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Создать отправку',
			'href' => set_url('*/shippings/create_shipping/ord_id/'.$data['order']['id_m_orders'])
		));
	}
	if($data['shipping'] !== FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Отправка '.$data['shipping']['shippings_number'],
			'href' => set_url('*/shippings/view_shipping/shp_id/'.$data['shipping']['id_m_orders_shippings'])
		));
	}
	if($data['invoice']['invoices_status'] != 'CN' && $data['invoice']['invoices_status'] != 'C')
	{
		$CI->form->add_button(
			array(
			'name' => 'Отправить повторно инвойс плательщику',
			'href' => set_url('*/invoices/send_invoice_mail/inv_id/'.$data['invoice']['id_m_orders_invoices'])
		));
	}
	if($data['invoice']['invoices_status'] != 'CN' && $data['order']['orders_state'] == 'IC')
	{
		$CI->form->add_button(
			array(
			'name' => 'Создать отправку',
			'href' => set_url('*/shippings/create_shipping/ord_id/'.$data['order']['id_m_orders'])
		));
	}
	if($data['invoice']['invoices_status'] != 'CN' && $data['invoice']['invoices_status'] != 'C' && $data['invoice']['invoices_status'] != 'COD' && $data['invoice']['invoices_status'] != 'CM')
	{
		$CI->form->add_button(
			array(
			'name' => 'Сохранить изменения',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
			)
		));
	}
	
	$CI->form->add_tab('i_d', 'Данные инвойса');
	$CI->form->add_tab('a_d', 'Данные плательщика');
	
	$invoice['invoice'] = $data['invoice'];
	$input_disabled = array();
	$select_disabled = array();
	if($invoice['invoice']['invoices_status'] == 'CN' || $invoice['invoice']['invoices_status'] == 'C' || $data['invoice']['invoices_status'] == 'COD' || $invoice['invoice']['invoices_status'] == 'CM')
	{
		$input_disabled = array('readonly' => NULL);
		$select_disabled = array('readonly' => NULL, 'disabled' => NULL);
	}
	
	$CI->form->add_group('i_d', $invoice);
	
	$lid = $CI->form->group('i_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация о инвойсе'
	);

	$inv_states = Minvoices::get_invoice_state_collection();
	if($invoice['invoice']['invoices_status'] != 'CN') unset($inv_states['CN']);
	if($invoice['invoice']['invoices_status'] != 'CM') unset($inv_states['CM']);
	
	$CI->form->group('i_d')->add_object_to($lid,
		'select', 
		'invoice[invoices_status]',
		'Состояние инвойса :',
		array(
			'options'	=> $inv_states,
			'option'	=> $select_disabled
		)
	);
	
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'invoice[invoices_number]',
		'Номер инвойса :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'textarea', 
		'invoice[note]',
		'Сообщение плательщику :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'textarea', 
		'invoice[admin_note]',
		'Примечание к инвойсу :',
		array(
			'option'	=> array('rows' => 3)+$input_disabled
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'invoice[create_date]',
		'Дата создания :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text', 
		'invoice[update_date]',
		'Дата обновления :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$order['order'] = $data['order'];
	$CI->form->add_group('o_d', $order);
	
	$lid = $CI->form->group('o_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация о заказе'
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[orders_status_name]',
		'Статус заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text', 
		'order[orders_number]',
		'Номер заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[currency_name]',
		'Валюта заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[total_qty_string]',
		'Сумарное количество единиц :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text', 
		'order[payment_method]',
		'Метод оплаты :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text', 
		'order[shipping_method]',
		'Метод доставки :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[l_name]',
		'Язык заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'textarea', 
		'order[note]',
		'Примечание к заказу :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'textarea',
		'order[admin_note]',
		'Примечание администратора :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);
	
	$ad['addresses'] = $data['addresses'];
	$CI->form->add_group('a_d', $ad);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][name]',
		'Фамидия, Имя :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][city]',
		'Область, Город :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][fax]',
		'Факс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[B][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$CI->form->add_block_to_tab('i_d', 'i_d');
	$CI->form->add_block_to_tab('i_d', 'o_d');
	$CI->form->add_block_to_tab('a_d', 'a_d');
	$CI->form->render_form();
}
?>