<?php
if(isset($news) && count($news)>0)
{	
if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
?>
<div class="clear_both"></div>
<div class="modules_block news_block">	
<?
		foreach($news as $ms)
		{
		?>
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
							if(isset($ms['timage']))
							{
							?>
								<div class="image_block">
									<a href="<?=$ms['bimage']?>" class="highslide" onclick="return hs.expand(this)" title="<?=quotes_to_entities($ms['image_name'])?>">
										<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>">
									</a>
								</div>	
							<?php
							}
							?>
							</td>
							<td valign="top">
								<div class="name"><a <?php if(isset($ms['detail_url'])) echo 'href="'.$ms['detail_url'].'"';?>><?=$ms['name']?></a></div>
								<div class="date"><?=$ms['date']?></div>
								<?php
								if(trim($ms['short_description']) != '')
								{
								?><div class="description"><?=$ms['short_description']?></div><div class="clear_both"></div><div class="bottom_line"></div><?
								}
								?>
							</td>
							</tr>
							</table>
							<div class="delail"><?php if(isset($ms['detail_url'])) echo '<a href="'.$ms['detail_url'].'" class="detail_link"><span>'.$this->lang->line('base_detail_link_text').'</span></a>';?></div>
							<div class="clear_both"></div>
						</div>
					</div>
				</div>	
				<div class="base_bot">
					<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
				</div>
			</div>	
		<?
		}
		?>
	</div>
	<?
if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);		
?>
<div class="clear_both"></div>
<?
}
?>