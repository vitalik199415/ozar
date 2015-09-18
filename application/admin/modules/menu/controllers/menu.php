<?php
class Menu extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Меню сайта');
		$this->template->add_navigation('Меню сайта')->add_navigation('Меню',set_url('*/'));
	}
	
	public function index()
	{
		$this->template->add_title(' - Меню сайта');
		$this->load->model('mmenu');
		
		if($select = $this->input->post('menu_selected'))
		{
			if ($checkbox = $this->input->post('menu_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mmenu->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно.');
					break;
					case "delete_all":
						$this->mmenu->delete($data_ID, TRUE);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно.');
					break;
					case "on":
						$this->mmenu->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно.');
					break;
					case "off":
						$this->mmenu->activate($data_ID,0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно.');
					break;
				}
			}
		}
		
		if($this->input->post('type') && $this->input->post('cid') && intval($this->input->post('cid')) > 0)
		{
			if($this->mmenu->change_position($this->input->post('type'), intval($this->input->post('cid'))))
			{
				$this->messages->add_success_message('Смена позиции меню с ID '.$this->input->post('cid').' прошла успешно.');
			}
			else
			{
				$this->messages->add_error_message('Смена позиции меню не возможна!');
			}
		}
		
		$this->mmenu->render_menu_grid();
	}
	
	function add()
	{
		$this->template->add_title(' - Добавление меню');
		$this->template->add_navigation('Добавление меню');
		
		$this->load->model('mmenu');
		$this->mmenu->add();
	}
	
	function edit()
	{
		$this->template->add_title(' - Редактирование меню');
		$this->template->add_navigation('Редактирование меню');
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('mmenu');
			if($this->mmenu->isset_menu($ID))
			{
				$this->mmenu->edit($ID);
			}
			else
			{
				$this->messages->add_error_message('Меню с ID = '.$ID.' не существует!');
				$this->_redirect(set_url('*/'));
			}
			
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function save()
	{
		if($this->input->post('menu'))
		{
			$this->load->model('mmenu');
			$URI = $this->uri->uri_to_assoc(3);
			if (isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mmenu->save($ID))
				{
					$this->messages->add_success_message('Меню с ID = '.$ID.' успешно отредактировано!');
					$this->_redirect(set_url('*/'));
					
					if(isset($_GET['return']))
					{
						$this->_back_to_tab();
						$this->_redirect(set_url('*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании меню!');
					$this->_redirect(set_url('*/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mmenu->save())
				{
					$this->messages->add_success_message('Меню удачно добавлено!');
					$this->_redirect(set_url('*/'));
					
					if (isset($_GET['return']))
					{
						$this->_back_to_tab();
						$this->_redirect(set_url('*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникла ошибка при добавлении нового меню!');
					$this->_redirect(set_url('*/add'));
				}
			}
		}
		else
		{
			$this->_redirect(set_url('*/'));
		}	
	}
	
	public function check_url()
	{
		$this->load->model('mmenu');
		if($url = $this->input->post('menu'))
		{
			if(isset($url['url']))
			{
				$url = $url['url'];
				
				$URI = $this->uri->uri_to_assoc(3);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mmenu->id_menu = $id;
				}
				if($this->mmenu->check_isset_url($url))
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
	
	public function load_menu()
	{
		if(isset($_POST['level']) && intval($_POST['level']) > 0)
		{
			if(isset($_POST['id']) && ($id = intval($_POST['id'])) > 0) $id = intval($_POST['id']); else $id = FALSE;
			$level = intval($_POST['level']);
			$this->load->model('mmenu');
			$this->mmenu->load_menu($level, $id);
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$id= intval($URI['id']);
			$this->load->model('mmenu');
			if($this->mmenu->delete($id))
			{
				$this->messages->add_success_message('Меню успешно удалено со всеми подкатегориями!');
				$this->_redirect(set_url('*/'));
			}
			else
			{
				$this->messages->add_error_message('Меню с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->_add_error_massage('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function menu_modules()
	{
		$this->template->add_title(' - Добавление модулей к меню');
		$this->template->add_navigation('Добавление модулей к меню');
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('mmenu');
			if($this->mmenu->isset_menu($ID))
			{
				$this->mmenu->menu_modules($ID);
			}
			else
			{
				$this->messages->add_error_message('Меню с ID = '.$ID.' не существует!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Добаление невозможно!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function modules_save()
	{
		if($this->input->post('save'))
		{
			$this->load->model('mmenu');
			$URI = $this->uri->uri_to_assoc(3);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('mmenu');
				if($this->mmenu->isset_menu($ID))
				{
					if($this->mmenu->modules_save($ID))
					{
						$this->messages->add_success_message('К меню с ID = '.$ID.' успешно добавлены модули!');
						$this->_redirect(set_url('*/menu_modules/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Модули не выбраны. Сохранение не возможно!');
						$this->_redirect(set_url('*/menu_modules/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Меню с ID = '.$ID.' не существует!');
					$this->_redirect(set_url('*/menu_modules/id/'.$ID));
				}
				
			}
			else
			{
				$this->messages->add_error_message('Параметр ID отсутствует! Добаление невозможно!');
				$this->_redirect(set_url('*/menu_modules/id/'.$ID));
			}
		}
		else 
		{
				$this->_redirect(set_url('*/menu_modules/id/'.$ID));
		}
	}
	
	public function delete_menu_modul()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0 )
		{
			if(isset($URI['id_module']) && intval($URI['id_module'])>0)
			{
				$id = intval($URI['id']);
				$id_module = intval($URI['id_module']);
				$this->load->model('mmenu');
				if($this->mmenu->isset_menu($id))
				{
					$this->mmenu->delete_menu_modul($id, $id_module);
					$this->messages->add_success_message('Модуль успешно удален!');
					$this->_redirect(set_url('*/menu_modules/id/'.$id));
				}
				else
				{
					$this->messages->add_error_message('Модуль с ID = '.$id_module.' не существует, или произошла ошибка при удалении!');
					$this->_redirect(set_url('*/'));
				}
			}
			else
			{
				$this->messages->_add_error_massage('Параметр ID отсутствует! Процес удаления модуля не возможен!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->_add_error_massage('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function change_position_module() 
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0 )
		{
			if(isset($URI['id_module']) && intval($URI['id_module'])>0)
			{
				$id = intval($URI['id']);
				$id_module = intval($URI['id_module']);
				$type = $URI['change'];
				$this->load->model('mmenu');
				if($this->mmenu->isset_menu($id))
				{
					$this->mmenu->change_position_module($id, $id_module, $type);
					$this->messages->add_success_message('Позиция успешно изменена!');
					$this->_redirect(set_url('*/menu_modules/id/'.$id));
				}
				else
				{
					$this->messages->add_error_message('Меню ID = '.$id.' не существует, или произошла ошибка при удалении!');
					$this->_redirect(set_url('*/'));
				}
			}
			else
			{
				$this->messages->_add_error_massage('Параметр ID отсутствует! Процес изменения позици не возможно');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->_add_error_massage('Параметр ID отсутствует! Процес изменения не возможен!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function change_base_module()
	{
		if($this->input->post('id_menu'))
		{
			if(($id = (int) $this->input->post('id')) > 0 && ($id_menu = (int) $this->input->post('id_menu')) > 0)
			{
				$this->load->model('mmenu');
				if($this->mmenu->isset_menu($id_menu))
				{
					$this->mmenu->change_base_module($id_menu, $id);
				}
				else
				{
					echo "Меню с переданным айди не существует!";
				}
			}
		}
		else
		{
			echo "FUCK OFF";
		}
	}
}
?>