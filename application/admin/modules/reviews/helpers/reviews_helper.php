<?php
function helper_reviews_grid_build(Grid $grid)
{
	$grid->add_button('Настройки модуля',  set_url('*/*/*/settings'),
		array(
				'class' => 'addButton'
		)
	);

	$grid->add_button('Добавить отзыв', set_url('*/*/*/add'),
			array(
				'class' => 'addButton'
			)
		);
	$grid->set_checkbox_actions('ID', 'reviews_grid_checkbox',
			array(
				'options' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete' => 'Удалить'
				),
				'name' => 'reviews_grid_select'	
			)
		);
	$grid->add_column(
		array
			(
				'index'		 => 'review',
				'type'		 => 'text',
				'filter'	 => true
			),'Отзыв');
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'type'		 => 'text',
				'tdwidth'	 => '14%',
				'filter'	 => true
			),'Имя');
	$grid->add_column(
		array
			(
				'index'		 => 'email',
				'type'		 => 'text',
				'tdwidth'	 => '14%',
				'filter'	 => true
			),'E-mail');
	$grid->add_column(
		array
			(
				'index'		 => 'create_date',
				'type'		 => 'date',
				'tdwidth'	 => '10%',
				'filter'	 => true
			),'Создан');
	$grid->add_column(
		array
			(
				'index'		 => 'active',
				'type'		 => 'select',
				'tdwidth'	 => '7%',
				'filter'	 => true,
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
			),'Активность');

	$grid->add_column(
		array
		(
			'index'		 => 'is_answer',
			'tdwidth'	 => '6%',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'filter'	 => true
		),'Ответ');

	$grid->add_column(
		array
		(
			'index'		 => 'new_comment',
			'tdwidth'	 => '8%',
			'type'		 => 'select',
			'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
			'filter'	 => true
		),'Новый');

	$grid->add_column(
		array
		(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '9%',
			'option_string' => 'align="center"',
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/*/edit/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/*/delete/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
				)
			),'Действия')
	);
}

function helper_reviews_form_build($data = FALSE, $save_param = '')
{
	$form_id = 'reviews_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Отзывы', $form_id, set_url('*/*/*/save'.$save_param));	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/*/'),
			'options' => array(	)
		)
	);
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить отзыв',
				'href' => set_url('*/*/*/add'),
				'options' => array()
			)
		);
		$CI->form->add_button(
			array(
				'name' => 'Удалить отзыв',
				'href' => set_url('*/*/*/delete'.$save_param),
				'options' => array(
					'class' => 'delete_question'
				)
			)
		);
	}
		
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton'
			)
		)
	);
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton'
			)
		)
	);
	
	$CI->form->add_tab('base', 'Основные данные');
	
	$mdata = FALSE;
	if($data) $mdata['main'] = $data;

	$CI->form->add_group('base', $mdata);
	$lid = $CI->form->group('base')->add_object(
			'fieldset',
			'base_fieldset',
			'Основные данные'
		);
	$CI->form->group('base')->add_object_to($lid,
			'select', 
			'main[active]',
			'Активность :',
			array(
				'options'	=> array('1' => 'Да', '0' => 'Нет')
			)
		);
	$CI->form->group('base')->add_object_to($lid,
			'text', 
			'main[name]',
			'Имя :'
		);
	$CI->form->group('base')->add_object_to($lid,
			'text', 
			'main[email]',
			'E-mail :'
		);

	if($save_param != '')
	{
		$CI->form->group('base')->add_object_to($lid,
			'select',
			'main[id_langs]',
			'Выберите язык :',
			array(
				'option' => array('disabled' => NULL, 'readonly' => NULL),
				'options' => $data['on_langs']
			)
		);
	}
	else
	{
		$CI->form->group('base')->add_object_to($lid,
			'select',
			'main[id_langs]',
			'Выберите язык :',
			array(
				'options' => $data['on_langs']
			)
		);
	}
	
	$CI->form->group('base')->add_object_to($lid,
			'textarea', 
			'main[review]',
			'Текст отзыва:',
			array(
				'option' => array('rows' => '4')
			)
		);

	$CI->form->group('base')->add_object_to($lid,
		'textarea',
		'main[answer]',
		'Текст ответа:',
		array(
			'option' => array('rows' => '8', 'class' => 'ckeditor')
		)
	);
	
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->render_form();
}
?>