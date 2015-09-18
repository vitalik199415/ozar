<?php
function helper_customers_types_grid_build($grid)
{
	$grid->add_button('Добавить группу покупателей', set_url('*/*/add'),
		array(
			'class' => 'addButton'
		)
	);
	
	$grid->set_checkbox_actions('ID', 'customers_types_grid_checkbox',
		array(
			'options' => array(
				'on'	=> 'Активность: Да',
				'off'	=> 'Активность: Нет',
				'delete'=> 'Удалить'
			),
			'name'	=> 'customers_types_grid_select'
		)
	);
	
	$grid->add_column(
		array(
			'index'		=> 'alias',
			'type'		=> 'text',
			'tdwidth'	=> '24%',
			'filter'	=> true
		), 'Идентификатор');
	
	$grid->add_column(
		array(
			'index'		=> 'name',
			'type'		=> 'text',
			'filter'	=> 	true
		), 'Название группы покупателей');
	$grid->add_column(
		array(
			'index'		=> 'CCOUNT',
			'type'		=> 'text',
			'tdwidth'	=> '17%'
		), 'К-во покупателей в группе');
	$grid->add_column(
		array(
			'index'		=> 'active',
			'type'		=> 'select',
			'tdwidth'	=> '12%',
			'filter'	=> true,
			'options'	=> array('' => '', '0' => 'Нет', '1' => 'Да')
		), 'Активность');
	
	$grid->add_column(
		array(
			'index'			=> 'action',
			'type'			=> 'action',
			'tdwidth'		=> '12%',
			'option_string'	=> 'align="center"',
			'actions'		=> array(
				array(
					'type' 			=> 'link',
					'html' 			=> '',
					'href' 			=> set_url('*/*/action/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Перейти')
				),
				array(
					'type'			=> 'link',
					'html'			=> '',
					'href'			=> set_url('*/*/edit/id/$1'),
					'href_values'	=> array('ID'),
					'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать тип свойств')
				),
				array(
					'type'			=> 'link',
					'html'			=> '',
					'href'			=> set_url('*/*/delete/id/$1'),
					'href_values'	=> array('ID'),
					'options'		=> array('class'=>'icon_detele  delete_question', 'title'=>'Удалить тип свойств')	
				)
			)
		), 'Действия');
}
function helper_customers_types_form_build($data = array(), $save_param = '')
{
	$form_id = 'customers_types_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Группы покупателей', $form_id, set_url('*/*/save'.$save_param));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	
	if($save_param !='')
	{
		$CI->form->add_button(
			array(
				'name'		=> 'Добавить группу покупателей',
				'href'		=> set_url('*/*/add'),
			));
		
		$CI->form->add_button(
			array(
				'name'		=> 'Удалить группу',
				'href'		=> set_url('*/*/delete'.$save_param),
				'options'	=> array('class'=>'delete_question')
			));
	}
	
	$CI->form->add_button(
		array(
			'name'		=> 'Сохранить и продолжить редактирование',
			'href'		=> '#',
			'options'	=> array(
				'id'	 => 'submit_back', 
				'class' => 'addButton'
			)
		));
	
	$CI->form->add_button(
		array(
			'name'		=> 'Сохранить',
			'href'		=> '#',
			'options'	=> array(
				'id'	=> 'submit',
				'class' => 'addButton'
			)
		));
	
	if($save_param == '')
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias').'", type:"post"}'));
	}
	else
	{
		$CI->form->add_validation('main[alias]', array('required' => 'true', 'remote' => '{url:"'.set_url('*/*/check_alias'.$save_param).'", type:"post"}'));
	}
	$CI->form->add_validation_massages('main[alias]', array('remote' => 'Группа покупателей с указанным индификатором уже существует!'));
	
	
	$CI->form->add_tab('main_block', 'Основные данные');
	$CI->form->add_tab('desc_block', 'Описание группы');
	
	$edit_data = FALSE;
	if(isset($data['main'])) $edit_data['main'] = $data['main'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['main'] = $session_temp['main'];
	}
	$CI->form->add_group('main_block', $edit_data);

	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Основные данные'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'main[alias]', 
		'Идентификатор(латиницей) (*):'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'select',
		'main[active]',
		'Активнось (*):',
		array(
			'options' => array('0' => 'Нет', '1' => 'Да')
		)
	);
	
	$edit_data = FALSE;
	if(isset($data['desc'])) $edit_data['desc'] = $data['desc'];
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data['desc'] = $session_temp['desc'];
	}
	$CI->form->add_group('desc_block', $edit_data, $data['on_langs']);
	$lid = $CI->form->group('desc_block')->add_object(
		'fieldset',
		'name_fieldset',
		'Описание группы'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'text',
		'desc[$][name]',
		'Название группы :'
	);
	$CI->form->group('desc_block')->add_object_to($lid,
		'textarea',
		'desc[$][description]',
		'Oписание группы:',
		array(
			'option'=>array('rows' => '4') 
		)
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('desc_block', 'desc_block');
	$CI->form->render_form();
}

function helper_customers_types_group_grid_build($grid, $id)
{			
	$grid->add_column(
		array(
				'index'		 => 'email',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'filter'	 => true
		), 'E-Mail');
	$grid->add_column(
		array(
				'index'		 => 'name',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'filter'	 => true
		), 'Никнейм');
	$grid->add_column(
		array(
				'index'		 => 'bname',
				'searchtable'=> 'B',
				'searchname'=> 'name',
				'type'		 => 'text',
				'filter'	 => true
		),'Фамилия, Имя плательщика');
	$grid->add_column(
		array(
				'index'		 => 'active',
				'type'		 => 'select',
				'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
				'tdwidth'	 => '10%'
		), 'Активность');
	$grid->add_column(
		array(
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
					'href' 			=> set_url('*/view/id/$1'),
					'href_values' 	=> array('ID'),
					'options'		=> array('class' => 'icon_view', 'title' => 'Просмотр', 'target' => '_blank')
				)
			)
		), 'Actions');
}

function helper_customers_types_actions_build($id, $data)
{
	$form_id = 'customers_types_group_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Просмотр группы покупателей', $form_id, set_url('*/*/customers_group/'.$id));
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*'),
		));
	$CI->form->add_button(
		array(
			'name' 		=> 'Создать рассылку',
			'href' 		=> set_url('*/*/mailing_form/id/'.$id)
		));
	
	$CI->form->add_tab('main_block', 'Пользователи группы');
	
	$CI->form->add_group('main_block');
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Список пользователей',
		array(
			'style' => 'background-color:#CCCCCC;'
		)
	);
	
	$CI->form->group('main_block')->add_html_to($lid, $data['customers']);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->render_form();
}

function helper_customers_types_mailing_grid_build($grid, $id)
{			
	$grid->set_checkbox_actions('ID', Mcustomers_types::ID_CT.'[]',
		array(
			'options' => NULL,
			'name'	=> NULL
		)
	);
	$grid->add_column(
		array(
				'index'		 => 'email',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'filter'	 => true
		), 'E-Mail');
	$grid->add_column(
		array(
				'index'		 => 'name',
				'searchtable'=> 'B',
				'type'		 => 'text',
				'filter'	 => true
		), 'Никнейм');
	$grid->add_column(
		array(
				'index'		 => 'bname',
				'searchtable'=> 'B',
				'searchname'=> 'name',
				'type'		 => 'text',
				'filter'	 => true
		),'Фамилия, Имя плательщика');
	$grid->add_column(
		array(
				'index'		 => 'active',
				'type'		 => 'select',
				'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
				'tdwidth'	 => '10%'
		), 'Активность');
	$grid->add_column(
		array(
			'index'		 => 'action',
			'type'		 => 'action',
			'tdwidth'	 => '10%',
			'option_string' => 'align="center"',
			'sortable' 	 => false,
			'filter'	 => false,
			'actions'	 => array()
		), 'Actions');
}

function helper_customers_types_mailing_form_build($id, $data)
{	
	$form_id = 'customers_types_mailing_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Рассылка группе покупателей', $form_id, set_url('*/*/create_mailing/id/'.$id));
	$CI->form->enable_CKE();
	
	$CI->form->add_button(
		array(
			'name' 		=> 'Назад',
			'href' 		=> set_url('*/*/action/id/'.$id),
	));
	$CI->form->add_button(
		array(
			'name' 		=> 'Произвести рассылку',
			'href' 		=> '#',
			'options'	=> array(
				'id'	=> 'submit'
			)
		));
		
	$CI->form->add_validation('subject', array('required' => 'true', 'minlength' => '10'));
	//$CI->form->add_validation('message', array('required' => 'true', 'minlength' => '100'));
	
	$CI->form->add_tab('main_block', 'Рассылка');
	
	$edit_data = FALSE;
	if($session_temp = $CI->session->flashdata($form_id))
	{
		$edit_data = $session_temp;
		$edit_data['message'] = str_replace('&lt;', '<', $edit_data['message']);
		$edit_data['message'] = str_replace('&gt;', '>', $edit_data['message']);
	}
	
	$CI->form->add_group('main_block',  $edit_data);

	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Рассылка писем'
	);

	$CI->form->group('main_block')->add_object_to($lid,
		'text',
		'subject',
		'Тема письма (*):'
	);
	
	$CI->form->group('main_block')->add_object_to($lid,
		'textarea', 
		'message',
		'Текст письма (*):',
		array(
			'option' 	=> array(
				'class' => 'ckeditor'
			)
		)
	);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'mailing_type',
		'Тип рассылки (*):',
		array(
			'options' => array('all' => 'Всем покупателям группы', 'selected' => 'Выбранным покупателям группы'),
			'option' => array('id' => 'group_customers_select')
		)
	);
	
	$display = 'display:none;';
	if(isset($edit_data['mailing_type']) && $edit_data['mailing_type'] == 'selected') $display = 'display:block;';
	
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Список покупателей группы',
		array(
			'style' => 'background-color:#CCCCCC;'.$display, 'id' => 'group_customers'
		)
	);
	
	$CI->form->group('main_block')->add_html_to($lid, $data['customers']);
	$CI->form->group('main_block')->add_object(
		'js',
		"$('#".$form_id."').find('#group_customers_select').bind('click', function()
		{
			if($(this).val() == 'all') $('#".$form_id."').find('#group_customers').css('display', 'none');
			else $('#".$form_id."').find('#group_customers').css('display', 'block');
		});"
	);
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->render_form();
}
?>