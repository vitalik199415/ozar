<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
 
/* load the MX_Loader class */
require APPPATH."third_party/MX/Controller.php";
 
class AG_Controller extends MX_Controller
{
	function __construct($base_load = true)
	{
		parent::__construct();
		$CI = & get_instance();
		$CI->redirect = FALSE;
	}
}