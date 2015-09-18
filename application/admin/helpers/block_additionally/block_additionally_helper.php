<?php
function helper_block_additionally_grid_build($grid)
{
	$grid->add_button('Добавить блок', set_url('*/*/*/add'),
			array(
				'class' => 'addButton'
			)
		);
	$grid->set_checkbox_actions('ID', 'block_additionally_grid_checkbox',
		array(
			'options' => array(
						'on' => 'Активность: Да',
						'off' => 'Активность: Нет',
						'delete' => 'Удалить выбраные'
			),
			'name' => 'block_additionally_grid_select'	
		));
	$grid->add_column(
		array(
			'index'		 => 'alias',
			'type'		 => 'text',
			'sortable' 	 => false,
			'filter'	 => true
		),'Идентификатор');
	$grid->add_column(
			array(
				'index'		 => 'active',
				'type'		 => 'select',
				'tdwidth'	 => '15%',
				'sortable' 	 => false,
				'filter'	 => true,
				'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
			),'Активность');

	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '9%',
			'option_string' => 'align="center"',
			'actions'	 => array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/edit/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать блок')
				),
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/delete/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить блок')
				)
			)
			,'Действия'));
}

function helper_block_additionally_form_build($data = array(), $save_param = '')
{
	$form_id = 'block_additionally_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Дополнительный блок', $form_id, set_url('*/*/save'.$save_param));	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/'),
			'options' => array()
		)
	);
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить блок',
				'href' => set_url('*/*/add'),
				'options' => array()
			)
		);
		$CI->form->add_button(
			array(
				'name' => 'Удалить блок',
				'href' => set_url('*/*/delete'.$save_param),
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
	$CI->form->add_tab('base','Данные блока');
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	$CI->form->add_group('base', $data['base']);
	$lid = $CI->form->group('base')->add_object(
		'fieldset',
		'base_fieldset',
		'Активность, Идентификатор'
	);
	$CI->form->group('base')->add_object_to($lid,
		'text', 
		'main[alias]',
		'Идентификатор :'
	);
	$CI->form->group('base')->add_object_to($lid,
		'select',
		'main[active]',
		'Активность :',
		array(
			'options' => array('0' => 'Нет', '1' => 'Да')
		)
	);
	$lid = $CI->form->group('base')->add_object(
		'fieldset',
		'code_fieldset',
		'Наполнение блока'
	);
	$CI->form->group('base')->add_object_to($lid,
		'textarea',
		'main[code]',
		'Код наполнения блока :',
		array(
			'option' => array('cols' => 8)
		)	
	);
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->render_form();
}
?>