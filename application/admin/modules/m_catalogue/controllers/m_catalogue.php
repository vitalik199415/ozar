<?php
class M_catalogue extends AG_Controller
{
	const N = 5;
	const BACK = '*/*/*/';
	function __construct()
		{
			parent::__construct(FALSE);
			$this->template->add_title('Тип модуля - Упрощенный каталог');
		}
	
	public function index()
	{
		$this->load->model('m_catalogue/mm_catalogue');
		if(isset($_POST['m_catalogue_grid_select']))
		{
			if(isset($_POST['m_catalogue_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['m_catalogue_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['m_catalogue_grid_select'])
				{
					case "delete":
						$this->mm_catalogue->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mm_catalogue->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mm_catalogue->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mm_catalogue->getCollectionToHtml();
	}
	public function add()
	{
		$this->template->add_title(' Добавление');
		$this->template->add_navigation('Добавление');
		$this->load->model('m_catalogue/mm_catalogue');
		$this->mm_catalogue->add();
	}
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('m_catalogue/mm_catalogue');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mm_catalogue->check_isset_m_catalogue($ID))
				{
					if($this->mm_catalogue->save($ID))
					{
						$this->messages->add_success_message('Объект успешно отредактирован!');
						$this->_redirect(set_url(self::BACK));
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки при редактировании!');
						$this->_redirect(set_url(self::BACK));
					}
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Объект не существует!');
					$this->_redirect(set_url(self::BACK));
				}	
			}
			else
			{
				if($ID = $this->mm_catalogue->save())
				{
					$this->messages->add_success_message('Объект успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении объекта!');
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
	public function edit()
	{
		$this->template->add_title(' Редактирование объекта');
		$this->template->add_navigation('Редактирование объекта');
		
		$this->load->model('m_catalogue/mm_catalogue');
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mm_catalogue->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования объекта!');
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
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('m_catalogue/mm_catalogue');
			if($this->mm_catalogue->check_isset_m_catalogue($ID))
			{
				if($this->mm_catalogue->delete($ID))
				{
					$this->messages->add_success_message('Объект успешно удален!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				$this->messages->add_error_message('Объект с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
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
		$this->template->add_navigation('Настройки модуля');
		$this->load->model('m_catalogue/mm_catalogue_settings');
		$this->mm_catalogue_settings->edit();
		$this->template->add_js('iColorPicker');
	}
	public function save_settings()
	{
		if(isset($_POST))
		{	
			$this->load->model('mm_catalogue_settings');
			if($this->mm_catalogue_settings->save())
			{
				$this->messages->add_success_message('Настройки успешно сохранены!');
				$this->_redirect(set_url('*/*/*/settings'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при сохранении настроек!');
				$this->_redirect(set_url('*/*/*/settings'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/*/settings'));
			
		}
	}
	public function photo()
	{	
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			
			$this->load->model('m_catalogue/mm_catalogue');
			if($this->mm_catalogue->check_isset_m_catalogue($ID))
			{	
				$this->template->add_title(' - Изображения объекта');
				$this->template->add_navigation('Изображения объекта c ID = '.$ID);
				
				$this->template->add_css('form');
				$this->template->add_css('swfupload_prod','swfupload');
				$this->template->add_js('swfupload','swfupload');
				$this->template->add_js('swfupload.queue','swfupload');
				$this->template->add_js('fileprogress','swfupload');
				$this->template->add_js('handlers','swfupload');
				$this->mm_catalogue->edit_photo($ID);
			}
			else
			{
				$this->messages->add_error_message('Объект с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
	
	public function photo_save()
	{
		if (isset($_FILES["Filedata"]) && is_uploaded_file($_FILES["Filedata"]["tmp_name"]) && $_FILES["Filedata"]["error"] == 0)
		{
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('m_catalogue/mm_catalogue');
				if($this->mm_catalogue->check_isset_m_catalogue($ID))
				{
					$this->mm_catalogue->upload_photo($ID);
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
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('m_catalogue/mm_catalogue');
			if($this->mm_catalogue->check_isset_m_catalogue($ID))
			{
				if($this->mm_catalogue->save_photo_desc($ID))
				{
					$this->messages->add_success_message('Описания фотографий успешно отредактированы!');
					$this->_redirect(set_url('*/*/*/'));
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании описаний фотографий!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Объект с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}		
	}

	public function change_position_photo()
	{
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('m_catalogue/mm_catalogue');
			if($this->mm_catalogue->check_isset_m_catalogue($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mm_catalogue->change_position_photo($IMG_ID, $URI['position']))
					{
						$this->messages->add_success_message('Смена позиции изображения прошла успешно!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Смена позиции для изображения с IMG_ID = '.$IMG_ID.' не возможна!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG или position отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Объект с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
	
	public function delete_photo()
	{
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('m_catalogue/mm_catalogue');
			if($this->mm_catalogue->check_isset_m_catalogue($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mm_catalogue->delete_photo($IMG_ID))
					{
						$this->messages->add_success_message('Изображение удалено!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Изображение с IMG_ID = '.$IMG_ID.' не существует! Действие не возможно!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Альбом с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/*/'));
		}
	}
	public function check_url()
	{
		$this->load->model('m_catalogue/mm_catalogue');
		if($url = $this->input->post('main'))
		{
			$url = $url['url'];
			$URI = $this->uri->uri_to_assoc(self::N);
			
			if(isset($URI['id']) && ($id = intval($URI['id']))>0)
			{
				$this->mm_catalogue->id_categorie = $id;
			}
			if($this->mm_catalogue->check_isset_url($url))
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
?>
