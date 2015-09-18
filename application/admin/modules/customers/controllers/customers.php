<?php
class Customers extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Покупатели');
		$this->template->add_navigation('Покупатели', set_url('*'));
	}
	
	public function index()
	{
		$this->load->model('mcustomers');
		if($action = $this->input->post('actions_with_customers'))
		{
			if($checkbox = $this->input->post('customers_checkbox'))
			{
				if(is_array($checkbox))
				{
					
					switch($action)
					{
						case "delete":
							$this->mcustomers->delete($checkbox);
							$this->messages->add_ajax_success_message('Удаление выбраных покупателей прошло успешно!');
						break;
						case "on":
							$this->mcustomers->set_active($checkbox);
							$this->messages->add_ajax_success_message('Активация выбраных покупателей прошла успешно!');
						break;
						case "off":
							$this->mcustomers->set_active($checkbox, 0);
							$this->messages->add_ajax_success_message('Деактивация выбраных покупателей прошла успешно!');
						break;
					}
				}
			}
		}
		$this->mcustomers->render_customers_grid();
	}
	
	public function add()
	{
		$this->template->add_title(' | Добавить покупателя');
		$this->template->add_navigation('Добавить покупателя');
		
		$this->load->model('mcustomers');
		$this->mcustomers->add();
		
		$this->template->add_js('jquery.gbc_customer_add','modules_js/customers');
	}
	
	public function edit()
	{
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$URI = $this->uri->uri_to_assoc(3);
		if (isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->template->add_title(' | Редактировать покупателя');
			$this->template->add_navigation('Редактировать покупателя');
			
			$this->load->model('mcustomers');
			$this->template->add_js('jquery.gbc_customer_add','modules_js/customers');
			if(!$this->mcustomers->edit($id))
			{
				$this->messages->add_error_message('Покупатель отсутствует! Редактирование не возможно!');
				$this->_redirect(set_url('*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование не возможно!');
			$this->_redirect(set_url('*'));
		}
	}
	
	public function view()
	{
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$URI = $this->uri->uri_to_assoc(3);
		if (isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->template->add_title(' | Просмотр покупателя');
			$this->template->add_navigation('Просмотр покупателя');
			
			$this->load->model('mcustomers');
			if(!$this->mcustomers->view($id))
			{
				$this->messages->add_error_message('Просмотр пользователя не возможен!');
				$this->_redirect(set_url('*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Просмотр пользователя не возможен!');
			$this->_redirect(set_url('*'));
		}
	}
	
	public function save()
	{
		$this->load->model('mcustomers');
		$URI = $this->uri->uri_to_assoc(3);
		if (isset($URI['id']))
		{
			if(($ID = intval($URI['id'])) == 0) { $this->messages->add_error_message('Процесс редактирования невозможен!');$this->_redirect(set_url('*'));return FALSE; }
			if($this->mcustomers->save($ID))
			{
				$this->messages->add_success_message('Покупатель успешно отредактирован!');
				$this->_redirect(set_url('*'));
				
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/edit/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании Покупателя!');
				$this->_redirect(set_url('*/edit/id/'.$ID));
			}
		}
		else
		{
			if($ID = $this->mcustomers->save())
			{
				$this->messages->add_success_message('Покупатель удачно добавлен!');
				$this->_redirect(set_url('*'));
				
				if (isset($_GET['return']))
				{
					$this->_redirect(set_url('*/edit/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Возникла ошибка при добавлении нового покупателя!');
				$this->_redirect(set_url('*/add'));
			}
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('mcustomers');
			if($this->mcustomers->delete($id))
			{
				$this->messages->add_success_message('Покупатель успешно удален!');
				$this->_redirect(set_url('*'));
			}
			else
			{
				$this->messages->add_error_message('Покупатель отсутствует! Удаление не возможно!');
				$this->_redirect(set_url('*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Удаление не возможно!');
			$this->_redirect(set_url('*'));
		}
	}
	
	public function check_email()
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
					if($this->mcustomers->check_isset_ct($id))
					{
						$this->mcustomers->set_ct_id($id);
					}
					else
					{
						echo json_encode(false);
					}
				}
				echo json_encode($this->mcustomers->check_isset_ct_email($email));
			}
		}
	}
	
	public function get_ajax_customer_orders()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('mcustomers');
			echo $this->mcustomers->get_customer_orders_collection($id);
		}
	}
	
	/*public function get_emails()
	{
		$arr = array();
		$this->db->select("`email`")
			->from("`m_u_customers`")->where_in("id_users", array(12126,12036,11840,11766,10354,11115,11440,10369,11152))
			->limit(400, 2800);
			
		$result = $this->db->get()->result_array();
		
		foreach($result as $ms)
		{
			$arr[] = $ms['email'];
		}
		
		echo implode(', ', $arr);
	}*/
}
?>