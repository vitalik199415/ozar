<?php
function helper_products_grid_build($grid)
{
	$grid->add_button('Экспорт продуктов в Excel', set_url('*/products_excel_export'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
	$grid->add_button('Добавить Продукт', set_url('*/*/add'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));

	$grid->set_checkbox_actions('ID', 'products_checkbox',
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
		);

	$grid->add_column(
		array
			(
				'index'		 => 'sku',
				'type'		 => 'text',
				'tdwidth'	 => '10%',
				'filter'	 => true
			), 'Артикул');
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'type'		 => 'text',
				'filter'	 => true
			),'Название');
	$grid->add_column(
		array
			(
				'index'		 => 'create_date',
				'type'		 => 'date',
				'tdwidth'	 => '11%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Создан');
	$grid->add_column(
		array
			(
				'index'		 => 'update_date',
				'type'		 => 'date',
				'tdwidth'	 => '11%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Обновлен');
	$grid->add_column(
		array
			(
				'index'		 => 'status',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			), 'В поиске');
	$grid->add_column(
		array
			(
				'index'		 => 'in_stock',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			),'В наличии');
	$grid->add_column(
			array
			(
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
						'options'		=> array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','photo','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_photo', 'title'=>'Изображения')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','edit','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','delete','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
					)
				)
			), 'Actions');
}
function helper_products_additionally_grid_build($grid)
{
	$grid->set_checkbox_actions('ID', 'products_checkbox',
			array(
				'options' => array(
					'new_on' => 'Новинка: Да',
					'new_off' => 'Новинка: Нет',
					'bestseller_on' => 'Хит продаж: Да',
					'bestseller_off' => 'Хит продаж: Нет',
					'sale_on' => 'Распродажа: Да',
					'sale_off' => 'Распродажа: Нет',
					'status_on' => 'В поиске: Да',
					'status_off' => 'В поиске: Нет',
					'in_stock_on' => 'В наличии: Да',
					'in_stock_off' => 'В наличии: Нет',
                    'action_on' => 'Акция: Да',
                    'action_off' => 'Акция: Нет',
                    'different_colors_on' => 'Есть разные цвета: Да',
                    'different_colors_off' => 'Есть разные цвета: Нет',
                    'super_price_on' => 'Супер цена: Да',
                    'super_price_off' => 'Сепер цена: Нет',
                    'restricted_party_on' => 'Ограниченая партия: Да',
                    'restricted_party_off' => 'Ограниченая партия: Нет',
                    'customised_product_on' => 'Модель под заказ: Да',
                    'customised_product_off' => 'Модель под заказ: Нет',
				),
				'name' => 'products_select_action'
			)
		);
	$grid->add_column(
		array
			(
				'index'		 => 'ID',
				'searchname' => 'id_m_c_products',
				'searchtable'=> 'A',
				'type'		 => 'number',
				'tdwidth'	 => '7%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'ID');

	$grid->add_column(
		array
			(
				'index'		 => 'sku',
				'type'		 => 'text',
				'tdwidth'	 => '8%',
				'filter'	 => true
			), 'Артикул');
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'type'		 => 'text',
				'filter'	 => true
			),'Название');
	$grid->add_column(
		array
			(
				'index'		 => 'new',
				'type'		 => 'select',
				'options'	 => array(''=>'', '0'=>'Нет', '1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			), 'Новинка');
	$grid->add_column(
		array
			(
				'index'		 => 'bestseller',
				'type'		 => 'select',
				'options'	 => array(''=>'', '0'=>'Нет', '1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			),'Хит продаж');
	$grid->add_column(
		array
			(
				'index'		 => 'sale',
				'type'		 => 'select',
				'options'	 => array(''=>'', '0'=>'Нет', '1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			),'Распродажа');
	$grid->add_column(
		array
			(
				'index'		 => 'status',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			), 'В поиске');
	$grid->add_column(
		array
			(
				'index'		 => 'in_stock',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',
				'filter'	 => true
			),'В наличии');
    $grid->add_column(
        array
        (
            'index'      => 'action',
            'type'       => 'select',
            'options'    => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'    => '8%',
            'filter'     => true
        ),'Акция');
    $grid->add_column(
        array
        (
            'index'      => 'different_colors',
            'type'       => 'select',
            'options'    => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'    => '8%',
            'filter'     => true
        ),'Есть разные цвета');
    $grid->add_column(
        array
        (
            'index'      => 'super_price',
            'type'       => 'select',
            'options'    => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'    => '8%',
            'filter'     => true
        ),'Супер цена');
    $grid->add_column(
        array
        (
            'index'      => 'restricted_party',
            'type'       => 'select',
            'options'    => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'    => '8%',
            'filter'     => true
        ),'Ограниченая партия');
    $grid->add_column(
        array
        (
            'index'      => 'customised_product',
            'type'       => 'select',
            'options'    => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'    => '8%',
            'filter'     => true
        ),'Модель под заказ');
	$grid->add_column(
			array
			(
				'index'		 => 'actions',
				'type'		 => 'action',
				'tdwidth'	 => '12%',
				'option_string' => 'align="center"',
				'sortable' 	 => false,
				'filter'	 => false,
				'actions'	 => array()
			), 'Actions');
}
?>