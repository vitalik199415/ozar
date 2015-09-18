<?php
class Favorites extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/favorites');
		$this->mlangs->load_language_file('modules/cart');
		$this->template->add_js('jquery.gbc_favorites', 'modules_js/sales');
	}
	
	public function index()
	{
		$this->load->model('sales/mfavorites');
		$data = $this->mfavorites->get_favorites_short();
		$this->template->add_view_to_template('favorites_block', 'sales/favorites/favorites_outside_block', array('favorites_block_id' => 'favorites_block')+$data);
		$this->template->add_view_to_template('favorites_inside_block', 'sales/favorites/favorites_block', array());
		$this->template->add_view_to_template('favorites_block', 'sales/favorites/favorites_block_js', array());
	}
	
	public function ajax_add_item()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0)
		{
			$this->load->model('sales/mfavorites');
			$this->load->model('langs/mlangs');
			$data = $this->mfavorites->add_item($id);
			if($data['success'] == TRUE)
			{
				$favorites_data = $this->mfavorites->get_favorites_short();
				$favorites_html = $this->load->view('sales/favorites/favorites_block', $favorites_data, TRUE);
				$site_messages = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
				echo json_encode(array('success' => 1, 'favorites_html' => $favorites_html, 'site_messages' => $site_messages));
			}
			else
			{
				$site_messages = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
				echo json_encode(array('success' => 0, 'site_messages' => $site_messages));
			}
		}
		else
		{
			$site_messages = $this->load->view('site_messages/site_messages', array('error_message' => '<p>Server error!</p>'), TRUE);
			echo json_encode(array('success' => 0, 'site_messages' => $site_messages));
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