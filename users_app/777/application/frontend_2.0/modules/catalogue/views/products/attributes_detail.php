<?php
if(isset($PRD_array['attributes_array']) && count($PRD_array['attributes_array'])>0)
{
?>
<div class="attributes_block" id="attributes_block">
	<div class="block">
		
			<?php
			foreach($PRD_array['attributes_array']['attributes'] as $ms)
			{
			?>
				<div class="attribute_block">
					
						<div class="attribute_select_label" rel="<?=$ms['ID']?>">
							
								<span><?=$ms['a_name']?>:</span>
							
						</div>
					
				
					
						<div class="attribute_select_block" rel="<?=$ms['ID']?>"><?=form_dropdown("attributes[".$ms['ID']."]", $PRD_array['attributes_array']['options'][$ms['ID']], '', 'rel="'.$ms['ID'].'" class="select_attributes"')?>
						</div>
				</div>	
				
			<?
			}
			?>
		
	</div>
</div>
<?
}
?>