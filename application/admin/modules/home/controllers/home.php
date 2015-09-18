<?php
class Home extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Главная страница');
		$this->template->add_navigation('Главная страница');
	}
	function index()
	{
		$this->load->model('mhome');
		$this->mhome->edit();
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('mhome');
			if($this->mhome->save())
			{
				$this->messages->add_success_message('Модули успешно сохранены!');
				$this->_redirect(set_url('*/'));
			}
			else
			{
				$this->messages->add_error_message('Не выбраны модули для добавления!');
				$this->_redirect(set_url('*/'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/'));
		}	
	}
	public function change_position_module() 
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id_module']) && intval($URI['id_module'])>0)
		{
			$id_module = intval($URI['id_module']);
			$type = $URI['change'];
			$this->load->model('mhome');
			
				$this->mhome->change_position_module($id_module, $type);
				$this->messages->add_success_message('Позиция успешно изменена!');
				$this->_redirect(set_url('*/'));
		}
		else
		{
			$this->messages->_addErrorMassage('Параметр ID отсутствует! Процес изменения позици не возможно');
			$this->_redirect(set_url('*/'));
		}
	}
	public function delete_menu_modul()
	{
		$URI = $this->uri->uri_to_assoc(3);
		if(isset($URI['id_module']) && intval($URI['id_module'])>0)
		{
			$id_module = intval($URI['id_module']);
			$this->load->model('mhome');
			
				$this->mhome->delete_menu_modul($id_module);
				$this->messages->add_success_message('Модуль успешно удален!');
				$this->_redirect(set_url('*/'));
		}
		else
		{
			$this->messages->_addErrorMassage('Параметр ID отсутствует! Процес удаления модуля не возможен!');
			$this->_redirect(set_url('*/'));
		}
	}
}
?>