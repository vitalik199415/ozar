<?php
function helper_products_grid_build($grid)
{
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
					'href' 			=> set_url(array('catalogue','products','view','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url(array('catalogue','products','photo','id','$1')),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_photo', 'title'=>'Изображения')
				)

			)
		), 'Actions');
		
}

function helper_excel_export_form($data)
{
	$form_id = 'excel_export_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Действия с товарами', $form_id, set_url('*/*/export'));

	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('catalogue/products')
		));

	$CI->form->add_button(
		array(
			'name' => 'Експортировать в Excel',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		));

	$CI->form->add_tab('m_b', 'Список продуктов');

	$CI->form->add_group('m_b');

	$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'sale_actions_data',
		'Действие'
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




	/*$lid = $CI->form->group('m_b')->add_object(
		'fieldset',
		'categories_products_data',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);*/
	$CI->form->group('m_b')->add_html_to($lid, $data['products']);
	$CI->form->group('m_b')->add_view_to($lid, 'catalogue/products/products_grid_js');

	$CI->form->add_block_to_tab('m_b', 'm_b');
	$CI->form->render_form();
}

?>