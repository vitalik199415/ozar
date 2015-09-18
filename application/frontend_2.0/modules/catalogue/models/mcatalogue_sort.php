<?php
class Mcatalogue_sort extends AG_Model
{
	const SPHINX_INDEX = SPHINX_INDEX;

	public $sort_options = FALSE;

	private $_was_init = FALSE;

	public function __construct()
	{
		parent::__construct();
	}

	public function _init()
	{
		if($this->_was_init) return FALSE;
		$category_id = $this->variables->get_vars('category_id');
		$category_url = $this->variables->get_vars('category_url');
		if(!$category_url) show_404();

		if($this->input->post('products_sort_clear'))
		{
			$this->clear_sort();
			redirect($this->router->build_url('category_filters_lang', array('category_url' => $this->variables->get_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}

		if(!$this->category_sort())
		{
			$product_sort_options = $this->session->userdata('product_sort_data');
			$product_sort_options = isset($product_sort_options[$category_id]) ? $product_sort_options[$category_id] : FALSE;
			$this->sort_options = $product_sort_options;
		}

		if(!$this->variables->get_url_vars('sort_params') && $this->sort_options)
		{
			$sort_url_string = $this->build_sort_url();
			redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $this->variables->get_vars('category_url'), 'sort_params' => $sort_url_string, 'filters_params' => $this->variables->get_url_vars('filters_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}
		return TRUE;
	}

	public function clear_sort()
	{
		$category_id = $this->variables->get_vars('category_id');

		$data = $this->session->userdata('product_sort_data');
		if(isset($data[$category_id])) unset($data[$category_id]);
		$this->session->set_userdata('product_sort_data', $data);
		$this->sort_options = FALSE;
	}

	public function get_limit_array()
	{
		$this->load->model('catalogue/mproducts_settings');
		$s = $this->mproducts_settings->get_settings();
		$s_limit = $s['products_count_to_page'];
		return array(
			'products_limit_array' => array(
				$this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $s_limit)), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)) 		=> $s_limit,
				$this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $s_limit * 2)), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)) 	=> $s_limit * 2,
				$this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $s_limit * 4)), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)) 	=> $s_limit * 4,
				$this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $s_limit * 8)), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)) 	=> $s_limit * 8,
				$this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $s_limit * 16)), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)) => $s_limit * 16),
			'products_limit_active' => $this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $this->variables->get_additional_url_vars('limit'))), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code))
		);
	}

	public function get_active_sort()
	{
		$active_sort = array();
		if(!is_array($this->sort_options)) return $active_sort;
		foreach($this->sort_options as $key => $ms)
		{
			if($key == 'options')
			{
				$active_sort[] = array('products_sort[options]', $ms[0]);
			}
			else
			{
				$active_sort[] = array('products_sort['.$key.']', $ms[1]);
			}
		}
		return $active_sort;
	}

	public function submit_sort($category_url)
	{
		$this->clear_sort();
		$this->load->model('catalogue/mcategories');
		$category = $this->mcategories->get_category_by_url($category_url);
		$category_id = $category['ID'];
		$this->variables->set_vars('category_id', $category_id);

		if($sort = $this->input->post('products_sort'))
		{
			$sort_options = array();
			foreach($sort as $key => $ms)
			{
				if($key == 'options')
				{
					if($ms == 'new' || $ms == 'bestseller' || $ms == 'sale') $sort_options[$key] = array($ms, 'DESC');
				}
				if($key == 'price')
				{
					$sort_options[$key] = ($ms == 'DESC') ? array($key, 'DESC') : array($key, 'ASC');
				}
				if($key == 'create_date')
				{
					$sort_options[$key] = ($ms == 'DESC') ? array($key, 'DESC') : array($key, 'ASC');
				}
			}
			$data = $this->session->userdata('product_sort_data');
			$data[$category_id] = $sort_options;
			$this->session->set_userdata('product_sort_data', $data);
			$this->sort_options = $sort_options;
		}

		$sort_url_string = $this->build_sort_url();

		if($sort_url_string != '')
		{
			redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $category_url, 'sort_params' => $sort_url_string, 'filters_params' => $this->variables->get_url_vars('filters_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}
		else
		{
			redirect($this->router->build_url('category_filters_lang', array('category_url' => $category_url, 'filters_params' => $this->variables->get_url_vars('filters_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}
	}

	protected function build_sort_url()
	{
		$sort_implode_array = array();
		if($this->sort_options && count($this->sort_options) > 0)
		{
			foreach($this->sort_options as $ms)
			{
				$sort_implode_array[] = implode('=', $ms);
			}
		}
		return implode('&', $sort_implode_array);
	}

	public function pars_sort_url($url)
	{
		$return_sort = array();
		$sort_pars_array = explode('&', $url);

		foreach($sort_pars_array as $ms)
		{
			list($type, $prop) = explode('=', $ms);

			if($type != 'new' && $type != 'bestseller' && $type != 'sale' && $type != 'price' && $type != 'create_date')
			{
				show_404();
			}
			if($prop != 'DESC' && $prop != 'ASC')
			{
				show_404();
			}

			if($type == 'new' || $type == 'bestseller' || $type == 'sale')
			{
				$return_sort['options'] = array($type, $prop);
			}
			else
			{
				$return_sort[$type] = array($type, $prop);
			}
		}
		return $return_sort;
	}

	public function category_sort()
	{
		if(!$this->variables->get_url_vars('sort_params')) return FALSE;
		$sort_params = rawurldecode($this->variables->get_url_vars('sort_params'));

		if(count($sort_options = $this->pars_sort_url($sort_params)) == 0) return FALSE;

		$data = $this->session->userdata('product_sort_data');
		$data[$this->variables->get_vars('category_id')] = $sort_options;
		$this->session->set_userdata('product_sort_data', $data);
		$this->sort_options = $sort_options;
		return TRUE;
	}

	public function get_sphinx_products_order_by()
	{
		$order_by = '';
		$order_by_array = array();
		if(!$this->sort_options) return $order_by;
		foreach($this->sort_options as $ms)
		{
			$order_by_array[] = implode(' ', $ms);
		}
		$order_by = implode(',', $order_by_array);
		return ' ORDER BY '.$order_by;
	}
}
?>