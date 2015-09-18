<?php 
class Langs extends AG_Controller
{
	function __construct()
	{
		parent :: __construct();
		$this->template->add_title('Языки');
		$this->template->add_navigation('Языки', set_url('*'));
	}
	
	public function index()
	{
		$this->load->model('langs/mlangs');
				
		if($select = $this->input->post('langs_grid_select'))
		{
			if($checkbox = $this->input->post('langs_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "on":
						$this->mlangs->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных языков прошла успешно!');
					break;
					case "off":
						$this->mlangs->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных языков прошла успешно!');
					break;
					case "on_site_true":
						$this->mlangs->on_site($data_ID);
						$this->messages->add_success_message('Язык добавлен на сайт!');
					break;
					case "on_site_false":
						$this->mlangs->on_site($data_ID, 0);
						$this->messages->add_success_message('Язык удален из сайта!');
					break;
				}
			}
		}
		
		$this->mlangs->render_langs();
	}
	
	public function add()
	{
		$this->template->add_title('Добавление языка');
		$this->template->add_navigation('Добавление языка');
		$this->load->model('langs/mlangs');
		if(!$this->mlangs->add())
		{
			$this->messages->add_error_message('Все доступные языки уже добавлены!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование языка');
		$this->template->add_navigation('Редактирование');
		
		$this->load->model('langs/mlangs');
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mlangs->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении языка!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url('*/'));
		}
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('langs/mlangs');
			$URI = $this->uri->uri_to_assoc(3);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mlangs->save($ID))
				{
					$this->messages->add_success_message('Язык успешно отредактирован!');
					$this->_redirect(set_url('*'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании языка!');
					$this->_redirect(set_url('*'));
				}
				
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mlangs->save())
				{
					$this->messages->add_success_message('Язык успешно добавлен!');
					$this->_redirect(set_url('*'));
				}
				else
				{
					$this->messages->add_error_message('Возникла ошибка при добавлении нового языка!');
					$this->_redirect(set_url('*'));
				}
			}	
		}
		else
		{
			$this->_redirect(set_url('*'));
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$id = intval($URI['id']);
			$this->load->model('langs/mlangs');
			if($this->mlangs->delete($id))
			{
				$this->messages->add_success_message('Язык успешно удален!');
				$this->_redirect(set_url('*/'));
			}
			else
			{
				$this->messages->add_error_message('Язык с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url('*/'));
		}	
	}
	
	public function change_position()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0 && ($URI['type'] == 'up' || $URI['type'] == 'down'))
		{
			$this->load->model('langs/mlangs');
			if($this->mlangs->change_position($id, $URI['type']))
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


}



?>