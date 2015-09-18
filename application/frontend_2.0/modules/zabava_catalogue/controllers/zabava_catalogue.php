<?php
class Zabava_catalogue extends AG_Controller
{
	protected $id_users_modules = FALSE;
	protected $settings = FALSE;
	
	function __construct()
		{
			parent::__construct();
		}
	protected function _init($data)
	{
		if($data)
		{
			if(isset($data['id_users_modules']))
			{
				$this->id_users_modules = $data['id_users_modules'];
			}
			if(isset($data['settings']))
			{
				$this->settings = $data['settings'];
			}
		}
	}
	
	public function index($data)
	{
		$this->_init($data);
		$this->load->model('zabava_catalogue/mzabava_catalogue');
		if($this->id_users_modules)
		{
			$this->mzabava_catalogue->_init($this->id_users_modules, $this->settings);
			$data = $this->mzabava_catalogue->get_catalogue_collection();
			$this->template->add_view_to_template('center_block', 'zabava_catalogue/zabava_catalogue_'.$data[0], $data[1]+array('settings' => $this->settings));
		}
	}
}
?>