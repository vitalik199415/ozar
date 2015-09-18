<?php
function users_payment_methods($data)
{
	$form_id = 'users_shipping_methods_edit_form';
	$Form = new Agform('Редактирование методов доставки', $form_id, setUrl('*/save'));
	$Form ->addButton(
		array(
			'name' => 'Назад',
			'href' => setUrl('*/'),
			'options' => array( ),
		 )
	);
	$Form->addButton(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
				'id' => 'submit',
				'class' => 'addButton',
			)
		)
	);
	
	$Form->addTabs('users_shipping_methods', 'Методы доставки');
	
	$Users_shipping_methods = new Agform_block();
		$pm_array['form_id'] = 						$form_id;
		$pm_array['on_langs'] = 					$data['on_langs'];
		$pm_array['users_shipping_methods'] = 		$data['users_shipping_methods'];
		$pm_array['shipping_methods'] = 			$data['shipping_methods'];
		
		$Users_shipping_methods->addView('form_u_shipping_methods', $pm_array);
	
	$Form->addBlockToTabs('users_shipping_methods', $Users_shipping_methods);
	$Form->renderForm();
}