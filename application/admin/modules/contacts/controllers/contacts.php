<?php
class Contacts extends AG_Controller
{
	const BACK 	= '*/*/*/';
	const N 	= 5;
	
	function __construct()
		{
			parent::__construct(FALSE);
			$this->template->add_title('Модуль Контакты');
			//$this->template->add_navigation('Контакты', set_url(self::BACK));
		}
		
	public function index()
	{
		$this->load->model('contacts/mcontacts');
		if(isset($_POST['contacts_grid_select']))
			{
				if(isset($_POST['contacts_grid_checkbox']))
				{
					$data_ID = array();
					foreach($_POST['contacts_grid_checkbox'] as $ms)
					{
						$data_ID[] = $ms;
					}
					switch($_POST['contacts_grid_select'])
					{
						case "delete":
							$this->mcontacts->delete($data_ID);
							$this->messages->add_success_message('Удаление выбраных контактов прошло успешно!');
						break;
						case "on":
							$this->mcontacts->set_active($data_ID);
							$this->messages->add_success_message('Активация выбраных контактов прошла успешно!');
						break;
						case "off":
							$this->mcontacts->set_active($data_ID, 0);
							$this->messages->add_success_message('Деактивация выбраных контактов прошла успешно!');
						break;
					}
				}
			}
			$this->mcontacts->get_collection();
		}
		
	public function add()
	{
		$this->template->add_title(' - Добавить контакт');
		$this->template->add_navigation('Добавление контакта');
		$this->load->model('contacts/mcontacts');
		$this->mcontacts->add();
	}
	
	public function save()
	{
		
		if(isset($_POST))
		{
			$this->load->model('contacts/mcontacts');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mcontacts->save($ID))
				{
					$this->messages->add_success_message('Контакт успешно отредактирован!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании контакта!');
					$this->_redirect(set_url(self::BACK));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mcontacts->save())
				{
					$this->messages->add_success_message('Контакт успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));	
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении контакта!');
					$this->_redirect(set_url(self::BACK));
				}
			}
		}
		else
		{
			$this->_redirect(set_url(self::BACK));
		}
	}
	
	public function edit()
	{
		$this->template->add_title(' - Редактирование контакта');
		$this->template-> add_navigation('Редактирование контакта');
		
		
		$this->load->model('contacts/mcontacts');
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mcontacts->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования контакта!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url(self::BACK));
		}
	}
	
	public function delete()
	{
		
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('contacts/mcontacts');
			if($this->mcontacts->delete($ID))
			{
				$this->messages->add_success_message('Контакт успешно удален!');
				$this->_redirect(set_url(self::BACK));
			}
			else
			{
				$this->messages->add_error_message('Контакт с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url(self::BACK));
		}	
	}
	
	function settings()
	{
		$this->_redirect(set_url(self::BACK));
	}
}
?>