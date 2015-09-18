<?php
class Variables
{
	private $vars = array();
	private $url_vars = array();
	
	public function __construct()
	{
		$CI = & get_instance();
		$param = $CI->uri->ruri_to_assoc();
		$this->set_vars('CI', $CI);
		$this->url_vars = $param;
	}
	
	public function get_url_vars($key = FALSE)
	{
		if(!$key)
		{
			return $this->url_vars;
		}
		if(isset($this->url_vars[$key]))
		{
			return $this->url_vars[$key];
		}
		return FALSE;
	}
	
	public function set_vars($key, $value)
	{
		$this->vars[$key] = $value;
		return $this;
	}
	
	public function get_vars($key)
	{
		if(isset($this->vars[$key]))
		{
			return $this->vars[$key];
		}
		return FALSE;
	}
}
?>