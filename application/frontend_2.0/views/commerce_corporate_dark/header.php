	<div class="header">
		<div class="gbc_top">
			<div><span>Сайт на платформе <span class="service">GBC</span><span>Тип услуги : <span class="service">VIP-Commerc</span></span><span>Интернет-магазин за 2000 грн. только у нас - <a href="http://gbc.net.ua">GBC.ua</a></span></span></div>
		</div>
		<div class="language_select_curency_block">
			<?=$this->template->get_temlate_view('langs_block');?>
			<?=$this->template->get_temlate_view('select_currency_block');?>
		</div>
		<?=$this->template->get_temlate_view('customers_block');?>
		<?=$this->template->get_temlate_view('favorites_block');?>
		<?=$this->template->get_temlate_view('cart_block');?>
		<?=$this->template->get_temlate_view('search_block');?>
		<?=$this->template->get_temlate_view('site_description_block');?>
	</div>
	<?=$this->template->get_temlate_view('menu_block');?>