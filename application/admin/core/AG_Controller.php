<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Controller.php";

class AG_Controller extends MX_Controller
{
	private $_navigation = array();
	private $_back_to_tab = '';
	function __construct($base_load = true)
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$CI = & get_instance();
		$CI->redirect = FALSE;
		$this->load->helper('url');

			$this->load->model('login/mlogin');
			if(!$this->mlogin->isAutorize())
			{
				redirect('login');
			}
			else
			{
				$this->config->load('menu_config');
				if($base_load) $this->_setBaseHtml();
			}

		if($this->uri->segment(1) != 'login') {
			$module = FALSE;
			if ($this->uri->segment(2)) {
				$module = $this->uri->segment(2);
			}
			$action = FALSE;
			if ($this->uri->segment(3)) {
				$action = $this->uri->segment(3);
			}
			if ($module) {
				if (!$this->check_system_permission($module, $action)) {
					echo "You don't have permission!";
					die;
				}
			}
		}
	}

	private function check_system_permission($module, $action = FALSE) {
		if($this->session->get_data('primary') || $this->session->get_data('super')) {
			return TRUE;
		}
		$permissions_modules = $this->session->get_data('system_perm');
		$permissions_actions = $this->session->get_data('system_actions');
		if($action) {
			if(isset($permissions_actions[$module][$action])) {
				return TRUE;
			}
		} else {
			if(isset($permissions_modules[$module])) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function _setBaseHtml()
	{
		$this->template->add_css('admincss');
		$this->template->add_css('overlay', 'jquery_tools/overlay');
		$this->template->add_css('gbc_dropdown_menu');

		$this->template->add_js('jquery-1.7.1.min');
		$this->template->add_js('jquery-ui.min');
		$this->template->add_js('jquery.tools.min');
		//$this->template->add_js('jquery.multi-ddm');
		$this->template->add_js('jquery.gbc_dropdown_menu');
		$this->template->add_js('jquery.gbcmassages');
		$this->template->add_js('functions');
		$this->template->add_js('modules_js/catalogue/products/jquery.gbc_products_albums');
		$this->template->add_js('modules_js/catalogue/products/jquery.gbc_products_detail');


		$this->template->add_js('jquery.datepick.min', 'datepicker');
		$this->template->add_js('jquery.datepick-ru', 'datepicker');
		$this->template->add_css('jquery.datepick','datepicker');

		$this->template->add_js_code('
		$(document).ready(function() {
			start_js_func();
		});
		');

		$this->load->model('sys/mpermissions');
		$arr['menu'] = $this->mpermissions->get_menu();
		$this->template->add_base_header('menu',  $arr,'menu');

		//$this->template->add_base_header('menu',  array(),'menu');
		$this->template->add_base_header('header',array(),'head');


		$this->template->add_base_header('navigation',array(),'navigation');
		$this->_add_navigation('Главная', site_url());

		$this->template->add_base_footer('footer',array(),'footer');
	}

	public function	_add_navigation($name, $href = false, $options = array())
	{
		$this->template->add_navigation($name, $href , $options);
		return $this;
	}
	public function _render_navigation($name = false, $href = false, $options = array())
	{
		return $this->_add_navigation($name, $href, $options);
	}
	public function _redirect($url = '')
	{
		if(trim($url)!='')
		{
			$CI = & get_instance();
			$CI->redirect = $url.$this->_back_to_tab;
		}
	}
	public function _back_to_tab($TR = TRUE)
	{
		if($TR)
		{
			if(isset($_GET['tab']))
			{
				$this->_back_to_tab = $_GET['tab'];
			}
		}
		else
		{
			$this->_back_to_tab = '';
		}
	}
}