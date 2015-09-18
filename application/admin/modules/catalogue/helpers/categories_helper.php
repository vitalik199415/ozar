<?php
function categories_grid_build($Grid)
{
	$Grid-> addButton(
			array(
				'name' => 'Добавить категорию',
				'href' => set_url('*/*/add'),
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
					'delete_all' => 'Удаление категории со всеми ее подкатегориями'
				),
				'select_name' => 'categories_selected',
				'index' => 'ID',
				'checkbox_name' => 'categories_checkbox'
				
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
						'tdwidth'	 => '6%'
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
						'tdwidth'	 => '8%'
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
			'Создана',
			array
				(
					'index'		 => 'create_date',
					'tdwidth'	 => '10%'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Обновлена',
			array
				(
					'index'		 => 'update_date',
					'tdwidth'	 => '10%'
				)
		)
	);
	$Grid->addGridColumn(
		array(
			'Активность',
			array
				(
					'index'		 => 'active',
					'tdwidth'	 => '6%'
				)
		)
	);
	
	$Grid->addGridColumn(
		array(
			'Видимая',
			array
				(
					'index'		 => 'active',
					'tdwidth'	 => '6%'
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
							'href' 			=> set_url(array('*','*','edit','id','$1')),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
						),
						array(
							'type' 			=> 'link',
							'html' 			=> '',
							'href' 			=> set_url(array('*','*','delete','id','$1')),
							'href_values' 	=> array('ID'),
							'options'		=> array('class'=>'icon_detele delete_question')
						)
					)
				)
		)
	);
	return $Grid;
}

function helper_categories_form_build($data, $save_param = '')
{
	$form_id = 'categories_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление/Редактирование категории', $form_id, set_url('*/*/save'.$save_param));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/')
		));
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' => 'Добавить категорию',
				'href' => set_url('*/*/add')
			));
		$CI->form->add_button(
			array(
				'name' => 'Удалить категорию',
				'href' => set_url('*/*/delete'.$save_param),
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
							'class' => 'addButton',
			)
		));
	
	$CI->form->add_tab('main_block'		, 'Основные атрибуты');
	$CI->form->add_tab('desc_block'		, 'Описание категории');
	if($save_param == '')
	{
		$CI->form->add_tab('parent_block'	, 'Родительская категория');
	}	
	$CI->form->add_tab('SEO_block'		, 'SEO категории');
	
	
	if($save_param == '')
	{
		$CI->form->add_validation('categories[url]', array('remote' => '{url:"'.set_url('*/*/check_url').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('categories[url]', array('remote' => '{url:"'.set_url('*/*/check_url'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('categories[url]', array('remote' => 'Категория с указанным сегментом URL уже существует!'));
	
	$session_temp = $CI->session->flashdata($form_id);
	
	if(!isset($data['categories'])) $data['categories'] = FALSE;
	/*if($session_temp)
	{
		$data['categories']['categories'] = $session_temp['categories'];
	}*/
	
	$CI->form->add_group('main_block', $data['categories']);
	$CI->form->group('main_block')->add_object(
		'text',
		'categories[url]',
		'Сегмент URL :'
	);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'categories[active]', 
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'categories[show]', 
		'Показывать на сайте :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'categories[open]', 
		'Развернутая категория :',
		array(
			'options'	=> array('0' => 'Нет', '1' => 'Да')
		)
	);

	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'customers_types_fieldset',
		'Правила показа продукции'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'permissions[permission]',
		'Правила показа :',
		array(
			'options'	=> array('0' => 'Показывать всем посетителям', '1' => 'Показывать только зарегистрированым покупателям', '2' => 'Показать только выбранным группам покупателей'),
			'option'	=> array('class' => $form_id.'_customers_types_select')
		)
	);

	$display = 'none';
	$CI->form->group('main_block')->add_object_to($lid,
		'html',
		'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_customers_types">'
	);
	foreach($data['data_customers_types'] as $key => $ms)
	{
		$CI->form->group('main_block')->add_object_to($lid,
			'checkbox',
			'permissions[m_u_types]['.$key.']',
			$ms.' :',
			array(
				'value' => $key
			)
		);
	}
	$CI->form->group('main_block')->add_object_to($lid,
		'html',
		'</div>'
	);

	$js = '
	$("#'.$form_id.'").find(".'.$form_id.'_customers_types_select").each(function()
	{
		if($(this).val() == 2)
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "block");
		}
	});
	$("#'.$form_id.'").on("change", ".'.$form_id.'_customers_types_select", function()
	{
		if($(this).val() == 2)
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "block");
		}
		else
		{
			$(this).parents("fieldset").find("#'.$form_id.'_customers_types").css("display", "none");
		}
	});
	';
	$CI->form->group('main_block')->add_object(
		'js',
		$js
	);
	
	
	
	//Блок описание категории
	if(!isset($data['categories_desc'])) $data['categories_desc'] = FALSE;
	if($session_temp)
	{
		$data['categories_desc']['categories_desc'] = $session_temp['categories_desc'];
	}
	$CI->form->add_group('desc_block', $data['categories_desc'], $data['on_langs']);
	
	$CI->form->group('desc_block')->add_object(
			'text',
			'categories_desc[$][name]',
			'Название категории :',
			array(
				'option'	=> array('maxlenght' => '100')
			)
		);
	
	$CI->form->group('desc_block')->add_object(
		'textarea',
		'categories_desc[$][description]',
		'Описание категории :',
		array(
			'option' 	=> array(
				'class' => 'ckeditor'
			)
		)
	);
	$CI->form->group('desc_block')->add_object(
		'hidden',
		'categories_desc[$][id_m_c_categories_description]',
		'Описание категории :'
	);
	//------------Блок описание категории----------------
	//SEO
	$CI->form->add_group('SEO_block', $data['categories_desc'], $data['on_langs']);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'categories_desc[$][seo_title]',
		'Title : ',
		array(
			'option'	=> array('maxlenght' => '200')
		));
	$CI->form->group('SEO_block')->add_object(
		'textarea',
		'categories_desc[$][seo_description]',
		'Description : ',
		array(
			'option' 	=> array(
				'rows' => '3'
			)
		));
	$CI->form->group('SEO_block')->add_object(
		'text',
		'categories_desc[$][seo_keywords]',
		'Keywords : ',
		array(
			'option'	=> array('maxlenght' => '100')
		));
	//-------------SEO-----------
	//Parets Block
	if($save_param == '')
	{
	$CI->form->add_group('parent_block', $data['categories']);
	$CI->form->group('parent_block')->add_object(
		'select',
		'categories[level]',
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
			'categories[id_parent]',
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
					url: "'.set_url('*/*/load_categories').'",
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
	$CI->form->add_block_to_tab('desc_block'		, 'desc_block');
	if($save_param == '')
	{
		$CI->form->add_block_to_tab('parent_block'		, 'parent_block');
	}	
	$CI->form->add_block_to_tab('SEO_block'			, 'SEO_block');
	
	$CI->form->render_form();
}

function helper_load_categories($data)
{
	$form_id = 'categories_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	
	$CI->form->add_group('cat_block');
	$CI->form->group('cat_block')->add_object(
			'select',
			'categories[id_parent]',
			'Выберите родительскую категорию :',
			array(
				'options' => array('' => 'Выберите родительскую категорию') + $data
			)
		);
	echo $CI->form->group('cat_block')->block_to_HTML();
}
?>