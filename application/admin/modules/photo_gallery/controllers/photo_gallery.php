<?php 
class Photo_gallery extends AG_Controller
{
	const BACK 	= '*/*/*/';
	const N 	= 5;
	
	function __construct()
		{
			parent::__construct(FALSE);
		}
		
	public function index()
	{
		$this->load->model('photo_gallery/mphoto_gallery');
		if(isset($_POST['photo_gallery_grid_select']))
			{
				if(isset($_POST['photo_gallery_grid_checkbox']))
				{
					$data_ID = array();
					foreach($_POST['photo_gallery_grid_checkbox'] as $ms)
					{
						$data_ID[] = $ms;
					}
					switch($_POST['photo_gallery_grid_select'])
					{
						case "delete":
							$this->mphoto_gallery->delete($data_ID);
							$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
						break;
						case "on":
							$this->mphoto_gallery->activate($data_ID);
							$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
						break;
						case "off":
							$this->mphoto_gallery->activate($data_ID, 0);
							$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
						break;
					}
				}
			}
			$this->mphoto_gallery->render_albums();
		}
	public function add()
	{
		$this->template->add_title('Добавить фото-альбом');
		$this->template->add_navigation('Добавление фото-альбома');
		$this->load->model('photo_gallery/mphoto_gallery');
		$this->mphoto_gallery->add();
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('photo_gallery/mphoto_gallery');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mphoto_gallery->check_isset_album($ID))
				{
					if($this->mphoto_gallery->save($ID))
					{
						$this->messages->add_success_message('Фото-альбом успешно отредактирован!');
						$this->_redirect(set_url(self::BACK));
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки при редактировании фото-альбома!');
						$this->_redirect(set_url(self::BACK));
					}
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Альбома не существует!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				if($ID = $this->mphoto_gallery->save())
				{
					$this->messages->add_success_message('Фото-альбом успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));	
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении фото-альбома!');
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
		$this->template->add_title('Редактирование фото-альбома');
		$this->template-> add_navigation('Редактирование фото-альбома');
		
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{
				if(!$this->mphoto_gallery->edit($ID))
				{
					$this->messages->add_error_message('Возникли ошибки генерации редактирования фото-альбома!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				$this->messages->add_error_message('Альбом с указанным ID отсутсвует! Процесс редактирования не возможен!');
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
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{
				if($this->mphoto_gallery->delete($ID))
				{
					$this->messages->add_success_message('Фото-альбом успешно удален!');
					$this->_redirect(set_url(self::BACK));
				}
			}
			else
			{
				$this->messages->add_error_message('Фото-альбом с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url(self::BACK));
		}	
	}
	
	public function photo()
	{	
		$URI = $this->uri->uri_to_assoc(5);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{			
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{	
				$this->template->add_title(' - Фотографии альбома');
				$this->template->add_navigation('Фотографии альбома c ID = '.$ID);
				
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

				$this->template->add_js('jquery.gbc_photo_gallery_img_upload', 'modules_js/photo_gallery');

				$this->template->add_css('jquery.fileupload', 'jquery-fileupload');
				$this->template->add_css('jquery.fileupload-ui', 'jquery-fileupload');
				$this->template->add_css('jquery-ui', 'jquery-ui/themes/ui-darkness');
				$this->template->add_css('theme', 'jquery-ui/themes/black-tie');
				$this->mphoto_gallery->edit_photo($ID);
			}
			else
			{
				$this->messages->add_error_message('Альбомa с ID = '.$ID.' не существует! Действие не возможно!');
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
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				$this->load->model('photo_gallery/mphoto_gallery');
				if($this->mphoto_gallery->check_isset_album($ID))
				{
					$this->mphoto_gallery->upload_photo($ID);
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
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{
				if($this->mphoto_gallery->save_photo_desc($ID))
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
				$this->messages->add_error_message('Фото-альбом с ID = '.$ID.' не существует! Действие не возможно!');
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
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mphoto_gallery->change_position_photo($IMG_ID, $URI['position']))
					{
						$this->messages->add_success_message('Смена позиции фотографии прошла успешно!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Смена позиции для фотографии с IMG_ID = '.$IMG_ID.' не возможна!');
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
				$this->messages->add_error_message('Фото-альбом с ID = '.$ID.' не существует! Действие не возможно!');
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
			$this->load->model('photo_gallery/mphoto_gallery');
			if($this->mphoto_gallery->check_isset_album($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mphoto_gallery->delete_photo($IMG_ID))
					{
						$this->messages->add_success_message('Фотография удалена!');
						$this->_redirect(set_url('*/*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Фотография с IMG_ID = '.$IMG_ID.' не существует! Действие не возможно!');
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
	
	function settings()
	{
		$this->template->add_navigation('Настройки модуля');
		$this->load->model('photo_gallery/mphoto_gallery_settings');
		$this->mphoto_gallery_settings->edit();
		$this->template->add_js('iColorPicker');
	}
	
	public function save_settings()
	{
		if(isset($_POST))
		{	
			$this->load->model('mphoto_gallery_settings');
			if($this->mphoto_gallery_settings->save())
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

}
?>