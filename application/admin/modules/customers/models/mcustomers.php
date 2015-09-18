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
	
	public $ct_id = FALSE;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_customers_grid()
	{
		$this->load->model('customers/mcustomers_types');
		$customers_groups = $this->mcustomers_types->get_customers_types();
		if(count($customers_groups) > 0)
		{
			$customers_groups = array('0' => 'Пользователи без группы') + $customers_groups;
		}
		  
		$this->load->library("grid");
		$this->grid->_init_grid("customers_grid");
		  
		if($extra_search = $this->grid->get_options('search'))
		{
			if(isset($extra_search['id_m_u_types']))
			{
				$temp_extra_search = $extra_search;
				unset($temp_extra_search['id_m_u_types']);
				$this->grid->set_options('search', $temp_extra_search);
				$update_select_types = $extra_search['id_m_u_types'];
			}
		}
		  
		$qty_query = clone $this->db;
		$qty_query->select("COUNT(*) AS numrows")
				->from("`".self::CT."` AS A")
				->join("`".self::CT_ADDR."` AS B",
						"B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		  
		$this->grid->db->select("A.`".self::ID_CT."` AS ID, A.`email`, A.`create_date`, A.`update_date`, A.`active`, B.`name`, B.`city`, GROUP_CONCAT(D.`name` ORDER BY D.`".self::ID_CT_TYPE."` SEPARATOR '<BR>') AS id_m_u_types")
				->from("`".self::CT."` AS A")
				->join( "`".self::CT_ADDR."` AS B",
						"B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
						"LEFT")
				->join("`".self::CT_N_TYPE."` AS C",
						"C.`".self::ID_CT."` = A.`".self::ID_CT."`",
						"LEFT")
				->join("`".self::CT_TYPE_DESC."` AS D",
						"D.`".self::ID_CT_TYPE."` = C.`".self::ID_CT_TYPE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->group_by("A.`".self::ID_CT."`");
		   
		   
		if(isset($update_select_types))
		{
			$update_select_types = intval($update_select_types);
			if($update_select_types > 0)
			{
				$this->grid->db->join("`".self::CT_N_TYPE."` AS T",
					   "T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					   "INNER");
				$qty_query->join("`".self::CT_N_TYPE."` AS T",
					   "T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					   "INNER");
				 
			}
			else if($update_select_types == 0)
			{
				$this->grid->db->where("C.`".self::ID_CT_TYPE."` IS NULL", NULL, FALSE);
				$qty_query->where("`have_m_u_types`", 0);
			}
		}
		$this->grid->set_extra_select_qty_object($qty_query);
		unset($qty_query);
		  
		$this->load->helper("customers/customers_helper");
		helper_customers_grid_build($this->grid, $customers_groups);
		  
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));
		if(isset($update_select_types))
		{
			$extra_search = $this->grid->get_options('search');
			$extra_search['id_m_u_types'] = $update_select_types;
			$this->grid->set_search_manualy('id_m_u_types', $update_select_types);
			$this->grid->set_options('search', $extra_search);
		}
		$this->grid->render_grid();
	}

	public function prepare_customers_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("customers_grid");

		if($extra_search = $this->grid->get_options('search'))
		{
			if(isset($extra_search['id_m_u_types']))
			{
				$temp_extra_search = $extra_search;
				unset($temp_extra_search['id_m_u_types']);
				$this->grid->set_options('search', $temp_extra_search);
				$update_select_types = $extra_search['id_m_u_types'];
			}
		}

		$qty_query = clone $this->db;
		$qty_query->select("COUNT(*) AS numrows")
				  ->from("`".self::CT."` AS A")
				  ->join("`".self::CT_ADDR."` AS B",
					  "B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
					  "LEFT")
				  ->where("A.`".self::ID_USERS."`", $this->id_users);

		$this->grid->db->select("A.`".self::ID_CT."` AS ID, A.`email`, A.`create_date`, A.`update_date`, A.`active`, B.`name`, B.`city`, GROUP_CONCAT(D.`name` ORDER BY D.`".self::ID_CT_TYPE."` SEPARATOR '<BR>') AS id_m_u_types")
					   ->from("`".self::CT."` AS A")
					   ->join( "`".self::CT_ADDR."` AS B",
						   "B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
						   "LEFT")
					   ->join("`".self::CT_N_TYPE."` AS C",
						   "C.`".self::ID_CT."` = A.`".self::ID_CT."`",
						   "LEFT")
					   ->join("`".self::CT_TYPE_DESC."` AS D",
						   "D.`".self::ID_CT_TYPE."` = C.`".self::ID_CT_TYPE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
						   "LEFT")
					   ->where("A.`".self::ID_USERS."`", $this->id_users)->group_by("A.`".self::ID_CT."`");


		if(isset($update_select_types))
		{
			$update_select_types = intval($update_select_types);
			if($update_select_types > 0)
			{
				$this->grid->db->join("`".self::CT_N_TYPE."` AS T",
					"T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					"INNER");
				$qty_query->join("`".self::CT_N_TYPE."` AS T",
					"T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					"INNER");

			}
			else if($update_select_types == 0)
			{
				$this->grid->db->where("C.`".self::ID_CT_TYPE."` IS NULL", NULL, FALSE);
				$qty_query->where("`have_m_u_types`", 0);
			}
		}
		$this->grid->set_extra_select_qty_object($qty_query);
		unset($qty_query);

		if(isset($update_select_types))
		{
			$extra_search = $this->grid->get_options('search');
			$extra_search['id_m_u_types'] = $update_select_types;
			$this->grid->set_search_manualy('id_m_u_types', $update_select_types);
			$this->grid->set_options('search', $extra_search);
		}
		return $this->grid;
	}
	
	public function get_customer($id)
	{
		$array = array();
		$query = $this->db
			->select("A.`".self::ID_CT."` AS ID, A.`email`, A.`name`, A.`active`, A.`have_m_u_types`")
			->from("`".self::CT."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CT."`", $id)->limit(1);
		$array = $query->get()->row_array();
		if(count($array) > 0)
		{
			return $array;
		}
		return FALSE;
	}

	public function get_customer_addresses($id_ct)
	{
		$addresses = array();
		$this->db->select("*")
			->from("`".self::CT_ADDR."`")
			->where("`".self::ID_CT."`", $id_ct)->limit(2);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$addresses[$ms['type']] = $ms;
		}
		return $addresses;
	}
	
	public function add()
	{	
		$data = $this->prepare_add_edit_data();
		helper_customers_form_build($data);
	}
	
	public function edit($id)
	{	
		if(!$this->check_isset_ct($id)) return FALSE;
		$data = $this->prepare_add_edit_data();
		$data += $this->prepare_edit_data($id);
		helper_customers_form_build($data, '/id/'.$id);
		return TRUE;
	}
	
	public function view($id)
	{
		$data = $this->prepare_add_edit_data();
		$data += $this->prepare_edit_data($id);
		$data['customer_orders'] = $this->get_customer_orders_collection($id);
		helper_customers_view_build($id, $data);
		return TRUE;
	}
	
	protected function prepare_add_edit_data()
	{
		$this->load->helper("customers/customers_helper");
		$this->load->model("customers/mcustomers_types");
		$data['data_customers_types'] = $this->mcustomers_types->get_customers_types();
		
		return $data;
	}
	
	protected function prepare_edit_data($id)
	{
		$data = array();
		$query = $this->db
			->select("A.`".self::ID_CT."` AS ID, A.`email`, A.`name` AS `cname`, A.`password`, A.`active`, A.`have_m_u_types`, B.*")
			->from("`".self::CT."` AS A")
			->join(	"`".self::CT_ADDR."` AS B",
					"B.`".self::ID_CT."` = A.`".self::ID_CT."`",
					"LEFT")
			->where("A.`".self::ID_CT."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$customer = $query->get()->result_array();
		foreach($customer as $ms)
		{
			$data['customer'] = array(self::ID_CT => $ms[self::ID_CT], 'email' => $ms['email'], 'name' => $ms['cname'], 'active' => $ms['active'], 'have_m_u_types' => $ms['have_m_u_types']);
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
		$data['customer_types'] = $this->get_customer_types($id, TRUE);
		
		return $data;
	}
	
	public function get_customer_orders_collection($ct_id)
	{
		$this->load->model('sales/morders');
		$this->load->library("grid");
		$this->grid->_init_grid("customer_orders_grid", array('sort' => 'orders_number', 'desc' => 'DESC', 'url' => set_url('*/get_ajax_customer_orders/id/'.$ct_id)), TRUE);
		
		$this->grid->db
				->select("A.`".Morders::ID_ORD."` AS ID, A.`orders_number`, A.`orders_state`, CONCAT(A.`total`, ' ', A.`base_currency_name`) AS total, A.`create_date`, A.`update_date`")
				->from("`".Morders::ORD."` AS A")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CT."`", $ct_id);
		$this->load->helper("customers/customers_helper");
		helper_customer_orders_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		
		$this->grid->update_grid_data("orders_state", $this->morders->get_order_state_collection());
		return $this->grid->render_grid(TRUE);
	}
	
	protected function save_ct_validation($id = FALSE)
	{
		$this->load->library('form_validation');
		if($id)
		{
			if(!$this->check_isset_ct($id)) return FALSE;
			$this->set_ct_id($id);
		}
		$this->form_validation->add_callback_function_class('check_isset_ct_email', 'mcustomers');
		$this->form_validation->add_callback_function_class('is_0_or_1', 'mcustomers');
		
		$this->form_validation->set_rules('customer[email]', 'E-Mail', 'trim|required|valid_email|callback_check_isset_ct_email');
		$this->form_validation->set_message('check_isset_ct_email', 'Пользователь с указанным E-Mail уже существует!');
		$this->form_validation->set_rules('customer[name]', 'Никнейм', 'trim|required|min_length[3]');

		$this->form_validation->set_message('is_0_or_1', 'Не верное значение поля "%s"!');
		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }
		
		return TRUE;
	}
	
	public function set_ct_id($id)
	{
		$this->ct_id = intval($id);
		return $this;
	}
	
	public function check_isset_ct_email($email)
	{
		$email = trim($email);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`email`", $email)->limit(1);
		if($this->ct_id)
		{
			$query->where("`".self::ID_CT."` <>", $this->ct_id);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset_ct($id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT."`", intval($id))->limit(1);
		$result = $query->get()->row_array();
		if($result['COUNT'] > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function save($id = FALSE)
	{    
		if(!$this->input->post('customer')) return FALSE;
		if($id)
		{
			$send_mail = FALSE;
			if($this->input->post('types_email')) $send_mail = TRUE;
			$groups_id = array();
			$upd_customer_session = FALSE;
			
			if(!$this->save_ct_validation($id)) return FALSE;
			
			$CPOST = $this->input->post('customer');
			$this->db->trans_start();

			$this->sql_add_data($CPOST)->sql_using_user()->sql_update_date()->sql_save(self::CT, $id);
			$c_t_result = $this->get_customer_types($id);
			
			if($CPOST['active'] == 0) $upd_customer_session = TRUE;
			if($CPOST['have_m_u_types'] == 1)
			{
				if($TPOST = $this->input->post('customer_types'))
				{
					$empty = TRUE;
					foreach($this->get_types() as $key => $ms)
					{
						if(isset($TPOST[$key]))
						{
							$empty = FALSE;
							if(!isset($c_t_result[$key]))
							{
								$this->sql_add_data(array(self::ID_CT => $id, self::ID_CT_TYPE => $key))->sql_save(self::CT_N_TYPE);
								$upd_customer_session = TRUE;

								$groups_id[]=$key;
								
							}
							else
							{
								unset($c_t_result[$key]);
							}
						}
					}
					if($empty)
					{
						$this->sql_add_data(array('have_m_u_types' => 0))->sql_using_user()->sql_save(self::CT, $id);
					}
				}
				else
				{
					$this->sql_add_data(array('have_m_u_types' => 0))->sql_using_user()->sql_save(self::CT, $id);
				}
				if(is_array($c_t_result))
				{
					$upd_customer_session = TRUE;
					foreach($c_t_result as $key => $ms)
					{
						$this->db->where("`".self::ID_CT."`", $id)->where("`".self::ID_CT_TYPE."`", $key)->delete(self::CT_N_TYPE);
					}
				}
			}
			else
			{
				if(count($c_t_result)>0)
				{
					$upd_customer_session = TRUE;
					$this->db->where("`".self::ID_CT."`", $id)->delete(self::CT_N_TYPE);
				}	
			}
			
			$query = $this->db
					->select("`".self::ID_CT_ADDR."`, `type`")
					->from("`".self::CT_ADDR."`")
					->where("`".self::ID_CT."`", $id);
			$temp_result = $query->get()->result_array();
			$result = array();
			
			foreach($temp_result as $ms)
			{
				$result[$ms['type']] = $ms;
			}
			unset($temp_result);
			
			$APOST = $this->input->post('customer_address');
			foreach($APOST as $key => $ms)
			{
				if($key == 'B' || $key == 'S')
				{
					$data = $ms;
					if(isset($result[$key]))
					{
						$this->sql_add_data($data)->sql_save(self::CT_ADDR, $result[$key][self::ID_CT_ADDR]);
					}
					else
					{
						$data += array(self::ID_CT => $id, 'type' => $key);
						$this->sql_add_data($data)->sql_save(self::CT_ADDR);
					}
				}
			}
			
			$this->db->trans_complete();
			if($this->db->trans_status()) 
			{
				if($send_mail && count($groups_id) > 0)
				{
					$this->load->model('customers/mcustomers_types');
					$this->mcustomers_types->customers_new_group_mail($id, $groups_id);
				}
				
				return TRUE; 
			}
			$this->messages->add_error_message('System error!');
			//$this->session->set_flashdata('customers_add_edit_form', $POST);
			return FALSE;
		}
		else
		{
			if(!$this->save_ct_validation()) return FALSE;
			$POST = $this->input->post();
			$CPOST = $this->input->post('customer');

			$this->load->helper('string');
			$new_password = random_string('alnum', 6);

			$CPOST['password'] = $new_password;
			$CPOST['code'] = random_string('alnum', 10);
			$send_mail = FALSE;
			if($this->input->post('types_email')) $send_mail = TRUE;
			$groups_id = array();
			$this->db->trans_start();
			$id = $this->sql_add_data($CPOST)->sql_using_user()->sql_update_date()->sql_save(self::CT);
			if($CPOST['have_m_u_types'] == 1)
			{
				if(isset($POST['customer_types']))
				{
					$empty = TRUE;
					foreach($this->get_types() as $key => $ms)
					{
						if(isset($POST['customer_types'][$key]))
						{
							$empty = FALSE;
							$this->sql_add_data(array(self::ID_CT => $id, self::ID_CT_TYPE => $key))->sql_save(self::CT_N_TYPE);
							$groups_id[]=$key;
						}
					}
					if($empty)
					{
						$this->sql_add_data(array('have_m_u_types' => 0))->sql_using_user()->sql_save(self::CT, $id);
					}
				}
				else
				{
					$this->sql_add_data(array('have_m_u_types' => 0))->sql_using_user()->sql_save(self::CT, $id);
				}
			}
			$APOST = $this->input->post('customer_address');
			foreach($APOST as $key => $ms)
			{
				if($key == 'B' || $key == 'S')
				{
					$data = $ms;
					$data += array(self::ID_CT => $id, 'type' => $key);
					unset($data[self::ID_CT_ADDR]);
					$this->sql_add_data($data)->sql_save(self::CT_ADDR);
				}
			}

			$this->load->model('users/musers');
			$user = $this->musers->get_user();

			$this->db->trans_complete();
			if($this->db->trans_status()) 
			{
				$this->db->select("*")->from("`".self::CT."`")->where("`".self::ID_CT."`", $id)->limit(1);
				$data = $this->db->get()->row_array();
				$data['site'] = $user['domain'];
				$mail_data = array('name' => $data['name'], 'site' => $data['site'], 'email' => $data['email'], 'password' => $data['password'], 'link' => 'http://'.$data['site'].'/customers/activate_customer/id/'.$id.'/code/'.md5($data['update_date'].$data['code']), 'addresses' => $APOST);
				$this->send_registration_email($mail_data);

				if($send_mail && count($groups_id) > 0)
				{
					$this->load->model('customers/mcustomers_types');
					$this->mcustomers_types->customers_new_group_mail($id, $groups_id);
				}
				
				return $id; 
			}
			$this->messages->add_error_message('System error!');
			return FALSE;
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
				$admin_session_id = session_id();
				$admin_data = $_SESSION;
				session_unset();
				session_destroy();
				
				$query = $this->db->select("*")
							->from("`".self::CT."`")
							->where_in("`".self::ID_CT."`", $del_array)->where("`".self::ID_USERS."`", $this->id_users);
				$customer = $query->get()->result_array();
				foreach($customer as $ms)
				{
					$session_id = $this->id_users.'S'.$ms[self::ID_CT].'S'.md5($ms['create_date']);
					$this->session->manually_session_start($session_id);
					session_destroy();
				}	
				
				$this->session->manually_session_start($admin_session_id);
				$_SESSION = $admin_data;
				
				$this->db->where_in(self::ID_CT, $del_array)->where("`".self::ID_USERS."`", $this->id_users)->delete(self::CT);
			}	
		}
		else
		{
			if(($u_id = intval($id))>0)
			{
				if($this->check_isset_ct($u_id))
				{	
					$admin_session_id = session_id();
					$admin_data = $_SESSION;
					session_unset();
					session_destroy();
					
					$query = $this->db->select("*")
								->from("`".self::CT."`")
								->where("`".self::ID_CT."`", $id)->limit(1);
					$customer = $query->get()->row_array();
					
					$session_id = $this->id_users.'S'.$customer[self::ID_CT].'S'.md5($customer['create_date']);
					$this->session->manually_session_start($session_id);
					session_destroy();
					
					$this->session->manually_session_start($admin_session_id);
					$_SESSION = $admin_data;
					
					
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

	protected function send_registration_email($data)
	{
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;

		$lang = $this->mlangs->get_language($this->mlangs->id_langs);

		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('no-reply@'.$data['site'], $data['site']);
		$this->email->to($data['email']);
		$this->email->subject('Registration Confirmation!');
		$this->email->message($this->load->view('customers/letters/'.$lang['language'].'/registration_mail', array('data' => $data), TRUE));
		$this->email->send();
		$this->email->clear();
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