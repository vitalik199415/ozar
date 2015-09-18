<?php 
class Photo_gallery extends AG_Controller
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
		$this->load->model('mphoto_gallery');
		if($this->id_users_modules)
		{
			$this->mphoto_gallery->_init($this->id_users_modules, $this->settings);
			$data = $this->mphoto_gallery->get_album_collection();
			$this->template->add_view_to_template('center_block', 'photo_gallery/albums_'.$data[0], $data[1]+array('settings' => $this->settings));
		}
	}
}
?>