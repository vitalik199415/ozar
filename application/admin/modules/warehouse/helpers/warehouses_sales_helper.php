<?php
function helper_wh_sales_grid_build(Grid $grid)
{
	$CI = & get_instance();
	$CI->load->model('warehouse/mwarehouses');
	$wh_array = $CI->mwarehouses->get_wh_to_select();

	$grid->add_button('Возвраты', set_url('*/warehouses_credit_memo'),
		array(
			'rel' => 'add',
			'class' => 'addButton',
		));
	$grid->add_button('Отправки', set_url('*/warehouses_shippings'),
		array(
			'rel' => 'add',
			'class' => 'addButton',
		));
	$grid->add_button('Инвойсы', set_url('*/warehouses_invoices'),
		array(
			'rel' => 'add',
			'class' => 'addButton',
		));
	$grid->add_button('Создать продажу', set_url('*/warehouses_sales/prepare_add_sale'),
		array(
			'rel' => 'add',
			'class' => 'addButton',
		));


	$grid->add_column(
		 array(
			 'index'	=> 'wh_sale_number',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '7%'
		 ), 'Продажа');
	$grid->add_column(
		 array(
			 'index'	=> 'orders_number',
			 'type'		=> 'text',
			 'searchtable'=> 'ORD',
			 'tdwidth'	=> '7%',
			 'filter'	=> true
		 ), 'Заказ');
	$grid->add_column(
		 array(
			 'index'	=> 'name',
			 'searchtable'	=> 'ADDR',
			 'type'		=> 'text',
			 'tdwidth'	=> '12%',
			 'filter' 	=> true
		 ), 'Покупатель');
	$grid->add_column(
		 array(
			 'index'	=> 'total_qty',
			 'type'		=> 'text',
			 'tdwidth'	=> '4%'
		 ), 'К-во');
	$grid->add_column(
		 array(
			 'index'	=> 'total',
			 'type'		=> 'text',
			 'tdwidth'	=> '8%'
		 ), 'Сумма');
	$grid->add_column(
		 array(
			 'index'	=> 'comment',
			 'type'		=> 'text'
		 ), 'Комментарий');
	$grid->add_column(
		 array(
			 'index'		=> 'wh_alias',
			 'type'		 	=> 'select',
			 'searchtable'	=> 'C',
			 'searchname' 	=> 'id_wh',
			 'options'	 	=> array('' => '') + $wh_array,
			 'filter'		=> true,
			 'tdwidth'	=> '10%'
		 ), 'Склад');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_sale_state',
			 'type'		=> 'text',
			 'tdwidth'	=> '10%'
		 ), 'Статус продажи');
	$grid->add_column(
		 array(
			 'index'	=> 'create_date',
			 'searchtable'	=> 'A',
			 'type'		=> 'date',
			 'tdwidth'	=> '12%',
			 'filter'	=> true
		 ), 'Дата создания');
	$grid->add_column(
		 array(
			 'index'	=> 'action',
			 'type'		=> 'action',
			 'tdwidth'	=> '9%',
			 'option_string' => 'align="center"',
			 'actions'	 => array(
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('*', 'warehouses_sales', 'view_sale', 'wh_id', '$2', 'sale_id', '$1')),
					 'href_values' 	=> array('ID', 'WH_ID'),
					 'options'		=> array('class'=>'icon_view', 'title'=>'Просмотр')
				 )
			 )
		 ), 'Действия');
}

function helper_sales_wh_shop_products_grid_build(Grid $grid, $wh_id, $sale_id = 0)
{
	$grid->add_column(
		 array(
			 'index'		=> 'sku',
			 'searchtable'	=> 'A',
			 'type'		 	=> 'text',
			 'tdwidth'	 	=> '9%',
			 'filter'	 	=> true
		 ), 'Артикул');
	$grid->add_column(
		 array(
			 'index'		=> 'name',
			 'searchtable'	=> 'B',
			 'type'			=> 'text',
			 'filter'	 	=> true
		 ), 'Название');
	$grid->add_column(
		 array(
			 'index'		=> 'qty',
			 'type'			=> 'text',
			 'tdwidth'	=> '5%',
		 ), 'К-во');
	$grid->add_column(
		 array(
			 'index'	=> 'create_date',
			 'type'		=> 'date',
			 'tdwidth'	=> '12%',
			 'sortable' => true,
			 'filter'	=> true
		 ), 'Создан');
	$grid->add_column(
		 array(
			 'index'	=> 'status',
			 'type'		=> 'select',
			 'options'	=> array('' => '', '0' => 'Нет', '1' => 'Да'),
			 'tdwidth'	=> '9%',
			 'filter'	=> true
		 ), 'В поиске');

	$grid->add_column(
		 array(
			 'index'		=> 'action',
			 'type'		 	=> 'action',
			 'tdwidth'	 	=> '10%',
			 'option_string'=> 'align="center"',
			 'sortable' 	=> false,
			 'filter'	 	=> false,
			 'actions'	 	=> array(
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('warehouse','warehouses_sales','ajax_get_view_wh_shop_product', 'wh_id', $wh_id, 'sale_id', $sale_id, 'pr_id','$1')),
					 'href_values' 	=> array('ID'),
					 'options'		=> array('class' => 'sale_view_add_product icon_plus', 'title' => 'Подробней')
				 )
			 )
		 ), 'Действие');
}

function helper_not_edited_wh_sale_products_grid_build($wh_id, $sale_id, Nosql_grid $grid)
{
	$grid->add_column(
		 array(
			 'index'	=> 'number',
			 'type'		=> 'text',
			 'tdwidth'	=> '3%'
		 ), ' ');
	$grid->add_column(
		 array(
			 'index'		=> 'sku',
			 'searchtable'	=> 'A',
			 'type'		 	=> 'text',
			 'tdwidth'	 	=> '11%'
		 ), 'Артикул');
	$grid->add_column(
		 array(
			 'index'	=> 'name',
			 'type'		=> 'text',
			 'tdwidth'	=> '22%'
		 ), 'Название');
	$grid->add_column(
		 array(
			 'index'	=> 'price',
			 'type'		=> 'text'
		 ), 'Цена');
	$grid->add_column(
		 array(
			 'index'	=> 'qty_str',
			 'type'		=> 'text',
			 'tdwidth'	=> '5%',
			 'option_string' => 'class="pr_qty"'
		 ), 'К-во');
	$grid->add_column(
		 array(
			 'index'	=> 'total',
			 'type'		=> 'text'
		 ), 'Сумма');
	$grid->add_column(
		 array(
			 'index'	=> 'attributes',
			 'type'		=> 'text',
		 ), 'Атрибуты');

	$grid->add_column(
		 array(
			 'index'	=> 'action',
			 'type'		=> 'action',
			 'tdwidth'	=> '10%',
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

function helper_wh_sale_products_grid_build($wh_id, $sale_id, Nosql_grid $grid)
{
	$c_label = 'Удалить продукты';
	if($sale_id > 0) $c_label = 'Отменить изменения';
	$grid->add_button($c_label, set_url('warehouse/warehouses_sales/ajax_unset_sale_products_temp_data/wh_id/'.$wh_id.'/sale_id/'.$sale_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'unset_products_temp_data'
		));
	$grid->add_button('Добавить Продукт', set_url('warehouse/warehouses_sales/ajax_get_wh_shop_products_grid/wh_id/'.$wh_id.'/sale_id/'.$sale_id),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'add_product_to_sale'
		));

	$grid->add_column(
		 array(
			 'index'	=> 'number',
			 'type'		=> 'text',
			 'tdwidth'	=> '3%'
		 ), ' ');
	$grid->add_column(
		 array(
			 'index'		=> 'sku',
			 'searchtable'	=> 'A',
			 'type'		 	=> 'text',
			 'tdwidth'	 	=> '11%'
		 ), 'Артикул');
	$grid->add_column(
		 array(
			 'index'	=> 'name',
			 'type'		=> 'text',
			 'tdwidth'	=> '22%'
		 ), 'Название');
	$grid->add_column(
		 array(
			 'index'	=> 'price',
			 'type'		=> 'text'
		 ), 'Цена');
	$grid->add_column(
		 array(
			 'index'	=> 'qty_str',
			 'type'		=> 'text',
			 'tdwidth'	=> '5%',
			 'option_string' => 'class="pr_qty"'
		 ), 'К-во');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_qty_str',
			 'type'		=> 'text',
			 'tdwidth'	=> '7%'
		 ), 'Остаток');
	$grid->add_column(
		 array(
			 'index'	=> 'total',
			 'type'		=> 'text'
		 ), 'Сумма');
	$grid->add_column(
		 array(
			 'index'	=> 'attributes',
			 'type'		=> 'text',
		 ), 'Атрибуты');

	$grid->add_column(
		 array(
			 'index'	=> 'action',
			 'type'		=> 'action',
			 'tdwidth'	=> '10%',
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
					 'href' 			=> set_url(array('warehouse/warehouses_sales', 'ajax_get_view_edit_product_qty', 'wh_id', $wh_id, 'sale_id', $sale_id, 'sale_pr_id', '$1')),
					 'href_values' 	=> array('SALES_PR_ID'),
					 'options'		=> array('class'=>'icon_edit edit_product_qty', 'title' => 'Редактировать количество')
				 ),
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('warehouse/warehouses_sales', 'ajax_detele_product_from_cart', 'wh_id', $wh_id, 'sale_id', $sale_id, 'sale_pr_id', '$1')),
					 'href_values' 	=> array('SALES_PR_ID'),
					 'options'		=> array('class' => 'icon_delete delete_product_qty', 'title' => 'Удалить')
				 )
			 )
		 ), 'Действие');
}
function helper_wh_sale_prepare_add($wh_data)
{
	$form_id = 'wh_sale_prepare_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Выбор склада', $form_id, set_url('*/*/add_sale_select_wh'));

	$CI->form->add_button(
		 array(
			 'name' => 'Список продаж',
			 'href' => set_url('*/*')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Далее',
			 'href' => '#',
			 'options' => array(
				 'id' => 'submit'
			 )
		 ));

	$CI->form->add_tab('wh', 'Склад');
	$CI->form->add_group('sale');

	$lid = $CI->form->group('sale')->add_object(
		'fieldset',
		'sale_base_data',
		'Выбор склада'
	);

	$CI->form->group('sale')->add_object_to($lid,
		'select',
		'wh_id',
		'Выберите склад (*):',
		array(
			'options'	=> $wh_data['wh_collection']
		)
	);
	$CI->form->add_block_to_tab('wh', 'sale');
	$CI->form->render_form();
}

function helper_controller_warehouses_products_form_add_sale($wh_id, $sale_data)
{
	$form_id = 'wh_sale_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить продажу', $form_id, set_url('*/warehouses_products/save_sale/wh_id/'.$wh_id));

	$CI->form->add_button(
		 array(
			 'name' => 'Список складов',
			 'href' => set_url('*/warehouses')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Склад '.$sale_data['warehouse_alias'],
			 'href' => set_url('*/warehouses/wh_actions/wh_id/'.$wh_id)
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Продажи '.$sale_data['warehouse_alias'],
			 'href' => set_url('*/warehouses_products/wh_sales_grid/wh_id/'.$wh_id)
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Сохранить',
			 'href' => '#',
			 'options' => array(
				 'id' => 'submit_back'
			 )
		 ));
	helper_wh_sale_form_add($wh_id, $sale_data);
}
function helper_controller_warehouses_sales_form_add_sale($wh_id, $sale_data)
{
	$form_id = 'wh_sale_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить продажу', $form_id, set_url('*/*/save_sale/wh_id/'.$wh_id.'/sale_id/0'));

	$CI->form->add_button(
		 array(
			 'name' => 'Список продаж',
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
	helper_wh_sale_form_add($wh_id, $sale_data);
}

function helper_wh_sale_form_add($wh_id, $sale_data)
{
	$form_id = 'wh_sale_add_form';
	$CI = & get_instance();

	$CI->form->add_tab('sale', 'Продажа');
	$CI->form->add_tab('customer', 'Покупатель');

	$CI->form->add_group('sale');

	/*$lid = $CI->form->group('sale')->add_object(
					'fieldset',
						'order_customer_data',
						'Покупатель'
	);
	$CI->form->group('sale')->add_view_to($lid, 'warehouse/sales/view_customer/sale_customer_block', array('sale_customer' => FALSE, 'wh_id' => $wh_id, 'sale_id' => 0));
*/
	$lid = $CI->form->group('sale')->add_object(
		'fieldset',
		'sale_products_data',
		'Продукты продажи',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'sale_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#wh_sale_products_grid').gbc_show_product();
	</script>
	";
	$CI->form->group('sale')->add_html_to($lid, $sale_data['sale_products_grid'].$init_show_product);

	$lid = $CI->form->group('sale')->add_object(
		'fieldset',
		'sale_base_data',
		'Данные продажи'
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[warehouse_alias]',
		'Склад :',
		array(
			'option'	=> array('readonly' => NULL, 'value' => $sale_data['warehouse_alias']),
			'value' => $sale_data['warehouse_alias']
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL, 'value' => $sale_data['base_currency']['name']),
			'value' => $sale_data['base_currency']['name']
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'select',
		'sale[id_m_c_currency]',
		'Валюта заказа :',
		array(
			'options'	=> $sale_data['users_currency']['currency'],
			'option' => array('value' => $sale_data['users_currency']['default'], 'id' => 'sale_currency'),
			'value' => $sale_data['users_currency']['default']
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option' => array('value' => $sale_data['default_selected_currency']['rate'], 'id' => 'sale_currency_rate')
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[total_qty_string]',
		'Сумарное количество :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('id' => 'sale_discount')
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'textarea',
		'sale[admin_note]',
		'Примечание администратора:',
		array(
			'option' => array('rows' => 3)
		)
	);

	//CUSTOMER
	$CI->form->add_group('customer');

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'sale_address_b_fieldset',
		'Платежный адрес',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][name]',
		'Имя, Фамилия :',
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
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);

	$CI->form->group('customer')->add_html('<div align="center"><a href="#" id="copy_billing_to_shipping">Скопировать платежный адрес в адрес доставки</a></div>');

	$lid = $CI->form->group('customer')->add_object(
					'fieldset',
						'sale_address_s_fieldset',
						'Адрес доставки',
						array(
							'id' => 'customer_address_s_fieldset'
						)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][name]',
		'Имя, Фамилия :',
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
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100')
		)
	);
	$init_add_sale = "var wh_sale_object = $('#".$form_id."').gbc_wh_sales({wh_id : '".$wh_id."'});";
	$CI->form->add_js_code($init_add_sale);

	$CI->form->add_block_to_tab('sale', 'sale');
	$CI->form->add_block_to_tab('customer', 'customer');
	$CI->form->render_form();
}




function helper_controller_warehouses_produtcs_form_view_sale($wh_id, $sale_data)
{
	$form_id = 'wh_sale_view_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Посмотреть продажу', $form_id, '');

	$CI->form->add_button(
		 array(
			 'name' => 'Список складов',
			 'href' => set_url('*/warehouses')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Склад '.$sale_data['sale']['warehouse_alias'],
			 'href' => set_url('*/warehouses/wh_actions/wh_id/'.$wh_id)
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Продажи '.$sale_data['sale']['warehouse_alias'],
			 'href' => set_url('*/warehouses_products/wh_sales_grid/wh_id/'.$wh_id)
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Создать продажу (Склад '.$sale_data['sale']['warehouse_alias'].')',
			 'href' => set_url('*/warehouses_products/create_sale/wh_id/'.$wh_id)
		 ));
	helper_wh_sale_form_view($wh_id, $sale_data);
}

function helper_controller_warehouses_sales_form_view_sale($wh_id, $sale_data)
{
	$form_id = 'wh_sale_view_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Посмотреть продажу', $form_id, set_url('*/*/save_sale'));

	$CI->form->add_button(
		 array(
			 'name' => 'Список продаж',
			 'href' => set_url('*/*')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Создать продажу (Склад '.$sale_data['sale']['warehouse_alias'].')',
			 'href' => set_url('*/warehouses_sales/add_sale/wh_id/'.$wh_id)
		 ));
	helper_wh_sale_form_view($wh_id, $sale_data);
}

function helper_wh_sale_form_view($wh_id, $sale_data)
{
	$form_id = 'wh_sale_view_form';
	$CI = & get_instance();

	$CI->form->add_tab('sale', 'Продажа');
	$CI->form->add_tab('customer', 'Покупатель');

	$sale['sale'] = $sale_data['sale'];
	$CI->form->add_group('sale', $sale);

	$lid = $CI->form->group('sale')->add_object(
		'fieldset',
		'sale_products_data',
		'Продукты продажи',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'sale_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#wh_sale_products_grid').gbc_show_product();
	</script>
	";
	$CI->form->group('sale')->add_html_to($lid, $sale_data['sale_products_grid'].$init_show_product);

	$lid = $CI->form->group('sale')->add_object(
		'fieldset',
		'sale_base_data',
		'Данные продажи'
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[warehouse_alias]',
		'Склад :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[wh_sale_state_name]',
		'Статус продажи :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[wh_sale_number]',
		'Номер продажи :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[base_currency_name]',
		'Базовая валюта :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[currency_name]',
		'Валюта заказа :',
		array(
			'option' => array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[currency_rate]',
		'Курс по отношению к базовой валюте :',
		array(
			'option' => array('readonly' => NULL, 'id' => 'sale_currency_rate')
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[total_qty_string]',
		'Сумарное количество :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[subtotal_string]',
		'Предварительная сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[discount]',
		'Скидка в валюте заказа :',
		array(
			'option'	=> array('id' => 'sale_discount', 'readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[total_string]',
		'Сумма :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[create_date]',
		'Дата создания :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'text',
		'sale[update_date]',
		'Дата обновления :',
		array(
			'option'	=> array('readonly' => NULL)
		)
	);
	$CI->form->group('sale')->add_object_to($lid,
		'textarea',
		'sale[comment]',
		'Примечание администратора:',
		array(
			'option' => array('rows' => 3, 'readonly' => NULL)
		)
	);

	//CUSTOMER
	$addresses['addresses'] = $sale_data['addresses'];
	$CI->form->add_group('customer', $addresses);

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'sale_address_b_fieldset',
		'Платежный адрес',
		array(
			'id' => 'customer_address_b_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[B][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);

	$lid = $CI->form->group('customer')->add_object(
		'fieldset',
		'sale_address_s_fieldset',
		'Адрес доставки',
		array(
			'id' => 'customer_address_s_fieldset'
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][name]',
		'Имя, Фамилия :',
		array(
			'option'	=> array('maxlenght' => '150', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][country]',
		'Страна :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][city]',
		'Город :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][zip]',
		'Индекс :',
		array(
			'option'	=> array('maxlenght' => '5', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address]',
		'Адрес :',
		array(
			'option'	=> array('maxlenght' => '200', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][telephone]',
		'Телефон :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	$CI->form->group('customer')->add_object_to($lid,
		'text',
		'addresses[S][address_email]',
		'E-Mail :',
		array(
			'option'	=> array('maxlenght' => '100', 'readonly' => NULL)
		)
	);
	//$init_add_sale = "var wh_sale_object = $('#".$form_id."').gbc_wh_sales({wh_id : '".$wh_id."'});";
	//$CI->form->add_js_code($init_add_sale);

	$CI->form->add_block_to_tab('sale', 'sale');
	$CI->form->add_block_to_tab('customer', 'customer');
	$CI->form->render_form();
}
?>