<?php
function helper_logs_grid_build(Grid $grid, $wh_array, $logs_types)
{
	$grid->add_button('Продажи', set_url('*/warehouses_sales'));
	$grid->add_button('Перенос', set_url('*/warehouses_transfers'));
	$grid->add_button('Количество добавлено', set_url('*/warehouses_logs/edit_pr_logs'));
	$grid->add_button('Списан', set_url('*/warehouses_logs/reject_pr_logs'));

	$grid->add_column(
		 array(
			 'index'	=> 'wh_log_number',
			 'type'		=> 'text',
			 'tdwidth'	 => '9%',
			 'sortable' 	 => true,
		 ), 'Номер');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
		), 'Комментарий');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'filter'	 => true
		), 'Дата создания');
	$grid->add_column(
		array(
				'index'		 => mwarehouses_logs::ID_WH,
				'type'		 => 'select',
				'options'	 => array('' => '') + $wh_array,
				'tdwidth'	 => '14%',
				'filter'	 => true
			), 'Склад');	
	$grid->add_column(
		array(
				'index'		 => 'type',
				'type'		 => 'select',
				'options'	 => array('' => '') + $logs_types,
				'tdwidth'	 => '14%',
				'filter'	 => true
			), 'Тип');
	
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '10%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
				/*array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','warehouses_logs','view_logs', 'log_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view view_wh_sale', 'title' => 'Просмотр')
				)*/
			)
		), 'Действие');
}

function helper_sales_logs_grid_build(Grid $grid, $wh_array)
{
	$grid->add_button('Все логи', set_url('*/warehouses_logs'));
	$grid->add_button('Переносы', set_url('*/warehouses_transfers'));
	$grid->add_button('Количество добавлено', set_url('*/warehouses_logs/edit_pr_logs'));
	$grid->add_button('Списан', set_url('*/warehouses_logs/reject_pr_logs'));
	
	$grid->add_column(
		array(
			'index'		 => 'sales_number',
			'tdwidth'	 => '10%',
			'type'		 => 'text',
			'filter'	 => true
		), 'Номер продажи');
	$grid->add_column(
		array(
			'index'		 => 'total_qty',
			'tdwidth'	 => '6%',
			'type'		 => 'text',
		), 'С-ое к-во');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'tdwidth'	 => '8%',
			'type'		 => 'text',
		), 'Сумма');	
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Дата создания');
	$grid->add_column(
		array
			(
				'index'		 => mwarehouses_logs::ID_WH,
				'type'		 => 'select',
				'options'	 => array('' => '') + $wh_array,
				'tdwidth'	 => '11%',
				'filter'	 => true
			), 'Склад');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
		), 'Комментарий');
	
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
					'href' 			=> set_url(array('*','warehouses_logs','ajax_view_log','wh_id','$1','log_id','$2')),
					'href_values' 	=> array(mwarehouses_logs::ID_WH, 'ID'),
					'options'		=> array('class'=>'icon_view view_wh_sale', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}
	
function helper_edit_pr_logs_grid_build($grid, $wh_array)
{
	$grid->add_button('Все логи', set_url('*/warehouses_logs'));
	$grid->add_button('Продажи', set_url('*/warehouses_sales'));
	$grid->add_button('Перенос', set_url('*/warehouses_transfers'));
	$grid->add_button('Списан', set_url('*/warehouses_logs/reject_pr_logs'));
	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
		), 'Артикул товара');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '7%',
		), 'Д-ное К-во');
	$grid->add_column(
		array
			(
			'index'		 => mwarehouses_logs::ID_WH,
			'type'		 => 'select',
			'options'	 => array('' => '') + $wh_array,
			'tdwidth'	 => '14%',
			'filter'	 => true
		), 'Склад');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Дата создания');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
		), 'Комментарий');
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
					'href' 			=> set_url(array('*','warehouses_logs','view_logs', 'log_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view view_wh_sale', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}
	
function helper_reject_pr_logs_grid_build($grid, $wh_array)
{
	$grid->add_button('Все логи', set_url('*/warehouses_logs'));
	$grid->add_button('Продажи', set_url('*/warehouses_sales'));
	$grid->add_button('Перенос', set_url('*/warehouses_transfers'));
	$grid->add_button('Количество добавлено', set_url('*/warehouses_logs/edit_pr_logs'));
	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
		), 'Артикул товара');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '9%',
		), 'Списано К-во');
	$grid->add_column(
		array
			(
			'index'		 => mwarehouses_logs::ID_WH,
			'type'		 => 'select',
			'options'	 => array('' => '') + $wh_array,
			'tdwidth'	 => '14%',
			'filter'	 => true
		), 'Склад');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '13%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Дата создания');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
		), 'Комментарий');
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
					'href' 			=> set_url(array('*','warehouses_logs','view_logs', 'log_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view view_wh_sale', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}
	
function helper_add_pr_logs_grid_build($grid, $wh_array)
{
		
}
	
function helper_delete_pr_logs_grid_build($grid, $wh_array)
{
		
}

function helper_sales_report_form_build($data = array(), $wh_id)
{
	$form_id = Mwarehouses_logs::SALE_REPORT_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Отчеты продаж', $form_id, set_url('*/warehouses_logs/sales_reports/wh_id/'.$wh_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/warehouses_logs/sales_grid/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
			'name' => 'Генерировать отчет',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
	));
	
	$CI->form->add_validation('date[date_from]', array('required' => 'true'));
	$CI->form->add_validation('date[date_to]', array('required' => 'true'));
	
	$CI->form->add_tab('main_block'	, 'Отчеты продаж');
	
	$data_date = FALSE;
	if(isset($data['date'])) $data_date['date'] = $data['date'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$data_date['date'] = @$session_temp['date'];
	}
	
	$CI->form->add_group('date', $data_date);
	$lid = $CI->form->group('date')->add_object(
		'fieldset',
		'base_fieldset',
		'Выбор интервала дат для генерации отчета'
	);
	$CI->form->group('date')->add_object_to($lid,
		'text',
		'date[date_from]',
		'Дата от :',
		array(
			'option' => array('class' => 'datepicker')
		)
	);
	$CI->form->group('date')->add_object_to($lid,
		'text',
		'date[date_to]',
		'Дата до :',
		array(
			'option' => array('class' => 'datepicker')
		)
	);
	
	if(isset($data['total_qty']) && isset($data['total_sum']))
	{
		$lid = $CI->form->group('date')->add_object(
			'fieldset',
			'base_fieldset',
			'Суммы'
		);
		$CI->form->group('date')->add_object_to($lid,
			'text',
			'total_qty',
			'Сумарное количество :',
			array(
				'option' => array('value' => $data['total_qty'])
			)
		);
		$CI->form->group('date')->add_object_to($lid,
			'text',
			'total_sum',
			'Сумма :',
			array(
				'option' => array('value' => $data['total_sum'])
			)
		);
	}
	
	$CI->form->add_group('sales');
	$lid = $CI->form->group('sales')->add_object(
		'fieldset',
		'order_products_data',
		'Список продаж',
		array(
			'style' => 'background-color:#CCCCCC;'
		)	
	);
	$CI->form->group('sales')->add_html_to($lid, $data['sales_grid']);
	//$CI->form->group('sales')->add_view_to($lid, 'warehouse/wh_create_sale_js', array('wh_create_sale_js_form_id' => $form_id));
	
	$CI->form->add_block_to_tab('main_block', 'date');
	$CI->form->add_block_to_tab('main_block', 'sales');
	
	$CI->form->render_form();
}

function helper_sales_reports_grid_build($grid, $wh_id)
{
	//$grid->add_button('Отчеты продаж', set_url('*/warehouses_logs/sales_reports/wh_id/'.$wh_id));
	
	$grid->add_column(
		array(
			'index'		 => 'sales_number',
			'type'		 => 'text',
			'tdwidth'	 => '13%',
		), 'Номер продажи');
	$grid->add_column(
		array(
			'index'		 => 'total_qty',
			'type'		 => 'text',
			'tdwidth'	 => '8%',
		), 'С-ное к-во');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text',
			'tdwidth'	 => '12%',
		), 'Сумма');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
		), 'Комментарий');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '15%'
		), 'Дата создания');
	
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
					'href' 			=> set_url(array('*','warehouses_logs','ajax_view_sales', 'wh_id', $wh_id, 'log_id', '$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view view_wh_sale', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}

function helper_sales_reports_products_grid_build($grid, $wh_id, $log_id)
{
	//$grid->add_button('Отчеты продаж', set_url('*/warehouses_logs/sales_reports/wh_id/'.$wh_id));
	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '12%',
		), 'Количество');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text',
			'tdwidth'	 => '15%',
		), 'Цена продажи');
	$grid->add_column(
		array(
			'index'		 => 'total',
			'type'		 => 'text',
			'tdwidth'	 => '18%'
		), 'Сумма');
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
					'href' 			=> set_url(array('*','warehouses_logs','ajax_view_sales_pr', 'wh_id', $wh_id, 'log_id', $log_id, 'pr_id', '$1')),
					'href_values' 	=> array('ID_PR'),
					'options'		=> array('class'=>'icon_view overlay_view_wh_sale_pr', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}

function helper_transfers_grid_build($grid, $wh_id, $wh_array, $wh_alias)
{
	$grid->add_button('Склад '.$wh_alias, set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
	$grid->add_button('Создать перенос(Склад '.$wh_alias.')', set_url('*/warehouses_products/create_transfer/wh_id/'.$wh_id));
	//$grid->add_button('Отчеты по продажам', set_url('*/warehouses_logs/sales_reports/wh_id/'.$wh_id));
	
	$grid->add_column(
		array(
			'index'		 => 'transfers_number',
			'type'		 => 'text',
			'tdwidth'	 => '9%',
		), 'Номер переноса');
		$grid->add_column(
		array(
			'index'		 => 'id_wh_to',
			'type'		 => 'select',
			'options'	 => array('' => '') + $wh_array,
			'tdwidth'	 => '16%',
			'filter'	 => true
		), 'Перенос в склад');
	$grid->add_column(
		array(
			'index'		 => 'total_qty',
			'type'		 => 'text',
			'tdwidth'	 => '8%',
		), 'С-ное к-во');
	$grid->add_column(
		array(
			'index'		 => 'comment',
			'type'		 => 'text',
			'filter'	 => true
		), 'Комментарий');
	$grid->add_column(
		array(
			'index'		 => 'create_date',
			'type'		 => 'date',
			'tdwidth'	 => '12%',
			'sortable' 	 => true,
			'filter'	 => true
		), 'Дата создания');
	
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
					'href' 			=> set_url(array('*','warehouses_logs','ajax_view_transfer', 'wh_id', $wh_id, 'log_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view view_wh_transfer', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}
		
function helper_transfers_reports_products_grid_build($grid, $wh_id, $log_id)
{
	//$grid->add_button('Отчеты продаж', set_url('*/warehouses_logs/sales_reports/wh_id/'.$wh_id));
	
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'qty',
			'type'		 => 'text',
			'tdwidth'	 => '12%',
		), 'Количество');
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
					'href' 			=> set_url(array('*','warehouses_logs','ajax_view_transfer_pr', 'wh_id', $wh_id, 'log_id', $log_id, 'pr_id', '$1')),
					'href_values' 	=> array('ID_PR'),
					'options'		=> array('class'=>'icon_view overlay_view_wh_transfer_pr', 'title' => 'Просмотр')
				)
			)
		), 'Действие');
}
?>