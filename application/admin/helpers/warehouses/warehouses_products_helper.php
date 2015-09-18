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

function helper_wh_products_grid_build($grid, $wh_id)
{
	//$grid->add_button('Добавить продукт', set_url('*/warehouses_products/add_pr_to_wh/wh_id/'.$wh_id), 
	//	array(
	//		'rel' => 'add',
	//		'class' => 'addButton'
	//	));
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
					'options'		=> array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
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

function helper_not_in_wh_pr_grid_build($grid, $wh_id)
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

function helper_wh_products_form_build($data = array(), $save_param = '')
{
	$form_id = Mwarehouses_products::PR_ADDEDIT_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление продукта', $form_id, set_url('*/*/save_pr_to_wh/wh_id/'.$data['data_wh'][Mwarehouses_products::ID_WH]));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*')
	));
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Удалить продукт',
				'href' => set_url('*/*/delete'.$save_param),
				'options' => array(
							'class' => 'delete_question'
							)
		));
		$CI->form->add_button(
			array(
				'name' => 'Изображения продукта',
				'href' => set_url('*/*/photo'.$save_param)
		));
		$CI->form->add_button(
			array(
				'name' => 'Создать копию',
				'href' => set_url('*/*/add_clone'.$save_param)
		));	
	}
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton'
			)
	));
	
	$CI->form->add_tab('main_block'			, 'Основные атрибуты');
	$CI->form->add_tab('description_block'	, 'Описание продукта');
	$CI->form->add_tab('price_block'		, 'Цены продукта');
	$CI->form->add_tab('types_block'		, 'Тип продукта');
	$CI->form->add_tab('attributes_block'	, 'Атрибуты продукта');
	$CI->form->add_tab('categories_block'	, 'Категории каталога');
	$CI->form->add_tab('SEO_block'			, 'SEO продукта');

	
	if($save_param == '')
	{
		$CI->form->add_validation('product[sku]', array('required' => 'true', 'remote' => '{url:"'.set_url('catalogue/products/check_pr_sku').'", type:"post"}'));
		$CI->form->add_validation('product[url_key]', array('remote' => '{url:"'.set_url('catalogue/products/check_pr_url').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('product[sku]', array('required' => 'true', 'remote' => '{url:"'.set_url('catalogue/products/check_pr_sku'.$save_param).'", type:"post"}'));
		$CI->form->add_validation('product[url_key]', array('remote' => '{url:"'.set_url('catalogue/products/check_pr_url'.$save_param).'", type:"post"}'));
	}
	
	$CI->form->add_validation('warehouse['.$data['data_wh'][Mwarehouses_products::ID_WH].'][qty]', array('required' => 'true'));
	
	$CI->form->add_validation_massages('product[sku]', array('remote' => 'Продукт с указанным SKU уже существует!'));
	$CI->form->add_validation_massages('product[url_key]', array('remote' => 'Продукт с указанным сегментом URL уже существует!'));
	
	$CI->form->add_inputmask('product[sku]', 'Regex', 'regex: "[-a-zA-Z0-9№ _|\\/#]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	$CI->form->add_inputmask('product[url_key]', 'Regex', 'regex: "[-a-zA-Z0-9_\\+]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	
	$CI->form->add_inputmask('warehouse['.$data['data_wh'][Mwarehouses_products::ID_WH].'][qty]', 'integer', 'allowMinus: false, rightAlignNumerics : false');
	
	$CI->form->add_inputmask_to_class($form_id.'_price_alias', 'Regex', 'regex : "[-a-zA-Z0-9№_\\/#]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	$CI->form->add_inputmask_to_class($form_id.'_price_value', 'Regex', 'regex : "^([0-9]{1,10})([\\.][0-9]{2})$"');
	$CI->form->add_inputmask_to_class($form_id.'_price_real_qty', 'Regex', 'regex : "^([1-9][0-9]{5})$"');
	$CI->form->add_inputmask_to_class($form_id.'_price_min_qty', 'Regex', 'regex : "^([1-9][0-9]{5})$"');
	
	//Main Product Data Block
	$edit_data = FALSE;
	if(isset($data['product'])) $edit_data['product'] = $data['product'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product'] = @$session_temp['product'];
	}
	$CI->form->add_group('main_block', $edit_data);
	$CI->form->group('main_block')->add_object(
		'text', 
		'product[sku]',
		'Артикул продукта(SKU) (*):',
		array(
			'option'	=> array('maxlength' => '50')
		)
	);
	$CI->form->group('main_block')->add_object(
		'text', 
		'product[url_key]', 
		'Сегмент URL :',
		array(
			'option'	=> array('maxlength' => '60')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'product[status]', 
		'Включен в поискс :',
		array(
			'options'	=> array('1'=>'Да', '0'=>'Нет')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'product[in_stock]', 
		'В наличии :',
		array(
			'options'	=> array('1'=>'Да', '0'=>'Нет')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'product[new]', 
		'Новинка :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'product[bestseller]', 
		'Хит продаж :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select', 
		'product[sale]', 
		'Акция | Распродажа :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	
	$edit_data = FALSE;
	if(isset($data['warehouse'])) $edit_data['warehouse'] = $data['warehouse'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['warehouse'] = @$session_temp['warehouse'];
	}
	$CI->form->add_group('warehouse_block', $edit_data);
	$lid = $CI->form->group('warehouse_block')->add_object(
		'fieldset',
		'warehouse_fieldset',
		'Склад'
	);
	$CI->form->group('warehouse_block')->add_object_to($lid,
		'text', 
		'warehouse['.$data['data_wh'][Mwarehouses_products::ID_WH].'][qty]',
		'Количество продукта (Склад <b>'.$data['data_wh']['alias'].'</b>) (*):',
		array(
			'option'	=> array('value' => 0, 'maxlength' => '7')
		)
	);
	
	//Product Description Block
	$edit_data = FALSE;
	if(isset($data['product_desc'])) $edit_data['product_desc'] = $data['product_desc'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_desc'] = @$session_temp['product_desc'];
		foreach($data['on_langs'] as $key => $ms)
		{
			$edit_data['product_desc'][$key]['short_description'] = str_replace('&lt;', '<', $edit_data['product_desc'][$key]['short_description']);
			$edit_data['product_desc'][$key]['short_description'] = str_replace('&gt;', '>', $edit_data['product_desc'][$key]['short_description']);
			$edit_data['product_desc'][$key]['full_description'] = str_replace('&lt;', '<', $edit_data['product_desc'][$key]['full_description']);
			$edit_data['product_desc'][$key]['full_description'] = str_replace('&gt;', '>', $edit_data['product_desc'][$key]['full_description']);
		}
	}
	$CI->form->add_group('description_block', $edit_data, $data['on_langs']);
	
	$CI->form->group('description_block')->add_object(
		'text', 
		'product_desc[$][name]',
		'Название продукта :',
		array(
			'option'	=> array('maxlength' => '200')
		)
	);
	$CI->form->group('description_block')->add_object(
		'textarea', 
		'product_desc[$][short_description]',
		'Короткое описание продукта :',
		array(
			'option' 	=> array(
				'class' => 'ckeditor'
			)
		)
	);
	$CI->form->group('description_block')->add_object(
		'textarea', 
		'product_desc[$][full_description]',
		'Полное описание продукта :',
		array(
			'option' 	=> array(
				'class' => 'ckeditor'
			)
		)
	);
	
	//Product Price Block
	$edit_data = FALSE;
	$prices_blocks = array('new_price' => '#1');
	if(isset($data['product_prices'])) $edit_data['product_prices'] = $data['product_prices'];
	if(isset($data['product_prices_blocks'])) $prices_blocks = $data['product_prices_blocks'];
	
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_prices'] = @$session_temp['product_prices'];
		if(isset($session_temp['product_prices']) && is_array($session_temp['product_prices']))
		{
			foreach($session_temp['product_prices'] as $key => $ms)
			{
				$prices_blocks[$key] = $ms['alias'];
			}
		}
	}
	
	$CI->form->add_group('price_block_top');
	$CI->form->group('price_block_top')->add_object(
		'html',
		'<div class="def_buttons" align="center"><a href="#" onclick="addTab();return false;">Добавить цену</a></div><br />'
	);
	$CI->form->add_group('price_block', $edit_data, $prices_blocks);
	$CI->form->group('price_block')->add_object(
		'html',
		'<div id="pruduct_price">
		
		<div style="padding:5px 0 20px 0;" class="def_buttons" align="center">
			<a href="#" class="delete_price">Удалить цену</a>
		</div>'
	);
	$CI->form->group('price_block')->add_object(
		'text',
		'product_prices[$][alias]',
		'Идентификатор :',
		array(
			'option'	=> array('class' => $form_id.'_price_alias', 'maxlength' => '20')
		)
	);
	$CI->form->group('price_block')->add_object(
		'text',
		'product_prices[$][price]',
		'Цена в <b>'.$data['data_default_currency'].'</b> :',
		array(
			'option'	=> array('value' => '0', 'class' => $form_id.'_price_value', 'maxlength' => '13')
		)
	);
	$CI->form->group('price_block')->add_object(
		'text',
		'product_prices[$][real_qty]',
		'Реальное количество в одной единице :',
		array(
			'option'	=> array('value' => 1, 'class' => $form_id.'_price_real_qty', 'maxlength' => '6')
		)
	);
	$CI->form->group('price_block')->add_object(
		'text',
		'product_prices[$][min_qty]',
		'Минимальное количество единиц для заказа :',
		array(
			'option'	=> array('value' => 1, 'class' => $form_id.'_price_min_qty', 'maxlength' => '6')
		)
	);
	$lid = $CI->form->group('price_block')->add_object(
		'fieldset',
		'customers_types_fieldset',
		'Правила показа цены'
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'select', 
		'product_prices[$][visible_rules]', 
		'Правила показа цены :',
		array(
			'options'	=> array('0' => 'Показывать всем посетителям', '1' => 'Показывать только зарегистрированым покупателям', '2' => 'Показать только выбранным группам покупателей'),
			'option'	=> array('class' => $form_id.'_customers_types_select')
		)
	);
	
	$display = 'none';
	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_customers_types">'
	);
	foreach($data['data_customers_types'] as $key => $ms)
	{
		$CI->form->group('price_block')->add_object_to($lid,
			'checkbox', 
			'product_prices[$][m_u_types]['.$key.']',
			$ms.' :',
			array(
				'value' => $key
			)
		);
	}
	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'</div>'
	);
	
	$lid = $CI->form->group('price_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Описание цены'
	);
	foreach($data['on_langs'] as $key => $ms)
	{
		$CI->form->group('price_block')->add_object_to($lid,
			'text',
			'product_prices[$][desc]['.$key.'][name]',
			'Название цены ('.$ms.') :',
			array(
				'option' => array('maxlength' => '50')
			)
		);
		$CI->form->group('price_block')->add_object_to($lid,
			'textarea',
			'product_prices[$][desc]['.$key.'][description]',
			'Описание к цене ('.$ms.') :',
			array(
				'option' => array('rows' => '3')
			)
		);
	}
	$lid = $CI->form->group('price_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Специальная цена'
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'text',
		'product_prices[$][special_price]',
		'Специальная цена в <b>'.$data['data_default_currency'].'</b> :',
		array(
			'option'	=> array('value' => '', 'class' => $form_id.'_price_value', 'maxlength' => '13')
		)
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'text',
		'product_prices[$][special_price_from]',
		'Специальная цена от даты :',
		array(
			'option' => array('class' => 'datepicker')
		)
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'text',
		'product_prices[$][special_price_to]',
		'Специальная цена до даты :',
		array(
			'option' => array('class' => 'datepicker')
		)
	);

	$lid = $CI->form->group('price_block')->add_object(
		'fieldset',
		'attributes_fieldset',
		'Правила показа атрибутов для цены'
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'select',
		'product_prices[$][show_attributes]',
		'Показывать атрибуты :',
		array(
			'options' => array('1' => 'Показывать все артибуты', '0' => 'Не показывать атрибуты', '2' => 'Показать только выбраные атрибуты'),
			'option' => array('id' => 'show_attributes')
		)
	);

	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'
		<div class="def_buttons" align="center">
			<a href="#" id="show_attributes_button" style="display:none;">Выбрать атрибуты</a>
		</div>
	');
	$CI->form->group('price_block')->add_object_to($lid,
		'hidden',
		'product_prices[$][id_attributes]'
	);	
	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'
		</div>
		'
	);
	$js_price_tabs = 
		'
		var Tabi=0;
		function addTab()
		{
			if($("#'.$form_id.' #price_block div.langs_tabs_block").length<=15)
			{
				$("#'.$form_id.' #price_block .langs_tabs ul").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('price_block')->create_tab("#"))).'");
				$("#'.$form_id.' #price_block").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('price_block')->create_tabs_block_NL())).'");
				fields = $("#'.$form_id.' #price_block div.langs_tabs_block:last").find("input,select,textarea");
				jQuery(fields).each(function()
				{
					str = $(this).attr("name");
					$(this).attr("name", str.replace(/\$/g, "new_"+Tabi));
				});
				$("#'.$form_id.' #price_block .langs_tabs ul").tabs("#'.$form_id.' #price_block div.langs_tabs_block");
				var api = $("#'.$form_id.' #price_block .langs_tabs ul").data("tabs");
				api.click(api.getTabs().length-1);
				Tabi++;
				datepicker_load();
				
				$("#'.$form_id.'").find(".'.$form_id.'_price_alias").inputmask("Regex", {regex : "[-a-zA-Z0-9№_\/#]+"});
				$("#'.$form_id.'").find(".'.$form_id.'_price_value").inputmask("Regex", {regex : "^([0-9]{1,10})([\.][0-9]{2})$"});
				$("#'.$form_id.'").find(".'.$form_id.'_price_real_qty").inputmask("Regex", {regex : "^([1-9][0-9]{5})$"});
				$("#'.$form_id.'").find(".'.$form_id.'_price_min_qty").inputmask("Regex", {regex : "^([1-9][0-9]{5})$"});
			}
		}
			$("#'.$form_id.'").on("keyup", ".'.$form_id.'_price_alias", function()
			{
				var api = $("#'.$form_id.' #price_block .langs_tabs ul").data("tabs");
				LI = api.getCurrentTab();
				$(LI).html($(this).val());
			});
			$("#'.$form_id.'").on("click", ".delete_price", function()
			{
				var api = $("#'.$form_id.' #price_block .langs_tabs ul").data("tabs");
				if(api.getPanes().length > 1)
				{
					var t = api.getCurrentPane();
					var z = api.getCurrentTab();
					
					z.remove();
					t.remove();
					
					api.destroy();
					$("#'.$form_id.' #price_block .langs_tabs ul").tabs("#'.$form_id.' #price_block div.langs_tabs_block");
				}
				else
				{
					alert("Вы не можете удалить все цены на продукт!");
				}
				return false;
			});
		';
	$CI->form->group('price_block_top')->add_object(
		'js',
		$js_price_tabs
	);
	$js = '
	$("#'.$form_id.'").find(".'.$form_id.'_customers_types_select").each(function()
	{
		if($(this).val() == 2)
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "block");
		}
	});
	$("#'.$form_id.'").on("change", ".'.$form_id.'_customers_types_select", function()
	{
		if($(this).val() == 2)
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "block");
		}
		else
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "none");
		}
	});
	';
	$CI->form->add_group('price_block_bot');
	$CI->form->group('price_block_bot')->add_object(
		'js',
		$js
	);
	
	//Product SEO Block
	$edit_data = FALSE;
	if(isset($data['product_desc'])) $edit_data['product_desc'] = $data['product_desc'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_desc'] = $session_temp['product_desc'];
	}
	$CI->form->add_group('SEO_block', $edit_data, $data['on_langs']);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'product_desc[$][seo_title]', 
		'Title : '
	);
	$CI->form->group('SEO_block')->add_object(
		'text', 
		'product_desc[$][seo_description]', 
		'Description : '
	);
	$CI->form->group('SEO_block')->add_object(
		'text', 
		'product_desc[$][seo_keywords]',
		'Keywords : '
	);
	
	//Product Types
	$edit_data = FALSE;
	if(isset($data['product_types'])) $edit_data['product_types'] = $data['product_types'];
	if(isset($data['product_properties'])) $edit_data['product_properties'] = $data['product_properties'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_types'] = @$session_temp['product_types'];
		$edit_data['product_properties'] = @$session_temp['product_properties'];
	}
	$CI->form->add_group('types_block', $edit_data);
	foreach($data['data_products_types']['types'] as $key => $ms)
	{
		$display = 'none';
		if(isset($edit_data['product_types'][$key]))
		{
			$display = 'block';
		}
		$lid = $CI->form->group('types_block')->add_object(
			'fieldset',
			'name_fieldset',
			$ms
		);
		$CI->form->group('types_block')->add_object_to($lid,
			'checkbox',
			'product_types['.$key.']',
			$ms,
			array(
				'value' => $key,
				'option' => array('class' => 'types')
			)
		);
		if(isset($data['data_products_types']['properties'][$key]))
		{
			$CI->form->group('types_block')->add_object_to($lid,
				'html',
				'<div style="padding:5px 0 0 30px; display:'.$display.'" id="properties_'.$key.'">'
			);
			foreach($data['data_products_types']['properties'][$key] as $pkey => $pms)
			{
				$CI->form->group('types_block')->add_object_to($lid,
					'checkbox',
					'product_properties['.$key.']['.$pkey.']',
					$pms,
					array(
						'value' => $pkey
					)
				);
			}
			$CI->form->group('types_block')->add_object_to($lid,
				'html',
				'</div>'
			);
		}
	}
	
	//Product Attributes
	$edit_data = FALSE;
	if(isset($data['product_attributes'])) $edit_data['product_attributes'] = $data['product_attributes'];
	if(isset($data['product_attributes_options'])) $edit_data['product_attributes_options'] = $data['product_attributes_options'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_attributes'] = @$session_temp['product_attributes'];
		$edit_data['product_attributes_options'] = @$session_temp['product_attributes_options'];
	}
	$CI->form->add_group('attributes_block', $edit_data);
	
	foreach($data['data_products_attributes']['attributes'] as $key => $ms)
	{
		$display = 'none';
		if(isset($edit_data['product_attributes'][$key]))
		{
			$display = 'block';
		}
		
		$lid = $CI->form->group('attributes_block')->add_object(
			'fieldset',
			'name_fieldset',
			$ms
		);
		$CI->form->group('attributes_block')->add_object_to($lid,
			'checkbox',
			'product_attributes['.$key.']',
			$ms,
			array(
				'value' => $key,
				'option' => array('class' => 'attributes')
			)
		);
		if(isset($data['data_products_attributes']['attributes_options'][$key]))
		{
			$CI->form->group('attributes_block')->add_object_to($lid,
				'html',
				'<div style="padding:5px 0 0 30px; display:'.$display.'" id="attributes_options_'.$key.'">'
			);
			foreach($data['data_products_attributes']['attributes_options'][$key] as $opkey => $opms)
			{
				$CI->form->group('attributes_block')->add_object_to($lid,
					'checkbox',
					'product_attributes_options['.$key.']['.$opkey.']',
					$opms,
					array(
						'value' => $opkey
					)
				);
			}
			$CI->form->group('attributes_block')->add_object_to($lid,
				'html',
				'</div>'
			);
		}
	}
	
	//Product Categories
	$edit_data = FALSE;
	if(isset($data['product_categories'])) $edit_data['product_categories'] = $data['product_categories'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_categories'] = @$session_temp['product_categories'];
	}
	
	$CI->form->add_group('categories_block', $edit_data);
	foreach($data['data_products_categories'] as $key => $ms)
	{
		$CI->form->group('categories_block')->add_object(
			'html',
			'<div style="padding:0 0 0 '.($ms['level']*40).'px;">'
		);
		$CI->form->group('categories_block')->add_object(
			'checkbox',
			'product_categories['.$ms['ID'].']',
			$ms['name'],
			array(
				'value' => $ms['ID']
			)
		);
		$CI->form->group('categories_block')->add_object(
			'html',
			'</div>'
		);
	}
	
	if(isset($data['product_price']) && is_array($data['product_price']))
	{
		$CI->form->add_js_code("var price_attributes = {};");
		foreach($data['product_price'] as $key => $ms)
		{
			$CI->form->add_js_code("price_attributes[".$key."] = '".$ms['id_attributes']."';
			");
		}
	}
	else
	{
		$CI->form->add_js_code("var price_attributes = {};");
	}
	
	$CI->form->add_js_code("$('#".$form_id."').gbc_products_addedit({price_attributes : price_attributes});
	");	
	
	$CI->form->add_block_to_tab('main_block'		, 'main_block');
	$CI->form->add_block_to_tab('main_block'		, 'warehouse_block');
	$CI->form->add_block_to_tab('description_block'	, 'description_block');
	$CI->form->add_block_to_tab('price_block'		, 'price_block_top');
	$CI->form->add_block_to_tab('price_block'		, 'price_block');
	$CI->form->add_block_to_tab('price_block'		, 'price_block_bot');
	$CI->form->add_block_to_tab('SEO_block'			, 'SEO_block');
	$CI->form->add_block_to_tab('types_block'		, 'types_block');
	$CI->form->add_block_to_tab('attributes_block'	, 'attributes_block');
	$CI->form->add_block_to_tab('categories_block'	, 'categories_block');
	
	$CI->form->render_form();
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