<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;

class Mtypes extends AG_Model
{
	const TYPES 		= 'm_c_products_types';
	const ID_TYPES 		= 'id_m_c_products_types';
	const TYPES_DESC 	= 'm_c_products_types_description';
	
	const PROP 		= 'm_c_products_properties';
	const ID_PROP 	= 'id_m_c_products_properties';
	const PROP_DESC = 'm_c_products_properties_description';
	
	const CAT 		= 'm_c_categories';
	const ID_CAT 	= 'id_m_c_categories';
	
	const PR_CAT 	= 'm_c_productsNcategories';
	const PR_TYPES 	= 'm_c_productsNtypes';

	const PR_TYPES_SET 			= 'm_c_products_types_set';
	const ID_PR_TYPES_SET 		= 'id_m_c_products_types_set';

	const PR_TYPES_SET_ALIAS 	= 'm_c_products_types_set_alias';

	const PR_TYPES_SET_DESC 	= 'm_c_products_types_set_description';
	const ID_PR_TYPES_SET_DESC 	= 'id_m_c_products_types_set_description';

	const PR 	= 'm_c_products';
	const ID_PR = 'id_m_c_products';

	const IMG_FOLDER = '/media/catalogue/products_properties/';

	const SPHINX_INDEX = SPHINX_INDEX;

	protected $img_path = FALSE;
	public $filters_options = array();
	public $filters_additional_options = array();
	public $filters_price_options = array();

	private $_was_init = FALSE;
	private $sphinxQL;
	
	public function __construct()
	{
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
		$this->sphinxQL = new Connection();
		$this->sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);
		parent::__construct();
	}
	
	public function _init()
	{
		if($this->_was_init) return FALSE;
		$category_id = $this->variables->get_vars('category_id');
		$category_url = $this->variables->get_vars('category_url');
		if(!$category_url) show_404();
		if($this->input->post('products_filters_clear'))
		{
			$this->clear_filters();
			redirect($this->router->build_url('category_sort_lang', array('category_url' => $category_url, 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}

		if(!$this->category_filters())
		{
			$product_filters_data = $this->session->userdata('product_filters_data');
			$product_filters_additional_data = $this->session->userdata('product_filters_additional_data');
			$product_filters_price_data = $this->session->userdata('product_filters_price_data');

			$product_filters_data = isset($product_filters_data[$category_id]) ? $product_filters_data[$category_id] : array();
			$product_filters_additional_data = isset($product_filters_additional_data[$category_id]) ? $product_filters_additional_data[$category_id] : array();
			$product_filters_price_data = isset($product_filters_price_data[$category_id]) ? $product_filters_price_data[$category_id] : array();

			$this->filters_options = $product_filters_data;
			$this->filters_additional_options = $product_filters_additional_data;
			$this->filters_price_options = $product_filters_price_data;
		}

		if(!$this->variables->get_url_vars('filters_params'))
		{
			if(count($this->filters_options)>0 || count($this->filters_additional_options)>0 || count($this->filters_price_options)>0)
			{
				$filters_url_string = $this->build_filters_url();

				if(($types_set_data = $this->mtypes->get_types_set()) && strlen($types_set_data['url']) > 0)
				{
					redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $category_url, 'filters_params' => rawurldecode($types_set_data['url']), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
				}
				else
				{
					redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $category_url, 'filters_params' => $filters_url_string, 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
				}
			}
		}
		return TRUE;
	}
	
	protected function get_filter_option($opt_id)
	{
		if(isset($this->filters_options[$opt_id]))
		{
			return $this->filters_options[$opt_id];
		}
		return array();
	}

	protected function build_qty_in_string($filters_array, $case_key = FALSE)
	{
		if(!$case_key)
		{
			if(count($filters_array) == 0) return FALSE;
			$JOIN = "";
			foreach($filters_array as $key => $ms)
			{
				$JOIN .= "
				INNER JOIN `".self::PR_TYPES."` AS JOIN".$key."
				ON JOIN".$key.".`".self::ID_PROP."` IN (".$ms.") && JOIN".$key.".`".self::ID_PR."` = QTY.`".self::ID_PR."`
				";
			}
			return $JOIN;
		}

		if(isset($filters_array[$case_key]))
		{
			unset($filters_array[$case_key]);
		}
		if(count($filters_array) == 0) return FALSE;

		$JOIN = "";
		foreach($filters_array as $key => $ms)
		{
			$JOIN .= "
			INNER JOIN `".self::PR_TYPES."` AS JOIN".$key."
			ON JOIN".$key.".`".self::ID_PROP."` IN (".$ms.") && JOIN".$key.".`".self::ID_PR."` = QTY.`".self::ID_PR."`
			";
		}
		return $JOIN;
	}

	protected function build_filters_qty_select($active_filters, $sphinx_products)
	{
		$PR_IN = implode(',', $sphinx_products);

		if(!is_array($active_filters) || count($active_filters) == 0)
		{
			return "(
			SELECT COUNT(*) FROM `".self::PR_TYPES."` AS QTY
			WHERE QTY.`".self::ID_PR."` IN (".$PR_IN.") && QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`)";
		}

		$temp_filters = array();
		foreach($active_filters as $key => $ms)
		{
			$temp_filters[$key] = implode(',', $ms);
		}

		$CASE = 'CASE ';
		foreach($temp_filters as $key => $ms)
		{
			IF($JOIN = $this->build_qty_in_string($temp_filters, $key))
			{
				$CASE .= "
				WHEN A.`".self::ID_TYPES."` = ".$key." THEN
				(
					SELECT COUNT(DISTINCT QTY.`".self::ID_PR."`) FROM `".self::PR_TYPES."` AS QTY
					".$JOIN."
					WHERE QTY.`".self::ID_PR."` IN (".$PR_IN.") && QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`
				)
				";
			}
			else
			{
				$CASE .= "
				WHEN A.`".self::ID_TYPES."` = ".$key." THEN
				(
					SELECT COUNT(DISTINCT QTY.`".self::ID_PR."`) FROM `".self::PR_TYPES."` AS QTY
					WHERE QTY.`".self::ID_PR."` IN (".$PR_IN.") && QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`
				)
				";
			}
		}
		$JOIN = $this->build_qty_in_string($temp_filters);
		$CASE .= "
		ELSE
		(
			SELECT COUNT(DISTINCT QTY.`".self::ID_PR."`) FROM `".self::PR_TYPES."` AS QTY
			".$JOIN."
			WHERE QTY.`".self::ID_PR."` IN (".$PR_IN.") && QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`
		)
		";

		$CASE .= "
		END
		";
		return $CASE;
	}

	protected function build_filters_qty_query($category_id, $active_filters, $sphinx_products)
	{
		$select_qty = $this->build_filters_qty_select($active_filters, $sphinx_products);

		$this->db->select("A.`".self::ID_TYPES."`, B.`".self::ID_PROP."`, ".$select_qty." AS pr_qty")
			 ->from("`".self::PROP."` AS B")
			 ->join("`".self::TYPES."` AS A",
				 "B.`".self::ID_TYPES."` = A.`".self::ID_TYPES."`",
				 "LEFT")
			 ->where("A.`".self::ID_USERS."`", $this->id_users)
			 ->where("A.`active`", 1)->where("B.`active`", 1)
			 ->where("
		EXISTS(
		SELECT EA.`".self::ID_TYPES."`, EA.`".self::ID_PROP."` FROM `".self::PR_TYPES."` AS EA
		INNER JOIN `".self::PR_CAT."` AS EB
			ON EB.`".self::ID_CAT."` = ".$category_id." && EB.`".self::ID_PR."` = EA.`".self::ID_PR."`
		WHERE EA.`".self::ID_PROP."` IS NOT NULL && EA.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && EA.`".self::ID_PROP."` = B.`".self::ID_PROP."`
		)
		", NULL, FALSE);
	}

	protected function build_sphinx_filters_string($active_filters)
	{
		if(!is_array($active_filters)) return '';
		$prop_string = '';
		if(count($active_filters) > 0)
		{
			$options_array = array();
			foreach($active_filters as $ms)
			{
				$options_array[] = self::ID_PROP.' IN ('.implode(',', $ms).')';
			}
			$prop_string = ' AND '.implode('AND ', $options_array);
		}
		return $prop_string;
	}

	protected function build_sphinx_filters_additional_string($additional_filters)
	{
		if(!is_array($additional_filters)) return '';
		$additional_string = '';
		foreach($additional_filters as $key => $ms)
		{
			$additional_string .= ' AND `'.$key.'` = 1';
		}
		return $additional_string;
	}

	protected function build_sphinx_filters_price_string($price_filters)
	{
		$price_string = '';
		if(isset($price_filters['price_from']) && $price_filters['price_from'] > 0) $price_string .= ' AND `price` >= '.$price_filters['price_from'];
		if(isset($price_filters['price_to']) && $price_filters['price_to'] > 0) $price_string .= ' AND `price` <= '.$price_filters['price_to'];
		return $price_string;
	}

	protected function get_sphinx_additional_qty($category_id, $sphinx_where, $active_filters)
	{
		$additional_prop['sale'] = 0;
		$additional_prop['bestseller'] = 0;
		$additional_prop['new'] = 0;

		foreach($additional_prop as $key => $ms)
		{
			$query = "SELECT count(*)
				FROM `".self::SPHINX_INDEX."`
				WHERE `".self::ID_USERS."` = ".$this->id_users." AND `status` = 1
				AND `".$key."` = 1
				AND `id_m_c_categories` = ".$category_id.$sphinx_where."
				LIMIT 1
			";
			$sphinx_result = $this->sphinxQL->query($query);
			if($active_filters)
			{
				if(count($sphinx_result) > 0) $additional_prop[$key] = isset($active_filters[$key]) ? $sphinx_result[0]['count(*)'] : '+'.$sphinx_result[0]['count(*)'];
			}
			else
			{
				if(count($sphinx_result) > 0) $additional_prop[$key] = $sphinx_result[0]['count(*)'];
			}
		}
		return $additional_prop;
	}

	protected function get_sphinx_price_qty($category_id, $sphinx_where)
	{
		$price_prop = 0;
		$query = "SELECT count(*)
			FROM `".self::SPHINX_INDEX."`
			WHERE `".self::ID_USERS."` = ".$this->id_users." AND `status` = 1 AND `id_langs` = ".$this->mlangs->id_langs."
			AND `id_m_c_categories` = ".$category_id.$sphinx_where."
			LIMIT 1
		";
		$sphinx_result = $this->sphinxQL->query($query);
		if(count($sphinx_result) > 0)
		{
			$price_prop = $sphinx_result[0]['count(*)'];
		}
		return $price_prop;
	}

	protected function get_sphinx_products_for_qty($category_id, $sphinx_where, $order_by = '')
	{
		$sphinx_products = array();
		$query = "SELECT `id_m_c_products`
				FROM `".self::SPHINX_INDEX."`
				WHERE `id_users` = ".$this->id_users." AND `status` = 1 AND `id_langs` = ".$this->mlangs->id_langs."
				AND `id_m_c_categories` = ".$category_id.$sphinx_where." ".$order_by."
				LIMIT 0, 2000
			";

		$sphinx_result = $this->sphinxQL->query($query);
		if(count($sphinx_result) > 0)
		{
			foreach($sphinx_result as $ms)
			{
				$sphinx_products[] = $ms[self::ID_PR];
			}
		}
		else
		{
			$sphinx_products[] = 0;
		}
		return $sphinx_products;
	}

	public function get_ajax_update_products_qty($category_id)
	{
		$prop_string = $this->build_sphinx_filters_string($this->input->post('products_filters'));

		$price_post = $this->input->post('products_filters_price');
		$this->load->model('catalogue/mcurrency');
		$current_currency = $this->mcurrency->get_current_currency();

		if(isset($price_post['price_from'])) $price_post['price_from'] = $price_post['price_from'] / $current_currency['rate'];
		if(isset($price_post['price_to'])) $price_post['price_to'] = $price_post['price_to'] / $current_currency['rate'];
		$price_string = $this->build_sphinx_filters_price_string($price_post);

		$additional_string = $this->build_sphinx_filters_additional_string($this->input->post('products_filters_additional'));

		$additional_prop = $this->get_sphinx_additional_qty($category_id, $prop_string.$price_string, $this->input->post('products_filters_additional'));
		$price_prop = $this->get_sphinx_price_qty($category_id, $prop_string.$price_string.$additional_string);

		$sphinx_products = $this->get_sphinx_products_for_qty($category_id, $additional_string.$price_string);

		$this->build_filters_qty_query($category_id, $this->input->post('products_filters'), $sphinx_products);

		$sel_prop = $this->input->post('products_filters');
		$prop = array();
		foreach($this->db->get()->result_array() as $ms)
		{
			if(isset($sel_prop[$ms[self::ID_TYPES]][$ms[self::ID_PROP]])) $prop[$ms[self::ID_TYPES]][$ms[self::ID_PROP]] = $ms['pr_qty'];
			else if(isset($sel_prop[$ms[self::ID_TYPES]])) $prop[$ms[self::ID_TYPES]][$ms[self::ID_PROP]] = '+'.$ms['pr_qty'];
			else $prop[$ms[self::ID_TYPES]][$ms[self::ID_PROP]] = $ms['pr_qty'];
		}
		return array('additional_prop' => $additional_prop, 'price_prop' => $price_prop, 'prop' => $prop);
	}

	protected function get_category_types_query($category_id)
	{
		$this->db->select("A.`".self::ID_TYPES."` AS ID, AD.`name` AS tname, AD.`seo_title` AS tseo_title, AD.`seo_description` AS tseo_description, AD.`seo_keywords` AS tseo_keywords,  A.`type_kind`, B.`id_color`, B.`image`, B.`".self::ID_PROP."` AS PID,
				BD.`name` AS pname, BD.`seo_title` AS pseo_title, BD.`seo_description` AS pseo_description, BD.`seo_keywords` AS pseo_keywords,
				(SELECT COUNT(*) FROM `".self::PR_TYPES."` AS QTY
				INNER JOIN `".self::PR_CAT."` AS Q_PRC
				ON Q_PRC.`".self::ID_CAT."` = '".$category_id."' && Q_PRC.`".self::ID_PR."` = QTY.`".self::ID_PR."`
				WHERE
				QTY.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` &&
				QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`) AS pr_qty ")
				 ->from("`".self::TYPES."` AS A")
				 ->join("`".self::TYPES_DESC."` AS AD",
					 "AD.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && AD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
					 "LEFT")
				 ->join("`".self::PROP."` AS B",
					 "B.`".self::ID_TYPES."` = A.`".self::ID_TYPES."`",
					 "LEFT")
				 ->join("`".self::PROP_DESC."` AS BD",
					 "BD.`".self::ID_PROP."` = B.`".self::ID_PROP."` && BD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
					 "LEFT")
				 ->where("A.`".self::ID_USERS."`", $this->id_users)
				 ->where("A.`active`", 1)->where("B.`active`", 1)
				 ->where("
			EXISTS(
			SELECT EA.`".self::ID_TYPES."`, EA.`".self::ID_PROP."` FROM `".self::PR_TYPES."` AS EA
			INNER JOIN `".self::PR_CAT."` AS EB
				ON EB.`".self::ID_CAT."` = ".$category_id." && EB.`".self::ID_PR."` = EA.`".self::ID_PR."`
			WHERE EA.`".self::ID_PROP."` IS NOT NULL && EA.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && EA.`".self::ID_PROP."` = B.`".self::ID_PROP."`
			)
			", NULL, FALSE)
			->order_by("A.`sort`, B.`sort`");
		return TRUE;
	}

	protected function get_category_types_additional($category_id)
	{
		$prop_string = $this->build_sphinx_filters_string($this->filters_options);
		$price_string = $this->build_sphinx_filters_price_string($this->filters_price_options);
		$qty = $this->get_sphinx_additional_qty($category_id, $prop_string.$price_string, $this->filters_additional_options);

		$additional_types['sale'] = array('pname' => $this->lang->line('products_sale'), 'pr_qty' => $qty['sale']);
		$additional_types['bestseller'] = array('pname' => $this->lang->line('products_bestseller'), 'pr_qty' => $qty['bestseller']);
		$additional_types['new'] = array('pname' => $this->lang->line('products_new'), 'pr_qty' => $qty['new']);
		//$additional_types['action'] = array('pname' => $this->lang->line('products_action'), 'pr_qty' => $qty['action']);

		return $additional_types;
	}

	protected function get_category_types_price($category_id)
	{
		$price_types = array();
		$price_types['price_from'] = array('pname' => $this->lang->line('products_from'), 'interval_price' => 0, 'filter_price' => 0);
		$price_types['price_to'] = array('pname' => $this->lang->line('products_to'), 'interval_price' => 0, 'filter_price' => 0);
		$pr_qty = 0;

		$this->load->model('catalogue/mcurrency');
		$current_currency = $this->mcurrency->get_current_currency();
		$currency_name = $current_currency['name'];

		$prop_string = $this->build_sphinx_filters_string($this->filters_options);
		$additional_string = $this->build_sphinx_filters_additional_string($this->filters_additional_options);
		$price_string = $this->build_sphinx_filters_price_string($this->filters_price_options);

		$query = "SELECT MIN(`price`) AS min_price, MAX(`price`) AS max_price
		FROM `".self::SPHINX_INDEX."`
		WHERE `".self::ID_USERS."` = ".$this->id_users." AND `status` = 1
		AND `id_m_c_categories` = ".$category_id.' AND `price` > 0 '.$prop_string.$additional_string."
		LIMIT 1
		";
		$sphinx_result = $this->sphinxQL->query($query);
		if(count($sphinx_result) > 0)
		{
			$pr_qty = $this->get_sphinx_price_qty($category_id, $prop_string.$additional_string.$price_string);

			$price_types['price_from']['interval_price'] = floor($sphinx_result[0]['min_price'] * $current_currency['rate']);
			$price_types['price_to']['interval_price'] = ceil($sphinx_result[0]['max_price'] * $current_currency['rate']);

			$price_types['price_from']['filter_price'] = isset($this->filters_price_options['price_from']) ? floor($this->filters_price_options['price_from'] * $current_currency['rate']) : floor($sphinx_result[0]['min_price'] * $current_currency['rate']);
			$price_types['price_to']['filter_price'] = isset($this->filters_price_options['price_to']) ? ceil($this->filters_price_options['price_to'] * $current_currency['rate']) : ceil($sphinx_result[0]['max_price'] * $current_currency['rate']);
		}
		return array(array('currency_name' => $currency_name, 'pr_qty' => $pr_qty), $price_types);
	}

	public function get_category_types_array($category_id = FALSE)
	{
		if(!$category_id) $category_id = $this->variables->get_vars('category_id');
		if(!$category_id) return FALSE;
		$select_array = array();
		$options_array = array();
		$tkind = array();
		$options_active = array();
		$category_url = $this->variables->get_url_vars('category_url');

		$additional_types = $this->get_category_types_additional($category_id);
		$select_array['filters_additionally'] = $this->lang->line('products_filters_additionally');
		$options_array['filters_additionally'] = $additional_types;
		$tkind['filters_additionally'] = 'additional';
		$options_active['filters_additionally'] = $this->filters_additional_options;

		list($type_array, $price_types) = $this->get_category_types_price($category_id);
		$select_array['filters_price'] = $type_array + array('tname' => $this->lang->line('products_filters_price'));
		$options_array['filters_price'] = $price_types;
		$tkind['filters_price'] = 'price';
		$options_active['filters_price'] = $this->filters_price_options;

		$this->get_category_types_query($category_id);
		$category_types = $this->db->get()->result_array();
		if(count($this->filters_options) > 0 || count($this->filters_price_options) > 0 || count($this->filters_additional_options) > 0)
		{
			$price_string = $this->build_sphinx_filters_price_string($this->filters_price_options);
			$additional_string = $this->build_sphinx_filters_additional_string($this->filters_additional_options);
			$sphinx_products = $this->get_sphinx_products_for_qty($category_id, $price_string.$additional_string);
			$this->build_filters_qty_query($category_id, $this->filters_options, $sphinx_products);
			$prop_pr_qty = array();

			foreach($this->db->get()->result_array() as $ms)
			{
				$prop_pr_qty[$ms[self::ID_TYPES]][$ms[self::ID_PROP]] = $ms['pr_qty'];
			}
		}

		if(count($category_types) > 0)
		{
			$active_filters = array();
			foreach($category_types as $ms)
			{
				$select_array[$ms['ID']] = $ms['tname'];
				$options_array[$ms['ID']][$ms['PID']]['pname'] = $ms['pname'];

				if(isset($prop_pr_qty))
				{
					if(isset($this->filters_options[$ms['ID']][$ms['PID']]))
					{
						$options_array[$ms['ID']][$ms['PID']]['pr_qty'] = $prop_pr_qty[$ms['ID']][$ms['PID']];
					}
					else if(isset($this->filters_options[$ms['ID']]))
					{
						$options_array[$ms['ID']][$ms['PID']]['pr_qty'] = $prop_pr_qty[$ms['ID']][$ms['PID']] >0 ? '+'.$prop_pr_qty[$ms['ID']][$ms['PID']] : $prop_pr_qty[$ms['ID']][$ms['PID']];
					}
					else
					{
						$options_array[$ms['ID']][$ms['PID']]['pr_qty'] = $prop_pr_qty[$ms['ID']][$ms['PID']];
					}
				}
				else
				{
					$options_array[$ms['ID']][$ms['PID']]['pr_qty'] = $ms['pr_qty'];
				}

				if(isset($this->filters_options[$ms['ID']][$ms['PID']]))
				{
					$active_filters[$ms['ID']][$ms['PID']] = $ms;
				}

				$options_active[$ms['ID']] = array();
				$tkind[$ms['ID']] =  $ms['type_kind'];
				if(isset($ms['id_color'])) $options_array[$ms['ID']][$ms['PID']]['id_color'] = $ms['id_color'];
				if(isset($ms['image'])) $options_array[$ms['ID']][$ms['PID']]['image'] = BASE_PATH.$this->img_path.$ms['ID'].'/'.$ms['PID'].'/'.$ms['image'];
				$options_active[$ms['ID']] = $this->get_filter_option($ms['ID']);
			}
			$this->generate_SEO($active_filters);
		}
		return array('type_kind' => $tkind, 'select_array' => $select_array, 'options_array' => $options_array, 'options_active' => $options_active, 'category_url' => $category_url);
	}

	public function generate_SEO($active_types = array())
	{
		$seo_title = '';
		$seo_desc = '';
		foreach($active_types as $ms)
		{
			$seo_title_type = '';
			$seo_desc_type = '';
			foreach($ms as $opt)
			{
				$seo_title_type = ($opt['tseo_title'] != '') ? ' '.$opt['tseo_title'].' :' : '';
				$seo_desc_type = ($opt['tseo_description'] != '') ? ' '.$opt['tseo_description'].' :' : '';
				$seo_title .= ($opt['pseo_title'] != '') ? ' '.$opt['pseo_title'].',' : '';
				$seo_desc .= ($opt['pseo_description'] != '') ? ' '.$opt['pseo_description'].',' : '';
			}
			$seo_title = $seo_title_type.$seo_title.';';
			$seo_desc = $seo_desc_type.$seo_desc.';';
		}
		$seo_title = substr($seo_title, 0, -2);
		$seo_desc = substr($seo_desc, 0, -2);

		$this->template->add_TDK(array('seo_title' => $seo_title, 'seo_description' => $seo_desc));
	}

	/*public function get_sphinx_products()
	{
		$category_id = $this->variables->get_vars('category_id');
		$products = array();
		$order_by = $this->mcatalogue_sort->update_types_sphinx_order_by();
		$sphinx_sort = FALSE;
		if($order_by != '') $sphinx_sort = TRUE;
		if(count($this->filters_options) > 0 || count($this->filters_price_options) > 0 || count($this->filters_additional_options) > 0)
		{
			$filters_string = $this->build_sphinx_filters_string($this->filters_options);
			$price_string = $this->build_sphinx_filters_price_string($this->filters_price_options);
			$additional_string = $this->build_sphinx_filters_additional_string($this->filters_additional_options);

			$products = $this->get_sphinx_products_for_qty($category_id, $filters_string.$price_string.$additional_string, $order_by);
		}
		else if($order_by != '')
		{
			$products = $this->get_sphinx_products_for_qty($category_id, '', $order_by);
		}
		return array($products, $sphinx_sort);
	}*/

	public function get_sphinx_products_where()
	{
		$filters_where = '';
		if(count($this->filters_options) > 0 || count($this->filters_price_options) > 0 || count($this->filters_additional_options) > 0)
		{
			$filters_string = $this->build_sphinx_filters_string($this->filters_options);
			$price_string = $this->build_sphinx_filters_price_string($this->filters_price_options);
			$additional_string = $this->build_sphinx_filters_additional_string($this->filters_additional_options);

			$filters_where = $filters_string.$price_string.$additional_string;
		}
		return $filters_where;
	}

	/*public function update_products_query()
	{
		foreach($this->filters_options as $ms)
		{
			$WHERE = '';
			$IN = implode(',', $ms);
			$WHERE .= "`".self::ID_PROP."` IN (".$IN.") ";
			$this->db->where("A.`".self::ID_PR."` IN (SELECT `".self::ID_PR."` FROM `".self::PR_TYPES."` WHERE ".$WHERE." )", NULL, FALSE);
		}
	}*/

	public function clear_filters()
	{
		$category_id = $this->variables->get_vars('category_id');

		$data = $this->session->userdata('product_filters_price_data');
		if(isset($data[$category_id])) unset($data[$category_id]);
		$this->session->set_userdata('product_filters_price_data', $data);
		$this->filters_price_options = array();

		$data = $this->session->userdata('product_filters_additional_data');
		if(isset($data[$category_id])) unset($data[$category_id]);
		$this->session->set_userdata('product_filters_additional_data', $data);
		$this->filters_additional_options = array();

		$data = $this->session->userdata('product_filters_data');
		if(isset($data[$category_id])) unset($data[$category_id]);
		$this->session->set_userdata('product_filters_data', $data);
		$this->filters_options = array();
	}

	protected function build_filters_url_string($filters)
	{
		$params_str = '';
		if(is_array($filters) && count($filters) > 0)
		{
			$params_str = 'f:';
			foreach($filters as $key => $ms)
			{	$params_str .= $key.'=';
				$params_str .= implode(',', $ms);
				$params_str .= ';';
			}
			$params_str = substr($params_str,0,-1);
		}
		return $params_str;
	}

	protected function build_filters_price_url_string($filters)
	{
		$params_str = '';
		if(is_array($filters) && count($filters) > 0)
		{
			$params_str = 'p:';
			foreach($filters as $key => $ms)
			{	if(($ms = intval($ms)) > 0)
				{
					$params_str .= $key.'=';
					$params_str .= $ms;
					$params_str .= ';';
				}
			}
			$params_str = substr($params_str,0,-1);
		}
		return $params_str;
	}

	protected function build_filters_additional_url_string($filters)
	{
		$params_str = '';
		if(is_array($filters) && count($filters) > 0)
		{
			$params_str = 'a:';
			foreach($filters as $key => $ms)
			{
				$params_str .= $key.'=1;';
			}
			$params_str = substr($params_str,0,-1);
		}
		return $params_str;
	}

	public function submit_filters($category_url)
	{
		$this->clear_filters();
		$this->load->model('catalogue/mcategories');
		$category = $this->mcategories->get_category_by_url($category_url);
		$category_id = $category['ID'];
		$this->variables->set_vars('category_id', $category_id);

		$filters_implode_array = array();
		if($filters_additional = $this->input->post('products_filters_additional'))
		{
			$filters_options = array();
			foreach($filters_additional as $key => $ms)
			{
				$filters_options[$key] = $ms;
			}
			$data = $this->session->userdata('product_filters_additional_data');
			$data[$category_id] = $filters_options;
			$this->session->set_userdata('product_filters_additional_data', $data);
			$this->filters_additional_options = $filters_options;
		}

		if($filters_price = $this->input->post('products_filters_price'))
		{
			$this->load->model('catalogue/mcurrency');
			$current_currency = $this->mcurrency->get_current_currency();

			$filters_options = array();
			foreach($filters_price as $key => $ms)
			{
				$ms = intval($ms);
				if($ms > 0) $filters_options[$key] = $ms / $current_currency['rate'];
			}
			$data = $this->session->userdata('product_filters_price_data');
			$data[$category_id] = $filters_options;
			$this->session->set_userdata('product_filters_price_data', $data);
			$this->filters_price_options = $filters_options;
		}

		if($filters = $this->input->post('products_filters'))
		{
			$filters_options = array();
			foreach($filters as $key => $ms)
			{
				if(intval($ms) > 0)
				{
					$filters_options[$key] = $ms;
				}
			}
			$data = $this->session->userdata('product_filters_data');
			$data[$category_id] = $filters_options;
			$this->session->set_userdata('product_filters_data', $data);
			$this->filters_options = $filters_options;
		}

		$filters_url_string = $this->build_filters_url();

		if($filters_url_string == '')
		{
			redirect($this->router->build_url('category_sort_lang', array('category_url' => $category_url, 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())) ,301);
		}

		if(($types_set_data = $this->mtypes->get_types_set()) && strlen($types_set_data['url']) > 0)
		{
			redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $category_url, 'filters_params' => rawurldecode($types_set_data['url']), 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}
		else
		{
			redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $category_url, 'filters_params' => $filters_url_string, 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
		}
	}

	protected function build_filters_url()
	{
		$filters_implode_array = array();
		if(count($this->filters_additional_options) > 0)
		{
			$filters_implode_array[] = $this->build_filters_additional_url_string($this->filters_additional_options);
		}
		if(count($this->filters_price_options) > 0)
		{
			$filters_implode_array[] = $this->build_filters_price_url_string($this->filters_price_options);
		}
		if(count($this->filters_options) > 0)
		{
			$filters_implode_array[] = $this->build_filters_url_string($this->filters_options);
		}
		return implode('&', $filters_implode_array);
	}

	public function category_filters()
	{
		if(!$this->variables->get_url_vars('filters_params')) return FALSE;
		$filters_params = rawurldecode($this->variables->get_url_vars('filters_params'));

		if(mb_strpos($filters_params, 'f:') !== FALSE && mb_strpos($filters_params, 'p:') === FALSE && mb_strpos($filters_params, 'a:') === FALSE)
		{
			$filters_array = $this->pars_filter_url($filters_params);

			$data = $this->session->userdata('product_filters_data');
			$data[$this->variables->get_vars('category_id')] = $filters_array['filters'];
			$this->session->set_userdata('product_filters_data', $data);
			$this->filters_options = $filters_array['filters'];

			$data = $this->session->userdata('product_filters_price_data');
			if(isset($data[$this->variables->get_vars('category_id')])) unset($data[$this->variables->get_vars('category_id')]);
			$this->session->set_userdata('product_filters_price_data', $data);
			$this->filters_price_options = array();

			$data = $this->session->userdata('product_filters_additional_data');
			if(isset($data[$this->variables->get_vars('category_id')])) unset($data[$this->variables->get_vars('category_id')]);
			$this->session->set_userdata('product_filters_additional_data', $data);
			$this->filters_additional_options = array();

			if($custom_set_result = $this->get_types_set())
			{
				if(strlen($custom_set_result['url']) > 0)
				{
					redirect($this->router->build_url('category_filters_sort_lang', array('category_url' => $this->variables->get_vars('category_url'), 'filters_params' => $custom_set_result['url'], 'sort_params' => $this->variables->get_url_vars('sort_params'), 'lang' => $this->mlangs->lang_code, 'additional_params' => $this->variables->build_additional_url_params())), 301);
				}
				$this->set_seo_params($custom_set_result);
			}
		}
		else if(mb_strpos($filters_params, 'f:') === FALSE && mb_strpos($filters_params, 'p:') === FALSE && mb_strpos($filters_params, 'a:') === FALSE)
		{
			$custom_url = $filters_params;
			if($filters_array = $this->get_types_set_by_url($custom_url))
			{
				$data =  $this->session->userdata('product_filters_data');
				$data[$this->variables->get_vars('category_id')] = $filters_array['filters'];
				$this->session->set_userdata('product_filters_data', $data);
				$this->filters_options = $filters_array['filters'];
				$this->set_seo_params($filters_array['seo']);
			}
			else
			{
				show_404();
			}
		}
		else
		{
			$filters_array = $this->pars_filter_url($filters_params);

			$data = $this->session->userdata('product_filters_data');
			$data[$this->variables->get_vars('category_id')] = $filters_array['filters'];
			$this->session->set_userdata('product_filters_data', $data);
			$this->filters_options = $filters_array['filters'];

			$data = $this->session->userdata('product_filters_price_data');
			$data[$this->variables->get_vars('category_id')] = $filters_array['filters_price'];
			$this->session->set_userdata('product_filters_price_data', $data);
			$this->filters_price_options = $filters_array['filters_price'];

			$data = $this->session->userdata('product_filters_additional_data');
			$data[$this->variables->get_vars('category_id')] = $filters_array['filters_additional'];
			$this->session->set_userdata('product_filters_additional_data', $data);
			$this->filters_additional_options = $filters_array['filters_additional'];
		}
		return TRUE;
	}

	public function get_types_set()
	{
		$category_id = $this->variables->get_vars('category_id');
		if(count($this->filters_options) > 0 && count($this->filters_additional_options) == 0 && count($this->filters_price_options) == 0)
		{
			$params_str = '';
			$count = 0;
			foreach($this->filters_options as $properties)
			{
				foreach($properties as $prop_id)
				{
					$params_str .= $prop_id.',';
					$count++;
				}
				$params_str = substr($params_str,0,-1);
				$params_str .= ',';
			}
			$params_str = substr($params_str,0,-1);

			$this->db->select("A.`".self::ID_PR_TYPES_SET."`, A.`types_set_url`, A.`url`, D.`seo_title`, D.`seo_description`, D.`seo_keywords`, D.`description`")
				->from("(SELECT S.*, (SELECT count(*) FROM `".self::PR_TYPES_SET_ALIAS."` WHERE `".self::ID_PR_TYPES_SET."` = S.`".self::ID_PR_TYPES_SET."`) AS P_COUNT FROM `".self::PR_TYPES_SET."` AS S WHERE `".self::ID_CAT."` = ".$category_id.") AS A")
				->join("`".self::PR_TYPES_SET_ALIAS."` AS B",
					"A.`".self::ID_PR_TYPES_SET."` = B.`".self::ID_PR_TYPES_SET."`  AND ".$count." = (SELECT count(*) FROM `".self::PR_TYPES_SET_ALIAS."`
					WHERE `".self::ID_PR_TYPES_SET."` = A.`".self::ID_PR_TYPES_SET."` AND `id_m_c_products_properties` IN (".$params_str.")) ",
					"INNER")
				->join("`".self::PR_TYPES_SET_DESC."` AS D", "D.`".self::ID_PR_TYPES_SET."` = A.`".self::ID_PR_TYPES_SET."`", "INNER")
				->where("A.`P_COUNT`", $count)->limit(1);

			if($result = $this->db->get()->row_array())
			{
				return $result;
			}
			return false;
		}
		return false;
	}

	public function get_types_set_by_url($custom_url)
	{
		$category_id = $this->variables->get_vars('category_id');

		$this->db->select("A.`".self::ID_PR_TYPES_SET."`, B.`".self::ID_PROP."` AS ID_PROP, T.`".self::ID_TYPES."` AS ID_TYPES, D.`seo_title`, D.`seo_description`, D.`seo_keywords`, D.`description`")
			->from("`".self::PR_TYPES_SET."` AS A")
			->join("`".self::PR_TYPES_SET_ALIAS."` AS B", "A.`".self::ID_PR_TYPES_SET."` = B.`".self::ID_PR_TYPES_SET."`",
					"INNER")
			->join("`".self::PROP."` AS T",	"B.`".self::ID_PROP."` = T.`".self::ID_PROP."`", "INNER")
			->join("`".self::PR_TYPES_SET_DESC."` AS D",
					"A.`".self::ID_PR_TYPES_SET."` = D.`".self::ID_PR_TYPES_SET."` && `".self::ID_LANGS."` = ".$this->mlangs->id_langs,
					"LEFT")
			->where("A.`url`", $custom_url)
			->where("A.`".self::ID_CAT."`", $category_id)
			->where("A.`".self::ID_USERS."`", $this->id_users);

		if($result = $this->db->get()->result_array())
		{
			$data = array();
			foreach($result as $ms)
			{
				$data['filters'][$ms['ID_TYPES']][$ms['ID_PROP']] =  $ms['ID_PROP'];
				$data['seo'] = array('seo_title' => $ms['seo_title'], 'seo_description' => $ms['seo_description'], 'seo_keywords' => $ms['seo_keywords'], 'description' => $ms['description']);
			}
			return $data;
		}
		return false;
	}

	public function build_filters_params_str($filters_data)
	{
		if(!is_array($filters_data)) return FALSE;
		$params_str = '';
		foreach($filters_data as $type_id => $properties)
		{	$params_str .= $type_id.'=';
			foreach($properties as $prop_id)
			{
				$params_str .=$prop_id.',';
			}
			$params_str = substr($params_str,0,-1);
			$params_str .= ';';
		}
		$params_str = substr($params_str,0,-1);
		return $params_str;
	}

	public function build_filters_params_array($params_str)
	{
		if(strlen($params_str) > 0)
		{
			$filters_options_array = array();
			$tmp_params = explode(';', $params_str);
			foreach($tmp_params as $str_params)
			{
				$type_properties = explode('=', $str_params);
				$properties =  explode(',',$type_properties[1]);
				foreach($properties as $prop_id)
				{
					$filters_options_array[$type_properties[0]][$prop_id] =  $prop_id;
				}
			}
			return $filters_options_array;
		}
		else
		{
			return FALSE;
		}
	}

	public function pars_filter_url($url)
	{
		$return_filters['filters'] = array();
		$return_filters['filters_price'] = array();
		$return_filters['filters_additional'] = array();
		$filters_pars_array = explode('&', $url);
		foreach($filters_pars_array as $ms)
		{
			if(mb_strpos($ms, 'f:') !== FALSE)
			{
				$filters_array = array();
				$str = substr($ms, 2);
				$sets = explode(';', $str);
				foreach($sets as $ms1)
				{
					list($type, $prop) = explode('=', $ms1);
					$prop = explode(',', $prop);
					foreach($prop as $pp)
					{
						$filters_array[$type][$pp] = $pp;
					}
				}
				$return_filters['filters'] = $filters_array;
			}
			if(mb_strpos($ms, 'p:') !== FALSE)
			{
				$filters_array = array();
				$str = substr($ms, 2);
				$sets = explode(';', $str);
				foreach($sets as $ms1)
				{
					list($type, $prop) = explode('=', $ms1);
					$prop = explode(',', $prop);
					foreach($prop as $pp)
					{
						$filters_array[$type] = $pp;
					}
				}
				$return_filters['filters_price'] = $filters_array;
			}
			if(mb_strpos($ms, 'a:') !== FALSE)
			{
				$filters_array = array();
				$str = substr($ms, 2);
				$sets = explode(';', $str);
				foreach($sets as $ms1)
				{
					list($type, $prop) = explode('=', $ms1);
					$prop = explode(',', $prop);
					foreach($prop as $pp)
					{
						$filters_array[$type] = $pp;
					}
				}
				$return_filters['filters_additional'] = $filters_array;
			}
		}
		return $return_filters;
	}

	public function set_seo_params($seo_array)
	{
		$this->template->add_title($seo_array['seo_title']);
		$this->template->add_description($seo_array['seo_description']);
		$this->template->add_keywords($seo_array['seo_keywords']);
		$this->template->set_view_to_template('categories_description_block', 'catalogue/categories/categories_description', array('description' => $seo_array['description']));
	}
}
?>