<?php
if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
?>
<div class="modules_block reviews_block">
<div class="base_block">
<div class="base_top">
	<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
</div>
<div class="base_center">
	<div class="base_center_left"></div><div class="base_center_right"></div>
	<div class="base_center_repeat">
		<div class="block">
<?
if(isset($reviews) && count($reviews)>0)
{
	foreach($reviews as $ms)
	{
		?>
			<div class="review">
				<div class="name"><?=$ms['name']?></div>
				<div class="description"><?=$ms['review']?></div>
				<? if(isset($ms['answer'])) { ?>
					<div class="description_answer"><?=$ms['answer']?></div>
				<? }?>
			</div>
		<?
	}
}
?>
			<div class="clear_both"></div>
			<form enctype="multipart/form-data" action="<?=$this->router->build_url('ajax_lang', array('ajax' => 'reviews/save_review', 'lang' => $this->mlangs->lang_code));?>" method="post" id="reviews_form">
				<div class="reviews_form_block">
					<div class="form_block">
						<div class="form_label"><?=$this->lang->line('reviews_form_label')?> :</div>
							<input type="hidden" name="id_users_modules" value="<?=$id_users_modules?>">
							<div class="form_field"><label for="name"><?=$this->lang->line('reviews_form_fname')?> 	(*):</label><input type="text" name="name"><div class="clear_both"></div></div>
							<div class="form_field"><label for="email"><?=$this->lang->line('reviews_form_femail')?> 	(*):</label><input type="text" name="email"><div class="clear_both"></div></div>
							<input type="hidden" value="1" name="mail_notification">
							<div class="form_field"><label for="text"><?=$this->lang->line('reviews_form_freview')?> 	(*):</label><textarea name="review" rows="4"></textarea><div class="clear_both"></div></div>
							<div class="captcha">
								<div class="form_field">
									<label for="captcha">
									<?=$this->lang->line('reviews_form_fcaptcha')?> (*):
									<img src="/additional_libraries/kcaptcha/check.php?<?php echo session_name()?>=<?php echo session_id()?>&session_key=captcha_keystring&rand=<?=rand(1000, 9999)?>" id="reviews_form_captcha_img">
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
						<div class="submit"><a href="#" id="submit"><?=$this->lang->line('reviews_form_fsubmit')?></a></div>
					</div>
				</div>	
			</form>
		</div>
	</div>
</div>	
<div class="base_bot">
	<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
</div>
</div>
</div>
<?php
if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
?>
<script>
$('#reviews_form').gbc_reviews('init',
{
	error_submit : '<?=$this->lang->line('reviews_form_error_submit')?>'
});
</script>