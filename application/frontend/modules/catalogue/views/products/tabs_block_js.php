<script>
	$('.tab').show();

	$(function() {
		// setup ul.tabs to work as tabs for each div directly under div.panes
		$("ul.tabs").tabs("div.panes > div" , {
			effect: 'slide',
			fadeOutSpeed: "slow"}
		);
	});
</script>