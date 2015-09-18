<?php
function helper_products_form_build($data = array(), $save_param = '')
{
	$form_id = Mproducts_save::PR_ADDEDIT_FORM_ID;
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление продукта', $form_id, set_url('*/*/save'.$save_param));
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

	$CI->form->add_tab('main_block'			, 'Основные атрибуты');
	$CI->form->add_tab('description_block'	, 'Описание продукта');
	$CI->form->add_tab('price_block'		, 'Цены продукта');
	$CI->form->add_tab('types_block'		, 'Фильтры продукта');
	$CI->form->add_tab('attributes_block'	, 'Атрибуты продукта');
	$CI->form->add_tab('categories_block'	, 'Категории каталога');
	$CI->form->add_tab('SEO_block'			, 'SEO продукта');
	$CI->form->add_tab('related_block'		, 'Сопутствующие продукты');
	$CI->form->add_tab('similar_block'		, 'Похожие продукты');

	$CI->form->add_tab('album_block'		, 'Альбомы');


	if($save_param == '')
	{
		$CI->form->add_validation('product[sku]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_pr_sku').'", type:"post"}'));
		$CI->form->add_validation('product[url_key]', array('remote' => '{url:"'.set_url('*/*/check_pr_url').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('product[sku]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_pr_sku'.$save_param).'", type:"post"}'));
		$CI->form->add_validation('product[url_key]', array('remote' => '{url:"'.set_url('*/*/check_pr_url'.$save_param).'", type:"post"}'));
	}
	/*if($data['wh_on'])
	{
		foreach($data['data_warehouses'] as $key => $ms)
		{
			$CI->form->add_validation('warehouse['.$key.'][qty]', array('required' => 'true'));
		}
	}*/

	$CI->form->add_validation_massages('product[sku]', array('remote' => 'Продукт с указанным SKU уже существует!'));
	$CI->form->add_validation_massages('product[url_key]', array('remote' => 'Продукт с указанным сегментом URL уже существует!'));

	$CI->form->add_inputmask('product[sku]', 'Regex', 'regex: "[-a-zA-Z0-9№ _|\\/#]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	$CI->form->add_inputmask('product[url_key]', 'Regex', 'regex: "[-a-zA-Z0-9_\\+]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	/*if($data['wh_on'])
	{
		foreach($data['data_warehouses'] as $key => $ms)
		{
			$CI->form->add_inputmask('warehouse['.$key.'][qty]', 'integer', 'allowMinus: false, rightAlignNumerics : false');
		}
	}*/

	$CI->form->add_inputmask_to_class($form_id.'_price_alias', 'Regex', 'regex : "[-a-zA-Z0-9№_\\/#]+", clearMaskOnLostFocus : false, showMaskOnHover : false');
	//$CI->form->add_inputmask_to_class($form_id.'_price_value', 'decimal', 'allowMinus: false, radixPoint: ".", rightAlignNumerics : false');
	//$CI->form->add_inputmask_to_class($form_id.'_price_value', 'Regex', 'regex : "^([0-9]{1,10})([\\.][0-9]{2})$"');
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
		'Артикул товара(SKU) (*):',
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
		'Включен в поиск :',
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
		'Распродажа :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'product[action]',
		'Акция :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'product[different_colors]',
		'Есть разные цвета :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'product[super_price]',
		'Супер цена :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'product[restricted_party]',
		'Ограниченная партия :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
    $CI->form->group('main_block')->add_object(
        'select',
        'product[customised_product]',
        'Модель под заказ :',
        array(
            'options'   => array('0'=>'Нет', '1'=>'Да')
        )
    );

	/*if($data['wh_on'])
	{
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
		foreach($data['data_warehouses'] as $key => $ms)
		{
			$CI->form->group('warehouse_block')->add_object_to($lid,
				'text',
				'warehouse['.$key.'][qty]',
				'Количество продукта (Склад <b>'.$ms['alias'].'</b>) (*):',
				array(
					'option'	=> array('value' => 0, 'maxlength' => '7')
				)
			);
		}
	}*/

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
	$edit_data['product_prices']['new_price']['show_in_short'] = 1;
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
	$CI->form->group('price_block')->add_object(
		'select',
		'product_prices[$][show_in_short]',
		'Показывать в коротком описании :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	$CI->form->group('price_block')->add_object(
		'select',
		'product_prices[$][show_in_detail]',
		'Показывать в полном описании :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
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
		'prices_attributes_fieldset',
		'Правила показа атрибутов для цены'
	);
	$CI->form->group('price_block')->add_object_to($lid,
		'select',
		'product_prices[$][show_attributes]',
		'Показывать атрибуты :',
		array(
			'options' => array('1' => 'Показывать все артибуты', '0' => 'Не показывать атрибуты', '2' => 'Показать только выбраные атрибуты'),
			'option' => array('id' => 'show_prices_attributes')
		)
	);
	$show_attributes = 'display:none;';
	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'
		<div id="products_prices_attributes_block" style="'.$show_attributes.'">
	');
	if(isset($data['data_products_attributes']['attributes']))
	{
		foreach($data['data_products_attributes']['attributes'] as $key => $ms)
		{
			if(isset($data['product_attributes'][$key]))
			{
				$ch_style = array('id' => 'products_prices_attributes_checkbox_'.$key);
				$CI->form->group('price_block')->add_object_to($lid,
					'html',
					'
					<div class="products_prices_attributes_checkbox_block_'.$key.'" style="display:block">
				');
			}
			else
			{
				$ch_style = array('style' => 'display:none', 'id' => 'products_prices_attributes_checkbox_'.$key);
				$CI->form->group('price_block')->add_object_to($lid,
					'html',
					'
					<div class="products_prices_attributes_checkbox_block_'.$key.'" style="display:none">
				');
			}
			$CI->form->group('price_block')->add_object_to($lid,
				'checkbox',
				'product_prices[$][id_attributes]['.$key.']',
				$ms,
				array(
					'value' => $key,
					'option' => $ch_style
				)
			);
			$CI->form->group('price_block')->add_object_to($lid,
				'html',
				'
				</div>
				'
			);
		}
	}
	/*$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'
		<div class="def_buttons" align="center">
			<a href="#" id="show_attributes_button" style="display:none;">Выбрать атрибуты</a>
		</div>
	');
	$CI->form->group('price_block')->add_object_to($lid,
		'hidden',
		'product_prices[$][id_attributes]'
	);*/
	$CI->form->group('price_block')->add_object_to($lid,
		'html',
		'
		</div>
		'
	);
	$CI->form->group('price_block')->add_object(
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

	$CI->form->add_group('related_block');
	$lid = $CI->form->group('related_block')->add_object(
		'fieldset',
		'related',
		'Сопутствующие продукты',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	if(isset($data['data_related_pr']))
	{
		$CI->form->group('related_block')->add_html_to($lid, "<div id='related_products_block'>");
		$CI->form->group('related_block')->add_html_to($lid, $data['data_related_pr']);
		$CI->form->group('related_block')->add_html_to($lid, "</div>");
		$CI->form->add_js_code("$('#related_products_block').gbc_show_product();");
	}
	$CI->form->add_group('not_related_block');
	$lid = $CI->form->group('not_related_block')->add_object(
		'fieldset',
		'not_related',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	$CI->form->group('not_related_block')->add_html_to($lid, "<div id='not_related_products_block'>");
	$CI->form->group('not_related_block')->add_html_to($lid, $data['data_not_related_pr']);
	$CI->form->group('not_related_block')->add_html_to($lid, "</div>");
	$CI->form->add_js_code("$('#not_related_products_block').gbc_show_product();");

	$CI->form->add_group('similar_block');
	$lid = $CI->form->group('similar_block')->add_object(
		'fieldset',
		'related',
		'Сопутствующие продукты',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	if(isset($data['data_similar_pr']))
	{
		$CI->form->group('similar_block')->add_html_to($lid, "<div id='similar_products_block'>");
		$CI->form->group('similar_block')->add_html_to($lid, $data['data_similar_pr']);
		$CI->form->group('similar_block')->add_html_to($lid, "</div>");
		$CI->form->add_js_code("$('#similar_products_block').gbc_show_product();");
	}
	$CI->form->add_group('not_similar_block');
	$lid = $CI->form->group('not_similar_block')->add_object(
		'fieldset',
		'not_similar',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	$CI->form->group('not_similar_block')->add_html_to($lid, "<div id='not_similar_products_block'>");
	$CI->form->group('not_similar_block')->add_html_to($lid, $data['data_not_similar_pr']);
	$CI->form->group('not_similar_block')->add_html_to($lid, "</div>");
	$CI->form->add_js_code("$('#not_similar_products_block').gbc_show_product();");



	//Product Albums Block
	$edit_data = FALSE;
	$album_blocks = array();
	if(isset($data['product_album'])) $edit_data['product_album'] = $data['product_album'];
	if(isset($data['product_album_blocks'])) $album_blocks = $data['product_album_blocks'];
	/*if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['product_prices'] = @$session_temp['product_prices'];
		if(isset($session_temp['product_prices']) && is_array($session_temp['product_prices']))
		{
			foreach($session_temp['product_prices'] as $key => $ms)
			{
				$prices_blocks[$key] = $ms['alias'];
			}
		}
	}*/

	$CI->form->add_group('album_block_top');
	$CI->form->group('album_block_top')->add_object(
		'html',
		'<div class="def_buttons" align="center"><a href="#" onclick="add_album_Tab();return false;">Добавить альбом</a></div><br />'
	);
	$CI->form->add_group('album_block', $edit_data, $album_blocks);
	$CI->form->group('album_block')->add_object(
		'html',
		'<div id="pruduct_album">

		<div style="padding:5px 0 20px 0;" class="def_buttons" align="center">
			<a href="#" class="delete_album">Удалить альбом</a>
		</div>'
	);
	$CI->form->group('album_block')->add_object(
		'text',
		'product_album[$][alias]',
		'Идентификатор :',
		array(
			'option'	=> array('class' => $form_id.'_album_alias', 'maxlength' => '20')
		)
	);
	$CI->form->group('album_block')->add_object(
		'select',
		'product_album[$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('album_block')->add_object(
		'select',
		'product_album[$][type]',
		'Тип кнопки альбома :',
		array(
			'options'	=> array('COLOR' => 'Цвет', 'TEXT' => 'Текст'),
			'option'	=> array('id' => 'album_select_type')
		)
	);

	$lid = $CI->form->group('album_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Цвет иконки',
		array('id' => 'ALBUM_COLOR', 'class' => 'album_type_fieldset')
	);
	$CI->form->group('album_block')->add_object_to($lid,
		'text',
		'product_album[$][color]',
		'Выбор цвета :',
		array(
			'option' => array('class' => $form_id.'_album_name album_color_pick', 'value' => 'ffffff', 'maxlength' => '6', 'id' => 'color_pick', 'style' => 'float:right; width:95%;', 'readonly' => NULL)
		)
	);

	$lid = $CI->form->group('album_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Описание альбома',
		array('style' => 'display:none;', 'id' => 'ALBUM_TEXT', 'class' => 'album_type_fieldset')
	);
	foreach($data['on_langs'] as $key => $ms)
	{
		$CI->form->group('album_block')->add_object_to($lid,
			'text',
			'product_album[$][desc]['.$key.'][name]',
			'Название альбома ('.$ms.') :',
			array(
				'option' => array('class' => $form_id.'_album_name', 'maxlength' => '50')
			)
		);
	}

	$CI->form->group('album_block')->add_object_to($lid,
		'html',
		'
		</div>
		'
	);
	$js_albums_tabs =
		'
		var Tabi=0;
		function add_album_Tab()
		{
			if($("#'.$form_id.' #album_block div.langs_tabs_block").length<=10)
			{
				$("#'.$form_id.' #album_block .langs_tabs ul").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('album_block')->create_tab("#"))).'");
				$("#'.$form_id.' #album_block").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('album_block')->create_tabs_block_NL())).'");
				fields = $("#'.$form_id.' #album_block div.langs_tabs_block:last").find("input,select,textarea");
				jQuery(fields).each(function()
				{
					str = $(this).attr("name");
					$(this).attr("name", str.replace(/\$/g, "new_"+Tabi));
				});
				$("#'.$form_id.' #album_block .langs_tabs ul").tabs("#'.$form_id.' #album_block div.langs_tabs_block");
				var api = $("#'.$form_id.' #album_block .langs_tabs ul").data("tabs");
				api.click(api.getTabs().length-1);
				Tabi++;

				$("#'.$form_id.' #album_block div.langs_tabs_block:last").find(".album_color_pick").jPicker();

				$("#'.$form_id.'").find(".'.$form_id.'_album_alias").inputmask("Regex", {regex : "[-a-zA-Z0-9№_\/#]+"});
				$("#'.$form_id.'").find(".'.$form_id.'_album_name").inputmask("Regex", {regex : "[-a-zA-Z0-9№_\/#]+"});
			}
		}
			$("#'.$form_id.'").on("keyup", ".'.$form_id.'_album_alias", function()
			{
				var api = $("#'.$form_id.' #album_block .langs_tabs ul").data("tabs");
				LI = api.getCurrentTab();
				$(LI).html($(this).val());
			});
			$("#'.$form_id.'").on("click", ".delete_album", function()
			{
				var api = $("#'.$form_id.' #album_block .langs_tabs ul").data("tabs");

				var t = api.getCurrentPane();
				var z = api.getCurrentTab();

				z.remove();
				t.remove();

				api.destroy();
				$("#'.$form_id.' #album_block .langs_tabs ul").tabs("#'.$form_id.' #album_block div.langs_tabs_block");

				return false;
			});
		';
	$CI->form->group('album_block_top')->add_object(
		'js',
		$js_albums_tabs
	);
	$js = '
	$("#'.$form_id.'").find(".album_color_pick").jPicker();
	$("#'.$form_id.'").find("#album_select_type").each(function()
	{
		change_album_type(this);
	});
	$("#'.$form_id.'").on("change", "#album_select_type", function()
	{
		change_album_type(this);
	});
	function change_album_type($this)
	{
		$($this).parents("#pruduct_album").find(".album_type_fieldset").css("display", "none");
		$($this).parents("#pruduct_album").find("#ALBUM_"+$($this).val()).css("display", "block");
	}
	';
	$CI->form->add_group('album_block_bot');
	$CI->form->group('album_block_bot')->add_object(
		'js',
		$js
	);

	$CI->form->add_block_to_tab('main_block'		, 'main_block');
	//if($data['wh_on']) $CI->form->add_block_to_tab('main_block'		, 'warehouse_block');
	$CI->form->add_block_to_tab('description_block'	, 'description_block');
	$CI->form->add_block_to_tab('price_block'		, 'price_block_top');
	$CI->form->add_block_to_tab('price_block'		, 'price_block');
	$CI->form->add_block_to_tab('price_block'		, 'price_block_bot');
	$CI->form->add_block_to_tab('SEO_block'			, 'SEO_block');
	$CI->form->add_block_to_tab('types_block'		, 'types_block');
	$CI->form->add_block_to_tab('attributes_block'	, 'attributes_block');
	$CI->form->add_block_to_tab('categories_block'	, 'categories_block');
	$CI->form->add_block_to_tab('related_block'		, 'related_block');
	$CI->form->add_block_to_tab('related_block'		, 'not_related_block');
	$CI->form->add_block_to_tab('similar_block'		, 'similar_block');
	$CI->form->add_block_to_tab('similar_block'		, 'not_similar_block');
	$CI->form->add_block_to_tab('album_block'		, 'album_block_top');
	$CI->form->add_block_to_tab('album_block'		, 'album_block');
	$CI->form->add_block_to_tab('album_block'		, 'album_block_bot');

	$CI->form->render_form();
}

function products_img_form($ID, $data, $save_param = '')
{
	$form_id = 'products_img_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Свойства продуктов', $form_id, set_url('*/*/save_photo_desc'.$save_param));

	$CI->form->add_button(
		array(
			'name' => 'Список продуктов',
			'href' => set_url('*/*'),
			'options' => array( ),
		 )
	);
	$CI->form->add_button(
		array(
			'name' => 'Продукт',
			'href' => set_url('*/*/edit/id/'.$ID),
			'options' => array( ),
		 )
	);
	$CI->form->add_button(
		array(
			'name' => 'Сохранить описание изображений',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
   			)
		)
	);

	$CI->form->add_group('main_block');
	$array = array();

	if(!isset($data['img'])){ $data['img'] = FALSE; $data['img_desc'] = FALSE; }

	$array['PID'] = $ID;
	$array['form_id'] = $form_id;

	$ddata['PID'] = $ID;
	$ddata['form_id'] = $form_id;
	$ddata['id_users'] = $data['id_users'];
	$ddata['on_langs'] = $data['on_langs'];
	$ddata['ajax'] = FALSE;
	$array['img_html'] = '';
	if(is_array($data['img']))
	{
		foreach($data['img'] as $key => $ms)
		{
			$ddata['id'] = $ms['id_m_c_products_images'];
			$ddata['timage'] = $ms['timage'];
			$ddata['bimage'] = $ms['bimage'];
			$ddata['preview']['preview'] = $data['preview'][$key]['preview'];
			$ddata['values'] = array('img_desc' => array($key => $data['img_desc'][$key]));
			$array['img_html'] .= pr_img_desc_form($ddata);
		}
	}
	//$CI->form->group('main_block')->add_view('products/form_img', $array);
	$CI->form->group('main_block')->add_view('products/products_img_loading_form', $array);

	$save_img_url = set_url('*/*/photo_save/id/'.$ID);
	if(isset($ALB_ID))
	{
		$save_img_url = set_url('*/*/photo_in_album_save/id/'.$ID.'/album_id/'.$ALB_ID);
	}

	$js = "
	$('#".$form_id."').products_img_upload({
		upload_url: '".set_url($save_img_url)."',
		form_id: '".$form_id."'
	});
	";
	$CI->form->group('main_block')->add_object(
		'js',
		$js
	);
	$CI->form->add_block($CI->form->group('main_block'));
	$CI->form->render_form();
}

function products_img_in_album_form($ID, $ALB_ID, $data, $save_param = '')
{
	$form_id = 'products_img_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Свойства продуктов', $form_id, set_url('*/*/save_album_photo_desc'.$save_param));

	$CI->form->add_button(
		array(
			'name' => 'Список продуктов',
			'href' => set_url('*/*'),
			'options' => array( ),
		 )
	);
	$CI->form->add_button(
		array(
			'name' => 'Продукт',
			'href' => set_url('*/*/edit/id/'.$ID),
			'options' => array( ),
		 )
	);
	$CI->form->add_button(
		array(
			'name' => 'Сохранить изменения',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
   			)
		)
	);

	$CI->form->add_group('main_block');
	if(isset($data['albums']))
	{
		$bhtml = '';
		foreach($data['albums'] as $ms)
		{
			$bhtml .= '<a href="'.set_url('*/*/*/id/'.$ID.'/album_id/'.$ms['ID']).'">'.$ms['alias'].'</a>';
		}
		$CI->form->group('main_block')->add_object(
			'html',
			'
			<div align="center" style="font-size:18px;color:#EEEEEE;"> Выбор альбома</div><BR>
			<div class="def_buttons" align="center">'.$bhtml.'</div><br />'
		);
	}

	$array = array();

	if(!isset($data['img'])){ $data['img'] = FALSE; $data['img_desc'] = FALSE; }

	$array['PID'] = $ID;
	$array['ALB_ID'] = $ALB_ID;
	$array['form_id'] = $form_id;

	$ddata['PID'] = $ID;
	$ddata['album_id'] = $ALB_ID;
	$ddata['form_id'] = $form_id;
	$ddata['id_users'] = $data['id_users'];
	$ddata['on_langs'] = $data['on_langs'];
	$ddata['ajax'] = FALSE;
	$array['img_html'] = '';
	if(is_array($data['img']))
	{
		foreach($data['img'] as $key => $ms)
		{
			$ddata['id'] = $ms['id_m_c_products_images'];
			$ddata['timage'] = $ms['timage'];
			$ddata['bimage'] = $ms['bimage'];
			$ddata['preview']['preview'] = $data['preview'][$key]['preview'];
			$ddata['preview']['album_preview'] = $data['preview'][$key]['album_preview'];
			$ddata['bimage'] = $ms['bimage'];
			$ddata['values'] = array('img_desc' => array($key => $data['img_desc'][$key]));
			$array['img_html'] .= pr_img_desc_form($ddata);
		}
	}

	$alb_array['data']['product_attributes'] = $data['product_attributes'];
	$alb_array['data']['product_attributes_options'] = $data['product_attributes_options'];
	$alb_array['values']['album_data'] = $data['album_data'];
	$alb_array['values']['album_attributes'] = $data['album_attributes'];
	$alb_array['form_id'] = $form_id;
	$alb_array['on_langs'] = $data['on_langs'];
	$alb_array['id'] = $ALB_ID;

	$CI->form->group('main_block')->add_view('products/form_album', $alb_array);
	$CI->form->group('main_block')->add_view('products/products_img_loading_form', $array);

	$save_img_url = set_url('*/*/photo_save/id/'.$ID);
	if(isset($ALB_ID))
	{
		$save_img_url = set_url('*/*/photo_in_album_save/id/'.$ID.'/album_id/'.$ALB_ID);
	}

	$js = "
	$('#".$form_id."').products_img_upload({
		upload_url: '".set_url($save_img_url)."',
		form_id: '".$form_id."'
	});
	";
	$CI->form->group('main_block')->add_object(
		'js',
		$js
	);
	$CI->form->add_block($CI->form->group('main_block'));
	//$CI->form->render_form();
}

function pr_img_desc_form($data)
{
	//Описание изображения
	//$IB = new Agform_block();
		$CI = & get_instance();
		return $CI->load->view('products/form_img_desc', $data, TRUE);
	//----------Описание изображения-----------
}

function add_related_pr_grid_build($grid)
{
	$grid->set_checkbox_actions('ID', 'products_related_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
			)
		);
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'filter'	 => true
		),'Название');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'В поиске');
	$grid->add_column(
		array(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		),'В наличии');
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
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
				)
			)
		), 'Actions');
}

function related_pr_grid_build($grid, $pr_id)
{
	/*$grid->set_checkbox_actions('ID', 'get_related_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
			)
		);*/

	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '10%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text'
		),'Название');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
		), 'В поиске');
	$grid->add_column(
		array(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%'

		),'В наличии');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '12%',
			'option_string' => 'align="center"',
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
					'href' 			=> set_url(array('catalogue','products','ajax_delete_related','pr_id',$pr_id,'pr_rl_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_related_pr', 'title'=>'Удалить')
				)

			)
		), 'Actions');
}

function add_similar_pr_grid_build($grid)
{
	$grid->set_checkbox_actions('ID', 'products_similar_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
			)
		);
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'filter'	 => true
		),'Название');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		), 'В поиске');
	$grid->add_column(
		array(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
			'filter'	 => true
		),'В наличии');
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
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
				)
			)
		), 'Actions');
}

function similar_pr_grid_build($grid, $pr_id)
{
	/*$grid->set_checkbox_actions('ID', 'get_related_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
			)
		);*/

	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '10%'
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text'
		),'Название');
	$grid->add_column(
		array(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%',
		), 'В поиске');
	$grid->add_column(
		array(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '8%'

		),'В наличии');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '12%',
			'option_string' => 'align="center"',
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
					'href' 			=> set_url(array('catalogue','products','ajax_delete_similar','pr_id',$pr_id,'pr_sm_id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_similar_pr', 'title'=>'Удалить')
				)

			)
		), 'Actions');
}
?>