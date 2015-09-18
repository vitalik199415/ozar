<?php
if(!$Grid->AjaxOutput)
{
?>
<div align="center">
<div align="center" class="grid_block" id="<?=$Grid->GridName?>">
<?php
}
	if($buttons = $Grid->createButtons())
	{
	?>
	<div class="grid_buttons" id="grid_buttons">
		<div class="def_buttons"><?=$buttons?></div>
		
	<div style="display:none;" id="top_fixed_buttons">
		<div class="grid_buttons fixed_margin" id="<?=$Grid->GridName?>_hide_buttons" style="display:none;"><div class="def_buttons"><?=$buttons?></div></div>
	</div>
		
	</div>
	
	<?php
	}
if($ch_actions = $Grid->getSelectCheckboxActions())
{
?>
<div class="select_action_block">
	<div class="border">
		<div class="select_all"><a href="#" rel="check">Выбрать все элементы</a><span style="cursor:default;">&nbsp | &nbsp</span><a href="#" rel="uncheck">Снять выбраное</a></div>
		<div class="button">
			<a href="submit" id="submit">Применить</a>
		</div>
		<div class="select">
			<?=$ch_actions?>
		</div>
		<div class="text">
			Действие с выбраными элементами: 
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
<?php
}
echo form_open('');
?>
<table cellpadding="0" cellspacing="0" border="0" rules="none" align="center" class="grid_table">
<thead>
<tr class="field_name">
<td width="2"></td>
<td width="8%">
	<div class="normal">
	<div class="l"></div><div class="r"></div>
		<div class="cont">
		
		</div>
	</div>
</td>
<?php
	foreach($Grid->getGridObjects() as $ms)
	{	
	?>
		<td <?php if($ms->getOption('tdwidth')) echo 'width="'.$ms->getOption('tdwidth').'"';?>>
			<div class="normal">
			<div class="l"></div><div class="r"></div>
				<div class="cont">
				<?=$ms->getHtmlTitle();?>
				</div>
			</div>
		</td>
	<?
	}
?>
<td width="1"></td>
</tr>
</thead>
<tbody>
<?php
	foreach($Grid->getDataArray() as $ms)
	{
	?>
		<tr class="field_data field_data<?=$ms['level']?>" id="P_<?=$ms['id_parent']?>">
		<td width="1" style="background:#686868; border:none;"></td>
		<td valign="middle">
			<?php
			if(isset($ms['PARENT_COUNT']) && $ms['PARENT_COUNT']>0)
			{
			
			?>
				<div><a href="#" class="<?=$ms['have_chield']?>" id="<?=$ms['ID']?>" style="margin:0 0 0 <?=$ms['level']*10?>; <?php if($ms['level']>1) echo "border-left:2px solid #666666;";?>"></a></div>
			<?php
			}
			else
			{
			?>
				<div><a href="#" class="icon_plus" style="background:none; margin:0 0 0 <?=$ms['level']*10?>; <?php if($ms['level']>1) echo "border-left:2px solid #666666;";?>"></a></div>
			<?php
			}
			?>
		</td>
	<?
		
		foreach($Grid->getGridObjects() as $in)
		{
		?>
			<td valign="middle" <?=$in->getOption('option_string')?>>
				<div><?php echo $ms[$in->getOption('index')];?></div>
			</td>
		<?
		}
	?>
		<td width="1" style="background:#686868; border:none;"></td>
		</tr>
	<?
	}
?>
<tr>
	<td height="2px" style="background:#686868; border:none;" colspan="<?=count($Grid->getGridObjects())+2?>"></td>
</tr>
</tbody>
</table>
<?php 
echo form_close();
if(!$Grid->AjaxOutput)	
{
?>
</div>
</div>
<script>
$('#<?=$Grid->GridName?>').create_grid_tree();
</script>
<?php
}
/*
else
{
	foreach($Grid->getDataArray() as $ms)
	{
	?>
		<tr class="field_data field_data<?=$ms['level']?>" id="P_<?=$ms['id_parent']?>">
		<td width="1" style="background:#686868; border:none;"></td>
		<td valign="middle">
			<?php
			if(isset($ms['PARENT_COUNT']) && $ms['PARENT_COUNT']>0)
			{
			?>
				<div><a href="#" class="<?=$ms['have_chield']?>" id="<?=$ms['ID']?>" style="margin:0 0 0 <?=$ms['level']*10?>; <?php if($ms['level']>1) echo "border-left:2px solid #666666;";?>"></a></div>
			<?php
			}
			else
			{
			?>
				<div><a href="#" class="icon_plus" style="background:none; margin:0 0 0 <?=$ms['level']*10?>; <?php if($ms['level']>1) echo "border-left:2px solid #666666;";?>"></a></div>
			<?php
			}
			?>
		</td>
	<?	
		
		foreach($Grid->getGridObjects() as $in)
		{
		?>
			<td valign="middle" <?=$in->getOption('option_string')?>>
				<div><?php echo $ms[$in->getOption('index')];?></div>
			</td>
		<?
		}
	?>
		<td width="1" style="background:#686868; border:none;"></td>
		</tr>
	<?
	}
}*/
?>
