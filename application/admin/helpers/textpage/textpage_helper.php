<?php
function helper_textpage_grid_build($grid)
{
	$grid->add_button('Добавить текстовый блок', set_url('*/*/*/add'),
			array(
				'class' => 'addButton'
			)
		);
	$grid->set_checkbox_actions('ID', 'textpage_grid_checkbox',
			array(
				'options' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete' => 'Удалить'
				), 'name' => 'textpage_grid_select',
			)
		);
	/*$grid->add_column(
			array
				(
					'index'		 => 'ID',
					'searchname' => 'id_m_textpage',
					'searchtable'=> 'A',
					'type'		 => 'number',
					'tdwidth'	 => '8%',
					'sortable' 	 => true,
					'filter'	 => true
				), 'ID'
			);*/
	$grid->add_column(
			array
				(
					'index'		 => 'name',
					'type'		 => 'text',
					'filter'	 => true
				), 'Название текстовго блока'
			);
	$grid->add_column(
			array
			(
				'index'		 => 'show',
				'type'		 => 'select',
				'tdwidth'	 => '16%',
				'filter'	 => true,
				'options' => array('' => '', '0' => 'Показывать только заголовок', '1' => 'Показыть весь текст')
			), 'Отображение'
	);
	$grid->add_column(
			array
			(
				'index'		 => 'active',
				'type'		 => 'select',
				'tdwidth'	 => '8%',
				'filter'	 => true,
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
			), 'Активность'
		);
	$grid->add_column(
			array
			(
				'index'		 => 'create_date',
				'type'		 => 'date',
				'tdwidth'	 => '12%',
				'filter'	 => true
			), 'Создан'
	);
	$grid->add_column(
			array
			(
				'index'		 => 'update_date',
				'type'		 => 'update_date',
				'tdwidth'	 => '12%',
				'filter'	 => true
			), 'Обновлен'
		);
	$grid->add_column(
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
						'href' 			=> set_url('*/*/*/edit/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать текстовый блок')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/*/*/delete/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить текстовый блок')
					)
				)
			), 'Действия'
	);
}
function helper_textpage_form_build($data = array(), $save_param = '')
{
	$form_id = 'photo_gallery_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Текстовые блоки', $form_id, set_url('*/*/*/save'.$save_param));
	$CI->form->enable_CKE();
		$CI->form->add_button(
					array(
						'name' 		=> 'Назад',
						'href'		=> set_url('*/*/*/'),
						'options' 	=> array(	)
					)
				);
	
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить текстовый блок',
				'href' => set_url('*/*/*/add'),
				'options' => array(
					'class' => ''
				)
			)
		);
		$CI->form->add_button(
			array(
				'name' => 'Удалить текстовый блок',
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
	$CI->form->add_tab('desc', 'Текст');
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	$CI->form->add_group('base', $data['base']);
	
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
				'options'	=> array('0' => 'Нет', '1' => 'Да')
			)
		);
	
	$CI->form->group('base')->add_object_to($lid,
			'select', 
			'main[show]',
			'Отображение:',
			array(
				'options'	=> array('0' => 'Показывать только заголовок', '1' => 'Показыть весь текст')
			)
		);
	if(!isset($data['desc'])) $data['desc'] = FALSE;
	$CI->form->add_group('desc', $data['desc'], $data['on_langs']);
	$lid = $CI->form->group('desc')->add_object(
			'fieldset',
			'name_fieldset',
			'Название текстовго блока'
		);
	$CI->form->group('desc')->add_object_to($lid,
		'text', 
		'langs[$][name]',
		'Название текстовго блока :',
		array(
			'option'	=> array()
		));
	$lid = $CI->form->group('desc')->add_object(
		'textarea', 
		'langs[$][text]',
		'Текст:',
		array(
			'option'	=> array('class' => 'ckeditor')
		));
	$CI->form->group('desc')->add_object(
		'hidden', 
		'langs[$][id_m_textpage_description]'
	);
	
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->add_block_to_tab('desc', 'desc');
	$CI->form->render_form();
}
?>