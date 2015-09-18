<?php
if(isset($PRD_block_id) && isset($PRD_array))
{
	?>
	<script>
	var prices_attributes = {};
	var prices_rules = {};
	var albums_attributes = {};
	<?php
	if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
	{
		foreach($PRD_array['albums_array'] as $ms)
		{
			if($ms['ATTR_ID'] != NULL && $ms['OPT_ID'] != NULL)
			{
			?>
			albums_attributes[<?=$ms['ALBUM_ID']?>] = {};
			albums_attributes[<?=$ms['ALBUM_ID']?>]['attr'] = <?=$ms['ATTR_ID']?>;
			albums_attributes[<?=$ms['ALBUM_ID']?>]['opt'] = <?=$ms['OPT_ID']?>;
			<?
			}
		}
	}
	
	if(isset($PRD_array['prices_array']) && count($PRD_array['prices_array'])>0)
	{
		foreach($PRD_array['prices_array'] as $ms)
		{
			?>
			prices_attributes[<?=$ms['PRICE_ID']?>] = {};
			prices_rules[<?=$ms['PRICE_ID']?>] = {};
			
			prices_attributes[<?=$ms['PRICE_ID']?>]['show_attributes'] = '<?=$ms['price_attributes_js_array']['show_attributes']?>';
			prices_attributes[<?=$ms['PRICE_ID']?>]['id_attributes'] = {};
			
			prices_rules[<?=$ms['PRICE_ID']?>]['min_qty'] = <?=$ms['min_qty']?>;
			prices_rules[<?=$ms['PRICE_ID']?>]['real_qty'] = <?=$ms['real_qty']?>;
			<?php
			foreach($ms['price_attributes_js_array']['id_attributes'] as $ms1)
			{
			?>	
			prices_attributes[<?=$ms['PRICE_ID']?>]['id_attributes'][<?=$ms1?>] = '<?=$ms1?>';
			<?php
			}
		}
	}
	?>
	$('#<?=$PRD_block_id?>').gbc_products_detail({
		prices_attributes : prices_attributes,
		prices_rules : prices_rules,
		albums_attributes : albums_attributes,
		pcs : 'шт.',
		product_id : <?=$PRD_ID?>
	});
	order_object.gbc_orders('init_view_product', <?=$PRD_ID?>, prices_attributes, <?=$ORD_ID?>);
	</script>
<?php
}
?>