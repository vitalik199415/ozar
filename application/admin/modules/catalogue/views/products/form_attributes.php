<?php
if(isset($form_id) && isset($attributes) && count($attributes)>0)
{
$Form_object_type = new Agform_block($values);
foreach($attributes as $key => $ms)
{
	$display = 'none';
	if(isset($values['products_attributes'][$key]))
	{
		$display = 'block';
	}
	
	$lid = $Form_object_type->addObject(
			'fieldset',
			'name_fieldset',
			$ms
		);
	$Form_object_type->addObjectTo($lid,
		'checkbox',
		'products_attributes['.$key.']',
		$ms,
		array(
			'value' => $key,
			'option' => array('class' => 'attributes')
		)
	);
	if(isset($attributes_options[$key]))
	{
		$Form_object_type->addObjectTo($lid,
			'html',
			'<div style="padding:5px 0 0 30px; display:'.$display.'" id="attributes_options_'.$key.'">'
		);
		foreach($attributes_options[$key] as $key1 => $ms1)
		{
			$Form_object_type->addObjectTo($lid,
				'checkbox',
				'products_attributes_options['.$key.']['.$key1.']',
				$ms1,
				array(
					'value' => $key1
				)
			);
		}
		$Form_object_type->addObjectTo($lid,
			'html',
			'</div>'
		);
	}
}
?>
<div id="pruduct_attributes_block">
	<?=$Form_object_type->BlockToHTML($form_id, 'attributes_block');?>
</div>
<?
}
?>