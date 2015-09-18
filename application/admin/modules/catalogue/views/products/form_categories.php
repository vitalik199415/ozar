<style>
.field_block #pruduct_types_block .block_w_field_main
{
margin:2px 0 2px 0 !IMPORTANT;
}
</style>
<?php
if($categories)
{
$Form_object_categories = new Agform_block($values);
foreach($categories as $key => $ms)
{
	$Form_object_categories->addObject(
		'html',
		'<div style="padding:0 0 0 '.($ms['level']*40).'px;">'
	);
	$Form_object_categories->addObject(
		'checkbox',
		'products_categories['.$ms['ID'].']',
		$ms['name'],
		array(
			'value' => $ms['ID']
		)
	);
	$Form_object_categories->addObject(
		'html',
		'</div>'
	);
}
?>
<div id="pruduct_types_block">
	<?=$Form_object_categories->BlockToHTML($form_id, 'categories_block');?>
</div>
<?php
}
?>