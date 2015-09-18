<?php
function helper_shippings_grid_build(Grid $grid)
{
	$grid->add_button('Инвойсы', set_url('*/invoices'));
	$grid->add_button('Заказы', set_url('*/orders'));
	
	$grid->add_column(
		array(
			'index'		 => 'shippings_number',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер отправки');
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
			'index'		 => 'invoices_number',
			'searchtable'=> 'C',
			'type'		 => 'text',
			'tdwidth'	 => '14%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер инвойса');
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
			'index'		 => 'shippings_status',
			'type'		 => 'select',
			'options'	 => array('' => '') + Mshippings::get_shipping_state_collection(),
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
					'href' 			=> set_url('*/*/view_shipping/shp_id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view', 'title' => 'Просмотр')
				)
			)
		), 'Actions');
}

function helper_shippings_create_shipping($ord_id, $order_data)
{
	$form_id = 'shippings_create_shipping';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создание отправки', $form_id, set_url('*/*/save_shipping/ord_id/'.$ord_id));
	
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$order_data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$ord_id)
	));
	$CI->form->add_button(
		array(
		'name' => 'Инвойс '.$order_data['invoice']['invoices_number'],
		'href' => set_url('*/invoices/view/inv_id/'.$order_data['invoice']['id_m_orders_invoices'])
	));
	$CI->form->add_button(
		array(
		'name' => 'Создать отправку',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	$CI->form->add_tab('i_d', 'Данные заказа');
	$CI->form->add_tab('a_d', 'Данные получателя');
	
	$od['order'] = $order_data['order'] + array('invoices_number' => $order_data['invoice']['invoices_number']);
	$CI->form->add_group('i_d', $od);
	
	$CI->form->group('i_d')->add_object(
		'textarea', 
		'shipping[admin_note]',
		'Примечание к отправке :',
		array(
			'option'	=> array('rows' => 3)
		)
	);

	$lid = $CI->form->group('i_d')->add_object(
		'fieldset',
		'order_products_data',
		'Продукты заказа',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'orders_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#orders_products_data').gbc_show_product();
	</script>
	";
	$CI->form->group('i_d')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);
	
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
		'order[invoices_number]',
		'Номер инвойса :',
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
	
	$ad['addresses'] = $order_data['addresses'];
	$CI->form->add_group('a_d', $ad);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][name]',
		'Фамидия, Имя :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][city]',
		'Область, Город :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][fax]',
		'Факс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$CI->form->add_block_to_tab('i_d', 'i_d');
	$CI->form->add_block_to_tab('a_d', 'a_d');
	$CI->form->render_form();
}

function helper_shipping_view($order_data)
{
	$form_id = 'shippings_view_shipping';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Просмотр отправки', $form_id, set_url('*/*/edit_shipping/shp_id/'.$order_data['shipping']['id_m_orders_shippings']));
	
	$CI->form->add_button(
		array(
		'name' => 'Список доставок',
		'href' => set_url('*/*')
	));
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$order_data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$order_data['order']['id_m_orders'])
	));
	$CI->form->add_button(
		array(
		'name' => 'Инвойс '.$order_data['invoice']['invoices_number'],
		'href' => set_url('*/invoices/view_invoice/inv_id/'.$order_data['invoice']['id_m_orders_invoices'])
	));
	if($order_data['shipping']['shippings_status'] != 'CN' && $order_data['shipping']['shippings_status'] != 'C')
	{
		$CI->form->add_button(
			array(
			'name' => 'Отменить отправку',
			'href' => set_url('*/*/cancel_shipping/shp_id/'.$order_data['shipping']['id_m_orders_shippings']),
			'options' => array(
				'class' => 'action_question'
			)
		));
	}
	if($order_data['shipping']['shippings_status'] == 'C')
	{
		$CI->form->add_button(
			array(
			'name' => 'Отправить повторно письмо получателю',
			'href' => set_url('*/shippings/send_shipping_mail/shp_id/'.$order_data['shipping']['id_m_orders_shippings'])
		));
	}
	if($order_data['shipping']['shippings_status'] != 'CN' && $order_data['shipping']['shippings_status'] != 'C')
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
	
	$CI->form->add_tab('s_d', 'Данные отправки');
	$CI->form->add_tab('a_d', 'Данные получателя');
	
	$shipping['shipping'] = $order_data['shipping'];
	$input_disabled = array();
	$select_disabled = array();
	if($shipping['shipping']['shippings_status'] == 'CN' || $shipping['shipping']['shippings_status'] == 'C' )
	{
		$input_disabled = array('readonly' => NULL);
		$select_disabled = array('readonly' => NULL, 'disabled' => NULL);
	}
	$CI->form->add_group('s_d', $shipping);
	
	$lid = $CI->form->group('s_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация об отправке'
	);
	if($shipping['shipping']['shippings_status'] != 'CN' && $shipping['shipping']['shippings_status'] != 'CN')
	{
		$shp_states = Mshippings::get_shipping_state_collection();
		unset($shp_states['CN']);
		unset($shp_states['CM']);
		$CI->form->group('s_d')->add_object_to($lid,
			'select', 
			'shipping[shippings_status]',
			'Состояние отправки :',
			array(
				'options'	=> $shp_states,
				'option'	=> array('id' => 'shipping_state')+$select_disabled
			)
		);
	}
	else
	{
		$shp_states = Mshippings::get_shipping_state_collection();
		$CI->form->group('s_d')->add_object_to($lid,
			'select', 
			'shipping[shippings_status]',
			'Состояние отправки :',
			array(
				'option'	=> $select_disabled,
				'options'	=> $shp_states
			)
		);
	}
	$CI->form->group('s_d')->add_object_to($lid,
		'text', 
		'shipping[shippings_number]',
		'Номер отправки :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	if($shipping['shipping']['shippings_status'] == 'N')
	{
		$CI->form->group('s_d')->add_object_to($lid,
			'html',
			'<div id="shipping_send_mail" class="hidden">'
		);
		$CI->form->group('s_d')->add_object_to($lid,
			'checkbox', 
			'shipping[send_mail]',
			'Отправить письмо получателю :',
			array(
				'value' => 1,
				'option'	=> array('checked' => 'checked')
			)
		);
		$CI->form->group('s_d')->add_object_to($lid,
			'textarea',
			'shipping[note]',
			'Сообщение получателю :',
			array(
				'option'	=> array('rows' => 3)
			)
		);
		$CI->form->group('s_d')->add_object_to($lid,
			'html',
			'</div>'
		);
		$CI->form->add_js_code(
		'
		$(\'#shipping_state\').change(function()
		{
			if($(this).val() == \'C\') {$(\'#shipping_send_mail\').removeClass(\'hidden\');}
			else {$(\'#shipping_send_mail\').addClass(\'hidden\');}
		});');
	}
	else
	{
		$CI->form->group('s_d')->add_object_to($lid,
			'textarea',
			'shipping[note]',
			'Сообщение получателю :',
			array(
				'option'	=> array('rows' => 3)+$input_disabled
			)
		);
	}
	$CI->form->group('s_d')->add_object_to($lid,
		'textarea', 
		'shipping[admin_note]',
		'Примечание к отправке :',
		array(
			'option'	=> array('rows' => 3)+$input_disabled
		)
	);
	$CI->form->group('s_d')->add_object_to($lid,
		'text', 
		'shipping[create_date]',
		'Дата создания :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('s_d')->add_object_to($lid,
		'text', 
		'shipping[update_date]',
		'Дата обновления :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$order['order'] = $order_data['order'];
	$CI->form->add_group('o_d', $order);

	$lid = $CI->form->group('o_d')->add_object(
		'fieldset',
		'order_products_data',
		'Продукты заказа',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'orders_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#orders_products_data').gbc_show_product();
	</script>
	";
	$CI->form->group('o_d')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);

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
		'order[total_qty]',
		'Сумарное количество единиц :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('o_d')->add_object_to($lid,
		'text',
		'order[subtotal]',
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
		'order[total]',
		'Сумма :',
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
	
	$ad['addresses'] = $order_data['addresses'];
	$CI->form->add_group('a_d', $ad);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][name]',
		'Фамидия, Имя :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][city]',
		'Область, Город :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][fax]',
		'Факс :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('a_d')->add_object(
		'text', 
		'addresses[S][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	
	$CI->form->add_block_to_tab('s_d', 's_d');
	$CI->form->add_block_to_tab('s_d', 'o_d');
	$CI->form->add_block_to_tab('a_d', 'a_d');
	$CI->form->render_form();
}
?>