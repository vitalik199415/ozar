<?php
if(count($active_filters_array) > 0)
{
?>
	<div  id="active_filters_additional_block" class="ui-modified" align="center">
		<div class="block">
			<div class="title">
				<?=$this->lang->line('products_filter_choose')?>
			</div>
				<?
				foreach($active_filters_array as $key => $ms)
				{
					switch($ms['type_kind'])
					{
						case 'checkbox':	
						?>
						<div class="<?=$ms['type_kind']?>_filter_block filter_block">
							<div class="block">
								<div class="group_name">
									<span>
										<?=$ms['group_name']?>
									</span>
								</div>
								<div class="properties_block">
								<?
								foreach($ms['options_active'] as $chkey => $chms) {
							
								$checked = true;
								$checkbox_data = array(
									'type' => 'checkbox',
									'id' => 'filter_checkbox_'.$chkey.'_active_additional',
									'name' => 'products_active_filters['.$key.']['.$chkey.']',
									'value' => 'products_filters['.$key.']['.$chkey.']',
									'checked' => $checked
								);
								?>
									<div class="property">
										<div class="checkbox"><label for="filter_checkbox_<?=$chkey?>_active_additional"></label><?=form_checkbox($checkbox_data)?></div><span class="checkbox_name"><?=$chms['pname']?></span><i></i>
									</div>

									<div class="clear_both"></div>
								<?
								}
								?>
								</div>
							</div>
						</div>
						<?
						break;
						case 'additional':
						?>
						<div class="<?=$ms['type_kind']?>_filter_block filter_block">
							<div class="block">
								<div class="group_name">
									<span>
										<?=$ms['group_name']?>
									</span>
								</div>
								<div class="properties_block">
							<?
								foreach($ms['options_active'] as $chkey => $chms) {

									$checked = true;
									$checkbox_data = array(
										'type' => 'checkbox',
										'id' => 'filter_checkbox_'.$chkey.'_active',
										'name' => 'products_active_filters['.$key.']['.$chkey.']',
										'value' => 'products_filters_additional['.$chkey.']',
										'checked' => $checked
									);
									?>
									<div class="property">
										<div class="checkbox"><label for="filter_checkbox_<?=$chkey?>_active"></label><?=form_checkbox($checkbox_data)?></div><span class="checkbox_name"><?=$chms['pname']?></span><i></i>
									</div>
									<?
									}
									?>
								<div class="clear_both"></div>
								</div>
							</div>
						</div>
							<?
						break;
						case 'dropdown':
						
						break;
						case 'dropdown_checkbox':
						
						break;
						case 'color':
						?>
						<div class="<?=$ms['type_kind']?>_filter_block filter_block">
							<div class="block">
								<div class="group_name">
									<span>
										<?=$ms['group_name']?>
									</span>
								</div>
								<div class="properties_block">
								<?
								foreach($ms['options_active'] as $chkey => $chms) {
							
								$checked = true;
								$checkbox_data = array(
															'type' => 'checkbox',
															'id' => 'filter_checkbox_'.$chkey.'_active_additional',
															'name' => 'products_active_filters['.$key.']['.$chkey.']',
															'value' => 'products_filters['.$key.']['.$chkey.']',
															'checked' => $checked
								);
								?>
								<div class="property">
									<div class="checkbox">
										<label for="filter_checkbox_<?=$chkey?>_active_additional">
											<div style="background:#<?=$chms['id_color']?>" class="bgcolor">
											</div>
										</label>
										<?=form_checkbox($checkbox_data)?>
									</div>
									<div class="clear_both"></div>
								</div>
								<?
								}
								?>
								<div class="clear_both"></div>
								</div>
							</div>
						</div>
						<?
						break;
						case 'image':
						?>
						<div class="<?=$ms['type_kind']?>_filter_block filter_block">
							<div class="block">
								<div class="group_name">
									<span>
										<?=$ms['group_name']?>
									</span>
								</div>
								<div class="properties_block">
								<?
								foreach($ms['options_active'] as $chkey => $chms) {
								$checked = true;
								if(isset($options_active[$chkey])) $checked = true;
								$checkbox_data = array(
															'type' => 'checkbox',
															'id' => 'filter_checkbox_'.$chkey.'_active_additional',
															'name' => 'products_active_filters['.$type_id.']['.$chkey.']',
															'value' => 'products_filters['.$key.']['.$chkey.']',
															'checked' => $checked
									);
								?>
								<div class="property">
									<div class="checkbox">
										<label for="filter_checkbox_<?=$chkey?>_active_additional">
											<div class="img_block">
												<img src=<?=$chms['image']?> />
											</div>
										</label>
										<span class="checkbox_name"><?=$chms['pname']?></span><i></i>
										<?=form_checkbox($checkbox_data)?>
									</div>
									<div class="clear_both"></div>
								</div>
								<?
								}
								?>
								<div class="clear_both"></div>
								</div>
							</div>
						</div>
						<?
						break;
						case 'price':
						
							$checked = true;
							if(isset($options_active)) $checked = true;
							$checkbox_data = array(
														'type' => 'checkbox',
														'id' => 'active_range_price_filter_additional',
														'name' => 'products_filters_price[active]',
														'value' => 'products_filters_price[active]',
														'checked' => $checked
							);
					
						?>
						<div class="<?=$ms['type_kind']?>_filter_block filter_block">
							<div class="block">
								<div class="group_name">
									<span>
										Диапазон цен
									</span>
								</div>
								<div class="properties_block">
									<div class="property">
										<div class="checkbox">
											<label for="active_range_price_filter_additional">
											</label>
									
											<?=form_checkbox($checkbox_data)?>
										</div>
											<span class="checkbox_name">
											<?
											foreach($ms['options_active'] as $chkey => $chms)
											{
											?>
												<?=$chms['pname']?> <b><?=$chms['filter_price']?></b>
											<?
											}
											?>
											</span><i></i>
										</div>
										<div class="clear_both"></div>
									</div>
								<div class="clear_both"></div>
							</div>
						</div>
						<?
						break;
					}
					?>
				<?
				}
				?>
		<div class="filters_buttons">
			<a href="#" class="activate_filter" id="filter_activate_additional_block"><span><?=$this->lang->line('products_filter_activate')?></span></a>
			<a href = "#" class="clear_filter" id="filter_clear_additional_block"><span><?=$this->lang->line('products_filter_clear')?></span></a>
		</div>
	</div>
</div>
<?
}
?>


	
