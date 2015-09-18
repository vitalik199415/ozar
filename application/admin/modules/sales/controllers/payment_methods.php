<?php
class Payment_methods extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Заказы - Методы оплаты');
		$this->template->add_navigation('Заказы')->add_navigation('Методы оплаты', setUrl('*/*/'));
	}
	
	public function index()
	{
		$this->load->model('sales/mpayment_methods');
		if($select = $this->input->post('users_payment_methods_action'))
		{
			if($checkbox = $this->input->post('users_payment_methods_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "active_on":
						$this->mpayment_methods->active_payment_method($data_ID, 1);
						$this->messages->add_success_message('Выбраные позиции успешно активированы.');
					break;
					case "active_off":
						$this->mpayment_methods->active_payment_method($data_ID, 0);
						$this->messages->add_success_message('Выбраные позиции успешно деактивированы.');
					break;
				}
			}
		}
		$this->mpayment_methods->get_users_payment_methods_collection();
	}
	
	public function add()
	{
		$this->template->add_title(' - Добавить метод оплаты');
		$this->template->add_navigation('Добавить метод оплаты');
		$this->load->model('sales/mpayment_methods');
		$this->mpayment_methods->add();
	}
	
	public function add_save()
	{
		if($id = $this->input->post('payment_method'))
		{
			$this->_redirect(set_url('*/*/add_method/id/'.$id));
		}
		else
		{
			$this->_redirect(set_url('*/*/add'));
		}
	}
	
	public function add_method()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->template->add_title(' - Добавить метод оплаты');
			$this->template->add_navigation('Добавить метод оплаты');
			$this->load->model('sales/mpayment_methods');
			if(!$this->mpayment_methods->add_method($id))
			{
				$this->messages->add_error_message('Возникли ошибки при обработке метода оплаты!');
				$this->_redirect(set_url('*/*/add'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/add'));
		}
	}
	
	public function edit_method()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id_users_pm']) && ($id = intval($URI['id_users_pm']))>0)
		{
			$this->template->add_title(' - Редактирование метода оплаты');
			$this->template->add_navigation('Редактирование метода оплаты');
			$this->load->model('sales/mpayment_methods');
			if(!$this->mpayment_methods->edit_method($id))
			{
				$this->messages->add_error_message('Возникли ошибки при обработке метода оплаты!');
				$this->_redirect(set_url('*/*/add'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function delete_method()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id_users_pm']) && ($id = intval($URI['id_users_pm']))>0)
		{
			$this->load->model('sales/mpayment_methods');
			if($this->mpayment_methods->delete_payment_method($id))
			{
				$this->messages->add_success_message('Метод оплаты успешно удален!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при удалении метода оплаты!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function save()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id_users_pm']) && ($id = intval($URI['id_users_pm']))>0)
		{
			$this->load->model('sales/mpayment_methods');
			if($this->mpayment_methods->save($id))
			{
				$this->messages->add_success_message('Метод оплаты успешно отредактирован.');
				$this->_redirect(set_url('*/*/')); 
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_method/id_users_pm/'.$id)); 
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании метода оплаты. Попробуйте еще раз.');
				$this->_redirect(set_url('*/*/edit_method/id_users_pm/'.$id));
			}
		}
		else
		{
			$this->load->model('sales/mpayment_methods');
			if($id = $this->mpayment_methods->save())
			{
				$this->messages->add_success_message('Метод оплаты успешно добавлен.');
				$this->_redirect(set_url('*/*/')); 
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_method/id_users_pm/'.$id)); 
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении метода оплаты. Попробуйте еще раз.');
				$this->_redirect(set_url('*/*/')); 
			}
		}
	}
	
	public function check_alias()
	{
		$this->load->model('sales/mpayment_methods');
		if($alias = $this->input->post('payment_method'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id_users_pm']) && ($id_users_pm = intval($URI['id_users_pm']))>0)
				{
					$this->mpayment_methods->id_users_pm = $id_users_pm;
				}
				if($this->mpayment_methods->check_isset_alias($alias))
				{
					echo json_encode(true);
				}
				else
				{
					echo json_encode(false);
				}
			}	
		}
	}
}
?>