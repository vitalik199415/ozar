<?php
class Mcustomers_types extends AG_Model
{
	const CT_TYPE 		= 'm_u_types';
	const ID_CT_TYPE 	= 'id_m_u_types';
	
	const CT_TYPE_DESC 		= 'm_u_types_description';
	const ID_CT_TYPE_DESC 	= 'id_m_u_types_description';
	
	const CT 				= 'm_u_customers';
	const ID_CT 			= 'id_m_u_customers';
	
	const CT_CTYPE			= 'm_u_customers_types';
	const ID_CT_CTYPE		= 'id_m_u_customers_types';
	
	const CT_ADDR 			= 'm_u_customers_address';
	const ID_CT_ADDR 		= 'id_m_u_customers_address';
	
	const LANGS				= 'langs';
	const ID_LANGS			= 'id_langs';
	
	const FDATA_KEY = 'customers_types_add_edit_form';
	const FDATA_MAILING_KEY = 'customers_types_mailing_form';
	
	public $id_type = FALSE;
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_customers_types_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("customers_types_grid");
		
		$this->grid->db	
			->select("A.`".self::ID_CT_TYPE."` AS ID, A.`active`, A.`alias`, B.`name`, (SELECT COUNT(*) FROM `".self::CT_CTYPE."` WHERE `".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."`) AS CCOUNT")
			->from("`".self::CT_TYPE."` AS A")
			->join("`".self::CT_TYPE_DESC."` AS B", 
				   "B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				   "left") 
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('customers/customers_types_helper');
		helper_customers_types_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
	}
	
	public function add()
	{
		$this->load->helper('customers/customers_types_helper');
		
		$this->load->model('langs/mlangs'); 
		$data['on_langs'] = $this->mlangs->get_active_languages();
		helper_customers_types_form_build($data);
	}
	
	public function edit($id)
	{
		if(!$this->check_isset_type($id)) return FALSE;
		$result = $this->get_edit_query($id);
		$result = $result->get()->result_array();
		$data = array();
		
		foreach($result as $ms)
		{
			$data['main']['alias'] = $ms['alias'];
			$data['main']['active'] = $ms['active'];
			$data['desc'][$ms['id_langs']] = $ms;
			
			unset($data['desc'][$ms['id_langs']]['alias']);
			unset($data['desc'][$ms['id_langs']]['ID']);
			unset($data['desc'][$ms['id_langs']]['active']);	
		}
		
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		$this->load->helper('customers/customers_types_helper');
		
		helper_customers_types_form_build($data,'/id/'.$id);
		return TRUE;
	}
	
	public function action($id)
	{
		$id = intval($id);
		if(!$this->check_isset_type($id)) return FALSE;
		
		$query = $this->db->select("B.`name`")
			->from("`".self::CT_TYPE."` AS A")
			->join(	"`".self::CT_TYPE_DESC."` AS B",
					"B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_CT_TYPE."`", $id)->limit(1);
		$result = $query->get()->row_array();
		
		$this->template->add_navigation($result['name']);

		$data['customers'] = $this->build_types_customers_grid($id);
		$this->load->model('langs/mlangs');
		$data['langs'] = $this->mlangs->get_active_languages();
		helper_customers_types_actions_build($id, $data);
		return TRUE;
	}
	
	public function build_types_customers_grid($t_id)
	{
		$this->load->library('grid');
		$this->grid->_init_grid('types_customers_grid', array('limit' => 50, 'url' => set_url('customers/customers_types/get_ajax_types_customers/id/'.$t_id)), TRUE);
		$this->grid->init_fixed_buttons(FALSE);
		
		$this->grid->db
			->select("A.`".self::ID_CT."` AS ID, A.`name`, A.`email`, A.`active`, B.`name` AS `bname`")
			->from("`".self::CT."` AS A")
			->join("`".self::CT_CTYPE."` AS C",
					"C.`".self::ID_CT_TYPE."` = '".$t_id."' && C.`".self::ID_CT."` = A.`".self::ID_CT."`",
					"INNER")
			->join("`".self::CT_ADDR."` AS B",
					"B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('customers/customers_types_helper');
		helper_customers_types_group_grid_build($this->grid, $t_id);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));

		return $this->grid->render_grid(TRUE);
	}
	
	public function build_types_customers_mailing_grid($t_id)
	{
		$this->load->library('grid');
		$this->grid->_init_grid('types_customers_mailing_grid', array('limit' => 50, 'url' => set_url('customers/customers_types/get_ajax_types_customers_mailing/id/'.$t_id)), TRUE);
		$this->grid->init_fixed_buttons(FALSE);
		
		$this->grid->db
			->select("A.`".self::ID_CT."` AS ID, A.`name`, A.`email`, A.`active`, B.`name` AS `bname`")
			->from("`".self::CT."` AS A")
			->join("`".self::CT_CTYPE."` AS C",
					"C.`".self::ID_CT_TYPE."` = '".$t_id."' && C.`".self::ID_CT."` = A.`".self::ID_CT."`",
					"INNER")
			->join("`".self::CT_ADDR."` AS B",
					"B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('customers/customers_types_helper');
		helper_customers_types_mailing_grid_build($this->grid, $t_id);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));

		return $this->grid->render_grid(TRUE);
	}
	
	public function mailing_form($id)
	{	
		if(($id = intval($id))>0)
		{
			if($this->check_isset_type($id))
			{
				$query = $this->db->select("B.`name`")
					->from("`".self::CT_TYPE."` AS A")
					->join(	"`".self::CT_TYPE_DESC."` AS B",
							"B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"LEFT")
					->where("A.`".self::ID_CT_TYPE."`", $id)->limit(1);
				$result = $query->get()->row_array();
				
				$this->template->add_title(' | '.$result['name']);
				$this->template->add_navigation($result['name'], set_url('*/*/action/id/'.$id));
				$this->template->add_title(' | Рассылка');
				$this->template->add_navigation('Рассылка');
				
				$data['customers'] = $this->build_types_customers_mailing_grid($id);
				$this->load->model('langs/mlangs');
				$data['langs'] = $this->mlangs->get_active_languages();
				$data['email'] = $this->get_customers_group_mails($id);
				
				$this->load->helper('customers/customers_types_helper');				
				helper_customers_types_mailing_form_build($id, $data);
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	protected function create_mailing_validation($id = FALSE)
	{	
		if($this->input->post('mailing_type') == 'selected' && !$this->input->post(self::ID_CT))
		{
			$this->messages->add_error_message('Пользователи не выбраны!');
			$this->session->set_flashdata(self::FDATA_MAILING_KEY, $this->input->post());
			return FALSE;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('subject', 'Тема письма', 'required|min_length[10]');
		$this->form_validation->set_rules('message', 'Текст письма', 'required|min_length[50]');

		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); $this->session->set_flashdata(self::FDATA_MAILING_KEY, $this->input->post()); return FALSE; }
		
		return TRUE;
	}
	
	public function create_mailing($t_id)
	{	
		if (!$this->create_mailing_validation()) return FALSE;
		$query = $this->db->select("A.`email`, A.`name`, L.`language`")
				->from("`".self::CT."` AS A")
				->join("`".self::CT_CTYPE."` AS C",
						"C.`".self::ID_CT_TYPE."` = '".$t_id."' && C.`".self::ID_CT."` = A.`".self::ID_CT."`",
						"INNER")
				/*->join("`".self::CT_ADDR."` AS B",
						"B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
						"LEFT")*/
				->join("`".self::LANGS."` AS L",
						"L.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
					
		$customers_id = FALSE;
		if($this->input->post('mailing_type') == 'selected')
		{
			$customers_id = $this->input->post(self::ID_CT);
			$query->where_in("A.`".self::ID_CT."`", $customers_id);
		}
		$customers = $query->get()->result_array();
		
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		
		$this->load->library('email');
		
		$this->load->model('customers/mcustomers_settings');
		$sender = $this->mcustomers_settings->get_distribution_settings();
		
		$letter_data['subject'] = $this->input->post('subject');
		$letter_data['message'] = $this->input->post('message');
		$letter_data['domain'] = $user['domain'];
		
		foreach($customers as $ms)
		{
			strlen($sender['distribution_email'])>0 ? $sender_email = $sender['distribution_email'] : $sender_email = 'no-reply@'.$user['domain'];

			$letter_data['name'] = $ms['name'];
			$letter_data['email'] = $ms['email'];
			
			$letter_html = $this->load->view('customers/letters/'.$ms['language'].'/customers_mailing', $letter_data, TRUE);
			
			$this->email->initialize($config);
			$this->email->from($sender_email, $user['domain']);
			$this->email->to($ms['email']);
			$this->email->subject($letter_data['subject']);
			$this->email->message($letter_html);
			$this->email->send();
			$this->email->clear();
		}
		return TRUE;
	}
	
	public function customers_new_group_mail($ID, $groups_id)
	{	
		$this->load->model('customers/mcustomers');
		$customer_data = $this->mcustomers->get_customer($ID);
		
		$this->load->model('langs/mlangs');
		$query = $this->db->select("A.`language`, A.`".self::ID_LANGS."`")
					->from("`".self::LANGS."` AS A")
					->join("`".self::CT."` AS B", "B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`", "INNER")
					->where("B.`".self::ID_USERS."`", $this->id_users)
					->limit(1);
		$letter_lang= $query->get()->row_array();	
		
		$letter_data_array = array('name' => $customer_data['name'], 'email' => $customer_data['email']);
		
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		
		$letter_data_array += array('domain' => $user['domain']);
		
		$req = $this->db->select("A.`name`, A.`description`")
					->from(" `".self::CT_TYPE_DESC."` AS A")
					->where_in("`".self::ID_CT_TYPE."`",$groups_id)
					->where("A.`id_langs` = ".$letter_lang['id_langs']."");
					
		$groups = $req->get()->result_array();
		
		$letter_data_array += array('groups' => $groups);
		
		$this->load->model('customers/mcustomers_settings');
		$sender = $this->mcustomers_settings->get_distribution_settings();
	
		strlen($sender['distribution_email'])>0 ? $sender_email = $sender['distribution_email'] : $sender_email = 'no-reply@'.$user['domain'];
		
		$letter_html = $this->load->view('customers/letters/'.$letter_lang['language'].'/customers_change_type', $letter_data_array, TRUE);
	
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from($sender_email, $user['domain']);
		$this->email->to($customer_data['email']);
		$this->email->subject('Update customer groups!');
		$this->email->message($letter_html);
		$this->email->send();
		$this->email->clear();
	
		return TRUE;
	}
	
	public function check_isset_type($id)
	{
		$id = intval($id);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT_TYPE."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CT_TYPE."`", $id)->limit(1);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset_alias($alias)
	{
		$alias = trim($alias);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CT_TYPE."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_type)
		{
			$query->where("`".self::ID_CT_TYPE."` <>", $this->id_type);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function set_validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('main[alias]','Индификатор','trim|required|check_isset_alias');
		$this->form_validation->set_message('check_isset_alias', 'Группа покупателей с указанным индификатором уже существует!');
	}
	
	public function save($id = FALSE)
	{
		if($this->input->post('main'))
		{
			if($id)
			{
				$this->id_type = $id;
			}
			$this->set_validation();
			if($this->form_validation->run())
			{
				if($id) 
				{
					$query = $this->get_edit_query($id, TRUE); 
					$database_temp_data = $query->get()->result_array();
					if(count($database_temp_data)>0)
					{
						foreach($database_temp_data as $ms)
						{
							$database_data[$ms['id_langs']] = $ms;
						}
						$POST = $this->input->post('main');
						$this->db->trans_start();
						$result = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::CT_TYPE, $id);
						if($result && ($POST = $this->input->post('desc')) != FALSE)
						{
							$this->load->model('langs/mlangs');
							$langs = $this->mlangs->get_active_languages();
							foreach($langs as $key => $ms)
							{
								if (isset($POST[$key]))
								{		
									if(isset($database_data[$key]))
									{
										$data = $POST[$key]; 
										$this->sql_add_data($data)->sql_save(self::CT_TYPE_DESC, $database_data[$key]['DID']);
									}
									else
									{
										$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_CT_TYPE => $id);
										$this->sql_add_data($data)->sql_save(self::CT_TYPE_DESC);
									} 
								}
							}
							$this->db->trans_complete();
							if($this->db->trans_status())
							{
								return TRUE;
							}
							else
							{
								$this->set_post_to_session();
								return FALSE;
							}	
						}
						return FALSE;
					}
					return FALSE;
				}
				else
				{
					$POST = $this->input->post('main');
					$this->db->trans_start();
					$ID = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::CT_TYPE);
					if($ID && $ID > 0 && ($POST = $this->input->post('desc')) != FALSE)
					{
						$this->sql_add_data(array('sort' => $ID))->sql_save(self::CT_TYPE, $ID);
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_CT_TYPE => $ID);
								$this->sql_add_data($data)->sql_save(self::CT_TYPE_DESC); 
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status()) 
						{
							return $ID; 
						}
						else
						{
							$this->set_post_to_session();
							return FALSE;
						}	
					}
					return FALSE;
				}
			}
			else
			{
				$this->messages->add_error_message(validation_errors());
				$this->set_post_to_session();
				return FALSE;
			}
		}
		return FALSE;		
	}
	
	public function set_post_to_session()
	{
		$this->session->set_flashdata('customers_types_add_edit_form', $this->input->post());
		return $this;
	}
	
	public function get_edit_query($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`".self::ID_LANGS."`, B.`".self::ID_CT_TYPE_DESC."` AS DID"; 
		}
		else
		{
			$select = "A.`".self::ID_CT_TYPE."` AS ID, A.`active`, A.`alias`, B.`name`, B.`description`, B.`".self::ID_LANGS."`, B.`".self::ID_CT_TYPE_DESC."`";	
		}
		
		$result = $this->db ->select($select)
							->from("`".self::CT_TYPE."` AS A")
							->join("`".self::CT_TYPE_DESC."` AS B",
								   "B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."`", 
								   "LEFT")
							->where("A.`".self::ID_CT_TYPE."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
		return $result;
	}

	public function delete($id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self::ID_CT_TYPE, $id)->where("`".self::ID_USERS."`",$this->id_users);  
			$this->db->delete(self::CT_TYPE);
			return TRUE;
		}
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: CT_TYPE."` AS A")
							->where("A.`".self :: ID_CT_TYPE."`", $id)->where("`A.`".self::ID_USERS."`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{	
			
			$this->db->where(self::ID_CT_TYPE, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self::CT_TYPE))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
			
	}
	
	public function activate($id, $activate = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $activate); 
			foreach($id as $ms) 
			{
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::CT_TYPE, $ms); 
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function get_customers_types($ID = FALSE)
	{
		$array = array();
		$query = $this->db
				->select("A.`".self::ID_CT_TYPE."` AS ID, B.`name`")
				->from("`".self::CT_TYPE."` AS A")
				->join("`".self::CT_TYPE_DESC."` AS B",
					   "B.`".self::ID_CT_TYPE."` = A.`".self::ID_CT_TYPE."` && `".self::ID_LANGS."` = '".$this->id_langs."'", 
					   "LEFT")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->order_by("`sort`");
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
	
	public function get_customers_group_mails($id)
	{
		if(($cat_id = intval($id))>0)
		{
			if($this->check_isset_type($cat_id))
			{
				$query = $this->db
						->select("D.`email`")
						->from("`".self::CT_TYPE."` AS A")
						->join("`".self::CT_CTYPE."` AS C",
								"A.`".self::ID_CT_TYPE."` = C.`".self::ID_CT_TYPE."`",
								"INNER")
						->join("`".self::CT."` AS D",
						"C.`".self::ID_CT."` = D.`".self::ID_CT."`",
						"LEFT")
						->where("A.`".self::ID_USERS."` = '".$this->id_users."' AND A.`".self::ID_CT_TYPE."` = '".$id."' ");
				$result = $query->get()->result_array();
				//$res = array();
				foreach ($result as $val)
				{	foreach ($val as $val2)
					{
						$res[] = $val2;
					}
				}
							
				return $res;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	
	
}
?>