<script>
	var active_array = JSON.parse('<?php echo json_encode($active_sort); ?>');
	var prod_per_page_array = JSON.parse('<?php echo json_encode($prod_per_page); ?>');

	$("#sorting_ui_block").gbcSorting({activeSortArr: active_array, prodPerPageArr: prod_per_page_array});
</script>