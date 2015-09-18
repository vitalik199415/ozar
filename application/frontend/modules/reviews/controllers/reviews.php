<?php
class Reviews extends AG_Controller
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
		$this->template->add_js('jquery.gbc_reviews', 'modules_js/reviews');
		$this->mlangs->load_language_file('modules/reviews');
		$this->_init($data);
		$this->load->model('reviews/mreviews');
		if($this->id_users_modules)
		{
			$this->mreviews->_init($this->id_users_modules, $this->settings);
			$view_array = $this->mreviews->get_reviews_collection();
			$this->template->add_view_to_template('center_block', 'reviews/reviews', $view_array + array('settings' => $this->settings));
		}
	}
	
	public function save_review()
	{
		$this->mlangs->load_language_file('modules/reviews');
		$id_users_modules = intval($this->input->post('id_users_modules'));
		if($id_users_modules > 0 && strlen($this->input->post('email'))>5 && strlen($this->input->post('name'))>5 && strlen($this->input->post('review'))>9)
		{
			$captcha = trim($this->input->post('captcha'));
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $captcha)
			{
				$this->load->model('reviews/mreviews');
				if($this->mreviews->save_review($id_users_modules, $this->input->post()))
				{
					if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
					echo json_encode(array('status' => 1, 'success' => $this->lang->line('reviews_succes_send_message'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring'.'&rand='.rand(1000, 9999).'" id="reviews_form_captcha_img">'));
				}
				else
				{
					if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
					echo json_encode(array('status' => 0, 'errors' => 'Server Error', 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring'.'&rand='.rand(1000, 9999).'" id="reviews_form_captcha_img">'));
				}
			}
			else
			{
				if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
				echo json_encode(array('status' => 0, 'errors' => $this->lang->line('reviews_error_wrong_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring'.'&rand='.rand(1000, 9999).'" id="reviews_form_captcha_img">'));
			}
		}
		else
		{
			if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
			echo json_encode(array('status' => 0, 'errors' => $this->lang->line('reviews_error_wrong_data'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring'.'&rand='.rand(1000, 9999).'" id="reviews_form_captcha_img">'));
		}
	}
}
?>