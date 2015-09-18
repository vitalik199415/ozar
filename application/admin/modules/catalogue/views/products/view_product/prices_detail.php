<?php
if(isset($PRD_array['prices_array']))
{
?>
<div class="prices_detail_block" id="prices_block">
<div class="block">
<div class="prices_block_label"><span>Цена</span></div>
<?php
foreach($PRD_array['prices_array'] as $ms)
{
	?>
	<div class="price_block" id="price_block">
	<?php
	if(count($PRD_array['prices_array']) > 1)
	{
		?><input type="radio" class="price_select" id="price_radio" name="price" value="<?=$ms['PRICE_ID']?>" <?php if($ms['selected_price']) echo 'checked="checked"';?>><?php
	}
	else
	{
		?><input type="radio" class="price_select hidden" id="price_radio" name="price" value="<?=$ms['PRICE_ID']?>" <?php if($ms['selected_price']) echo 'checked="checked"';?>><?php
	}
	if($ms['special_price_rate'])
	{
		?><span class="price_name"><?=$ms['price_name']?></span><span class="special_price_s"><s><?=$ms['price_rate_string']?></s> </span><span class="special_price"><?=$ms['special_price_rate_string']?> <?=$ms['currency_name']?></span>
		<?php
		if($ms['special_price_to_date'])
		{
			?><span class="special_price_to_date"><?=$this->lang->line('products_special_price_to')?> <?=$ms['special_price_to_date']?></span><?
		}
	}
	else
	{
		?><span class="price_name"><?=$ms['price_name']?></span><span class="price"><?=$ms['price_rate_string']?> <?=$ms['currency_name']?></span><?
	}
	if(strlen($ms['price_description'])>0)
	{
	?>
		<div class="price_description" id="price_description">
			<span><?=$ms['price_description']?></span>
		</div>
	<?php
	}
	?>
	</div>
	<?
}
?>
</div>
</div>
<?php
}
?>