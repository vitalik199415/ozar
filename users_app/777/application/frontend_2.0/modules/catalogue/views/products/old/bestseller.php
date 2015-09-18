
<div class="novelty_title">хит продаж</div>
<div class="novelty">
<div id="vWrapper">
<div id="carouselv">
<?php
if(isset($products))
{
	foreach($products as $ms)
	{
	?>
	<div>
			<?php
			if(isset($ms['timage']))
			{
			?>
				<a href="<?=$ms['detail_url']?>"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" /></a>
			<?php
			}
			?>				
				<span class="thumbnail-text"><?=$ms['price']?></span>
			
	</div>
	<?
	}
}
?>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#carouselv').jsCarousel({ onthumbnailclick: function(src) { alert(src); }, autoscroll: true, circular: true, masked: false, itemstodisplay: 4, orientation: 'h' });
	});       
</script>
</div>