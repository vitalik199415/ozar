<?php
function helper_wh_transfers_grid_build(Grid $grid)
{
	$CI = & get_instance();
	$CI->load->model('warehouse/mwarehouses');
	$wh_array = $CI->mwarehouses->get_wh_to_select();

	$grid->add_button('Создать перенос', set_url('*/warehouses_transfers/prepare_add_transfer'),
		array(
			'rel' => 'add',
			'class' => 'addButton',
		));

	$grid->add_column(
		array(
			'index'	=> 'wh_transfer_number',
			'type'		=> 'text',
			'filter'	=> true,
			'tdwidth'	=> '7%'
		), 'Номер');

	$grid->add_column(
		array(
			'index'	=> 'total_qty',
			'type'		=> 'text',
			'tdwidth'	=> '4%'
		), 'К-во');

	$grid->add_column(
		array(
			'index'	=> 'admin_note',
			'type'		=> 'text'
		), 'Комментарий');

	$grid->add_column(
		array(
			'index'		=> 'wh_from_alias',
			'type'		 	=> 'select',
			'searchtable'	=> 'A',
			'searchname' 	=> 'id_wh_from',
			'options'	 	=> array('' => '') + $wh_array,
			'filter'		=> true,
			'tdwidth'	=> '9%'
		), 'Со склада');

	$grid->add_column(
		 array(
			 'index'		=> 'wh_to_alias',
			 'type'		 	=> 'select',
			 'searchtable'	=> 'A',
			 'searchname' 	=> 'id_wh_to',
			 'options'	 	=> array('' => '') + $wh_array,
			 'filter'		=> true,
			 'tdwidth'	=> '9%'
		 ), 'На склад');

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
			'href' 			=> set_url(array('*', 'warehouses_transfers', 'view_transfer', 'tr_id', '$1')),
			'href_values' 	=> array('ID'),
			'options'		=> array('class'=>'icon_view', 'title'=>'Просмотр')
			)
		)
	), 'Действия');
}

function helper_wh_transfer_prepare_add($data)
{
	$form_id = 'wh_transfer_prepare_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Выбор склада', $form_id, set_url('*/warehouses_transfers/add_tranfer_select_wh'));

	$CI->form->add_button(
		 array(
			 'name' => 'Список переносов',
			 'href' => set_url('*/warehouses_transfers')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Далее',
			 'href' => '#',
			 'options' => array(
				 'id' => 'submit'
			 )
		 ));

	$CI->form->add_tab('wh', 'Выбор склада');
	$CI->form->add_group('transfer');

	$lid = $CI->form->group('transfer')->add_object(
		'fieldset',
		'sale_base_data',
		'Выбор склада'
	);

	$CI->form->group('transfer')->add_object_to($lid,
		'select',
		'wh_id_from',
		'Перенос со склада (*):',
		array(
			'options'	=> $data['wh_collection']
		)
	);
	$CI->form->group('transfer')->add_object_to($lid,
		'select',
		'wh_id_to',
		'Перенос на склад (*):',
		array(
			'options'	=> $data['wh_collection']
		)
	);
	$CI->form->add_block_to_tab('wh', 'transfer');
	$CI->form->render_form();
}

function helper_controller_warehouses_transfers_form_add_sale($wh_id_from, $wh_id_to, $transfer_data)
{
	$form_id = 'wh_transfer_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создать перенос продукта', $form_id, set_url('*/warehouses_transfers/save_transfer/wh_id_from/'.$wh_id_from.'/wh_id_to/'.$wh_id_to));

	$CI->form->add_button(
		 array(
			 'name' => 'Список переносов',
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
	helper_wh_transfer_form_add($wh_id_from, $wh_id_to, $transfer_data);
}

function helper_wh_transfer_form_add($wh_id_from, $wh_id_to, $transfer_data)
{
	$form_id = 'wh_transfer_add_form';
	$CI = & get_instance();

	$CI->form->add_tab('transfer', 'Продажа');

	$CI->form->add_group('transfer');

	$CI->form->group('transfer')->add_object(
		'textarea',
		'transfer[admin_note]',
		'Примечание администратора :',
		array(
			'option' => array('rows' => 3)
		)
	);
	$CI->form->group('transfer')->add_object(
		 'hidden',
		 'transfer[wh_id_from]',
		 '',
		 array(
			'value' => $wh_id_from,
			'option' => array('value' => $wh_id_from)
		 )
	);
	$CI->form->group('transfer')->add_object(
		 'hidden',
		 'transfer[wh_id_to]',
		 '',
		 array(
		 	'value' => $wh_id_to,
			'option' => array('value' => $wh_id_to)
		 )
	);
	$lid = $CI->form->group('transfer')->add_object(
		'fieldset',
		'sale_products_data',
		'Продукты продажи',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'transfer_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#wh_transfer_products_grid').gbc_show_product();
	</script>
	";
	$CI->form->group('transfer')->add_html_to($lid, $transfer_data['transfer_products_grid'].$init_show_product);

	$init_add_sale = "$('#".$form_id."').gbc_wh_transfers({wh_id_from : '".$wh_id_from."', wh_id_to : '".$wh_id_to."'});";

	$CI->form->add_js_code($init_add_sale);

	$CI->form->add_block_to_tab('transfer', 'transfer');
	$CI->form->render_form();
}

function helper_transfers_wh_shop_products_grid_build(Grid $grid, $wh_id_from)
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
					 'href' 			=> set_url(array('warehouse','warehouses_transfers','ajax_get_view_wh_shop_product', 'wh_id_from', $wh_id_from, 'pr_id','$1')),
					 'href_values' 	=> array('ID'),
					 'options'		=> array('class' => 'transfer_view_add_product icon_plus', 'title' => 'Подробней')
				 )
			 )
		 ), 'Действие');
}

function helper_wh_transfer_products_grid_build(Nosql_grid $grid, $wh_id_from)
{
	$c_label = 'Удалить продукты';
	$grid->add_button($c_label, set_url('warehouse/warehouses_transfers/ajax_unset_products_temp_data/wh_id_from/'.$wh_id_from),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'unset_products_temp_data'
		));
	$grid->add_button('Добавить Продукт', set_url('warehouse/warehouses_transfers/ajax_get_wh_shop_products_grid/wh_id_from/'.$wh_id_from),
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'add_product_to_transfer'
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
			 'type'		=> 'text'
		 ), 'Название');
	$grid->add_column(
		 array(
			 'index'	=> 'qty',
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
					 'href' 			=> set_url(array('warehouse/warehouses_transfers', 'ajax_get_view_edit_product_qty', 'wh_id_from', $wh_id_from, 'tr_pr_id', '$1')),
					 'href_values' 	=> array('TR_PR_ID'),
					 'options'		=> array('class'=>'icon_edit edit_product_qty', 'title' => 'Редактировать количество')
				 ),
				 array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('warehouse/warehouses_transfers', 'ajax_detele_product_from_cart', 'wh_id_from', $wh_id_from, 'tr_pr_id', '$1')),
					 'href_values' 	=> array('TR_PR_ID'),
					 'options'		=> array('class' => 'icon_delete delete_product_qty', 'title' => 'Удалить')
				 )
			 )
		 ), 'Действие');
}

function helper_not_edited_wh_transfer_products_grid_build(Nosql_grid $grid)
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
			 'type'		=> 'text'
		 ), 'Название');
	$grid->add_column(
		 array(
			 'index'	=> 'qty',
			 'type'		=> 'text',
			 'tdwidth'	=> '5%',
			 'option_string' => 'class="pr_qty"'
		 ), 'К-во');
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

function helper_controller_warehouses_transfers_form_view_transfer($data)
{
	$form_id = 'wh_transfer_view_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Перенос продукта', $form_id, '');

	$CI->form->add_button(
		 array(
			 'name' => 'Список переносов',
			 'href' => set_url('*/*')
		 ));
	$CI->form->add_button(
		 array(
			 'name' => 'Создать перенос',
			 'href' => set_url('*/*/prepare_add_transfer')
		 ));
	helper_wh_transfer_form_view($data);
}

function helper_wh_transfer_form_view($transfer_data)
{
	$form_id = 'wh_transfer_view_form';
	$CI = & get_instance();

	$CI->form->add_tab('transfer', 'Продажа');

	$tr_data['transfer'] = $transfer_data['transfer'];
	$CI->form->add_group('transfer', $tr_data);

	$CI->form->group('transfer')->add_object(
		 'textarea',
		 'transfer[admin_note]',
		 'Примечание администратора :',
		 array(
			 'option' => array('rows' => 3)
		 )
	);
	$CI->form->group('transfer')->add_object(
		 'text',
		 'transfer[warehouse_from_alias]',
		 'Перенос с :',
		 array(
			 'option' => array('readonly' => NULL)
		 )
	);
	$CI->form->group('transfer')->add_object(
		'text',
		'transfer[warehouse_to_alias]',
		'Перенос в :',
		array(
			'option' => array('readonly' => NULL)
		)
	);

	$lid = $CI->form->group('transfer')->add_object(
		'fieldset',
		'sale_products_data',
		'Продукты продажи',
		array(
			'style' => 'background-color:#CCCCCC;',
			'id' => 'transfer_products_data'
		)
	);
	$init_show_product = "
	<script>
		$('#".$form_id."').find('#wh_transfer_products_grid').gbc_show_product();
	</script>
	";
	$CI->form->group('transfer')->add_html_to($lid, $transfer_data['transfer_products_grid'].$init_show_product);

	$CI->form->add_block_to_tab('transfer', 'transfer');
	$CI->form->render_form();
}

?>