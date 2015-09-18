<div class="form_message_block site_message_message_block" id="site_message_message_block">
	<?php
	if(isset($error_message))
	{
	?>
	<div class="error_message" id="error">
		<div><?=$error_message?></div>
	</div>
	<?php
	}
	?>
	<?php
	if(isset($success_message))
	{
	?>
	<div class="success_message" id="success">
		<div><?=$success_message?></div>
	</div>
	<?php
	}
	?>
</div>