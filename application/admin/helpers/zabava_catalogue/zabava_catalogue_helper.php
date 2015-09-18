<?php
function helper_zabava_catalogue_grid_build($grid)
{
	$grid->add_button('Добавить позицию', set_url('*/*/*/add'),
			array(
				'class' => 'addButton',
			)
		);
	
	$grid->set_checkbox_actions('ID', 'zabava_catalogue_grid_checkbox',
		array(
			'options' => array(
				'on' => 'Активность: Да',
				'off' => 'Активность: Нет',
				'delete' => 'Удалить выбраные'
			),'name' => 'zabava_catalogue_grid_select')
		);
	
	$grid->add_column(
				array
					(
						'index'		 => 'sort',
						'type'		 => 'number',
						'tdwidth'	 => '7%',
						'filter'	 => FALSE,
						'sortable'	 => FALSE,
						'option_string' => 'align="center"'
				),'Позиция');
	$grid->add_column(
				array
					(
						'index'		 => 'name',
						'type'		 => 'text',
						'filter'	 => true
					),'Название позиции');
	$grid->add_column(
				array
					(
						'index'		 => 'active',
						'type'		 => 'select',
						'tdwidth'	 => '12%',
						'filter'	 => true,
						'options' => array('' => '', '0' => 'Нет', '1' => 'Да')
					),'Активность');
	$grid->add_column(
				array
					(
						'index'		 => 'create_date',
						'type'		 => 'date',
						'tdwidth'	 => '14%',
						'sortable' 	 => true,
						'filter'	 => true
					),'Создана');
	$grid->add_column(
				array
					(
						'index'		 => 'update_date',
						'type'		 => 'date',
						'tdwidth'	 => '14%',
						'sortable' 	 => true,
						'filter'	 => true
					), 'Отредактирована');
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
								'href' 			=> set_url('*/*/*/photo/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_photo', 'title'=>'Изображения позиции')
							),
							array(
								'type' 			=> 'link',
								'html' 			=> '',
								'href' 			=> set_url('*/*/*/edit/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать позицию')
							),
							array(
								'type' 			=> 'link',
								'html' 			=> '',
								'href' 			=> set_url('*/*/*/delete/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить позицию')
							)
						),'Действия'));
}
function helper_zabava_catalogue_form_build($data = array(), $save_param = '')
{
	$form_id = 'news_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Каталог', $form_id, set_url('*/*/*/save'.$save_param));
	$CI->form->enable_CKE();
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*/*/'),
			'options' 	=> array()
		));
	
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' 		=> 'Изображения',
				'href' 		=> set_url('*/*/*/photo'.$save_param)
			));
		$CI->form->add_button(
			array(
				'name' 		=> 'Добавить позицию',
				'href' 		=> set_url('*/*/*/add')
			));
		$CI->form->add_button(
			array(
				'name' 		=> 'Удалить позицию',
				'href' 		=> set_url('*/*/*/delete'.$save_param),
				'options' 	=> array(
					'class' => 'delete_question'
				)
			));
	}
		
	$CI->form->add_button(
		array(
			'name' 		=> 'Сохранить и продолжить редактирование',
			'href' 		=> '#',
			'options' 	=> array(
					'id' 	=> 'submit_back',
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
	$CI->form->add_tab('desc','Основное описание');
	
	$CI->form->add_tab('description','Подробное описание');
	$CI->form->add_tab('specifications','Технические характеристики');
	$CI->form->add_tab('terms_business','Условия сотрудничества');
	$CI->form->add_tab('manual','Инструкция');
	$CI->form->add_tab('video_presentation','Видео-презентация');
	$CI->form->add_tab('video','Видео игры');
	$CI->form->add_tab('game','Файл игры');
	
	$CI->form->add_tab('SEO_block','SEO элемента');
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	$CI->form->add_group('base', $data['base']);
	$lid = $CI->form->group('base')->add_object(
				'fieldset',
				'base_fieldset',
				'Основные данные'
			);
	$CI->form->group('base')->add_object_to($lid,
				'text', 
				'main[url]',
				'Сегмент URL :',
				array(
					'option' => array('maxlength' => '80')
				)
			);
	$CI->form->group('base')->add_object_to($lid,
				'select', 
				'main[active]',
				'Активность :',
				array(
					'options'	=> array('0' => 'Нет', '1' => 'Да')
				)
			);
	
	if(!isset($data['desc'])) $data['desc'] = FALSE;
	$CI->form->add_group('desc', $data['desc'], $data['on_langs']);
	$lid = $CI->form->group('desc')->add_object(
				'fieldset',
				'name_fieldset',
				'Основное описание'
			);
	$CI->form->group('desc')->add_object_to($lid,
		'text', 
		'langs[$][name]',
		'Название позиции :',
		array(
			'option'	=> array()
		)
	);
	$CI->form->group('desc')->add_object_to($lid,
		'textarea',
		'langs[$][short_description]',
		'Короткое описание :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	$CI->form->group('desc')->add_object_to($lid,
		'textarea', 
		'langs[$][full_description]',
		'Полное описание :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('description', $data['additional'], $data['on_langs']);
	$CI->form->group('description')->add_object(
		'select', 
		'additional[1][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('description')->add_object(
		'textarea', 
		'additional[1][$][data]',
		'Полное описание :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('specifications', $data['additional'], $data['on_langs']);
	$CI->form->group('specifications')->add_object(
		'select', 
		'additional[2][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('specifications')->add_object(
		'textarea', 
		'additional[2][$][data]',
		'Технические характеристики :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('terms_business', $data['additional'], $data['on_langs']);
	$CI->form->group('terms_business')->add_object(
		'select', 
		'additional[3][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('terms_business')->add_object(
		'textarea', 
		'additional[3][$][data]',
		'Условия сотрудничества :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('manual', $data['additional'], $data['on_langs']);
	$CI->form->group('manual')->add_object(
		'select', 
		'additional[4][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('manual')->add_object(
		'file', 
		'additional[4][$][data]',
		'Загрузка инструкции :'
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('video_presentation', $data['additional'], $data['on_langs']);
	$CI->form->group('video_presentation')->add_object(
		'select', 
		'additional[5][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('video_presentation')->add_object(
		'textarea', 
		'additional[5][$][data]',
		'Видео-презентация :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('video', $data['additional'], $data['on_langs']);
	$CI->form->group('video')->add_object(
		'select', 
		'additional[6][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$CI->form->group('video')->add_object(
		'textarea', 
		'additional[6][$][data]',
		'Видео игры :',
		array(
			'option'	=> array('class' => 'ckeditor')
		)
	);
	
	if(!isset($data['additional'])) $data['additional'] = FALSE;
	$CI->form->add_group('game', $data['additional'], $data['on_langs']);
	$CI->form->group('game')->add_object(
		'select', 
		'additional[7][$][active]',
		'Активность :',
		array(
			'options'	=> array('1' => 'Да', '0' => 'Нет')
		)
	);
	$lid = $CI->form->group('game')->add_object(
		'text', 
		'additional[7][$][data]',
		'Файл игры :'
	);
	
	$CI->form->add_group('SEO_block', $data['desc'], $data['on_langs']);
	$CI->form->group('SEO_block')->add_object(
		'text',
		'langs[$][seo_title]', 
		'Title : '
	);
	$CI->form->group('SEO_block')->add_object(
		'text', 
		'langs[$][seo_description]', 
		'Description : '
	);
	$CI->form->group('SEO_block')->add_object(
		'text', 
		'langs[$][seo_keywords]',
		'Keywords : '
	);
	
	
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->add_block_to_tab('desc', 'desc');
	
	$CI->form->add_block_to_tab('description', 'description');
	$CI->form->add_block_to_tab('specifications', 'specifications');
	$CI->form->add_block_to_tab('terms_business', 'terms_business');
	$CI->form->add_block_to_tab('manual', 'manual');
	$CI->form->add_block_to_tab('video_presentation', 'video_presentation');
	$CI->form->add_block_to_tab('video', 'video');
	$CI->form->add_block_to_tab('game', 'game');
	
	$CI->form->add_block_to_tab('SEO_block', 'SEO_block');
	$CI->form->render_form();
}

function helper_zabava_catalogue_photo_form($ID, $data, $save_param = '')
{
	$form_id = 'news_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление фотографий новости', $form_id, set_url('*/*/*/save_photo_desc'.$save_param));
	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('*/*/*/'),
			'options' => array( ),
		 )
	);
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование описания фотографий',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton'
   			)
		)
	);
	$CI->form->add_button(
		array(
			'name' => 'Сохранить описание фотографий',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton',
			)
		)
	);
	
	$CI->form->add_group('main_block');
	$array = array();
	
	if(!isset($data['image'])){ $data['image'] = FALSE; $data['img_desc'] = FALSE; }
	
	$array['PID'] = $ID;
	$array['form_id'] = $form_id;
	
	$ddata['PID'] = $ID;
	$ddata['form_id'] = $form_id;
	$ddata['id_users'] = $data['id_users'];
	$ddata['on_langs'] = $data['on_langs'];
	$ddata['ajax'] = FALSE;
	$array['img_html'] = '';
	if(is_array($data['image']))
	{
		foreach($data['image'] as $key => $ms)
		{
			$ddata['id'] = $ms['id_m_zabava_catalogue_photos'];
			$ddata['image'] = $ms['image'];
			$ddata['values'] = array('img_desc' => array($key => $data['img_desc'][$key]));
			$array['img_html'] .= helper_zabava_catalogue_photo_desc_form($ddata);
		}
	}
	$CI->form->group('main_block')->add_view('zabava_catalogue/form_img', $array);
	$CI->form->add_block($CI->form->group('main_block'));
		
	$CI->form->render_form();
}
function helper_zabava_catalogue_photo_desc_form($data)
{
	$CI = & get_instance();
	return $CI->load->view('zabava_catalogue/form_img_desc', $data, TRUE);
}
?>