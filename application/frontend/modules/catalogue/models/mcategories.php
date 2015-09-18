<?php
class Mcategories extends AG_Model
{
	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';
	
	const CAT_LINK 		= 'm_c_categories_link';
	
	protected $temp_tree_array = array();
	protected $tree_array = array();
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	/*public function get_categories_tree()
	{
		$param = $this->uri->ruri_to_assoc(3);
		if(isset($param['categorie_url']))
		{
			$url = $param['categorie_url'];
			$query = $this->db
				->select("`".self::ID_CAT."`, `url`, (SELECT `".self::ID_CAT."` FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."` LIMIT 1) AS chield")
				->from("`".self::CAT."` AS A")
				->where("A.`".self::ID_CAT."`", $url)->or_where("A.`url`", $url)->limit(1);
			$result = $query->get()->row_array();	
			
			if(count($result)>0)
			{
				$id_categories = $result[self::ID_CAT];
				$categories_href = $id_categories;
				if(trim($result['url']) != '')
				{
					$categories_href = trim($result['url']);
				}
				$id_categories_query = $id_categories;
				if($result['chield']>0)
				{
					$id_categories_query = $result['chield'];
				}
				$query = $this->db
					->select("A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, B.`description`")
					->from("`".self::CAT."` AS A")
					->join("`".self::CAT_LINK."` AS L",
							"L.`id_parent` <=> A.`id_parent`",
							"INNER")
					->join("`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`id_langs` = ".$this->mlangs->id_langs,
							"LEFT")
					->where("A.`id_users`", $this->id_users)->where("A.`active`", 1)->where("L.`".self::ID_CAT."`", $id_categories_query)
					->order_by("A.`sort`");
				$temp_result = $query->get()->result_array();
				$result['categories'] = $this->create_categories_array($temp_result, TRUE);
				unset($temp_result);
				$result['id_categories'] = $id_categories;
				$result['categories_href'] = $categories_href;
				return $result;
			}
			else
			{
				return $this->get_categories_collection();
			}
		}
		else
		{
			return $this->get_categories_collection();
		}
	}*/
	
	public function get_categories_tree_collection()
	{
		$url = $this->variables->get_url_vars('categorie_url');
		if($url)
		{
			if($res = $this->get_categorie_by_url($url))
			{
				if($url == $res['ID'] && strlen($res['url'])>2)
				{
					redirect($this->router->build_url('categorie_lang', array('categorie_url' => $res['url'], 'lang' => $this->mlangs->lang_code)), 301);
				}
				$id = $res['ID'];
				$this->variables->set_vars('categorie_id', $id);
				$this->variables->set_vars('categorie_parent_id', $res['id_parent']);
				$this->variables->set_vars('categorie_url', $url);
				if(trim(strip_tags($res['description'])) != '')
				{
					$this->template->add_view_to_template('categories_description_block', 'catalogue/categories/categories_description', array('description' => $res['description']));
				}
				if($res['seo_title'] == '')
				{
					$res['seo_title'] = $res['name'];
				}
				$this->template->set_TDK($res);
				
				$query = $this->get_categories_collection_query();
				$query	->join(	"`".self::CAT_LINK."` AS L",
								"L.`id_parent` <=> A.`id_parent` && 
								(
									L.`".self::ID_CAT."` IN (SELECT `".self::ID_CAT."` FROM `".self::CAT_LINK."` WHERE `id_parent` = '".$id."' && `open` = 1) 
									OR 
									L.`".self::ID_CAT."` = (SELECT if(count(*), `".self::ID_CAT."`, '".$id."') FROM `".self::CAT."` WHERE `id_parent` = '".$id."' LIMIT 1)
								)",
								"INNER");
				//echo $query->_compile_select();
				$result = $this->get_categories_tree_collection_array($query->get()->result_array());
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
			$query = $this->db
				->select("DISTINCT A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, (SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS have_chield, (SELECT COUNT(*) FROM m_c_productsNcategories WHERE `".self::ID_CAT."` = A.`".self::ID_CAT."`) AS products_count")
				->from("`".self::CAT."` AS A")
				->join("`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->where("A.`show`", 1)
				->order_by("A.`sort`");
				
			
			$query	->join(
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
			
			/*$query	->join(
			"(
				SELECT ZZZ.`id_m_c_categories` FROM 
				(
					SELECT ZZ.`id_m_c_categories`, (SELECT MAX(`open`) FROM `m_c_categories_link` WHERE `id_m_c_categories` = ZZ.`id_parent` LIMIT 1) AS zopen FROM 
					(
						SELECT * FROM `m_c_categories_link` WHERE `id_users` = '".$this->id_users."' ORDER BY `id_parent` DESC
					) AS ZZ GROUP BY `id_m_c_categories`

				) AS ZZZ WHERE ZZZ.zopen = 1 OR ZZZ.zopen IS NULL
			) AS L",
			"L.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
			"INNER");*/
							
			//echo $query->_compile_select();
			/*
			
			SELECT SQL_NO_CACHE DISTINCT A.`id_m_c_categories` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, (SELECT COUNT(*) FROM `m_c_categories` WHERE `id_parent` = A.`id_m_c_categories`) AS have_chield FROM (`m_c_categories` AS A) LEFT JOIN `m_c_categories_description` AS B ON B.`id_m_c_categories` = A.`id_m_c_categories` && B.`id_langs` = 1 

INNER JOIN (

SELECT ZZZ.`id_m_c_categories` FROM 

    (
     SELECT ZZ.`id_m_c_categories`, (SELECT MAX(`open`) FROM `m_c_categories_link` WHERE `id_m_c_categories` = ZZ.`id_parent` LIMIT 1) AS zopen FROM 

(SELECT * FROM `m_c_categories_link` WHERE `id_users` = 11082 ORDER BY `id_parent` DESC) AS ZZ GROUP BY `id_m_c_categories`


    ) AS ZZZ

WHERE ZZZ.zopen = 1 OR ZZZ.zopen IS NULL

) AS L ON A.`id_m_c_categories` = L.`id_m_c_categories`

WHERE 

`A`.`active` = 1 ORDER BY A.`sort` 
			
			*/
			
			$result = $this->get_categories_tree_collection_array($query->get()->result_array());
			return array('categories_original' => $result[0], 'categories' => $result[1]);
		}
	}
	
	public function get_categorie_by_url($url)
	{
		$query = $this->db
				->select("A.`".self::ID_CAT."` AS ID, A.`url`, A.`id_parent`, B.`name`, B.`description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::CAT."` AS A")
				->join("`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->where("A.`".self::ID_CAT."`", $url)->or_where("A.`url`", $url)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}
	
	protected function get_categories_collection_query()
	{
		$query = $this->db
				->select("DISTINCT A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, (SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS have_chield, (SELECT COUNT(*) FROM m_c_productsNcategories WHERE `".self::ID_CAT."` = A.`".self::ID_CAT."`) AS products_count")
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
		//$this->temp_tree_array['chield'] = array();
		foreach($result as $ms)
		{
			$cat_url = $ms['ID'];
			if(strlen($ms['url']) > 2)
			{
				$cat_url = $ms['url'];
			}
		
			$url = $this->router->build_url('categorie_lang', array('categorie_url' => $cat_url, 'lang' => $this->mlangs->lang_code));
			
			if($ms['id_parent']==NULL)
			{
				$this->temp_tree_array[0][$ms['ID']] = $ms + array('categorie_url' => $url, 'lang' => $this->mlangs->lang_code);
			}
			else
			{
				$this->temp_tree_array[$ms['id_parent']][] = $ms + array('categorie_url' => $url, 'lang' => $this->mlangs->lang_code);
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
				$navigation_array[] = array($this->tree_array[$ms['id_parent']]['categorie_url'], $this->tree_array[$ms['id_parent']]['name']);
			}	
		}
		$navigation_array[] = array($this->tree_array[$id]['categorie_url'], $this->tree_array[$id]['name']);
		$this->variables->set_vars('navigation_array', array('navigation' => array($navigation_array)));
		
		return $this;
	}
	
	public function get_categories_collection()
	{
		$query = $this->db
			->select("A.`".self::ID_CAT."` AS ID, A.`url`, A.`level`, A.`id_parent`, B.name, B.`description`")
			->from("`".self::CAT."` AS A")
			->join("`".self::CAT_DESC."` AS B",
					"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`id_langs` = ".$this->mlangs->id_langs,
					"LEFT")
			->where("A.`id_users`", $this->id_users)->where("A.`active`", 1)->where("A.`level`", 1)
			->order_by("A.`sort`");
		$temp_result	= $query->get()->result_array();
		$result['categories'] = $this->create_categories_array($temp_result);
		return $result;
	}
	
	public function get_child_categories_collection($parent_id)
	{
		$array = array();
		$query = $this->get_categories_collection_query();
		$query->where("A.`id_parent`", $parent_id);
		$result = $query->get()->result_array();
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
}