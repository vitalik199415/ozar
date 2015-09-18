<?php
if(isset($contacts) && count($contacts)>0)
{
$start_js_form = FALSE;
?>
<div class="contacts_block">
<?
	foreach($contacts as $ms)
	{
		?>
		<div class="block">
			<?php
			if(trim($ms['name']) != '') {?><div class="name"><h2><?=$ms['name']?></h2></div><?}
			?>
			<div class="text"><?=$ms['text']?></div>
		</div>
		<?php
		if($ms['show_form'] == 1)
		{
			$start_js_form = TRUE;
			?>
			<form enctype="multipart/form-data" action="<?=$this->router->build_url('ajax_lang', array('ajax' => 'contacts/send_message', 'lang' => $this->mlangs->lang_code));?>" method="post" id="contacts_form">
			<div class="contacts_form_block">
				<div class="form_block">
					<div class="form_label"><?=$this->lang->line('contacts_form_label')?> :</div>
						<input type="hidden" name="id" value="<?=$ms['ID']?>">
						<div class="contacts_field"><label for="name"><?=$this->lang->line('contacts_form_fname')?> 	(*):</label><input type="text" name="name"><div class="clear_both"></div></div>
						<div class="contacts_field"><label for="email"><?=$this->lang->line('contacts_form_femail')?> 	(*):</label><input type="text" name="email"><div class="clear_both"></div></div>
						<div class="contacts_field"><label for="phone"><?=$this->lang->line('contacts_form_fphome')?> 	:</label><input type="text" name="phone"><div class="clear_both"></div></div>
						<div class="contacts_field"><label for="text"><?=$this->lang->line('contacts_form_ftext')?> 	(*):</label><textarea name="text" rows="4"></textarea><div class="clear_both"></div></div>
						<div class="captcha">
							<div class="contacts_field">
								<label for="captcha">
								<?=$this->lang->line('contacts_form_fcaptcha')?> (*):
								<img src="/additional_libraries/kcaptcha/check.php?<?php echo session_name()?>=<?php echo session_id()?>&session_key=captcha_keystring_<?=$ms['ID']?>&rand=<?=rand(1000, 9999)?>" id="contacts_form_captcha_img_<?=$ms['ID']?>">
								</label><input type="text" name="captcha"><div class="clear_both"></div>
							</div>
						</div>
					<div class="form_massage_block" id="form_massage_block">
						<div class="error_massage" id="error">
							<div></div>
						</div>
						<div class="success_massage" id="success">
							<div></div>
						</div>
					</div>
					<div class="submit"><a href="#" id="submit"><?=$this->lang->line('contacts_form_fsubmit')?></a></div>
				</div>
			</div>	
			</form>
			<?
		}
		?><div class="clear:both"></div><?
	}
?>
</div>
	<?php
	if($start_js_form)
	{
	?>
		<script>
		$('#contacts_form').gbc_contacts('init',
		{
			error_submit : '<?=$this->lang->line('contacts_form_error_submit')?>'
		});
		</script>
	<?
	}

}
?>