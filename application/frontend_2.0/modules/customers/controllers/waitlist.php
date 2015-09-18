<?php
class Waitlist extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/products');
	}
	
	public function ajax_add_item()
	{
		$id = '';
		if(isset($_POST['product_id'])) {
			$id = $_POST['product_id'];
		} else {
			$URI = $this->uri->ruri_to_assoc(3);
			$id = $URI['id'];
		}

		if($id != '' && intval($id))
		{
			if(!isset($_POST['email']) && !$this->session->userdata('customer_id')) {
				$html = $this->load->view('customers/waitlist', array('product_id' => $id), TRUE);
				echo json_encode(array('success' => 1, 'wait_form_html' => $html));
			} else
				if(isset($_POST['email']) || $this->session->userdata('customer_id')) {
				$this->load->model('customers/mwaitlist');
				$this->load->model('langs/mlangs');

				$data = $this->mwaitlist->add_item($id);
				if ($data['success'] == TRUE) {
					$site_messages = $this->load->view('site_messages/site_messages', array('success_message' => '<p>' . $data['message'] . '</p>'), TRUE);
					echo json_encode(array('success' => 2, 'site_messages' => $data['message']));
				} else {
					$site_messages = $this->load->view('site_messages/site_messages', array('error_message' => '<p>' . $data['message'] . '</p>'), TRUE);
					echo json_encode(array('success' => 0, 'site_messages' => $data['message']));

				}
			}
		}
		else
		{
			$site_messages = $this->load->view('site_messages/site_messages', array('error_message' => '<p>Server error!</p>'), TRUE);
			echo json_encode(array('success' => 0, 'site_messages' => '<p>Server error!</p>'));
		}
	}
	
	public function ajax_show_favorites_products()
	{
		$this->mlangs->load_language_file('modules/products');
		$this->load->model('sales/mfavorites');
		$data = $this->mfavorites->get_favorites_products();
		if($output = $this->load->view('sales/favorites/favorites_products_block', $data , TRUE))
		{
			echo json_encode(array('success' => 1, 'favorites_products_html' => $output));
		}
		else
		{
			echo json_encode(array('success' => 0));
		}
	}
	
	public function ajax_delete_favorites_product()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['rowid']))
		{
			$this->mlangs->load_language_file('modules/products');
			$this->load->model('sales/mfavorites');
			$data = $this->mfavorites->delete_item($URI['rowid']);
			$sdata = $this->mfavorites->get_favorites_short();
			$out['favorites_products_html'] = $this->load->view('sales/favorites/favorites_products_block', $data , TRUE);
			$out['favorites_html'] = $this->load->view('sales/favorites/favorites_block', $sdata, TRUE);
			echo json_encode(array('success' => 1, 'site_messages' => '<p>'.$this->lang->line('cart_success_delete_item').'</p>') + $out);
		}
		else
		{
			echo json_encode(array('success' => 0, 'site_messages' => '<p>Server Error. Try later!</p>'));
		}
	}
}