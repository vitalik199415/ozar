<?php
function helper_currency_form_build($data)
{
	$form_id = 'ucurrency_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Редактирование настроек валют', $form_id, set_url('*/*/save'));
	
	$CI->form->add_button(
		array(
			'name' => 'Сохранить',
			'href' => '#',
			'options' => array(
							'id' => 'submit_back',
							'class' => 'addButton',
			)
		));
	
	$CI->form->add_tab('currency_block'	, 'Валюты магазина');
	
	if(!isset($data['users_currency'])) $data['users_currency'] = FALSE;
	$CI->form->add_group('currency_block', $data['users_currency']);
	foreach($data['data_currency'] as $key => $ms)
	{
		$display = 'none';
		if(isset($data['users_currency']['users_currency'][$key]))
		{
			$display = 'block';
		}
		
		$lid = $CI->form->group('currency_block')->add_object(
				'fieldset',
				'name_fieldset',
				$ms['name']
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'checkbox',
			'users_currency['.$key.']',
			$ms['name'],
			array(
				'value' => $key,
				'option' => array('class' => $form_id.'_cur')
			)
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'html',
			'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_curdesc_'.$key.'">'
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'text',
			'users_currency_desc['.$key.'][rate]',
			'Курс по соотношению к основной валюте :'
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'select',
			'users_currency_desc['.$key.'][active]', 
			'Показывать на сайте :',
			array(
				'options'	=> array('1' => 'Да','0' => 'Нет')
			)
		);
		
		$CI->form->group('currency_block')->add_object_to($lid,
			'select', 
			'users_currency_desc['.$key.'][visible_rules]', 
			'Правила показа валюты :',
			array(
				'options'	=> array('0' => 'Показывать всем посетителям', '1' => 'Показывать только зарегистрированым покупателям', '2' => 'Показать только выбранным группам покупателей'),
				'option'	=> array('class' => $form_id.'_customers_types_select')
			)
		);
		
		$display = 'none';
		$CI->form->group('currency_block')->add_object_to($lid,
			'html',
			'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_customers_types">'
		);
		
			foreach($data['customers_types'] as $key1 => $ms1)
			{
				$CI->form->group('currency_block')->add_object_to($lid,
					'checkbox', 
					'users_currency_desc['.$key.'][m_u_types]['.$key1.']',
					$ms1.' :',
					array(
						'value' => $key1
					)
				);
			}
		
		$CI->form->group('currency_block')->add_object_to($lid,
			'html',
			'</div>'
		);
		
		$CI->form->group('currency_block')->add_object_to($lid,
			'hidden',
			'users_currency_desc['.$key.'][ID]'
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'radio',
			'users_currency_desc[default]',
			'Основная валюта каталога :',
			array(
				'option' => array('value' => $key)
			)
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'radio',
			'users_currency_desc[default_selected]',
			'Валюта выбрана по умолчанию :',
			array(
				'option' => array('value' => $key)
			)
		);
		$CI->form->group('currency_block')->add_object_to($lid,
			'html',
			'</div>'
		);
	}
	
	$js = '
		$(".'.$form_id.'_cur").live("change",function()
		{
			if($(this).prop("checked"))
			{
				$("#'.$form_id.'_curdesc_"+$(this).val()).css("display","block");
			}
			else
			{
				$("#'.$form_id.'_curdesc_"+$(this).val()).css("display","none");
			}
		}
	);	
	';
	
	$CI->form->group('currency_block')->add_object(
		'js',
		$js
	);
	
	$js = "
	$('.".$form_id."_customers_types_select').each(function()
	{
		if($(this).val() == 2)
		{
			$(this).parents('fieldset').find('#".$form_id."_customers_types').css('display','block');
		}
	});
	$('.".$form_id."_customers_types_select').live('change',function()
		{
			if($(this).val() == 2)
			{
				$(this).parents('fieldset').find('#".$form_id."_customers_types').css('display','block');
			}
			else
			{
				$(this).parents('fieldset').find('#".$form_id."_customers_types').css('display','none');
			}
		}
	);
	";
	$CI->form->group('currency_block')->add_object(
		'js',
		$js
	);
		//$CI->form->group('currency_block')->add_view('currency/form_ucurrency', $cur_array);
	
	$CI->form->add_block_to_tab('currency_block'	, 'currency_block');
	$CI->form->render_form();
}
?>