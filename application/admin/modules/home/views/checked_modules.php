<fieldset>
<legend>Выбранные</legend>
<?php
if(isset($values) && count($values) > 0)
{

foreach($values as $ms)
		{	
?>
	<div class="block_w_fileld_main">
		<div class="block_w_field_bg" style="height:25px;">
			<div style="float:left; border:#000; margin:5px 5px 5px 5px;">
					<a class='arrow_down' href="<?=setUrl('*/change_position_module/id_module/'.$ms['id_users_modules'].'/change/down')?>" title='Смена позиции: Опустить'></a>
					<a class='arrow_up' href="<?=setUrl('*/change_position_module/id_module/'.$ms['id_users_modules'].'/change/up')?>" title='Смена позиции: Поднять'></a>
			</div>
			<div style="float:left; margin:5px 5px 5px 5px;">
				<label style="width:200px;"><?=$ms['alias']?></label>
			</div>
			<div style="float:right;">
				<div style="padding: 3px 0 0 0 ;">
					<a href="<?=set_url('*/delete_menu_modul/id_module/'.$ms['id_users_modules'])?>" class="icon_detele delete_question"></a>
				</div>
			</div>
		</div>
	</div>
<?php
			}
}
else
{
FALSE;
}
?>			
</fieldset>
