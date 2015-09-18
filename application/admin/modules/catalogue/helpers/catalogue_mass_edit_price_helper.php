<?php
function helper_catalogue_mass_edit_price_grid_build($Grid)
{	
	$Grid->addGridColumn(
		array(
			'ID',
			array(
				'index'		 => 'ID',
				'tdwidth'	 => '6%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'ID Родителя',
			array(
				'index'		 => 'id_parent',
				'tdwidth'	 => '8%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Уровень',
			array(
				'index'		 => 'level',
				'tdwidth'	 => '8%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Название',
			array(
				'index'		 => 'name'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Создана',
			array(
				'index'		 => 'create_date',
				'tdwidth'	 => '10%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Обновлена',
			array(
				'index'		 => 'update_date',
				'tdwidth'	 => '10%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Активность',
			array(
				'index'		 => 'active',
				'tdwidth'	 => '8%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Действия',
			array(
				'index'		 => 'action',
				'type'		 => 'action',
				'tdwidth'	 => '10%',
				'option_string' => 'align="center"',
				'actions'	 => array(
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','actions','cat_id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class' => 'icon_arrow_r', 'title' => 'Перейти')
					)
				)
			)
		)
	);
}

function helper_catalogue_mass_edit_price_categorie_products_grid_build(Nosql_grid $grid, $cat_id)
{
	$grid->set_checkbox_actions('ID', 'products_checkbox[]',
			array(
				'options' => NULL,
				'name' => NULL
			)
		);
	$grid->add_column(
		array(
			'index'		 => 'sku',
			'type'		 => 'text',
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'Артикул');
	$grid->add_column(
		array(
			'index'		 => 'name',
			'type'		 => 'text',
			'filter'	 => true
		), 'Название');
	$grid->add_column(
		array(
			'index'		 => 'price',
			'type'		 => 'text',
			'tdwidth'	 => '8%',
		), 'Цена');
	$grid->add_column(
		array(
			'index'		 => 'special_price',
			'type'		 => 'text',
			'tdwidth'	 => '9%',
		), 'Спец. цена');
	$grid->add_column(
		array
			(
			'index'		 => 'special_price_from',
			'type'		 => 'date',
			'tdwidth'	 => '9%'
		), 'С.Ц. от');
	$grid->add_column(
		array
			(
			'index'		 => 'special_price_to',
			'type'		 => 'date',
			'tdwidth'	 => '9%'
		), 'С.Ц. до');
	$grid->add_column(
		array
			(
			'index'		 => 'status',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => TRUE
		), 'В поиске');
	$grid->add_column(
		array
			(
			'index'		 => 'in_stock',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => TRUE
		),'В наличии');
	$grid->add_column(
		array
			(
			'index'		 => 'sale',
			'type'		 => 'select',
			'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
			'tdwidth'	 => '7%',
			'filter'	 => TRUE
		),'Акция');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '8%',
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
				)
			)
		), 'Действие');
}

function helper_catalogue_mass_edit_price_action_form_build($cat_id, $data)
{
	$form_id = 'catalogue_mass_sale_categories_action_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Действия с товарами', $form_id, set_url('*/*/save_changes/cat_id/'.$cat_id));

	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*')
		));

	$CI->form->add_button(
		array(
		'name' => 'Применить действие к выбраным продуктам',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('m_b', 'Действия');
	
	$CI->form->add_group('m_b');
	
	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'sale_actions_data',
		'Действие'
	);

	$CI->form->group('m_b')->add_object_to($lid,
		'select', 
		'price_actions[type]',
		'Выбор типа действия (*):',
		array(
			'options' => array(
				'percent_minus' => 'Уменьшение значения цены на %',
				'percent_plus' => 'Увеличение значения цены на %',
				'price_minus' => 'Уменьшение цены на фиксированую сумму',
				'price_plus' => 'Увеличение цены на фиксированую сумму',
				'cancel_sale' => 'Отменить акционную цену и пометку акции'
			),
			'option' => array('id' => 'sale_actions')
		)
	);

	$CI->form->group('m_b')->add_html_to($lid, "<div class='div_select_type' id='div_percent_minus'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[percent_minus_value]',
		'Процент (*):',
		array(
			'option' => array('maxlength' => 4)
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'price_actions[percent_minus_price_options]',
		'Тип действия :',
		array(
			'options'	=> array(
				'sale_price' => 'Создание акционной цены',
				'noise_price' => 'Уменьшение оригинальной цены'
			),
			'option'	=> array(
				'class' => 'price_options'
			)
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "<div class='select_date'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'price_actions[percent_minus_sale_sticker]',
		'Отметить как акция :',
		array(
			'options'	=> array(
				'1' => 'Да',
				'0' => 'Нет'
			)
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[percent_minus_special_price_from]',
		'Специальная цена от даты :',
		array(
			'option' => array('readonly' => NULL, 'class' => 'datepicker')
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[percent_minus_special_price_to]',
		'Специальная цена до даты :',
		array(
			'option' => array('readonly' => NULL, 'class' => 'datepicker')
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "</div>");
	$CI->form->group('m_b')->add_html_to($lid, "</div>");


	$CI->form->group('m_b')->add_html_to($lid, "<div class='div_select_type' id='div_percent_plus'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[percent_plus_value]',
		'Процент (*):',
		array(
			'option' => array('maxlength' => 4)
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "</div>");


	$CI->form->group('m_b')->add_html_to($lid, "<div class='div_select_type' id='div_price_minus'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[price_minus_value]',
		'Сумма (*):'
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'price_actions[price_minus_price_options]',
		'Тип действия :',
		array(
			'options'	=> array(
				'sale_price' => 'Создание акционной цены',
				'noise_price' => 'Уменьшение оригинальной цены'
			),
			'option'	=> array(
				'class' => 'price_options'
			)
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "<div class='select_date'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'price_actions[price_minus_sale_sticker]',
		'Отметить как акция :',
		array(
			'options'	=> array(
				'1' => 'Да',
				'0' => 'Нет'
			)
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[price_minus_special_price_from]',
		'Специальная цена от даты :',
		array(
			'option' => array('readonly' => NULL, 'class' => 'datepicker')
		)
	);
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[price_minus_special_price_to]',
		'Специальная цена до даты :',
		array(
			'option' => array('readonly' => NULL, 'class' => 'datepicker')
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "</div>");
	$CI->form->group('m_b')->add_html_to($lid, "</div>");


	$CI->form->group('m_b')->add_html_to($lid, "<div class='div_select_type' id='div_price_plus'>");
	$CI->form->group('m_b')->add_object_to($lid,
		'text',
		'price_actions[price_plus_value]',
		'Сумма (*):',
		array(
			'option' => array('maxlength' => 10)
		)
	);
	$CI->form->group('m_b')->add_html_to($lid, "</div>");

	$CI->form->group('m_b')->add_html_to($lid, "<div class='div_select_type' id='div_cancel_sale'>");
	$CI->form->group('m_b')->add_html_to($lid, "</div>");

	$js = "
		var c_val = $('#".$form_id."').find('#sale_actions').val();
		$('#".$form_id."').find('.div_select_type').hide();
		$('#".$form_id."').find('#div_'+c_val).show();

		$('#".$form_id."').find('#sale_actions').change(function()
		{
			$('#".$form_id."').find('.div_select_type').hide();
			$('#".$form_id."').find('#div_'+$(this).val()).show();
		});

		$('#".$form_id."').find('.price_options').change(function()
		{
			if($(this).val() == 'new_action_price')
			{
				$(this).parents('.div_select_type').find('.select_date').show();
			}
			else
			{
				$(this).parents('.div_select_type').find('.select_date').hide();
			}
		});
	";

	$CI->form->group('m_b')->add_object(
		'js',
		$js
	);
	
	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Товары в категории',
		array(
			'style' => 'background-color:#CCCCCC;'
		)	
	);
	$CI->form->group('m_b')->add_html_to($lid, $data['products']);
	$CI->form->group('m_b')->add_view_to($lid, 'catalogue/products/products_grid_js', array('product_grid_id' => 'products_mass_edit_price_grid'));
	
	$CI->form->add_block_to_tab('m_b', 'm_b');
	$CI->form->render_form();
}	
?>