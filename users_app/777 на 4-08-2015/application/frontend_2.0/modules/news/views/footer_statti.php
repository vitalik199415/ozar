<div class="last_news">
    <div id="sntop"><div class="snl">Последние статьи</div><div class="snr"><a href="<?=set_url('articles')?>">Все статьи</a> →</div></div>
    <div id="sncenter" class="nounl">
		<?php
        foreach($news as $ms)
        {
        ?>
            <div class="block" align="left">
            	<div style="color:#33cc99"><?=$ms['date']?></div>
                <div class="name"><a href="<?=$ms['detail_url']?>"><?=$ms['name']?></a></div>
                <div class="text_block">
                    <?=$ms['short_description']?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>
