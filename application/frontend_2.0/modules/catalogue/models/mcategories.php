<?php
class Mcategories extends AG_Model
{
	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';

	const  CAT_PER          = 'm_c_categories_permission';
	const  ID_CAT_PER       = 'id_m_c_categories';
	
	const CAT_LINK 		= 'm_c_categories_link';
	
	protected $temp_tree_array = array();
	protected $tree_array = array();
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_categories_tree_collection()
	{
		$url = $this->variables->get_url_vars('category_url');
		if($url)
		{

			if($res = $this->get_category_by_url($url))
			{
				
				
				if($url == $res['ID'] && strlen($res['url'])>2)
				{
					redirect($this->router->build_url('category_lang', array('category_url' => $res['url'], 'lang' => $this->mlangs->lang_code)), 301);
				}
				$id = $res['ID'];
				$this->variables->set_vars('category_id', $id);
				$this->variables->set_vars('category_parent_id', $res['id_parent']);
				$this->variables->set_vars('category_url', $url);
				if(trim(strip_tags($res['description'])) != '')
				{
					$this->template->add_view_to_template('categories_description_block', 'catalogue/categories/categories_description', array('description' => $res['description']));
				}
				if($res['seo_title'] == '')
				{
					$res['seo_title'] = $res['name'];
				}
				$this->template->set_TDK($res);
				
				$this->get_categories_collection_query();
				$this->db->join(	"`".self::CAT_LINK."` AS L",
								"L.`id_parent` <=> A.`id_parent` && 
								(
									L.`".self::ID_CAT."` IN (SELECT `".self::ID_CAT."` FROM `".self::CAT_LINK."` WHERE `id_parent` = '".$id."' && `open` = 1) 
									OR 
									L.`".self::ID_CAT."` = (SELECT if(count(*), `".self::ID_CAT."`, '".$id."') FROM `".self::CAT."` WHERE `id_parent` = '".$id."' LIMIT 1)
								)",
								"INNER");
				$result = $this->get_categories_tree_collection_array($this->db->get()->result_array());
				$this->set_categories_navigation($id);
				return array('categories_original' => $result[0], 'categories' => $result[1]);
			}
			else
			{
				show_404();
			}			
		}
		else
		{
			$this->db
				->select("DISTINCT A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, (SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS have_chield,
				(SELECT COUNT(*) FROM m_c_productsNcategories AS NC INNER JOIN `m_c_products` AS PR ON PR.`id_m_c_products` = NC.`id_m_c_products` && PR.`status` = 1 WHERE NC.`".self::ID_CAT."` = A.`".self::ID_CAT."`) AS products_count")
				->from("`".self::CAT."` AS A")
				->join("`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->where("A.`show`", 1)
				->order_by("A.`sort`");


			$this->db->join(
			"(
				SELECT ZZZ.`id_m_c_categories` FROM 
				(
					SELECT ZZ.`id_m_c_categories`, (SELECT `open` FROM `m_c_categories` WHERE `id_m_c_categories` = ZZ.`id_parent` LIMIT 1) AS zopen FROM 
					(
						SELECT * FROM `m_c_categories_link` WHERE `id_users` = '".$this->id_users."' ORDER BY `id_parent` DESC
					) AS ZZ GROUP BY `id_m_c_categories`

				) AS ZZZ WHERE ZZZ.zopen = 1 OR ZZZ.zopen IS NULL
			) AS L",
			"L.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
			"INNER");
			
			$result = $this->get_categories_tree_collection_array($this->db->get()->result_array());
			return array('categories_original' => $result[0], 'categories' => $result[1]);
		}
	}
	
	public function get_category_by_url($url)
	{
		$this->db
			->select("A.`".self::ID_CAT."` AS ID, A.`url`, A.`id_parent`, B.`name`, B.`description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
			->from("`".self::CAT."` AS A")
			->join("`".self::CAT_DESC."` AS B",
					"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
					"LEFT")
			->where("A.`".self::ID_CAT."`", $url)->or_where("A.`url`", $url)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);

		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}
	
	protected function get_categories_collection_query()
	{
		$query = $this->db
				->select("DISTINCT A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, (SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS have_chield,
				(SELECT COUNT(*) FROM m_c_productsNcategories AS NC INNER JOIN `m_c_products` AS PR ON PR.`id_m_c_products` = NC.`id_m_c_products` && PR.`status` = 1 WHERE NC.`".self::ID_CAT."` = A.`".self::ID_CAT."`) AS products_count")
				->from("`".self::CAT."` AS A")
				->join("`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->where("A.`show`", 1)
				->order_by("A.`sort`");
		return $query;
	}
	
	protected function get_categories_tree_collection_array($result = array())
	{
		$array = array();
		$this->temp_tree_array[0] = array();
		$add_params = $this->variables->build_additional_url_params();
		foreach($result as $ms)
		{
			$cat_url = $ms['ID'];
			if(strlen($ms['url']) > 2)
			{
				$cat_url = $ms['url'];
			}
		
			$url = $this->router->build_url('category_lang', array('category_url' => $cat_url, 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params));
			
			if($ms['id_parent'] == NULL)
			{
				$this->temp_tree_array[0][$ms['ID']] = $ms + array('category_url' => $url, 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params);
			}
			else
			{
				$this->temp_tree_array[$ms['id_parent']][] = $ms + array('category_url' => $url, 'lang' => $this->mlangs->lang_code, 'additional_params' => $add_params);
			}
		}
		$array[0] = $this->temp_tree_array;
		$array[1] = $this->build_categories_tree();
		return $array;
	}
	
	public function set_categories_navigation($id)
	{
		$query = $this->db
				->select("`id_parent`")
				->from("`".self::CAT_LINK."`")
				->where("`".self::ID_CAT."`", $id);
		$navigation_array = array();
		foreach($query->get()->result_array() as $ms)
		{
			if(isset($this->tree_array[$ms['id_parent']]))
			{
				$navigation_array[] = array($this->tree_array[$ms['id_parent']]['category_url'], $this->tree_array[$ms['id_parent']]['name']);
			}
		}
		$navigation_array[] = array($this->tree_array[$id]['category_url'], $this->tree_array[$id]['name']);
		$this->variables->set_vars('navigation_array', array('navigation' => array($navigation_array)));
		
		return $this;
	}
	
	public function get_child_categories_collection($parent_id)
	{
		$array = array();
		$this->get_categories_collection_query();
		$this->db->where("A.`id_parent`", $parent_id);
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			$array = $this->get_categories_tree_collection_array($result);
			$array = $array[0][$parent_id];
		}
		return array('categories' => $array);
	}
	
	protected function build_categories_tree($id_parent = 0)
	{
		if(!isset($this->temp_tree_array[$id_parent])) return FALSE;
		$array = $this->temp_tree_array[$id_parent];
		foreach($array as $key => $ms)
		{
			$this->tree_array[$ms['ID']] = $ms;
			$this->build_categories_tree($ms['ID']);
		}
		if($id_parent == 0)
		{
			$result = $this->tree_array;
			$this->temp_tree_array = array();
			return $result;
		}
	}
	public function check_permission() 
	{
		$url = $this->variables->get_url_vars('category_url');
		$this->db
			->select("A.`".self::ID_CAT."` AS ID, A.`permission`")
			->from("`".self::CAT."` AS A")
			->where("A.`".self::ID_CAT."`", $url)->or_where("A.`url`", $url)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}

	public function get_category_id_groups($id)
	{
		$this->db
			->select("A.`id_m_u_types`")
			->from("`m_c_categories_permissions` AS A")
			->where("A.`id_m_c_categories`", $id);
		$result = $this->db->get()->result_array();
		//echo var_dump($result);
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}
}


