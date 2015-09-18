<?php
Class Massages
{
	private $error_massages = false;
	private $success_massages = false;
	
	private $render = false;
	public function __construct()
	{
	
	}
	
	public function add_error_massage($massage)
	{
		$this->error_massages[] = $massage;
		$this->render = true;
		return $this;
	}

	public function add_success_massage($massage)
	{
		$this->success_massages[] = $massage;
		$this->render = true;
		return $this;
	}
	
	public function add_ajax_error_massage($massage)
	{
		return $this->add_error_massage($massage);
	}
	public function add_ajax_success_massage($massage)
	{
		return $this->add_success_massage($massage);
	}
	
	public function set_massages($toSession = FALSE)
	{
		$massages = array();
		if($this->render && $toSession)
		{
			$massages = $this->_create_massage_array();
			$ci = & get_instance();
			$ci->session->set_flashdata('massages', $massages); 
		}
		else if($this->render)
		{
			$massages = $this->_create_massage_array();
			$ci = & get_instance();
			$ci->template->add_header('massages',$massages,'massages');
		}
		else
		{
			$ci = & get_instance();
			if($massages = $ci->session->flashdata('massages'))
			{
				$ci->template->add_header('massages',$massages,'massages');
			}
		}
		return $this;
	}
	private function _create_massage_array()
	{
		$massages = array();
		if($this->error_massages)
		{
			$massages['error_massages'] = $this->error_massages;
		}
		if($this->success_massages)
		{
			$massages['success_massages'] = $this->success_massages;
		}
		return $massages;
	}
}
?>