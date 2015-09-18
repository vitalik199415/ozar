<?php
function helper_photo_gallery_grid_build($grid)
{
	$grid->add_button('Настройки модуля', set_url('*/*/*/settings'));
	$grid->add_button('Добавить Фото-альбом', set_url('*/*/*/add'),
			array(
				'class' => 'addButton'
			)
		);
	$grid->set_checkbox_actions('ID', 'photo_gallery_grid_checkbox',
		array(
				'options' => array(
					'on' => 'Активность: Да',
					'off' => 'Активность: Нет',
					'delete' => 'Удалить'
				),
				'name' => 'photo_gallery_grid_select'
			 ));
	
	/*$grid->add_column(
		array
			(
				'index'		 => 'ID',
				'searchname' => 'id_m_photo_gallery_albums',
				'searchtable'=> 'A',
				'type'		 => 'number',
				'tdwidth'	 => '9%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'ID');*/
	$grid->add_column(
		array
			(
				'index'		 => 'name',
				'type'		 => 'text',
				'filter'	 => true
			), 'Название Фото-альбома');
	$grid->add_column(
		array
			(
				'index'		 => 'create_date',
				'type'		 => 'date',
				'tdwidth'	 => '12%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Создан');
	$grid->add_column(
		array
			(
				'index'		 => 'update_date',
				'type'		 => 'date',
				'tdwidth'	 => '12%',
				'sortable' 	 => true,
				'filter'	 => true
			), 'Изменен');
	$grid->add_column(
		array
			(
				'index'		 => 'active',
				'type'		 => 'select',
				'tdwidth'	 => '12%',
				'filter'	 => true,
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
						'href' 			=> set_url(array('*','*','*','photo','id','$1')),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_photo', 'title'=>'Изображения')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/*/*/edit/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать Фото-альбом')
					),
					array(
						'type' 			=> 'link',
						'html' 			=> '',
						'href' 			=> set_url('*/*/*/delete/id/$1'),
						'href_values' 	=> array('ID'),
						'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить Фото-альбом')
					)
				)
			), 'Действия');
}
function helper_photo_gallery_form_build($data = array(), $save_param = '')
{
	$form_id = 'photo_gallery_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Фото-галлерея', $form_id, set_url('*/*/*/save'.$save_param));
	$CI->form->enable_CKE();
	$CI->form->add_button(
				array(
					'name' 		=> 'Назад',
					'href' 		=> set_url('*/*/*/'),
				)
			);
	
	if($save_param != '')
	{
		$CI->form->add_button(
			array(
				'name' 		=> 'Изображения альбома',
				'href' 		=> set_url('*/*/*/photo/'.$save_param),
				'options' 	=> array()
			));
		$CI->form->add_button(
			array(
				'name' 		=> 'Добавить фото-альбом',
				'href' 		=> set_url('*/*/*/add'),
				'options' 	=> array()
			));
		$CI->form->add_button(
			array(
				'name' 		=> 'Удалить фото-альбом',
				'href' 		=> set_url('*/*/*/delete'.$save_param),
				'options' 	=> array(
					'class' => 'delete_question'
				)
			));
	}
		
	$CI->form->add_button(
				array(
					'name' 		=> 'Сохранить и продолжить редактирование',
					'href' 		=> ('*/*/*/'.$save_param),
					'options' 	=> array(
										'id' 	=> 'submit_back',
										'class' => 'addButton'
					)
				)
			);
	$CI->form->add_button(
				array(
					'name' 		=> 'Сохранить',
					'href' 		=> '#',
					'options' 	=> array(
									'id' 	=> 'submit',
									'class' => 'addButton'
					)
				)
			);
	$CI->form->add_tab('base','Основные данные');
	$CI->form->add_tab('desc','Описание альбома');
	$CI->form->add_tab('seo','SEO альбома');
	
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
				'option'	=> array('maxlenght' => '50')
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
	$CI->form->add_group('name', $data['desc'], $data['on_langs']);
	
	$lid = $CI->form->group('name')->add_object(
		'fieldset',
		'name_fieldset',
		'Название альбома'
	);		
	$CI->form->group('name')->add_object_to($lid,
		'text', 
		'langs[$][name]',
		'Название альбома:',
			array(
				'option'	=> array('maxlenght' => '100')
			)
	);
	
	
	$CI->form->add_group('desc', $data['desc'],$data['on_langs']);
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
		'Полное описание:',
			array(
				'option'	=> array('class' => 'ckeditor')
			)
	);
	$lid = $CI->form->group('desc')->add_object(
		'hidden', 
		'langs[$][id_m_photo_gallery_albums_description]'
	);
	if(!isset($data['seo'])) $data['seo'] = FALSE;
	$CI->form->add_group('seo', $data['seo'], $data['on_langs']);
	$lid = $CI->form->group('seo')->add_object(
		'fieldset',
		'name_fieldset',
		'SEO'
	);
			
	$CI->form->group('seo')->add_object_to($lid,
		'text',
		'langs[$][seo_title]',
		'Title :',
			array('option'=>array('maxlenght'=>'200') )
	);
			
	$CI->form->group('seo')->add_object_to($lid,
		'text',
		'langs[$][seo_description]',
		'Description :',
			array('maxlenght'=>'300')
	);	
	$CI->form->group('seo')->add_object_to($lid,
		'text',
		'langs[$][seo_keywords]',
		'Keywords :',
			array('maxlenght'=>'100')
	);
	$CI->form->add_block_to_tab('base', 'base');
	$CI->form->add_block_to_tab('base', 'name');
	$CI->form->add_block_to_tab('desc', 'desc');
	$CI->form->add_block_to_tab('seo', 'seo');
	$CI->form->render_form();
}

function helper_photo_gallery_form($ID, $data, $save_param = '')
{
	$form_id = 'photo_gallery_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление фотографий альбома', $form_id, set_url('*/*/*/save_photo_desc'.$save_param));
	
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
			$ddata['id'] = $ms['id_m_photo_gallery_photos'];
			$ddata['image'] = $ms['image'];
			$ddata['values'] = array('img_desc' => array($key => $data['img_desc'][$key]));
			$array['img_html'] .= helper_photo_desc_form($ddata);
		}
	}
	$CI->form->group('main_block')->add_view('photo_gallery/photo_gallery_img_loading_form', $array);

	$save_img_url = set_url('*/*/*/photo_save/id/'.$ID);

	$js = "
	$('#".$form_id."').products_img_upload({
		upload_url: '".set_url($save_img_url)."',
		form_id: '".$form_id."'
	});
	";
	$CI->form->group('main_block')->add_object(
		'js',
		$js
	);
	$CI->form->add_block($CI->form->group('main_block'));
		
	$CI->form->render_form();
}
function helper_photo_desc_form($data)
{
		$CI = & get_instance();
		return $CI->load->view('photo_gallery/form_img_desc', $data, TRUE);
}
?>