<?php 
function helper_langs_grid_build($grid)
{
	$grid->add_button('Добавить язык', set_url('*/add'),
		array(
			'class' => 'addButton'
		)
	);
	
	$grid->set_checkbox_actions('ID_UL', 'langs_grid_checkbox',
		array(
			'options' => array(
				'on' => 'Активность: Да',
				'off' => 'Активность: Нет',
				'on_site_true' => 'Включить на сайте',
				'on_site_false' =>'Выключить на сайте',
				
				),
			'name' => 'langs_grid_select',
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
				'index' => 'code',
				'type' => 'text',
				'tdwidth' => '15%',
				
		), 'Код');
		
	$grid->add_column(
		array(
				'index' =>'name',
				'type' => 'text',
				
		), 'Название');
		
	$grid->add_column(
		array(
				'index' =>'language',
				'type' => 'text',
				
		), 'Язык');
		
	$grid->add_column(
		array(
				'index' => 'default',
				'type' => 'select',
				'tdwidth'	=> '12%',
				
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
		), 'По умолчанию');
	
	$grid->add_column(
		array(
				'index' => 'active',
				'type' => 'select',
				'tdwidth'	=> '12%',
				
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
		), 'Активность');
		
	$grid->add_column(
		array(
				'index' => 'on',
				'type' => 'select',
				'tdwidth'	=> '12%',
				
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да'),
					array(
					'index'		 => 'action',
					'type'		 => 'action',
					'tdwidth'	 => '12%',
					'option_string' => 'align="center"',
					),
		), 'На сайте');
		
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
					'href' 			=> set_url('*/edit/id/$1'),
					'href_values' 	=> array('ID_UL'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать язык')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/delete/id/$1'),
					'href_values' 	=> array('ID_UL'),
					'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить язык')
				)
			)
		), 'Действия');
	
	
}

function helper_langs_form_build($data = array(), $save_param = '')
{
	$form_id = 'langs_add_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавить язык', $form_id, set_url('*/save'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*')
		));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		)
	);
	$CI->form->add_tab('langs_block','Языки');
	
	$select_langs_options = array();
	if(isset($data['select_langs'])) $select_langs_options = $data['select_langs'];
	
	$CI->form->add_group('langs_block');
	
	$lid = $CI->form->group('langs_block')->add_object(
			'fieldset',
			'base_fieldset',
			'Доступные языки'
	);
	
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[id_langs]',
		'Выберите язык:',
		array('options'	=> $select_langs_options)
	);
	
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[active]',
		'Активность:',
		array('options'	=> array('1' => 'Да', '0' => 'Нет'))
	);
	
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[on]',
		'На сайте:',
		array('options'	=> array('0' => 'Нет', '1' => 'Да'))
	);
	
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[default]',
		'По умолчанию:',
		array('options'	=> array('0' => 'Нет', '1' => 'Да'))
	);
	
	$CI->form->add_block_to_tab('langs_block', 'langs_block');
	$CI->form->render_form();

}

function helper_langs_edit_form_build($data = array(), $save_param = '')
{
	$form_id = 'langs_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Редактировать язык', $form_id,  set_url('*/save'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*')
		));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		)
	);
	$CI->form->add_tab('langs_block','Языки');
	
	$select_langs_options = array();
	if(isset($data['select_langs'])) $select_langs_options = $data['select_langs'];
				
	$CI->form->add_group('langs_block');
	
	$lid = $CI->form->group('langs_block')->add_object(
			'fieldset',
			'base_fieldset',
			'Редактирование языка'
	);
	
	$select_langs_options['active'] == 1 ? $options = array('1' => 'Да', '0' => 'Нет') : $options = array('0' => 'Нет', '1' => 'Да');
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[active]',
		'Активность:',
		
		array('options'	=> $options)
	);
	
	$select_langs_options['on'] == 1 ? $options = array('1' => 'Да', '0' => 'Нет') : $options = array('0' => 'Нет', '1' => 'Да');
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[on]',
		'На сайте:',
		array('options'	=> $options)
	);
	
	$select_langs_options['default'] == 1 ? $options = array('1' => 'Да', '0' => 'Нет') : $options = array('0' => 'Нет', '1' => 'Да');
	$CI->form->group('langs_block')->add_object_to($lid,
		'select', 
		'users_langs[default]',
		'По умолчанию:',
		array('options'	=> $options)
	);
	
	$CI->form->add_block_to_tab('langs_block', 'langs_block');
	$CI->form->render_form();

}

?>