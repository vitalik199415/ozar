<?php
if(isset($form_id) && isset($currency))
{

$Form_object = new Agform_block($values);
foreach($currency as $key => $ms)
{
	$display = 'none';
	if(isset($values['users_currency'][$key]))
	{
		$display = 'block';
	}
	
	$lid = $Form_object->addObject(
			'fieldset',
			'name_fieldset',
			$ms['name']
	);
	$Form_object->addObjectTo($lid,
		'checkbox',
		'users_currency['.$key.']',
		$ms['name'],
		array(
			'value' => $key,
			'option' => array('class' => $form_id.'_cur')
		)
	);
	$Form_object->addObjectTo($lid,
		'html',
		'<div style="padding:5px 0 0 30px; display:'.$display.'" id="'.$form_id.'_curdesc_'.$key.'">'
	);
	$Form_object->addObjectTo($lid,
		'text',
		'users_currency_desc['.$key.'][rate]',
		'Курс по соотношению к основной валюте :'
	);
	$Form_object->addObjectTo($lid,
		'select',
		'users_currency_desc['.$key.'][active]', 
		'Показывать на сайте :',
		array(
			'options'	=> array('1' => 'Да','0' => 'Нет')
		)
	);
	$Form_object->addObjectTo($lid,
		'select',
		'users_currency_desc['.$key.'][permission]', 
		'Показывать валюту :',
		array(
			'options'	=> array('0' => 'Всем пользователям','1' => 'Только под кодом оптовика')
		)
	);
	$Form_object->addObject(
		'hidden',
		'users_currency_desc['.$key.'][ID]'
	);
	$Form_object->addObjectTo($lid,
		'radio',
		'users_currency_desc[default]',
		'Основная валюта каталога :',
		array(
			'option' => array('value' => $key)
		)
	);
	$Form_object->addObjectTo($lid,
		'radio',
		'users_currency_desc[default_selected]',
		'Валюта выбрана по умолчанию :',
		array(
			'option' => array('value' => $key)
		)
	);
	$Form_object->addObjectTo($lid,
		'html',
		'</div>'
	);	
}
?>
<div id="currency_types_block">
	<?=$Form_object->BlockToHTML($form_id, 'currency_block');?>
<script>
	$('.<?=$form_id?>_cur').live('change',function()
		{
			if($(this).prop('checked'))
			{
				$('#<?=$form_id?>_curdesc_'+$(this).val()).css('display','block');
			}
			else
			{
				$('#<?=$form_id?>_curdesc_'+$(this).val()).css('display','none');
			}
		}
	);	
</script>
</div>
<?
}
?>