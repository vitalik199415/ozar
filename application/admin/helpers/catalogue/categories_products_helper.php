<?php
function categories_products_grid_build($Grid)
{
	$Grid->addGridColumn(
			array(
				'ID',
				array
					(
						'index'		 => 'ID',
						'tdwidth'	 => '6%'
					)
			)
		);
	$Grid->addGridColumn(
			array(
				'ID Родителя',
				array
					(
						'index'		 => 'id_parent',
						'tdwidth'	 => '8%'
					)
			)
		);
	$Grid->addGridColumn(
			array(
				'Уровень',
				array
					(
						'index'		 => 'level',
						'tdwidth'	 => '8%'
					)
			)
		);	
	$Grid->addGridColumn(
		array(
			'Название',
			array
				(
					'index'		 => 'name'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Создана',
			array
				(
					'index'		 => 'create_date',
					'tdwidth'	 => '10%'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Обновлена',
			array
				(
					'index'		 => 'update_date',
					'tdwidth'	 => '10%'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Активность',
			array
				(
					'index'		 => 'active',
					'tdwidth'	 => '8%'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Действия',
			array
				(
					'index'		 => 'action',
					'type'		 => 'action',
					'tdwidth'	 => '10%',
					'option_string' => 'align="center"',
					'actions'	 => array(
						array(
							'type' 			=> 'link',
							'html' 			=> '',
							'href' 			=> set_url(array('*','*','action','cat_id','$1')),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Перейти')
						)
					)
				)
		)
	);
	return $Grid;
}

function categories_products_incat_grid_build($grid, $cat_id)
{
	$grid->add_column(
		array(
			'index'		 => 'sort',
			'type'		 => 'text',
			'tdwidth'	 => '10%',
			'option_string' => 'align="center"',
			'option'	=> array('readonly' => 'readonly')
		), 'Сорт. вес');
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
		array
			(
			'index'		 => 'status',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		), 'В поиске');
	$grid->add_column(
		array
			(
			'index'		 => 'in_stock',
			'searchtable'=> 'A',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		),'В наличии');
	$grid->add_column(
		array
			(
			'index'		 => 'new',
			'searchtable'=> 'C',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'tdwidth'	 => '9%',
			'filter'	 => true
		),'Новинка');
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
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
				)
			)
		), 'Действие');
}

function helper_catalogue_mass_sale_categories_action_form_build($cat_id, $data)
{
	$form_id = 'catalogue_categories_products_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Действия с товарами', $form_id, set_url('*/*/save_changes/cat_id/'.$cat_id));
	
	$CI->form->add_button(
		array(
			'name' => 'Експорт товаров',
			'href' => set_url('*/*/export_action/cat_id/'.$cat_id)
		));
	
	$CI->form->add_button(
		array(
		'name' => 'Сохранить',
		'href' => '#',
		'options' => array(
			'id' => 'submit'
		)
	));
	
	$CI->form->add_tab('m_b', 'Продукты');
	
	$CI->form->add_group('m_b');
	if($data['settings']['products_sort_type'] == 1)
	{
		$CI->form->group('m_b')->add_object(
		'html',
		'<div style="margin:10px 0; color:#EEEEEE;">Сортировка продуктов по "весу" от большего к меньшему. Измение порядка сортировки в меню <b>Каталог продукции->Продукты каталога->Настройки продуктов</b><br>Приоритет в сортировке имеют продукты, которые отмечены как <b>Новинка</b></div>');
	}
	else
	{
		$CI->form->group('m_b')->add_object(
		'html',
		'<div style="margin:10px 0; color:#EEEEEE;">Сортировка продуктов по "весу" от меньшего к большему. Измение порядка сортировки в меню <b>Каталог продукции->Продукты каталога->Настройки продуктов</b><br>Приоритет в сортировке имеют продукты, которые отмечены как <b>Новинка</b></div>');
	}
	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Товары в категории',
		array(
			'style' => 'background-color:#CCCCCC;'
		)	
	);
	$CI->form->group('m_b')->add_html_to($lid, $data['products']);
	
	$CI->form->add_block_to_tab('m_b', 'm_b');
	$CI->form->render_form();
}

function helper_catalogue_mass_sale_categories_action_export_form_build($cat_id, $data)
{
	$form_id = 'catalogue_categories_products_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Действия с товарами', $form_id, set_url('*/*/export_cat/cat_id/'.$cat_id));

	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/action/cat_id/'.$cat_id)
		));

	$CI->form->add_button(
		array(
			'name' => 'Експортировать',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		));

	$CI->form->add_tab('m_b', 'Продукты');
	//$CI->form->add_tab('e_b', 'Експорт');

	$CI->form->add_group('m_b');


	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Опции експорта',
		array(
			//'style' => 'background-color:#CCCCCC;'
		)
	);

	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'excel_short_description',
		'Добавить короткое описание :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')

		)
	);

	$CI->form->group('m_b')->add_object_to($lid,
		'select',
		'excel_images',
		'Добавить изображения :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')

		)
	);

	if($data['settings']['products_sort_type'] == 1)
	{
		$CI->form->group('m_b')->add_object(
			'html',
			'<div style="margin:10px 0; color:#EEEEEE;">Сортировка продуктов по "весу" от большего к меньшему. Измение порядка сортировки в меню <b>Каталог продукции->Продукты каталога->Настройки продуктов</b><br>Приоритет в сортировке имеют продукты, которые отмечены как <b>Новинка</b></div>');
	}
	else
	{
		$CI->form->group('m_b')->add_object(
			'html',
			'<div style="margin:10px 0; color:#EEEEEE;">Сортировка продуктов по "весу" от меньшего к большему. Измение порядка сортировки в меню <b>Каталог продукции->Продукты каталога->Настройки продуктов</b><br>Приоритет в сортировке имеют продукты, которые отмечены как <b>Новинка</b></div>');
	}

	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Товары в категории',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);



	$CI->form->group('m_b')->add_html_to($lid, $data['products']);
	$CI->form->group('m_b')->add_view_to($lid, 'catalogue/products/products_grid_js', array('product_grid_id' => 'categories_products_grid'));

	
	$CI->form->add_block_to_tab('m_b', 'm_b');

	$CI->form->render_form();
}
?>