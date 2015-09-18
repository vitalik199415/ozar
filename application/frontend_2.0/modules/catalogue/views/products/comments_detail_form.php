<form enctype="multipart/form-data" action="<?=$this->router->build_url('ajax_lang', array('ajax' => 'catalogue/products/save_product_comment/pr_id/'.$PRD_ID, 'lang' => $this->mlangs->lang_code));?>" method="post" id="product_comments_form_<?=$PRD_ID?>">
<div class="product_comment_form_block">
	<div class="form_block">
		<div class="form_label"><span><?=$this->lang->line('products_comments_add_comment')?> :</span></div>
			<div class="form_field"><label for="name"><?=$this->lang->line('products_comments_fname')?> 	(*):</label><input type="text" name="name"><div class="clear_both"></div></div>
			<div class="form_field"><label for="email"><?=$this->lang->line('products_comments_femail')?> 	(*):</label><input type="text" name="email"><div class="clear_both"></div></div>
			<div class="form_field"><label for="text"><?=$this->lang->line('products_comments_fcomment')?> 	(*):</label><textarea name="message" rows="4"></textarea><div class="clear_both"></div></div>
			<div class="captcha">
				<div class="form_field">
					<label for="captcha">
					<?=$this->lang->line('products_comments_fcaptcha')?> (*):
					<img src="/additional_libraries/kcaptcha/check.php?<?php echo session_name()?>=<?php echo session_id()?>&session_key=captcha_product_comment_keystring_<?=$PRD_array['product']['ID']?>&rand=<?=rand(1000, 9999)?>" id="captcha_img">
					</label><input type="text" name="captcha"><div class="clear_both"></div>
				</div>
			</div>
		<div class="submit"><a href="#" id="submit" class="big_button"><span><?=$this->lang->line('products_comments_fsubmit')?></span></a></div>
	</div>
</div>
</form>