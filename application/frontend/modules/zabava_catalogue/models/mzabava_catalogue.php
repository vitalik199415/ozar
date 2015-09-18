<?php
class Mzabava_catalogue extends AG_Model
{
	const CAT 		= 'm_zabava_catalogue';
	const ID_CAT 	= 'id_m_zabava_catalogue';
	const CAT_DESC = 'm_zabava_catalogue_description';
	
	const CP 			= 'm_zabava_catalogue_photos';
	const ID_CP 		= 'id_m_zabava_catalogue_photos';
	const CP_DESC 		= 'm_zabava_catalogue_photos_description';
	const ID_CP_DESC 	= 'id_m_zabava_catalogue_photos_description';
	
	const AD			= 'm_zabava_additional_block';
	const ID_AD			= 'id_m_zabava_additional_block';
	const AD_ALIAS		= 'm_zabava_additional_block_alias';
	const ID_AD_ALIAS	= 'id_m_zabava_additional_block_alias';
	
	const IMG_FOLDER = '/zabava_catalogue_album/';
	const FILE_FOLDER = '/zabava_catalogue_file/';
	
	protected $img_path = FALSE;
	protected $manual_path = FALSE;
	protected $game_path = FALSE;
	
	protected $id_users_modules = FALSE;
	
	protected $detail = FALSE;
	protected $settings = FALSE;
	
	protected $output_settings = array('module_news_per_page' => 50);
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function _init($id_users_modules, $settings = FALSE)
	{
		$this->id_users_modules = (int) $id_users_modules;
		$this->img_path = IMG_PATH.ID_USERS.'/media/module_'.$id_users_modules.self::IMG_FOLDER;
		$this->manual_path = IMG_PATH.ID_USERS.'/media/module_'.$id_users_modules.self::FILE_FOLDER;
		$this->game_path = IMG_PATH.ID_USERS.'/media/zabava_games/';
		if($detail = $this->variables->get_url_vars('detail')) $this->detail = $detail;
		
		$this->settings = $settings;
		$this->get_settings(TRUE);
	}
	
	public function get_catalogue_collection()
	{
		if($this->detail) return $this->get_catalogue_detail();
		$query = $this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::CAT."`")
				->where("`id_users_modules`", $this->id_users_modules)->where("`active`", 1);
		$count = $query->get()->row_array();
		$count = $count['COUNT'];
		if($count>0)
		{
			$page = 1;
			if($this->variables->get_url_vars('page') == 1) redirect($this->router->build_url('menu_lang', array('menu_url' => $this->variables->get_url_vars('menu_url'), 'lang' => $this->langs->id_langs)) ,301);
			if((int) $this->variables->get_url_vars('page')>0)
			{
				$page = (int) $this->variables->get_url_vars('page');
			}
			
			$query = $this->get_catalogue_collection_query();
			$pages_array = $this->set_limit($query, $count, $page);
			//echo $query->_compile_select();
			$array = $this->get_catalogue_collection_array($query->get()->result_array(), $pages_array);
			return array('short', $array);
		}
		else
		{
			return FALSE;
		}
	}
	
	protected function get_catalogue_collection_query()
	{
		$query = $this->db
				->select("A.`".self::ID_CAT."` AS ID, A.`url`, B.`name`, B.`short_description`, C.`image`, D.`name` AS image_name, D.`title` AS image_title, D.`alt` AS image_alt, M.`menu_id`, M.`menu_url`")
				->from("`".self::CAT."` AS A")
				->join(	"(SELECT B.`id_m_menu` AS menu_id, B.`url` AS menu_url, A.`id_users_modules` FROM `users_menu_modules` AS A USE INDEX (`id_users_modules`), `m_menu` AS B USE INDEX (`PRIMARY`) WHERE A.`".self::ID_USERS."` = ".$this->id_users." && A.`id_users_modules` = '".$this->id_users_modules."' && A.`base_module` = 1 && B.`id_m_menu` = A.`id_m_menu` LIMIT 1) AS M",
						"M.`id_users_modules` = A.`id_users_modules`",
						"LEFT")
				->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->join(	"`".self::CP."` AS C",
						"C.`".self::ID_CAT."` = A.`".self::ID_CAT."` && C.`".self::ID_CP."` = (SELECT `".self::ID_CP."` FROM `".self::CP."` WHERE `".self::ID_CAT."` = A.`".self::ID_CAT."` ORDER BY `sort` LIMIT 1)",
						"LEFT")
				->join(	"`".self::CP_DESC."` AS D",
						"D.`".self::ID_CP."` = C.`".self::ID_CP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->order_by("A.`sort`");
		return $query;
	}
	
	protected function get_catalogue_collection_array($result = array(), $pages_data = FALSE)
	{
		$array = array();
		foreach($result as $ms)
		{
			$menu_url = $ms['menu_id'];
			if(trim($ms['menu_url']) != '')
			{
				$menu_url = trim($ms['menu_url']);
			}
		
			if($menu_url != NULL && ($url = trim($ms['url'])) != '' && strlen($url)>2)
			{
				$url = $this->router->build_url('menu_detail_lang', array('menu_url' => $menu_url, 'detail' => $url, 'lang' => $this->mlangs->lang_code));
			}
			else if($menu_url != NULL)
			{
				$url = $ms['ID'];
				$url = $this->router->build_url('menu_detail_lang', array('menu_url' => $menu_url, 'detail' => $url, 'lang' => $this->mlangs->lang_code));
			}
			$array[$ms['ID']] = $ms;
			if($ms['image'] != NULL)
			{
				$array[$ms['ID']] = $ms + array('timage' => $this->img_path.$ms['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['image']);
			}
			if(isset($url))
			{
				$array[$ms['ID']] = $array[$ms['ID']] + array('detail_url' => $url);
			}
		}
		$pages_array = array();
		if($menu_url != NULL)
		{
			if($pages_data && count($pages_data)>0)
			{
				$this->load->helper('pages');
				$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('menu_page_lang', array('menu_url' => $menu_url, 'page' => '', 'lang' => $this->mlangs->lang_code)));
			}	
		}
		return array('catalogue' => $array, 'pages' => $pages_array);
	}
	
	protected function set_limit($query, $count, $page = 1)
	{
		$limit = $this->output_settings['module_news_per_page'];
		if($page > ceil($count/$limit))
		{
			redirect($this->router->build_url('menu_page_lang', array('page' => ceil($count/$limit), 'menu_url' => $this->variables->get_url_vars('menu_url'), 'lang' => $this->langs->id_langs)) ,301);
		}
		
		if($page == 0)
		{
			$page = 1;
		}
		$offset = ($page-1)*$limit;
		$showcount = $limit;
		$query->limit($showcount, $offset);

		return array($count, $page, $limit);
	}
	
	public function get_catalogue_detail($id = FALSE, $back_link = TRUE)
	{
		$result = array();
		$query = $this->db
				->select("A.`".self::ID_CAT."` AS ID, A.`url`, A.`sort`, B.`name`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::CAT."` AS A")
				->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->limit(1);
		if($id)
		{
			$query->where("(A.`".self::ID_CAT."` = '".$id."')", NULL, FALSE);
		}
		else
		{
			$query->where("(A.`".self::ID_CAT."` = '".$this->detail."' OR A.`url` = '".$this->detail."')", NULL, FALSE);
		}
		$result['catalogue'] = $query->get()->row_array();
		if(count($result['catalogue'])>0)
		{
			$seo = array('name' => $result['catalogue']['name'], 'seo_title' => $result['catalogue']['seo_title'], 'seo_description' => $result['catalogue']['seo_description'], 'seo_keywords' => $result['catalogue']['seo_keywords']);
			if(count($seo)>0)
			{
				if($seo['seo_title'] == '')
				{
					$seo['seo_title'] = $seo['name'];
				}
				$this->template->set_TDK($seo);
			}
			
			if($this->detail == $result['catalogue']['ID'] && strlen($result['catalogue']['url'])>2)
			{
				redirect($this->router->build_url('menu_detail_lang', array('menu_url' => $this->variables->get_url_vars('menu_url'), 'detail' => $result['news']['url'], 'lang' => $this->mlangs->lang_code)), 301);
				exit;
			}
			if($back_link)
			{
				$query = $this->db
					->select("COUNT(*) AS COUNT")
					->from("`".self::CAT."`")
					->where("`id_users_modules`", $this->id_users_modules)->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1);
				
				$query->where("`sort` >=", $result['catalogue']['sort']);
				
				$temp_back = $query->get()->row_array();
				
				$limit = $this->output_settings['module_news_per_page'];
				$page = ceil($temp_back['COUNT']/$limit);
				$menu_url = $this->variables->get_url_vars('menu_url');

				if($page>1)
				{
					$result['catalogue']['back_url'] = $this->router->build_url('menu_page_lang', array('menu_url' => $menu_url, 'page' => $page, 'lang' => $this->mlangs->lang_code));
				}
				else
				{
					$result['catalogue']['back_url'] = $this->router->build_url('menu_lang', array('menu_url' => $menu_url, 'lang' => $this->mlangs->lang_code));
				}
				$navigation_array = $this->variables->get_vars('navigation_array');
				$navigation_array['navigation'][0][] = array(FALSE, $result['catalogue']['name']);
				$this->variables->set_vars('navigation_array', $navigation_array);
			}
			
			$query = $this->db
					->select("A.`image`, B.`name` AS image_name, B.`title` AS image_title, B.`alt` AS image_alt")
					->from("`".self::CP."` AS A")
					->join(	"`".self::CP_DESC."` AS B",
						"B.`".self::ID_CP."` = A.`".self::ID_CP."` && B.`id_langs` = '".$this->mlangs->id_langs."'",
						"LEFT")
					->where("A.`".self::ID_CAT."`", $result['catalogue']['ID'])->order_by("A.`sort`");
			$temp_img = $query->get()->result_array();
			$result['catalogue']['img'] = array();
			foreach($temp_img as $ms)
			{
				$result['catalogue']['img'][] = $ms+array('timage' => $this->img_path.$result['catalogue']['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$result['catalogue']['ID'].'/'.$ms['image']);
			}
			
			$query = $this->db->select("B.`".self::ID_AD."` AS ID, B.`data`, A.`type`, A.`alias`, A.`".self::ID_AD_ALIAS."` AS AD_ID")
					->from("`".self::AD_ALIAS."` AS A")
					->join("`".self::AD."` AS B",
							"B.`".self::ID_CAT."` = '".$result['catalogue']['ID']."' && B.`".self::ID_AD_ALIAS."` = A.`".self::ID_AD_ALIAS."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."' && B.`active` = '1'",
							"INNER")
					->order_by("A.`sort`");
			$temp_add = $query->get()->result_array();
			$result['catalogue']['additional'] = array();
			foreach($temp_add as $ms)
			{
				if($ms['type'] == 'text')
				{
					$result['catalogue']['additional'][] = array('ID' => $ms['ID'], 'data' => $ms['data'], 'type' => $ms['type'], 'alias' => $ms['alias']);
				}
				else
				{
					if($ms['AD_ID'] == 4)
					{
						$result['catalogue']['additional'][] = array('ID' => $ms['ID'], 'data' => $this->manual_path.$result['catalogue']['ID'].'/'.$ms['data'], 'type' => $ms['type'], 'alias' => $ms['alias']);
					}
					if($ms['AD_ID'] == 7)
					{
						$result['catalogue']['additional'][] = array('ID' => $ms['ID'], 'data' => $this->game_path.$ms['data'], 'type' => $ms['type'], 'alias' => $ms['alias']);
					}
				}
			}
		}
		return array('detail', $result);
	}
	
	public function get_settings($output_only)
	{
		/*$query = $this->db
				->select("CONCAT(A.`prefix`, A.`alias`) AS name, A.`alias`, A.`prefix`, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUE."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."` && B.`".self::ID_U_MOD."` = ".$this->id_users_modules,
						"inner")
				->where("A.`module`", self::MODULE);
		if($output_only)
		{
			$query->where("A.`input_output`", 1);
		}
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$this->output_settings[$ms['name']] = $ms['value'];
		}*/
	}
	
	public function get_last_news($limit = 1)
	{
		$query = $this->get_news_collection_query();
		$query = $query->limit($limit);
		return $this->get_news_collection_array($query->get()->result_array());
	}
}
?>