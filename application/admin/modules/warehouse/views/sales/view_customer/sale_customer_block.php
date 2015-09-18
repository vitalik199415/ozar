<div id="order_customer_block">
	<div align="right">
		<a href="<?=set_url('*/*/ajax_get_customers_grid/wh_id/'.$wh_id.'/sale_id/'.$sale_id)?>" id="select_customer">Выбрать покупателя</a><br><br>
	</div>
	<div id="order_customer">
		<?=$this->load->view('warehouse/sales/view_customer/sale_customer', array(), TRUE)?>
	</div>
</div>