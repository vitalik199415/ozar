<?php
class News extends AG_Controller
{
	const N = 5;
	const BACK = '*/*/*/';
	function __construct()
		{
			parent::__construct(FALSE);
			$this->template->add_title('Тип модуля - Новости');
		}
	
	public function index()
	{
		$this->load->model('news/mnews');
		if(isset($_POST['news_grid_select']))
		{
			if(isset($_POST['news_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['news_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['news_grid_select'])
				{
					case "delete":
						$this->mnews->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mnews->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mnews->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mnews->getCollectionToHtml();
	}
	public function add()
	{
		$this->template->add_title(' Добавление новости');
		$this->template->add_navigation('Добавление новости');
		$this->load->model('news/mnews');
		$this->mnews->add();
	}
	public function edit()
	{
		$this->template->add_title(' Редактирование новости');
		$this->template->add_navigation('Редактирование новости');
		
		$this->load->model('news/mnews');
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mnews->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования новости!');
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
			$this->load->model('news/mnews');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mnews->check_isset_news($ID))
				{
					if($this->mnews->save($ID))
					{
						$this->messages->add_success_message('Новость успешно отредактирована!');
						$this->_redirect(set_url(self::BACK));
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки при редактировании новости!');
						$this->_redirect(set_url(self::BACK));
					}
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Новости не существует!');
					$this->_redirect(set_url(self::BACK));
				}	
			}
			else
			{
				if($ID = $this->mnews->save())
				{
					$this->messages->add_success_message('Новость успешно добавлена!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении новости!');
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
			$this->load->model('news/mnews');
			if($this->mnews->check_isset_news($ID))
			{
				if($this->mnews->delete($ID))
				{
					$this->messages->add_success_message('Новость успешно Удалена!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				$this->messages->add_error_message('Новость с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
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
		$this->load->model('news/mnews_settings');
		$this->mnews_settings->edit();
		$this->template->add_js('iColorPicker');
	}
	
	public function save_settings()
	{
		if(isset($_POST))
		{	
			$this->load->model('mnews_settings');
			if($this->mnews_settings->save())
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
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			
			$this->load->model('news/mnews');
			if($this->mnews->check_isset_news($ID))
			{	
				$this->template->add_title(' - Изображения новости');
				$this->template->add_navigation('Изображения новости c ID = '.$ID);
				
				$this->template->add_css('form');
				$this->template->add_js('tmpl.min', 'javascript-templates');
				$this->template->add_js('load-image.all.min', 'javascript-load-image');
				$this->template->add_js('canvas-to-blob.min', 'canvas-to-blob');

				$this->template->add_js('jquery.iframe-transport', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-process', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-image', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-audio', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-video', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-validate', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-ui', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-jquery-ui', 'jquery-file-upload');

				$this->template->add_js('jquery.gbc_news_img_upload', 'modules_js/news');

				$this->template->add_css('jquery.fileupload', 'jquery-fileupload');
				$this->template->add_css('jquery.fileupload-ui', 'jquery-fileupload');
				$this->template->add_css('jquery-ui', 'jquery-ui/themes/ui-darkness');
				$this->template->add_css('theme', 'jquery-ui/themes/black-tie');
				$this->mnews->edit_photo($ID);
			}
			else
			{
				$this->messages->add_error_message('Новость с ID = '.$ID.' не существует! Действие не возможно!');
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
			$URI = $this->uri->uri_to_assoc(5);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('news/mnews');
				if($this->mnews->check_isset_news($ID))
				{
					$this->mnews->upload_photo($ID);
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
			$this->load->model('news/mnews');
			if($this->mnews->check_isset_news($ID))
			{
				if($this->mnews->save_photo_desc($ID))
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
				$this->messages->add_error_message('Новость с ID = '.$ID.' не существует! Действие не возможно!');
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
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('news/mnews');
			if($this->mnews->check_isset_news($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mnews->change_position_photo($IMG_ID, $URI['position']))
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
				$this->messages->add_error_message('Новость с ID = '.$ID.' не существует! Действие не возможно!');
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
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('news/mnews');
			if($this->mnews->check_isset_news($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mnews->delete_photo($IMG_ID))
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
}
?>