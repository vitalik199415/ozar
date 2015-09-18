<?php
class Site_modules extends AG_Controller
{
	const N = 3;
	const BACK = '*/';
	function __construct()
	{
		parent :: __construct();
		$this->template->add_title('Модули сайта');
		$this->template->add_navigation('Модули сайта', set_url('*/'));
	}
		
	public function index()
	{
		$this->load->model('msite_modules');
		if(isset($_POST['site_modules_grid_select']))
		{
			if(isset($_POST['site_modules_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['site_modules_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['site_modules_grid_select'])
				{
					case "delete":
						$this->msite_modules->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->msite_modules->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных модулей прошла успешно!');
					break;
					case "off":
						$this->msite_modules->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных модулей прошла успешно!');
					break;
				}
			}
		}
		$this->msite_modules->getCollectionToHtml();
	}
	
	public function add()
	{
		$this->template->add_title(' - Добавление модуля');
		$this->template->add_navigation('Добавление модуля');
		$this->load->model('msite_modules');
		$this->msite_modules->add();
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование модуля');
		$this->template->add_navigation('Редактирование модуля');		
		$this->load->model('site_modules/msite_modules');
		$URI = $this->uri->uri_to_assoc(self::N);
		
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->msite_modules->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования модуля');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url(self::BACK));
		}
	}
		
	public function save()
	{
		if (isset($_POST))
		{
			$this->load->model('msite_modules');
			$URI = $this->uri->uri_to_assoc(3);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->msite_modules->save($ID))
				{
					$this->messages->add_success_message('Отзыв успешно отредактирован!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании отзыва!');
					$this->_redirect(set_url(self::BACK));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
				}
			}
			else
			{
				if ($ID = $this->msite_modules->save())
				{
					$this->messages->add_success_message('Модуль удачно добавлен!');
					$this->_redirect(set_url('*/'));
					
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url('*/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникла ошибка при добавлении нового модуля!');
					$this->_redirect(set_url('*/'));
				}
			}	
		}
		else
		{
			$this->messages->add_error_message('Данные отсудствуют - операция не возможна!');
			$this->_redirect(set_url('*/'));
		}
	}
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$id = intval($URI['id']);
			$this->load->model('msite_modules');
			if($this->msite_modules->delete($id))
			{
				$this->messages->add_success_message('Модуль успешно удален!');
				$this->_redirect(set_url('*/'));
			}
			else
			{
				$this->messages->add_error_message('Модуль с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url('*/'));
		}	
	}	
}
?>