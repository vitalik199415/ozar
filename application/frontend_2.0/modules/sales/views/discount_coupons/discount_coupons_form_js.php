<script>
$("#coupon_info").gbc_discount_coupons(
{
	url:				'<?=$this->router->build_url('ajax', array('ajax' => 'sales/discount_coupons/view_coupon/'));?>',
	err_mess:			'<?=$this->lang->line('d_c_error')?>'
});

</script>