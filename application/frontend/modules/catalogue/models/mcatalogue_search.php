<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;

class Mcatalogue_search extends AG_Model
{
	const SPHINX_INDEX = SPHINX_INDEX;

	public function search_products($search_string)
	{
		$sphinxQL = new Connection();
		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);

		$match_search_string = $search_string;

		$select = "`id_m_c_products`, `in_stock` AS order_in_stock, weight()";
		$this->load->model('warehouse/mwarehouses');
		if($wh_id = $this->mwarehouses->get_shop_wh_id())
		{
			$select = "`id_m_c_products`, IF (qty, in_stock, 0) AS order_in_stock, weight()";
		}

		$search_query = "SELECT ".$select."
		FROM `".self::SPHINX_INDEX."`
		WHERE `id_users` = ".$this->id_users." AND `status` = 1 AND MATCH('(".$match_search_string.") | (@sku *".$match_search_string."*)')
		GROUP BY `id_m_c_products`
		ORDER BY `order_in_stock` DESC, weight() DESC
		LIMIT 0, 2000
		OPTION max_matches = 2000,
		field_weights = (name = 100, short_description = 30, full_description = 20, seo_title = 50, seo_description = 30, seo_keywords = 100, sku = 200)";


		$sphinx_result = $sphinxQL->query($search_query);
		$search_count = count($sphinx_result);

		$this->load->model('catalogue/mproducts');
		$products_settings = $this->mproducts->get_settings();
		$search_limit = $products_settings['products_count_to_page'];

		list($pages, $limit_data) = $this->set_limit($search_count, $search_limit, $search_string);

		$products_array = array();
		$products_id = array();

		$seo = array('seo_title' => $match_search_string, 'seo_description' => $match_search_string);
		$this->template->set_TDK($seo);

		if($search_count > 0)
		{
			for($i = $limit_data[0]; $i < $limit_data[1]; $i++)
			{
				$products_id[$sphinx_result[$i]['id_m_c_products']] = $sphinx_result[$i]['id_m_c_products'];
			}

			$products_temp = $this->mproducts->get_search_products_collection($products_id);
			foreach($products_id as $key => $ms)
			{
				if(isset($products_temp['products'][$key])) $products_array[] = $products_temp['products'][$key];
			}
		}
		return array('products' => $products_array, 'pages' => $pages, 'search_keywords' => $match_search_string);
	}

	public function set_limit($count, $limit, $search_string)
	{
		$page = 1;
		if($this->variables->get_url_vars('page') == 1) redirect($this->router->build_url('search_data_lang', array('search_string' => $search_string, 'lang' => $this->langs->id_langs)) ,301);
		if((int) $this->variables->get_url_vars('page')>0)
		{
			$page = (int) $this->variables->get_url_vars('page');
		}

		if($page > 1 && $page > ceil($count/$limit))
		{
			redirect($this->router->build_url('search_data_page_lang', array('page' => ceil($count/$limit), 'search_string' => $search_string, 'lang' => $this->langs->id_langs)) ,301);
		}

		if($page == 0)
		{
			$page = 1;
		}
		$from = ($page-1)*$limit;
		$to = $from + $limit;
		if($count < $to) $to = $count;

		return array($this->build_pagination_array(array($count, $page, $limit), $search_string), array($from, $to));
	}

	public function build_pagination_array($pages_data, $search_string)
	{
		$pages_array = array();
		if($pages_data && count($pages_data)>0)
		{
			$this->load->helper('pages');
			$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('search_data_page_lang', array('search_string' => $search_string, 'page' => '', 'lang' => $this->mlangs->lang_code)));
		}
		return $pages_array;
	}
}
?>