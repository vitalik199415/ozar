<div class="block">
	<div class="cart_data_block">
		<div><span class="label"><?=$this->lang->line('cart_total_items')?> : </span><span class="data"><?=$total_items?></span></div>
		<div><span class="label"><?=$this->lang->line('cart_total_price')?> : </span><span class="data"><?=$total_price?></span></div>
	</div>
	<div class="buttons">
		<a href="#" class="big_button cart_edit" id="cart_edit_button"><span><?=$this->lang->line('cart_edit')?></span></a><a href="#" class="big_button cart_buy" id="cart_create_order_button"><span><?=$this->lang->line('cart_create_order')?></span></a>
	</div>
</div>