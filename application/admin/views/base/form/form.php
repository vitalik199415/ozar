<div align="center">
<div id="<?=$Form->getId();?>" class="main_form_block">
<?php
if($buttons = $Form->createButtons())
{
?>
<div class="form_buttons" id="form_buttons">
	<div class="def_buttons"><?=$buttons?></div>
	
	<div style="display:none;" id="top_fixed_buttons">
		<div class="form_buttons fixed_margin" id="hide_buttons" style="display:none;" align="right"><div class="def_buttons"><?=$buttons?></div></div>
	</div>
</div>
<?php
}
?>
<?=form_open_multipart($Form->getHref(),array('id'=>'form_'.$Form->getId(), 'name'=>'form_'.$Form->getId()))?>
<div class="form_block">
<div class="form_block_padding">
<?php
if($TABS = $Form->getTabs())
{
?>	
	<div class="tabs_block">	
	<ul>
	<?php
	foreach($TABS as $key => $ms)
	{
		?>
		<li><a href="#<?=$key?>" class="href"><?=$ms['name']?></a></li>
		<?php
	}
	?>
	</ul>
	</div>
	<div class="block">
	<div class="block_padding">
	<?php
	foreach($TABS as $key=>$ms)
	{
		?>
		<div class="field_block">
			<?php
			foreach($Form->getBlock($key) as $bkey => $bms)
			{
				echo $bms->BlockToHTML($Form->getId(), $key.$bkey);
			}
			?>
		</div>
		<?php
	}	
	?>
	</div>
	</div>
<?php	
}
else
{
?>
<div class="block" style="float:none; width:100%">
<div class="block_padding">
<div class="field_block">
<?
foreach($Form->getBlock() as $bkey => $bms)
	{
		echo $bms->BlockToHTML($Form->getId(), '', TRUE);
	}
?>
</div>
</div>
</div>
<?	
}	
?>
<div style="clear:both;"></div>
</div>
</div>
<?=form_close()?>
<?=$Form->getHtmlCode();?>
</div>
</div>
<script>
$("#<?=$Form->getId();?>").create_form('<?=$Form->getId();?>');
<?=$Form->getJsCode();?>
</script>