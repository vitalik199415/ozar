<?php
if(isset($PRS_price_array))
{
?>
<div class="prices_short_block">
<div class="block">
<?php
foreach($PRS_price_array as $key => $ms)
{
	if($ms['special_price_rate'])
	{
		?><div class="price_block"><span class="price_name"><?=$ms['price_name']?></span><span class="special_price_s"><s><?=$ms['price_rate_string']?></s></span> <span class="special_price"><?=$ms['special_price_rate_string']?> <?=$ms['currency_name']?></span></div><?
	}
	else
	{
		?><div class="price_block"><span class="price_name"><?=$ms['price_name']?></span><span class="price"><?=$ms['price_rate_string']?> <?=$ms['currency_name']?></span></div><?
	}
}
if($PRS_price_access['prices_error_access_string'])
{
	?>
	<div class="price_access_error_block">
	<span class="price_access_error"><?=$PRS_price_access['prices_error_access_string']?></span>
	<?php
	if($PRS_price_access['prices_white_admin'])
	{
		?><a href="#" class="products_write_admin"><span><?=$this->lang->line('products_price_write_admin')?></span></a><?
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
?>