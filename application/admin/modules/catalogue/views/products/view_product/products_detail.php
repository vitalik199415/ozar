<?php
if(isset($PRD_array) && isset($PRD_block_id))
{
?>
	<div class="clear_both"></div>
	<div class="product_detail_block" id="<?=$PRD_block_id?>">
		<div class="block">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
			<td valign="top" width="1">
			<?=$this->load->view('catalogue/products/view_product/images_detail', array(), TRUE);?>
			</td>
			<td valign="top">
				<div class="product_name"><span class="value"><?=$PRD_array['product']['name']?></span></div>
				<div class="product_sku"><span class="label">Артикул</span> : <span class="value"><?=$PRD_array['product']['sku']?></span></div>
				<?
				if($PRD_array['product']['bestseller']) echo '<div class="BNS_label"><span>Хит продаж</span></div>';
				if($PRD_array['product']['new']) echo '<div class="BNS_label"><span>Новинка</span></div>';
				if(!$PRD_array['product']['in_stock'])
				{
					?><div><div class="BNS_label"><span>Нет в наличии</span></div></div><?
				}
				if($PRD_array['product']['in_stock'] && $PRD_array['product']['sale'])
				{
					?><div><div class="BNS_label"><span>Акция</span></div></div><?
				}
				?>
				<?=$this->load->view('catalogue/products/view_product/albums_detail', array(), TRUE);?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="prices_n_attributes">
				<tr>
				<td width="70%" valign="top">
					<div class="product_price">
						<?=$this->load->view('catalogue/products/view_product/prices_detail', array(), TRUE);?>
					</div>
				</td>
				<td width="30%" valign="bottom" align="right">
					<div class="products_attributes">
						<?=$this->load->view('catalogue/products/view_product/attributes_detail', array(), TRUE);?>
					</div>
				</td>
				</tr>
				</table>
                <div style="padding:10px 0">
                    <h2>Краткое описание</h2>
                    <?=$PRD_array['product']['short_description']?>
                </div>
				<div style="padding:10px 0">
                    <h2>Полное описание</h2>
				    <?=$PRD_array['product']['full_description']?>
				</div>
			</td>
			</tr>
			</table>
		</div>
	</div>
	<div class="clear_both"></div>
<?
}
?>