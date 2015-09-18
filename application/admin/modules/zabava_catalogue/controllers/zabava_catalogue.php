<?php
class Zabava_catalogue extends AG_Controller
{
	const N = 5;
	const BACK = '*/*/*/';
	function __construct()
	{
		parent::__construct(FALSE);
		$this->template->add_title('Тип модуля - Каталог Забава');
	}
	
	public function index()
	{
		$this->load->model('zabava_catalogue/mzabava_catalogue');
		if(isset($_POST['zabava_catalogue_grid_select']))
		{
			if(isset($_POST['zabava_catalogue_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['zabava_catalogue_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['zabava_catalogue_grid_select'])
				{
					case "delete":
						$this->mzabava_catalogue->delete($data_ID);
						$this->massages->add_success_massage('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mzabava_catalogue->activate($data_ID);
						$this->massages->add_success_massage('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mzabava_catalogue->activate($data_ID, 0);
						$this->massages->add_success_massage('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mzabava_catalogue->get_collection_to_html();
	}
	
	public function change_position()
	{
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && ($id = intval($URI['id'])) > 0 && ($URI['type'] == 'up' || $URI['type'] == 'down'))
		{
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($id))
			{
				if($this->mzabava_catalogue->change_position($id, $URI['type']))
				{
					$this->massages->add_success_massage('Смена позиции прошла успешно!');
					$this->index();
				}
				else
				{
					$this->massages->add_error_massage('Смена позиции не возможна!');
					$this->index();
				}
			}
			else
			{
				$this->massages->add_error_massage('Данной позиции не существует. Смена позиции не возможна!');
				$this->index();
			}
		}
		else
		{
			$this->index();
		}
	}
	
	public function add()
	{
		$this->template->add_title(' Добавление позиции');
		$this->template->add_navigation('Добавление позиции');
		$this->load->model('zabava_catalogue/mzabava_catalogue');
		$this->mzabava_catalogue->add();
	}
	
	public function edit()
	{
		$this->template->add_title(' Редактирование позиции');
		$this->template->add_navigation('Редактирование позиции');
		
		$this->load->model('zabava_catalogue/mzabava_catalogue');
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mzabava_catalogue->edit($ID))
			{
				$this->massages->add_error_massage('Возникли ошибки генерации редактирования позиции!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url(self::BACK));
		}
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mzabava_catalogue->check_isset($ID))
				{
					if($this->mzabava_catalogue->save($ID))
					{
						$this->massages->add_success_massage('Позиция успешно отредактирована!');
						$this->_redirect(set_url(self::BACK));
					}
					else
					{
						$this->massages->add_error_massage('Возникли ошибки при редактировании позиции!');
						$this->_redirect(set_url(self::BACK));
					}
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
					}
				}
				else
				{
					$this->massages->add_error_massage('Позиции не существует!');
					$this->_redirect(set_url(self::BACK));
				}	
			}
			else
			{
				if($ID = $this->mzabava_catalogue->save())
				{
					$this->massages->add_success_massage('Позиция успешно добавлена!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->massages->add_error_massage('Возникли ошибки при добавлении позиции!');
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
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($ID))
			{
				if($this->mzabava_catalogue->delete($ID))
				{
					$this->massages->add_success_massage('Позиция успешно Удалена!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				$this->massages->add_error_massage('Позиция с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url(self::BACK));
		}	
	}
	
	public function photo()
	{	
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($ID))
			{	
				$this->template->add_title(' - Изображения позиции');
				$this->template->add_navigation('Изображения позиции c ID = '.$ID);
				
				$this->template->add_css('form');
				$this->template->add_css('swfupload_prod','swfupload');
				$this->template->add_js('swfupload','swfupload');
				$this->template->add_js('swfupload.queue','swfupload');
				$this->template->add_js('fileprogress','swfupload');
				$this->template->add_js('handlers','swfupload');
				$this->mzabava_catalogue->edit_photo($ID);
			}
			else
			{
				$this->massages->add_error_massage('Позиция с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
	
	public function photo_save()
	{
		if (isset($_FILES["Filedata"]) && is_uploaded_file($_FILES["Filedata"]["tmp_name"]) && $_FILES["Filedata"]["error"] == 0)
		{
			$URI = $this->uri->uri_to_assoc(5);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('zabava_catalogue/mzabava_catalogue');
				if($this->mzabava_catalogue->check_isset($ID))
				{
					$this->mzabava_catalogue->upload_photo($ID);
				}
				else
				{
					header("HTTP/1.1 500 File Upload Error");
					echo 'Invalid parameters!';
				}
			}
			else
			{
				header("HTTP/1.1 501 File Upload Error");
				echo 'Invalid parameters!';
			}
		}
		else
		{
			header("HTTP/1.1 502 File Upload Error");
			if (isset($_FILES["Filedata"]))
			{
				echo $_FILES["Filedata"]["error"];
			}
		}
	}
	/*---------------------------------------------------------*/
	public function save_photo_desc()
	{
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($ID))
			{
				if($this->mzabava_catalogue->save_photo_desc($ID))
				{
					$this->massages->add_success_massage('Описания фотографий успешно отредактированы!');
					$this->_redirect(set_url('*/*/*/'));
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->massages->add_error_massage('Возникли ошибки при редактировании описаний фотографий!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->massages->add_error_massage('Новость с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}		
	}

	public function change_position_photo()
	{
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mzabava_catalogue->change_position_photo($IMG_ID, $URI['position']))
					{
						$this->massages->add_success_massage('Смена позиции изображения прошла успешно!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->massages->add_error_massage('Смена позиции для изображения с IMG_ID = '.$IMG_ID.' не возможна!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->massages->add_error_massage('Параметр ID_IMG или position отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->massages->add_error_massage('Новость с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
	
	public function delete_photo()
	{
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('zabava_catalogue/mzabava_catalogue');
			if($this->mzabava_catalogue->check_isset($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mzabava_catalogue->delete_photo($IMG_ID))
					{
						$this->massages->add_success_massage('Изображение удалено!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->massages->add_error_massage('Изображение с IMG_ID = '.$IMG_ID.' не существует! Действие не возможно!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->massages->add_error_massage('Параметр ID_IMG отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->massages->add_error_massage('Альбом с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
}
?>