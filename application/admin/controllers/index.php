<?php
class Index extends AG_Controller
{
	function __construct()
		{
			parent::__construct();
			//$this->_setData('title','Adminpanel');
		}
	function index()
		{

			$this->template->add_template('intex_template', array());
		}
}
?>