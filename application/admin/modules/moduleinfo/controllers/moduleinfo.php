<?php
class Moduleinfo extends AG_Controller
{
	private $module_alias = FALSE;
	
	function __construct()
	{
		parent::__construct();
		
		$URI = $this->uri->uri_to_assoc(2);
		if(isset($URI['mid']) && intval($URI['mid'])>0)
		{
			$ID = intval($URI['mid']);
			$this->load->model('site_modules/msite_modules');
			if($module = $this->msite_modules->checkIssetModule($ID))
			{
				$this->template->add_title('Модули сайта - '.$module['alias'].' - ');
				$this->template->add_navigation('Модули сайта', set_url('site_modules'))->add_navigation($module['alias'], set_url('*/*/*/'));
				$this->module_alias = $module['Malias'];
			}
			else
			{
				$this->messages->add_error_message('Module error! Module is not exist!');
				$this->_redirect(set_url('site_modules'));
			}
		}
		else
		{
			$this->messages->add_error_message('Module error!');
			$this->_redirect(set_url('site_modules'));
		}
	}
	public function mid()
	{
		if($this->module_alias)
		{
			$method = $this->uri->segment(4, 'index');
			echo modules::run($this->module_alias.'/'.$method);
		}	
	}
}
?>