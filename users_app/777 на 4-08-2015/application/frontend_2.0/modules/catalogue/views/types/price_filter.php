<div id="<?=$filter_item_id?>" class="price_slider_block filter_block">

	<div class="block">

		<div class="group_name">

			<span>

				<?=$group_name['tname']?>

				<span><?=$group_name['currency_name']?></span>

				<i class="pr_qty">(<?=$group_name['pr_qty']?>)</i>

			</span>

		</div>

		<div class="slider_block">

			<div id="filters_price_slider"></div>

			<div class="inputs_block clearfix">

			<?

			foreach($options_array as $chkey => $chms)

			{

				$input_data = array(

					'type' => 'text',

					'id' => 'products_filters_price_'.$chkey,

					'name' => 'products_filters_price['.$chkey.']',

					'value' => $chms['filter_price'],

					'placeholder' => $chms['interval_price']

				);

				

				$input_hidden_data = array(

					'type' => 'hidden',

					'id' => 'products_filters_price_hidden_'.$chkey,

					'name' => 'products_filters_hidden['.$chkey.']',

					'value' => $chms['interval_price']

				);

				

				?>

				<div>

					<span><?=$chms['pname']?></span>

					<?

					echo form_input($input_data);

					echo form_input($input_hidden_data);

					?>

				</div>



				<?

			}

			?>
				<div class="clear_both"></div>
			</div>

		</div>

	</div>

</div>	









	

