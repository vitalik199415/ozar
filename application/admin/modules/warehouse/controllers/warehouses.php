<?php
require_once "./additional_libraries/sphinx/SphinxQL.php";
require_once "./additional_libraries/sphinx/Connection.php";

use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;
class Warehouses extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад');
		$this->template->add_navigation('Склад');
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
	}
	
	public function index()
	{
		$this->template->add_title(' | Список складов');
		$this->template->add_navigation('Список складов', set_url('*/*'));
		$this->load->model('warehouse/mwarehouses');
		$this->mwarehouses->render_wh_grid();
	}
	
	public function add_wh()
	{
		$this->template->add_title(' | Список складов | Добавление');
		$this->template->add_navigation('Список складов', set_url('*/*'))->add_navigation('Добавление');
		
		$this->load->model('warehouse/mwarehouses_save');
		$this->mwarehouses_save->add_wh();
	}
	
	public function edit_wh()
	{
		$this->template->add_title(' | Список складов | Редактирование');
		$this->template->add_navigation('Список складов', set_url('*/*'))->add_navigation('Редактирование');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('mwarehouses_save');
			if(!$this->mwarehouses_save->edit_wh($wh_id))
			{
				$this->messages->add_error_message('Запись отсутствует! Редактирование невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function wh_actions()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id'])) > 0)
		{
			$this->template->add_title(' | Список складов');
			$this->template->add_navigation('Список складов', set_url('*/*'));
			
			$this->template->add_js('jquery.print_element.min');
			$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
			$this->template->add_js('jquery.wh_action', 'modules_js/warehouse');
			$this->load->model('warehouse/mwarehouses');
			if(!$this->mwarehouses->view($wh_id))
			{
				$this->messages->add_error_message('Действие невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function save_wh()
	{
		$this->load->model('warehouse/mwarehouses_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']))
		{
			if(($wh_id = intval($URI['wh_id'])) == 0) { $this->messages->add_error_message('Процесс редактирования невозможен!');$this->_redirect(set_url('*/*'));return FALSE; }
			if($this->mwarehouses_save->save_wh($wh_id))
			{
				$this->messages->add_success_message('Процесс редактирования прошел успешно!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->messages->add_error_message('Процесс редактирования невозможен!');
				$this->_redirect(set_url('*/*'));
			}
			if(isset($_GET['return']))
			{
				$this->_redirect(set_url('*/*/edit_wh/wh_id/'.$wh_id));
			}
		}
		else
		{
			if($wh_id = $this->mwarehouses_save->save_wh())
			{
				$this->messages->add_success_message('Процесс добавления прошел успешно!');
				$this->_redirect(set_url('*/*'));
				
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_wh/wh_id/'.$wh_id));
				}
			}
			else
			{
				$this->messages->add_error_message('Процесс добавления невозможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
	}
	
	public function check_wh_alias()
	{
		$this->load->model('warehouse/mwarehouses_save');
		if($alias = $this->input->post('main'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
				{
					$this->mwarehouses_save->id_wh = $wh_id;
				}
				echo json_encode($this->mwarehouses_save->check_isset_wh_alias($alias));
			}	
		}
	}
	
	public function wh_shops()
	{
		$this->template->add_title(' | Точки продаж');
		$this->template->add_navigation('Точки продажи', set_url('*/*/wh_shops'));
		$this->load->model('warehouse/mwarehouses');
		$this->mwarehouses->render_wh_shops_grid();
	}
	
	public function add_wh_shop()
	{
		$this->template->add_title(' | Точки продаж | Добавление');
		$this->template->add_navigation('Точки продажи', set_url('*/*/wh_shops'))->add_navigation('Добавление');
		
		$this->load->model('warehouse/_savemwarehouses');
		if(!$this->mwarehouses_save->add_wh_shop())
		{
			$this->messages->add_error_message('Склады отсутствуют, процесс добавления не возможен!');
			$this->_redirect(set_url('*/*/wh_shops'));
		}
	}
	
	public function edit_wh_shop()
	{
		$this->template->add_title(' | Точки продаж | Редактирование');
		$this->template->add_navigation('Точки продажи', set_url('*/*/wh_shops'))->add_navigation('Редактирование');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_shop_id']) && ($wh_shop_id = intval($URI['wh_shop_id'])) > 0)
		{
			$this->load->model('mwarehouses_save');
			if(!$this->mwarehouses_save->edit_wh_shop($wh_shop_id))
			{
				$this->messages->add_error_message('Процесс редактирование невозможен!');
				$this->_redirect(set_url('*/*/wh_shops'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Процесс редактирование невозможен!');
			$this->_redirect(set_url('*/*/wh_shops'));
		}
	}
	
	public function save_wh_shop()
	{
		$this->load->model('warehouse/mwarehouses_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_shop_id']))
		{
			if(($wh_shop_id = intval($URI['wh_shop_id'])) == 0) { $this->messages->add_error_message('Процесс редактирования невозможен!');$this->_redirect(set_url('*/*'));return FALSE; }
			if($this->mwarehouses_save->save_wh_shop($wh_shop_id))
			{
				$this->messages->add_success_message('Процесс редактирования прошел успешно!');
				$this->_redirect(set_url('*/*/wh_shops'));
			}
			else
			{
				$this->messages->add_error_message('Процесс редактирования невозможен!');
				$this->_redirect(set_url('*/*/wh_shops'));
			}
			if(isset($_GET['return']))
			{
				$this->_redirect(set_url('*/*/edit_wh_shop/wh_shop_id/'.$wh_shop_id));
			}
		}
		else
		{
			if($wh_shop_id = $this->mwarehouses->save_wh_shop())
			{
				$this->messages->add_success_message('Процесс добавления прошел успешно!');
				$this->_redirect(set_url('*/*/wh_shops'));
				
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit_wh_shop/wh_shop_id/'.$wh_shop_id));
				}
			}
			else
			{
				$this->messages->add_error_message('Процесс добавления невозможен!');
				$this->_redirect(set_url('*/*/add_wh_shop'));
			}
		}
	}
	
	public function check_wh_shop_alias()
	{
		$this->load->model('warehouse/mwarehouses_save');
		if($alias = $this->input->post('main'))
		{
			if(isset($alias['alias']))
			{
				$alias = $alias['alias'];
				
				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['wh_shop_id']) && ($wh_shop_id = intval($URI['wh_shop_id'])) > 0)
				{
					$this->mwarehouses_save->id_wh_shop = $wh_shop_id;
				}
				echo json_encode($this->mwarehouses_save->check_isset_wh_shop_alias($alias));
			}	
		}
	}
}
?>