<?php
function helper_products_attributes_options_grid_build($grid, $data = false)
{
	$grid->add_button('Добавить опцию атрибута', set_url('*/*/add'),
		array(
			'rel' => 'add',
			'class' => 'addButton'
		));
	$grid->set_checkbox_actions('ID', 'attributes_options_grid_checkbox',
		array(
				'options' 		=> array(
					'on' 		=> 'Активность: Да',
					'off' 		=> 'Активность: Нет',
					'delete' 	=> 'Удалить'
				),'name' => 'attributes_options_grid_select'));
		
	$grid->add_column(
		array
			(
				'index'		 => 'sort',
				'tdwidth'	 => '6%',
				'option_string' => 'align="center"'
			), 'Позиция');
	$grid->add_column(
		array(
				'index' 	=> 'alias',
				'type' 		=> 'text',
				'tdwidth' 	=> '15%',
				'filter' 	=> true
		), 'Идентификатор');
	$grid->add_column(
		array(
				'index' 		=>'name',
				'searchname' 	=> 'name',
				'searchtable' 	=> 'B',
				'type' 			=> 'text',
				'tdwidth' 		=> '30%',
				'filter' 		=> true
		), 'Название опции');
	$grid->add_column(
		array(
				'index' 	=> 'id_m_c_products_attributes',
				'type' 		=> 'select',
				'tdwidth' 	=> '22%',
				'filter' 	=> true,
				'options' 	=> array('' => '')+$data['products_attributes']
		), 'Атрибут (Индификатор - название)');
	$grid->add_column(
		array(
				'index' 	=> 'active',
				'type' 		=> 'select',
				'filter' 	=> true,
				'options' 	=> array('' => '', '0' => 'Нет', '1' => 'Да')
		), 'Активность');
	$grid->add_column(
		array(
				'index'		 	=> 'action',
				'type'		 	=> 'action',
				'tdwidth'	 	=> '10%',
				'option_string' => 'align="center"',
				'actions'	 	=> array(
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/*/edit/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать опцию')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/*/delete/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele', 'title'=>'Удалить опцию')
					)
				)
		), 'Действия');
}

function helper_attributes_options_form_build($data = array(), $save_param = '')
{
	$form_id = 'products_attributes_options_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Опции атрибутов продукции', $form_id, set_url('*/*/save'.$save_param));
	

	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*/'),
		));
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить опцию',
				'href' => set_url('*/*/add'),
				'options' => array(
					'class' => 'addButton'
				)
		));
	
		$CI->form->add_button(
			array(
				'name' => 'Удалить опцию',
				'href' => set_url('*/*/delete'.$save_param),
				'href_values' => array('ID'),
				'options' => array(
					'class' => 'delete_question'
				)
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
	
	$CI->form->add_tab('main_block', 'Основные данные');
	$CI->form->add_tab('desc_block', 'Описание свойств');
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Опция атрибута с указанным индификатором уже существует!'));
	
	$edit_data = FALSE;
	if(isset($data['main'])) $edit_data['main'] = $data['main'];

	$CI->form->add_group('main_block', $edit_data);
	$lid = $CI->form->group('main_block')->add_object(
			'fieldset',
			'base_fieldset',
			'Опции'
		);
		
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[id_m_c_products_attributes]',
		'Атрибут продукции (*):',
		array(
			'options' => $data['products_attributes']
		)
	);
	
	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[alias]',
		'Идентификатор(латиницей) (*):',
		array ('maxlenght' => '50')
	);
	
	$CI->form->group('main_block')->add_object_to($lid,
		'select', 
		'main[active]',
		'Активность (*):',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	
	$edit_data = FALSE;
	if(isset($data['desc'])) $edit_data['desc'] = $data['desc'];
	
	$CI->form->add_group('desc_block', $edit_data, $data['on_langs']);
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
		'Описание свойства :',
		array(
			'option'=>array('rows' => '4') 
		)
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('desc_block', 'desc_block');
	$CI->form->render_form();
}	
?>