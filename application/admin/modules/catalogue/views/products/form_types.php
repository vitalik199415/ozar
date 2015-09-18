<?php
if(isset($form_id) && isset($types) && count($types)>0)
{

$Form_object_type = new Agform_block($values);
foreach($types as $key => $ms)
{
	$display = 'none';
	if(isset($values['products_types'][$key]))
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
		'products_types['.$key.']',
		$ms,
		array(
			'value' => $key,
			'option' => array('class' => 'types')
		)
	);
	if(isset($properties[$key]))
	{
		$Form_object_type->addObjectTo($lid,
			'html',
			'<div style="padding:5px 0 0 30px; display:'.$display.'" id="properties_'.$key.'">'
		);
		foreach($properties[$key] as $key1 => $ms1)
		{
			$Form_object_type->addObjectTo($lid,
				'checkbox',
				'products_properties['.$key.']['.$key1.']',
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
<div id="pruduct_types_block">
	<?=$Form_object_type->BlockToHTML($form_id, 'types_block');?>
</div>
<?
}
?>