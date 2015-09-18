<?php
class Login extends MX_Controller
{
private $error = array();

	function __construct()
	{
		parent::__construct();
		$CI = & get_instance();
		$CI->redirect = false;
	}
	public function index()
		{
			$data = array();
			if($this->session->flashdata('ErrorArray'))
				{
					$data['ErrorArray'] = $this->session->flashdata('ErrorArray');
				}
			$this->load->helper('form');
			header('HTTP/1.1 303 Moved Login');
			$this->parser->parse('loginform', $data);
		}
	public function auth()
		{
			if(isset($_POST['login']) && isset($_POST['password']))
				{
					$er = false;
					if(trim($_POST['login']==''))
						{
							$this->error[] = 'Не корректное или пустое поле "Логин".';
							$er = true;
						}
					if(trim($_POST['password']==''))
						{
							$this->error[] = 'Не корректное или пустое поле "Пароль".';
							$er = true;
						}
					if($er)
						{
							$this->session->set_flashdata('ErrorArray', $this->error);
							redirect('login');
							exit;
						}
					
					$data['login'] = trim($_POST['login']);
					$data['password'] = trim($_POST['password']);
								
					$this->load->model('mlogin');
					if($this->mlogin->autorize($data) == 2)
						{
							redirect('');
						}
					else
						if($this->mlogin->autorize($data) == 1)
						{
							$this->error[] = 'Ваша учетная запись не активирована, обратитесь к админисстратору.';
							$this->session->set_flashdata('ErrorArray', $this->error);
							redirect('login');
						}
						else
							if($this->mlogin->autorize($data) == 0)
							{
								$this->error[] = 'Не верно введен "Логин" или "Пароль".';
								$this->session->set_flashdata('ErrorArray', $this->error);
								redirect('login');
							}
				}
			else
				{
					$this->error[] = 'Не корректное или пустое поле "Логин" или "Пароль".';
					$this->session->set_flashdata('ErrorArray', $this->error);
					redirect('login');
				}		
		}
	
	public function logout()
	{
		session_unset();
		session_destroy();
		redirect('/');
	}
}
?>