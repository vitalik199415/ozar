<?php
class Products_types_set extends AG_Controller
{
	function __construct()
	{
		parent:: __construct();
		$this->template->add_title('Каталог продукции | ')->add_title('SEO Наборы фильтров | ');
		$this->template->add_navigation('Каталог продукции')->add_navigation('SEO Наборы фильтров', set_url('*/products_types_set'));
	}

	public function index()
	{
		$this->load->model('catalogue/mproducts_types_set');
		$this->mproducts_types_set->render_types_set_grid();
	}

	public function select_category()
	{
		$this->template->add_title('Добавление набора свойств');
		$this->template->add_navigation('Добавление набора свойств | Выбор категории');

		$this->load->model('catalogue/mproducts_types_set');
		$this->mproducts_types_set->select_category();
	}

	public function add()
	{
		$this->load->model('catalogue/mproducts_types_set');
		$this->load->model('catalogue/mcategories_products');

		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			if(!$this->mproducts_types_set->add($cat_id))
			{
				$this->messages->add_error_message('Возникли ошибки! Добавление невозможно!');
				$this->_redirect(set_url('*/*/select_category'));
			}
		}
		else
		{
			$this->messages->add_error_message('Отсутствует ID категории! Добавление невозможно!');
			$this->_redirect(set_url('*/*/select_category'));
		}
	}

	public function edit()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0 && isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0 )
		{
			$this->load->model('catalogue/mproducts_types_set');
			if(!$this->mproducts_types_set->edit($cat_id, $id))
			{
				$this->messages->add_error_message('Возникли ошибки! Редактирование невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Возникли ошибки!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function save()
	{
		$this->load->model('catalogue/mproducts_types_set');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0 )
		{
			if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				if($this->mproducts_types_set->save($cat_id, $ID))
				{
					$this->messages->add_success_message('Набор успешно отредактирован!');
					$this->_redirect(set_url('*/*'));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании Набора! Повторите попытку.');
					$this->_redirect(set_url('*/*/edit/cat_id/'.$cat_id.'/id/'.$ID));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/cat_id/'.$cat_id.'/id/'.$ID));
				}
			}
			else
			{
				if($ID = $this->mproducts_types_set->save($cat_id))
				{
					$this->messages->add_success_message('Набор успешно добавлен!');
					$this->_redirect(set_url('*/*'));

					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/edit/cat_id/'.$cat_id.'/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при добавлении набора!');
					$this->_redirect(set_url('*/*/add/cat_id/'.$cat_id));
				}
			}
		}
	}

	public function delete()
	{
		$this->load->model('catalogue/mproducts_types_set');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->mproducts_types_set->delete($ID);
			$this->messages->add_success_message('Запись успешно удалена!');
			$this->_redirect(set_url('*/*'));

		}
		else
		{
			$this->messages->add_error_message('Отсутствует параметр ID - удаление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

} 