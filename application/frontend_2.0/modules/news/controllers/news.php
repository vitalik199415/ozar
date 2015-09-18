<?php
class News extends AG_Controller
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
		$this->load->model('mnews');
		if($this->id_users_modules)
		{
			$this->mnews->_init($this->id_users_modules, $this->settings);
			$data = $this->mnews->get_news_collection();
			if(count($data) > 0 && count($data[1])){
					$this->template->add_view_to_template('center_block', 'news/news_'.$data[0], $data[1]+array('settings' => $this->settings));
			}
		}
	}
}
?>