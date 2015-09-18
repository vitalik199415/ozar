<?php
class Mlangs extends AG_Model
{
	const LANGS = 'langs';
	
	const USERS_LANGS = 'users_langs';
	const ID_USERS_LANGS = 'id_users_langs';
	
	public $current_lang = FALSE;
	public $id_langs = FALSE;
	public $lang_code = FALSE;
	public $language = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->current_lang = $this->get_current_lang();
		$this->load_language_file('modules/base');
	}
	
	public function get_site_languages($short = false)
	{
		$array = array();
		$result = $this->db->select("A.`".self::ID_LANGS."` AS ID, A.`code`, A.`name`, A.`short_name`, A.`language`, B.`default`")
				->from("`".self::LANGS."` AS A")
				->join("`".self::USERS_LANGS."` AS B", 
						"B.`".self::ID_USERS."` = '".$this->id_users."' && B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."` && B.`on` = 1 && B.`active` = 1", 
						"INNER")
				->where("A.`active`", 1)
				->order_by("B.`sort`");
		$result = $result->get()->result_array();
		$current_lang = $this->get_current_lang();
		foreach($result as $ms)
		{
			if($ms['code'] == $current_lang['code'])
			{
				$array[] = $ms + array('href' => FALSE);
			}
			else
			{
				if($ms['default'] == 1)
				{
					$array[] = $ms + array('href' => $this->langs_href());
				}
				else
				{
					$array[] = $ms + array('href' => $this->langs_href($ms['code']));
				}
			}
		}
		return $array;
	}
	
	public function langs_href($lang = FALSE)
	{
		$url = '';
		$segment_array = $this->uri->segment_array();
		$ruri = $this->uri->ruri_to_assoc();
		
		if(isset($ruri['lang']))
		{
			$scount = count($segment_array)-1;
		}
		else
		{
			$scount = count($segment_array);
		}
		
		for($i = 1; $i <= $scount; $i++)
		{
			$url .= '/'.$segment_array[$i];
		}
		
		if($lang)
		{
			$url .= '/lang-'.$lang;
		}
		$url = substr($url,1);
		return base_url().$url;
	}
	
	public function get_current_lang()
	{
		if($this->current_lang)
		{
			$this->id_langs = $this->current_lang['ID'];
			$this->language = $this->current_lang['language'];
			return $this->current_lang;
		}
		$URL = $this->uri->ruri_to_assoc(3);
		if(isset($URL['lang']))
		{
			$query = $this->db->select("B.`".self::ID_LANGS."` AS ID, B.`code`, B.`name`, A.`default`, B.`language`")
				->from("`".self::USERS_LANGS."` AS A")
				->join(	"`".self::LANGS."` AS B", 
						"B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
						"INNER")
				->where("A.`active`", 1)->where("A.`on`", 1)->where("A.`".self::ID_USERS."`", $this->id_users)->where("B.`code`", $URL['lang'])
				->limit(1);

			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				$this->current_lang = $result;
				$this->id_langs = $this->current_lang['ID'];
				$this->lang_code = $this->current_lang['code'];
				$this->language = $this->current_lang['language'];
				if($result['default'] == 1)
				{
					$this->lang_code = FALSE;
					$url = $this->input->server('REQUEST_URI');
					$url = str_replace('/lang-'.$URL['lang'], '', $url);
					redirect($url, 'location', 301);
					exit;
				}
				return $this->current_lang;
			}
			else
			{
				$url = $this->input->server('REQUEST_URI');
				$def_lang = $this->get_default_user_lang();
				$def_lang = $def_lang['code'];
				$url = str_replace('/lang-'.$URL['lang'], '', $url);
				redirect($url, 'location', 301);
				exit;
			}
		}
		else
		{
			return $this->get_default_user_lang();
		}
	}
	
	public function get_default_user_lang()
	{
		$query = $this->db->select("B.`".self :: ID_LANGS."` AS ID, B.`code`, B.`name`, A.`default`, B.`language`")
			->from("`".self :: USERS_LANGS."` AS A")
			->join(	"`".self :: LANGS."` AS B", 
					"B.`".self :: ID_LANGS."` = A.`".self :: ID_LANGS."`",
					"INNER")
			->where("A.`active`", 1)->where("A.`on`", 1)->where("A.`".self :: ID_USERS."`", $this->id_users)
			->order_by("A.`default`", "DESC")->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$this->current_lang = $result;
			$this->id_langs = $this->current_lang['ID'];
			//$this->lang_code = $this->current_lang['code'];
			$this->language = $this->current_lang['language'];
			return $this->current_lang;
		}
	}
	
	public function load_language_file($file, $language = FALSE)
	{
		if($language)
		{
			$this->lang->load($file, $language);
			return $this;
		}
		$this->lang->load($file, $this->language);
		return $this;
	}
}
?>