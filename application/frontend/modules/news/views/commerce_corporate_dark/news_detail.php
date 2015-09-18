<?php
if(isset($news))
{
?>
<div class="modules_block news_detail_block">
	<div class="base_block">
		<div class="base_top">
			<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
		</div>
		<div class="base_center">
			<div class="base_center_left"></div><div class="base_center_right"></div>
			<div class="base_center_repeat">
				<div class="block">
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
					<td valign="top" width="1">
					<?php
					if(count($news['img'])>0)
					{
					?>
						<div class="image_block">
						<?
							foreach($news['img'] as $ms)
							{
								?>
									<a href="<?=$ms['bimage']?>" class="highslide" onclick="return hs.expand(this)" title="<?=quotes_to_entities($ms['image_name'])?>">
										<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" >
									</a>
								<?
							}
						?>
						</div>
					<?php
					}
					?>
					</td>
					<td valign="top">
						<div class="name"><?=$news['name']?></div>
						<div class="date"><?=$news['date']?></div>
						<div class="description"><?=$news['full_description']?></div><div class="clear_both"></div><div class="bottom_line"></div>
					</td>
					</tr>
					</table>
					<div class="back"><?php if(isset($news['back_url'])) echo '<a href="'.$news['back_url'].'" class="back_link"><span>'.$this->lang->line('base_back_link_text').'</span></a>';?></div>
				<div class="clear_both"></div>
				</div>
			</div>
		</div>
		<div class="base_bot">
			<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
		</div>
	</div>
</div>
<?
}
?>