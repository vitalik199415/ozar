<?php
class Mphoto_gallery extends AG_Model
{
	const MID 			= 3;
	const GALL 			= 'm_photo_gallery_albums';
	const ID_GALL 		= 'id_m_photo_gallery_albums';
	const GALL_DESC 	= 'm_photo_gallery_albums_description';
	
	const PGP			= 'm_photo_gallery_photos';
	const ID_PGP		= 'id_m_photo_gallery_photos';
	const PGP_DESC		= 'm_photo_gallery_photos_description';
	const ID_PGP_DESC	= 'id_m_photo_gallery_photos_description';
	
	
	const S_ALIAS = 'users_modules_settings_alias';
	const ID_S_ALIAS = 'id_users_modules_settings_alias';
	const S_VALUE = 'users_modules_settings_value';
	const MODULE = 'm_photo_gallery';
	
	const IMG_FOLDER = '/photo_album/';
	
	protected $img_path = FALSE;
	
	protected $id_users_modules = FALSE;
	protected $detail = FALSE;
	protected $settings = FALSE;
	
	protected $output_settings = array('module_album_per_page' => 8);

	function __construct()
	{
		parent::__construct();
	}
	
	public function _init($id_users_modules, $settings = FALSE)
	{
		$this->id_users_modules = (int) $id_users_modules;
		$this->img_path = IMG_PATH.ID_USERS.'/media/module_'.$id_users_modules.self::IMG_FOLDER;
		if($detail = $this->variables->get_url_vars('detail')) $this->detail = $detail;
		
		$this->settings = $settings;
		$this->get_settings(TRUE);
	}
	
	public function get_album_collection()
	{
		if($this->detail) return $this->get_gall_detail();
		$query = $this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::GALL."` AS A")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1);
		$count = $query->get()->row_array();
		$count = $count['COUNT'];
		if($count == 1)
		{
			$query = $this->db
				->select("A.`".self::ID_GALL."` AS ID")
				->from("`".self::GALL."` AS A")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->limit(1);
			$ID = $query->get()->row_array();
			$ID = $ID['ID'];
			return $this->get_gall_detail($ID, FALSE);
		}
		else if($count > 1)
		{
			$page = 1;
			if($this->variables->get_url_vars('page') == 1) redirect($this->router->build_url('menu_lang', array('menu_url' => $this->variables->get_url_vars('menu_url'), 'lang' => $this->langs->id_langs)) ,301);
			if((int) $this->variables->get_url_vars('page')>0)
			{
				$page = (int) $this->variables->get_url_vars('page');
			}
			
			$query = $this->get_album_collection_query();
			
			$pages_array = $this->set_limit($query, $count, $page);
			
			$array = $this->get_album_collection_array($query->get()->result_array(), $pages_array);
			return array('short', $array);
		}
		else
		{
			return FALSE;
		}
	}
	
	protected function get_album_collection_query()
	{
		$query = $this->db
				->select("A.`".self::ID_GALL."` AS ID, A.`url`, B.`name`, B.`short_description`, C.`image`, D.`name` AS image_name, D.`title` AS image_title, D.`alt` AS image_alt, M.`menu_id`, M.`menu_url`")
				->from("`".self::GALL."` AS A")
				->join(	"(SELECT B.`id_m_menu` AS menu_id, B.`url` AS menu_url, A.`id_users_modules` FROM `users_menu_modules` AS A USE INDEX (`id_users_modules`), `m_menu` AS B USE INDEX (`PRIMARY`) WHERE A.`".self::ID_USERS."` = ".$this->id_users." && A.`id_users_modules` = '".$this->id_users_modules."' && A.`base_module` = 1 && B.`id_m_menu` = A.`id_m_menu` LIMIT 1) AS M",
						"M.`id_users_modules` = A.`id_users_modules`",
						"LEFT")
				->join(	"`".self::GALL_DESC."` AS B",
						"B.`".self::ID_GALL."` = A.`".self::ID_GALL."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->join(	"`".self::PGP."` AS C",
						"C.`".self::ID_GALL."` = A.`".self::ID_GALL."` && C.`".self::ID_PGP."` = (SELECT `".self::ID_PGP."` FROM `".self::PGP."` WHERE `".self::ID_GALL."` = A.`".self::ID_GALL."` ORDER BY `sort` LIMIT 1)",
						"LEFT")
				->join(	"`".self::PGP_DESC."` AS D",
						"D.`".self::ID_PGP."` = C.`".self::ID_PGP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->order_by("A.`sort`");
		//echo $query->_compile_select().'<br><br>';
		return $query;
	}
	
	protected function get_album_collection_array($result = array(), $pages_data = FALSE)
	{
		$array = array();
		foreach($result as $ms)
		{
			$menu_url = $ms['menu_id'];
			if(strlen($ms['menu_url']) > 2)
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
		return array('albums' => $array, 'pages' => $pages_array);
	}
	
	protected function set_limit($query, $count, $page = 1)
	{
		$limit = $this->output_settings['module_album_per_page'];
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
	
	public function get_gall_detail($id = FALSE, $back_link = TRUE)
	{
		$result = array();
		$query = $this->db
				->select("A.`".self::ID_GALL."` AS ID, A.`url`, A.`sort`, B.`name`, B.`full_description`")
				->from("`".self::GALL."` AS A")
				->join(	"`".self::GALL_DESC."` AS B",
						"B.`".self::ID_GALL."` = A.`".self::ID_GALL."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->limit(1);
		if($id)
		{
			$query->where("(A.`".self::ID_GALL."` = '".$id."')", NULL, FALSE);
		}
		else
		{
			$query->where("(A.`".self::ID_GALL."` = '".$this->detail."' OR A.`url` = '".$this->detail."')", NULL, FALSE);
		}
		$result['album'] = $query->get()->row_array();
		if(count($result['album'])>0)
		{
			if($this->detail == $result['album']['ID'] && strlen($result['album']['url'])>2)
			{
				redirect($this->router->build_url('menu_detail_lang', array('menu_url' => $this->variables->get_url_vars('menu_url'), 'detail' => $result['album']['url'], 'lang' => $this->mlangs->lang_code)), 301);
				exit;
			}
				
			if($back_link)
			{
				$query = $this->db
					->select("COUNT(*) AS COUNT")
					->from("`".self::GALL."`")
					->where("`id_users_modules`", $this->id_users_modules)->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->where("`sort` >=", $result['album']['sort']);
				$temp_back = $query->get()->row_array();
				
				$limit = $this->output_settings['module_album_per_page'];
				$page = ceil($temp_back['COUNT']/$limit);
				$menu_url = $this->variables->get_url_vars('menu_url');

				if($page>1)
				{
					$result['album']['back_url'] = $this->router->build_url('menu_page_lang', array('menu_url' => $menu_url, 'page' => $page, 'lang' => $this->mlangs->lang_code));
				}
				else
				{
					$result['album']['back_url'] = $this->router->build_url('menu_lang', array('menu_url' => $menu_url, 'lang' => $this->mlangs->lang_code));
				}
				$navigation_array = $this->variables->get_vars('navigation_array');
				$navigation_array['navigation'][0][] = array(FALSE, $result['album']['name']);
				$this->variables->set_vars('navigation_array', $navigation_array);
			}	
			
			$query = $this->db
					->select("A.`image`, B.`name` AS image_name, B.`title` AS image_title, B.`alt` AS image_alt")
					->from("`".self::PGP."` AS A")
					->join(	"`".self::PGP_DESC."` AS B",
						"B.`".self::ID_PGP."` = A.`".self::ID_PGP."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
					->where("A.`".self::ID_GALL."`", $result['album']['ID'])->order_by("A.`sort`");
			$temp_img = $query->get()->result_array();
			$result['album']['img'] = array();
			foreach($temp_img as $ms)
			{
				$result['album']['img'][] = $ms+array('timage' => $this->img_path.$result['album']['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$result['album']['ID'].'/'.$ms['image']);
			}
		}
		else
		{
			show_404();
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
	
	public function get_last_album($limit = 1)
	{
		$query = $this->get_album_collection_query();
		$query = $query->limit($limit);
		return $this->get_album_collection_array($query->get()->result_array());
	}
}
?>