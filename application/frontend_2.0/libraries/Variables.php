<?php
class Variables
{
	private $vars = array();
	private $url_vars = array();

	protected $additional_url_vars = array(
		'limit' => FALSE,
		'currency' => FALSE
	);

	public function __construct()
	{
		$CI = & get_instance();
		$param = $CI->uri->ruri_to_assoc();
		$this->set_vars('CI', $CI);
		$this->url_vars = $param;

		foreach($param as $key => $ms)
		{
			if(isset($this->additional_url_vars[$key]))
			{
				$this->set_additional_url_vars($key, $ms);
			}
		}
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

	public function get_additional_url_vars($key = FALSE)
	{
		if(!$key)
		{
			return $this->additional_url_vars;
		}

		if(isset($this->additional_url_vars[$key]))
		{
			return $this->additional_url_vars[$key];
		}

		return FALSE;
	}

	public function set_additional_url_vars($ar, $vl = FALSE)
	{
		if(is_array($ar))
		{
			foreach($ar as $key => $ms)
			{
				$this->additional_url_vars[$key] = $ms;
			}
			return TRUE;
		}

		if(is_string($ar) && $vl)
		{
			$this->additional_url_vars[$ar] = $vl;
			return TRUE;
		}

		return FALSE;
	}

	public function build_additional_url_params($params = array())
	{
		$v_params = $this->additional_url_vars;
		$url = '';
		foreach($params as $key => $ms)
		{
			$url .= '/'.$key.'/'.$ms;
			if(isset($v_params[$key])) unset($v_params[$key]);
		}

		foreach($v_params as $key => $ms)
		{
			if($ms)
			{
				$url .= '/'.$key.'/'.$ms;
			}
		}
		if($url != '') $url = substr($url, 1);
		return $url;
	}
}
?>