<?php
if(isset($description) && trim($description) != '')
{
	?>
		<div id="cat_descr" class="block" align="center">
			<?=$description?>
		</div>
	<?
}
?>
<script>
    $('#cat_descr').readmore({
        speed: 500,
        maxHeight: 63,
        moreLink: '<a href="#" id="more2">Развернуть</a>',
        lessLink: '<a href="#" id="less2">Свернуть</a>'
    });
</script>