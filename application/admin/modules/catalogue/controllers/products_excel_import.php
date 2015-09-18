<?php
class Products_excel_import extends AG_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->template->add_title('Импорт');
		$this->template->add_navigation('Продукты каталога', set_url('catalogue/products'));
		$this->template->add_navigation('Импорт');
		$this->load->model('catalogue/mproducts_excel_import');
		$this->mproducts_excel_import->upload_file();
	}

	public function upload_xls()
	{
		$this->load->model('catalogue/mproducts_excel_import');
		if(!$this->mproducts_excel_import->upload_xls())
		{
			$this->massages->add_error_massage('Файл не загружен');
			$this->_redirect(set_url('*/*'));
		}
		else
		{
			$this->massages->add_success_massage('Файл успешно загружен');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function import()
	{
		$this->load->model('catalogue/mproducts_excel_import');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['file']) && strlen($URI['file'])>0)
		{
			$file = $URI['file'];
			if($this->mproducts_excel_import->import($file))
			{
				$this->massages->add_success_massage("Товары успешно обновлены!");
				$this->_redirect(set_url('catalogue/products'));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки!');
				$this->_redirect(set_url('catalogue/products'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Отсутствует имя файла! Процес обработки невозможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	public function other_import()
	{
		$this->load->model('catalogue/mproducts_excel_import');

			if($this->mproducts_excel_import->other_import())
			{
				$this->massages->add_success_massage("Товары успешно обновлены!");
				$this->_redirect(set_url('catalogue/products'));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки!');
				$this->_redirect(set_url('catalogue/products'));
			}
	}

	public function delete_file()
	{
		$this->load->model('catalogue/mproducts_excel_import');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['file']) && strlen($URI['file'])>0)
		{
			$file = $URI['file'];
			if($this->mproducts_excel_import->delete_file($file))
			{
				$this->massages->add_success_massage('Файл успешно удален!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->massages->add_error_massage('Файл '.$file.' не существует, или произошла ошибка при удалении!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Отсутствует имя файла! Удаление невозможен!');
			$this->_redirect(set_url('*/*'));
		}
	}



} 