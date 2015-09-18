<?php
function helper_site_modules_grid_build($grid)
	{
		$grid->add_button('Добавить модуль', set_url('*/*/add'),
				array(
						'class' => 'addButton'
				)
			);
		$grid->set_checkbox_actions('ID', 'site_modules_grid_checkbox',
					array(
							'options' => array(
								'on' => 'Активность: Да',
								'off' => 'Активность: Нет',
								'delete' => 'Удалить'
							),
							'name' => 'site_modules_grid_select'
						 )
				);
		$grid->add_column(
			array(
					'index' => 'alias',
					'type' => 'text',
					'filter' => true
			), 'Артикул модуля');
			
		$grid->add_column(
			array(
					'index' =>'name',
					'type' => 'text'
	 		), 'Тип модуля');
		
		$grid->add_column(
			array(
					'index' => 'active',
					'type' => 'select',
					'filter' => true,
					'tdwidth' => '10%',
					'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
			), 'Активность');
			
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
							'href' 			=> set_url('moduleinfo/mid/$1'),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_arrow_r', 'title'=>'Управление модулем')
						),
						array(
							'type' 			=> 'link',
							'html' 			=> '',
							'href' 			=> set_url('moduleinfo/mid/$1/settings'),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_settings', 'title'=>'Настройка модуля')
						),
						array(
							'type' 			=> 'link',
							'html' 			=> '',
							'href' 			=> set_url('*/edit/id/$1'),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать модуль')
						),
						array(
							'type' 			=> 'link',
							'html' 			=> '',
							'href' 			=> set_url('*/delete/id/$1'),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить модуль')
						)
					)
				), 'Действия');
	}
	
function helper_site_modules_form_build($data = array(), $save_param = '')
{
	$form_id = 'modules_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Модули сайта', $form_id, set_url('*/save'.$save_param));

	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/'),
			)
		);
	if($save_param != '')
	{
	$CI->form->add_button(
			array(
				'name' => 'Добавить модуль',
				'href' => set_url('*/add')
			)
		);
	$CI->form->add_button(
			array(
				'name' => 'Удалить модуль',
				'href' => set_url('*/delete'.$save_param),
				'options' => array(
					'class' => 'delete_question'
				)
			)
		);
	}

	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
							'id' => 'submit',
							'class' => 'addButton',
			)
		)
	);
	$CI->form->add_tab('base','Основные данные');
	if(!isset($data['base'])) $data['base'] = FALSE;
	$CI->form->add_group('base', $data['base']);
	
	$lid = $CI->form->group('base')->add_object(
			'fieldset',
			'base_fieldset',
			'Основные данные'
		);
		
	$CI->form->group('base')->add_object_to($lid,
		'text',
		'main[alias]',
		'Идентификатор:',
		array ('maxlenght' => '50')
	);
	if ($save_param == '')
	{
	$CI->form->group('base')->add_object_to($lid,
		'select',
		'main[id_modules]',
		'Выбор модуля:',
			array(
				'options' => $data['modules_list']
			)
	);
	}
	$CI->form->group('base')->add_object_to($lid,
		'select', 
		'main[active]',
		'Активность:',
			array(
				'options'	=> array('0' => 'Нет', '1' => 'Да')
			)
	);
	
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->render_form();
}	
?>