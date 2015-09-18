<?php
class Products_properties extends Ag_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции - Свойства продуктов');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Группы свойств продукции',set_url('*/products_types'))->add_navigation('Свойства продуктов',set_url('*/products_properties'));
	}
	
	public function index()
	{
		$this->load->model('catalogue/mproducts_properties');
		if($selected = $this->input->post('products_properties_grid_select'))
		{
			if($checkbox = $this->input->post('products_properties_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;	
				}
				switch($selected)
				{
					case 'on':
						$this->mproducts_properties->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case 'off':
						$this->mproducts_properties->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно');
					break;
					case 'delete':
						$this->mproducts_properties->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно');
					break;
				}
			}
		}
		$this->mproducts_properties->render_properties_grid();
	}
	
	public function add()
	{
		$this->template->add_title('Добавление свойства');
		$this->template->add_navigation('Добавление свойства');
		
		$this->load->model('catalogue/mproducts_properties');
		$this->mproducts_properties->add();
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование свойства');
		$this->template->add_navigation('Редактирование свойства');
		
		$this->load->model('catalogue/mproducts_properties');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if(!$this->mproducts_properties->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования свойства!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('catalogue/mproducts_properties');
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && ($ID = intval($URI['id'])) > 0)
			{
				if($this->mproducts_properties->save($ID))
				{
					$this->messages->add_success_message('Свойство успешно отредактировано!');
					$this->_redirect(set_url('*/*/'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании свойства.');
					$this->_redirect(set_url('*/*/'));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mproducts_properties->save())
				{
					$this->messages->add_success_message('Свойство продукции успешно добавлено.');
					$this->_redirect(set_url('*/*'));
					if(isset($_GET['return']))
					{
						$this->_back_to_tab();
						$this->_redirect(set_url('*/*/edit/id/'.$ID));	
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании свойства.');
					$this->_redirect(set_url('*/*'));
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
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0)
		{
			$this->load->model('catalogue/mproducts_properties');
			if($this->mproducts_properties->delete($id))
			{
				$this->messages->add_success_message('Свойство успешно удалено!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Свойство с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function change_position()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0 && ($URI['type'] == 'up' || $URI['type'] == 'down'))
		{
			$this->load->model('catalogue/mproducts_properties');
			if($this->mproducts_properties->change_position($id, $URI['type']))
			{
				$this->messages->add_success_message('Смена позиции прошла успешно!');
				$this->index();
			}
			else
			{
				$this->messages->add_error_message('Смена позиции не возможна!');
				$this->index();
			}
		}
		else
		{
			$this->index();
		}
	}
	
	public function check_alias()
	{
		if($alias = $this->input->post('main'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				$this->load->model('catalogue/mproducts_properties');
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_properties->id_propertie = $id;
				}
				if($this->mproducts_properties->check_isset_alias($alias))
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