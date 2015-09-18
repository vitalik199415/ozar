<?php
function helper_menu_modules_form($data = array(), $save_param = '')
{
	$form_id = 'add_home_modules_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Добавление модулей к главной', $form_id, set_url('*/save'.$save_param));		
	$CI->form->add_button(
		array(
			'name' => 'Сохранить и продолжить редактирование',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back',
				'class' => 'addButton',
			)
		)
	);

	$CI->form->add_tab('main_block', 'Модули главной');
	$CI->form->add_tab('desc_block', 'SEO главной');
	
	$CI->form->add_group('main_block');
	$array = array();
	if(isset($data['checkbox_checked'])) 
	{
		$main_array['values'] = $data['checkbox_checked'];
		$CI->form->group('main_block')->add_view('home/checked_modules', $main_array);
		
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
	$lid = $CI->form->group('main_block')->add_object(
			'hidden',
			'save',
			'',
			array(
				'value' => '1'
			)
		);
	
	if(!isset($data['home_desc'])) $data['home_desc'] = FALSE;
	$CI->form->add_group('desc_block', $data['home_desc'], $data['on_langs']);
	$lid = $CI->form->group('desc_block')->add_object(
			'text', 
			'home_desc[$][seo_title]',
			'Title :'
		);
	$lid = $CI->form->group('desc_block')->add_object(
			'textarea', 
			'home_desc[$][seo_description]',
			'Description :',
			array(
				'option' 	=> array(
					'rows' => '3'
				)
			)
		);
	$lid = $CI->form->group('desc_block')->add_object(
			'text', 
			'home_desc[$][seo_keywords]',
			'Keywords :'
		);
	
	
	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->add_block_to_tab('desc_block', 'desc_block');
	$CI->form->render_form();
}
?>