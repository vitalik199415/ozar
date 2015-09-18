<?php
class Products_attributes_options extends AG_Controller
{
	function __construct()
	{
		parent :: __construct();
		$this->template->add_title('Каталог продукции - ')->add_title('Атрибуты продукции — ')->add_title('Опции атрибутов — ');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Атрибуты продукции', set_url('*/products_attributes'));
		$this->template->add_navigation('Опции атрибутов', set_url('*/*'));
	}
	
	function index()
	{
		$this->load->model('catalogue/mproducts_attributes_options');
		
		if($select = $this->input->post('attributes_options_grid_select'))
		{
			if($checkbox = $this->input->post('attributes_options_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mproducts_attributes_options->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mproducts_attributes_options->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mproducts_attributes_options->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mproducts_attributes_options->render_products_attributes_options_grid();
	}
		
	public function add()
	{
		$this->template->add_title('Добавление опции атрибута');
		$this->template->add_navigation('Добавление опции');
		
		$this->load->model('catalogue/mproducts_attributes_options');
		$this->mproducts_attributes_options->add();
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование опции атрибута');
		$this->template-> add_navigation('Редактирование опции');

		$this->load->model('catalogue/mproducts_attributes_options');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if(!$this->mproducts_attributes_options->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования опции!');
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
			$this->load->model('catalogue/mproducts_attributes_options');
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mproducts_attributes_options->save($ID))
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
					//$this->_back_to_tab();
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}

			}
			else 
			{
				if ($ID = $this->mproducts_attributes_options->save())
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
			
	public	function delete()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_attributes_options');
			if($this->mproducts_attributes_options->delete($id))
			{
				$this->messages->add_success_message('Опция успешно удалена!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Опция с ID = '.$id.' не существует, или произошла ошибка при удалении!');
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
			$this->load->model('catalogue/mproducts_attributes_options');
			if($this->mproducts_attributes_options->change_position($id, $URI['type']))
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
				$this->load->model('catalogue/mproducts_attributes_options');
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_attributes_options->id_attribute_option = $id;
				}
				if($this->mproducts_attributes_options->check_isset_alias($alias))
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