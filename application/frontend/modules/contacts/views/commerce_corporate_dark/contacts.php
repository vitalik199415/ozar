<?php
if(isset($contacts) && count($contacts)>0)
{
$start_js_form = FALSE;
?>
<div class="modules_block contacts_block">
<div class="base_block">
	<div class="base_top">
		<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
	</div>
	<div class="base_center">
		<div class="base_center_left"></div><div class="base_center_right"></div>
		<div class="base_center_repeat">
		<?
		foreach($contacts as $ms)
		{
			?>
			<div class="block">
				<?php
				if(trim($ms['name']) != '') {?><div><h2><?=$ms['name']?></h2></div><?}
				?>
				<div><?=$ms['text']?></div>
			<?php
			if($ms['show_form'] == 1)
			{
				$start_js_form = TRUE;
				?>
				<div class="clear_both"></div>
				<form enctype="multipart/form-data" action="<?=$this->router->build_url('ajax_lang', array('ajax' => 'contacts/send_message', 'lang' => $this->mlangs->lang_code));?>" method="post" id="contacts_form">
				<div class="contacts_form_block">
					<div class="form_block">
						<div class="form_label"><?=$this->lang->line('contacts_form_label')?> :</div>
							<input type="hidden" name="id" value="<?=$ms['ID']?>">
							<div class="form_field"><label for="name"><?=$this->lang->line('contacts_form_fname')?> 	(*):</label><input type="text" name="name"><div class="clear_both"></div></div>
							<div class="form_field"><label for="email"><?=$this->lang->line('contacts_form_femail')?> 	(*):</label><input type="text" name="email"><div class="clear_both"></div></div>
							<div class="form_field"><label for="phone"><?=$this->lang->line('contacts_form_fphome')?> 	:</label><input type="text" name="phone"><div class="clear_both"></div></div>
							<div class="form_field"><label for="text"><?=$this->lang->line('contacts_form_ftext')?> 	(*):</label><textarea name="text" rows="4"></textarea><div class="clear_both"></div></div>
							<div class="captcha">
								<div class="form_field">
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
			</div>	
			<?
			}
			?><div class="clear_both"></div><?
		}
		?>
		</div>
	</div>	
	<div class="base_bot">
		<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
	</div>
</div>
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