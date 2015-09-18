<?php
class Mfavorites extends AG_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('favorites');
	}
	
	public function add_item($id)
	{
		$this->load->model('catalogue/mproducts');
		if($this->mproducts->check_isset_product($id, array('`status`' => 1, '`in_stock`' => 1)))
		{
			$data['id'] = $id;
			$data['qty'] = 1;
			$data['price'] = 1;
			$data['name'] = 1;
			if($this->favorites->isset_favorites_item($id))
			{
				return array('success' => FALSE, 'message' => $this->lang->line('favorites_error_add_item_item_already_exist'));
			}
			else
			{
				$this->favorites->insert($data);
				return array('success' => TRUE, 'message' => $this->lang->line('favorites_success_add_item'));
			}
		}
		return array('success' => FALSE, 'message' => $this->lang->line('favorites_error_add_item'));
	}
	
	public function delete_item($rowid)
	{
		$data = array(
			'rowid' => $rowid,
			'qty'	=> 0
		);
		$this->favorites->update($data);
		return $this->get_favorites_products();
	}
	
	public function get_favorites_short()
	{
		$data['total_items'] = $this->favorites->total_items();
		return $data;
	}
	
	public function get_favorites_products()
	{
		$data = array();
		$favorites = $this->favorites->contents();
		if(count($favorites)>0)
		{	
			$this->load->model('catalogue/mproducts');
			$data = $this->mproducts->get_favorites_products($favorites);
			return array('favorites_products' => $data);
		}
		return FALSE;
	}
}