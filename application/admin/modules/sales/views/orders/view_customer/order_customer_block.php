<div id="order_customer_block">
	<div align="right">
		<a href="<?=set_url('*/*/ajax_get_customers_grid/ord_id/'.$ord_id)?>" id="select_customer">Выбрать покупателя</a><br><br>
	</div>
	<div id="order_customer">
		<?=$this->load->view('sales/orders/view_customer/order_customer', array(), TRUE)?>
	</div>
</div>