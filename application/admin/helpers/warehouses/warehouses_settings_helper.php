<?php
function helper_warehouses_settings_form_build($data)
{
	$form_id = 'warehouses_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки склада', $form_id, set_url('*/*/save'));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit_back'
			)
		));
	
	$CI->form->add_tab('main_block'	, 'Основные настройки');
	
	$edit_data = FALSE;
	if(isset($data['settings'])) $edit_data['settings'] = $data['settings'];
	$CI->form->add_group('main_block', $edit_data);
	
	$CI->form->group('main_block')->add_object(
		'select',
		'settings[wh_on]',
		'Система склада :',
		array(
			'options'	=> array('0' => 'Не актвная', '1' => 'Активная')
		)
	);
	$CI->form->group('main_block')->add_object(
		'select',
		'settings[wh_active]',
		'Включить систему склада (Система будет активирована на сайте) :',
		array(
			'options'	=> array('0' => 'Отключено', '1' => 'Включено')
		)
	);
		
	$CI->form->add_block_to_tab('main_block', 'main_block');

	$CI->form->render_form();
}
?>