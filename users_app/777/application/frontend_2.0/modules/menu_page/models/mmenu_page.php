<?php
class Mmenu_page extends AG_Model
{
	CONST MENU_MODULES 	= 'users_menu_modules';
	CONST UM 			= 'users_modules';
	CONST ID_UM 		= 'id_users_modules';
	
	CONST M 	= 'modules';
	CONST ID_M 	= 'id_modules';
	
	CONST MENU 		= 'm_menu';
	CONST ID_MENU 	= 'id_m_menu';
	CONST MENU_DESC = 'm_menu_description';
	CONST MENU_LINK = 'm_menu_link';
	
	protected $menu_tree = array();
	protected $menu_tree_nav = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_menu_collection()
	{
		$current_menu_url = $this->variables->get_url_vars('menu_url');
		$query = $this->db
				->select("A.`".self::ID_MENU."` AS ID, A.`url`, A.`clickable`, A.`active`, A.`id_parent`, A.`level`, B.`name`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::MENU."` AS A")
				->join(	"`".self::MENU_DESC."` AS B",
						"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("A.`sort`");
		$temp_result = $query->get()->result_array();
		$result = array();
		$result[0] = array();
		if($current_menu_url)
		{
			$p_404 = TRUE;
			$menu_base = array();
			foreach($temp_result as $key => $ms)
			{
				if($current_menu_url == $ms['ID'] OR $current_menu_url == $ms['url'])
				{
					if($current_menu_url == $ms['ID'] && strlen($ms['url'])>2)
					{
						redirect($this->router->build_url('menu_lang', array('menu_url' => $ms['url'], 'lang' => $this->mlangs->lang_code)), 301);
						exit;
					}
					$ms = $ms + array('selected' => 1);
					$this->variables->set_vars(self::ID_MENU, $ms['ID']);
					$this->variables->set_vars('active_menu', $ms);
					$p_404 = FALSE;
					
					$seo = array('seo_title' => $ms['seo_title'], 'seo_description' => $ms['seo_description'], 'seo_keywords' => $ms['seo_keywords']);
					if(trim($seo['seo_title']) == '')
					{
						$seo['seo_title'] = $ms['name'];
					}
					$this->template->set_TDK($seo);
				}
				
				if($ms['clickable'] == 1)
				{
					$menu_url = trim($ms['url']);
					if(strlen($menu_url) < 3 )
					{
						$menu_url = $ms['ID'];
					}
					$ms = $ms + array('menu_url' => $this->router->build_url('menu_lang', array('menu_url' => $menu_url, 'lang' => $this->mlangs->lang_code)));
				}
				else
				{
					$ms = $ms + array('menu_url' => FALSE);
				}
				
				if($ms['id_parent'] == NULL)
				{
					$result[0][] = $ms;
					if($ms['active'] == 1)
					{
						$menu_base[0][] = $ms;
					}
				}
				else
				{
					$result[$ms['id_parent']][] = $ms;
					if($ms['active'] == 1)
					{
						$menu_base[$ms['id_parent']][] = $ms;
					}	
				}
			}
			if($p_404) { show_404(); }
			$this->_recurs_tree($result, $K = 0);
			$this->set_menu_navigation();
			return array('menu_base' => $menu_base, 'menu' => $this->menu_tree);
		}
		else
		{
			$query = $this->db
				->select("B.`name`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::MENU_DESC."` AS B")
				->where("B.`".self::ID_USERS."`", $this->id_users)->where("B.`".self::ID_LANGS."`", $this->mlangs->id_langs)->limit(1);
			$seo = $query->get()->row_array();
			if(count($seo)>0)
			{
				if(trim($seo['seo_title']) == '')
				{
					$seo['seo_title'] = $seo['name'];
				}
				$this->template->set_TDK($seo);
			}
			
			foreach($temp_result as $ms)
			{
				if($ms['active'] == 1)
				{
					if($ms['clickable'] == 1)
					{
						$menu_url = trim($ms['url']);
						if(strlen($menu_url) < 3 )
						{
							$menu_url = $ms['ID'];
						}
						$ms = $ms + array('menu_url' => $this->router->build_url('menu_lang', array('menu_url' => $menu_url, 'lang' => $this->mlangs->lang_code)));
					}
					else
					{
						$ms = $ms + array('menu_url' => FALSE);
					}
					
					if($ms['id_parent'] == NULL)
					{
						$result[0][] = $ms;
					}
					else
					{
						$result[$ms['id_parent']][] = $ms;
					}
				}
			}
			$this->_recurs_tree($result, $K = 0);
		}
		return array('menu_base' => $result, 'menu' => $this->menu_tree);
	}
	
	protected function _recurs_tree($array, $K = 0)
	{
		if(isset($array[$K]))
		{
			foreach($array[$K] as $key => $ms)
			{
				$this->menu_tree_nav[$ms['ID']] = $ms;
				if($ms['active'] == 1)
				{
					$this->menu_tree[$ms['ID']] = $ms;
					if(isset($array[$ms['ID']]))
					{
						$this->_recurs_tree($array, $ms['ID']);
					}
				}
			}
		}
	}
	
	public function set_menu_navigation()
	{
		if($id = $this->variables->get_vars(self::ID_MENU))
		{
			$query = $this->db
					->select("`id_parent`")
					->from("`".self::MENU_LINK."`")
					->where("`".self::ID_MENU."`", $id)->order_by("`id_m_menu_link`");
			$navigation_array = array();
			foreach($query->get()->result_array() as $ms)
			{
				if(isset($this->menu_tree[$ms['id_parent']]))
				{
					$navigation_array[] = array($this->menu_tree_nav[$ms['id_parent']]['menu_url'], $this->menu_tree_nav[$ms['id_parent']]['name']);
				}	
			}
			$navigation_array[] = array($this->menu_tree_nav[$id]['menu_url'], $this->menu_tree_nav[$id]['name']);
			$this->variables->set_vars('navigation_array', array('navigation' => array($navigation_array)));
		}
		return $this;
	}
	
	public function get_users_menu_modules($home_modules = FALSE)
	{
		$id = $this->variables->get_vars(self::ID_MENU);
		$query = $this->db
				->select("A.`".self::ID_UM."`, C.alias")
				->from("`".self::MENU_MODULES."` AS A")
				->join(	"`".self::UM."` AS B",
						"B.`".self::ID_UM."` = A.`".self::ID_UM."` && B.`".self::ID_USERS."` = '".$this->id_users."' && B.`active` = '1'",
						"inner")
				->join(	"`".self::M."` AS C",
						"C.`".self::ID_M."` = B.`".self::ID_M."`",
						"inner")
				->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("A.`sort`");
		if($id)
		{
			$query->where("A.`".self::ID_MENU."`", $id);
			return $query->get()->result_array();
		}
		if($home_modules)
		{
			$query->where("A.`".self::ID_MENU."` IS NULL", NULL, FALSE);
			return $query->get()->result_array();
		}
		return array();
	}
	
	public function call_module_function()
	{
		$modules_array = $this->get_users_menu_modules();
		foreach($modules_array as $ms)
		{
			echo modules::run($ms['alias']."/index", $ms);
		}
	}
	public function call_home_module_function()
	{
		$modules_array = $this->get_users_menu_modules(TRUE);
		foreach($modules_array as $ms)
		{
			echo modules::run($ms['alias']."/index", $ms);
		}
	}
}
?>