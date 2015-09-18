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
echo $Grid->getPagesHtml();
echo form_open('');
?>
<table cellpadding="0" cellspacing="0" border="0" rules="none" align="center" class="grid_table">
<thead>
<tr class="field_name">
<td width="2"></td>
<?php
	foreach($Grid->getGridObjects() as $ms)
	{	
	?>
		<td <?php if($ms->getOption('tdwidth')) echo 'width="'.$ms->getOption('tdwidth').'"';?>>
			<?php
			if(($actst = $Grid->getActiveSort($ms)) && $ms->getOption('sortable'))
			{
			?>
				<div class="active">
				<div class="l"></div><div class="r"></div>
				<div class="sorting">
					<?php
						if($actst == '1')
						{
							?>
								<a href="#" class="up" rel="<?=$ms->getOption('index')?>/DESC"></a>
							<?php
						}
						if($actst == '2')
						{
							?>
								<a href="#" class="down" rel="<?=$ms->getOption('index')?>"></a>
							<?php
						}	
					?>
				</div>
			<?php
			}
			else
			{
			?>
				<div class="normal">
				<div class="l"></div><div class="r"></div>
					<?php
					if($ms->getOption('sortable'))
					{
						?>
						<div class="sorting">
						<a href="#" class="up" rel="<?=$ms->getOption('index')?>/DESC"></a>
						<a href="#" class="down" rel="<?=$ms->getOption('index')?>"></a>
						</div>
						<?php
					}
					?>
			<?
			}
			?>
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
<tr class="field_search">
<td width="2" style="background:#686868; border:none;"></td>
<?php
	foreach($Grid->getGridObjects() as $ms)
	{
	?>
		<td valign="middle">
			<div align="center"><?php echo $ms->getHtmlSearch($Grid->getOutputSearch());?></div>
		</td>
	<?php
	}
?>
<td width="1" style="background:#686868; border:none;"></td>
</tr>
</thead>
<tbody>
<?php
	$i=0;
	foreach($Grid->getDataArray() as $ms)
	{
	?>
		<tr class="field_data <?php if($i) echo "field_data_d";?>">
		<td width="1" style="background:#686868; border:none;"></td>
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
		if($i) $i=0; else $i=1;	
	}
?>
<tr>
	<td height="2px" style="background:#686868; border:none;" colspan="<?=count($Grid->getGridObjects())+2?>"></td>
</tr>
</tbody>
</table>
<?php
	echo form_close();
	echo form_hidden('sort',$Grid->getOptions('sort').'/'.$Grid->getOptions('desc'));
	echo form_hidden('page',$Grid->getOptions('page'));
?>
<?php
if(!$Grid->AjaxOutput)
{
?>
</div>
</div>
<script>
$('#<?=$Grid->GridName?>').create_grid({url : '<?=$Grid->getOptions('url');?>', init_fixed_buttons : <?=$Grid->getOptions('init_fixed_buttons')?>});
</script>
<?php
}
?>