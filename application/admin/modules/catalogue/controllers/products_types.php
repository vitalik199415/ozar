<?php
class Products_types extends AG_Controller
{
	function __construct()
	{
		parent:: __construct();
		$this->template->add_title('Каталог продукции - ')->add_title('Группы фильтров');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Группы фильтров', set_url('*/products_types'));
	}
	
	public function index()
	{
		$this->load->model('catalogue/mproducts_types');
		if ($select = $this->input->post('products_types_grid_select'))
		{
			if ($checkbox = $this->input->post('products_types_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mproducts_types->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно');
					break;
					case "on":
						$this->mproducts_types->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно');
					break;
					case "off":
						$this->mproducts_types->activate($data_ID,0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно');
					break;
				}
			}
		}
		$this->mproducts_types->render_types_grid();
	}	
	
	public function add()
	{
		$this->template->add_title('Добавить группу');
		$this->template->add_navigation('Добавить группу');

		$this->load->model('catalogue/mproducts_types');
		$this->mproducts_types->add();
	}
	
	public function edit()
	{
		$this->load->model('catalogue/mproducts_types');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->template->add_navigation('Редактирование группы');
			if(!$this->mproducts_types->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки редактирования!');
				$this->_redirect(set_url('*/*'));
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
		if($this->input->post('main'))
		{
			$this->load->model('catalogue/mproducts_types');
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mproducts_types->save($ID))
				{
					$this->messages->add_success_message('Группа фильтров успешно отредактирована.');
					$this->_redirect(set_url('*/*'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании группы!');
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mproducts_types->save())
				{
					$this->messages->add_success_message('Группа фильтров успешно добавлена.');
					$this->_redirect(set_url('*/*'));
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении!');
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
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_types');
			if($this->mproducts_types->delete($id))
			{
				$this->messages->add_success_message('Группа фильтров успешно удалена!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Группа не существует, или произошла ошибка при удалении!');
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
			$this->load->model('catalogue/mproducts_types');
			if($this->mproducts_types->change_position($id, $URI['type']))
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
				$this->load->model('catalogue/mproducts_types');

				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_types->id_type = $id;
				}
				if($this->mproducts_types->check_isset_alias($alias)) echo json_encode(true);
				else echo json_encode(false);
			}
			else echo json_encode(false);
		}
		else echo json_encode(false);
	}

	public function type_properties()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['type_id']) && ($type_id = intval($URI['type_id']))>0)
		{
			$this->load->model('catalogue/mproducts_types');

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
							$this->mproducts_types->activate_property($data_ID);
							$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
							break;
						case 'off':
							$this->mproducts_types->activate_property($data_ID, 0);
							$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно');
							break;
						case 'delete':
							$this->mproducts_types->delete_property($type_id, $data_ID);
							$this->messages->add_success_message('Удаление выбраных позиций прошло успешно');
							break;
					}
				}
			}

			if(!$this->mproducts_types->type_properties($type_id))
			{
				$this->messages->add_error_message('Возникли ошибки!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function add_property()
	{
		$this->template->add_css('jPicker-1.1.6', 'jpicker');
		$this->template->add_js('jpicker-1.1.6.min', 'jpicker');

		$this->load->model('catalogue/mproducts_types');

		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['type_id']) && ($type_id = intval($URI['type_id']))>0)
		{
			if(!$this->mproducts_types->add_property($type_id))
			{
				$this->messages->add_error_message('Возникли ошибки добавления свойства!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Добавление невозможно!');
			$this->_redirect(set_url('*/*'));
		}

	}

	public function edit_property()
	{
		$this->template->add_css('jPicker-1.1.6', 'jpicker');
		$this->template->add_js('jpicker-1.1.6.min', 'jpicker');

		$this->load->model('catalogue/mproducts_types');

		$URI = $this->uri->uri_to_assoc(4);
		if((isset($URI['prop_id']) && ($prop_id = intval($URI['prop_id']))>0) && isset($URI['type_id']) && ($type_id = intval($URI['type_id']))>0)
		{
			if(!$this->mproducts_types->edit_property($type_id, $prop_id))
			{
				$this->messages->add_error_message('Возникли ошибки редактирования свойства!');
				$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function save_property()
	{
		$this->load->model('catalogue/mproducts_types');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['type_id']) && ($type_id = intval($URI['type_id'])) > 0)
		{
			if(isset($URI['prop_id']) && ($prop_id = intval($URI['prop_id'])) > 0)
			{

				if($this->mproducts_types->save_property($type_id, $prop_id))
				{
					$this->messages->add_success_message('Свойство успешно отредактировано!');
					$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании свойства.');
					$this->_redirect(set_url('*/*'));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_property/type_id/'.$type_id.'/prop_id/'.$prop_id));
				}
			}
			else
			{
				if($prop_id = $this->mproducts_types->save_property($type_id))
				{
					$this->messages->add_success_message('Свойство успешно добавлено.');
					$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/edit_property/type_id/'.$type_id.'/prop_id/'.$prop_id));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении свойства.');
					$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
				}
			}
		}
	}

	public function delete_property()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if((isset($URI['prop_id']) && ($prop_id = intval($URI['prop_id']))>0) &&((isset($URI['type_id']) && ($type_id = intval($URI['type_id']))>0)))
		{
			$this->load->model('catalogue/mproducts_types');
			if($this->mproducts_types->delete_property($type_id, $prop_id))
			{
				$this->messages->add_success_message('Свойство успешно удалено!');
				$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
			}
			else
			{
				$this->messages->add_error_message('Произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*/type_properties/type_id/'.$type_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function change_property_position()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if((isset($URI['prop_id']) && ($prop_id = intval($URI['prop_id']))>0) && (isset($URI['type_id']) && ($type_id = intval($URI['type_id']))>0) && ($URI['type'] == 'up' || $URI['type'] == 'down'))
		{
			$this->load->model('catalogue/mproducts_types');
			if($this->mproducts_types->change_property_position($prop_id, $URI['type']))
			{
				$this->messages->add_success_message('Смена позиции прошла успешно!');
				$this->type_properties();
			}
			else
			{
				$this->messages->add_error_message('Смена позиции не возможна!');
				$this->type_properties();
			}
		}
		else
		{
			$this->type_properties();
		}
	}

	public function check_property_alias()
	{
		if($alias = $this->input->post('main'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				$this->load->model('catalogue/mproducts_types');

				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['prop_id']) && ($prop_id = intval($URI['prop_id']))>0)
				{
					$this->mproducts_types->id_prop = $prop_id;
				}
				if($this->mproducts_types->check_isset_property_alias($alias)) echo json_encode(true);
				else echo json_encode(false);
			}
			else echo json_encode(false);
		}
		else echo json_encode(false);
	}
}