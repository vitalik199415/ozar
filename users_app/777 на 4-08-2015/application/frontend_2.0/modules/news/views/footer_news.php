<div class="last_news_block">
    <div class="last_news_title">
        <span>Наши</span> новости
        <a href="/news">Все новости</a>    
    </div>

<?php
	foreach($news as $ms)
	{
	?>
		<div class="block">
        	<div class="last_news_img">
            	<?php
					if(isset($ms['timage']))
					{
					?>
						<div align="center"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>"></div>
					<?php
					}
				?>
            </div>
            <div class="last_news_text" align="left">
                <div class="last_news_name"><?=$ms['name']?></div>
                <div class="last_news_date"><?=$ms['date']?></div><br>
                <div class="last_news_description">
                    <?php
                        if(strlen($ms['short_description'])>411)
                        {
                         $points = "...";
                         $text = str_replace("&nbsp;", '', $ms['short_description']);
                         $text1 = substr($text, 4, 411);
                         $short_desc = $text1.$points;
                        echo $short_desc;
                        }
                        else echo $ms['short_description'];
                    ?>
                 </div>
            </div>
				
			<?php if(isset($ms['detail_url'])) echo '<a href="'.$ms['detail_url'].'" class="last_news_detail">'.$this->lang->line('base_detail_link_text').'</a>';?>	
		</div>
			<?php
			}
			?>
</div>
