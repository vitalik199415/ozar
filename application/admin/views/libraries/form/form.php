<div align="center">
<div id="<?=$form->get_id();?>" class="main_form_block">
<?php
if($buttons = $form->create_buttons())
{
?>
<div style="height:45px;">
<div class="form_buttons" id="form_buttons">
	<div class="def_buttons"><?=$buttons?></div>
</div>
</div>
<?php
}
?>
<?=form_open_multipart($form->get_href(), array('id' => 'form_'.$form->get_id()))?>
<div class="form_block">
<div class="form_block_padding">
<?php
if($TABS = $form->get_tabs())
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
			foreach($form->get_block($key) as $bkey => $bms)
			{
				echo $bms->block_to_HTML($form->get_id(), $key);
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
foreach($form->get_block() as $bkey => $bms)
	{
		echo $bms->block_to_HTML($form->get_id(), '', TRUE);
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
<?=$form->get_html_code();?>
</div>
</div>

<script>
$("#<?=$form->get_id();?>").ag_form();
<?=$form->render_js_validation();?>
<?=$form->render_js_inputmask();?>
<?=$form->get_js_code();?>
</script>