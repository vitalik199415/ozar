<?php
function helper_credit_memo_grid_build(Grid $grid)
{
	$grid->add_button('Отправки', set_url('*/shippings'));
	$grid->add_button('Инвойсы', set_url('*/invoices'));
	$grid->add_button('Заказы', set_url('*/orders'));
	
	$grid->add_column(
		array(
			'index'		 => 'credit_memo_number',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '14%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер возврата');
	$grid->add_column(
		array(
			'index'		 => 'orders_number',
			'searchtable'=> 'B',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Номер заказа');
	$grid->add_column(
		array(
			'index'		 => 'invoices_number',
			'searchtable'=> 'C',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Номер инвойса');
	$grid->add_column(
		array(
			'index'		 => 'shippings_number',
			'searchtable'=> 'D',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Номер отправки');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text',
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'admin_note',
			'type'		 => 'text',
		), 'Комментарий');
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
					'href' 			=> set_url('*/*/view_credit_memo/cm_id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view', 'title' => 'Просмотр')
				)
			)
		), 'Actions');
}

function helper_credit_memo_create_credit_memo($order_data)
{
	$form_id = 'credit_memo_create-view_credit_memo';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создание возврата', $form_id, set_url('*/*/save_credit_memo/ord_id/'.$order_data['order']['id_m_orders']));

	$CI->form->add_button(
		 array(
			 'name' => 'Список возвратов',
			 'href' => set_url('*/credit_memo')
		 ));
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$order_data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$order_data['order']['id_m_orders'])
	));
	$CI->form->add_button(
		array(
		'name' => 'Создать возврат',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$input_disabled = array('readonly' => NULL);
	
	$CI->form->add_tab('cm', 'Данные возврата');
	$CI->form->add_tab('a_b', 'Плательщик и получатель');
	
	$credit_memo['credit_memo'] = FALSE;
	$CI->form->add_group('cm', $credit_memo);
	
	/*$CI->form->group('i_d')->add_object(
		'checkbox', 
		'invoice[send_mail]',
		'Отправить инвойс плательщику :',
		array(
			'value' => 1,
			'option'	=> array('checked' => 'checked')
		)
	);*/
	/*$CI->form->group('i_d')->add_object(
		'textarea', 
		'invoice[note]',
		'Сообщение плательщику :',
		array(
			'option'	=> array('rows' => 3)
		)
	);*/
	$CI->form->group('cm')->add_object(
		'textarea', 
		'credit_memo[admin_note]',
		'Примечание к возврату :',
		array(
			'option'	=> array('rows' => 3)
		)
	);

	$lid = $CI->form->group('cm')->add_object(
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
	$CI->form->group('cm')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);
	
	$order['order'] = $order_data['order'];
	$CI->form->add_group('i_d', $order);
	$lid = $CI->form->group('i_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация о заказе'
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[orders_status_name]',
		'Статус заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
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
		'order[total_qty]',
		'Сумарное количество единиц :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[subtotal]',
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
		'order[total]',
		'Сумма :',
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
	
	$address['addresses'] = $order_data['addresses'];
	$CI->form->add_group('a_b', $address);
	
	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Адрес плательщика',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text',
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);

	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	
	$CI->form->add_block_to_tab('cm', 'cm');
	$CI->form->add_block_to_tab('cm', 'i_d');
	$CI->form->add_block_to_tab('a_b', 'a_b');
	
	if($order_data['invoice'] !== FALSE)
	{
		$invoice['invoice'] = $order_data['invoice'];
		$CI->form->add_tab('i_b', 'Инвойс');
		$CI->form->add_group('i_b', $invoice);
		
		$lid = $CI->form->group('i_b')->add_object(
			'fieldset',
			'order_invoice_fieldset',
			'Данные инвойса'
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[invoices_number]',
			'Номер инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[invoices_status_name]',
			'Статус инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'textarea',
			'invoice[note]',
			'Сообщение плательщику :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'textarea',
			'invoice[admin_note]',
			'Примечание к инвойсу :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('i_b', 'i_b');
	}
	
	if($order_data['shipping'] !== FALSE)
	{
		$shipping['shipping'] = $order_data['shipping'];
		$CI->form->add_tab('s_b', 'Отправка');
		$CI->form->add_group('s_b', $shipping);
		
		$lid = $CI->form->group('s_b')->add_object(
			'fieldset',
			'order_$shipping_fieldset',
			'Данные отправки'
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[shippings_number]',
			'Номер отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[shippings_status_name]',
			'Статус отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'textarea',
			'shipping[note]',
			'Сообщение получателю :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'textarea',
			'shipping[admin_note]',
			'Примечание к отправке :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('s_b', 's_b');
	}
	
	$CI->form->render_form();
}

function helper_credit_memo_view_credit_memo($order_data)
{
	$form_id = 'credit_memo_view_credit_memo';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создание возврата', $form_id, set_url('*/*/save_credit_memo/ord_id/'.$order_data['order']['id_m_orders']));

	$CI->form->add_button(
		 array(
			 'name' => 'Список возвратов',
			 'href' => set_url('*/credit_memo')
		 ));
	$CI->form->add_button(
		array(
		'name' => 'Заказ '.$order_data['order']['orders_number'],
		'href' => set_url('*/orders/view/ord_id/'.$order_data['order']['id_m_orders'])
	));
	
	$input_disabled = array('readonly' => NULL);
	
	$CI->form->add_tab('cm', 'Данные возврата');
	$CI->form->add_tab('a_b', 'Плательщик и получатель');
	
	$credit_memo['credit_memo'] = FALSE;
	if(isset($data['credit_memo'])) $credit_memo['credit_memo'] = $order_data['credit_memo'];
	$CI->form->add_group('cm', $credit_memo);

	$CI->form->group('cm')->add_object(
		'textarea', 
		'credit_memo[admin_note]',
		'Примечание к возврату :',
		array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
	);

	$lid = $CI->form->group('cm')->add_object(
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
	$CI->form->group('cm')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);

	$order['order'] = $order_data['order'];
	$CI->form->add_group('i_d', $order);
	$lid = $CI->form->group('i_d')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Информация о заказе'
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[orders_status_name]',
		'Статус заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
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
		'order[total_qty]',
		'Сумарное количество единиц :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('i_d')->add_object_to($lid,
		'text',
		'order[subtotal]',
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
		'order[total]',
		'Сумма :',
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
	
	$address['addresses'] = $order_data['addresses'];
	$CI->form->add_group('a_b', $address);
	
	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Адрес плательщика',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text',
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[B][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);

	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'addresses[S][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')+$input_disabled
		)
	);
	
	$CI->form->add_block_to_tab('cm', 'cm');
	$CI->form->add_block_to_tab('cm', 'i_d');
	$CI->form->add_block_to_tab('a_b', 'a_b');
	
	if($order_data['invoice'] !== FALSE)
	{
		$invoice['invoice'] = $order_data['invoice'];
		$CI->form->add_tab('i_b', 'Инвойс');
		$CI->form->add_group('i_b', $invoice);
		
		$lid = $CI->form->group('i_b')->add_object(
			'fieldset',
			'order_invoice_fieldset',
			'Данные инвойса'
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[invoices_number]',
			'Номер инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[invoices_status_name]',
			'Статус инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'textarea',
			'invoice[note]',
			'Сообщение плательщику :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'textarea',
			'invoice[admin_note]',
			'Примечание к инвойсу :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('i_b')->add_object_to($lid,
			'text',
			'invoice[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('i_b', 'i_b');
	}
	
	if($order_data['shipping'] !== FALSE)
	{
		$shipping['shipping'] = $order_data['shipping'];
		$CI->form->add_tab('s_b', 'Отправка');
		$CI->form->add_group('s_b', $shipping);
		
		$lid = $CI->form->group('s_b')->add_object(
			'fieldset',
			'order_$shipping_fieldset',
			'Данные отправки'
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[shippings_number]',
			'Номер отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[shippings_status_name]',
			'Статус отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'textarea',
			'shipping[note]',
			'Сообщение получателю :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'textarea',
			'shipping[admin_note]',
			'Примечание к отправке :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('s_b')->add_object_to($lid,
			'text',
			'shipping[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('s_b', 's_b');
	}
	
	$CI->form->render_form();
}