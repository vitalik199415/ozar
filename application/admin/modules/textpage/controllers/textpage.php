<?php
class Textpage extends AG_Controller
{
	const N = 5;
	const BACK = '*/*/*/';
	function __construct()
		{
			parent::__construct(FALSE);
			$this->template->add_title('Модуль текстовой страницы');
		}
		
	public function index()
	{
		$this->load->model('textpage/mtextpage');
		
		if(isset($_POST['textpage_grid_select']))
		{
			if(isset($_POST['textpage_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['textpage_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['textpage_grid_select'])
				{
					case "delete":
						$this->mtextpage->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mtextpage->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mtextpage->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mtextpage->getCollectionToHtml();
	}
	
	public function add()
	{
		$this->template->add_title(' - Добавление текстового блока');
		$this->template->add_navigation('Добавление текстового блока');
		
		$this->load->model('textpage/mtextpage');
		$this->mtextpage->add();
		
	}
	
	public function edit()
	{
		$this->template->add_title(' - Редактирование текстового блока');
		$this->template->add_navigation('Редактирование текстового блока');
		
		
		$this->load->model('textpage/mtextpage');
		
		$URI = $this->uri->uri_to_assoc(self::N);
		
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mtextpage->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования текстового блока');
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
		if(isset($_POST))
		{
			$this->load->model('textpage/mtextpage');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mtextpage->save($ID))
				{
					$this->messages->add_success_message('Текстовый блок успешно отредактирован!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании текстового блока!');
					$this->_redirect(set_url(self::BACK));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mtextpage->save())
				{
					$this->messages->add_success_message('Текстовый блок успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении текстового блока!');
					$this->_redirect(set_url(self::BACK));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
				}
			}
		}
		else
		{
			$this->_redirect(set_url(self::BACK));
		}
	}
	
	public function delete()
	{
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('textpage/mtextpage');
			if($this->mtextpage->delete($ID))
			{
				$this->messages->add_success_message('Текстового блока успешно удален!');
				$this->_redirect(set_url(self::BACK));
			}
			else
			{
				$this->messages->add_error_message('Текстовый блок с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
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