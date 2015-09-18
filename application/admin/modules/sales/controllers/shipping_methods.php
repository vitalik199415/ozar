<?php
class Shipping_methods extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Заказы - Методы доставки');
		$this->template->add_navigation('Заказы')->add_navigation('Методы доставки', setUrl('*/*/'));
	}
	
	public function index()
	{
		$this->load->model('sales/mshipping_methods');
		if($select = $this->input->post('users_shipping_methods_action'))
		{
			if($checkbox = $this->input->post('users_shipping_methods_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "active_on":
						$this->mshipping_methods->active_shipping_method($data_ID, 1);
						$this->messages->add_success_message('Выбраные позиции успешно активированы.');
					break;
					case "active_off":
						$this->mshipping_methods->active_shipping_method($data_ID, 0);
						$this->messages->add_success_message('Выбраные позиции успешно деактивированы.');
					break;
				}
			}
		}
		$this->mshipping_methods->get_users_shipping_methods_collection();
	}
	
	public function add()
	{
		$this->template->add_title(' - Добавить метод доставки');
		$this->template->add_navigation('Добавить метод доставки');
		$this->load->model('sales/mshipping_methods');
		$this->mshipping_methods->add();
	}
	
	public function add_save()
	{
		if($id = $this->input->post('shipping_method'))
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
			$this->template->add_title(' - Добавить метод доставки');
			$this->template->add_navigation('Добавить метод доставки');
			$this->load->model('sales/mshipping_methods');
			if(!$this->mshipping_methods->add_method($id))
			{
				$this->messages->add_error_message('Возникли ошибки при обработке метода доставки!');
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
		if(isset($URI['id_users_sm']) && ($id = intval($URI['id_users_sm']))>0)
		{
			$this->template->add_title(' - Редактирование метода доставки');
			$this->template->add_navigation('Редактирование метода доставки');
			$this->load->model('sales/mshipping_methods');
			if(!$this->mshipping_methods->edit_method($id))
			{
				$this->messages->add_error_message('Возникли ошибки при обработке метода доставки!');
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
		if(isset($URI['id_users_sm']) && ($id = intval($URI['id_users_sm']))>0)
		{
			$this->load->model('sales/mshipping_methods');
			if($this->mshipping_methods->delete_shipping_method($id))
			{
				$this->messages->add_success_message('Метод доставки успешно удален!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при удалении метода доставки!');
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
		if(isset($URI['id_users_sm']) && ($id = intval($URI['id_users_sm']))>0)
		{
			$this->load->model('sales/mshipping_methods');
			if($this->mshipping_methods->save($id))
			{
				$this->messages->add_success_message('Метод доставки успешно отредактирован.');
				$this->_redirect(set_url('*/*/')); 
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_method/id_users_sm/'.$id)); 
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании метода доставки. Попробуйте еще раз.');
				$this->_redirect(set_url('*/*/edit_method/id_users_sm/'.$id));
			}
		}
		else
		{
			$this->load->model('sales/mshipping_methods');
			if($id = $this->mshipping_methods->save())
			{
				$this->messages->add_success_message('Метод доставки успешно добавлен.');
				$this->_redirect(set_url('*/*/')); 
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_method/id_users_sm/'.$id)); 
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении метода доставки. Попробуйте еще раз.');
				$this->_redirect(set_url('*/*/')); 
			}
		}
	}
	
	public function check_alias()
	{
		$this->load->model('sales/mshipping_methods');
		if($alias = $this->input->post('shipping_method'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id_users_sm']) && ($id_users_sm = intval($URI['id_users_sm']))>0)
				{
					$this->mshipping_methods->id_users_sm = $id_users_sm;
				}
				if($this->mshipping_methods->check_isset_alias($alias))
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