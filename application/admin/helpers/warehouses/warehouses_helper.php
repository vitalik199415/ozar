<?php
function helper_wh_grid_build($grid)
{
	//$grid->add_button('Точки продаж', set_url('*/*/wh_shops'), 
	//	array(
	//		'rel' => 'add',
	//		'class' => 'addButton'
	//	));
	//$grid->add_button('Добавить точку продажи', set_url('*/*/add_wh_shop'), 
	//	array(
	//		'rel' => 'add',
	//		'class' => 'addButton'
	//	));
	$grid->add_button('Добавить склад', set_url('*/*/add_wh'), 
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
		
	/*$grid->set_checkbox_actions('ID', 'products_checkbox',
			array(
				'options' => array(
					'status_on' => 'В поиске: Да',
					'status_off' => 'В поиске: Нет',
					'in_stock_on' => 'В наличии: Да',
					'in_stock_off' => 'В наличии: Нет',
					'delete' => 'Удалить выбраные'
				),
				'name' => 'products_select_action'
			)
		);*/
			
	$grid->add_column(
		array(
			'index'		 => 'alias',
			'type'		 => 'text',
			'filter'	 => true
		), 'Артикул');
		
	$grid->add_column(
		array(
			'index'		 => 'i_s_wh',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '15%',
			'filter'	 => true
		),'Склад интернет-магазина');
		
	$grid->add_column(
		array(
			'index'		 => 'active',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'Активность');
		
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '12%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','wh_actions','wh_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r', 'title'=>'Просмотр продукта')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','edit_wh','wh_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','delete_wh','wh_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
				)
			)
		), 'Действия');	
}

function helper_wh_form_build($data = array(), $save_param = '')
{
	$form_id = 'wh_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление | Редактирование склада', $form_id, set_url('*/*/save_wh'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/wh_shops')
	));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование',
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
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_wh_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_wh_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Склад с указанным идентификатором уже существует!'));
	
	$CI->form->add_inputmask('main[alias]', 'Regex', 'regex: "[a-zA-Z0-9_-]+"');
	
	$CI->form->add_tab('main_block', 'Основные данные');
	
	$fdata['main'] = FALSE;
	if(isset($data['main'])) $fdata['main'] = $data['main'];
	
	$CI->form->add_group('main_block', $fdata);
	$CI->form->group('main_block')->add_object(
		'text', 
		'main[alias]',
		'Идентификатор латиницей (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'main[active]', 
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'main[i_s_wh]', 
		'Скалад интернет магазина :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->render_form();
}

function helper_wh_actions_form_build($data, $wh_id)
{
	$form_id = 'wh_actions_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Действия', $form_id, set_url('*/*/save_changes/cat_id/'.$wh_id));
	
	$CI->form->add_button(
		array(
		'name' => 'Список складов',
		'href' => set_url('*/warehouses')
	));
	$CI->form->add_button(
		array(
		'name' => 'Список переносов',
		'href' => set_url('warehouse/warehouses_logs/transfers_grid/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
		'name' => 'Перенос продуктов',
		'href' => set_url('warehouse/warehouses_products/create_transfer/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
		'name' => 'Список продаж',
		'href' => set_url('warehouse/warehouses_logs/sales_grid/wh_id/'.$wh_id)
	));
	$CI->form->add_button(
		array(
		'name' => 'Создать продажу',
		'href' => set_url('warehouse/warehouses_products/create_sale/wh_id/'.$wh_id)
	));
	
	
	/*$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));*/
	$CI->form->add_group('all_wh');
	$CI->form->group('all_wh')->add_view('warehouse/wh_actions_all_wh', array('all_wh' => $data['all_wh']));
	
	$CI->form->add_tab('p_b', 'Продукты');
	
	$CI->form->add_group('p_b');
	$lid = $CI->form->group('p_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Продукты склада',
		array(
			'style' => 'background-color:#CCCCCC;'
		)	
	);
	$CI->form->group('p_b')->add_html_to($lid, $data['wh_products']);
	$CI->form->group('p_b')->add_view_to($lid, 'catalogue/products/products_grid_js', array('product_grid_id' => 'wh_products_grid_'.$wh_id));
	$CI->form->group('p_b')->add_view_to($lid, 'warehouse/wh_actions_js', array('wh_actions_form_id' => $form_id));
	
	$CI->form->add_block_to_tab('p_b', 'all_wh');
	$CI->form->add_block_to_tab('p_b', 'p_b');
	$CI->form->render_form();
}









function helper_wh_shops_grid_build($grid, $data)
{
	$grid->add_button('Склады', set_url('*/*'));
	$grid->add_button('Добавить точку продажи', set_url('*/*/add_wh_shop'), 
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
			
	$grid->add_column(
		array(
			'index'		 => 'alias',
			'searchtable'=> 'A',
			'type'		 => 'text',
			'filter'	 => true
		), 'Артикул');
		
	$grid->add_column(
		array(
			'index'		 => 'active',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'Активность');
	
	$grid->add_column(
		array(
			'index'		 => Mwarehouses::ID_WH,
			'type'		 => 'select',
			'options'	 => array('' => '')+$data['wh'],
			'tdwidth'	 => '20%',
			'filter'	 => true
		), 'Склады точки продаж');
		
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '12%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r products_view', 'title'=>'Просмотр продукта')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','edit_wh_shop','wh_shop_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('*','*','delete_wh_shop','wh_shop_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
				)
			)
		), 'Действия');	
}

function helper_wh_shops_form_build($data = array(), $save_param = '')
{
	$form_id = 'products_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление | Редактирование точки продаж', $form_id, set_url('*/*/save_wh_shop'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*')
	));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование',
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
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_wh_shop_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_wh_shop_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Точка продаж с указанным идентификатором уже существует!'));
	
	$CI->form->add_inputmask('main[alias]', 'Regex', 'regex: "[a-zA-Z0-9_-]+"');
	
	$CI->form->add_tab('main_block', 'Основные данные');
	
	$fdata['main'] = FALSE;
	if(isset($data['main'])) $fdata['main'] = $data['main'];
	
	$CI->form->add_group('main_block', $fdata);
	$CI->form->group('main_block')->add_object(
		'text', 
		'main[alias]',
		'Идентификатор латиницей (*):',
		array(
			'option'	=> array('maxlenght' => '50')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'main[active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	
	$fdata['main_warehouses'] = FALSE;
	if(isset($data['main_warehouses'])) $fdata['main_warehouses'] = $data['main_warehouses'];
	
	$CI->form->add_group('main_wh_block', $fdata);
	$lid = $CI->form->group('main_wh_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Выберите склады для точки продажи'
	);
	foreach($data['warehouses'] as $key => $ms)
	{
		$CI->form->group('main_wh_block')->add_object_to($lid,
			'checkbox',
			'main_warehouses['.$key.']',
			$ms,
			array(
				'value' => $key
			)
		);
	}
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('main_block', 'main_wh_block');
	$CI->form->render_form();
}
?>