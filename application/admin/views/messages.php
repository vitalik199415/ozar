<?php
if(isset($error_messages) || isset($success_messages))
{
?>

<div align="center" class="succes_error_massages_id" <?php if($this->template->is_ajax()){ ?> style="margin:0;" <?php } ?>>
<div class="succes_error_massages">
<?php
	if(isset($error_messages))
	{
		?>
		<div class="error_massage" align="left">
			<?php
			foreach($error_messages as $ms)
			{
				?><div><?=$ms?></div><?
			}
			?>
		</div>
		<?
	}
	if(isset($success_messages))
	{
		?>
		<div class="succes_massage" align="left">
			<?php
			foreach($success_messages as $ms)
			{
				?><div><?=$ms?></div><?
			}
			?>
		</div>
		<?
	}
?>
</div>
</div>	
<script>
	$('.succes_error_massages_id').initSEmassages();
</script>
<?php
}
?>