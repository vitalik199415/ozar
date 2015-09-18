<?php
class Mreviews extends AG_Model
{
	const RV = 'm_reviews';
	const ID_RV = 'id_m_reviews';

	const U_MOD				= 'users_modules';
	const ID_U_MOD			= 'id_users_modules';

	const MODULE = 'reviews';
	
	protected $id_users_modules = FALSE;
	protected $settings = FALSE;

	protected $module_settings = FALSE;

	function __construct()
	{
		parent::__construct();
	}
	
	public function _init($id_users_modules, $settings = FALSE)
	{
		$this->id_users_modules = $id_users_modules;
		$this->settings = $settings;

		$this->load->model('reviews/mreviews_settings');
		$this->module_settings = $this->mreviews_settings->get_settings($this->id_users_modules);
	}
	
	public function get_reviews_collection()
	{
		$array = array('id_users_modules' => $this->id_users_modules);
		$this->db
				->select("COUNT(*) AS COUNT")
				->from("`".self::RV."` AS A")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1);
		$count = $this->db->get()->row_array();
		$count = $count['COUNT'];
		if($count>0)
		{
			$page = 1;
			if($this->variables->get_url_vars('page') == 1) redirect($this->router->build_url('menu_lang', array('menu_url' => $this->variables->get_url_vars('menu_url'), 'lang' => $this->langs->id_langs)) ,301);
			if((int) $this->variables->get_url_vars('page')>0)
			{
				$page = (int) $this->variables->get_url_vars('page');
			}
			
			$query = $this->get_reviews_collection_query();
			$pages_array = $this->set_limit($query, $count, $page);
			$array = $this->get_reviews_collection_array($query->get()->result_array(), $pages_array);

			return $array;
		}
		else
		{
			return $array;
		}
	}
	
	public function get_reviews_collection_query()
	{
		$query = $this->db
				->select("A.`".self::ID_RV."` AS ID, A.`name`, A.`review`, A.`answer`, A.`admin_name`, M.`menu_id`, A.`is_answer`, M.`menu_url`")
				->from("`".self::RV."` AS A")
				->join(	"(SELECT B.`id_m_menu` AS menu_id, B.`url` AS menu_url, A.`id_users_modules` FROM `users_menu_modules` AS A USE INDEX (`id_users_modules`), `m_menu` AS B USE INDEX (`PRIMARY`) WHERE A.`".self::ID_USERS."` = ".$this->id_users." && A.`id_users_modules` = '".$this->id_users_modules."' && A.`base_module` = 1 && B.`id_m_menu` = A.`id_m_menu` LIMIT 1) AS M",
						"M.`id_users_modules` = A.`id_users_modules`",
						"LEFT")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->order_by("A.`sort`", 'DESC');
		return $query;
	}
	
	public function get_reviews_collection_array($result = array(), $pages_data = FALSE)
	{
		foreach($result as $ms)
		{
			$menu_url = $ms['menu_id'];
			if(trim($ms['menu_url']) != '')
			{
				$menu_url = trim($ms['menu_url']);
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
		return array('reviews' => $result, 'pages' => $pages_array, 'id_users_modules' => $this->id_users_modules);
	}
	
	protected function set_limit($query, $count, $page = 1)
	{
		$limit = $this->module_settings['reviews_count_to_page'];
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
	
	public function save_review($id_users_modules, $POST)
	{
		$this->_init($id_users_modules);

		$this->db->select("A.`alias`")
				->from("`".self::U_MOD."` AS A")
				->join("`modules` AS B",
						"B.`id_modules` = A.`id_modules` && B.`alias` = '".self::MODULE."'",
						"INNER")
				->where("A.`".self::ID_U_MOD."`", $id_users_modules)->where("A.`active`", 1);
		$result = $this->db->get()->row_array();
		if(count($result) > 0)
		{
			if(!isset($POST['mail_notification']))
			{
				$POST['mail_notification'] = 1;
			}

			$this->db->select("MAX(`sort`) AS MAX")->from("`".self::RV."`")->where("`".self::ID_U_MOD."`", $id_users_modules);
			$max_sort = $this->db->get()->row_array();
			$max_sort = $max_sort['MAX'];
			if(is_null($max_sort)) $max_sort = 1; else $max_sort++;

			$this->db->trans_start();
			$id = $this->sql_add_data(array('id_users_modules' => $id_users_modules, 'sort' => $max_sort, 'name' => $POST['name'], 'email' => $POST['email'], 'review' => $POST['review'], 'mail_notification' => $POST['mail_notification'], 'active' => $this->module_settings['reviews_publication_immediately'], self::ID_LANGS => $this->mlangs->id_langs))->sql_update_date()->sql_using_user()->sql_save(self::RV);
			if($id && $id > 0)
			{
				$this->db->trans_complete();

				if($this->db->trans_status()) 
				{
					if($this->module_settings['reviews_admin_notice'] == 1)
					{
						$this->send_admin_notification($POST + array('module_alias' => $result['alias']));
					}
					return $id;
				}
			}
			else
			{
				return FALSE;
			}

		}
		return FALSE;
	}

	protected function send_admin_notification($data)
	{
		$admin_data['name'] = $this->module_settings['reviews_admin_name'];
		$admin_data['email'] = $this->module_settings['reviews_admin_email'];
		$admin_data['site'] = $_SERVER['SERVER_NAME'];

		$data['site'] = $admin_data['site'];

		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;

		if($admin_data['email'] != '')
		{
			$letter_html = $this->load->view('reviews/letters/'.$this->mlangs->language.'/new_review', array('data' => $data), TRUE);
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('no-reply@'.$admin_data['site'], $admin_data['site']);
			$this->email->to($admin_data['email']);
			$this->email->subject('New comment in module '.$data['module_alias'].'.');
			$this->email->message($letter_html);
			$this->email->send();
			$this->email->clear();
		}
		return TRUE;
	}
}
?>