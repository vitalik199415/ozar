<div class="menu">
<ul id="gbc_dropdown_menu">
	<li>
	<a href="#" style="width:140px;"><p>Меню | Модули</p></a>
			<ul>
				<li><a href="<?=set_url('home')?>"><p>Настройки главной</p></a></li>
				<li><a href="<?=set_url('menu')?>"><p>Меню сайта</p></a></li>
				<li><a href="<?=set_url('site_modules')?>"><p>Модули сайт</p></a></li>
			</ul>
	</li>
	<li>
	<a href="#" style="width:180px;"><p>Каталог продукции</p></a>
			<ul>
				<li>
					<a href="#" class="str"><p>Категории каталога</p></a>
					<ul>
						<li>
							<a href="<?=set_url('catalogue/categories')?>"><p>Категории каталога</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/categories_products')?>"><p>Продукты в категории</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/catalogue_mass_edit_price')?>"><p>Изменение цен</p></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#" class="str"><p>Продукты каталога</p></a>
					<ul>
						<li>
							<a href="<?=set_url('catalogue/products')?>"><p>Продукты каталога</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/products/additionally_grid')?>"><p>Продукты дополнительно</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/products_related')?>"><p>Сопутствующие продукты</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/products_similar')?>"><p>Похожие продукты</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/products_comments')?>"><p>Отзывы к продуктам</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/products_settings')?>"><p>Настройки продуктов</p></a>
						</li>
						<li>
							<a href="<?=set_url('catalogue/currency')?>"><p>Настройки валют</p></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#" class="str"><p>Фильтры</p></a>
						<ul>
							<li>
								<a href="<?=set_url('catalogue/products_types')?>"><p>Группы фильтров</p></a>
							</li>
							<li>
								<a href="<?=set_url('catalogue/products_types_set')?>"><p>SEO наборы фильтров</p></a>
							</li>
						</ul>
				</li>
				<li>
					<a href="#" class="str"><p>Атрибуты продукции</p></a>
						<ul>
							<li>
								<a href="<?=set_url('catalogue/products_attributes')?>"><p>Атрибуты</p></a>
							</li>
							<li>
								<a href="<?=set_url('catalogue/products_attributes_options')?>"><p>Опции артибутов</p></a>
							</li>
						</ul>
				</li>
			</ul>
	</li>
	<li>
	<a href="#" style="width:140px;"><p>Продажи</p></a>
			<ul>
				<li><a href="<?=set_url('sales/orders')?>"><p>Заказы</p></a></li>
				<li><a href="<?=set_url('sales/invoices')?>"><p>Инвойсы</p></a></li>
				<li><a href="<?=set_url('sales/shippings')?>"><p>Отправки</p></a></li>
				<li><a href="<?=set_url('sales/credit_memo')?>"><p>Возвраты</p></a></li>
				<li>
					<a href="<?=set_url('sales/payment_methods')?>"><p>Методы оплаты</p></a>
				</li>
				<li>
					<a href="<?=set_url('sales/shipping_methods')?>"><p>Методы доставки</p></a>
				</li>
				<li>
					<a href="<?=set_url('catalogue/discounts')?>"><p>Настройки скидок</p></a>
				</li>
				<li>
					<a href="<?=set_url('sales/sales_settings')?>"><p>Настройки</p></a>
				</li>
			</ul>
	</li>
	<li>
	<a href="#" style="width:140px;"><p>Покупатели</p></a>
			<ul>
				<li><a href="<?=set_url('customers')?>"><p>Покупатели</p></a></li>
				<li><a href="<?=set_url('customers/customers_types')?>"><p>Группы покупателей</p></a></li>
				<li><a href="<?=set_url('customers/customers_settings')?>"><p>Настройки</p></a></li>
			</ul>
	</li>
	<li>
	<a href="#" style="width:180px;"><p>Настройки сайта</p></a>
			<ul>
				<li><a href="<?=set_url('site_settings')?>"><p>Настройки сайта</p></a></li>
				<li><a href="<?=set_url('langs')?>"><p>Настройки языков</p></a></li>
				<li><a href="#" class="str"><p>Дополнительные блоки</p></a>
					<ul>
						<li><a href="<?=set_url('block_additionally/block_additionally_header')?>"><p>Дополнительное header</p></a>
						<li><a href="<?=set_url('block_additionally/block_additionally_footer')?>"><p>Счетчики footer</p></a>
					</ul>
				</li>
			</ul>
	</li>
	<?php
	if(isset($_SESSION['id_users']) /*&& $_SESSION['id_users'] == 10488*/)
	{
	?>
	<li>
	<a href="#" style="width:120px;"><p>Склад</p></a>
			<ul>
				<li><a href="<?=set_url('warehouse/warehouses')?>"><p>Склады</p></a></li>

				<li><a href="<?=set_url('warehouse/warehouses_products')?>"><p>Продукты</p></a></li>

				<li>
					<a href="#" class="str"><p>Продажи</p></a>
					<ul>
						<li><a href="<?=set_url('warehouse/warehouses_sales')?>"><p>Продажи</p></a></li>
						<li><a href="<?=set_url('warehouse/warehouses_invoices')?>"><p>Инвойсы</p></a></li>
						<li><a href="<?=set_url('warehouse/warehouses_shippings')?>"><p>Отправки</p></a></li>
						<li><a href="<?=set_url('warehouse/warehouses_credit_memo')?>"><p>Возвраты</p></a></li>
					</ul>
				</li>

				<li><a href="<?=set_url('warehouse/warehouses_transfers')?>"><p>Переносы</p></a></li>
				<li><a href="<?=set_url('warehouse/warehouses_logs')?>"><p>Логи, отчеты</p></a></li>
				<li><a href="<?=set_url('warehouse/wh_settings')?>"><p>Настройки склада</p></a></li>
			</ul>
	</li>
	<?php
	}
	?>
	<li>
		<a href="<?=set_url('login/logout')?>" style="width:90px;"><p>Выход</p></a>
	</li>
</ul>
</div>
<script>
$('#gbc_dropdown_menu').gbc_dropdown_menu();
</script>