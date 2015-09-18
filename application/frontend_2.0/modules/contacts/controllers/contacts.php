<?php
class Contacts extends AG_Controller
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
		$this->template->add_js('jquery.gbc_contacts', 'modules_js/contacts');
		$this->mlangs->load_language_file('modules/contacts');
		$this->_init($data);
		$this->load->model('mcontacts');
		if($this->id_users_modules)
		{
			$this->mcontacts->_init($this->id_users_modules, $this->settings);
			$view_array = $this->mcontacts->get_contacts_collection();
			$this->template->add_view_to_template('center_block', 'contacts/contacts', $view_array + array('settings' => $this->settings));
		}
	}
	
	public function send_message()
	{
		$this->mlangs->load_language_file('modules/contacts');
		$id = intval($this->input->post('id'));
		if($id > 0 && strlen($this->input->post('email'))>5 && strlen($this->input->post('name'))>5 && strlen($this->input->post('text'))>9)
		{
			$id = intval($this->input->post('id'));
			$captcha = trim($this->input->post('captcha'));
			if(isset($_SESSION['captcha_keystring_'.$id]) && $_SESSION['captcha_keystring_'.$id] == $captcha)
			{
				$this->load->model('contacts/mcontacts');
				$this->mcontacts->write_admin($id, $this->input->post());
				if(isset($_SESSION['captcha_keystring_'.$id])) unset($_SESSION['captcha_keystring_'.$id]);
				echo json_encode(array('status' => 1, 'id' => $id, 'success' => $this->lang->line('contacts_succes_send_message'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring_'.$id.'&rand='.rand(1000, 9999).'" id="contacts_form_captcha_img_'.$id.'">'));
			}
			else
			{
				if(isset($_SESSION['captcha_keystring_'.$id])) unset($_SESSION['captcha_keystring_'.$id]);
				echo json_encode(array('status' => 0, 'id' => $id, 'errors' => $this->lang->line('contacts_error_wrong_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring_'.$id.'&rand='.rand(1000, 9999).'" id="contacts_form_captcha_img_'.$id.'">'));
			}
		}
		else
		{
			if(isset($_SESSION['captcha_keystring_'.$id])) unset($_SESSION['captcha_keystring_'.$id]);
			echo json_encode(array('status' => 0, 'id' => $id, 'errors' => $this->lang->line('contacts_error_wrong_data'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&session_key=captcha_keystring_'.$id.'&rand='.rand(1000, 9999).'" id="contacts_form_captcha_img_'.$id.'">'));
		}
	}
}
?>