<?php
function helper_contacts_grid_build($grid)
{
	$grid->add_button('Добавить контакт',set_url('*/*/*/add'),
			array(
					'class' => 'addButton'
			));
	
	$grid->set_checkbox_actions('ID', 'contacts_grid_checkbox',
			array(
				'options' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete' => 'Удалить выбраных'
				),
				'name' => 'contacts_grid_select'
			));
	
	/*$grid->add_column(
			array
				(
					'index'		 => 'ID',
					'searchname' => 'id_m_contacts',
					'searchtable'=> 'A',
					'type'		 => 'number',
					'tdwidth'	 => '10%',
					'sortable' 	 => true,
					'filter'	 => true
				),'ID');*/
	$grid->add_column(
			array
				(
					'index'		 => 'name',
					'type'		 => 'text',
					'filter'	 => true
				),'Название блока');
	$grid->add_column(
			array
				(
					'index'		 => 'email',
					'type'		 => 'text',
					'tdwidth'	 => '20%',
					'filter'	 => true
				),'E-mail');
	$grid->add_column(
		array
			(
				'index'		 => 'active',
				'type'		 => 'select',
				'options'	 => array(''=>'', '0'=>'Нет', '1'=>'Да'),
				'tdwidth'	 => '9%',
				'filter'	 => true
			),'Активность');
	$grid->add_column(
		array
			(
				'index'		 => 'show_form',
				'type'		 => 'select',
				'options'	 => array(''=>'', '0'=>'Нет', '1'=>'Да'),
				'tdwidth'	 => '9%',
				'filter'	 => true
			),'Форма О.С.');		

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
			),'Действия'));
}
function helper_contacts_form_build($data = array(), $save_param = '')
{
	$form_id = 'contacts_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Контакты', $form_id, set_url('*/*/*/save'.$save_param));
	$CI->form->enable_CKE();
	$CI->form->add_button(
				array(
					'name' => 'Назад',
					'href' => set_url('*/*/*/'),
					'options' => array(	)
				));
	
	if($save_param != '')
	{
		$CI->form->add_button(
					array(
						'name' => 'Добавить контакт',
						'href' => set_url('*/*/*/add'),
						'options' => array()
					));
		$CI->form->add_button(
					array(
						'name' => 'Удалить контакт',
						'href' => set_url('*/*/*/delete'.$save_param),
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
	$CI->form->add_tab('base','Основные данные');
	
	$CI->form->add_validation('main[email]', array('email' => 'true'));
	
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
				'options'	=> array('1'=>'Да', '0'=>'Нет')
			)
		);
	$CI->form->group('base')->add_object_to($lid,
			'select', 
			'main[show_form]',
			'Форма обратной связи :',
			array(
				'options'	=> array('1'=>'Да', '0'=>'Нет')
			)
		);	
		
	$CI->form->group('base')->add_object_to($lid,
				'text', 
				'main[email]',
				'E-mail :',
				array(
					'option'	=> array('maxlength' => '100')
				)
			);
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	$CI->form->add_group('text', $data['base'], $data['on_langs']);
	$lid = $CI->form->group('text')->add_object(
				'fieldset',
				'name_fieldset',
				'Текст'
			);
	$CI->form->group('text')->add_object_to($lid,
				'text', 
				'langs[$][name]',
				'Название блока :',
				array(
					'option'	=> array('maxlength' => '100')
				)
			);		
	$CI->form->group('text')->add_object_to($lid,
				'textarea', 
				'langs[$][text]',
				'Текстовое наполнение :',
				array(
					'option'	=> array('class' => 'ckeditor')
				)
			);
		
	$lid = $CI->form->group('text')->add_object(
				'hidden', 
				'langs[$][id_m_contacts_description]'
			);
		
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->add_block_to_tab('base', 'text');
	$CI->form->render_form();
}
?>