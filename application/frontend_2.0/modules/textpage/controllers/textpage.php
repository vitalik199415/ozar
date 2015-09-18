<?php
class Textpage extends AG_Controller
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
	
	public function index($data = FALSE)
	{
		$this->_init($data);
		$this->load->model('mtextpage');
		if($this->id_users_modules)
		{
			$this->mtextpage->_init($this->id_users_modules, $this->settings);
			$view_array = $this->mtextpage->get_textpage_collection();
			$this->template->add_view_to_template('center_block', 'textpage/textpage', $view_array + array('settings' => $this->settings));
		}
	}
}
?>