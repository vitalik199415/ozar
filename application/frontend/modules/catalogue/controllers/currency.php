<?php
class Currency extends AG_Controller
{
	protected $settings = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/currency');
	}
	
	public function index()
	{
		$this->template->add_js('jquery.gbc_select_currency', 'modules_js/catalogue/currency');
		$this->load->model('catalogue/mcurrency');
		$select_currency_array = $this->mcurrency->get_currency_to_select();
		if(count($select_currency_array['currency_array']) > 0)
		{
			$this->template->add_view_to_template('select_currency_block', 'catalogue/currency/select_currency', array('select_currency_array' => $select_currency_array));
			$this->template->add_view_to_template('select_currency_block', 'catalogue/currency/select_currency_js', array());
		}
	}
	
	public function ajax_change_currency()
	{
		if($currency_id = $this->input->post('currency'))
		{
			$this->load->model('catalogue/mcurrency');
			if($this->mcurrency->change_currency($currency_id))
			{
				echo json_encode(array('success' => 1));
			}
			else
			{
				echo json_encode(array('success' => 0));
			}
		}
	}
}
?>