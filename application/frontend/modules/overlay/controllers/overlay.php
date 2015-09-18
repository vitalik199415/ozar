<?php
class Overlay extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function build_overlay_block()
	{
		$this->template->add_view_to_template('overlay_block', 'overlay/customers_overlay', array());	
		$this->template->add_view_to_template('overlay_block', 'overlay/favorites_overlay', array());	
		$this->template->add_view_to_template('overlay_block', 'overlay/cart_overlay', array());	
		$this->template->add_view_to_template('overlay_block', 'overlay/order_overlay', array());	
		$this->template->add_view_to_template('overlay_block', 'overlay/product_overlay', array());	
		$this->template->add_view_to_template('overlay_block', 'overlay/site_messages_overlay', array());	
	}
}
?>