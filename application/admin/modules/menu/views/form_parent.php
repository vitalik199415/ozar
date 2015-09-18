<?php
if(isset($form_id))
{

	if(!isset($parrent)) $parrent = FALSE;
	$Form_object_parent = new Agform_block($values);
	$Form_object_parent->addObject(
		'select',
		'menu[level]',
		'Выберите уровень :',
		array(
			'options' => $level,
			'option' => array('id' => 'parent_level')
		)
	);
	$Form_object_parent->addObject(
			'html',
			'<div id="parent_menu">'
		);
	if($parent)
	{
		$Form_object_parent->addObject(
			'select',
			'menu[id_parent]',
			'Выберите родительскую категорию :',
			array(
				'options' => array('' => 'Корневая категория 1-го уровня') + $parent
			)
		);
	}
	$Form_object_parent->addObject(
			'html',
			'</div>'
		);
?>
<div id="menu_parent_block"  >

	<?=$Form_object_parent->BlockToHTML($form_id, 'parent_block'); ?>
</div>	
<script>
var cat_id = false;
<?php
	$URI = $this->uri->uri_to_assoc(3);
	if (isset($URI['id']) && intval($URI['id'])>0)
	{
		?>
			cat_id = <?=$URI['id']?>;
		<?
	}
?>
function updateParrents(data)
{
	$('#<?=$form_id?> #parent_menu').html(data);
}
$('#<?=$form_id?> #parent_level').live('change', function()
{
	if(cat_id != false)
	{
		var data = {level: $(this).val(), id: cat_id};
	}
	else
	{
		var data = {level: $(this).val()};
	}
	$.ajaxAG(
		{
			url: "<?=setUrl('*/load_menu')?>",
			type: "POST",
			data: data,
			success: function(d){updateParrents(d)}
		}
	);
});
</script>
<?	
}
?>