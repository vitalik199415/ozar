<?php
function helper_orders_grid_build(Grid $grid)
{
	$grid->add_button('Добавить заказ', set_url('*/*/add'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
	$grid->add_column(
		array(
			'index'		 => 'orders_number',
			'searchname' => 'orders_number',
			'searchtable'=> 'A',
			'type'		 => 'number',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Номер заказа');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'tdwidth'	 => '20%',
			'filter'	 => true
		), 'Имя покупателя');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text'
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
			'index'		 => 'orders_state',
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
					'href' 			=> set_url(array('*','*','view','ord_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view', 'title' => 'Просмотр')
				)
			)
		), 'Actions');
}

function helper_customers_grid_build(Grid $grid, $c_groups, $ord_id)
{
	$grid->add_column(
		 array(
			 'index'		=> 'email',
			 'searchtable'	=> 'A',
			 'type'		 	=> 'text',
			 'filter'	 	=> true
		 ), 'E-Mail');
	$grid->add_column(
		 array(
			 'index'		=> 'name',
			 'searchtable'	=> 'B',
			 'type'		 	=> 'text',
			 'filter'	 	=> true
		 ),'Фамилия, Имя');
	$grid->add_column(
		 array(
			 'index'	=> 'create_date',
			 'type'		=> 'date',
			 'tdwidth'	=> '11%',
			 'sortable' => true,
			 'filter'	=> true
		 ), 'Создан');
	$grid->add_column(
		 array(
			 'index'	=> 'id_m_u_types',
			 'type'		=> 'select',
			 'options'	=> array('' => '') + $c_groups,
			 'tdwidth'	=> '12%',
			 'filter'	=> true
		 ), 'Группы');
	$grid->add_column(
		 array(
			 'index'	 => 'action',
			 'type'		 => 'action',
			 'tdwidth'	 => '10%',
			 'option_string' => 'align="center"',
			 'sortable'  => false,
			 'filter'	 => false,
			 'actions'	 => array(
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url('*/*/ajax_set_order_customer/ord_id/'.$ord_id.'/cm_id/$1'),
					 'href_values' 	=> array('ID'),
					 'options'		=> array('class' => 'icon_plus set_order_customer', 'title' => 'Добавить')
				 )
			 )
		 ), 'Actions');
}

//WH products
function helper_orders_wh_products_grid_build(Nosql_grid $grid, $ord_id = 0)
{
	$c_label = 'Удалить продукты';
	if($ord_id > 0) $c_label = 'Отменить изменения';
	$grid->add_button($c_label, set_url('*/*/ajax_unset_products_temp_data/ord_id/'.$ord_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'unset_products_temp_data'
		));
	$grid->add_button('Добавить Продукт', set_url('*/*/ajax_get_shop_products_grid/ord_id/'.$ord_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'add_product_to_order'
		));

	$grid->add_column(
		array(
			'index'		 => 'number',
			'type'		 => 'text',
			'tdwidth'	 => '3%'
		), ' ');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '11%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'tdwidth'	 => '22%'
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text'
		), 'Цена');
	$grid->add_column(
		array(
			'index'		 => 'qty_str',
			'type'		 => 'text',
			'tdwidth'	 => '5%',
			'option_string' => 'class="pr_qty"'
		), 'К-во');
	$grid->add_column(
		array(
			'index'		 => 'wh_qty_str',
			'type'		 => 'text',
			'tdwidth'	 => '7%'
		), 'Остаток');	
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text'
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'attributes',
			'type'		 => 'text',
		), 'Атрибуты');

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
					'href' 			=> set_url(array('catalogue','products','view', 'id','$1')),
					'href_values' 	=> array('PR_ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title' => 'Просмотр продукта')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','ajax_get_view_edit_product_qty', 'ord_id', $ord_id, 'ord_pr_id', '$1')),
					'href_values' 	=> array('ORD_PR_ID'),
					'options'		=> array('class'=>'icon_edit edit_product_qty', 'title' => 'Редактировать количество')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','ajax_detele_product_from_cart', 'ord_id', $ord_id, 'ord_pr_id', '$1')),
					'href_values' 	=> array('ORD_PR_ID'),
					'options'		=> array('class' => 'icon_delete delete_product_qty', 'title' => 'Удалить')
				)
			)
		), 'Действие');
}
//--WH products

function helper_orders_products_grid_build(Nosql_grid $grid, $ord_id = 0)
{
	$c_label = 'Удалить продукты';
	if($ord_id > 0) $c_label = 'Отменить изменения';
	$grid->add_button($c_label, set_url('*/*/ajax_unset_products_temp_data/ord_id/'.$ord_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'unset_products_temp_data'
		));
	$grid->add_button('Добавить Продукт', set_url('*/*/ajax_get_shop_products_grid/ord_id/'.$ord_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'add_product_to_order'
		));

	$grid->add_column(
		array(
			'index'		 => 'number',
			'type'		 => 'text',
			'tdwidth'	 => '3%'
		), ' ');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '11%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'tdwidth'	 => '22%'
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text'
		), 'Цена');
	$grid->add_column(
		array(
			'index'		 => 'qty_str',
			'type'		 => 'text',
			'tdwidth'	 => '5%',
			'option_string' => 'class="pr_qty"'
		), 'К-во');	
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text'
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'attributes',
			'type'		 => 'text',
		), 'Атрибуты');
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
					 'href' 			=> set_url(array('catalogue','products','view', 'id','$1')),
					 'href_values' 	=> array('PR_ID'),
					 'options'		=> array('class'=>'icon_view show_product_link', 'title' => 'Просмотр продукта')
				 ),
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('*','*','ajax_get_view_edit_product_qty', 'ord_id', $ord_id, 'ord_pr_id', '$1')),
					 'href_values' 	=> array('ORD_PR_ID'),
					 'options'		=> array('class'=>'icon_edit edit_product_qty', 'title' => 'Редактировать количество')
				 ),
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('*','*','ajax_detele_product_from_cart', 'ord_id', $ord_id, 'ord_pr_id', '$1')),
					 'href_values' 	=> array('ORD_PR_ID'),
					 'options'		=> array('class' => 'icon_delete delete_product_qty', 'title' => 'Удалить')
				 )
			 )
		 ), 'Действие');
}

function helper_not_edited_orders_products_grid_build(Nosql_grid $grid)
{
	$grid->add_column(
		array(
			'index'		 => 'number',
			'type'		 => 'text',
			'tdwidth'	 => '3%'
		), ' ');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '11%',
			'filter'	 => FALSE
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'tdwidth'	 => '22%',
			'filter'	 => FALSE
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text'
		), 'Цена');
	$grid->add_column(
		array(
			'index'		 => 'qty_str',
			'type'		 => 'text',
			'tdwidth'	 => '5%',
			'option_string' => 'class="pr_qty"'
		), 'К-во');	
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text'
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'attributes',
			'type'		 => 'text',
		), 'Атрибуты');
	
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
					'href' 			=> set_url(array('catalogue','products','view', 'id','$1')),
					'href_values' 	=> array('PR_ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title' => 'Просмотр продукта')
				)
			)
		), 'Действие');
}

function helper_shop_products_grid_build(Grid $grid, $ord_id = 0)
{
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'searchtable'=> 'B',
			'type'		 => 'text',

			'filter'	 => true
		), 'Название');
	$grid->add_column(
		array
			(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '12%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Создан');
	$grid->add_column(
		array
			(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
	$grid->add_column(
		array
			(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		),'В наличии');
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
					'href' 			=> set_url(array('*','*','ajax_get_view_shop_product', 'ord_id', $ord_id, 'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'order_view_add_product icon_view', 'title' => 'Просмотр')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','ajax_get_view_shop_product', 'ord_id', $ord_id, 'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'order_view_add_product icon_plus', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}

function helper_orders_form_add($order_data)
{
	$form_id = 'orders_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить заказ', $form_id, set_url('*/*/save'));

	$CI->form->add_button(
		array(
			'name' => 'Список заказов',
			'href' => set_url('*/*')
		));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
			)
		));

	$CI->form->add_validation('addresses[B][name]', array('required' => 'true'));
	$CI->form->add_validation('addresses[S][name]', array('required' => 'true'));
	$CI->form->add_validation('addresses[B][address_email]', array('required' => 'true', 'email' => 'true'));
	$CI->form->add_validation('addresses[S][address_email]', array('required' => 'true', 'email' => 'true'));


	$CI->form->add_tab('order', 'Заказ');
	$CI->form->add_tab('customer', 'Адреса');

	$CI->form->add_group('order');

	$lid = $CI->form->group('order')->add_object(
		'fieldset',
		'order_customer_data',
		'Покупатель'
	);
	$CI->form->group('order')->add_view_to($lid, 'sales/orders/view_customer/order_customer_block', array('order_customer' => FALSE, 'ord_id' => 0));

	$lid = $CI->form->group('order')->add_object(
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
	$CI->form->group('order')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);

	$lid = $CI->form->group('order')->add_object(
		'fieldset',
		'order_base_data',
		'Данные заказа'
	);
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[status]',
		'Подтвержден (*):',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	/*$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[orders_state]',
		'Статус заказа (*):',
		array(
			'options'	=> $order_data['orders_states']
		)
	);*/
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL, 'value' => $order_data['base_currency']['name']),
			'value' => $order_data['base_currency']['name']
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[id_m_c_currency]',
		'Валюта заказа :',
		array(
			'options'	=> $order_data['users_currency']['currency'],
			'option' => array('value' => $order_data['users_currency']['default'], 'id' => 'order_currency'),
			'value' => $order_data['users_currency']['default']
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option' => array('value' => $order_data['default_selected_currency']['rate'], 'id' => 'order_currency_rate')
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[total_qty_string]',
		'Сумарное количество :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('id' => 'order_discount')
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	if(count($order_data['users_payment_methods']['payment_methods']) > 0)
	{
		$CI->form->group('order')->add_object_to($lid,
			'select',
			'order[id_m_users_payment_methods]',
			'Метод оплаты (*):',
			array(
				'options'	=> $order_data['users_payment_methods']['payment_methods'],
				'option' => array('value' => $order_data['users_payment_methods']['default']),
				'value' => $order_data['users_payment_methods']['default']
			)
		);
	}
	if(count($order_data['users_shipping_methods']['shipping_methods']) > 0)
	{
		$CI->form->group('order')->add_object_to($lid,
			'select',
			'order[id_m_users_shipping_methods]',
			'Метод доставки (*):',
			array(
				'options'	=> $order_data['users_shipping_methods']['shipping_methods'],
				'option' => array('value' => $order_data['users_shipping_methods']['default']),
				'value' => $order_data['users_shipping_methods']['default']
			)
		);
	}
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[id_langs]',
		'Язык заказа :',
		array(
			'options'	=> $order_data['users_langs']['langs'],
			'option' => array('value' => $order_data['users_langs']['default'])
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'textarea',
		'order[admin_note]',
		'Примечание администратора:',
		array(
			'option' => array('rows' => 3)
		)
	);

	//CUSTOMER
	$CI->form->add_group('customer');

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Платежный адрес',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);

	$CI->form->group('customer')->add_html('<div align="center"><a href="#" id="copy_billing_to_shipping">Скопировать платежный адрес в адрес доставки</a></div>');

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'order_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$init_add_order = "var order_object = $('#".$form_id."').gbc_orders();";
	$CI->form->add_js_code($init_add_order);

	$CI->form->add_block_to_tab('order', 'order');
	$CI->form->add_block_to_tab('customer', 'customer');
	$CI->form->render_form();
}

function helper_orders_form_view($order_data, $ord_id = '')
{
	$form_id = 'orders_view_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Просмотр заказа', $form_id, set_url('*/*/view_save/ord_id/'.$ord_id));
	
	$CI->form->add_button(
		array(
		'name' => 'Список заказов',
		'href' => set_url('*/*'),
	));
	
	if($order_data['order']['orders_state'] == 'COD_S_С')
	{
		$CI->form->add_button(
			array(
			'name' => 'Наложенный платеж оплачен',
			'href' => set_url('*/*/order_COD_paid/ord_id/'.$ord_id)
		));
	}
	if($order_data['order']['orders_state'] != 'CN' && $order_data['order']['orders_state'] != 'COD_S_С' && ($order_data['invoice'] === FALSE || ($order_data['invoice']['invoices_status'] == 'N' || $order_data['invoice']['invoices_status'] == 'P' || $order_data['invoice']['invoices_status'] == 'IS' || $order_data['invoice']['invoices_status'] == 'COD')))
	{
		$CI->form->add_button(
			array(
			'name' => 'Отменить заказ',
			'href' => set_url('*/*/cancel_order/ord_id/'.$ord_id),
			'options' => array(
				'class' => 'action_question'
			)
		));
	}
	if($order_data['invoice'] !== FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Инвойс '.$order_data['invoice']['invoices_number'],
			'href' => set_url('*/invoices/view_invoice/inv_id/'.$order_data['invoice']['id_m_orders_invoices'])
		));
	}
	if(($order_data['order']['orders_state'] != 'CN' && $order_data['invoice'] !== FALSE && ($order_data['invoice']['invoices_status'] == 'C' || $order_data['invoice']['invoices_status'] == 'COD')) && $order_data['shipping'] === FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Создать отправку',
			'href' => set_url('*/shippings/create_shipping/ord_id/'.$ord_id)
		));
	}
	if($order_data['shipping'] !== FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Отправка '.$order_data['shipping']['shippings_number'],
			'href' => set_url('*/shippings/view_shipping/shp_id/'.$order_data['shipping']['id_m_orders_shippings'])
		));
	}
	if($order_data['order']['orders_state'] != 'CN' && $order_data['invoice'] === FALSE)
	{
		$CI->form->add_button(
			array(
			'name' => 'Создать инвойс',
			'href' => set_url('*/invoices/create_invoice/ord_id/'.$ord_id)
		));
	}
	if($order_data['order']['orders_state'] == 'IC' || $order_data['order']['orders_state'] == 'C' || $order_data['order']['orders_state'] == 'COD_S_С')
	{
		$CI->form->add_button(
			array(
			'name' => 'Создать возврат',
			'href' => set_url('*/credit_memo/create_credit_memo/ord_id/'.$ord_id)
		));
	}
	$CI->form->add_button(
		array(
		'name' => 'Печать заказа',
		'href' => set_url('*/*/print_order/ord_id/'.$ord_id),
		'options' => array(
			'target' => '_blank'
		)
	));
	if($order_data['invoice'] === FALSE && ($order_data['order']['orders_state'] == 'N'))
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

	$CI->form->add_validation('addresses[B][name]', array('required' => 'true'));
	$CI->form->add_validation('addresses[S][name]', array('required' => 'true'));
	$CI->form->add_validation('addresses[B][address_email]', array('required' => 'true', 'email' => 'true'));
	$CI->form->add_validation('addresses[S][address_email]', array('required' => 'true', 'email' => 'true'));

	$input_disabled = array();
	$select_disabled = array();
	if($order_data['order']['orders_state'] != 'N')
	{
		$input_disabled = array('readonly' => NULL);
		$select_disabled = array('readonly' => NULL, 'disabled' => NULL);
	}

	$CI->form->add_tab('order', 'Заказ');
	$CI->form->add_tab('customer', 'Адреса');

	$order['order'] = $order_data['order'];
	$CI->form->add_group('order', $order);

	$lid = $CI->form->group('order')->add_object(
		'fieldset',
		'order_customer_data',
		'Покупатель'
	);
	$CI->form->group('order')->add_view_to($lid, 'sales/orders/view_customer/order_customer_block', array('order_customer' => $order_data['customer'], 'ord_id' => $ord_id));

	$CI->form->group('order')->add_html('<div align="center"><a href="'.set_url('*/*/ajax_show_products_with_photo/ord_id/'.$ord_id).'" id="show_products_with_photo" style="font-size:18px;">Продукты с фото</a></div>');

	$lid = $CI->form->group('order')->add_object(
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
	$CI->form->group('order')->add_html_to($lid, $order_data['order_products_grid'].$init_show_product);

	$lid = $CI->form->group('order')->add_object(
		'fieldset',
		'order_base_data',
		'Данные заказа'
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[orders_number]',
		'Номер заказа :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[status]',
		'Подтвержден (*):',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет'),
			'option'	=> $select_disabled
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[orders_state]',
		'Статус заказа (*):',
		array(
			'options'	=> $order_data['orders_states'],
			'option'	=> array('readonly' => NULL, 'disabled' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL),
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[id_m_c_currency]',
		'Валюта заказа :',
		array(
			'options'	=> $order_data['users_currency']['currency'],
			'option' => array('value' => $order_data['users_currency']['default'], 'id' => 'order_currency') + $select_disabled,
			'value' => $order_data['users_currency']['default']
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option' => array('id' => 'order_currency_rate') + $input_disabled
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[total_qty_string]',
		'Сумарное количество :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('id' => 'order_discount') + $input_disabled
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'text',
		'order[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	if(count($order_data['users_payment_methods']['payment_methods']) > 0)
	{
		$CI->form->group('order')->add_object_to($lid,
			'select',
			'order[id_m_users_payment_methods]',
			'Метод оплаты (*):',
			array(
				'options'	=> $order_data['users_payment_methods']['payment_methods'],
				'option' => array('value' => $order_data['users_payment_methods']['default']) + $select_disabled,
				'value' => $order_data['users_payment_methods']['default']
			)
		);
	}
	if(count($order_data['users_shipping_methods']['shipping_methods']) > 0)
	{
		$CI->form->group('order')->add_object_to($lid,
			'select',
			'order[id_m_users_shipping_methods]',
			'Метод доставки (*):',
			array(
				'options'	=> $order_data['users_shipping_methods']['shipping_methods'],
				'option' => array('value' => $order_data['users_shipping_methods']['default']) + $select_disabled,
				'value' => $order_data['users_shipping_methods']['default']
			)
		);
	}
	$CI->form->group('order')->add_object_to($lid,
		'select',
		'order[id_langs]',
		'Язык заказа :',
		array(
			'options'	=> $order_data['users_langs']['langs'],
			'option' => array('value' => $order_data['users_langs']['default']) + array('readonly' => NULL, 'disabled' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'textarea',
		'order[note]',
		'Примечание Покупателя:',
		array(
			'option' => array('rows' => 3, 'readonly' => NULL)
		)
	);
	$CI->form->group('order')->add_object_to($lid,
		'textarea',
		'order[admin_note]',
		'Примечание администратора:',
		array(
			'option' => array('rows' => 3)
		)
	);


	//CUSTOMER
	$order_address['addresses'] = $order_data['addresses'];
	$CI->form->add_group('customer', $order_address);

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Платежный адрес',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'order_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);

	$init_add_order = "var order_object = $('#".$form_id."').gbc_orders({order_id : ".$ord_id."});";
	$CI->form->add_js_code($init_add_order);

	$CI->form->add_block_to_tab('order', 'order');
	$CI->form->add_block_to_tab('customer', 'customer');
	
	if($order_data['invoice'] !== FALSE)
	{
		$invoice['invoice'] = $order_data['invoice'];
		$CI->form->add_tab('invoice', 'Инвойс');
		$CI->form->add_group('invoice', $invoice);
		
		$lid = $CI->form->group('invoice')->add_object(
			'fieldset',
			'order_invoice_fieldset',
			'Данные инвойса'
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'text',
			'invoice[invoices_number]',
			'Номер инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'text',
			'invoice[invoices_status_name]',
			'Статус инвойса :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'textarea',
			'invoice[note]',
			'Сообщение плательщику :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'textarea',
			'invoice[admin_note]',
			'Примечание к инвойсу :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'text',
			'invoice[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('invoice')->add_object_to($lid,
			'text',
			'invoice[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('invoice', 'invoice');
	}
	
	if($order_data['shipping'] !== FALSE)
	{
		$shipping['shipping'] = $order_data['shipping'];
		$CI->form->add_tab('shipping', 'Отправка');
		$CI->form->add_group('shipping', $shipping);
		
		$lid = $CI->form->group('shipping')->add_object(
			'fieldset',
			'order_$shipping_fieldset',
			'Данные отправки'
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'text',
			'shipping[shippings_number]',
			'Номер отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'text',
			'shipping[shippings_status_name]',
			'Статус отправки :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'textarea',
			'shipping[note]',
			'Сообщение получателю :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'textarea',
			'shipping[admin_note]',
			'Примечание к отправке :',
			array(
			'option'	=> array('rows' => 3, 'readonly' => NULL)
		)
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'text',
			'shipping[create_date]',
			'Дата создания:',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->group('shipping')->add_object_to($lid,
			'text',
			'shipping[update_date]',
			'Дата последнего обновления :',
			array(
				'option'	=> array('readonly' => NULL)
			)
		);
		$CI->form->add_block_to_tab('shipping', 'shipping');
	}
	
	$CI->form->render_form();
}

function helper_orders_form_build($data, $save_param = '')
{
	$form_id = 'orders_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление | Редактирование заказа', $form_id, set_url('*/*/save'.$save_param));
	
	$CI->form->add_button(
		array(
		'name' => 'Назад',
		'href' => set_url('*/*/'),
		'options' => array( ),
	));
	$CI->form->add_button(
		array(
		'name' => 'Сохранить и продолжить редактирование',
		'href' => '#',
		'options' => array(
			'id' => 'submit_back',
			'class' => 'addButton'
		)
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
	
	$CI->form->add_tab('m_b'	, 'Основная информация');
	$CI->form->add_tab('a_b'	, 'Покупатель');
	$CI->form->add_tab('p_b'	, 'Продукты заказа');
	
	if(!isset($data['order'])) $data['order'] = FALSE;
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$data['order']['order'] = $session_temp['order'];
	}
	$CI->form->add_group('m_b', $data['order']);
	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Статус заказа'
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'order[state]',
		'Статус заказа (*):',
		array(
			'options'	=> helper_get_order_statuses()
		)
	);
	
	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Оплата и доставка'
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'select', 
		'order[payment_methods]',
		'Метод оплаты (*):',
		array(
			'options'	=> $data['payment_methods']
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'select', 
		'order[shipping_methods]',
		'Метод доставки (*):',
		array(
			'options'	=> $data['shipping_methods']
		)
	);

	if(!isset($data['order_address'])) $data['order_address'] = FALSE;
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$data['order_address']['order_address'] = $session_temp['order_address'];
	}
	
	$CI->form->add_group('a_b', $data['order_address']);
	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'customers_b_fieldset',
		'Зарегистрированые покупатели',
		array(
			'id' => 'customers_b_fieldset'
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'select',
		'order_customers[id_m_u_customers]',
		'Выбор покупателя',
		array(
			'options' => array('0' => 'Выбор покупателя') + $data['customers'],
			'option' => array('id' => 'customers', 'rel' => set_url('*/*/get_customers_addresses'))
		)	
	);
	
	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_b_fieldset',
		'Платежный адрес',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	/*$CI->form->group('a_b')->add_object(
		'hidden',
		'order_address[B][id_m_u_customers_address]'
	);*/
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][country]',
		'Страна (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][city]',
		'Город (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][address]',
		'Адрес (*):',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[B][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
		
		if($save_param == '')
		{
			$CI->form->group('a_b')->add_object(
				'checkbox', 
				'same_as_billing',
				'Адрес доставки совпадает с платежным адресом :',
				array(
					'value'	=> 0,
					'option' => array('id' => 'same_as_billing_checkbox')
				)
			);
		}
		
	$lid = $CI->form->group('a_b')->add_object(
		'fieldset',
		'order_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	/*$CI->form->group('a_b')->add_object(
		'hidden', 
		'order_address[S][id_m_u_customers_address]'
	);*/
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][name]',
		'Имя, Фамилия (*):',
		array(
			'option'	=> array('maxlenght' => '150')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][country]',
		'Страна (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][city]',
		'Город (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][address]',
		'Адрес (*):',
		array(
			'option'	=> array('maxlenght' => '200')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][fax]',
		'Факс :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$CI->form->group('a_b')->add_object_to($lid,
		'text', 
		'order_address[S][address_email]',
		'E-Mail (*):',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	
	
	//Блок выбора продукта
	if(!isset($data['products'])) $data['products'] = FALSE;
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$data['products']['products'] = $session_temp['products'];
	}
	
	$CI->form->add_group('p_b', $data['products']);
		$lid = $CI->form->group('p_b')->add_object(
			'fieldset',
			'selected_prod_fieldset',
			'Валюта заказа :'
		);
		$CI->form->group('p_b')->add_object_to($lid,
			'select',
			'products[currency]',
			'Валюта(*) :',
			array(
				'options' => $data['currency'],
				'option' => array('id' => 'orders_currency')
			));
		$CI->form->group('p_b')->add_object(
			'html',
			'<div style="padding:5px 0 10px 0;" align="center" id="cart">
			'.$data['cart'].'
			</div>'
		);
		$CI->form->group('p_b')->add_object(
			'html',
			'<div style="padding:5px 0 20px 0;" class="def_buttons" align="center">
				<a href="'.set_url('*/*/get_products_grid').'" id="orders_add_product">Добавить продукты</a>
			</div>'
		);
	
	/*$CI->form->add_html_code('
	<div class="JQ_tools_overlay" id="orders_products_grid_overlay">
		<div id="content">
	
		</div>
	</div>
	<div class="JQ_tools_overlay" id="orders_products_view_overlay">
		<div id="content">
	
		</div>
	</div>
	');*/
	
	//------------Блок выбора продукта----------------
	//$Form->addHtmlCode("<div align='center' class='grid_block' id='orders_products_grid'></div>");
	$CI->form->add_js_code("$('#".$form_id."').gbc_orders_add();");
	
	$CI->form->add_block_to_tab('m_b', 'm_b');
	$CI->form->add_block_to_tab('a_b', 'a_b');
	$CI->form->add_block_to_tab('p_b', 'p_b');
	
	$CI->form->render_form();
}

function helper_products_grid_build($grid)
{
	$grid->add_column(
		array(
			'index'		 => 'ID',
			'searchname' => 'id_m_c_products',
			'searchtable'=> 'A',
			'type'		 => 'number',
			'tdwidth'	 => '8%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'ID');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '13%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'sortable' 	 => FALSE,
			'filter'	 => true
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'Включен');
	$grid->add_column(
		array(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'В наличие');
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
					'href' 			=> set_url(array('*','*','show_product_to_cart','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_plus JQ_products_view', 'title'=>'Добавить к заказу')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view JQ_products_view', 'title'=>'Просмотр продукта')
				)
			)
		), 'Actions');
}
function helper_products_view_to_cart($data)
{
	$CI = & get_instance();
	$CI->template->add_template('sales/orders/orders_products_view_to_cart', $data, 'view_prod');
	$CI->template->is_ajax(TRUE);
}
?>