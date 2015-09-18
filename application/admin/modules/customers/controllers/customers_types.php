<?php
class Customers_types extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Покупатели | Группы покупателей');
		$this->template->add_navigation('Покупатели')->add_navigation('Группы покупателей', set_url('*/*'));
	}
	
	public function index()
	{
		$this->load->model('customers/mcustomers_types');
		if ($select = $this->input->post('customers_types_grid_select'))
		{
			if ($checkbox = $this->input->post('customers_types_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mcustomers_types->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно');
					break;
					case "on":
						$this->mcustomers_types->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно');
					break;
					case "off":
						$this->mcustomers_types->activate($data_ID,0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно');
					break;
				}
			}
		}
		$this->mcustomers_types->render_customers_types_grid();
	}
	
	public function action()
	{		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$this->load->model('customers/mcustomers_types');
			$ID = intval($URI['id']);
			if(!$this->mcustomers_types->action($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации просмотра группы покупателей!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function add()
	{
		$this->template->add_title('Добавление группы покупателей');
		$this->template->add_navigation('Добавление группы покупателей');

		$this->load->model('customers/mcustomers_types');
		$this->mcustomers_types->add();
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование группы покупателей');
		$this->template->add_navigation('Редактирование группы покупателей');
		
		$this->load->model('customers/mcustomers_types');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mcustomers_types->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования группы покупателей!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function save()
	{
		if($this->input->post('main'))
		{
			$this->load->model('customers/mcustomers_types');
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mcustomers_types->save($ID))
				{
					$this->messages->add_success_message('Группа покупателей успешно отредактирована.');
					$this->_redirect(set_url('*/*')); 
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании группы покупателей!');
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
				if(isset($_GET['return'])) 
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else 
			{
				if($ID = $this->mcustomers_types->save()) 
				{
					$this->messages->add_success_message('Группа покупателей продукции успешно добавлена.');
					$this->_redirect(set_url('*/*')); 
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/edit/id/'.$ID)); 
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении группы покупателей!');
					$this->_redirect(set_url('*/*/add'));
				}
			}
		}
		else
		{
			$this->_redirect(set_url('*/*'));		
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$id= intval($URI['id']);
			$this->load->model('customers/mcustomers_types');
			if($this->mcustomers_types->delete($id))
			{
				$this->messages->add_success_message('Группа покупателей успешно удален!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->messages->add_error_message('Группа покупателей с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function mailing_form()
	{	
		$this->load->model('customers/mcustomers_types');
				
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id'])) > 0)
		{
			if(!$this->mcustomers_types->mailing_form($ID))
			{
				$this->messages->add_error_message('Возникли ошибки рассылки!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Рассылка невозможна!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function create_mailing()
	{	
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('customers/mcustomers_types');
			if($this->mcustomers_types->create_mailing($ID))
			{
				$this->messages->add_success_message('Рассылка успешно произведена!');
				$this->_redirect(set_url('*/*/action/id/'.$ID));
			}
			else
			{
				$this->messages->add_error_message('Не возможно осуществить рассилку!');
				$this->_redirect(set_url('*/*/mailing_form/id/'.$ID));
			}
		}
		else
		{
			$this->messages->add_success_message('Отсутсвуют параметры!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function check_alias()
	{
		if($alias = $this->input->post('main'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				$this->load->model('customers/mcustomers_types');
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mcustomers_types->id_type = $id;
				}
				if($this->mcustomers_types->check_isset_alias($alias))
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
	
	public function get_ajax_types_customers()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('customers/mcustomers_types');
			echo $this->mcustomers_types->build_types_customers_grid($id);
		}
	}
	
	public function get_ajax_types_customers_mailing()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('customers/mcustomers_types');
			echo $this->mcustomers_types->build_types_customers_mailing_grid($id);
		}
	}
}
?>