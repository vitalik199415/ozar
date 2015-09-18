<?php
function helper_warehouses_all_pr_grid_build($grid, $wh_array)
{
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text'
		), 'Нозвание');
	$grid->add_column(
		array(
			'index'		 => 'WH_QTY',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('0' => '') + $wh_array,
			'tdwidth'	 => '20%',
			'filter'	 => true
		), 'Склад и количество');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '11%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
			array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('PR_ID'),
					'options'		=> array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
				)
			)
		), 'Действие');
}

function helper_wh_products_grid_build(Grid $grid, $wh_id)
{
	$grid->add_button('Печать продуктов склада', set_url('*/warehouses_products/ajax_print_wh_pr/wh_id/'.$wh_id), 
		array(
			'rel' => 'add',
			'id' => 'print_wh_pr'
		));
	$grid->add_button('Добавить существующий продукт', set_url('*/warehouses_products/ajax_get_not_exists_pr/wh_id/'.$wh_id), 
		array(
			'rel' => 'add',
			'id' => 'add_exist_pr'
		));
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
			'filter'	 => true,
			'sortable' 	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'searchtable'=> 'B',
			'type'		 => 'text',
			'filter'	 => true,
			'sortable' 	 => true
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'searchtable'=> 'C',
			'type'		 => 'number',
			'tdwidth'	 => '10%',
			'filter'	 => true,
			'sortable' 	 => true
		), 'К-во');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '14%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
			array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
				),
			array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('warehouse','warehouses_products','add_pr_qty','wh_id',$wh_id,'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_plus', 'title'=>'Добавить количество')
				),
			array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('warehouse','warehouses_products','reject_pr_qty','wh_id',$wh_id,'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_minus', 'title'=>'Списать товар')
				),
			array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('warehouse','warehouses_products','delete_pr','wh_id',$wh_id,'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_delete delete_question', 'title'=>'Удалить товар')
				)
			)
		), 'Действие');
}

function helper_not_in_wh_pr_grid_build(Grid $grid, $wh_id)
{
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
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
		array(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
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
					'href' 			=> set_url(array('warehouse', 'warehouses_products', 'add_exist_pr', 'wh_id', $wh_id, 'pr_id', '$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_plus', 'title' => 'Добавить выбраный продукт')
				)
			)
		), 'Действие');
}

function helper_wh_exist_pr_form_build($data = array())
{
	$form_id = Mwarehouses_products::PR_ADDEDIT_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление продукта', $form_id, set_url('*/*/save_exist_pr/wh_id/'.$data['data_wh'][Mwarehouses_products::ID_WH].'/pr_id/'.$data['data_pr'][Mwarehouses_products::ID_PR]));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('/warehouse/warehouses/wh_actions/wh_id/'.$data['data_wh'][Mwarehouses_products::ID_WH])
	));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	
	$CI->form->add_tab('main_block'			, 'Основные атрибуты');
	
	$CI->form->add_inputmask('warehouse['.$data['data_wh'][Mwarehouses_products::ID_WH].'][qty]', 'integer', 'allowMinus: false, rightAlignNumerics : false');
	
	//Main Product Data Block
	$CI->form->add_group('main_block');
	$CI->form->group('main_block')->add_object(
		'text', 
		'product[sku]',
		'Артикул продукта(SKU) (*):',
		array(
			'option'	=> array('maxlength' => '50', 'readonly' => NULL, 'value' => $data['data_pr']['sku'])
		)
	);
	
	$edit_data = FALSE;
	if(isset($data['warehouse'])) $edit_data['warehouse'] = $data['warehouse'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse'] = @$session_temp['warehouse'];
	}
	$CI->form->add_group('warehouse_block');
	$lid = $CI->form->group('warehouse_block')->add_object(
		'fieldset',
		'warehouse_fieldset',
		'Склад '.$data['data_wh']['alias']
	);
	$CI->form->group('warehouse_block')->add_object_to($lid,
		'text', 
		'warehouse['.$data['data_wh'][Mwarehouses_products::ID_WH].'][qty]',
		'Количество продукта (Склад <b>'.$data['data_wh']['alias'].'</b>) (*):',
		array(
			'option'	=> array('value' => 0, 'maxlength' => '7')
		)
	);
	
	$CI->form->add_block_to_tab('main_block'		, 'main_block');
	$CI->form->add_block_to_tab('main_block'		, 'warehouse_block');
	
	$CI->form->render_form();
}

function helper_add_pr_qty_form_build($data = array(), $wh_id, $pr_id)
{
	$form_id = Mwarehouses_products::ADD_PR_QTY_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление количества продукта', $form_id, set_url('*/*/save_add_pr_qty/wh_id/'.$wh_id.'/pr_id/'.$pr_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('/warehouse/warehouses/wh_actions/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	$CI->form->add_validation('warehouse_product[add_qty]', array('required' => 'true', 'min' => 1));
	$CI->form->add_inputmask('warehouse_product[add_qty]', 'integer', 'allowMinus: false, rightAlignNumerics : false');
	
	$CI->form->add_tab('main_block'			, 'Основные атрибуты');
	
	$edit_data = FALSE;
	if(isset($data['warehouse_product'])) $edit_data['warehouse_product'] = $data['warehouse_product'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse_product'] = @$session_temp['warehouse_product'];
	}
	$CI->form->add_group('main_block', $edit_data);
	$CI->form->group('main_block')->add_object(
		'text', 
		'warehouse_product[sku]',
		'Артикул продукта(SKU) (*):',
		array(
			'option'	=> array('maxlength' => '50', 'readonly' => NULL)
		)
	);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'warehouse_fieldset',
		'Склад'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text', 
		'warehouse_product[qty]',
		'Текущее количество продукта (*):',
		array(
			'option'	=> array('value' => 0, 'maxlength' => '7', 'readonly' => NULL)
		)
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text', 
		'warehouse_product[add_qty]',
		'Добавить количество продукта (*):',
		array(
			'option'	=> array('value' => 1, 'maxlength' => '7')
		)
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	
	$CI->form->render_form();
}

function helper_reject_pr_qty_form_build($data = array(), $wh_id, $pr_id)
{
	$form_id = Mwarehouses_products::ADD_PR_QTY_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Списание количества продукта', $form_id, set_url('*/*/save_reject_pr_qty/wh_id/'.$wh_id.'/pr_id/'.$pr_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('/warehouse/warehouses/wh_actions/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	$CI->form->add_validation('warehouse_product[reject_qty]', array('required' => 'true', 'min' => 1));
	$CI->form->add_inputmask('warehouse_product[reject_qty]', 'integer', 'allowMinus: false, rightAlignNumerics : false');
	
	$CI->form->add_tab('main_block'			, 'Основные атрибуты');
	
	$edit_data = FALSE;
	if(isset($data['warehouse_product'])) $edit_data['warehouse_product'] = $data['warehouse_product'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse_product'] = @$session_temp['warehouse_product'];
	}
	$CI->form->add_group('main_block', $edit_data);
	$CI->form->group('main_block')->add_object(
		'text', 
		'warehouse_product[sku]',
		'Артикул продукта(SKU) (*):',
		array(
			'option'	=> array('maxlength' => '50', 'readonly' => NULL)
		)
	);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'warehouse_fieldset',
		'Склад'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text', 
		'warehouse_product[qty]',
		'Текущее количество продукта (*):',
		array(
			'option'	=> array('value' => 0, 'maxlength' => '7', 'readonly' => NULL)
		)
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text', 
		'warehouse_product[reject_qty]',
		'Списать количество продукта (*):',
		array(
			'option'	=> array('value' => 1, 'maxlength' => '7')
		)
	);
	$CI->form->group('main_block')->add_object(
		'textarea',
		'warehouse_product[comment]',
		'Комментарий',
		array(
			'option' => array('rows' => '2')
		)	
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	
	$CI->form->render_form();
}

function helper_create_sale_form_build($data = array(), $wh_id)
{
	$form_id = Mwarehouses_products::CREATE_SALE_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создание продажи', $form_id, set_url('*/*/save_wh_sale/wh_id/'.$wh_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('/warehouse/warehouses/wh_actions/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	
	$CI->form->add_tab('main_block'			, 'Продукты продажи');
	
	$edit_data = FALSE;
	if(isset($data['warehouse_product'])) $edit_data['warehouse_product'] = $data['warehouse_product'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse_product'] = @$session_temp['warehouse_product'];
	}
	$CI->form->add_group('main_block', $edit_data);
	$CI->form->group('main_block')->add_object(
		'textarea',
		'wh_log[comment]',
		'Комментарий к продаже',
		array(
			'option' => array('rows' => '2')
		)	
	);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'order_products_data',
		'Продукты продажи',
		array(
			'style' => 'background-color:#CCCCCC;'
		)	
	);
	$CI->form->group('main_block')->add_html_to($lid, $data['sale_products']);
	$CI->form->group('main_block')->add_view_to($lid, 'warehouse/wh_create_sale_js', array('wh_create_sale_js_form_id' => $form_id));
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	
	$CI->form->render_form();
}

function helper_create_sale_pr_in_grid_build($grid, $wh_id)
{
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
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
		array(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
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
					'href' 			=> set_url(array('warehouse', 'warehouses_products', 'add_exist_pr', 'wh_id', $wh_id, 'pr_id', '$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_plus', 'title' => 'Добавить выбраный продукт')
				)
			)
		), 'Действие');
}

function helper_sale_products_grid_build($grid, $wh_id)
{
	$grid->add_button('Добавить Продукт', set_url('*/warehouses_products/ajax_get_create_sale_wh_pr_grid/wh_id/'.$wh_id), 
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'sale_wh_add_product'
		));
	
	$grid->add_column(
		array(
			'index'		 => 'i',
			'type'		 => 'text',
			'tdwidth'	 => '3%'
		), '');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '14%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text'
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '6%',
			'option_string' => 'class="pr_qty"'
		), 'К-во');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text',
			'tdwidth'	 => '14%',
			'option_string' => 'class="pr_price"'
		), 'Цена единицы');
	
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
					'href' 			=> set_url(array('*','warehouses_products','ajax_view_edit_pr_sale_qty', 'wh_id', $wh_id, 'row_id', '$1')),
					'href_values' 	=> array('rowid'),
					'options'		=> array('class'=>'icon_edit view_edit_pr_sale_qty', 'title' => 'Редактировать количество')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','warehouses_products','ajax_delete_pr_from_sale', 'wh_id', $wh_id, 'row_id', '$1')),
					'href_values' 	=> array('rowid'),
					'options'		=> array('class' => 'icon_delete delete_pr_from_sale', 'title' => 'Удалить позицию')
				)
			)
		), 'Действие');
}

function helper_sale_wh_products_grid_build($grid, $wh_id)
{	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
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
		array(
			'index'		 => 'qty',
			'searchtable'=> 'C',
			'type'		 => 'number',
			'tdwidth'	 => '9%',
			'filter'	 => true,
			'sortable' 	 => true
		), 'К-во');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
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
					'href' 			=> set_url(array('*','warehouses_products','ajax_get_pr_to_sale','wh_id',$wh_id,'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_plus view_pr_to_sale', 'title' => 'Добавить продукт')
				)
			)
		), 'Действие');
}

function helper_create_transfer_form_build($data, $wh_id)
{
	$form_id = Mwarehouses_products::CREATE_TRANFER_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Создать перенос продуктов', $form_id, set_url('*/*/save_wh_transfer/wh_id/'.$wh_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('/warehouse/warehouses/wh_actions/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	
	$CI->form->add_tab('main_block'			, 'Продукты продажи');
	
	$edit_data = FALSE;
	if(isset($data['warehouse_product'])) $edit_data['warehouse_product'] = $data['warehouse_product'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse_product'] = @$session_temp['warehouse_product'];
	}
	$CI->form->add_group('main_block', $edit_data);
	$CI->form->group('main_block')->add_object(
		'text',
		'wh[from]',
		'Перенос с :',
		array(
			'option' => array('value' => $data['wh']['alias'], 'readonly' => NULL)
		)	
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'wh[to]',
		'Перенос в :',
		array(
			'options' => $data['wh_to_array'],
			'option' => array('rows' => '2')
		)	
	);
	$CI->form->group('main_block')->add_object(
		'textarea',
		'wh[comment]',
		'Комментарий к переносу :',
		array(
			'option' => array('rows' => '2')
		)	
	);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'create_transfer_products_data',
		'Продукты переноса',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	$CI->form->group('main_block')->add_html_to($lid, $data['transfer_products']);
	$CI->form->group('main_block')->add_view_to($lid, 'warehouse/wh_create_transfer_js', array('wh_create_transfer_js_form_id' => $form_id));
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	
	$CI->form->render_form();
}

function helper_transfer_products_grid_build($grid, $wh_id)
{
	$grid->add_button('Добавить Продукт', set_url('*/warehouses_products/ajax_get_transfer_wh_pr_grid/wh_id/'.$wh_id), 
		array(
			'rel' => 'add',
			'class' => 'addButton',
			'id' => 'transfer_wh_add_product'
		));
	$grid->add_column(
		array(
			'index'		 => 'i',
			'type'		 => 'text',
			'tdwidth'	 => '3%'
		), '');
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '14%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text'
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '6%',
			'option_string' => 'class="pr_qty"'
		), 'К-во');
	
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
					'href' 			=> set_url(array('*','warehouses_products','ajax_view_edit_pr_transfer_qty', 'wh_id', $wh_id, 'row_id', '$1')),
					'href_values' 	=> array('rowid'),
					'options'		=> array('class'=>'icon_edit view_edit_pr_transfer_qty', 'title' => 'Редактировать количество')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','warehouses_products','ajax_delete_pr_from_transfer', 'wh_id', $wh_id, 'row_id', '$1')),
					'href_values' 	=> array('rowid'),
					'options'		=> array('class' => 'icon_delete delete_pr_from_transfer', 'title' => 'Удалить позицию')
				)
			)
		), 'Действие');
}

function helper_transfer_wh_products_grid_build($grid, $wh_id)
{	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
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
		array(
			'index'		 => 'qty',
			'searchtable'=> 'C',
			'type'		 => 'number',
			'tdwidth'	 => '9%',
			'filter'	 => true,
			'sortable' 	 => true
		), 'К-во');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
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
					'href' 			=> set_url(array('*','warehouses_products','ajax_get_pr_to_transfer','wh_id',$wh_id,'pr_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_plus view_pr_to_transfer', 'title' => 'Добавить продукт')
				)
			)
		), 'Действие');
}
?>