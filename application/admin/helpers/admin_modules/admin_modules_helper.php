<?php
function admin_modules_grid_build($Grid)
{
	$Grid-> addButton(
			array(
				'name' => 'Добавить',
				'href' => setUrl('*/add'),
				'options' => array(
					'rel' => 'add',
					'class' => 'addButton'
				)
			)
		);
		/*$Grid->SetCheckboxActions(
			array(
				 	'actions' => array(
						'on' => 'Активность: Да',
						'off' => 'Активность: Нет',
						'delete' => 'Удалить'
					),
					'select_name' => 'admin_grid_select',
					'index' => 'ID',
					'checkbox_name' => 'admin_grid_checkbox'
				 )
		);*/
		$Grid -> addGridColumn(
			array(
				 	'ID',
					array
						(
							'index' => 'ID',
							'searchname' => 'id_modules',
							'searchtable' => 'A',
							'type' => 'number',
							'tdwidth' => '10%',
							'sortable' => true,
							'filter' => true
						 )
				 )
		);
		
		$Grid -> addGridColumn(
			array (
				  	'Артикул',
						array (
								'index' => 'alias',
								'type' => 'text',
								'tdwidth' => '20%',
								'sortable' => true,
								'filter' => true
								)
				  )
		);
		$Grid -> addGridColumn(
			array(
					'Название',
						array(
								'index' =>'name',
								'searchname' => 'name',
								'searchtable' => 'B',
								'type' => 'text',
								'filter' => true,
								'sortable' => true
							 )
				 )
		);
		
		$Grid -> addGridColumn(
			array(
					'Ранг',
						array(
								'index' => 'rang',
								'type' => 'select',
								'tdwidth' => '7%',
								'filter' => true,
								'options' => array('' => 'Ранг', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5')
							 )
				 )
		);
		
		$Grid -> addGridColumn(
			array(
					'Активность',
						array(
								'index' => 'active',
								'type' => 'select',
								'tdwidth' => '12%',
								'sortable' => true,
								'filter' => true,
								'options' => array('' => 'Активность', '0' => 'Нет', '1' => 'Да')
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
								'href' 			=> setUrl('*/*/edit/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать опцию')
							),
							array(
								'type' 			=> 'link',
								'html' 			=> '',
								'href' 			=> setUrl('*/*/delete/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_detele', 'title'=>'Удалить опцию')
							)
						)
					)
			)
		);
		return $Grid;
}

function admin_modules_form_build($data = array(), $save_param = '')
{
	$Form = new Agform('Опции', 'admin_add_edit_form', setUrl('*/save'.$save_param));
	$Form -> addButton(
				array(
					'name' => 'Назад',
					'href' => setUrl('*/'),
					'options' => array( ),
				 )
			);
	if($save_param != '')
	{
		$Form-> addButton(
			array(
				'name' => 'Добавить',
				'href' => setUrl('*/add'),
				'options' => array(
					'class' => 'addButton'
				)
			)
		);
	
	$Form-> addButton(
			array(
				'name' => 'Удалить',
				'href' => setUrl('*/delete'.$save_param),
				'href_values' => array('ID'),
				'options' => array(
					'class' => 'delete_question'
				)
			)
		);
	}
	
	$Form-> addButton(
		array(
			'name' => 'Сохранить и продолжить редактирование',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton'
			)
		)
	);
	
	$Form-> addButton(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
							'id' => 'submit',
							'class' => 'addButton',
			)
		)
	);
	
	$Form->addTabs('base','Опции');
	$Form->addTabs('desc', 'Описание опции');
	$array_tabs_base = array(
		'attributes_options[alias]' => 'alias'
	);
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	$admin_base = new Agform_block($data['base']);
	$lid = $admin_base->addObject(
			'fieldset',
			'base_fieldset',
			'Опции'
		);
		
	
	
	$admin_base->addObjectTo($lid,
		'text',
		'main[alias]',
		'Артикул:',
		array ('maxlenght' => '50')
	);
	
	$admin_base->addObjectTo($lid,
		'select',
		'main[rang]',
		'Ранг:',
		array(
			'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5')
			 )
	);
	
	$admin_base->addObjectTo($lid,
		'select', 
		'main[active]',
		'Активность :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	if(!isset($data['desc'])) $data['desc'] = FALSE;
	$admin_name = new Agform_block($data['desc'],$data['on_langs']);
	$lid = $admin_name->addObject(
			'fieldset',
			'name_fieldset',
			'Название'
		);
	
	$admin_name->addObjectTo($lid,
		'text', 
		'langs[$][name]',
		'Название:',
		array(
			'option'	=> array()
		)
	);
	
	if (!isset($data['desc'])) $data['desc'] = FALSE;
	$admin_description = new Agform_block($data['desc'], $data['on_langs']);
		$admin_description-> addObject(
			'textarea', 
			'langs[$][description]',
			'Короткое описание:',
			array(
				'option'	=> array('rows' => '3')
				 )
		);
	$admin_description-> addObject(
			'hidden', 
			'langs[$][id_modules_description]'
		);	
	
	$Form->addBlockToTabs('base', $admin_base);
	$Form->addBlockToTabs('base', $admin_name);
	$Form->addBlockToTabs('desc', $admin_description);
	$Form->renderForm();
}
?>