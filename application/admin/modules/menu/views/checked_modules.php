<fieldset>
<legend>Выбранные</legend>
<?php
if(isset($values) && count($values) > 0)
{
?>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="color:#FFFFFF;" bordercolor="#999999" rules="all">
<thead>
	<tr>
		<td align="left" width="20%"><b>Позиция</b></td>
		<td align="left" width="40%"><b>Модуль</b></td>
		<td align="left" width="20%"><b>Основной</b></td>
		<td align="left" width="20%"><b>Действия</b></td>
	</tr>
</thead>
<tbody>
	<?php
	foreach($values as $ms)
	{
	?>
		<tr style="font-size:12px;">
		<td align="center">
			<a class='arrow_down' href="<?=set_url('*/change_position_module/id/'.$id.'/id_module/'.$ms['id_users_modules'].'/change/down')?>" title='Смена позиции: Опустить'></a>
			<a class='arrow_up' href="<?=set_url('*/change_position_module/id/'.$id.'/id_module/'.$ms['id_users_modules'].'/change/up')?>" title='Смена позиции: Поднять'></a>
		</td>
		<td align="left">
			<?=$ms['alias']?>
		</td>
		<td align="center">
			<input type="radio" name="base_module" value="<?=$ms['id_users_modules']?>" <?php if($ms['base_module'] == 1) echo 'checked="checked"';?> />
		</td>
		<td align="center">
			<a href="<?=set_url('*/delete_menu_modul/id/'.$id.'/id_module/'.$ms['id_users_modules'])?>" class="icon_detele delete_question"></a>
		</td>
		</tr>
	<?php
	}
	?>	
</tbody>	
</table>
<script>
	$("input[type='radio'][name='base_module']").change(function()
	{
		var id = $(this).val();
		jQuery.ajaxAG(
		{
			url: '<?=set_url('*/change_base_module')?>',
			type: "POST",
			data: {id: id, id_menu : <?=$id?>},
			success: function(d)
			{
				alert(d);
			}
		});
	});
</script>
<!--			
	<div class="block_w_fileld_main">
		<div class="block_w_field_bg" style="height:25px;">
			<div style="float:left; border:#000; margin:5px 5px 5px 5px;">
					<a class='arrow_down' href="<?=setUrl('*/change_position_module/id/'.$id.'/id_module/'.$ms['id_users_modules'].'/change/down')?>" title='Смена позиции: Опустить'></a>
					<a class='arrow_up' href="<?=setUrl('*/change_position_module/id/'.$id.'/id_module/'.$ms['id_users_modules'].'/change/up')?>" title='Смена позиции: Поднять'></a>
			</div>
			<div style="float:left; margin:5px 5px 5px 5px;">
				<label style="width:200px;"><?=$ms['alias']?></label>
			</div>
			<div style="float:right;">
				<div style="padding: 3px 0 0 0 ;">
					<a href="<?=setUrl('*/delete_menu_modul/id/'.$id.'/id_module/'.$ms['id_users_modules'])?>" class="icon_detele delete_question"></a>
				</div>
			</div>
		</div>
	</div>
-->	
<?php
}
else
{
FALSE;
}
?>			
</fieldset>
