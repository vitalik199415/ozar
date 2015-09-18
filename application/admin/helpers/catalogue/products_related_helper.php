<?php

function helper_products_related_grid_build($grid)
{
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
						'href' 			=> set_url(array('catalogue','products','view','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
					),
					
					array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/get_related/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Сопутствующие товары')
					)
				)
			), 'Actions');	
}

function add_related_grid_build($grid)
{
	$grid->set_checkbox_actions('ID', 'products_related_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
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
						'options'		=> array('class'=>'icon_view show_product_link', 'title'=>'Просмотр продукта')
					)
				)
			), 'Actions');	
}

function get_related_grid_build($grid, $pr_id)
{	
	$grid->set_checkbox_actions('ID', 'get_related_checkbox[]',
			array(
				'options' => NULL,
				'name'	  => NULL
			)
		);
		
	
						
	$grid->add_column(
		array
			(
				'index'		 => 'sku',
				'type'		 => 'text',
				'tdwidth'	 => '10%'
		
			), 'Артикул');
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'type'		 => 'text'
				
			),'Название');
	
	$grid->add_column(
		array
			(
				'index'		 => 'status',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%',		
			), 'В поиске');
	$grid->add_column(
		array
			(
				'index'		 => 'in_stock',
				'type'		 => 'select',
				'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
				'tdwidth'	 => '8%'
			
			),'В наличии');
	$grid->add_column(
			array
			(
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
						'href' 			=> set_url(array('*','*','delete_related','id',$pr_id,'pr_id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
					)
				)
			), 'Actions');	
}

function helper_add_related_form_build($data, $id)
{
$form_id = 'related_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить сопутствующие', $form_id, set_url('*/*/save_related/id/'.$id));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Добавить в сопутствующие',
			'href' 		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));
	
	$CI->form->add_tab('related_block', 'Добавить продукты');
	
	$CI->form->add_group('related_block');
	$lid = $CI->form->group('related_block')->add_object(
		'fieldset',
		'not_related',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
		
	$CI->form->group('related_block')->add_html_to($lid, $data['products']);
	
		
	
	
	$CI->form->add_block_to_tab('related_block', 'related_block');
	$CI->form->render_form();

}

function helper_get_related_form_build($data, $id)
{
$form_id = 'related_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить сопутствующие', $form_id, set_url('*/*/delete_related/id/'.$id));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Удалить выбранные из сопутствующих',
			'href' 		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Добавить сопутствующие',
			'href' 		=> set_url('*/*/add_related/id/'.$id)
			/*'options'	=> array(
				'id'	=> 'submit'
			)*/
		));
	
	$CI->form->add_tab('related_block', 'Сопутствующие продукты');
	
	$CI->form->add_group('related_block');
	$lid = $CI->form->group('related_block')->add_object(
		'fieldset',
		'related',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	if (isset($data['is_related']))	
	{
		$CI->form->group('related_block')->add_html_to($lid, $data['is_related']);
	}
		
	
	
	$CI->form->add_block_to_tab('related_block', 'related_block');
	$CI->form->render_form();

}



?>