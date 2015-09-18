<?php
class Reviews extends AG_Controller
{
	const BACK 	= '*/*/*';
	const N 	= 5;
	
	function __construct()
	{
		parent::__construct(FALSE);
	}
		
	public function index()
	{
		$this->template->add_title('Отзывы');
		$this->template-> add_navigation('Отзывы');
		$this->load->model('reviews/mreviews');
		if(isset($_POST['reviews_grid_select']))
		{
			if(isset($_POST['reviews_grid_checkbox']))
			{
				$data_ID = array();
				foreach($_POST['reviews_grid_checkbox'] as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($_POST['reviews_grid_select'])
				{
					case "delete":
						$this->mreviews->delete($data_ID);
						$this->messages->add_success_message('Удаление выбраных отзывов прошло успешно!');
					break;
					case "on":
						$this->mreviews->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных отзывов прошла успешно!');
					break;
					case "off":
						$this->mreviews->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных отзывов прошла успешно!');
					break;
				}
			}
		}
		$this->mreviews->render_reviews_collection();
	}
		
	public function add()
	{
		$this->template->add_title('Добавить отзыв');
		$this->template-> add_navigation('Добавление отзыва');
		$this->load->model('reviews/mreviews');
		$this->mreviews->add();
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('reviews/mreviews');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mreviews->save($ID))
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
					$this->_redirect(set_url(self::BACK.'/edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mreviews->save())
				{
					$this->messages->add_success_message('Отзыв успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'/edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении отзыва!');
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
		$this->template->add_title('Редактирование отзыва');
		$this->template-> add_navigation('Редактирование отзыва');
		
		
		$this->load->model('reviews/mreviews');
		$URI = $this->uri->uri_to_assoc(self::N);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			if(!$this->mreviews->edit($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования отзыва!');
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
			$this->load->model('reviews/mreviews');
			if($this->mreviews->delete($ID))
			{
				$this->messages->add_success_message('Отзыв успешно удален!');
				$this->_redirect(set_url(self::BACK));
			}
			else
			{
				$this->messages->add_error_message('Отзыв с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
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
		$this->template->add_title('Настройки модуля');
		$this->template->add_navigation('Настройки модуля');
		$this->load->model('reviews/mreviews_settings');
		$this->mreviews_settings->edit();
	}

	public function save_settings()
	{
		if(isset($_POST) && count($_POST) > 0)
		{
			$this->load->model('reviews/mreviews_settings');
			if($this->mreviews_settings->save())
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