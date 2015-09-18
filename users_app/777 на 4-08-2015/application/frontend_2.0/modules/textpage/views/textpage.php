<?php
if(isset($textpage) && count($textpage)>0)
{
?>
<?
	foreach($textpage as $ms)
	{
		?>
			<div class="content_text">
                <div class="title"><?=$ms['name']?></div>
                <div class="text_block"><?=$ms['text']?></div>
			</div>
		<?
	}
?>
<?
}
?>