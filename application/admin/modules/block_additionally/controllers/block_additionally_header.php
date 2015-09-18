<?php
class Block_additionally_header extends AG_Controller
{
	const BACK = '*/*/';
	const N = 4;
	protected $block_id = 0;
	
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Дополнительный блок - Header');
		$this->template->add_navigation('Дополнительный блок')->add_navigation('Header', set_url(self::BACK));
	}
	public function index()
	{
		$this->load->model('block_additionally/mblock_additionally');
		if ($select = $this->input->post('block_additionally_grid_select'))
		{
			if ($checkbox = $this->input->post('block_additionally_grid_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				switch($select)
				{
					case "delete":
						$this->mblock_additionally->delete($this->block_id, $data_ID);
						$this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
					break;
					case "on":
						$this->mblock_additionally->activate($data_ID);
						$this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
					break;
					case "off":
						$this->mblock_additionally->activate($data_ID, 0);
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
					break;
				}
			}
		}
		$this->mblock_additionally->get_collection_to_html($this->block_id);
	}
	
	public function add()
	{
		$this->template->add_title('Добавление блока в Header');
		$this->template->add_navigation('Добавление блока в Header');
		$this->load->model('block_additionally/mblock_additionally');
		$this->mblock_additionally->add($this->block_id);
	}
	
	public function edit()
	{
		$this->template->add_title('Редактирование блока в Header');
		$this->template->add_navigation('Редактирование блока в Header');		
		$this->load->model('block_additionally/mblock_additionally');
		$URI = $this->uri->uri_to_assoc(self::N);
		
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if(!$this->mblock_additionally->edit($this->block_id, $ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации редактирования блока в Header');
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
			$this->load->model('block_additionally/mblock_additionally');
			$URI = $this->uri->uri_to_assoc(self::N);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$ID = intval($URI['id']);
				if($this->mblock_additionally->save($this->block_id, $ID))
				{
					$this->messages->add_success_message('Oбъект блока Header успешно отредактирован!');
					$this->_redirect(set_url(self::BACK));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании объекта блока Header!');
					$this->_redirect(set_url(self::BACK));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mblock_additionally->save($this->block_id))
				{
					$this->messages->add_success_message('Oбъект блока Header успешно добавлен!');
					$this->_redirect(set_url(self::BACK));
					if (isset($_GET['return']))
					{
						$this->_redirect(set_url(self::BACK.'edit/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении объекта Header!');
					$this->_redirect(set_url(self::BACK));
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
			$id = intval($URI['id']);
			$this->load->model('mblock_additionally');
			if($this->mblock_additionally->delete($this->block_id, $id))
			{
				$this->messages->add_success_message('Oбъект блока Header успешно удален!');
				$this->_redirect(set_url(self::BACK));
			}
			else
			{
				$this->messages->add_error_message('Oбъект блока Header с ID = '.$id.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url(self::BACK));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url(self::BACK));
		}	
	}
}
?>