<?php
class Customers extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/login');
		$this->mlangs->load_language_file('modules/orders_customers');
	}
	
	public function sendmail()
	{
		echo "Sending mail...<br><br>";
		
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		
		$this->load->library('email');
		$this->email->initialize($config);
		
		$this->email->sender('noreply@stato.com.ua');
		$this->email->from('noreply@stato.com.ua', 'Andrey Goyan');
		$this->email->to('andrey.goyan@gmail.com');
		
		$this->email->reply_to('leonche@bk.ru', 'Andrey Goyan');
		$this->email->subject('Some test letter v3.0!');
		$this->email->message('This is some big test letter for some big test to many tests!!');
		//echo var_dump($this->email->_headers);
		$this->email->send();
		echo $this->email->print_debugger();
		$this->email->clear();
	}
	
	public function index()
	{
		$this->template->add_js('jquery.gbc_customers', 'modules_js/customers');
		$this->template->add_view_to_template('customers_block', 'customers/customers_outside_block', array('customers_block_id' => 'customers_block'));
		$this->template->add_view_to_template('customers_block_inside', 'customers/customers_block', array());
		$this->template->add_view_to_template('customers_block', 'customers/customers_outside_block_js', array());
	}
	
	public function login_form()
	{
		$this->template->add_template_ajax('customers/login_form', array('login_form_id' => 'customers_login_form'));
		$this->template->add_template_ajax('customers/login_form_js', array('login_form_id' => 'customers_login_form'));
	}
	
	public function login()
	{
		if(isset($_POST) && count($_POST) > 0)
		{
			$email = trim($this->input->post('email'));
			$password = trim($this->input->post('password'));
			if($email && $password && strlen($email) > 5 && strlen($password) > 5)
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('email','E-Mail','trim|required|valid_email');
				if($this->form_validation->run() === TRUE)
				{
					$this->load->model('customers/mcustomers');
					$result = $this->mcustomers->login(trim($email), trim($password));
					switch($result)
					{
						case 0:
							echo json_encode(array('status' => 0, 'errors' => $this->lang->line('login_error_wrong_login_or_pass')));
						break;
						case 1:
							echo json_encode(array('status' => 0, 'errors' => $this->lang->line('login_error_not_active')));
						break;
						case 2:
							echo json_encode(array('status' => 1, 'success' => $this->lang->line('login_success')));
						break;
					}
				}
				else
				{
					echo json_encode(array('status' => 0, 'errors' => validation_errors()));
				}
			}
			else
			{
				echo json_encode(array('status' => 0, 'errors' => '<p>Wrong login data!</p> '));
			}
		}
		else
		{
			echo json_encode(array('status' => 0, 'errors' => '<p>Login data is empty!</p>'));
		}
	}
	
	public function logout()
	{
		$this->session->unset_userdata('CUSTOMER');
		$this->session->unset_userdata('customer_id');
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function registration_form()
	{
		$this->mlangs->load_language_file('modules/orders_customers');
		$this->load->model('customers/mcustomers_settings');
		$registration_settings = $this->mcustomers_settings->get_settings();
		$this->template->add_template_ajax('customers/registration_form', array('registration_settings' => $registration_settings, 'registration_form_id' => 'customers_registration_form'));
		$this->template->add_template_ajax('customers/registration_form_js', array('registration_settings' => $registration_settings, 'registration_form_id' => 'customers_registration_form'));
	}
	
	public function registration()
	{
		if(isset($_POST) && count($_POST) > 0)
		{
			$this->mlangs->load_language_file('modules/orders_customers');
			$this->load->model('customers/mcustomers');
			
			if($this->session->userdata('customer_id') > 0)
			{
				$result = $this->mcustomers->save($this->session->userdata('customer_id'));
				switch($result)
				{
					case 0:
						echo json_encode(array('status' => 0, 'errors' => $this->lang->line('c_o_error_enter_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_registration_captcha_img">'));
					break;
					case 2:
						echo json_encode(array('status' => 1, 'errors' => $this->lang->line('c_o_error_customer_edit')));
					break;
					case 3:
						echo json_encode(array('status' => 2, 'success' => $this->lang->line('c_o_success_customer_edit'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_registration_captcha_img">'));
					break;
				}
			}
			else
			{
				$result = $this->mcustomers->save();
				switch($result)
				{
					case 0:
						echo json_encode(array('status' => 0, 'errors' => $this->lang->line('c_o_error_enter_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_registration_captcha_img">'));
					break;
					case 1:
						echo json_encode(array('status' => 1, 'errors' => validation_errors()));
					break;
					case 2:
						echo json_encode(array('status' => 1, 'errors' => $this->lang->line('c_o_error_customer_registration')));
					break;
					case 3:
						echo json_encode(array('status' => 2, 'success' => $this->lang->line('c_o_success_customer_registration')));
					break;
				}
			}	
		}
		else
		{
			echo json_encode(array('status' => 1, 'errors' => 'Wrong registration data!'));
		}
	}
	
	public function office_form()
	{
		if($this->session->userdata('customer_id') > 0)
		{
			$this->mlangs->load_language_file('modules/orders_customers');
			$this->load->model('customers/mcustomers');
			if($this->mcustomers->check_isset_user($this->session->userdata('customer_id')))
			{
				$data = $this->mcustomers->edit($this->session->userdata('customer_id'));
				$this->template->add_template_ajax('customers/office_form', array('customer_edit_data' => $data, 'customer_edit_form_id' => 'customer_edit_form'));
				$this->template->add_template_ajax('customers/office_form_js', array('customer_edit_data' => $data, 'customer_edit_form_id' => 'customer_edit_form'));
			}
		}
	}
	
	public function forgot_password_form()
	{
		$this->template->add_template_ajax('customers/forgot_password_form', array('customer_fp_form_id' => 'customers_forgot_password_form'));
		$this->template->add_template_ajax('customers/forgot_password_form_js', array('customer_fp_form_id' => 'customers_forgot_password_form'));
	}
	
	public function forgot_password()
	{
		$email = trim($this->input->post('email'));
		$captcha = trim($this->input->post('captcha'));
		if($email && $captcha && strlen($captcha)>5 && strlen($captcha)<8)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email','E-Mail','trim|required|valid_email');
			if($this->form_validation->run() === TRUE)
			{
				if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $captcha)
				{
					$this->load->model('customers/mcustomers');
					$result = $this->mcustomers->forgot_password($email);
					switch($result)
					{
						case 0:
							if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
							echo json_encode(array('status' => 1, 'errors' => $this->lang->line('forgot_password_error_wrong_email'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_forgot_password_captcha_img">'));
						break;
						case 1:
							if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
							echo json_encode(array('status' => 2, 'success' => $this->lang->line('forgot_password_email_success')));
						break;
					}
				}
				else
				{
					if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
					echo json_encode(array('status' => 1, 'errors' => $this->lang->line('forgot_password_error_wrong_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_forgot_password_captcha_img">'));
				}
			}
			else
			{
				echo json_encode(array('status' => 0, 'errors' => '<p>Wrong forgot password email!</p>'));
			}
		}
		else
		{
			echo json_encode(array('status' => 0, 'errors' => '<p>Forgot password data is empty or wrong!</p>'));
		}
	}
	
	public function change_password_form()
	{
		if($this->session->userdata('customer_id'))
		{
			$this->template->add_template_ajax('customers/change_password_form', array('customer_cp_form_id' => 'customers_change_password_form'));
			$this->template->add_template_ajax('customers/change_password_form_js', array('customer_cp_form_id' => 'customers_change_password_form'));
		}
		else
		{
			echo "<h1 align='center'>Wron customer data. You need login!</h1>";
		}
	}
	
	public function change_password()
	{
		if($this->session->userdata('customer_id'))
		{
			$POST = $this->input->post();
			if(isset($POST['old_password']) && isset($POST['new_password']) && strlen($POST['old_password'] = trim($POST['old_password'])) > 5 && strlen($POST['new_password'] = trim($POST['new_password'])) > 5)
			{
				$captcha = trim($this->input->post('captcha'));
				if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $captcha)
				{
					$this->load->model('customers/mcustomers');
					$result = $this->mcustomers->change_password($POST['old_password'], $POST['new_password']);
					switch($result)
					{
						case 0:
							if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
							echo json_encode(array('status' => 0, 'errors' => '<p>Error change password. Try later.</p>', 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_change_password_captcha_img">'));
						break;
						case 1:
							if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
							echo json_encode(array('status' => 1, 'errors' => $this->lang->line('change_password_error_wrong_old_password'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_change_password_captcha_img">'));
						break;
						case 2:
							if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
							echo json_encode(array('status' => 2, 'success' => $this->lang->line('change_password_success')));
						break;
					}
				}
				else
				{
					if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
					echo json_encode(array('status' => 1, 'errors' => $this->lang->line('forgot_password_error_wrong_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_change_password_captcha_img">'));
				}
			}
			else
			{
				echo json_encode(array('status' => 0, 'errors' => '<p>Change password data is empty or wrong!</p>', 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_change_password_captcha_img">'));
			}
		}
	}
	
	public function activate_forgot_password()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && isset($URI['code']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('customers/mcustomers');
			if($this->mcustomers->check_isset_user($id))
			{
				if($this->mcustomers->send_new_password($id, $URI['code']))
				{
					redirect(site_url());
				}
				else
				{
					echo "<h1 align='center'>Activation link is not correct!</h1>";
				}
			}
			else
			{
				echo "<h1 align='center'>Activation link is not correct!</h1>";
			}
		}
		else
		{
			echo "<h1 align='center'>Activation link is not correct!</h1>";
		}
	}
	
	public function activate_customer()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && isset($URI['code']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('customers/mcustomers');
			if($this->mcustomers->check_isset_user($id))
			{
				if($this->mcustomers->customer_activate($id, $URI['code']))
				{
					$this->mcustomers->manually_login($id);
					redirect(site_url());
				}
				else
				{
					echo "<h1 align='center'>Activation link is not correct!</h1>";
				}
			}
			else
			{
				echo "<h1 align='center'>Activation link is not correct!</h1>";
			}
		}
		else
		{
			echo "<h1 align='center'>Activation link is not correct!</h1>";
		}
	}
	
	public function check_isset_email()
	{
		if($email = $this->input->post('customer'))
		{
			if(isset($email['email']))
			{
				$email = $email['email'];
				$this->load->model('mcustomers');
				
				$URI = $this->uri->uri_to_assoc(3);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					if($this->mcustomers->check_isset_user($id))
					{
						$this->mcustomers->set_user_id($id);
					}
					else
					{
						echo json_encode(false);
						exit;
					}
				}
				if($this->mcustomers->check_isset_email($email))
				{
					echo json_encode(true);
					exit;
				}
				else
				{
					echo json_encode(false);
					exit;
				}
			}
			echo json_encode(false);
			exit;
		}
		echo json_encode(false);
		exit;
	}
	
	public function write_admin_form()
	{
		if($this->session->userdata('customer_id'))
		{
			$this->template->add_template_ajax('customers/write_admin_form', array('customer_wa_form_id' => 'customers_write_admin_form'));
			$this->template->add_template_ajax('customers/write_admin_form_js', array('customer_wa_form_id' => 'customers_write_admin_form'));
		}
	}
	
	public function write_admin()
	{
		if($this->session->userdata('customer_id'))
		{
			$captcha = trim($this->input->post('captcha'));
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $captcha)
			{
				$this->load->model('customers/mcustomers');
				$this->mcustomers->write_admin($this->session->userdata('customer_id'));
				if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
				echo json_encode(array('status' => 1, 'success' => $this->lang->line('wa_success')));
			}
			else
			{
				if(isset($_SESSION['captcha_keystring'])) unset($_SESSION['captcha_keystring']);
				echo json_encode(array('status' => 0, 'errors' => $this->lang->line('forgot_password_error_wrong_captcha'), 'img' => '<img src="/additional_libraries/kcaptcha/check.php?'.session_name().'='.session_id().'&rand='.rand(1000, 9999).'" id="customer_wa_captcha_img">'));
			}
		}
	}
}
?>