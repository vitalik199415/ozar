<?php
function helper_m_catalogue_grid_build($grid)
{
	$grid->add_button('Настройки модуля', set_url('*/*/*/settings'),
			array(
				'class' => 'addButton'
			)
		);
	$grid->add_button('Добавить объект', set_url('*/*/*/add'),
			array(
				'class' => 'addButton',
			)
		);
	
	$grid->set_checkbox_actions('ID', 'm_catalogue_grid_checkbox',
		array(
			'options' => array(
				'on' => 'Активность: Да',
				'off' => 'Активность: Нет',
				'delete' => 'Удалить выбраные'
			),'name' => 'm_catalogue_grid_select')
		);
	
	$grid->add_column(
				array
					(
						'index'		 => 'ID',
						'searchname' => 'id_m_catalogue',
						'searchtable'=> 'A',
						'type'		 => 'number',
						'tdwidth'	 => '9%',
						'filter'	 => true
				),'ID');
	$grid->add_column(
				array
					(
						'index'		 => 'name',
						'type'		 => 'text',
						'tdwidth'	 => '28%',
						'filter'	 => true
					),'Название');
	$grid->add_column(
				array
					(
						'index'		 => 'price',
						'type'		 => 'text',
						'tdwidth'	 => '8%',
						'filter'	 => false,
						'sortable' 	 => false,
					),'Цена');
	$grid->add_column(
				array
					(
						'index'		 => 'create_date',
						'type'		 => 'date',
						'sortable' 	 => true,
						'filter'	 => true
					),'Создан');
	$grid->add_column(
				array
					(
						'index'		 => 'update_date',
						'type'		 => 'date',
						'sortable' 	 => true,
						'filter'	 => true
					), 'Отредактирован');
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
								'options'		=> array('class'=>'icon_photo', 'title'=>'Изображения объекта')
							),
							array(
								'type' 			=> 'link',
								'html' 			=> '',
								'href' 			=> set_url('*/*/*/edit/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать объект')
							),
							array(
								'type' 			=> 'link',
								'html' 			=> '',
								'href' 			=> set_url('*/*/*/delete/id/$1'),
								'href_values' 	=> array('ID'),
								'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить объект')
							)
						),'Действия'));
}
function helper_m_catalogue_form_build($data = array(), $save_param = '')
{
	$form_id = 'm_catalogue_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Упрощенный каталог', $form_id, set_url('*/*/*/save'.$save_param));
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
				'name' 		=> 'Добавить объект',
				'href' 		=> set_url('*/*/*/add')
			));
		$CI->form->add_button(
			array(
				'name' 		=> 'Удалить объект',
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
	$CI->form->add_tab('desc','Описание объектa');
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[url]', array('remote' => '{url:"'.set_url('*/*/*/check_url').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[url]', array('remote' => '{url:"'.set_url('*/*/*/check_url'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[url]', array('remote' => 'Объект с указанным сегментом URL уже существует!'));
	
	$session_temp = $CI->session->flashdata($form_id);
	
	if(!isset($data['base'])) $data['base'] = FALSE;
	if($session_temp)
	{
		$data['base']['base'] = $session_temp['base'];
	}
	
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
					'option' => array('maxlength' => '50')
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
	$CI->form->group('base')->add_object_to($lid,
				'text', 
				'main[price]',
				'Цена и Валюта:',
				array(
					'option' => array('maxlength' => '50')
				)
			);
	
	if(!isset($data['desc'])) $data['desc'] = FALSE;
	$CI->form->add_group('desc', $data['desc'], $data['on_langs']);
	$lid = $CI->form->group('desc')->add_object(
				'fieldset',
				'name_fieldset',
				'Название объекта'
			);
	$CI->form->group('desc')->add_object_to($lid,
				'text', 
				'langs[$][name]',
				'Название объекта :',
				array(
					'option'	=> array()
				)
			);
	$lid = $CI->form->group('desc')->add_object(
				'textarea', 
				'langs[$][short_description]',
				'Короткое описание :',
				array(
					'option'	=> array('class' => 'ckeditor')
				)
			);
	$lid = $CI->form->group('desc')->add_object(
				'textarea', 
				'langs[$][full_description]',
				'Полное описание :',
				array(
					'option'	=> array('class' => 'ckeditor')
				)
			);
	$lid = $CI->form->group('desc')->add_object(
				'hidden', 
				'langs[$][id_m_catalogue_description]'
			);
	
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->add_block_to_tab('desc', 'desc');
	$CI->form->render_form();
}

function helper_m_catalogue_photo_form($ID, $data, $save_param = '')
{
	$form_id = 'm_catalogue_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление фотографий объекта', $form_id, set_url('*/*/*/save_photo_desc'.$save_param));
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
			$ddata['id'] = $ms['id_m_catalogue_photos'];
			$ddata['image'] = $ms['image'];
			$ddata['values'] = array('img_desc' => array($key => $data['img_desc'][$key]));
			$array['img_html'] .= helper_m_catalogue_photo_desc_form($ddata);
		}
	}
	$CI->form->group('main_block')->add_view('m_catalogue/form_img', $array);
	$CI->form->add_block($CI->form->group('main_block'));
		
	$CI->form->render_form();
}
function helper_m_catalogue_photo_desc_form($data)
{
	$CI = & get_instance();
	return $CI->load->view('m_catalogue/form_img_desc', $data, TRUE);
}
?>