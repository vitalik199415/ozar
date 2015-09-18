<?php
if(isset($order_data['products']['cart_products']) && count($order_data['products']['cart_products'])>0)
{
?>
<div class="form_block create_order_form">
<form enctype="multipart/form-data" action="<?=$this->router->build_url('order_methods_lang', array('method' => 'save', 'lang' => $this->mlangs->lang_code));?>" method="post" id="create_order">
<div class="form_label order_label"><span><?=$this->lang->line('c_o_form_creating_order')?></span></div>
<?=$this->template->get_temlate_view('customers_form');?>
<div class="form_help create_order_help">
	<span><?=$this->lang->line('c_o_help_required_fields')?></span>
</div>
<div class="payment_block" id="order_payment_block">
<?=$this->template->get_temlate_view('payment_methods_form');?>
</div>
<div class="shipping_block" id="order_shipping_block">
<?=$this->template->get_temlate_view('shipping_methods_form');?>
</div>
<div class="form_item_block cart_product_block">
<div class="form_item_inside_block">
	<div class="form_field order_field note_field"><label for="order[note]"><?=$this->lang->line('c_o_form_note')?> :</label><textarea type="text" name="order[note]" rows="4"></textarea><div class="clear_both"></div></div>
</div>
</div>

<div class="form_label order_label"><span><?=$this->lang->line('c_o_form_activate_code')?></span></div>
<div class="form_item_block cart_product_block activate_item_block">
	<div class="form_item_inside_block">
		<div class="form_field order_field note_field"><label for="promocode"><?=$this->lang->line('c_o_activate_note')?> *:</label><input type="text" name="promocode" id="promocode" value="<?=@$order_data['promocode']?>"><div class="clear_both"></div></div>
	</div>
	<div class="form_message_block activate_message_block">
		<div class="error_message">
			<div></div>
		</div>
		<div class="success_message">
			<div></div>
		</div>
	</div>
	<div class="form_button order_button submit_order"><a href="#" id="activate"><span><?=$this->lang->line('c_o_activate_submit')?></span></a></div>
</div>

<div class="form_message_block create_order_message_block" id="order_message_block">
	<div class="error_message">
		<div></div>
	</div>
	<div class="success_message">
		<div></div>
	</div>
</div>
<div class="form_button order_button submit_order"><a href="#" id="order_submit"><span><?=$this->lang->line('c_o_form_order_submit')?></span></a></div>
	
<div id="order_products"><?=$this->template->get_temlate_view('order_products');?></div>

<div class="form_message_block create_order_message_block" id="order_message_block_bot">
	<div class="error_message">
		<div></div>
	</div>
	<div class="success_message">
		<div></div>
	</div>
</div>
<div class="form_button order_button submit_order"><a href="#" id="order_submit_bot"><span><?=$this->lang->line('c_o_form_order_submit')?></span></a></div>
</form>
</div>
<?php
}
else
{
?>
<div class="form_block create_order_form">
<div class="form_label order_label"><span><?=$this->lang->line('cart_is_empty')?></span></div>
</div>
<?
}
?>