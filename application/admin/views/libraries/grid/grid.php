<?php
if(!$grid->ajax_output)
{
?>
<div align="center">
<div align="center" class="grid_block" id="<?=$grid->get_grid_name()?>">
<?php
}
	if($buttons = $grid->render_buttons())
	{
		if($grid->init_fixed_buttons)
		{
			?>
			<div class="grid_buttons" id="grid_buttons">
				<div class="def_buttons"><?=$buttons?></div>
				<div style="display:none;" id="top_fixed_buttons">
					<div class="grid_buttons fixed_margin" id="<?=$grid->get_grid_name()?>_hide_buttons" style="display:none;"><div class="def_buttons"><?=$buttons?></div></div>
				</div>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="grid_buttons" id="grid_buttons">
				<div class="def_buttons"><?=$buttons?></div>
			</div>
			<?php
		}	
	}
if($ch_actions = $grid->render_select_actions_for_checkbox())
{
?>
<div class="select_action_block">
	<div class="border">
		<div class="select_all"><a href="#" rel="check">Выбрать все элементы</a><span style="cursor:default;">&nbsp | &nbsp</span><a href="#" rel="uncheck">Снять выбраное</a></div>
		<?php
		if($ch_actions !== TRUE)
		{
		?>
			<div class="button">
				<a href="submit" id="submit">Применить</a>
			</div>
			<div class="select">
				<?=$ch_actions?>
			</div>
			<div class="text">
				Действие с выбраными элементами: 
			</div>
		<?php
		}
		?>
		<div style="clear:both;"></div>
	</div>
</div>
<?php
}
echo $grid->render_pages();
//echo form_open('');
?>
<table cellpadding="0" cellspacing="0" border="0" rules="none" align="center" class="grid_table">
<thead>
<tr class="field_name">
<td width="2"></td>
<?php
	foreach($grid->get_columns() as $ms)
	{	
	?>
		<td <?php if($ms->get_options('tdwidth')) echo 'width="'.$ms->get_options('tdwidth').'"';?>>
			<?php
			if(($actst = $grid->get_sort($ms)) && $ms->get_options('sortable'))
			{
			?>
				<div class="active">
				<div class="l"></div><div class="r"></div>
				<div class="sorting">
					<?php
						if($actst == '1')
						{
							?>
								<a href="#" class="up" rel="<?=$ms->get_options('index')?>/DESC"></a>
							<?php
						}
						if($actst == '2')
						{
							?>
								<a href="#" class="down" rel="<?=$ms->get_options('index')?>"></a>
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
					if($ms->get_options('sortable'))
					{
						?>
						<div class="sorting">
						<a href="#" class="up" rel="<?=$ms->get_options('index')?>/DESC"></a>
						<a href="#" class="down" rel="<?=$ms->get_options('index')?>"></a>
						</div>
						<?php
					}
					?>
			<?
			}
			?>
			<div class="cont">
			<?=$ms->render_title();?>
			</div>
			</div>
		</td>
	<?
	}
?>
<td width="1"></td>
</tr>
<?php
if($grid->filter_block)
{
?>
<tr class="field_search">
<td width="2" style="background:#686868; border:none;"></td>
<?php
	foreach($grid->get_columns() as $ms)
	{
	?>
		<td valign="middle">
			<div align="center"><?php echo $ms->render_search($grid->get_search());?></div>
		</td>
	<?php
	}
?>
<td width="1" style="background:#686868; border:none;"></td>
</tr>
<?php
}
?>
</thead>
<tbody>
<?php
	$i=0;
	foreach($grid->get_grid_data() as $ms)
	{
	?>
		<tr class="field_data <?php if($i) echo "field_data_d";?>">
		<td width="1" style="background:#686868; border:none;"></td>
	<?	
		
		foreach($grid->get_columns() as $in)
		{
		?>
			<td valign="middle" <?=$in->get_options('option_string')?>>
				<div><?php echo $ms[$in->get_options('index')];?></div>
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
	<td height="2px" style="background:#686868; border:none;" colspan="<?=count($grid->get_columns())+2?>"></td>
</tr>
</tbody>
</table>
<?php
	//echo form_close();
	echo form_hidden('sort',$grid->get_options('sort').'/'.$grid->get_options('desc'));
	echo form_hidden('page',$grid->get_options('page'));
?>
<?php
if(!$grid->ajax_output)
{
?>
</div>
</div>
<script>
$('#<?=$grid->get_grid_name()?>').create_grid({url : '<?=$grid->get_options('url');?>', init_fixed_buttons : <?=$grid->init_fixed_buttons?>});
//$('#<?=$grid->get_grid_name()?>').create_grid();
//$().create_grid('<?=$grid->get_grid_name()?>', {url : '<?=$grid->get_options('url');?>', init_fixed_buttons : <?=$grid->init_fixed_buttons?>});
</script>
<?php
}
?>