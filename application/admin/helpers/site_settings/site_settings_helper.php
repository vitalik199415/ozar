<?php
function helper_site_settings_form_build($data)
{
	$form_id = 'site_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки', $form_id, set_url('*/save'));	
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit'
			)
		)
	);
	
	$CI->form->add_validation('site_admin[email]', array('required' => 'true', 'email' => 'true'));
	$CI->form->add_validation('site_admin[name]', array('required' => 'true'));
	
	$CI->form->add_tab('site_admin', 'Администратор сайта');
	$CI->form->add_tab('site_description', 'Описание сайта');	
	
	$CI->form->add_group('site_admin', $data['site_admin']);

		$CI->form->group('site_admin')->add_object(
			'text',
			'site_admin[email]',
			'E-Mail администратора :',
			array(
				'option'	=> array('maxlength' => '50')
			)
		);
		$CI->form->group('site_admin')->add_object( 
			'text',
			'site_admin[name]',
			'Имя администратора :',
			array(
				'option'	=> array('maxlength' => '60')
			)
		);
		
	
	$CI->form->add_group('site_description', $data['site_description'], $data['on_langs']);

		$CI->form->group('site_description')->add_object(
			'text',
			'site_description[$][company_name]',
			'Название компании :',
			array(
				'option'	=> array('maxlength' => '100')
			)
		);
		$CI->form->group('site_description')->add_object( 
			'text',
			'site_description[$][work_name]',
			'Название деятельности :',
			array(
				'option'	=> array('maxlength' => '100')
			)
		);
		$CI->form->group('site_description')->add_object( 
			'text',
			'site_description[$][work_description]',
			'Описание деятельности :',
			array(
				'option'	=> array('cols' => '5')
			)
		);
		$CI->form->group('site_description')->add_object( 
			'text',
			'site_description[$][company_title]',
			'Начало Meta Title :',
			array(
				'option'	=> array('maxlength' => '100')
			)
		);
		$CI->form->group('site_description')->add_object( 
			'text',
			'site_description[$][company_description]',
			'Начало Meta Decsription :',
			array(
				'option'	=> array('maxlength' => '100')
			)
		);
		$CI->form->group('site_description')->add_object( 
			'text',
			'site_description[$][TD_separator]',
			'Розделитель перед продолжением Meta Title, Meta Decsription :',
			array(
				'option'	=> array('maxlength' => '10', 'value' => '')
			)
		);
		
	$CI->form->add_block_to_tab('site_admin'	, 'site_admin');
	$CI->form->add_block_to_tab('site_description'	, 'site_description');
	$CI->form->render_form();
}
?>