<?php
class Products extends AG_Controller
{
	protected $settings = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/products');
	}
	
	public function get_category_products()
	{
		modules::run('catalogue/catalogue_sort/index');
		modules::run('catalogue/types/index');

		if($category_id = $this->variables->get_vars('category_id'))
		{
			$this->build_category_products_temptate_js();
			$this->load->model('catalogue/mproducts');
			$products_array = $this->mproducts->get_category_products_collection($category_id);
			$this->build_category_products_template_blocks($products_array);
		}
	}
	
	public function get_category_products_sort_filtered()
	{
		modules::run('catalogue/catalogue_sort/index');
		modules::run('catalogue/types/index');

		if($category_id = $this->variables->get_vars('category_id') )
		{
			$this->load->model('catalogue/mcatalogue_sort');
			$this->mcatalogue_sort->_init();

			$this->load->model('catalogue/mtypes');
			$this->mtypes->_init();

			$this->build_category_products_temptate_js();
			$this->load->model('catalogue/mproducts');
			$products_array = $this->mproducts->get_category_products_collection_sort_filtered($category_id);
			$this->build_category_products_template_blocks($products_array);
		}
	}
	
	public function build_category_products_temptate_js()
	{
		$this->template->add_js('jquery.gbc_products_short', 'modules_js/catalogue/products');
	}
	
	public function build_category_products_template_blocks($products_array)
	{

		$this->template->add_view_to_template('center_block', 'catalogue/products/products_short_block', array('PRS_array' => $products_array, 'PRS_block_id' => 'PRS_block'));
		
		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short', array());
		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short_js', array());
		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short_js_additional', array());
	}

	public function build_search_products_template_blocks($products_array)
	{
		$this->template->add_view_to_template('center_block', 'catalogue/products/products_search_block', array('PRS_array' => $products_array, 'PRS_block_id' => 'PRS_block'));

		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short', array());
		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short_js', array());
		$this->template->add_view_to_template('PRS', 'catalogue/products/products_short_js_additional', array());
	}
	
	public function get_product()
	{
		if($url = $this->variables->get_url_vars('product_url'))
		{
			$this->build_product_temptate_js();
			$this->load->model('catalogue/mproducts');
			if($product_array = $this->mproducts->get_product($url))
			{
				if(isset($product_array['multi_product']))
				{
					$this->get_multi_product($url);
				}
				else
				{
					$this->build_product_template_blocks($product_array);
				}
			}
		}
	}
	
	protected function build_product_temptate_js()
	{
		$this->template->add_js('jquery.gbc_products_detail', 'modules_js/catalogue/products');
		$this->template->add_js('jquery.gbc_products_albums', 'modules_js/catalogue/products');
		$this->template->add_js('jquery.gbc_products_detail_comments', 'modules_js/catalogue/products');
	}
	
	protected function build_product_template_blocks($product_array)
	{
		$PRID = $product_array['product']['ID'];
					
		$this->template->add_view_to_template('center_block', 'catalogue/products/products_detail_block', array('PRD_ID' => $PRID, 'PRD_array' => $product_array, 'PRD_block_id' => 'PRD_block'.$PRID));
		
		$this->template->add_view_to_template('PRD_'.$PRID, 'catalogue/products/products_detail', array());
		$this->template->add_view_to_template('PRD_'.$PRID, 'catalogue/products/products_detail_js', array());
		$this->template->add_view_to_template('PRD_'.$PRID, 'catalogue/products/products_detail_js_additional', array());
		
		$this->template->add_view_to_template('PRD_images_'.$PRID, 'catalogue/products/images_detail', array());
		$this->template->add_view_to_template('PRD_albums_'.$PRID, 'catalogue/products/albums_detail', array());
		
		$this->template->add_view_to_template('PRD_tab_images_'.$PRID, 'catalogue/products/tab_images_detail', array());
		$this->template->add_view_to_template('PRD_tab_images_'.$PRID, 'catalogue/products/tab_images_detail_js', array());
		
		$this->template->add_view_to_template('PRD_'.$PRID, 'catalogue/products/albums_detail_js', array());
		$this->template->add_view_to_template('PRD_'.$PRID, 'catalogue/products/albums_detail_js_additional', array());
		
		$this->template->add_view_to_template('PRD_prices_'.$PRID, 'catalogue/products/prices_detail', array());
		
		$this->template->add_view_to_template('PRD_attributes_'.$PRID, 'catalogue/products/attributes_detail', array());
		$this->template->add_view_to_template('PRD_description_'.$PRID, 'catalogue/products/description_detail', array());
		$this->template->add_view_to_template('PRD_description_short_'.$PRID, 'catalogue/products/description_short', array());
		
		$this->template->add_view_to_template('PRD_tabs_'.$PRID, 'catalogue/products/tabs_block', array());
		$this->template->add_view_to_template('PRD_tabs_'.$PRID, 'catalogue/products/tabs_block_js', array());
		
		$this->template->add_view_to_template('PRD_comments_block_'.$PRID, 'catalogue/products/comments_detail_block', array());
		$this->template->add_view_to_template('PRD_comments_'.$PRID, 'catalogue/products/comments_detail', array());
		$this->template->add_view_to_template('PRD_comments_form_'.$PRID, 'catalogue/products/comments_detail_form', array());
		$this->template->add_view_to_template('PRD_comments_block_'.$PRID, 'catalogue/products/comments_detail_js', array());
		$this->template->add_view_to_template('PRD_comments_block_'.$PRID, 'catalogue/products/comments_detail_js_additional', array());
		
		$this->template->add_view_to_template('PRD_related_'.$PRID, 'catalogue/products/related_detail', array());
		$this->template->add_view_to_template('PRD_related_'.$PRID, 'catalogue/products/related_detail_js', array());
		$this->template->add_view_to_template('PRD_similar_'.$PRID, 'catalogue/products/similar_detail', array());
		$this->template->add_view_to_template('PRD_similar_'.$PRID, 'catalogue/products/similar_detail_js', array());
		

	}
	
	public function get_multi_product($url)
	{
	
	}
	
	public function get_search_products()
	{
		$search_string = $this->variables->get_url_vars('search_string');
		$search_string = rawurldecode($search_string);
		$this->load->model('catalogue/mproducts');
		if($products_array = $this->mproducts->get_search_products_collection($search_string))
		{
			$this->build_category_products_temptate_js();
			$this->build_category_products_template_blocks($products_array);
		}	
	}
	
	public function get_bestseller_products($count = 10, $random = FALSE)
	{
		$this->load->model('catalogue/mproducts');
		$products_array = $this->mproducts->get_bestseller_products($count, $random);
		$this->build_bestseller_products_template_blocks($products_array);
	}
	
	protected function build_bestseller_products_template_blocks($products_array)
	{
		$this->template->add_view_to_template('bestseller_products_block', 'catalogue/products/bestseller_products_block', array('PRS_bestseller' => $products_array, 'PRS_bestseller_block_id' => 'PRS_bestseller_block'));
		$this->template->add_view_to_template('bestseller_products',  'catalogue/products/bestseller_products', array());
		$this->template->add_view_to_template('bestseller_products',  'catalogue/products/bestseller_products_js', array());
	}
	
	public function get_sale_products($count = 10, $random = FALSE)
	{
		$this->load->model('catalogue/mproducts');
		$products_array = $this->mproducts->get_sale_products($count, $random);
		$this->build_sale_products_template_blocks($products_array);
	}
	
	protected function build_sale_products_template_blocks($products_array)
	{
		$this->template->add_view_to_template('sale_products_block', 'catalogue/products/sale_products_block', array('PRS_sale' => $products_array, 'PRS_sale_block_id' => 'PRS_sale_block'));
		$this->template->add_view_to_template('sale_products',  'catalogue/products/sale_products', array());
		$this->template->add_view_to_template('sale_products',  'catalogue/products/sale_products_js', array());
	}
	
	public function get_new_products($count = 10, $random = FALSE)
	{
		$this->load->model('catalogue/mproducts');
		$products_array = $this->mproducts->get_new_products($count, $random);
		$this->build_new_products_template_blocks($products_array);
	}
	
	protected function build_new_products_template_blocks($products_array)
	{
		$this->template->add_view_to_template('new_products_block', 'catalogue/products/new_products_block', array('PRS_new' => $products_array, 'PRS_new_block_id' => 'PRS_new_block'));
		$this->template->add_view_to_template('new_products',  'catalogue/products/new_products', array());
		$this->template->add_view_to_template('new_products',  'catalogue/products/new_products_js', array());
	}
	
	public function ajax_get_products_comments()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id'])) > 0)
		{
			$this->load->model('catalogue/mproducts');
			$comments_data['comments_array'] = $this->mproducts->get_product_detail_comments($pr_id);
			$html = $this->load->view('catalogue/products/comments_detail', array('PRD_array' => $comments_data), TRUE);
			echo $html;
		}
	}
	
	public function save_product_comment()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id'])) > 0)
		{
			$captcha = trim($this->input->post('captcha'));
			if(isset($_SESSION['captcha_product_comment_keystring_'.$pr_id]) && $_SESSION['captcha_product_comment_keystring_'.$pr_id] == $captcha)
			{
				$this->load->model('catalogue/mproducts');
				$data = $this->mproducts->add_product_detail_comment($pr_id);
				$settings = $this->mproducts->get_settings();
				$data['comments_html'] = '';
				$data['reload'] = 0;
				if($settings['reviews_publication_immediately'] == 1)
				{
					$comments_data['comments_array'] = $this->mproducts->get_product_detail_comments($pr_id);
					$data['comments_html'] = $this->load->view('catalogue/products/comments_detail', array('PRD_array' => $comments_data), TRUE);
					$data['reload'] = 1;
				}
				
				if(isset($_SESSION['captcha_product_comment_keystring_'.$pr_id])) unset($_SESSION['captcha_product_comment_keystring_'.$pr_id]);
				echo json_encode($data + array('img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_product_comment_keystring_'.$pr_id.'&rand='.rand(1000, 9999).'" id="captcha_img">'));
			}
			else
			{
				if(isset($_SESSION['captcha_product_comment_keystring_'.$pr_id])) unset($_SESSION['captcha_product_comment_keystring_'.$pr_id]);
				echo json_encode(array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$this->lang->line('products_comments_error_captcha').'</p>'), TRUE), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_product_comment_keystring_'.$pr_id.'&rand='.rand(1000, 9999).'" id="captcha_img">'));
			}
		}
		else
		{
			echo json_encode(array('success' => 0, 'messages' => 'Server error! Try later.'));
		}
	}
}
?>