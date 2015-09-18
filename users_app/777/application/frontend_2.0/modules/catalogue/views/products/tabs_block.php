<div class="tabs_block">
	<ul class="tabs">
		<?php
			
			if(isset($PRD_array['images_array']) && count($PRD_array['images_array'])>0)
			{
				?>
					<li><a href="#"><div><?=$this->lang->line('products_photo')?></div></a></li>
				<?
			}
			if(trim(strip_tags($PRD_array['product']['full_description'])) != '')
			{
				?>
					<li><a href="#"><div>Таблицы размеров</div></a></li>
				<?
			}
			if($PRD_array['product_settings']['reviews_on'] == 1)
			{
				?>
					<li><a href="/reviews"><div><?=$this->lang->line('products_review')?></div></a></li>
				<?
			}
			if($PRD_array['product_settings']['related_on'] == 1 && count($PRD_array['related_products']['related_products']) > 0)
			{
				?>
					<li><a href="#" id="similar"><div><?=$this->lang->line('products_similar')?></div></a></li>
				<?
			}
		?>
	</ul>
	<div class="panes">
		<?php
		
		if(isset($PRD_array['images_array']) && count($PRD_array['images_array'])>0)
		{
		?>
		<div class="tab">
			<script>
				var pr_config_tab_highslide_album = {
					slideshowGroup: 'pr_tab_album_group_<?=$PRD_array['product']['ID']?>',
					transitions: ['expand', 'crossfade']
				};
			</script>
			<div class="tab_image_block" >
				<?php
					foreach($PRD_array['images_array'] as $ms)
						{
							?>
								<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this, pr_config_tab_highslide_album)">
									<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
								</a>
							<?
						}
				?>
			</div>
		</div>
		<?
		}
		if(trim(strip_tags($PRD_array['product']['full_description'])) != '')
		{
			?>
				<div class="tab">
					<?=$this->template->get_temlate_view('PRD_description_'.$PRD_ID);?>
				</div>
			<?
		}
		if($PRD_array['product_settings']['reviews_on'] == 1)
		{
		?>
		<div class="tab">
			<?=$this->template->get_temlate_view('PRD_comments_block_'.$PRD_ID);?>
			<div style="clear:both;"></div>
		</div>
		<?
		}
		if($PRD_array['product_settings']['related_on'] == 1 && count($PRD_array['related_products']['related_products']) > 0)
		{
		?>
		<div class="tab" id="related_content">
			<?=$this->template->get_temlate_view('PRD_similar_'.$PRD_ID);?>
		</div>
		<?
		}
		?>
	</div>
</div>