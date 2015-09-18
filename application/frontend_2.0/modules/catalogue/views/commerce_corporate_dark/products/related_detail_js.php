<script>
setTimeout(function()
{
var tab_related_products_hide = false;
var tab_related_products = $("#tab_related_products_<?=$PRD_ID?>").parents('.tab');
if(tab_related_products.is(":hidden")) { tab_related_products.show(); tab_related_products_hide = true;}
$('#tab_related_products_<?=$PRD_ID?>').anythingSlider({
	showMultiple : 3,
	changeBy     : 3,
	navigationSize : 18,
	resizeContents : false,
	buildStartStop : false,
	hashTags : false
});
if(tab_related_products_hide) tab_related_products.hide();
}, 1000);
</script>