<?php
function helper_products_types_set_grid_build(Grid $grid, $categories)
{
	$grid->add_button('Добавить набор фильтров', set_url('*/*/select_category'),
		array(
			'class' => 'addButton'
		)
	);

	$grid->add_column(
		 array(
			 'index'	=> 'show_id',
			 'type'		=> 'text',
			 'tdwidth'	=> '5%',
			 'filter'	=> true
		 ), 'ID');

	$grid->add_column(
		array(
			'index'		=> 'set_name',
			'type'		=> 'text',
			'tdwidth'	=> '20%',
			'filter'	=> true
		), 'Название');

	$grid->add_column(
		array(
			'index'		=> 'set_description',
			'type'		=> 'text'
		), 'Описание набора');

	$grid->add_column(
		array(
			'index'		=> 'url',
			'type'		=> 'text',
			'tdwidth'	=> '20%'
		), 'URL набора');

	$grid->add_column(
		array(
			'index'		=> 'cat_name',
			'searchtable' => 'A',
			'searchname' => Mproducts_types_set::ID_CAT,
			'type'		 => 'select',
			'options'	 => array('' => '') + $categories,
			'tdwidth'	=> '20%',
			'filter'	=> true
		), 'Категория');

	$grid->add_column(
		array(
			'index'			=> 'action',
			'type'			=> 'action',
			'tdwidth'		=> '12%',
			'option_string'	=> 'align="center"',
			'actions'		=> array(
				array(
					'type'			=> 'link',
					'html'			=> '',
					'href'			=> set_url('*/*/edit/cat_id/$2/id/$1'),
					'href_values'	=> array('ID', 'CAT_ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
				),
				array(
					'type'			=> 'link',
					'html'			=> '',
					'href'			=> set_url('*/*/delete/id/$1'),
					'href_values'	=> array('ID'),
					'options'		=> array('class'=>'icon_detele  delete_question', 'title'=>'Удалить')
				)
			)
		), 'Действия');
}

function categories_grid_build($Grid)
{
	$Grid-> addButton(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*')
		)
	);

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
						'href' 			=> set_url(array('*','*','add','cat_id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_arrow_r', 'title'=>'Добавить')
					)
				)
			)
		)
	);
	return $Grid;
}

function helper_products_types_set_add_edit_form($data, $save_param = '')
{
	$form_id = 'products_types_set_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Наборы свойств', $form_id, set_url('*/*/save/cat_id/'.$data['cat_id'].$save_param));
	$CI->form->enable_CKE();

	if($save_param !='')
	{
		$CI->form->add_button(
			array(
				'name' 		=> 'Назад',
				'href' 		=> set_url('*/*')
			));

	}
	else
	{
		$CI->form->add_button(
			array(
				'name' 		=> 'Назад',
				'href' 		=> set_url('*/*/select_category')
			));

	}

	$CI->form->add_button(
		array(
			'name'		=> 'Сохранить и продолжить редактирование',
			'href'		=> '#',
			'options'	=> array(
				'id'	 => 'submit_back'
			)
		));

	$CI->form->add_button(
		array(
			'name'		=> 'Сохранить',
			'href'		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));

	$CI->form->add_tab('main_block', 'Основные данные');
	$CI->form->add_tab('properties_block', 'Выбор фильтров');
	$CI->form->add_tab('SEO_block', 'SEO');

	if(isset($data['main'])) $PMdata['main'] = $data['main'];
	$CI->form->add_group('main_block', $data);

	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Основные данные'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[set_name]',
		'Название набора :'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'textarea',
		'main[set_description]',
		'Описание набора :',
		array(
			'option'	=> array('rows' => '3')
		)

	);
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[active]',
		'Активность :',
		array(
			'options' => array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[url]',
		'Сегмент URL набора :'
	);

	$edit_data = FALSE;
	if(isset($data['edit_properties']['product_types'])) $edit_data['product_types'] = $data['edit_properties']['product_types'];
	if(isset($data['edit_properties']['product_properties'])) $edit_data['product_properties'] = $data['edit_properties']['product_properties'];

	$CI->form->add_group('properties_block', $edit_data);
	foreach($data['category_properties']['types'] as $key => $ms)
	{
		$display = 'none';
		if(isset($edit_data['product_types'][$key]))
		{
			$display = 'block';
		}
		$CI->form->group('properties_block')->add_object(
			'checkbox',
			'product_types['.$key.']',
			$ms,
			array(
				'value' => $key,
				'option' => array('class' => 'types')
			)
		);
		if(isset($data['category_properties']['properties'][$key]))
		{
			$CI->form->group('properties_block')->add_object(
				'html',
				'<div style="padding:5px 0 0 30px; display:'.$display.'" id="properties_'.$key.'">'
			);
			foreach($data['category_properties']['properties'][$key] as $pkey => $pms)
			{
				$CI->form->group('properties_block')->add_object(
					'checkbox',
					'product_properties['.$key.']['.$pkey.']',
					$pms,
					array(
						'value' => $pkey
					)
				);
			}
			$CI->form->group('properties_block')->add_object(
				'html',
				'</div>'
			);
		}
	}

	$js = "
		$('#".$form_id."').find('.types').bind('change', function()
		{
			if($(this).prop('checked'))
			{
				$('#".$form_id."').find('#properties_'+$(this).val()).css('display','block');
			}
			else
			{
				$('#".$form_id."').find('#properties_'+$(this).val()).css('display','none');
			}
		});
	";

	$CI->form->group('properties_block')->add_object(
		'js',
		$js
	);

	$edit_data = FALSE;
	if(isset($data['seo_desc'])) $edit_data['seo_desc'] = $data['seo_desc'];

	$CI->form->add_group('SEO_block', $edit_data, $data['on_langs']);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'seo_desc[$][seo_title]',
		'Title : '
	);
	$CI->form->group('SEO_block')->add_object(
		'textarea',
		'seo_desc[$][seo_description]',
		'Description : ',
		array(
			'option'	=> array('rows' => '3')
		)
	);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'seo_desc[$][seo_keywords]',
		'Keywords : '
	);

	$CI->form->group('SEO_block')->add_object(
		'textarea',
		'seo_desc[$][description]',
		'Text : ',
		array(
			'option' 	=> array(
				'class' => 'ckeditor'
			)
		)
	);

	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('properties_block', 'properties_block');
	$CI->form->add_block_to_tab('SEO_block', 'SEO_block');

	$CI->form->render_form();
}
?>