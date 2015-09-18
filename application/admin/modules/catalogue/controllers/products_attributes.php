<?php
class Products_attributes extends AG_Controller
{
	function __construct()
	{
		parent :: __construct();
		$this->template->add_title('Каталог продукции - ')->add_title('Атрибуты продукции — ');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Атрибуты продукции', set_url('*/*/'));
	}
	
	public function index()
	{
		$this->load->model('catalogue/mproducts_attributes');
		if($select = $this->input->post('attributes_grid_select'))
		{
			if($checkbox = $this->input->post('attributes_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mproducts_attributes->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mproducts_attributes->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mproducts_attributes->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mproducts_attributes->render_attributes_grid();
	}
	
	public function add()
	{
		$this->template->add_title('Добавление атрибута');
		$this->template->add_navigation('Добавление атрибута');
		
		$this->load->model('catalogue/mproducts_attributes');
		$this->mproducts_attributes->add();
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование атрибута');
		$this->template->add_navigation('Редактирование атрибута');
		
		$this->load->model('catalogue/mproducts_attributes');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if(!$this->mproducts_attributes->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования атрибутов!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('catalogue/mproducts_attributes');
			$URI = $this->uri->uri_to_assoc(4);
			if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mproducts_attributes->save($ID))
				{
					$this->messages->add_success_message('Атрибут успешно отредактирован!');
					$this->_redirect(set_url('*/*/'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании атрибута!');
					$this->_redirect(set_url('*/*/'));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else 
			{
				if ($ID = $this->mproducts_attributes->save())
				{
					$this->messages->add_success_message('Атрибут удачно добавлен!');
					$this->_redirect(set_url('*/*/'));
					
					if (isset($_GET['return']))
					{
						$this->_back_to_tab();
						$this->_redirect(set_url('*/*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникла ошибка при добавлении нового атрибута!');
					$this->_redirect(set_url('*/*/'));
				}
			}	
		}
		else
		{
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_attributes');
			if($this->mproducts_attributes->delete($id))
			{
				$this->messages->add_success_message('Атрибут успешно удален!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Атрибут с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url('*/*/'));
		}	
	}
	
	public function change_position()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0 && ($URI['type'] == 'up' || $URI['type'] == 'down'))
		{
			$this->load->model('catalogue/mproducts_attributes');
			if($this->mproducts_attributes->change_position($id, $URI['type']))
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
				$this->load->model('catalogue/mproducts_attributes');
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_attributes->id_attribute = $id;
				}
				if($this->mproducts_attributes->check_isset_alias($alias))
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