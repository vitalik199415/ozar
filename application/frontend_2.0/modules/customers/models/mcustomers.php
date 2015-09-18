<?php
class Mcustomers extends AG_Model
{
	const CT 				= 'm_u_customers';
	const ID_CT 			= 'id_m_u_customers';

	const CT_ADDR 			= 'm_u_customers_address';
	const ID_CT_ADDR 		= 'id_m_u_customers_address';

	const CT_TYPE 		= 'm_u_types';
	const ID_CT_TYPE 	= 'id_m_u_types';
	const CT_TYPE_DESC 		= 'm_u_types_description';
	const ID_CT_TYPE_DESC 	= 'id_m_u_types_description';

	const CT_N_TYPE		= 'm_u_customers_types';

	public $user_id = FALSE;

	function __construct()
	{
		parent::__construct();
	}

	public function login($email, $password)
	{
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->where("`password`", $password)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			if($result['active'] == 0) return 1;
			$old_data = $this->session->all_userdata();
			session_unset();
			session_destroy();

			$session_id = $this->id_users.'S'.$result[self::ID_CT].'S'.md5($result['create_date']);
			$this->session->manually_session_start($session_id, $old_data);
			$this->session->set_userdata('customer_id', $result[self::ID_CT]);
			$customer_array = array('customer_id' => $result[self::ID_CT],'name' => $result['name'], 'have_m_u_types' => $result['have_m_u_types'], 'id_langs' => $result['id_langs'], 'email' => $result['email'], 'id_users' =>$result['id_users']);
			if($result['have_m_u_types'] == 1)
			{
				$customer_types = array();
				$query = $this->db->select("*")->from("`".self::CT_N_TYPE."`")->where("`".self::ID_CT."`", $result[self::ID_CT]);
				$result = $query->get()->result_array();
				foreach($result as $ms)
				{
					$customer_types[$ms[self::ID_CT_TYPE]] = $ms[self::ID_CT_TYPE];
				}
				$customer_array['m_u_types'] = $customer_types;
			}
			$this->session->set_userdata('CUSTOMER', $customer_array);
			return 2;
		}
		return 0;
	}

	public function manually_login($id)
	{

		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT."`", $id)->where("`active`", 1)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$old_data = $this->session->all_userdata();
			session_unset();
			session_destroy();

			$session_id = $this->id_users.'S'.$result[self::ID_CT].'S'.md5($result['create_date']);
			$this->session->manually_session_start($session_id, $old_data);
			$this->session->set_userdata('customer_id', $result[self::ID_CT]);
			$customer_array = array('customer_id' => $result[self::ID_CT],'name' => $result['name'], 'have_m_u_types' => $result['have_m_u_types'], 'id_langs' => $result['id_langs'], 'email' => $result['email'], 'id_users' =>$result['id_users']);
			if($result['have_m_u_types'] == 1)
			{
				$customer_types = array();
				$query = $this->db->select("*")->from("`".self::CT_N_TYPE."`")->where("`".self::ID_CT."`", $result[self::ID_CT]);
				$result = $query->get()->result_array();
				foreach($result as $ms)
				{
					$customer_types[$ms[self::ID_CT_TYPE]] = $ms[self::ID_CT_TYPE];
				}
				$customer_array['m_u_types'] = $customer_types;
			}
			$this->session->set_userdata('CUSTOMER', $customer_array);
			return true;
		}
		return false;
	}

	public function forgot_password($email)
	{
		$email = trim($email);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->where("`active`", 1)->limit(1);
		$result = $query->get()->row_array();
		if($result['COUNT']>0)
		{
			$this->send_forgot_password_email($email);
			return 1;
		}
		return 0;
	}

	public function change_password($old_pass, $new_pass)
	{
		if($id = $this->session->userdata('customer_id'))
		{
			$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT."`", $id)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				if($old_pass == $result['password'])
				{
					$this->sql_add_data(array('password' => $new_pass))->sql_save(self::CT, $id);
					return 2;
				}
				return 1;
			}
			return 0;
		}
		return 0;
	}

	public function get_customers_types()
	{

	}

	public function edit($id)
	{
		$query = $this->db->select("A.`email`, A.`name` AS cname, A.`active`, A.`have_m_u_types`, B.*")
				->from("`".self::CT."` AS A")
				->join(	"`".self::CT_ADDR."` AS B",
						"B.`".self::ID_CT."` = A.`".self::ID_CT."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CT."`", $id)->limit(2);
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$data['customer'] = array('email' => $ms['email'], 'name' => $ms['cname'], 'active' => $ms['active'], 'have_m_u_types' => $ms['have_m_u_types']);
			$data['customer_address'][$ms['type']] = array(
				self::ID_CT_ADDR => $ms[self::ID_CT_ADDR],
				'name' => 			$ms['name'],
				'country' => 		$ms['country'],
				'city' => 			$ms['city'],
				'zip' => 			$ms['zip'],
				'address' => 		$ms['address'],
				'telephone' => 		$ms['telephone'],
				'fax' => 			$ms['fax'],
				'address_email' => 	$ms['address_email']
			);
		}

		//$data['customer']['customer_types'] = $this->get_customer_types($id, TRUE);
		return $data;
	}

	public function get_customer_address($id, $type = FALSE)
	{
		if(!$this->check_isset_user($id))
		{
			return FALSE;
		}
		$query = $this->db->select("B.*")
				->from("`".self::CT."` AS A")
				->join(	"`".self::CT_ADDR."` AS B",
						"B.`".self::ID_CT."` = A.`".self::ID_CT."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CT."`", $id);
		if($type == 'B' || $type == 'S')
		{
			$query->where("B.`type`", $type)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				return $result + array('ID' => $id);
			}
			return FALSE;
		}
		else
		{
			$query->limit(2);
			$result = $query->get()->result_array();
			$data = array();
			if(count($result)>0)
			{
				foreach($result as $ms)
				{
					$data[$ms['type']] = $ms + array('ID' => $id);
				}
				return $data;
			}
			return FALSE;
		}
	}

	public function get_order_customer()
	{
		if($this->session->userdata('customer_id'))
		{
			return $this->edit($this->session->userdata('customer_id'));
		}
		return FALSE;
	}

	public function set_user_id($id)
	{
		$this->user_id = intval($id);
		return $this;
	}

	function check_isset_email($email)
	{
		$email = trim($email);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->limit(1);
		if($this->user_id)
		{
			$query->where("`".self::ID_CT."` <>", $this->user_id);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function check_isset_user($id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT."`", intval($id))->limit(1);
		$result = $query->get()->row_array();
		if($result['COUNT'] > 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function get_customer_by_email($email)
	{
		$email = trim($email);
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}

	public function set_validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_email', 'mcustomers')->set_rules('customer[email]','E-Mail','trim|required|valid_email|callback_check_isset_email');
		$this->form_validation->set_message('check_isset_email', 'Пользователь с указанным E-Mail уже существует!');
	}

	public function save($id = FALSE)
	{
		$POST = $this->input->post();

        if(isset($POST['captcha']) && isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $POST['captcha'])
        {

    		if($id && ($id = intval($id))>0)
    		{
    			$data = $POST['customer'];
    			$this->db->trans_start();

    			$this->sql_add_data($data)->sql_using_user()->sql_update_date()->sql_save(self::CT, $id);

    			$query = $this->db->select("*")
    					->from("`".self::CT_ADDR."`")
    					->where("`".self::ID_CT."`", $id)->limit(2);
    			$temp = $query->get()->result_array();
    			$result = array();
    			foreach($temp as $ms)
    			{
    				$result[$ms['type']] = $ms;
    			}

    			foreach($POST['customer_address'] as $key => $ms)
    			{
    				if($key == 'B' || $key == 'S')
    				{
    					$data = $ms;
    					if(isset($result[$key]))
    					{
    						$this->sql_add_data($data)->sql_save(self::CT_ADDR, $result[$key][self::ID_CT_ADDR]);
    					}
    					else if(!isset($result[$key]))
    					{
    						$data += array(self::ID_CT => $id, 'type' => $key);
    						$this->sql_add_data($data)->sql_save(self::CT_ADDR);
    					}
    				}
    			}

    			$this->db->trans_complete();
    			if($this->db->trans_status())
    			{
    				return 3;
    			}
    			return 2;
    		}
    		else
    		{
    			$this->set_validation();
    			if($this->form_validation->run() === TRUE)
    			{
    				$data = $POST['customer'];
    				$data['name'] = trim($data['name']);
    				$data['email'] = trim($data['email']);
    				$data['password'] = trim($data['password']);
    				$this->load->helper('string');
    				$data['code'] = random_string('alnum', 10);
    				$this->db->trans_start();
    				$id = $this->sql_add_data($data)->sql_using_user()->sql_update_date()->sql_save(self::CT);

    				foreach($POST['customer_address'] as $key => $ms)
    				{
    					if($key == 'B' || $key == 'S')
    					{
    						$adata = $ms;
    						$adata += array(self::ID_CT => $id, 'type' => $key);
    						$this->sql_add_data($adata)->sql_save(self::CT_ADDR);
    						$l_address_data[$key] = $adata;
    					}
    				}
    				$this->db->trans_complete();
    				if($this->db->trans_status())
    				{
    					$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_CT."`", $id);
    					$data = $query->get()->row_array();
    					$mail_data = array('name' => $data['name'], 'site' => $_SERVER['SERVER_NAME'], 'email' => $data['email'], 'password' => $data['password'], 'link' => site_url('/customers/activate_customer/id/'.$id.'/code/'.md5($data['update_date'].$data['code'])), 'addresses' => $l_address_data);
    					$this->send_registration_email($mail_data);
    					return 3;
    				}
    				return 2;
    			}
    			else
    			{
    				return 1;
    			}
    		}
        }
        else
        {
            unset($_SESSION['captcha_keystring']);
            return 0;
        }
	}

	protected function send_registration_email($data)
	{
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;

		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('no-reply@'.$data['site'], $data['site']);
		$this->email->to($data['email']);
		$this->email->subject('Registration Confirmation!');
		$this->email->message($this->load->view('customers/letters/'.$this->mlangs->language.'/registration_mail', array('data' => $data), TRUE));
		$this->email->send();
		$this->email->clear();
	}

	protected function send_forgot_password_email($email)
	{
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->where("`active`", 1)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['name'] = $result['name'];
			$data['email'] = $result['email'];

			$this->load->helper('string');
			$new_password = random_string('alnum', 6);
			$code = random_string('alnum', 10);
			$this->sql_add_data(array('code' => $code, 'new_password' => $new_password))->sql_save(self::CT, $result[self::ID_CT]);
			$data['link'] = site_url('/customers/activate_forgot_password/id/'.$result[self::ID_CT].'/code/'.md5($result[self::ID_CT].$code.$new_password));

			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;

			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('no-reply@'.$data['site'], $data['site']);
			$this->email->to($data['email']);
			$this->email->subject('Forgot Password!');
			$this->email->message($this->load->view('customers/letters/'.$this->mlangs->language.'/forgot_password_request', array('data' => $data), TRUE));
			$this->email->send();
			$this->email->clear();
		}
	}

	public function send_new_password($id, $code)
	{
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT."`", $id)->where("`active`", 1)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			if($code == md5($result[self::ID_CT].$result['code'].$result['new_password']))
			{
				$data['site'] = $_SERVER['SERVER_NAME'];
				$data['name'] = $result['name'];
				$data['email'] = $result['email'];
				$data['password'] = $result['new_password'];
				$this->sql_add_data(array('code' => NULL, 'password' => $result['new_password'], 'new_password' => NULL))->sql_save(self::CT, $result[self::ID_CT]);

				$config['protocol'] = 'sendmail';
				$config['wordwrap'] = FALSE;
				$config['mailtype'] = 'html';
				$config['charset'] = 'utf-8';
				$config['priority'] = 1;

				$this->load->library('email');
				$this->email->initialize($config);
				$this->email->from('no-reply@'.$data['site'], $data['site']);
				$this->email->to($data['email']);
				$this->email->subject('New password - Forgot Password!');
				$this->email->message($this->load->view('customers/letters/'.$this->mlangs->language.'/new_password', array('data' => $data), TRUE));
				$this->email->send();
				$this->email->clear();
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function write_admin($id)
	{
		$query = $this->db->select("A.`email`, A.`name`")
				->from("`".self::CT."` AS A")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CT."`", $id)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['name'] = $result['name'];
			$data['email'] = $result['email'];
			$data['message'] = $this->input->post('message');

			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;

			$this->load->model('customers/mcustomers_settings');
			$CS = $this->mcustomers_settings->get_settings();
			$data['admin_email'] = $CS['registration_notice_email'];

			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from($result['email'], $data['site']);
			$this->email->to($data['admin_email']);
			$this->email->subject($data['site'].' - New message to administrator!');
			$this->email->message($this->load->view('customers/letters/'.$this->mlangs->language.'/write_admin', array('data' => $data), TRUE));
			$this->email->send();
			$this->email->clear();
			return TRUE;
		}
	}

	public function customer_activate($id, $code)
	{
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_CT."`", $id);
		$data = $query->get()->row_array();
		if($code == md5($data['update_date'].$data['code']))
		{
			$this->sql_add_data(array('active' => 1, 'code' => NULL))->sql_update_date()->sql_save(self::CT, $id);

			$this->load->model('customers/mcustomers_settings');
			$CS = $this->mcustomers_settings->get_settings();
			if($CS['registration_notice_on'] == 1)
			{
				$this->send_registration_notice_email($id);
			}
			return TRUE;
		}
		return FALSE;
	}

	public function send_registration_notice_email($id)
	{
		$query = $this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_CT."`", $id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$this->load->model('customers/mcustomers_settings');
			$CS = $this->mcustomers_settings->get_settings();

			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['name'] = $result['name'];
			$data['email'] = $result['email'];

			$addr = $this->db->select("*")->from("`".self::CT_ADDR."`")->where("`".self::ID_CT."`", $id)->where("`type`", 'B')->limit(1);
			$addr = $addr->get()->row_array();

			$data['addresses']['B'] = $addr;

			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;

			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('no-reply@'.$data['site'], $data['site']);
			$this->email->to($CS['registration_notice_email']);
			$this->email->subject('New registered customer!');
			$this->email->message($this->load->view('customers/letters/registration_notice', array('data' => $data), TRUE));
			$this->email->send();
			$this->email->clear();
		}
	}

	public function delete($id)
	{
		if(is_array($id))
		{
			$del_array = array();
			foreach($id as $ms)
			{
				if(($u_id = intval($ms))>0)
				{
					$del_array[] = $u_id;
				}
			}
			if(count($del_array)>0)
			{
				$query = $this->db->where_in(self::ID_CT, $del_array)->where("`".self::ID_USERS."`", $this->id_users)->delete(self::CT);
			}
		}
		else
		{
			if(($u_id = intval($id))>0)
			{
				if($this->check_isset_user($u_id))
				{
					$this->db->where(self::ID_CT, $id)->delete(self::CT);
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
			return FALSE;
		}
	}

	public function set_active($id, $active = 1)
	{
		$data = array('active' => $active);
		if(is_array($id))
		{
			$act_array = array();
			foreach($id as $ms)
			{
				if(($u_id = intval($ms))>0)
				{
					$act_array[] = $u_id;
				}
			}
			if(count($act_array)>0)
			{
				$query = $this->db->where_in(self::ID_CT, $act_array);
			}
		}
		else
		{
			if(($u_id = intval($id))>0)
			{
				$query = $this->db->where(self::ID_CT, $u_id);
			}
		}
		$query->where("`".self::ID_USERS."`", $this->id_users)->update(self::CT, $data);
	}

	public function get_customer_types($id, $short = FALSE)
	{
		$array = array();
		$query = $this->db->select("A.`".self::ID_CT_TYPE."` AS ID, B.`name`, B.`description`")
				->from("`".self::CT_N_TYPE."` AS A")
				->join("`".self::CT_TYPE_DESC."` AS B",
						"B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_CT."`", $id);
		if($short)
		{
			foreach($query->get()->result_array() as $ms)
			{
				$array[$ms['ID']] = $ms['ID'];
			}
			return $array;
		}

		foreach($query->get()->result_array() as $ms)
		{
			$array[$ms['ID']] = $ms;
		}
		return $array;
	}

	public function get_types($ID = FALSE)
	{
		$array = array();
		$query = $this->db
				->select("A.`".self::ID_CT_TYPE."` AS ID, B.`name`")
				->from("`".self::CT_TYPE."` AS A")
				->join("`".self::CT_TYPE_DESC."` AS B",
					   "B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && `".self::ID_LANGS."` = '".$this->id_langs."'",
					   "LEFT")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1);
		if($ID)
		{
			$query->where("`".self::ID_CT_TYPE."`", $ID)->limit(1);
			$result = $query->get()->row_array();
		}
		else
		{
			$result = $query->get()->result_array();
		}

		foreach($result as $ms)
		{
			$array[$ms['ID']] = $ms['name'];
		}
		return $array;
	}
}