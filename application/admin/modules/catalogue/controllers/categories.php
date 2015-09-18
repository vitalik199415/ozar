<?php
class Categories extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции - Категории каталога');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Категории каталога',set_url('*/*/'));
	}
	
	public function index()
	{
		$this->load->model('mcategories');
			if ($select = $this->input->post('categories_selected'))
			{
				if ($checkbox = $this->input->post('categories_checkbox'))
				{
					$data_ID = array();
					foreach($checkbox as $ms)
					{
						$data_ID[] = $ms;
					}
					switch($select)
					{
						case "delete":
							$this->mcategories->delete($data_ID);
							$this->messages->add_success_message('Удаление выбраных позиций прошло успешно.');
						break;
						case "delete_all":
							$this->mcategories->delete($data_ID, TRUE);
							$this->messages->add_success_message('Удаление выбраных позиций прошло успешно.');
						break;
						case "on":
							$this->mcategories->activate($data_ID);
							$this->messages->add_success_message('Активация выбраных позиций прошла успешно.');
						break;
						case "off":
							$this->mcategories->activate($data_ID,0);
							$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно.');
						break;
					}
				}
			}
			if($this->input->post('type') && ($cid = intval($this->input->post('cid'))) > 0)
			{
				if($this->mcategories->changePosition($this->input->post('type'), $cid))
				{
					$this->messages->add_success_message('Смена позиции категории с ID '.$cid.' прошла успешно.');
				}
				else
				{
					$this->messages->add_error_message('Смена позиции категории не возможна!');
				}
			}
		
		$this->mcategories->render_categories_grid();
	}
	
	function add()
	{
		$this->template->add_title(' - Добавление категории');
		$this->template->add_navigation('Добавление категории');
		
		$this->load->model('mcategories');
		$this->mcategories->add();
	}
	
	function edit()
	{
		$this->template->add_title(' - Редактирование категории');
		$this->template->add_navigation('Редактирование категории');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('mcategories');
			$this->mcategories->edit($ID);
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	public function save()
	{
		if($this->input->post('categories'))
		{
			$this->load->model('mcategories');
			$URI = $this->uri->uri_to_assoc(4);
			if (isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mcategories->save($ID))
				{
					$this->messages->add_success_message('Категория с ID = '.$ID.' успешно отредактирована!');
					$this->_redirect(set_url('*/*/'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании категории!');
					$this->_redirect(set_url('*/*/'));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mcategories->save())
				{
					$this->messages->add_success_message('Категия удачно добавлена!');
					$this->_redirect(set_url('*/*/'));
					
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникла ошибка при добавлении новой категории!');
					$this->_redirect(set_url('*/*/'));
				}
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/'));
		}	
	}
	
	public function load_categories()
	{
		if(($level = (int) $this->input->post('level')) > 0)
		{
			$id = $this->input->post('id');
			$this->load->model('mcategories');
			$this->mcategories->load_categories($level, $id);
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$id = intval($URI['id']);
			$this->load->model('mcategories');
			if($this->mcategories->delete($id))
			{
				$this->messages->add_success_message('Категория успешно удалена со всеми подкатегориями!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Категория с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->_add_error_massage('Параметр ID отсутствует! Процес удаления не возможен!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function check_url()
	{
		$this->load->model('catalogue/mcategories');
		//$this->mcategories->keep_check_save_flashdata();
		if($url = $this->input->post('categories'))
		{
			if(isset($url['url']))
			{
				$url = $url['url'];
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mcategories->id_categorie = $id;
				}
				if($this->mcategories->check_isset_url($url))
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