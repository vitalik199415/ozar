<?php
function helper_menu_grid_build($Grid)
{
	$Grid-> addButton(
			array(
				'name' => 'Добавить меню',
				'href' => set_url('*/add'),
				'options' => array(
					'rel' => 'add',
					'class' => 'addButton'
				)
			)
		);
	$Grid->setCheckboxActions(
			array(
				'actions' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete_all' => 'Удаление меню со всеми ее подменю'
				),
				'select_name' => 'menu_selected',
				'index' => 'ID',
				'checkbox_name' => 'menu_checkbox'
				
			)
		);
	$Grid->addGridColumn(
			array(
				'Позиция',
				array
				(
					'index'		 => 'sort',
					'tdwidth'	 => '5%',
					'option_string' => 'align="center"'
				)
			)
		);
	$Grid->addGridColumn(
			array(
				'ID',
				array
				(
					'index'		 => 'ID',
					'tdwidth'	 => '7%',
					'filter'	 => true
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
					'tdwidth'	 => '10%'
					
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
			'URL',
			array
			(
				'index'		 => 'url',
				'tdwidth'	 => '11%'
			)
		)
	);
	$Grid->addGridColumn(
		array(
			'Кликабельно',
			array
			(
				'index'		 => 'clickable',
				'tdwidth'	 => '9%',
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
				'sortable' 	 => false,
				'filter'	 => false,
				'actions'	 => array(
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','menu_modules','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_plus', 'title'=>'Добавить модули к меню')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','edit','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать меню')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url(array('*','*','delete','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить меню')
					)
					
				)
			)
		)
	);
	return $Grid;
}

function menu_form_build($data, $save_param = '')
{
	$form_id = 'menu_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление/Редактирование меню', $form_id, set_url('*/save'.$save_param));
	//$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/'),
			'options' => array( ),
		));
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить меню',
				'href' => set_url('*/add')
			));
		$CI->form->add_button(
			array(
				'name' => 'Удалить меню',
				'href' => set_url('*/delete'.$save_param),
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
	
	$CI->form->add_tab('main_block'		, 'Основные атрибуты');
	if($save_param == '')
	{
		$CI->form->add_tab('parent_block'	, 'Родительское меню');
	}	
	$CI->form->add_tab('SEO_block'		, 'SEO меню');
	
	if($save_param == '')
	{
		$CI->form->add_validation('menu[url]', array('remote' => '{url:"'.set_url('*/check_url').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('menu[url]', array('remote' => '{url:"'.set_url('*/check_url'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('menu[url]', array('remote' => 'Меню с указанным сегментом URL уже существует!'));
	
	$session_temp = $CI->session->flashdata($form_id);
	
	if(!isset($data['menu'])) $data['menu'] = FALSE;
	if($session_temp)
	{
		$data['menu']['menu'] = $session_temp['menu'];
	}
	
	$CI->form->add_group('main_block', $data['menu']);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'menu[active]', 
		'Активность :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'menu[clickable]', 
		'Кликабельность :',
		array(
			'options'	=> array('0'=>'Нет', '1'=>'Да')
		)
	);
	$CI->form->group('main_block')->add_object(
			'text',
			'menu[url]',
			'Сегмент URL :',
			array(
				'option'	=> array('maxlenght' => '100')
			)
		);
	
	
	//Блок описание категории
	if(!isset($data['menu_desc'])) $data['menu_desc'] = FALSE;
	if($session_temp)
	{
		$data['menu_desc']['menu_desc'] = $session_temp['menu_desc'];
	}
	$CI->form->add_group('desc_block', $data['menu_desc'], $data['on_langs']);

	$CI->form->group('desc_block')->add_object(
			'text',
			'menu_desc[$][name]',
			'Название меню :',
			array(
				'option'	=> array('maxlenght' => '100')
			)
		);	
	//------------Блок описание категории----------------
		$CI->form->group('desc_block')->add_object(
			'hidden',
			'menu_desc[$][id_m_menu_description]'
		);

	
	//SEO
	$CI->form->add_group('SEO_block', $data['menu_desc'], $data['on_langs']);
		$CI->form->group('SEO_block')->add_object(
			'text',
			'menu_desc[$][seo_title]',
			'Title : ',
			array(
				'option'	=> array('maxlenght' => '200')
			)
		);
		$CI->form->group('SEO_block')->add_object(
			'textarea',
			'menu_desc[$][seo_description]',
			'Description : ',
			array(
				'option' 	=> array(
					'rows' => '3'
				)
			)
		);
		$CI->form->group('SEO_block')->add_object(
			'text',
			'menu_desc[$][seo_keywords]',
			'Keywords : ',
			array(
				'option'	=> array('maxlenght' => '100')
			)
		);
		/*$CI->form->group('SEO_block')->add_object(
			'textarea',
			'langs[$][seo_sky]',
			'Облако тегов : ',	
			array(
				'option' 	=> array(
					'class' => 'ckeditor'
				)
			)
		);*/
	//-------------SEO-----------
	//Parets Block
	if($save_param == '')
	{
	$CI->form->add_group('parent_block', $data['menu']);
	$CI->form->group('parent_block')->add_object(
		'select',
		'menu[level]',
		'Выберите уровень :',
		array(
			'options' => $data['data_max_level'],
			'option' => array('id' => 'parent_level')
		)
	);
	$CI->form->group('parent_block')->add_object(
			'html',
			'<div id="parent_categories">'
		);
	if($data['data_parents'])
	{
		$CI->form->group('parent_block')->add_object(
			'select',
			'menu[id_parent]',
			'Выберите родительскую категорию :',
			array(
				'options' => array('' => 'Корневая категория 1-го уровня') + $data['data_parents']
			)
		);
	}
	$CI->form->group('parent_block')->add_object(
			'html',
			'</div>'
		);
	$URI = $CI->uri->uri_to_assoc(4);
	if(isset($URI['id']) && ($cid = intval($URI['id']))>0)
	{
		$js_idcat = 'var cat_id = '.$cid.';';
	}
	else
	{
		$js_idcat = 'var cat_id = false;';
	}
	
	$js_categories = '
		function updateParrents(data)
		{
			$("#'.$form_id.' #parent_categories").html(data);
		}
		$("#'.$form_id.' #parent_level").live("change", function()
		{
			if(cat_id != false)
			{
				var data = {level: $(this).val(), id: cat_id};
			}
			else
			{
				var data = {level: $(this).val()};
			}
			$.ajaxAG(
				{
					url: "'.set_url('*/load_menu').'",
					type: "POST",
					data: data,
					success: function(d){updateParrents(d)}
				}
			);
		});
	';
	
	$CI->form->group('parent_block')->add_object(
		'js',
		$js_idcat
	);
	$CI->form->group('parent_block')->add_object(
		'js',
		$js_categories
	);	
	}	
	//-------------Parets Block-----------
	
	$CI->form->add_block_to_tab('main_block'		, 'main_block');
	$CI->form->add_block_to_tab('main_block'		, 'desc_block');
	if($save_param == '')
	{
		$CI->form->add_block_to_tab('parent_block'		, 'parent_block');
	}	
	$CI->form->add_block_to_tab('SEO_block'			, 'SEO_block');
	
	$CI->form->render_form();
}

function helper_menu_modules_form($data, $id)
{
	$form_id = 'menu_add_modules_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление модулей к меню', $form_id, set_url('*/modules_save/id/'.$id));
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/'),
			'options' => array( ),
		));
		
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton',
			)
		));

	$CI->form->add_tab('main_block'		, 'Модули меню');
	$CI->form->add_group('main_block');
	if(isset($data['checkbox_checked'])) 
	{
		$main_array['values'] = $data['checkbox_checked'];
		$main_array['id'] = $id;
		$CI->form->group('main_block')->add_view('menu/checked_modules', $main_array);
		if(isset($data['checkbox']) && count($data['checkbox'])>0)
		{
			$lid = $CI->form->group('main_block')->add_object(
				'fieldset',
				'noch',
				'Не выбранные'
				);
			foreach($data['checkbox'] as $ms)
			{	
				$CI->form->group('main_block')->add_object_to($lid,
					'checkbox',
					'id_users_modules['.$ms['id_users_modules'].']',
					$ms['alias'],
					array(
					'value' => $ms['id_users_modules']
					)
				);
			
			}	
		}
	}
	else
	{
		
		if(isset($data['checkbox']) && count($data['checkbox'])>0)
		{
			$lid = $CI->form->group('main_block')->add_object(
				'fieldset',
				'noch',
				'Не выбранные'
				);
			foreach($data['checkbox'] as $ms)
			{	
					$CI->form->group('main_block')->add_object_to($lid,
						'checkbox',
						'id_users_modules['.$ms['id_users_modules'].']',
						$ms['alias'],
						array(
						'value' => $ms['id_users_modules']
						)
					);
			
			}	
		}
	}
	$CI->form->group('main_block')->add_object(
			'hidden',
			'save',
			'',
			array(
				'value' => '123'
			)
		);
	
	$CI->form->add_block_to_tab('main_block'	, 'main_block');
	$CI->form->render_form();
}

function load_menu($data)
{
	$CI = & get_instance();
	$CI->load->library('form');
	
	$CI->form->add_group('cat_block');
	$CI->form->group('cat_block')->add_object(
			'select',
			'menu[id_parent]',
			'Выберите родительское меню :',
			array(
				'options' => array('' => 'Выберите родительское меню') + $data
			)
		);
	echo $CI->form->group('cat_block')->block_to_HTML();
}
?>