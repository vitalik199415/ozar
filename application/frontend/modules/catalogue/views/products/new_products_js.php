<script>
var v_block = $("#<?=$PRS_new_block_id?>").find(".vertical_scroll");
var v_h = $("#<?=$PRS_new_block_id?>").find(".vertical_scroll_block").css("height");
$(v_block).css("height", parseFloat(v_h) - 81+"px").css("margin", "40px 0 0 0");
$(v_block).scrollable({ vertical: true, speed : 1000, mousewheel: true, keyboard : false, items : ".scroll_items", circular : true, next : "#<?=$PRS_new_block_id?> #products_carousel_vertical_block_down", prev : "#<?=$PRS_new_block_id?> #products_carousel_vertical_block_up" }).autoscroll({ autoplay: true, interval : 3000 });
</script>