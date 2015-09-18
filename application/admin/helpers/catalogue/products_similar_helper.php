<?php

function helper_products_similar_grid_build($grid)
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
					'href' 			=> set_url('*/*/get_similar/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Похожые товары')
					)
				)
			), 'Actions');	
}

function add_similar_grid_build($grid)
{
	$grid->set_checkbox_actions('ID', 'products_similar_checkbox[]',
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

function get_similar_grid_build($grid, $pr_id)
{	
	$grid->set_checkbox_actions('ID', 'get_similar_checkbox[]',
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
						'href' 			=> set_url(array('*','*','delete_similar','id',$pr_id,'pr_id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
					)
					
				)
			), 'Actions');	
}

function helper_add_similar_form_build($data, $id)
{
$form_id = 'similar_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить похожые', $form_id, set_url('*/*/save_similar/id/'.$id));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Добавить в похожые',
			'href' 		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));
	
	$CI->form->add_tab('similar_block', 'Добавить продукты');
	
	$CI->form->add_group('similar_block');
	$lid = $CI->form->group('similar_block')->add_object(
		'fieldset',
		'not_similar',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
		
	$CI->form->group('similar_block')->add_html_to($lid, $data['products']);
	
		
	
	
	$CI->form->add_block_to_tab('similar_block', 'similar_block');
	$CI->form->render_form();

}

function helper_get_similar_form_build($data, $id)
{
$form_id = 'similar_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить похожые', $form_id, set_url('*/*/delete_similar/id/'.$id));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Удалить выбранные из похожых',
			'href' 		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Добавить похожые',
			'href' 		=> set_url('*/*/add_similar/id/'.$id)
			/*'options'	=> array(
				'id'	=> 'submit'
			)*/
		));
	
	$CI->form->add_tab('similar_block', 'Похожые продукты');
	
	$CI->form->add_group('similar_block');
	$lid = $CI->form->group('similar_block')->add_object(
		'fieldset',
		'similar',
		'Список продуктов',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	if (isset($data['is_similar']))
	{
		$CI->form->group('similar_block')->add_html_to($lid, $data['is_similar']);
	}
		
	
	
	$CI->form->add_block_to_tab('similar_block', 'similar_block');
	$CI->form->render_form();

}



?>