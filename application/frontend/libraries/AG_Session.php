<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class AG_Session
{

	private $flashdata_key	= 'flash';
	private $flashdata = array();
	private $userdata	= array();
	private $CI;
	
	private $user_key 	= 'customers';
    
	public function __construct()
	{
		$this->CI =& get_instance();
		/*if(stristr($this->CI->input->server('HTTP_USER_AGENT'), 'Shockwave Flash') || stristr($this->CI->input->server('HTTP_USER_AGENT'), 'Adobe Flash Player'))
		{
			if($this->CI->input->post(md5('US_'.session_name())))
			{
				session_id($this->CI->input->post(md5('US_'.session_name())));
			}
		}*/
		//session_name('GBCSESSION'.ID_USERS);
		session_name('GBCSESSION');
		session_start();

		if(isset($_SESSION[$this->flashdata_key]))
		{
			$this->flashdata = $_SESSION[$this->flashdata_key];
			unset($_SESSION[$this->flashdata_key]);
		}
	}
	
	public function manually_session_start($session_id = FALSE, $session_user_data = FALSE)
	{
		session_name('GBCSESSION');
		if($session_id)
		{
			session_id($session_id);
		}
		session_start();
		if(is_array($session_user_data))
		{
			$this->set_all_userdata($session_user_data);
		}
	}
	
	public function clear_user_data()
	{
		unset($_SESSION[$this->user_key]);
	}
	
	public function clearUserData()
	{
		$this->clear_user_data();
	}
	
	public function set_data($key, $value = NULL, $user_data = FALSE)
	{
		if(is_array($key))
		{
			$key_str = "";
			foreach($key as $ms)
			{
				$key_str .= "[\"".$ms."\"]";
			}
			$UD = '';
			if($user_data)
			{
				$UD = "[\"".$this->user_key."\"]";
			}
			eval('$_SESSION'.$UD.$key_str.' = '.$value.';');
			return TRUE;
		}
		else
		{
			if($user_data)
			{
				$_SESSION[$this->user_key][$key] = $value;
				return TRUE;
			}
			$_SESSION[$key] = $value;
			return TRUE;
		}
	}
	
	public function setData($key, $value = NULL, $user_data = FALSE)
	{
		return $this->set_data($key, $value, $user_data);
	}
	
	public function unset_data($key, $user_data = FALSE)
	{
		if(is_array($key))
		{
			$key_str = "";
			foreach($key as $ms)
			{
				$key_str .= "[\"".$ms."\"]";
			}
			$UD = '';
			if($user_data)
			{
				$UD = "[\"".$this->user_key."\"]";
			}
			eval('if(isset($_SESSION'.$UD.$key_str.')) unset($_SESSION'.$UD.$key_str.');');
			return TRUE;
		}
		else
		{
			if($user_data)
			{
				if(isset($_SESSION[$this->user_key][$key])) unset($_SESSION[$this->user_key][$key]);
				return TRUE;
			}
			if(isset($_SESSION[$key])) unset($_SESSION[$key]);
			return TRUE;
		}
	}
	
	public function unsetData($key, $user_data = FALSE)
	{
		return $this->unset_data($key, $user_data);
	}
	
	public function get_data($key, $user_data = FALSE)
	{
		if(is_array($key))
		{
			$val = FALSE;
			if($user_data)
			{
				if(!isset($_SESSION[$this->user_key]))
				{
					return FALSE;
				}
				$val = $_SESSION[$this->user_key];
			}
			foreach($key as $ms)
			{
				if($val && !isset($val[$ms]))
				{
					return FALSE;
				}
				else
				{
					if($val)
					{
						if(!isset($val[$ms])) return FALSE;
						$val = $val[$ms];
					}
					else
					{
						if(!isset($_SESSION[$ms])) return FALSE;
						$val = $_SESSION[$ms];
					}
				}
			}
			return $val;
		}
		else
		{
			if($user_data)
			{
				if(isset($_SESSION[$this->user_key][$key])) return $_SESSION[$this->user_key][$key];
				return FALSE;
			}
			else
			{
				if(isset($_SESSION[$key]))	return $_SESSION[$key];
				return FALSE;
			}
			return FALSE;
		}
	}
	
	public function getData($key, $user_data = FALSE)
	{
		return $this->get_data($key, $user_data);
	}
	
	public function set_userdata($key, $value)
	{
		return $this->set_data($key, $value, TRUE);
	}
	
	public function set_all_userdata(array $value)
	{
		$_SESSION[$this->user_key] = $value;
	}
	
	public function unset_userdata($key)
	{
		return $this->unset_data($key, TRUE);
	}
	
	public function userdata($key)
	{
		return $this->get_data($key, TRUE);
	}
	
	public function all_userdata()
	{
		if(isset($_SESSION[$this->user_key]))
		{
			return $_SESSION[$this->user_key];
		}
		return array();
	}
	
	public function set_flashdata($key, $data)
	{
		$_SESSION[$this->flashdata_key][$key] = $data;
		$this->flashdata[$key] = $data;
	}
	
	public function flashdata($key)
	{
		if(isset($this->flashdata[$key]))
		{
			return $this->flashdata[$key];
		}
		return FALSE;
	}
	
	public function keep_flashdata($key = FALSE)
	{
		if($key)
		{
			if(isset($this->flashdata[$key]))
			{
				$_SESSION[$this->flashdata_key][$key] = $this->flashdata[$key];
				return TRUE;
			}
			return FALSE;
		}
		$_SESSION[$this->flashdata_key] = $this->flashdata;
		return TRUE;
	}
    
    /**
     * Create string for Javascript containing cookie data
     *
     * @access    public
     * @return    string
     */
    function get_js_session()
    {
        $output = '"'.md5('US_'.session_name()).'" : "'.session_id().'"';
        return $output;        
    }
}		
?>