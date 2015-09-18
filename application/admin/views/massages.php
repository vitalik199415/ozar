<?php
if(isset($error_massages) || isset($success_massages))
{
?>

<div align="center" class="succes_error_massages_id" <?php if($this->template->is_ajax()){ ?> style="margin:0;" <?php } ?>>
<div class="succes_error_massages">
<?php
	if(isset($error_massages))
	{
		?>
		<div class="error_massage" align="left">
			<?php
			foreach($error_massages as $ms)
			{
				?><div><?=$ms?></div><?
			}
			?>
		</div>
		<?
	}
	if(isset($success_massages))
	{
		?>
		<div class="succes_massage" align="left">
			<?php
			foreach($success_massages as $ms)
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