<?php
class Mproducts extends AG_Model
{
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';

	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';

	const PR_IMG 			= 'm_c_products_images';
	const ID_PR_IMG 		= 'id_m_c_products_images';
	const PR_IMG_DESC 		= 'm_c_products_images_description';

	const PR_ALB 			= 'm_c_products_albums';
	const ID_PR_ALB 		= 'id_m_c_products_albums';
	const PR_ALB_DESC 		= 'm_c_products_albums_description';

	const PR_SETTINGS = 'm_c_products_settings';

	const CAT 		= 'm_c_categories';
	const ID_CAT 	= 'id_m_c_categories';
	const CAT_DESC 		= 'm_c_categories_description';

	const PR_CAT 		= 'm_c_productsNcategories';
	const ID_PR_CAT 	= 'id_m_c_productsNcategories';
	const CAT_LINK 		= 'm_c_categories_link';

	const CUR 		= 'm_c_currency';
	const ID_CUR 	= 'id_m_c_currency';
	const UCUR 		= 'm_c_users_currency';

	const ATTR 			= 'm_c_products_attributes';
	const ID_ATTR 		= 'id_m_c_products_attributes';
	const ATTR_DESC 	= 'm_c_products_attributes_description';
	const OP 			= 'm_c_products_attributes_options';
	const ID_OP 		= 'id_m_c_products_attributes_options';
	const OP_DESC 		= 'm_c_products_attributes_options_description';
	const PNA = 'm_c_productsNattributes';
	const PNT = 'm_c_productsNtypes';

	const PR_REL		= 'm_c_products_related';
	const ID_PR_REL		= 'id_m_c_products_related';

	const PR_SIM		= 'm_c_products_similar';
	const ID_PR_SIM		= 'id_m_c_products_similar';

	const PR_COMM 		= 'm_c_products_comments';
	const ID_PR_COMM 	= 'id_m_c_products_comments';

	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	const WH_PR 	= 'wh_products';
	const ID_WH_PR 	= 'id_wh_products';

	const IMG_FOLDER = '/media/catalogue/products/';

	protected $temp_nav_array = array();
	protected $nav_array = array();
	protected $img_path = FALSE;

	protected $settings = array();

	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
		$this->_init();
	}

	public function _init()
	{
		$this->load->model('catalogue/mproducts_settings');
		$this->settings = $this->mproducts_settings->get_settings();

		//limit
		if(!$this->variables->get_additional_url_vars('limit') && $this->session->userdata('product_limit'))
		{
			if($this->variables->get_url_vars('search_string')) redirect($this->router->build_url('search_data_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $this->session->userdata('product_limit'))), 'search_string' => $this->variables->get_url_vars('search_string'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)), 301);
			else if($this->variables->get_url_vars('category_url')) redirect($this->router->build_url('category_filters_sort_page_lang', array('additional_params' => $this->variables->build_additional_url_params(array('limit' => $this->session->userdata('product_limit'))), 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => $this->variables->get_url_vars('page'), 'lang' => $this->mlangs->lang_code)), 301);
		}
		if($this->variables->get_additional_url_vars('limit') && ($this->settings['products_count_to_page'] != $this->variables->get_additional_url_vars('limit'))) $this->session->set_userdata('product_limit', $this->variables->get_additional_url_vars('limit'));
		//---limit---

		if($this->variables->get_additional_url_vars('limit'))
		{
			if(($limit = intval($this->variables->get_additional_url_vars('limit'))) > 0) $this->settings['products_count_to_page'] = $limit;
		}
	}

	public function get_settings()
	{
		return $this->settings;
	}

	public function set_settings($array = array())
	{
		foreach($array as $key => $ms)
		{
			$this->settings[$key] = $ms;
		}
	}

	public function get_bestseller_products($count = 10, $random = FALSE)
	{
		$bestseller_id = array();
		$this->get_BSN_products_query($count, $random);
		$this->db->where("`bestseller`", 1);
		$bestseller_products = $this->db->get()->result_array();
		foreach($bestseller_products as $ms)
		{
			$bestseller_id[] = $ms['ID'];
		}
		return $this->get_products_by_id($bestseller_id, $random);
	}

	public function get_sale_products($count = 10, $random = FALSE)
	{
		$sale_id = array();
		$this->get_BSN_products_query($count, $random, TRUE);
		$this->db->where("`sale`", 1);
		$sale_products = $this->db->get()->result_array();
		foreach($sale_products as $ms)
		{
			$sale_id[] = $ms['ID'];
		}
		return $this->get_products_by_id($sale_id, $random, TRUE);
	}

	public function get_new_products($count = 10, $random = FALSE)
	{
		$sale_id = array();
		$this->get_BSN_products_query($count, $random);
		$this->db->where("`new`", 1);
		$sale_products = $this->db->get()->result_array();
		foreach($sale_products as $ms)
		{
			$sale_id[] = $ms['ID'];
		}
		return $this->get_products_by_id($sale_id, $random, TRUE);
	}

	protected function get_BSN_products_query($count, $random = FALSE)
	{
		$this->db
				->select("RAND() AS RAND, `".self::ID_PR."` AS ID")
				->from("`".self::PR."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`status`", 1)->where("`in_stock`", 1)->limit($count);
		if($random)
		{
			$this->db->order_by("`RAND`");
		}
	}

	protected function get_products_by_id($id, $random = FALSE, $in_stock = FALSE)
	{
		$_products = array('products' => array());
		if(is_array($id))
		{
			if(count($id)>0)
			{
				$this->get_products_by_id_query($id, $in_stock);
				$_products = $this->get_products_by_id_array($this->db->get()->result_array(), $random);
				return $_products + array('products_settings' => $this->settings);
			}
		}
		return $_products  + array('products_settings' => $this->settings);
	}

	protected function get_products_by_id_query($id, $in_stock = FALSE)
	{
		$this->load->model('warehouse/mwarehouses');
		if($wh_id = $this->mwarehouses->get_shop_wh_id())
		{
			$this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt,
				IF (A.`in_stock` > 0, IF (WHP.`qty` > 0, 1, 0), 0) AS `in_stock`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"left")
				->join( "`".self::WH_PR."` AS WHP",
					"WHP.`".self::ID_PR."` = A.`".self::ID_PR."`&& WHP.`".self::ID_WH."` = ".$wh_id,
					"LEFT")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
					"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
					"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
					"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where_in("A.`".self::ID_PR."`", $id)->where("A.`status`", 1);
			if($in_stock) $this->db->where("A.`in_stock`", 1);
			return TRUE;
		}

		$this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
						"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where_in("A.`".self::ID_PR."`", $id)->where("A.`status`", 1);
				if($in_stock) $this->db->where("A.`in_stock`", 1);
		return TRUE;
	}

	protected function get_products_by_id_array($result = array(), $random = FALSE)
	{

		$array = $this->build_products_short_data($result);
		$products = $array;

		if($random)
		{
			shuffle($products);
		}
		return array('products' => $products);
	}

	public function get_search_products_collection($sphinx_products_id)
	{
		$products_array = $this->build_products_search_array($sphinx_products_id);
		$products_array['products_settings'] = $this->settings;

		return $products_array;
	}

	protected function build_products_search_array($sphinx_products_id)
	{
		$products_array = array();
		$products_temp = $this->get_products_by_id($sphinx_products_id);
		foreach($products_temp['products'] as $ms)
		{
			$products_array[$ms['ID']] = $ms;
		}
		return array('products' => $products_array);
	}

	protected function get_search_products_count($search)
	{
		$this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::PR."` AS A")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`status`", 1)->like("A.`sku`", $search);
		$count = $this->db->get()->row_array();
		$count = $count['COUNT'];
		return $count;
	}

	public function get_search_products_query($search, $category_id = FALSE)
	{
		$this->load->model('catalogue/mproducts_settings');
		$this->load->model('warehouse/mwarehouses');
		if($wh_id = $this->mwarehouses->get_shop_wh_id())
		{
			$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`,
                B.`name`, B.`short_description`, IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt,
				IF(A.`in_stock` > 0, IF(WHP.`qty` > 0, 1, 0), 0) AS `in_stock`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				//Select WH qty
				->join( "`".self::WH_PR."` AS WHP",
						"WHP.`".self::ID_PR."` = A.`".self::ID_PR."`&& WHP.`".self::ID_WH."` = ".$wh_id,
						"LEFT")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
						"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`status`", 1)->like("A.`sku`", $search)->order_by("A.new", "DESC")->order_by("A.sort", "DESC");
			return $query;
		}

		$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
						"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`status`", 1)->like("A.`sku`", $search)->order_by("A.new", "DESC")->order_by("A.sort", "DESC");
		return $query;
	}


	//FILTERED & SORT
	public function get_category_products_collection_sort_filtered($category_id)
	{
		$products_array = $this->build_products_sort_filtered_short_array($category_id);
		$products_array['products_settings'] = $this->settings;
		return $products_array;
	}

	protected function get_category_products_sort_filtered_count($category_id)
	{
		$this->db
			->select("COUNT(*) AS COUNT")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_CAT."` AS PRCAT",
				"PRCAT.`".self::ID_CAT."` = '".$category_id."' && PRCAT.`".self::ID_PR."` = A.`".self::ID_PR."`",
				"INNER")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`status`", 1);

		$this->load->model('catalogue/mproducts_sort_filters');
		list($sph_products, $sph_products_sort) = $this->mproducts_sort_filters->get_sphinx_products();
		if(count($sph_products) > 0)
		{
			$this->db->where_in("A.`".self::ID_PR."`", $sph_products);
		}

		$count = $this->db->get()->row_array();
		$count = $count['COUNT'];
		return $count;
	}

	protected function build_products_sort_filtered_short_array($category_id)
	{
		$products_array = array();
		$pages_array = array();
		$count = $this->get_category_products_sort_filtered_count($category_id);
		if($count > 0)
		{
			$page = 1;
			if((int) $this->variables->get_url_vars('page')>0)
			{
				$page = (int) $this->variables->get_url_vars('page');
			}

			$this->get_category_products_query($category_id);

			$this->load->model('catalogue/mproducts_sort_filters');
			list($sph_products, $sph_sort) = $this->mproducts_sort_filters->get_sphinx_products();
			if(count($sph_products) > 0)
			{
				if($sph_sort)
				{
					if($pages_data = $this->set_filtered_sort_limit($count, $page)) $sph_pr_limit = array_slice($sph_products, $pages_data[4], $pages_data[3], TRUE);
					else $sph_pr_limit = $sph_products;

					$this->db->where_in("PRCAT.`".self::ID_PR."`", $sph_pr_limit);
					//$new_pr_array = array();
					foreach($this->db->get()->result_array() as $ms)
					{
						$sph_pr_limit[$ms['ID']] = $ms;
					}
					$pr_array = $sph_pr_limit;
				}
				else
				{
					$pr_array = array();
					$this->db->where_in("PRCAT.`".self::ID_PR."`", $sph_products);
					if($pages_data = $this->set_filtered_sort_limit($count, $page)) $this->db->limit($pages_data[3], $pages_data[4]);
					$pr_array = $this->db->get()->result_array();
				}
			}
			else
			{
				if($pages_data = $this->set_filtered_sort_limit($count, $page)) $this->db->limit($pages_data[3], $pages_data[4]);
				$pr_array = $this->db->get()->result_array();
			}

			$products_array = $this->build_products_short_data($pr_array);

			if($pages_data)
			{
				$this->load->helper('pages');
				$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('category_filters_sort_page_lang', array('category_url' => $this->variables->get_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'page' => '', 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())));
			}
			return array('products' => $products_array, 'pages' => $pages_array);
		}
		else
		{
			return array('products' => $products_array, 'pages' => $pages_array);
		}
	}

	protected function set_filtered_sort_limit($count, $page = 1)
	{
		$limit = $this->settings['products_count_to_page'];
		$page_count = ceil($count/$limit);
		$add_params = $this->variables->build_additional_url_params();
		if($page > $page_count) redirect($this->router->build_url('category_filters_sort_page_lang', array('page' => $page_count, 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)) ,301);


		if($this->variables->get_url_vars('page') == 1)
		{
			redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)) ,301);
		}

		if($page == 0) $page = 1;

		if($page_count > 1)
		{
			if($page > 1) $this->template->unset_template_view('categories_description_block');
			if($page == 1)
			{
				$this->template->add_rel_next($this->router->build_url('category_filters_sort_page_lang', array('page' => $page + 1, 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
			}
			else if($page < $page_count)
			{
				if($page > 2)
				{
					$this->template->add_rel_prev($this->router->build_url('category_filters_sort_page_lang', array('page' => $page - 1, 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				else
				{
					$this->template->add_rel_prev($this->router->build_url('category_filters_sort_lang', array('category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				$this->template->add_rel_next($this->router->build_url('category_filters_sort_page_lang', array('page' => $page + 1, 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
			}
			else
			{
				if($page > 2)
				{
					$this->template->add_rel_prev($this->router->build_url('category_filters_sort_page_lang', array('page' => $page - 1, 'category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				else
				{
					$this->template->add_rel_prev($this->router->build_url('category_filters_sort_lang', array('category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
			}

			$offset = ($page-1)*$limit;
			$showcount = $limit;

			return array($count, $page, $limit, $showcount, $offset);
		}
		return FALSE;
	}
	//---FILTERED & SORT---

	public function get_category_products_collection($category_id)
	{
		$products_array = $this->build_products_short_array($category_id);
		$products_array['products_settings'] = $this->settings;

		return $products_array;
	}

	public function get_products_array_by_id($products_id = array())
	{
		$products_array = array();

		if(count($products_id) > 0)
		{
			$this->get_products_query($products_id);
			$pr_array = $this->db->get()->result_array();

			$products_array = $this->build_products_short_data($pr_array);
			$products_array = $this->build_products_short_image_array($products_array);

			return array('products' => $products_array, 'result' => 1);
		}
		else
		{
			return array('products' => $products_array, 'result' => 0);
		}
	}

	protected function build_products_short_array($category_id)
	{
		$products_array = array();
		$pages_array = array();
		$images_array = array();
		$count = $this->get_category_products_count($category_id);
		if($count > 0)
		{
			$page = 1;
			if($this->variables->get_url_vars('page') == 1) redirect($this->router->build_url('category_lang', array('category_url' => $this->variables->get_url_vars('category_url'), 'lang' => $this->mlangs->lang_code)) ,301);
			if((int) $this->variables->get_url_vars('page')>0)
			{
				$page = (int) $this->variables->get_url_vars('page');
			}

			$this->get_category_products_query($category_id);
			if($pages_data = $this->set_limit($count, $page)) $this->db->limit($pages_data[3], $pages_data[4]);
			$pr_array = $this->db->get()->result_array();

			$products_array = $this->build_products_short_data($pr_array);
			$products_array = $this->build_products_short_image_array($products_array);

			if($pages_data)
			{
				$this->load->helper('pages');
				$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('category_page_lang', array('category_url' => $this->variables->get_vars('category_url'), 'page' => '', 'lang' => $this->mlangs->lang_code)));
			}
			return array('products' => $products_array, 'pages' => $pages_array);
		}
		else
		{
			return array('products' => $products_array, 'pages' => $pages_array);
		}
	}

	protected function build_products_short_image_array($products_array)
	{
		$images_array = array();
		$id_array = array();
		if($products_array && count($products_array)>0)
		{
			foreach($products_array as $id => $ms) {
				$id_array[] = $id;
			}

			$lang = $this->mlangs->id_langs;
			$this->db
				->select("IMG.`".self::ID_PR."` as ID, IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG.`preview`, IMG_DESC.`name`, IMG_DESC.`title`")
				->from("`".self::PR_IMG."` AS IMG")
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
					"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$lang."'",
					"LEFT")
				->where_in("IMG.`".self::ID_PR."`", $id_array)
				->order_by("IMG.`preview`", "DESC")->order_by("IMG.`sort`", "asc");

			$result_array = $this->db->get()->result_array();

			foreach($result_array as $key => $image) {
				$alb_seg = '';
				if($image['ALB_ID'] != NULL)
				{
					$alb_seg = '/'.$image['ALB_ID'];
				}
				$result_array[$key]['image'] = $this->img_path.$image['ID'].$alb_seg.'/'.$image['image'];
				$result_array[$key]['timage'] = $this->img_path.$image['ID'].$alb_seg.'/thumb_'.$image['image'];
				$result_array[$key]['bimage'] = $this->img_path.$image['ID'].$alb_seg.'/'.$image['image'];
			}

			foreach($result_array as $image) {
				$products_array[$image['ID']]['images'][] = $image;
			}

		}
		return $products_array;
	}

	protected function get_category_products_count($category_id)
	{
		$this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_CAT."` AS PRCAT",
						"PRCAT.`".self::ID_CAT."` = '".$category_id."' && PRCAT.`".self::ID_PR."` = A.`".self::ID_PR."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`status`", 1);

		$count = $this->db->get()->row_array();
		$count = $count['COUNT'];
		return $count;
	}

	protected function get_products_query($products_id = array())
	{
		$this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
				"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
				"left")
			//Select Images
			->join(	"`".self::PR_IMG."` AS IMG",
				"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
				"LEFT")
			//Select Images Description
			->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
				"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
				"LEFT")
			->where_in("A.`".self::ID_PR."`", $products_id)->where("A.`".self::ID_USERS."`", $this->id_users);

		return TRUE;
	}

	protected function get_category_products_query($category_id = FALSE)
	{
		if(!$category_id)
		{
			if(!$category_id = $this->variables->get_vars('category_id'))
			{
				return FALSE;
			}
		}
		$this->load->model('catalogue/mproducts_settings');

		$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		if($wh_settings['wh_on'] == 1 && $wh_settings['wh_active'] == 1)
		{
			$this->db->select("`".self::ID_WH."`")
					->from("`".self::WH."`")
					->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->where("`i_s_wh`", 1)->limit(1);
			$SHOP_WH = $this->db->get()->row_array();
			if(count($SHOP_WH) > 0)
			{
				$wh_id = $SHOP_WH[self::ID_WH];
				$this->db
					->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`,
                    B.`name`, B.`short_description`, IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt,
					IF(A.`in_stock` > 0, IF(WHP.`qty` > 0, 1, 0), 0) AS `in_stock`")
					->from("`".self::PR_CAT."` AS PRCAT USE INDEX (`".self::ID_CAT."`)")

					->join(	"`".self::PR."` AS A",
							"A.`".self::ID_PR."` = PRCAT.`".self::ID_PR."` && A.`status` = '1'",
							"INNER")
					->join(	"`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					//Select WH qty
					->join( "`".self::WH_PR."` AS WHP",
							"WHP.`".self::ID_PR."` = PRCAT.`".self::ID_PR."`&& WHP.`".self::ID_WH."` = ".$wh_id,
							"LEFT")
					//Select Images
					->join(	"`".self::PR_IMG."` AS IMG",
							"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
							"LEFT")
					//Select Images Description
					->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
							"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("PRCAT.`".self::ID_CAT."`", $category_id)->where("PRCAT.`".self::ID_USERS."`", $this->id_users)->order_by("PRCAT.`new`", "DESC");

				if($this->settings['products_sort_type'] == 1) $this->db->order_by("PRCAT.`sort`", "DESC"); else $this->db->order_by("PRCAT.`sort`");
				return TRUE;
			}
		}

		$this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt")
				->from("`".self::PR_CAT."` AS PRCAT USE INDEX (`".self::ID_CAT."`)")

				->join(	"`".self::PR."` AS A",
						"A.`".self::ID_PR."` = PRCAT.`".self::ID_PR."` && A.`status` = '1'",
						"INNER")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
						"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("PRCAT.`".self::ID_CAT."`", $category_id)->where("PRCAT.`".self::ID_USERS."`", $this->id_users)->order_by("PRCAT.`new`", "DESC");
		if($this->settings['products_sort_type'] == 1) $this->db->order_by("PRCAT.`sort`", "DESC"); else $this->db->order_by("PRCAT.`sort`");
		return TRUE;
	}

	protected function set_limit($count, $page = 1)
	{
		$limit = $this->settings['products_count_to_page'];
		$page_count = ceil($count/$limit);
		$add_params = $this->variables->build_additional_url_params();
		if($page > $page_count)
		{
			redirect($this->router->build_url('category_page_lang', array('page' => $page_count, 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)) ,301);
		}

		if($page == 0)
		{
			$page = 1;
		}
		if($page_count > 1)
		{
			if($page > 1) $this->template->unset_template_view('categories_description_block');
			if($page == 1)
			{
				$this->template->add_rel_next($this->router->build_url('category_page_lang', array('page' => $page + 1, 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
			}
			else if($page < $page_count)
			{
				if($page > 2)
				{
					$this->template->add_rel_prev($this->router->build_url('category_page_lang', array('page' => $page - 1, 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				else
				{
					$this->template->add_rel_prev($this->router->build_url('category_lang', array('category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				$this->template->add_rel_next($this->router->build_url('category_page_lang', array('page' => $page + 1, 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
			}
			else
			{
				if($page > 2)
				{
					$this->template->add_rel_prev($this->router->build_url('category_page_lang', array('page' => $page - 1, 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
				else
				{
					$this->template->add_rel_prev($this->router->build_url('category_lang', array('category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params)));
				}
			}

			$offset = ($page-1)*$limit;
			$showcount = $limit;

			return array($count, $page, $limit, $showcount, $offset);
		}
		return FALSE;
	}

	protected function build_products_short_data($products_temp_array)
	{
		$products_array = array();
		if($products_temp_array && count($products_temp_array)>0)
		{
			$add_params = $this->variables->build_additional_url_params();

			foreach($products_temp_array as $ms)
			{
				if(is_array($ms)) {

				$product_url = $ms['ID'];
				if($ms['url_key'] != '')
				{
					$product_url = trim($ms['url_key']);
				}

				$url = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params));

				$products_array[$ms['ID']] = $ms;
				if($ms['image'] != NULL)
				{
					$alb_seg = '';
					if($ms['ALB_ID'] != NULL)
					{
						$alb_seg = '/'.$ms['ALB_ID'];
					}
					$products_array[$ms['ID']] += array('timage' => $this->img_path.$ms['ID'].$alb_seg.'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].$alb_seg.'/'.$ms['image']);
				}
				$products_array[$ms['ID']] += array('detail_url' => $url);
				}
			}

				//	exit;
			$products_array = $this->build_products_short_prices_array($products_array);
            $products_array = $this->build_products_short_image_array($products_array);
		}
		return $products_array;
	}

	protected function build_products_short_prices_array($products_array)
	{
		$price_pr_array = array();
		foreach($products_array as $key => $ms)
		{
			$price_pr_array[$key] = $key;
			$products_array[$key]['prices_array'] = array();
			$products_array[$key]['prices_access']['prices_visible_count'] = 0;
			$products_array[$key]['prices_access']['prices_error_access'] = FALSE;
		}

		if($this->get_short_products_prices_query($price_pr_array))
		{
			$products_array = $this->build_products_short_prices_data($this->db->get()->result_array(), $products_array);
		}
		$temp_pr_array = $products_array;
		$customer = $this->session->userdata('CUSTOMER');
		foreach($temp_pr_array as $key => $ms)
		{
			$ms['prices_access']['prices_customer_in_group'] = FALSE;
			$ms['prices_access']['prices_customer_is_registered'] = FALSE;
			if($customer)
			{
				$ms['prices_access']['prices_customer_is_registered'] = TRUE;
				if($customer['have_m_u_types'] == 1)
				{
					$ms['prices_access']['prices_customer_in_group'] = TRUE;
				}
			}
			$ms['prices_access']['prices_white_admin'] = FALSE;
			$ms['prices_access']['prices_error_access_string'] = FALSE;

			if($ms['prices_access']['prices_error_access'] && $ms['prices_access']['prices_visible_count'] == 0)
			{
				if($ms['prices_access']['prices_customer_is_registered'])
				{
					$ms['prices_access']['prices_error_access_string'] = $this->lang->line('products_price_no_access_to_register');
					$ms['prices_access']['prices_white_admin'] = TRUE;
				}
				else
				{
					$ms['prices_access']['prices_error_access_string'] = $this->lang->line('products_price_no_access');
				}
			}
			else if($ms['prices_access']['prices_error_access'] && !$ms['prices_access']['prices_customer_in_group'] && $ms['prices_access']['prices_visible_count'] > 0)
			{
				if($ms['prices_access']['prices_customer_is_registered'])
				{
					$ms['prices_access']['prices_error_access_string'] = $this->lang->line('products_price_many_no_access_to_register');
					$ms['prices_access']['prices_white_admin'] = TRUE;
				}
				else
				{
					$ms['prices_access']['prices_error_access_string'] = $this->lang->line('products_price_many_no_access');
				}
			}
			$ms['price'] = $this->generate_price_html($ms['prices_array'], $ms['prices_access']);
			$products_array[$key] = $ms;
		}
		return $products_array;
	}

	protected function build_products_short_prices_data($price_pr_array, $products_array)
	{
		$today = mktime();
		$pr_prices_result = $price_pr_array;

		foreach($pr_prices_result as $ms)
		{
			$ms['price_visible'] = TRUE;

			if($ms['price_visible_rules']>0)
			{
				if($ms['price_visible_rules'] == 1)
				{
					if(($this->session->userdata('customer_id')) == FALSE) $ms['price_visible'] = FALSE;
				}
				else if($ms['price_visible_rules'] == 2)
				{
					if(($this->session->userdata('customer_id')) == FALSE)
					{
						$ms['price_visible'] = FALSE;
					}
					else
					{
						$customer = $this->session->userdata('CUSTOMER');
						if($customer['have_m_u_types'] == 1)
						{
							$ms['price_visible'] = FALSE;
							$t_m_u_types = explode(',', $ms['price_m_u_types']);
							$customer_m_u_types = $customer['m_u_types'];
							foreach($t_m_u_types as $ms1)
							{
								if(isset($customer_m_u_types[$ms1]))
								{
									$ms['price_visible'] = TRUE;
									break;
								}
							}
						}
						else
						{
							$ms['price_visible'] = FALSE;
						}
					}
				}
			}
			if($ms['price_visible'])
			{
				$original_special_price = $ms['original_special_price'];
				$special_price = $ms['special_price_rate'];
				$special_price_to_date = FALSE;
				if((float) $special_price > 0)
				{
					$special_price_to_date = FALSE;
					if($ms['special_price_from'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_from']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						if($today < $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
						}
					}
					if($ms['special_price_to'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_to']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						$special_price_to_date = $ms['special_price_to'];
						if($today > $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
							$special_price_to_date = FALSE;
						}
					}
				}
				else
				{
					$original_special_price = FALSE;
					$special_price = FALSE;
				}

				$ms['original_special_price'] = $original_special_price;
				$ms['special_price_rate'] = $special_price;
				$ms['special_price_to_date'] = $special_price_to_date;

				$ms['original_price_string'] = number_format($ms['original_price'], 2, ',', ' ');
				$ms['price_rate_string'] = number_format($ms['price_rate'], 2, ',', ' ');
				if($special_price)
				{
					$ms['original_special_price_string'] = number_format($ms['original_special_price'], 2, ',', ' ');
					$ms['special_price_rate_string'] = number_format($ms['special_price_rate'], 2, ',', ' ');
				}
				if($ms['price_name'] != '') $ms['price_name'] .= ' ';
				$products_array[$ms['PR_ID']]['prices_access']['prices_visible_count'] += 1;
				$products_array[$ms['PR_ID']]['prices_array'][$ms['PRICE_ID']] = $ms;
			}
			else
			{
				$products_array[$ms['PR_ID']]['prices_access']['prices_error_access'] = TRUE;
			}
		}
		return $products_array;
	}

	protected function get_short_products_prices_query($products_id)
	{
		$this->load->model('catalogue/mcurrency');
		if(!$currency = $this->mcurrency->get_current_currency()) return FALSE;

		$this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS `PRICE_ID`, PRICE.`".self::ID_PR."` AS `PR_ID`,
				PRICE.`price` AS `original_price` ,(PRICE.`price` * ".$currency['rate'].") AS `price_rate`, PRICE.`special_price` AS `original_special_price`, (PRICE.`special_price` * ".$currency['rate'].") AS `special_price_rate`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`visible_rules` AS `price_visible_rules`, PRICE.`m_u_types` AS `price_m_u_types`, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS `price_description`,
				'".$currency['name']."' AS `currency_name`")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join( "`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where_in("PRICE.`".self::ID_PR."`", $products_id)->where("PRICE.`show_in_short`", 1)->order_by("PRICE.`".self::ID_PR_PRICE."`");
		return TRUE;
	}

	public function get_product($url = FALSE)
	{
		if(!$url)
		{
			if($url = $this->variables->get_url_vars('product_url'));
		}
		$product_array = $this->build_products_detail_array($url);

		return $product_array;
	}



	protected function build_products_detail_array($url)
	{
		if($product = $this->build_product_detail_data($url))
		{
			$product_array['product'] = $product;
			$pr_id = $product['ID'];
			$product_array['next'] = $this->get_next_product($pr_id);
			$product_array['prev'] = $this->get_prev_product($pr_id);
			$product_array += $this->build_product_detail_images_array($pr_id);
			$product_array += $this->build_product_detail_prices_array($pr_id);
			$product_array += $this->build_product_detail_attributes_array($pr_id, $product_array['selected_price']);
			if($this->settings['related_on'] == 1) $product_array['related_products'] = $this->get_product_detail_related_products($pr_id);
			if($this->settings['similar_on'] == 1) $product_array['similar_products'] = $this->get_product_detail_similar_products($pr_id);
			if($this->settings['reviews_on'] == 1) $product_array['comments_array'] = $this->get_product_detail_comments($pr_id);
			$product_array['product_settings'] = $this->settings;

			$this->build_product_detail_navigation($pr_id, $product_array['product']['name']);

			return $product_array;
		}
		return FALSE;
	}

	protected function get_next_product($pr_id)
	{
		$product = array();
		$cat_id = $this->get_cat_by_prod($pr_id);

		$sort = $this->db->select("`sort`")
			->from("`".self::PR_CAT."`")
			->where("`".self::ID_PR."`", $pr_id)
			->where("`".self::ID_USERS."`", $this->id_users)
			->limit(1)
			->get()->result_array();
			$sort = $sort[0]['sort'];

		$next_id = $this->db->select("`".self::ID_PR."`")
			->from("`".self::PR_CAT."`")
			->where("`sort` <", $sort)
			->where("`".self::ID_CAT."`", $cat_id)
			->where("`".self::ID_USERS."`", $this->id_users)
			->order_by(self::ID_PR, "DESC")
			->limit(1)
			->get()->result_array();

		if(!empty($next_id)) {
			$next_id = $next_id[0][self::ID_PR];
			$url_key = $this->db->select("`url_key`")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $pr_id)
				->where("`".self::ID_USERS."`", $this->id_users)
				->get()->result_array();
				$url_key = $url_key[0]['url_key'];

			$product_url = $next_id;
			if (trim($url_key) != '') {
				$product_url = trim($url_key);
			}

			$url = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));

			$name = $this->build_product_detail_data($next_id);
			$product_next['name'] = $name['name'];
			$image = $this->build_product_detail_images_array($next_id);
			$product_next['image'] = $image['images_array'][0]['timage'];
			$product_next['url'] = $url;

			return $product_next;
		}
	}

	protected function get_prev_product($pr_id)
	{
		$product = array();
		$cat_id = $this->get_cat_by_prod($pr_id);

		$sort = $this->db->select("`sort`")
			->from("`".self::PR_CAT."`")
			->where("`".self::ID_PR."`", $pr_id)
			->where("`".self::ID_USERS."`", $this->id_users)
			->limit(1)
			->get()->result_array();
			$sort = $sort[0]['sort'];

		$prev_id = $this->db->select("`".self::ID_PR."`")
			->from("`".self::PR_CAT."`")
			->where("`sort` >", $sort)
			->where("`".self::ID_CAT."`", $cat_id)
			->where("`".self::ID_USERS."`", $this->id_users)
			->order_by(self::ID_PR)
			->limit(1)
			->get()->result_array();

		if(!empty($prev_id)) {
			$prev_id = $prev_id[0][self::ID_PR];
			$url_key = $this->db->select("`url_key`")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $pr_id)
				->where("`".self::ID_USERS."`", $this->id_users)
				->get()->result_array();
				$url_key = $url_key[0]['url_key'];

			$product_url = $prev_id;
			if (trim($url_key) != '') {
				$product_url = trim($url_key);
			}

			$url = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));

			$name = $this->build_product_detail_data($prev_id);
			$product_prev['name'] = $name['name'];
			$image = $this->build_product_detail_images_array($prev_id);
			$product_prev['image'] = $image['images_array'][0]['timage'];
			$product_prev['url'] = $url;

			return $product_prev;
		}
	}

	protected function get_cat_by_prod($pr_id) {
		$parent_id = $this->db->select("CAT.`".self::ID_CAT."` as ID")
			->from("`".self::PR_CAT."` as PR_CAT")
			->join("`".self::CAT."` as CAT", "CAT.`".self::ID_CAT."` = PR_CAT.`".self::ID_CAT."`")
			->where("PR_CAT.`".self::ID_PR, $pr_id)
			->where("PR_CAT.`".self::ID_USERS."`", $this->id_users)
			->order_by("CAT.`level`")->order_by("CAT.`sort`")
			->limit(1)->get()->result_array();

		$cat_id = $this->db->select("CAT.`".self::ID_CAT."` as ID")
			->from("`".self::CAT_LINK."` as CAT_L")
			->join("`".self::CAT."` as CAT", "CAT_L.`id_parent` = ".$parent_id[0]['ID']." && CAT.`".self::ID_CAT."` = CAT_L.`".self::ID_CAT."`")
			->join("`".self::PR_CAT."` as PR_CAT", "CAT_L.`".self::ID_CAT."` = PR_CAT.`".self::ID_CAT."`")
			->where("PR_CAT.`".self::ID_PR, $pr_id)
			->where("CAT.`active`", 1)
			->where("CAT.`".self::ID_USERS."`", $this->id_users)
			->order_by("CAT.`level`", "DESC")->order_by("CAT.`sort`")
			->limit(1)->get()->result_array();

		/*$cat_id = $this->db->select("CAT.`".self::ID_CAT."` as ID")
			->from("`".self::PR_CAT."` as PR_CAT")
			->join("`".self::CAT."` as CAT", "CAT.`".self::ID_CAT."` = PR_CAT.`".self::ID_CAT."`")
			->where("PR_CAT.`".self::ID_PR, $pr_id)
			->where("PR_CAT.`".self::ID_USERS."`", $this->id_users)
			->order_by("CAT.`level`", "DESC")
			->limit(1)->get()->result_array();*/

		if(!empty($cat_id)) {
			return $cat_id[0]['ID'];
		} else {
			return $parent_id[0]['ID'];
		}
	}

	protected function build_product_detail_data($url)
	{
		$this->get_product_detail_query($url);
		$product_array = $this->db->get()->row_array();
		if(count($product_array) == 0) return FALSE;
		if($url == $product_array['ID'] && strlen($product_array['url_key']) != '')
		{
			redirect($this->router->build_url('product_lang', array('product_url' => $product_array['url_key'], 'lang' => $this->mlangs->lang_code)), 301);
		}

		$seo = array('name' => $product_array['name'], 'seo_title' => $product_array['seo_title'], 'seo_description' => $product_array['seo_description'], 'seo_keywords' => $product_array['seo_keywords']);
		if(count($seo)>0)
		{
			if($seo['seo_title'] == '')
			{
				$seo['seo_title'] = $seo['name'];
			}
			$this->template->set_TDK($seo);
		}
		//$ref = getenv("HTTP_REFERER");
        $ref = $this->session->get_data('back_url');
		$pos = strpos($ref, $_SERVER['SERVER_NAME']);
		if($pos && $pos < 10)
		{
			$product_array['back_url'] = $ref;
		}
		return $product_array;
	}

	protected function get_product_detail_query($url)
	{
		$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		if($wh_settings['wh_on'] == 1 && $wh_settings['wh_active'] == 1)
		{
			$this->db->select("`".self::ID_WH."`")
					->from("`".self::WH."`")
					->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->where("`i_s_wh`", 1)->limit(1);
			$SHOP_WH = $this->db->get()->row_array();
			if(count($SHOP_WH) > 0)
			{
				$wh_id = $SHOP_WH[self::ID_WH];
				$this->db
					->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`bestseller`, A.`sale`, A.`new`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`full_description`, B.`short_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`,
							IF(A.`in_stock` > 0, IF(WHP.`qty` > 0, 1, 0), 0) AS `in_stock`")
					->from("`".self::PR."` AS A")
					->join( "`".self::WH_PR."` AS WHP",
							"WHP.`".self::ID_PR."` = A.`".self::ID_PR."` && WHP.`".self::ID_WH."` = ".$wh_id,
							"LEFT")
					->join(	"`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"left")
					->where("A.`".self::ID_USERS."`", $this->id_users)->where("(A.`url_key` = '".$url."' || A.`".self::ID_PR."` = '".(int) $url."')", NULL)->limit(1);
				return TRUE;
			}
		}
		$this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`full_description`, B.`short_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("(A.`url_key` = '".$url."' || A.`".self::ID_PR."` = '".(int) $url."')", NULL)->limit(1);
		return TRUE;
	}

	protected function build_product_detail_images_array($product_id)
	{
		$albums_array = array();
		$this->get_product_detail_albums_query($product_id);
		$album_temp_array = $this->db->get()->result_array();
		foreach($album_temp_array as $ms)
		{
			$albums_array[$ms['ALBUM_ID']] = $ms;
		}

		$images_array = array();
		$images_in_album_array = array();
		$this->get_product_detail_images_query($product_id);
		$temp_array = $this->db->get()->result_array();
		if(count($albums_array)>0)
		{
			foreach($temp_array as $ms)
			{
				$images_in_album_array[$ms['ALBUM_ID']][] = $ms + array('timage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/'.$ms['image']);
				$images_array[] = $ms + array('timage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/'.$ms['image']);
			}
		}
		else
		{
			foreach($temp_array as $ms)
			{
				$images_array[] = $ms + array('timage' => $this->img_path.$ms['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['image']);
			}
		}
		return array('images_array' => $images_array, 'albums_array' => $albums_array, 'images_in_album_array' => $images_in_album_array);
	}

	protected function get_product_detail_images_query($product_id)
	{
		$query = $this->db
				->select("A.`image`, A.`".self::ID_PR_ALB."` AS ALBUM_ID, A.`".self::ID_PR."` AS ID, B.`name` AS image_name, B.`title` AS image_title, B.`alt` AS image_alt")
				->from("`".self::PR_IMG."` AS A")
				->join(	"`".self::PR_IMG_DESC."` AS B",
						"B.`".self::ID_PR_IMG."` = A.`".self::ID_PR_IMG."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_PR."`", $product_id)->order_by("A.`preview`", "DESC")->order_by("A.`sort`");
		return $query;
	}

	protected function get_product_detail_albums_query($product_id)
	{
		$this->db->select("A.`".self::ID_PR_ALB."` AS ALBUM_ID, A.`type`, A.`color`, A.`".self::ID_ATTR."` AS ATTR_ID, A.`".self::ID_OP."` AS OPT_ID, B.`name`")
				->from("`".self::PR_ALB."` AS A")
				->join("`".self::PR_ALB_DESC."` AS B",
						"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_PR."`", $product_id)->where("A.`active`", 1)->order_by("A.`sort`");
		return TRUE;
	}

	protected function build_product_detail_prices_array($product_id)
	{
		if(($this->get_product_detail_prices_query($product_id)) != FALSE)
		{
			$prices_array = $this->build_product_detail_prices_data($this->db->get()->result_array());
		}
		else
		{
			$prices_array['error_access'] = TRUE;
		}

		$prices_array['customer_in_group'] = FALSE;
		$prices_array['customer_is_registered'] = FALSE;
		if($customer = $this->session->userdata('CUSTOMER'))
		{
			$prices_array['customer_is_registered'] = TRUE;
			if($customer['have_m_u_types'] == 1)
			{
				$prices_array['customer_in_group'] = TRUE;
			}
		}
		$prices_array['white_admin'] = FALSE;
		$prices_array['price_error_access_string'] = FALSE;

		if($prices_array['error_access'] && $prices_array['visible_prices_count'] == 0)
		{
			if($prices_array['customer_is_registered'])
			{
				$prices_array['price_error_access_string'] = $this->lang->line('products_price_no_access_to_register');
				$prices_array['white_admin'] = TRUE;
			}
			else
			{
				$prices_array['price_error_access_string'] = $this->lang->line('products_price_no_access');
			}
		}
		else if($prices_array['error_access'] && !$prices_array['customer_in_group'] && $prices_array['visible_prices_count'] > 0)
		{
			if($prices_array['customer_is_registered'])
			{
				$prices_array['price_error_access_string'] = $this->lang->line('products_price_many_no_access_to_register');
				$prices_array['white_admin'] = TRUE;
			}
			else
			{
				$prices_array['price_error_access_string'] = $this->lang->line('products_price_many_no_access');
			}
		}
		return $prices_array;
	}

	protected function build_product_detail_prices_data($prices_temp_array)
	{
		$prices_array['prices_array'] = array();
		$prices_array['selected_price'] = FALSE;
		$prices_array['prices'] = FALSE;
		$prices_array['visible_prices_count'] = 0;
		$prices_array['error_access'] = FALSE;
		$selected_price = TRUE;

		foreach($prices_temp_array as $ms)
		{
			$ms['price_visible'] = TRUE;
			if($ms['price_visible_rules']>0)
			{
				if($ms['price_visible_rules'] == 1)
				{
					if(($this->session->userdata('customer_id')) == FALSE) $ms['price_visible'] = FALSE;
				}
				else if($ms['price_visible_rules'] == 2)
				{
					if(($this->session->userdata('customer_id')) == FALSE)
					{
						$ms['price_visible'] = FALSE;
					}
					else
					{
						$customer = $this->session->userdata('CUSTOMER');
						if($customer['have_m_u_types'] == 1)
						{
							$ms['price_visible'] = FALSE;
							$t_m_u_types = explode(',', $ms['price_m_u_types']);
							$customer_m_u_types = $customer['m_u_types'];
							foreach($t_m_u_types as $ms1)
							{
								if(isset($customer_m_u_types[$ms1]))
								{
									$ms['price_visible'] = TRUE;
									break;
								}
							}
						}
						else
						{
							$ms['price_visible'] = FALSE;
						}
					}
				}
			}
			if($ms['price_visible'])
			{
				$original_special_price = $ms['original_special_price'];
				$special_price = $ms['special_price_rate'];
				$special_price_to_date = FALSE;
				if((float) $special_price > 0)
				{
					$today = mktime();
					$special_price_to_date = FALSE;
					if($ms['special_price_from'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_from']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						if($today < $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
						}
					}
					if($ms['special_price_to'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_to']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						$special_price_to_date = $ms['special_price_to'];
						if($today > $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
							$special_price_to_date = FALSE;
						}
					}
				}
				else
				{
					$original_special_price = FALSE;
					$special_price = FALSE;
				}
				$ms['original_special_price'] = $original_special_price;
				$ms['special_price_rate'] = $special_price;
				$ms['special_price_to_date'] = $special_price_to_date;

				$ms['price_attributes_js_array']['show_attributes'] = $ms['show_attributes'];
				$ms['price_attributes_js_array']['id_attributes'] = array();
				if($ms['show_attributes'] == 2)
				{
					foreach(explode(',', $ms['id_attributes']) as $ms1)
					{
						if($ms1 != '')
						{
							$ms['price_attributes_js_array']['id_attributes'][$ms1] = $ms1;
						}
					}
				}
				if($selected_price)
				{
					$ms['selected_price'] = TRUE;
					$selected_price = FALSE;
					$prices_array['selected_price'] = $ms;
				}
				else
				{
					$ms['selected_price'] = FALSE;
				}
				$ms['original_price_string'] = number_format($ms['original_price'], 2, ',', ' ');
				$ms['price_rate_string'] = number_format($ms['price_rate'], 2, ',', ' ');
				if($special_price)
				{
					$ms['original_special_price_string'] = number_format($ms['original_special_price'], 2, ',', ' ');
					$ms['special_price_rate_string'] = number_format($ms['special_price_rate'], 2, ',', ' ');
				}
				if($ms['price_name'] != '') $ms['price_name'] .= ' ';
				$prices_array['prices_array'][$ms['PRICE_ID']] = $ms;
				$prices_array['visible_prices_count'] += 1;
			}
			else
			{
				$prices_array['error_access'] = TRUE;
			}
		}
		return $prices_array;
	}

	protected function get_product_detail_prices_query($product_id, $price_id = FALSE)
	{
		$this->load->model('catalogue/mcurrency');
		if(!$currency = $this->mcurrency->get_current_currency()) return FALSE;

		$this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS `PRICE_ID`, PRICE.`".self::ID_PR."` AS `PR_ID`,
				PRICE.`price` AS `original_price` ,(PRICE.`price` * ".$currency['rate'].") AS `price_rate`, PRICE.`special_price` AS `original_special_price`, (PRICE.`special_price` * ".$currency['rate'].") AS `special_price_rate`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`visible_rules` AS `price_visible_rules`, PRICE.`m_u_types` AS `price_m_u_types`, PRICE.`show_attributes`, PRICE.`id_attributes`, PRICE.`min_qty`, PRICE.`real_qty`, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS `price_description`,
				'".$currency['name']."' AS `currency_name`")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join( "`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where_in("PRICE.`".self::ID_PR."`", $product_id)->where("PRICE.`show_in_detail`", 1)->order_by("PRICE.`".self::ID_PR_PRICE."`")->limit(16);
		if($price_id) $this->db->where("PRICE.`".self::ID_PR_PRICE."`", $price_id);
		return TRUE;
	}

	protected function build_product_detail_attributes_array($product_id, $selected_price = FALSE)
	{
		$attributes_array['attributes_array'] = array();
		$attributes_array['attributes'] = '';
		$this->get_product_detail_attributes_query($product_id);
		if($selected_price)
		{
			foreach($this->db->get()->result_array() as $ms)
			{
				if($selected_price['price_attributes_js_array']['show_attributes'] == 1) $ms['visible'] = 1;
				if($selected_price['price_attributes_js_array']['show_attributes'] == 0) $ms['visible'] = 0;
				if($selected_price['price_attributes_js_array']['show_attributes'] == 2)
				{
					if(isset($selected_price['price_attributes_js_array']['id_attributes'][$ms['ID']])) $ms['visible'] = 1;
					else $ms['visible'] = 0;
				}
				$attributes_array['attributes_array']['attributes'][$ms['ID']] = $ms;
				$attributes_array['attributes_array']['options'][$ms['ID']][$ms['ID_OP']] = $ms['o_name'];
			}
		}
		else
		{
			foreach($this->db->get()->result_array() as $ms)
			{
				$ms['visible'] = 1;
				$attributes_array['attributes_array']['attributes'][$ms['ID']] = $ms;
				$attributes_array['attributes_array']['options'][$ms['ID']][$ms['ID_OP']] = $ms['o_name'];
			}
		}
		return $attributes_array;
	}

	protected function get_product_detail_attributes_query($product_id)
	{
		$this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($product_id))
		{
			$IN = '';
			foreach($product_id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$this->db->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
			unset($IN);
		}
		else if($product_id > 0)
		{
			$this->db->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".$product_id."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
		}
			$this->db->join("`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->where("A.`id_users`", $this->id_users);
		return TRUE;
	}

	protected function generate_price_html($prices_array, $short_price_access = FALSE)
	{
		if($short_price_access)
		{
			$str = $this->load->view('catalogue/products/prices_short', array('PRS_price_array' => $prices_array, 'PRS_price_access' => $short_price_access), TRUE);
			return $str;
		}

		$str = $this->load->view('catalogue/products/prices_detail', array('pr_prices_detail_array' => $prices_array), TRUE);
		return $str;
	}

	protected function generate_attributes_html($attributes_array)
	{
		$str = $this->load->view('catalogue/products/attributes_detail', $attributes_array, TRUE);
		return $str;
	}

	public function get_product_detail_comments($pr_id)
	{
		$comments_array = array();
		$count = $this->get_product_detail_comments_count($pr_id);
		if($count == 0) return array('comments' => $comments_array, 'pages' => FALSE);
		$page = 1;
		if((int) $this->variables->get_url_vars('product_comments_page')>0)
		{
			$page = (int) $this->variables->get_url_vars('product_comments_page');
		}

		$this->get_product_detail_comments_query($pr_id);
		$pages_data = $this->set_product_detail_comments_limit($count, $page);
		$pages_array = array();
		if($pages_data && count($pages_data)>0)
		{
			$this->load->helper('pages');
			$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('product_comments_page_lang', array('pr_id' => $pr_id, 'page' => '', 'lang' => $this->mlangs->lang_code)));
		}

		$comments_array = $this->build_product_detail_comments_data();
		return array('comments' => $comments_array, 'pages' => $pages_array);
	}

	protected function build_product_detail_comments_data()
	{
		return $this->db->get()->result_array();
	}

	protected function get_product_detail_comments_count($pr_id)
	{
		$this->db->select("COUNT(*) as COUNT")
				->from("`".self::PR_COMM."`")
				->where("`".self::ID_PR."`", $pr_id);
		$count = $this->db->get()->row_array();
		$count = $count['COUNT'];
		return $count;
	}

	protected function get_product_detail_comments_query($pr_id)
	{
		$this->db->select("`name`, `email`, `message`, `answer`, `admin_name`, `is_answer`, `create_date`, `update_date`, `id_langs`")
				->from("`".self::PR_COMM."`")
				->where("`".self::ID_PR."`", $pr_id)->where("`active`", 1)->order_by("`sort`", "DESC");
		return TRUE;
	}

	protected function set_product_detail_comments_limit($count, $page = 1)
	{
		$limit = $this->settings['reviews_count_to_page'];
		if($page > ceil($count/$limit))
		{
			redirect($this->router->build_url('category_page_lang', array('page' => ceil($count/$limit), 'category_url' => $this->variables->get_vars('category_url'), 'lang' => $this->mlangs->lang_code)) ,301);
		}

		if($page == 0)
		{
			$page = 1;
		}
		$offset = ($page-1)*$limit;
		$showcount = $limit;
		$this->db->limit($showcount, $offset);

		return array($count, $page, $limit);
	}

	public function add_product_detail_comment($pr_id)
	{
		if(!$this->check_isset_pr($pr_id)) return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => '<p>Error add comment!</p>'), TRUE));

		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Имя', 'required|min_length[4]');
		$this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
		$this->form_validation->set_rules('message', 'Комментарий', 'required|min_length[10]');

		if(!$this->form_validation->run())
		{
			return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => validation_errors()), TRUE));
		}

		$POST = $this->input->post();
		$data = array(self::ID_PR => $pr_id, 'name' => $POST['name'], 'email' => $POST['email'], 'message' => $POST['message'], 'new_comment' => 1, 'id_langs' => $this->mlangs->id_langs, self::ID_USERS => $this->id_users);
		if($this->settings['reviews_publication_immediately'] == 0)
		{
			$data['active'] = 0;
		}
		$id = $this->sql_add_data($data)->sql_update_date()->sql_using_user()->sql_save(self::PR_COMM);
		if($id)
		{
			$this->sql_add_data(array('sort' => $id))->sql_save(self::PR_COMM, $id);
			$this->products_detail_comment_send_mail_to_admin($pr_id, $data);

			if($this->settings['reviews_publication_immediately'] == 1)
			{
				return array('success' => 1, 'messages' => $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$this->lang->line('products_comments_success_add_comment').'</p>'), TRUE));
			}
			return array('success' => 1, 'messages' => $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$this->lang->line('products_comments_success_add_admin_confirm').'</p>'), TRUE));
		}
		return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => '<p>Error add comment!</p>'), TRUE));
	}

	protected function products_detail_comment_send_mail_to_admin($pr_id, $mail_data)
	{
		if($this->settings['reviews_admin_notice'] == 1)
		{
			$this->db->select("A.`sku`, A.`url_key`, B.`name`")
					->from("`".self::PR."` AS A")
					->join("`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`id_langs` = ".$this->mlangs->id_langs,
							"LEFT")
					->where("A.`".self::ID_PR."`", $pr_id)->limit(1);
			$product = $this->db->get()->row_array();
			$product_url = $pr_id;
			if($product['url_key'] != '')
			{
				$product_url = trim($product['url_key']);
			}
			$product['url'] = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));

			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;

			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['products_url'] = $product['url'];
			$data['products_name'] = $product['name'];
			$data['products_sku'] = $product['sku'];
			$data['name'] = $mail_data['name'];
			$data['email'] = $mail_data['email'];
			$data['message'] = $mail_data['message'];
			if($this->settings['reviews_admin_email'] != '')
			{
				$letter_html = $this->load->view('catalogue/products/letters/'.$this->mlangs->language.'/products_detail_new_comment', array('data' => $data), TRUE);
				$this->load->library('email');
				$this->email->initialize($config);
				$this->email->from('no-reply@'.$data['site'], $data['site']);
				$this->email->to($this->settings['reviews_admin_email']);
				$this->email->subject('New comment to product SKU '.$data['products_sku'].'.');
				$this->email->message($letter_html);
				$this->email->send();
				$this->email->clear();
			}
		}
		return TRUE;
	}

	public function get_product_detail_related_products($pr_id)
	{
		if($this->settings['related_on'])
		{
			return $this->build_product_detail_related_products_data($pr_id);
		}
		return FALSE;
	}

	protected function build_product_detail_related_products_data($pr_id)
	{
		$this->get_product_detail_related_products_query($pr_id);
		if(count($result = $this->db->get()->result_array()) > 0)
		{
			$related_products_array = array();
			$related_id = array();
			foreach($result as $ms)
			{
				$related_id[] = $ms['PR_REL_ID'];
			}
			$rand = FALSE;
			if($this->settings['related_random'] == 1) $rand = TRUE;
			$products = $this->get_products_by_id($related_id, $rand, TRUE);

			$related_products_array['related_products'] = $products['products'];
			$block_count = $this->settings['related_show_count'];
			$block = 0;
			$i = 1;
			foreach($products['products'] as $ms)
			{
				if($block_count <= $i)
				{
					$related_products_array['related_products_block'][$block][] = $ms;
					$i++;
				}
				else
				{
					$i=1;
					$block++;
				}
			}
			return $related_products_array;
		}
		return FALSE;
	}

	protected function get_product_detail_related_products_query($pr_id)
	{
		$this->db->select("`".self::ID_PR_REL."` AS PR_REL_ID")
				->from("`".self::PR_REL."`")
				->where("`".self::ID_PR."`", $pr_id);
		if($this->settings['related_random'] == 1) $this->db->order_by('RAND()');
		else $this->db->order_by('sort', 'DESC');
		if(($rel_count = $this->settings['related_count']) > 0) $this->db->limit($rel_count);
		return TRUE;
	}

	public function get_product_detail_similar_products($pr_id)
	{
		if($this->settings['similar_on'])
		{
			return $this->build_product_detail_similar_products_data($pr_id);
		}
		return FALSE;
	}

	protected function build_product_detail_similar_products_data($pr_id)
	{
		$this->get_product_detail_similar_products_query($pr_id);
		if(count($result = $this->db->get()->result_array()) > 0)
		{
			$similar_products_array = array();
			$similar_id = array();
			foreach($result as $ms)
			{
				$similar_id[] = $ms['PR_SIM_ID'];
			}
			$rand = FALSE;
			if($this->settings['similar_random'] == 1) $rand = TRUE;
			$products = $this->get_products_by_id($similar_id, $rand, TRUE);

			$similar_products_array['similar_products'] = $products['products'];
			$block_count = $this->settings['similar_show_count'];
			$block = 0;
			$i = 1;
			foreach($products['products'] as $ms)
			{
				if($block_count <= $i)
				{
					$similar_products_array['similar_products_block'][$block][] = $ms;
					$i++;
				}
				else
				{
					$i=1;
					$block++;
				}
			}
			return $similar_products_array;
		}
		return FALSE;
	}

	protected function get_product_detail_similar_products_query($pr_id)
	{
		$this->db->select("`".self::ID_PR_SIM."` AS PR_SIM_ID")
				->from("`".self::PR_SIM."`")
				->where("`".self::ID_PR."`", $pr_id);
		if($this->settings['similar_random'] == 1) $this->db->order_by('RAND()');
		else $this->db->order_by('sort', 'DESC');
		if(($rel_count = $this->settings['similar_count']) > 0) $this->db->limit($rel_count);
		return TRUE;
	}

	protected function build_product_detail_navigation($id, $pr_name)
	{
		$this->db->select("A.`".self::ID_CAT."`, CAT.`id_parent`, CAT.`url`, CAT.`level`, C.`name`")
				->from("`".self::PR_CAT."` AS A")
				->join(	"`".self::CAT."` AS CAT",
						"CAT.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
						"INNER")
				->join(	"`".self::CAT_DESC."` AS C",
						"C.`".self::ID_CAT."` = A.`".self::ID_CAT."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_PR."`", $id)->order_by("CAT.`level`")->order_by("CAT.`sort`");

		$res = $this->db->get()->result_array();
		$nav_array = array();
		if(count($res)>0)
		{
			$add_params = $this->variables->build_additional_url_params();
			foreach($res as $ms)
			{
				$url = $ms[self::ID_CAT];
				if(strlen($ms['url'])>2)
				{
					$url = $ms['url'];
				}

				$url = $this->router->build_url('category_lang', array('category_url' => $url, 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params));

				if($ms['level']>1)
				{
					if(isset($this->temp_nav_array[$ms['id_parent']]))
					{
						$tmp = $this->temp_nav_array[$ms['id_parent']];
						array_push($tmp, $ms + array('category_url' => $url));
						$this->temp_nav_array[$ms[self::ID_CAT]] = $tmp;
					}
					else
					{
						$this->temp_nav_array[$ms[self::ID_CAT]] = array($ms + array('category_url' => $url));
					}
				}
				else
				{
					$this->temp_nav_array[$ms[self::ID_CAT]] = array($ms + array('category_url' => $url));
				}
			}

			$tmp = $this->temp_nav_array;

			foreach($tmp as $key => $ms)
			{
				foreach($ms as $ar)
				{
					if(isset($this->temp_nav_array[$ar[self::ID_CAT]]) && $key != $ar[self::ID_CAT])
					{
						unset($this->temp_nav_array[$ar[self::ID_CAT]]);
					}
				}
			}

			$lim = FALSE;
			$ref = getenv("HTTP_REFERER");
			$pos = strpos($ref, $_SERVER['SERVER_NAME']);
			if($pos && $pos < 10)
			{
				$lim = TRUE;
			}

			$nav_array = array();
			arsort($this->temp_nav_array);
			foreach($this->temp_nav_array as $key => $ms)
			{
				foreach($ms as $ar)
				{
					$nav_array[$key][] = array($ar['category_url'], $ar['name']);
				}
				$nav_array[$key][] = array(FALSE, $pr_name);
				if($lim) break;
			}
			$this->variables->set_vars('navigation_array', array('navigation' => $nav_array));
		}
		else
		{
			$this->variables->set_vars('navigation_array', array('navigation' => array($nav_array)));
		}
	}

	public function check_isset_pr($pr_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $pr_id)->where("`".self::ID_USERS."`", $this->id_users)->where("`status`", 1);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 0) return FALSE;
		return TRUE;
	}

	public function check_isset_product($id, array $aditional = array())
	{
		$this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::PR."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_PR."`", $id)->limit(1);
		if(count($aditional)>0)
		{
			foreach($aditional as $key => $ms)
			{
				$this->db->where($key, $ms);
			}
		}
		$count = $this->db->get()->row_array();
		if($count['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function get_cart_products(array $cart)
	{
		if(count($cart)>0)
		{
			$cart_products_array = array();
			$pr_id = array();
			$price_id = array();
			$attr_id = array();
			$opt_id = array();
			foreach($cart as $ms)
			{
				$pr_id[$ms['id']] = $ms['id'];
				$price_id[$ms['options']['price']] = $ms['options']['price'];
				$attr_opt = $ms['options'];
				unset($attr_opt['options']['price']);
				foreach($attr_opt as $attr => $opt)
				{
					$attr_id[$attr] = $attr;
					$opt_id[$opt] = $opt;
				}
			}

			$this->get_cart_products_query($pr_id);
			$products_temp_array = $this->db->get()->result_array();
			$products_array = $this->build_cart_products_data($products_temp_array);

			$this->get_cart_products_prices_query($price_id);
			$prices_temp_array = $this->db->get()->result_array();
			$prices_array = $this->build_cart_products_prices_data($prices_temp_array);

			$attr_temp_array = $this->build_cart_products_attr_opt_data($attr_id, $opt_id);

			foreach($cart as $key => $ms)
			{
				if(isset($products_array[$ms['id']]) && isset($prices_array[$ms['options']['price']]))
				{
					$cart_products_array[$key]['cart'] = $ms;
					$cart_products_array[$key]['products'] = $products_array[$ms['id']];
					$cart_products_array[$key]['products_prices'] = $prices_array[$ms['options']['price']];
					if($prices_array[$ms['options']['price']]['special_price_rate'])
					{
						$cart_products_array[$key]['products_prices']['total_price_string'] = number_format($prices_array[$ms['options']['price']]['special_price_rate']*$ms['qty'], 2, ',', ' ');
						$cart_products_array[$key]['products_prices']['total_price'] = $prices_array[$ms['options']['price']]['special_price_rate']*$ms['qty'];
						$cart_products_array[$key]['products_prices']['original_total_price'] = $prices_array[$ms['options']['price']]['original_special_price']*$ms['qty'];
					}
					else
					{
						$cart_products_array[$key]['products_prices']['total_price_string'] = number_format($prices_array[$ms['options']['price']]['price_rate']*$ms['qty'], 2, ',', ' ');
						$cart_products_array[$key]['products_prices']['total_price'] = $prices_array[$ms['options']['price']]['price_rate']*$ms['qty'];
						$cart_products_array[$key]['products_prices']['original_total_price'] = $prices_array[$ms['options']['price']]['original_price']*$ms['qty'];
					}
					$cart_products_array[$key]['products_attributes'] = array();
					$attr_opt = $ms['options'];
					unset($attr_opt['options']['price']);
					foreach($attr_opt as $attr => $opt)
					{
						if(isset($attr_temp_array[$attr][$opt]))
						{
							$cart_products_array[$key]['products_attributes'][] = $attr_temp_array[$attr][$opt];
						}
					}
				}
				else
				{
					$data = array(
						'rowid' => $key,
						'qty'	=> 0
					);
					$this->cart->update($data);
				}
			}
			return $cart_products_array;
		}
		return FALSE;
	}

	protected function build_cart_products_data($products_temp_array)
	{
		$this->load->model('site_settings/msite_settings');
		$domain = $this->msite_settings->get_domain_settings();
		$domain = $domain['domain'];
		$products_array = array();
		foreach($products_temp_array as $ms)
		{
			$product_url = $ms['ID'];
			if($ms['url_key'] != '')
			{
				$product_url = trim($ms['url_key']);
			}

			$url = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));

			$products_array[$ms['ID']] = $ms;
			if($ms['image'] != NULL)
			{
				$alb_seg = '';
				if($ms['ALB_ID'] != NULL)
				{
					$alb_seg = '/'.$ms['ALB_ID'];
				}
				$products_array[$ms['ID']] += array('timage' => "http://".$domain.$this->img_path.$ms['ID'].$alb_seg.'/thumb_'.$ms['image'], 'bimage' => "http://".$domain.$this->img_path.$ms['ID'].$alb_seg.'/'.$ms['image']);
			}
			$products_array[$ms['ID']] += array('detail_url' => $url);
		}
		return $products_array;
	}

	protected function get_cart_products_query(array $products_array)
	{
		$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		if($wh_settings['wh_on'] == 1 && $wh_settings['wh_active'] == 1)
		{
			$this->db->select("`".self::ID_WH."`")
					->from("`".self::WH."`")
					->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->where("`i_s_wh`", 1)->limit(1);
			$SHOP_WH = $this->db->get()->row_array();
			if(count($SHOP_WH) > 0)
			{
				$wh_id = $SHOP_WH[self::ID_WH];
				$this->db
					->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`bestseller`, A.`sale`, A.`new`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`,
							IF(A.`in_stock` > 0, IF(WHP.`qty` > 0, 1, 0), 0) AS `in_stock`, WHP.`qty` AS qty, IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID")
					->from("`".self::PR."` AS A")
					->join( "`".self::WH_PR."` AS WHP",
							"WHP.`".self::ID_PR."` = A.`".self::ID_PR."` && WHP.`".self::ID_WH."` = ".$wh_id,
							"LEFT")
					->join(	"`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->join(	"`".self::PR_IMG."` AS IMG",
							"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
							"LEFT")
					->where_in("A.`".self::ID_PR."`", $products_array)->where("A.`".self::ID_USERS."`", $this->id_users);
				return TRUE;
			}
		}
		$this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`, B.`name`, B.`short_description`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`, IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				->where_in("A.`".self::ID_PR."`", $products_array)->where("A.`".self::ID_USERS."`", $this->id_users);
		return TRUE;
	}

	protected function build_cart_products_prices_data($prices_temp_array)
	{
		$prices_array['prices_array'] = array();
		$prices_array['prices'] = FALSE;
		$prices_array['error_access'] = FALSE;

		foreach($prices_temp_array as $ms)
		{
			$ms['price_visible'] = TRUE;
			if($ms['price_visible_rules']>0)
			{
				if($ms['price_visible_rules'] == 1)
				{
					if(($this->session->userdata('customer_id')) == FALSE) $ms['price_visible'] = FALSE;
				}
				else if($ms['price_visible_rules'] == 2)
				{
					if(($this->session->userdata('customer_id')) == FALSE)
					{
						$ms['price_visible'] = FALSE;
					}
					else
					{
						$customer = $this->session->userdata('CUSTOMER');
						if($customer['have_m_u_types'] == 1)
						{
							$ms['price_visible'] = FALSE;
							$t_m_u_types = explode(',', $ms['price_m_u_types']);
							$customer_m_u_types = $customer['m_u_types'];
							foreach($t_m_u_types as $ms1)
							{
								if(isset($customer_m_u_types[$ms1]))
								{
									$ms['price_visible'] = TRUE;
									break;
								}
							}
						}
						else
						{
							$ms['price_visible'] = FALSE;
						}
					}
				}
			}
			if($ms['price_visible'])
			{
				$original_special_price = $ms['original_special_price'];
				$special_price = $ms['special_price_rate'];
				$special_price_to_date = FALSE;
				if((float) $special_price > 0)
				{
					$today = mktime();
					$special_price_to_date = FALSE;
					if($ms['special_price_from'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_from']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						if($today < $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
						}
					}
					if($ms['special_price_to'] != NULL)
					{
						$spfrom = explode("-", $ms['special_price_to']);
						$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
						$special_price_to_date = $ms['special_price_to'];
						if($today > $spfrom)
						{
							$original_special_price = FALSE;
							$special_price = FALSE;
							$special_price_to_date = FALSE;
						}
					}
				}
				else
				{
					$original_special_price = FALSE;
					$special_price = FALSE;
				}
				$ms['original_special_price'] = $original_special_price;
				$ms['special_price_rate'] = $special_price;
				$ms['special_price_to_date'] = $special_price_to_date;

				$ms['price_attributes_array']['show_attributes'] = $ms['show_attributes'];
				$ms['price_attributes_array']['id_attributes'] = array();
				if($ms['show_attributes'] == 2)
				{
					foreach(explode(',', $ms['id_attributes']) as $ms1)
					{
						if($ms1 != '')
						{
							$ms['price_attributes_array']['id_attributes'][$ms1] = $ms1;
						}
					}
				}
				$ms['original_price_string'] = number_format($ms['original_price'], 2, ',', ' ');
				$ms['price_rate_string'] = number_format($ms['price_rate'], 2, ',', ' ');

				$ms['cart_price'] = $ms['original_price'];
				$ms['cart_price_rate_string'] = $ms['price_rate_string'];
				if($special_price)
				{
					$ms['original_special_price_string'] = number_format($ms['original_special_price'], 2, ',', ' ');
					$ms['special_price_rate_string'] = number_format($ms['special_price_rate'], 2, ',', ' ');

					$ms['cart_price'] = $ms['original_special_price'];
					$ms['cart_price_rate_string'] = $ms['special_price_rate_string'];
				}
				if($ms['price_name'] != '') $ms['price_name'] .= ' ';
				$prices_array[$ms['PRICE_ID']] = $ms;
			}
		}
		return $prices_array;
	}

	protected function get_cart_products_prices_query(array $prices_array)
	{
		$this->load->model('catalogue/mcurrency');
		if(!$currency = $this->mcurrency->get_current_currency()) return FALSE;

		$this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS `PRICE_ID`, PRICE.`".self::ID_PR."` AS `PR_ID`,
				PRICE.`price` AS `original_price` ,(PRICE.`price` * ".$currency['rate'].") AS `price_rate`, PRICE.`special_price` AS `original_special_price`, (PRICE.`special_price` * ".$currency['rate'].") AS `special_price_rate`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`visible_rules` AS `price_visible_rules`, PRICE.`m_u_types` AS `price_m_u_types`, PRICE.`show_attributes`, PRICE.`id_attributes`, PRICE.`min_qty`, PRICE.`real_qty`, PRICE.`alias` AS price_alias, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS `price_description`,
				'".$currency['name']."' AS `currency_name`")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join( "`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where_in("PRICE.`".self::ID_PR_PRICE."`", $prices_array)->where("PRICE.`show_in_detail`", 1);

		return TRUE;
	}

	protected function build_cart_products_attr_opt_data(array $attr_array, array $opt_array)
	{
		$pr_attr_array = array();
		if(count($attr_array) > 0)
		{
			$this->get_cart_products_attr_opt_query($attr_array, $opt_array);
			$temp_array = $this->db->get()->result_array();
			foreach($temp_array as $ms)
			{
				$pr_attr_array[$ms['ID_ATTR']][$ms['ID_OP']] = $ms;
			}
		}
		return $pr_attr_array;
	}

	protected function get_cart_products_attr_opt_query(array $attr_array, array $opt_array)
	{
		$this->db
			->select("A.`".self::ID_ATTR."` AS ID_ATTR, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A")
			->join(	"`".self::OP."` AS B",
					"B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
					"INNER")
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where_in("A.`".self::ID_ATTR."`", $attr_array)->where_in("B.`".self::ID_OP."`", $opt_array);
		return TRUE;
	}

	public function get_product_price($pr_id, $price_id)
	{
		if(($price_id = intval($price_id)) == 0) return FALSE;
		$this->get_product_detail_prices_query($pr_id, $price_id);
		$price = $this->db->get()->row_array();
		if(count($price) == 0) return FALSE;
		$price_array = $this->build_product_detail_prices_data(array($price));
		if($price_array['error_access']) return FALSE;

		return current($price_array['prices_array']);
	}

	public function get_product_attributes_and_options($id)
	{
		$this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($id))
		{
			$IN = '';
			foreach($id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$this->db->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"inner");
			unset($IN);
		}
		else if(intval($id)>0)
		{
			$this->db->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".intval($id)."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"inner");
		}
		$this->db
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->where("A.`id_users`", $this->id_users);
		$result = $this->db->get()->result_array();
		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']][$ms['ID_OP']] = $ms;
		}
		unset($result);
		return $array;
	}

	public function get_favorites_products(array $favorites)
	{
		$pr_array_id = array();
		foreach($favorites as $ms)
		{
			$pr_array_id[$ms['id']] = $ms['id'];
		}
		$pr_array = $this->get_products_by_id($pr_array_id);
		$pr_array = $pr_array['products'];
		foreach($pr_array as $key => $ms)
		{
			$pr_array[$key] = $ms + array('rowid' => md5($key));
		}
		return $pr_array;
	}
}
?>