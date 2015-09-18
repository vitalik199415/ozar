<?php
function helper_sales_settings_form_build($data)
{
	$form_id = 'sales_settings_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Настройки', $form_id, set_url('*/*/save'));	
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton'
			)
		)
	);
	
	$CI->form->add_tab('orders_settings', 'Настройки уведомлений');		
	
	if(!isset($data['settings'])) $data['settings'] = FALSE;
	$CI->form->add_group('registration_notice_block', $data['settings']);
	$CI->form->add_group('orders_settings', $data);

		$CI->form->group('orders_settings')->add_object(
			'select',
			'settings[mail_send_confirmed]',
			'Тип обработки заказов :',
			array(
				'options'	=> array('0' => 'Уведомлять о всех заказах', '1' => 'Уведомлять только о подтвержденных')
			)
		);
		$CI->form->group('orders_settings')->add_object(
			'text',
			'settings[mail_new_order_email]',
			'E-mail для уведомлений о заказах :',
			array(
				'option'	=> array('maxlength' => '50')
			)
		);
		$CI->form->group('orders_settings')->add_object( 
			'text',
			'settings[mail_shop_name]',
			'Название магазина :',
			array(
				'option'	=> array('maxlength' => '50')
			)
		);
		
	$CI->form->add_block_to_tab('orders_settings'	, 'orders_settings');

	$CI->form->render_form();
}
?>