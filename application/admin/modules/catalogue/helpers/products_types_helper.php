<?php
function helper_products_types_grid_build(Grid $grid)
{

	$grid->add_button('Добавить группу фильтров', set_url('*/*/add'));
	
	$grid->set_checkbox_actions('ID', 'products_types_grid_checkbox',
		array(
			'options' => array(
				'on'	=> 'Активность: Да',
				'off'	=> 'Активность: Нет',
				'delete'=> 'Удалить'
			),
			'name'	=> 'products_types_grid_select'
		)
	);
	
	$grid->add_column(
		array(
			'index'		 => 'sort',
			'tdwidth'	 => '6%',
			'option_string' => 'align="center"'
		), 'Позиция');

	$grid->add_column(
		 array(
			 'index'	=> 'show_id',
			 'type'		=> 'text',
			 'tdwidth'	=> '5%',
			 'filter'	=> true
		 ), 'ID');

	$grid->add_column(
		array(
			'index'		=> 'alias',
			'type'		=> 'text',
			'filter'	=> true
		), 'Идентификатор');
	
	$grid->add_column(
		array(
			'index'		=> 'name',
			'type'		=> 'text',
			'filter'	=> 	true
		), 'Название');

	$grid->add_column(
		 array(
			 'index'	=> 'type_kind',
			 'type'		=> 'select',
			 'tdwidth'	=> '12%',
			 'filter'	=> true,
			 'options'	=> array('' => '') + Mproducts_types :: get_filters_types()
		 ), 'Тип');

	$grid->add_column(
		array(
			'index'		=> 'active',
			'type'		=> 'select',
			'tdwidth'	=> '12%',
			'filter'	=> true,
			'options'	=> array('' => '', '0' => 'Нет', '1' => 'Да')
		), 'Активность');
	
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
					'href'			=> set_url('*/*/type_properties/type_id/$1'),
					'href_values'	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r', 'title'=>'Перейти в группу')
				),
				array(
					'type'			=> 'link',
					'html'			=> '',
					'href'			=> set_url('*/*/edit/id/$1'),
					'href_values'	=> array('ID'),
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

function helper_products_types_form_build($data = array(), $save_param = '')
{
	$form_id = 'products_types_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Группы фильтров продукции', $form_id, set_url('*/*/save'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	if($save_param !='')
	{
		$CI->form->add_button(
			array(
				'name'		=> 'Добавить группу фильтров',
				'href'		=> set_url('*/*/add'),
			));
		
		$CI->form->add_button(
			array(
				'name'		=> 'Удалить группу',
				'href'		=> set_url('*/*/delete'.$save_param),
				'options'	=> array('class'=>'delete_question')
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
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('remote' => '{url:"'.set_url('*/*/check_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Группа свойств с указанным идентификатором уже существует!'));
	
	$CI->form->add_tab('main_block', 'Основные данные');
	$CI->form->add_tab('desc_block', 'Описание группы');
	$CI->form->add_tab('SEO_block', 'SEO группы');


	$PMdata['main'] = FALSE;
	if(isset($data['main'])) $PMdata['main'] = $data['main'];

	$CI->form->add_group('main_block', $PMdata);

	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Основные данные'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[alias]', 
		'Идентификатор :'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[active]',
		'Активнось (*):',
		array(
			'options' => array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[type_kind]',
		'Тип группы (*):',
		array(
			'options' => Mproducts_types :: get_filters_types()
		)
	);

	$PDdata['desc'] = FALSE;
	if(isset($data['desc'])) $PDdata['desc'] = $data['desc'];

	$CI->form->add_group('desc_block', $PDdata, $data['on_langs']);
	$lid = $CI->form->group('desc_block')->add_object(
		'fieldset',
		'name_fieldset',
		'Описание группы'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'text',
		'desc[$][name]',
		'Название группы :'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'textarea',
		'desc[$][description]',
		'Описание группы :',
		array(
			'option' => array('rows' => 3)
		)
	);

	$CI->form->add_group('SEO_block', $PDdata, $data['on_langs']);
	$lid = $CI->form->group('SEO_block')->add_object(
		'fieldset',
		'name_fieldset',
		'SEO типа свойств'
	);
	$CI->form->group('SEO_block')->add_object_to($lid,
		'text',
		'desc[$][seo_title]',
		'Title : '
	);
	$CI->form->group('SEO_block')->add_object_to($lid,
		'textarea',
		'desc[$][seo_description]',
		'Description : ',
		array(
			'option'	=> array('rows' => '4')
		)
	);
	$CI->form->group('SEO_block')->add_object_to($lid,
		'text',
		'desc[$][seo_keywords]',
		'Keywords : '
	);

	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('desc_block', 'desc_block');
	$CI->form->add_block_to_tab('SEO_block', 'SEO_block');

	$CI->form->render_form();
}

function helper_type_properties_grid_build(Grid $grid, $type_id)
{
	$grid->add_button('Группы фильтров', set_url('*/*'));

	$grid->add_button('Добавить свойство фильтра', set_url('*/*/add_property/type_id/'.$type_id),
		array(
			'class' => 'addButton'
		)
	);

	$grid->set_checkbox_actions('ID', 'products_properties_grid_checkbox',
		array(
			'options' => array(
				'on'		=> 'Активность: Да',
				'off'		=> 'Активность: Нет',
				'delete'	=> 'Удалить выбраные'
			),
			'name'	=> 'products_properties_grid_select'
		)
	);

	$grid->add_column(
		array
		(
			'index'		 => 'sort',
			'tdwidth'	 => '6%',
			'option_string' => 'align="center"'
		), 'Позиция');

	$grid->add_column(
		 array(
			 'index'		=> 'show_id',
			 'type' 		=> 'text',
			 'tdwidth'		=> '6%',
			 'filter' 		=> TRUE
		 ), 'ID');

	$grid->add_column(
		array(
			'index'			=> 'alias',
			'type' 			=> 'text',
			'tdwidth'		=> '15%',
			'filter' 		=> TRUE
		), 'Идентификатор');

	$grid->add_column(
		array(
			'index'			=> 'name',
			'type' 			=> 'text',
			'filter' 		=> TRUE
		), 'Название свойста');

	/*$grid->add_column(
		array(
			'index' 		=> 'id_m_c_products_types',
			'type' 			=> 'select',
			'tdwidth' 		=> '25%',
			'filter' 		=> TRUE,
			'sortable' 		=> TRUE,
			'options' 		=> array('' => '')+$data['types']
		), 'Группа свойства продукции');*/

	$grid->add_column(
		array(
			'index' 		=> 'active',
			'type' 			=> 'select',
			'tdwidth' 		=> '8%',
			'filter' 		=> TRUE,
			'options' 		=> array( '' => '', '0' => 'Нет', '1' => 'Да')
		), 'Активность');

	$grid->add_column(
		array(
			'index' 		=> 'action',
			'type' 			=> 'action',
			'tdwidth' 		=> '10%',
			'option_string' => 'align = "center"',
			'actions' 		=> array(
				array(
					'type' 			=> 'link',
					'html'			=> '',
					'href' 			=> set_url('*/*/edit_property/type_id/'.$type_id.'/prop_id/$1'),
					'href_values' 	=> array('ID'),
					'options' 		=> array('class'=>'icon_edit', 'title'=>'Редактировать свойство')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/delete_property/type_id/'.$type_id.'/prop_id/$1'),
					'href_values' 	=> array('ID'),
					'options' 		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить свойство')
				)
			)
		), 'Действия');
}

function helper_add_edit_property_form_build($data = array(), $save_param = '')
{
	$form_id = 'products_property_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Свойства продуктов', $form_id, set_url('*/*/save_property/type_id/'.$data['type_id'].$save_param));

	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*/type_properties/type_id/'.$data['type_id']),
		));

	$CI->form->add_button(
		array(
			'name' 		=> 'Сохранить и продолжить редактирование',
			'href' 		=> '#',
			'options' 	=> array(
				'id' 	=> 'submit_back'
			)
		));

	$CI->form->add_button(
		array(
			'name' 		=> 'Сохранить',
			'href' 		=> '#',
			'options' 	=> array(
				'id'	=> 'submit'
			)
		));

	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('remote' => '{url:"'.set_url('*/*/check_property_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('remote' => '{url:"'.set_url('*/*/check_property_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Свойство фильтра с указанным индификатором уже существует!'));

	$CI->form->add_tab('main_block', 'Основные данные');
	$CI->form->add_tab('desc_block', 'Описание свойства');
	$CI->form->add_tab('SEO_block', 'SEO свойства');

	$filters_types = Mproducts_types::get_filters_types();
	$type = $filters_types[$data['type_kind']];
	$PMdata['main'] = FALSE;
	if(isset($data['main'])) $PMdata['main'] = $data['main'];

	$CI->form->add_group('main_block', $PMdata);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Основные данные'
	);

	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'type',
		'Тип фильтра :',
		array('option' => array('value' => $type, 'readonly' => NULL))
	);

	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[alias]',
		'Идентификатор (латиницей) :'
	);

	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[active]',
		'Активность (*):',
		array(
			'options' => array('1' => 'Да', '0' => 'Нет')
		)
	);

	$PDdata['desc'] = FALSE;
	if(isset($data['desc'])) $PDdata['desc'] = $data['desc'];

	$CI->form->add_group('desc_block', $PDdata, $data['on_langs']);
	$lid = $CI->form->group('desc_block')->add_object(
		'fieldset',
		'name_fieldset',
		'Название свойства'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'text',
		'desc[$][name]',
		'Название свойства :'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'textarea',
		'desc[$][description]',
		'Описание :',
		array('option' => array('rows' => 3))
	);

	$CI->form->add_group('SEO_block', $PDdata, $data['on_langs']);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'desc[$][seo_title]',
		'Title : '
	);
	$CI->form->group('SEO_block')->add_object(
		'textarea',
		'desc[$][seo_description]',
		'Description : ',
		array(
			'option'	=> array('rows' => '4')
		)
	);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'desc[$][seo_keywords]',
		'Keywords : '
	);

	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('desc_block', 'desc_block');
	$CI->form->add_block_to_tab('SEO_block', 'SEO_block');

	if($data['type_kind'] == 'color' || $data['type_kind'] == 'color_name')
	{
		$CI->form->add_group('color_block', $PMdata);
		$CI->form->group('color_block')->add_object(
			'text',
			'main[id_color]',
			'Выберите цвет :',
			array(
				'option'	=> array('maxlength' => '6', 'value' => 'FFFFFF', 'id' => 'id_color_picker', 'style' => 'float:right; width:94%;', 'readonly' => NULL)
			)
		);
		$js = '
			$("#'.$form_id.'").find("#id_color_picker").jPicker();
		';
		$CI->form->group('color_block')->add_object(
			'js',
			$js
		);
		$CI->form->add_block_to_tab('main_block', 'color_block');
	}

	if($data['type_kind'] == 'image')
	{
		$CI->form->add_group('file_block');
		$CI->form->group('file_block')->add_object(
			 'file',
			 'property_image',
			 'Выберите файл :'
		);
		if(isset($data['property_image']))
		{
			$CI->form->group('file_block')->add_object(
				'html',
				'<div align="left"><img src='.$data['property_image'].' style="border:2px solid #666666;"></div>'
			);
		}
		$CI->form->add_block_to_tab('main_block', 'file_block');
	}

	$CI->form->render_form();
}
?>