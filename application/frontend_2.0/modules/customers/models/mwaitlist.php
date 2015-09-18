<?php
class Mwaitlist extends AG_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function add_item($id)
	{
		if($this->input->post('email')) {
			$email = $this->input->post('email');

			$this->load->model('catalogue/mproducts');
			if ($this->mproducts->check_isset_product($id, array('`status`' => 1, '`in_stock`' => 0))) {
				if ($this->isset_waitlist_item($id, $email)) {
					return array('success' => FALSE, 'message' => $this->lang->line('waitlist_error_add_item_item_already_exist'));
				} else {
					if ($this->insert_item($id, $email)) {
						return array('success' => TRUE, 'message' => $this->lang->line('waitlist_success_add_item') . $email);
					}
				}
			}
		} else {
			if ($cust_id = $this->session->userdata('customer_id')) {
				$this->load->model('catalogue/mproducts');

				if ($this->mproducts->check_isset_product($id, array('`status`' => 1, '`in_stock`' => 0))) {

					$email = $this->db->select('`email`')
						->from('`m_u_customers`')
						->where('`id_m_u_customers`', $cust_id)
						->limit(1)->get()->row_array();

					if ($this->isset_waitlist_item($id, $email['email'])) {
						return array('success' => FALSE, 'message' => $this->lang->line('waitlist_error_add_item_item_already_exist'));
					} else {
						if($this->insert_item($id, $email['email'])) {
							return array('success' => TRUE, 'message' => $this->lang->line('waitlist_success_add_item'));
						}
					}
				}
			}
		}
			return array('success' => FALSE, 'message' => $this->lang->line('waitlist_error_add_item'));
	}

	public function insert_item($id, $email) {
		$data = array();
		$data['email'] = $email;
		$data['id_m_c_products'] = $id;

		$ID = $this->db->set($data)->insert('`m_u_customers_waitlist`');
		if($ID) return TRUE;

		return FALSE;
	}

	public function isset_waitlist_item($id, $email) {

		$count = $this->db->select()
			->from('`m_u_customers_waitlist`')
			->where('`email`', $email)
			->where('`id_m_c_products`', $id)
			->limit(1)->get()->row_array();

		if(count($count) > 0) return TRUE;

		return FALSE;
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