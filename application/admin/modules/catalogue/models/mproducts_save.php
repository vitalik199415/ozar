<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;

class Mproducts_save extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	const WH_SH 		= 'wh_shops';
	const ID_WH_SH 		= 'id_wh_shops';
	const WHNSH			= 'wh_whNshops';

	const WH_PR = 'wh_products';

	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';

	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';
	const ID_PR_PRICE_DESC 	= 'id_m_c_products_price_description';

	const NTYPES 		= 'm_c_productsNtypes';
	const NATTRIBUTES 	= 'm_c_productsNattributes';

	const ID_PR_TYPES 		= 'id_m_c_products_types';
	const ID_PR_PROPERTIES 	= 'id_m_c_products_properties';

	const PR_ATTRIBUTES 			= 'm_c_products_attributes';
	const ID_PR_ATTRIBUTES 			= 'id_m_c_products_attributes';
	const PR_ATTRIBUTES_DESC		= 'm_c_products_attributes_description';
	const PR_ATTRIBUTES_OPTIONS 	= 'm_c_products_attributes_options';
	const ID_PR_ATTRIBUTES_OPTIONS 	= 'id_m_c_products_attributes_options';
	const PR_ATTRIBUTES_OPTIONS_DESC 	= 'm_c_products_attributes_options_description';


	const PR_ALB 			= 'm_c_products_albums';
	const ID_PR_ALB 		= 'id_m_c_products_albums';
	const PR_ALB_DESC 		= 'm_c_products_albums_description';
	const ID_PR_ALB_DESC 	= 'id_m_c_products_albums_description';

	const PR_IMG 			= 'm_c_products_images';
	const ID_PR_IMG 		= 'id_m_c_products_images';
	const PR_IMG_DESC 		= 'm_c_products_images_description';
	const ID_PR_IMG_DESC 	= 'id_m_c_products_images_description';

	const CAT = 'm_c_categories';
	const ID_CAT = 'id_m_c_categories';

	const PR_CAT = 'm_c_productsNcategories';
	const ID_PR_CAT = 'id_m_c_productsNcategories';

	const CUR = 'm_c_currency';
	const ID_CUR = 'id_m_c_currency';
	const UCUR = 'm_c_users_currency';

	const PR_REL		= 'm_c_products_related';
	const ID_PR_REL		= 'id_m_c_products_related';

	const PR_SIM		= 'm_c_products_similar';
	const ID_PR_SIM		= 'id_m_c_products_similar';

	const M_U_WAIT		= 'm_u_customers_waitlist';

	const SPHINX_INDEX = SPHINX_INDEX;

	const IMG_FOLDER = '/media/catalogue/products/';
	private $img_path = FALSE;

	public $id_pr = FALSE;

	const PR_ADDEDIT_FORM_ID = 'products_add_edit_from';

	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}

	public function set_pr_id($id)
	{
		$this->id_pr = $id;
		return $this;
	}

	public function add_pr()
	{
		$data = $this->add_edit_base();
		$data['data_not_related_pr'] = $this->get_not_related_pr();
		$data['data_not_similar_pr'] = $this->get_not_similar_pr();
		$this->load->helper('catalogue/products_save_helper');

		/*$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		$data['wh_on'] = FALSE;
		if($wh_settings['wh_on'] == 1)
		{
			$this->load->model('warehouse/mwarehouses_products');
			$data['wh_on'] = TRUE;
			$data['data_warehouses'] = $this->mwarehouses_products->get_wh_pr_qty();
			$data['warehouse'] = array();
			foreach($data['data_warehouses'] as $key => $ms)
			{
				$data['warehouse'][$key]['qty'] = $ms['qty'];
			}
		}*/

		helper_products_form_build($data);
	}

	public function edit_pr($id)
	{
		if(!$this->check_isset_pr($id)) return FALSE;

		$data = $this->add_edit_base();
		$data += $this->get_edit_data($id, $data);

		/*$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		$data['wh_on'] = FALSE;
		if($wh_settings['wh_on'] == 1)
		{
			$this->load->model('warehouse/mwarehouses_products');
			$data['wh_on'] = TRUE;
			$data['data_warehouses'] = $this->mwarehouses_products->get_wh_pr_qty($id);
			$data['warehouse'] = array();
			foreach($data['data_warehouses'] as $key => $ms)
			{
				$data['warehouse'][$key]['qty'] = $ms['qty'];
			}
		}*/

		$this->load->helper('catalogue/products_save_helper');
		helper_products_form_build($data, '/id/'.$id);
		return TRUE;
	}

	public function clone_pr($id)
	{
		if(!$this->check_isset_pr($id)) return FALSE;

		$data = $this->add_edit_base();
		$data += $this->get_edit_data($id, $data);

		/*$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_wh_settings();
		$data['wh_on'] = FALSE;
		if($wh_settings['wh_on'] == 1)
		{
			$this->load->model('warehouse/mwarehouses_products');
			$data['wh_on'] = TRUE;
			$data['data_warehouses'] = $this->mwarehouses_products->get_wh_pr_qty();
			$data['warehouse'] = array();
			foreach($data['data_warehouses'] as $key => $ms)
			{
				$data['warehouse'][$key]['qty'] = $ms['qty'];
			}
		}*/

		$this->load->helper('catalogue/products_save_helper');

		unset($data['data_not_related_pr']);
		unset($data['data_related_pr']);

		unset($data['data_not_similar_pr']);
		unset($data['data_similar_pr']);

		unset($data['product']['sku']);
		unset($data['product']['url_key']);

		$data['data_not_related_pr'] = $this->get_not_related_pr();
		$data['data_not_similar_pr'] = $this->get_not_similar_pr();
		helper_products_form_build($data);
		return TRUE;
	}

	protected function get_edit_data($id, $data = array())
	{
		$this->db->select("A.*")
				->from("`".self::PR."` AS A")
				->where("A.`".self::ID_PR."`", $id)
				->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $this->db->get()->row_array();
		$data['product'] = $result;

		$this->db->select("A.*")
				->from(	"`".self::PR_DESC."` AS A")
				->where("A.`".self::ID_PR."`", $id)->limit(count($data['on_langs']));
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_desc'][$ms[self::ID_LANGS]] = array(
				'name' 							=> $ms['name'],
				'short_description' 			=> $ms['short_description'],
				'full_description' 				=> $ms['full_description'],
				'seo_title' 					=> $ms['seo_title'],
				'seo_description' 				=> $ms['seo_description'],
				'seo_keywords' 					=> $ms['seo_keywords'],
				'seo_sky' 						=> $ms['seo_sky']
			);
		}

		$this->db->select("A.*, A.`".self::ID_PR_PRICE."` AS ID, B.*")
				->from("`".self::PR_PRICE."` AS A")
				->join(	"`".self::PR_PRICE_DESC."` AS B",
						"B.`".self::ID_PR_PRICE."` = A.`".self::ID_PR_PRICE."`",
						"left")
				->where("A.`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_prices_blocks'][$ms['ID']] = $ms['alias'];
			if($ms['m_u_types'] !== NULL)
			{
				$m_u_types = explode(',', $ms['m_u_types']);
				if(is_array($m_u_types))
				{
					$ms['m_u_types'] = array();
					foreach($m_u_types as $m_u_t)
					{
						$ms['m_u_types'][$m_u_t] = $m_u_t;
					}
				}
			}
			if($ms['show_attributes'] == 2)
			{
				$id_attr = explode(',', $ms['id_attributes']);
				if(is_array($id_attr))
				{
					$ms['id_attributes'] = array();
					foreach($id_attr as $id_at)
					{
						$ms['id_attributes'][$id_at] = $id_at;
					}
				}
			}
			if(!isset($data['product_prices'][$ms['ID']]))
			{
				$data['product_prices'][$ms['ID']] = $ms;
				unset($data['product_prices'][$ms['ID']][self::ID_PR_PRICE_DESC]);
				unset($data['product_prices'][$ms['ID']]['name']);
				unset($data['product_prices'][$ms['ID']]['description']);
				unset($data['product_prices'][$ms['ID']][self::ID_LANGS]);
			}
			$data['product_prices'][$ms['ID']]['desc'][$ms[self::ID_LANGS]] = array('name' => $ms['name'], 'description' => $ms['description']);
		}

		$this->db->select("A.*, A.`".self::ID_PR_ALB."` AS ID, B.*")
				->from("`".self::PR_ALB."` AS A")
				->join(	"`".self::PR_ALB_DESC."` AS B",
						"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."`",
						"left")
				->where("A.`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_album_blocks'][$ms['ID']] = $ms['alias'];
			if(!isset($data['product_album'][$ms['ID']]))
			{
				$data['product_album'][$ms['ID']] = $ms;
				unset($data['product_prices'][$ms['ID']][self::ID_PR_ALB_DESC]);
				unset($data['product_prices'][$ms['ID']]['name']);
				unset($data['product_prices'][$ms['ID']][self::ID_LANGS]);
			}
			$data['product_album'][$ms['ID']]['desc'][$ms[self::ID_LANGS]] = array('name' => $ms['name']);
		}

		$this->db->select("*")
				->from("`".self::NTYPES."`")
				->where("`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_types'][$ms[self::ID_PR_TYPES]] = $ms[self::ID_PR_TYPES];
			$data['product_properties'][$ms[self::ID_PR_TYPES]][$ms[self::ID_PR_PROPERTIES]] = $ms[self::ID_PR_PROPERTIES];
		}

		$this->db->select("*")
				->from("`".self::NATTRIBUTES."`")
				->where("`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_attributes'][$ms[self::ID_PR_ATTRIBUTES]] = $ms[self::ID_PR_ATTRIBUTES];
			$data['product_attributes_options'][$ms[self::ID_PR_ATTRIBUTES]][$ms[self::ID_PR_ATTRIBUTES_OPTIONS]] = $ms[self::ID_PR_ATTRIBUTES_OPTIONS];
		}

		$this->db->select("A.`".self::ID_CAT."` AS ID")
				->from("`".self::PR_CAT."` AS A")
				->where("A.`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['product_categories'][$ms['ID']] = $ms['ID'];
		}

		$data['data_not_related_pr'] = $this->get_not_related_pr($id);
		$data['data_related_pr'] = $this->get_related_pr($id);

		$data['data_not_similar_pr'] = $this->get_not_similar_pr($id);
		$data['data_similar_pr'] = $this->get_similar_pr($id);
		return $data;
	}

	public function add_product_to_wh($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		$wh = $this->mwarehouses->get_wh($wh_id);
		$this->template->add_title(' | '.$wh['alias'])->add_title(' | Добавить продукт');
		$this->template->add_navigation($wh['alias'], set_url('*/*/wh_actions/wh_id/'.$wh_id))->add_navigation('Добавить продукт');

		$data = $this->add_edit_base($wh_id);

		$this->load->helper('catalogue/warehouses_products_helper');
		helper_wh_products_form_build($wh_id, $data);

		return TRUE;
	}

	public function add_exist_product_to_wh($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		$wh = $this->mwarehouses->get_wh($wh_id);
		$this->template->add_title(' | '.$wh['alias']);
		$this->template->add_navigation($wh['alias']);

		$data = $this->add_edit_base();

		$this->load->helper('warehouse/warehouses_products_helper');
		helper_wh_products_form_build($wh_id, $data);

		return TRUE;
	}

	public function add_edit_base()
	{
		$data = array();
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();

		$this->load->model('customers/mcustomers_types');
		$data['data_customers_types'] = $this->mcustomers_types->get_customers_types();

		$this->load->model('catalogue/mproducts_properties');
		$data['data_products_types'] = $this->mproducts_properties->get_types_N_properties();

		$this->load->model('catalogue/mproducts_attributes_options');
		$data['data_products_attributes'] = $this->mproducts_attributes_options->get_attributes_N_options();

		$this->load->model('catalogue/mcategories');
		$data['data_products_categories'] = $this->mcategories->get_categories_tree();

		$this->load->model('catalogue/mcurrency');
		$data['data_default_currency'] = $this->mcurrency->get_default_currency_name();

		return $data;
	}

	public function get_not_related_pr($pr_id = FALSE)
	{
		if($pr_id)
		{
			$ajax_url = '*/*/ajax_pr_not_related_grid/pr_id/'.$pr_id;
		}
		else
		{
			$ajax_url = '*/*/ajax_pr_not_related_grid';
		}

		$this->load->library('grid');
		$this->grid->_init_grid('products_not_related_grid', array('url' => set_url($ajax_url)));
		$this->grid->init_fixed_buttons(FALSE);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		if($pr_id)
		{
			$this->grid->db->join("`".self::PR_REL."` AS S",
						"S.`".self::ID_PR."` = '".$pr_id."' && S.`".self::ID_PR_REL."` = A.`".self::ID_PR."`",
						"LEFT")
				->where("A.`".self::ID_PR."` <> ".$pr_id, NULL, FALSE)
				->where("S.`".self::ID_PR_REL."` IS NULL", NULL, FALSE);
		}
		$this->load->helper('catalogue/products_save_helper');
		add_related_pr_grid_build($this->grid);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}

	public function get_related_pr($pr_id)
	{
		$this->load->library('nosql_grid');
		$this->nosql_grid->_init_grid('products_related_grid');
		$this->nosql_grid->init_fixed_buttons(FALSE);

		$query = $this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join("`".self::PR_REL."` AS C",
					"C.`".self::ID_PR."` = '".$pr_id."' && C.`".self::ID_PR_REL."` = A.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$result = $query->get()->result_array();

		$this->load->helper('catalogue/products_save_helper');
		related_pr_grid_build($this->nosql_grid, $pr_id);
		$this->nosql_grid->set_grid_data($result);
		$this->nosql_grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->nosql_grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function get_not_similar_pr($pr_id = FALSE)
	{
		if($pr_id)
		{
			$ajax_url = '*/*/ajax_pr_not_similar_grid/pr_id/'.$pr_id;
		}
		else
		{
			$ajax_url = '*/*/ajax_pr_not_similar_grid';
		}

		$this->load->library('grid');
		$this->grid->_init_grid('products_not_similar_grid', array('url' => set_url($ajax_url)));
		$this->grid->init_fixed_buttons(FALSE);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		if($pr_id)
		{
			$this->grid->db->join("`".self::PR_SIM."` AS S",
						"S.`".self::ID_PR."` = '".$pr_id."' && S.`".self::ID_PR_SIM."` = A.`".self::ID_PR."`",
						"LEFT")
				->where("A.`".self::ID_PR."` <> ".$pr_id, NULL, FALSE)
				->where("S.`".self::ID_PR_SIM."` IS NULL", NULL, FALSE);
		}

		$this->load->helper('catalogue/products_save_helper');
		add_similar_pr_grid_build($this->grid);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock', array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status', array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}

	public function get_similar_pr($pr_id)
	{
		$this->load->library('nosql_grid');
		$this->nosql_grid->_init_grid('products_similar_grid');
		$this->nosql_grid->init_fixed_buttons(FALSE);

		$query = $this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join("`".self::PR_SIM."` AS C",
					"C.`".self::ID_PR."` = '".$pr_id."' && C.`".self::ID_PR_SIM."` = A.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$result = $query->get()->result_array();

		$this->load->helper('catalogue/products_save_helper');
		similar_pr_grid_build($this->nosql_grid, $pr_id);
		$this->nosql_grid->set_grid_data($result);
		$this->nosql_grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->nosql_grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function get_related_pr_id($id_pr)
	{
		$query = $this->db
				->select("A.`".self::ID_PR_REL."` AS ID")
				->from("`".self::PR_REL."` AS A")
				->where("A.`".self::ID_PR."`", $id_pr);
		$result_arr = $query->get()->result_array();
		$result = array();
		foreach($result_arr as $val)
		{
			$result[] = $val['ID'];
		}
		return $result;
	}

	protected function save_pr_validation($pr_id = FALSE, $wh_id = FALSE)
	{
		if($pr_id)
		{
			if(!$this->check_isset_pr($pr_id)) return FALSE;
			$this->id_pr = $pr_id;
		}

		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_pr_sku', 'mproducts_save');
		$this->form_validation->add_callback_function_class('check_isset_pr_url', 'mproducts_save');
		$this->form_validation->add_callback_function_class('is_0_or_1', 'mproducts_save');

		$this->form_validation->set_message('is_0_or_1', 'Не верное значение поля "%s"!');
		$this->form_validation->set_message('check_isset_pr_sku', 'Продукт с указанным артикулом уже существует!');
		$this->form_validation->set_message('check_isset_pr_url', 'Продукт с указанным сегментом URL уже существует!');

		$this->form_validation->set_rules('product[sku]', 'Артикул', 'trim|required|callback_check_isset_pr_sku');
		$this->form_validation->set_rules('product[url_key]', 'Сегмент URL', 'trim|callback_check_isset_pr_url');


		$this->form_validation->set_rules('product[status]', 'Включен в поискс', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('product[in_stock]', 'В наличии', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('product[new]', 'Новинка', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('product[bestseller]', 'Хит продаж', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('product[sale]', 'Акция | Распродажа', 'required|callback_is_0_or_1');

		if($wh_id)
		{
			$this->form_validation->set_rules('warehouse['.$wh_id.'][qty]', 'Количество продукта', 'required|trim|intval|is_natural');
		}
		else
		{
			/*$this->load->model('warehouse/mwh_settings');
			$wh_settings = $this->mwh_settings->get_wh_settings();
			if($wh_settings['wh_on'] == 1)
			{
				$this->load->model('warehouse/mwarehouses_products');
				$data['data_warehouses'] = $this->mwarehouses_products->get_wh_pr_qty();
				foreach($data['data_warehouses'] as $key => $ms)
				{
					$this->form_validation->set_rules('warehouse['.$key.'][qty]', 'Количество продукта (Склад '.$ms['alias'].') ', 'required|trim|intval|is_natural');
				}
			}*/
		}

		foreach($this->input->post('product_prices') as $key => $ms)
		{
			$this->form_validation->set_rules('product_prices['.$key.'][real_qty]', 'Реальное количество в одной единице', 'required|trim|intval|greater_than[0]');
			$this->form_validation->set_rules('product_prices['.$key.'][min_qty]', 'Минимальное количество единиц для заказа', 'required|trim|intval|greater_than[0]');
			$this->form_validation->set_rules('product_prices['.$key.'][visible_rules]', 'Правила показа цены', 'required|intval|is_natural|less_than[3]');
		}

		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); $this->set_post_to_session(); return FALSE; }

		return TRUE;
	}

	public function check_isset_pr($pr_id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $pr_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0) return FALSE;
		return TRUE;
	}

	public function check_isset_pr_sku($sku)
	{
		$sku = trim($sku);
		$query = $this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`sku`", $sku)->limit(1);
		if($this->id_pr)
		{
			$query->where("`".self::ID_PR."` <>", $this->id_pr);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function check_isset_pr_url($url)
	{
		$url = trim($url);
		if($url == '') return TRUE;
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::PR."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`url_key`", $url)->limit(1);
		if($this->id_pr)
		{
			$query->where("`".self::ID_PR."` <>", $this->id_pr);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function save_pr($id = FALSE, $wh_id = FALSE)
		 {
		  if(!$this->input->post('product')) return FALSE;
		  if($id)
		  {
		   $id = intval($id);
		  }

		  if(!$this->save_pr_validation($id, $wh_id)) return FALSE;

		  if($id)
		  {
		   $pr_data = $this->input->post('product');
		   if($pr_data['in_stock'] == 1) {
		    $this->send_waitlist_message($id);
		   }

		   $this->db->trans_start();
		   $POST = $this->input->post('product');
		   list($id, $pr_sort) = $this->save_product($POST, $id);

		   $POST = $this->input->post('product_desc');
		   $this->save_product_desc($POST, $id, TRUE);

		   $POST = $this->input->post('product_prices');
		   $this->save_price($POST, $id, TRUE);

		   $POST = $this->input->post('product_album');
		   $this->save_albums($POST, $id, TRUE);

		   $POST = array('product_types' => $this->input->post('product_types'), 'product_properties' => $this->input->post('product_properties'));
		   $this->save_types($POST, $id, TRUE);

		   $POST = array('product_attributes' => $this->input->post('product_attributes'), 'product_attributes_options' => $this->input->post('product_attributes_options'));
		   $this->save_attributes($POST, $id, TRUE);


		   $POST = $this->input->post('product_categories');
		   $this->save_categories($POST, $id, $pr_sort, TRUE);

		   $POST = $this->input->post('products_related_checkbox');
		   $this->save_related($POST, $id);

		   $POST = $this->input->post('products_similar_checkbox');
		   $this->save_similar($POST, $id);

		   /*if($POST = $this->input->post('warehouse'))
		   {
		    $this->load->model('warehouse/mwarehouses_products');
		    foreach($POST as $key => $ms)
		    {
		     $this->mwarehouses_products->edit_wh_pr_qty($key, $id, $ms['qty']);
		    }
		   }*/

		   $this->db->trans_complete();
		   if($this->db->trans_status())
		   {
		    $this->replace_sphinx_product_data($id);
		    return $id;
		   }
		   else
		   {
		    $this->set_post_to_session();
		    return FALSE;
		   }
		  }
		  else
		  {
		   $this->db->trans_start();
		   $POST = $this->input->post('product');
		   list($id, $pr_sort) = $this->save_product($POST);
		   if($id)
		   {
		    $POST = $this->input->post('product_desc');
		    $this->save_product_desc($POST, $id);

		    if($this->input->post('product_prices'))
		    {
		     $POST = $this->input->post('product_prices');
		     $this->save_price($POST, $id);
		    }
		    if($this->input->post('product_album'))
		    {
		     $POST = $this->input->post('product_album');
		     $this->save_albums($POST, $id);
		    }
		    if($this->input->post('product_types'))
		    {
		     $POST =  array('product_types' => $this->input->post('product_types'), 'product_properties' => $this->input->post('product_properties'));
		     $this->save_types($POST, $id);
		    }
		    if($this->input->post('product_attributes'))
		    {
		     $POST =  array('product_attributes' => $this->input->post('product_attributes'), 'product_attributes_options' => $this->input->post('product_attributes_options'));
		     $this->save_attributes($POST, $id);
		    }
		    if($POST = $this->input->post('product_categories'))
		    {
		     $this->save_categories($POST, $id, $pr_sort);
		    }
		    if($POST = $this->input->post('products_related_checkbox'))
		    {
		     $this->save_related($POST, $id);
		    }
		    if($POST = $this->input->post('products_similar_checkbox'))
		    {
		     $this->save_similar($POST, $id);
		    }

		    /*if($POST = $this->input->post('warehouse'))
		    {
		     $this->load->model('warehouse/mwarehouses_products');
		     $PPOST = $this->input->post('product');
		     foreach($POST as $key => $ms)
		     {
		      $this->mwarehouses_products->edit_wh_pr_qty($key, $id, $PPOST['sku'], $ms['qty']);
		     }
		    }*/

		    $this->db->trans_complete();
		    if($this->db->trans_status())
		    {
		     $this->replace_sphinx_product_data($id);
		     return $id;
		    }
		    else
		    {
		     $this->set_post_to_session();
		     return FALSE;
		    }
		   }
		   else
		   {
		    $this->set_post_to_session();
		    return FALSE;
		   }
		  }
		 }

	public function set_post_to_session()
	{
		$this->session->set_flashdata(self::PR_ADDEDIT_FORM_ID, $this->input->post());
		return $this;
	}

	protected function replace_sphinx_product_data($pr_id)
	{
		$sphinxQL = new Connection();
		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);

		$wh_select_part = "0 AS qty, ";
		$wh_join_part = "";
		$this->load->model('warehouse/mwarehouses');
		if($wh_id = $this->mwarehouses->get_shop_wh_id())
		{
			$wh_select_part = 'IF(WH_PR.`qty`, WH_PR.`qty`, 0) AS qty, ';
			$wh_join_part = 'LEFT JOIN `wh_products` AS WH_PR ON WH_PR.`id_m_c_products` = B.`id_m_c_products` && WH_PR.`id_wh` = '.$wh_id;
		}

		$sql_query = '
			SELECT B.`id_m_c_products_description` as id, A.`id_m_c_products`, A.`sku`, A.`status`, A.`in_stock`, A.`new`, A.`bestseller`, A.`sale`, '.$wh_select_part.'A.`id_users`, B.`id_langs`, UNIX_TIMESTAMP(A.`create_date`) AS create_date ,
			(SELECT `price` FROM `m_c_products_price` WHERE `id_m_c_products` = B.`id_m_c_products` ORDER BY `id_m_c_products` LIMIT 1) AS price,
			B.`name` , B.`short_description` , B.`full_description` , B.`seo_title` , B.`seo_description` , B.`seo_keywords` ,
			GROUP_CONCAT( DISTINCT(CAST( PNC.`id_m_c_categories` AS CHAR )) ORDER BY PNC.`id_m_c_categories` SEPARATOR "," ) AS id_m_c_categories,
			GROUP_CONCAT( DISTINCT(CAST( PNT.`id_m_c_products_types` AS CHAR )) ORDER BY PNT.`id_m_c_products_types` SEPARATOR "," ) AS id_m_c_products_types,
			GROUP_CONCAT( DISTINCT(CAST( PNT.`id_m_c_products_properties` AS CHAR )) ORDER BY PNT.`id_m_c_products_properties` SEPARATOR "," ) AS id_m_c_products_properties,
			GROUP_CONCAT( DISTINCT(CAST( PNA.`id_m_c_products_attributes` AS CHAR )) ORDER BY PNA.`id_m_c_products_attributes` SEPARATOR "," ) AS id_m_c_products_attributes,
			GROUP_CONCAT( DISTINCT(CAST( PNA.`id_m_c_products_attributes_options` AS CHAR )) ORDER BY PNA.`id_m_c_products_attributes_options` SEPARATOR "," ) AS id_m_c_products_attributes_options
			FROM `m_c_products_description` AS B
			INNER JOIN `m_c_products` AS A ON A.`id_m_c_products` = B.`id_m_c_products` && A.`id_m_c_products` = '.$pr_id.'
			'.$wh_join_part.'
			LEFT JOIN `m_c_productsNcategories` AS PNC ON PNC.`id_m_c_products` = B.`id_m_c_products`
			LEFT JOIN `m_c_productsNtypes` AS PNT ON PNT.`id_m_c_products` = B.`id_m_c_products`
			LEFT JOIN  `m_c_productsNattributes` AS PNA ON PNA.`id_m_c_products` = B.`id_m_c_products`
			GROUP BY (B.`id_m_c_products_description`);';

		$DB = $this->db->query($sql_query);
		foreach($DB->result_array() as $ms)
		{
			$replace_part = "";
			$check_SP_query = "SELECT count(*) FROM `".self::SPHINX_INDEX."` WHERE `id` = ".$ms['id'];
			$check_SP_res = $sphinxQL->query($check_SP_query);

			$replace_part .= "(".$ms['id'].", ".$ms['id_m_c_products'].", '".$ms['sku']."', ".$ms['status'].", ".$ms['in_stock'].", ".$ms['new'].", ".$ms['bestseller'].", ".$ms['sale'].", ".$ms['qty'].", ".$ms['id_users'].", ".$ms['id_langs'].", ".$ms['create_date'].",
				".$ms['price'].",
				'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['name'])), ENT_COMPAT, 'UTF-8')))."',
				'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['short_description'])), ENT_COMPAT, 'UTF-8')))."',
				'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['full_description'])), ENT_COMPAT, 'UTF-8')))."',
				'".addslashes($ms['seo_title'])."',
				'".addslashes($ms['seo_description'])."',
				'".addslashes($ms['seo_keywords'])."',
				(".$ms['id_m_c_categories']."), (".$ms['id_m_c_products_types']."), (".$ms['id_m_c_products_properties']."), (".$ms['id_m_c_products_attributes']."), (".$ms['id_m_c_products_attributes_options']."))";

			if(count($check_SP_res) > 0 && $check_SP_res[0]['count(*)'] > 0)
			{
				$sphinx_query = "REPLACE INTO `".self::SPHINX_INDEX."` (`id`, `id_m_c_products`, `sku`, `status`, `in_stock`, `new`, `bestseller`, `sale`, `qty`, `id_users`, `id_langs`, `create_date`, `price`, `name`, `short_description`, `full_description`, `seo_title`, `seo_description`, `seo_keywords`, `id_m_c_categories`, `id_m_c_products_types`, `id_m_c_products_properties`, `id_m_c_products_attributes`, `id_m_c_products_attributes_options`) VALUES";
			}
			else
			{
				$sphinx_query = "INSERT INTO `".self::SPHINX_INDEX."` (`id`, `id_m_c_products`, `sku`, `status`, `in_stock`, `new`, `bestseller`, `sale`, `qty`, `id_users`, `id_langs`, `create_date`, `price`, `name`, `short_description`, `full_description`, `seo_title`, `seo_description`, `seo_keywords`, `id_m_c_categories`, `id_m_c_products_types`, `id_m_c_products_properties`, `id_m_c_products_attributes`, `id_m_c_products_attributes_options`) VALUES";
			}
			$update_data = $ms;
			$sphinx_query .= $replace_part;
			$sphinxQL->query($sphinx_query);
		}

		$sphinxQL->query(
			"UPDATE `".self::SPHINX_INDEX."` SET
			`status` = ".$update_data['status'].",
			`in_stock` = ".$update_data['in_stock'].",
			`bestseller` = ".$update_data['bestseller'].",
			`sale` = ".$update_data['sale'].",
			`qty` = ".$update_data['qty'].",
			`price` = ".$update_data['price'].",
			`id_m_c_categories` = (".$update_data['id_m_c_categories']."),
			`id_m_c_products_types` = (".$update_data['id_m_c_products_types']."),
			`id_m_c_products_properties` = (".$update_data['id_m_c_products_properties']."),
			`id_m_c_products_attributes` = (".$update_data['id_m_c_products_attributes']."),
			`id_m_c_products_attributes_options` = (".$update_data['id_m_c_products_attributes_options'].")
			WHERE `".self::ID_PR."` = ".$pr_id
		);
		return TRUE;
	}

	protected function save_product($POST, $id = FALSE)
	{
		if($id)
		{
			$this->db->select("MAX(`sort`) AS MAX")->from("`".self::PR."`")->where("`".self::ID_USERS."`", $this->id_users);
			$max_sort = $this->db->get()->row_array();
			$max_sort = $max_sort['MAX'];
			if(is_null($max_sort)) $max_sort = 1;

			$this->sql_add_data($POST)->sql_update_date()->sql_using_user()->sql_save(self::PR, $id);
			$this->sql_add_data(array('new' => $POST['new']))->sql_save(self::PR_CAT, array(self::ID_PR => $id));

			return array($id, $max_sort);
		}
		else
		{
			$this->db->select("MAX(`sort`) AS MAX")->from("`".self::PR."`")->where("`".self::ID_USERS."`", $this->id_users);
			$max_sort = $this->db->get()->row_array();
			$max_sort = $max_sort['MAX'];
			if(is_null($max_sort)) $max_sort = 1; else $max_sort++;

			$id = $this->sql_add_data($POST + array('sort' => $max_sort))->sql_update_date()->sql_using_user()->sql_save(self::PR);

			return array($id, $max_sort);
		}
	}

	protected function save_product_desc($POST, $PID, $edit = FALSE)
	{
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();

		if($edit)
		{
			$this->db
					->select("A.`".self::ID_PR."`, B.`".self::ID_PR_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR."` AS A")
					->join(	"`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."`",
							"left")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $this->db->get()->result_array();
			$products_desc_data = array();
			foreach($result as $ms)
			{
				$products_desc_data[$ms[self::ID_LANGS]] = $ms;
			}

			foreach($langs as $key => $ms)
			{
				if(isset($POST[$key]))
				{
					if(isset($products_desc_data[$key]))
					{
						$data = $POST[$key];
						$this->sql_add_data($data)->sql_save(self::PR_DESC, $products_desc_data[$key][self::ID_PR_DESC]);
						$products_desc_data[$key][self::ID_PR_DESC];
					}
					else
					{
						$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_PR => $PID);
						$this->sql_add_data($data)->sql_save(self::PR_DESC);
					}
				}
			}
		}
		else
		{
			foreach($langs as $key => $ms)
			{
				if(isset($POST[$key]))
				{
					$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_PR => $PID);
					$this->sql_add_data($data)->sql_save(self::PR_DESC);
				}
			}
		}
		return TRUE;
	}

	protected function save_price($POST, $PID, $edit = FALSE)
	{
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();

		if($edit)
		{
			$this->db->select("A.`".self::ID_PR_PRICE."` AS ID, B.`".self::ID_PR_PRICE_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_PRICE."` AS A")
					->join(	"`".self::PR_PRICE_DESC."` AS B",
							"B.`".self::ID_PR_PRICE."` = A.`".self::ID_PR_PRICE."`",
							"left")
					->where("A.`".self::ID_PR."`", $PID);

			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$qdata['prices'][$ms['ID']] = $ms['ID'];
				$qdata['prices_lang'][$ms['ID']][$ms[self::ID_LANGS]] = $ms[self::ID_PR_PRICE_DESC];
			}
			foreach($POST as $key => $ms)
			{
				if(isset($qdata['prices'][$key]))
				{
					$data = $ms;
					unset($data['desc']);

					$data = $this->prepare_save_price_visible_rules($data);
					$data = $this->prepare_save_price_show_attributes($data);
					$data = $this->prepare_save_special_price($data);

					$this->sql_add_data($data)->sql_save(self::PR_PRICE, $qdata['prices'][$key]);

					$POSTL = $ms['desc'];

					foreach($langs as $l_key => $l_ms)
					{
						if(isset($POSTL[$l_key]))
						{
							if(isset($qdata['prices_lang'][$key][$l_key]))
							{
								$data = $POSTL[$l_key];
								$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC, $qdata['prices_lang'][$key][$l_key]);
							}
							else
							{
								$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_PRICE => $qdata['prices'][$key]);
								$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC);
							}
						}
					}
					unset($qdata['prices'][$key]);
				}
				else
				{
					$data = $ms;
					unset($data['desc']);

					$data = $this->prepare_save_price_visible_rules($data);
					$data = $this->prepare_save_price_show_attributes($data);
					$data = $this->prepare_save_special_price($data);
					$ID = $this->sql_add_data($data + array(self::ID_PR => $PID))->sql_save(self::PR_PRICE);
					if($ID && $ID > 0)
					{
						$POSTL = $ms['desc'];
						foreach($langs as $l_key => $l_ms)
						{
							if(isset($POSTL[$l_key]))
							{
								$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_PRICE => $ID);
								$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC);
							}
						}
					}
				}
			}
			//DELETE PRICE
			$del_array = FALSE;
			if(isset($qdata['prices']))
			{
				foreach($qdata['prices'] as $ms)
				{
					$del_array[] = $ms;
				}
				if($del_array)
				{
					$this->db->where_in(self::ID_PR_PRICE, $del_array);
					$this->db->delete(self::PR_PRICE);
				}
			}
			//---------------------------------
		}
		else
		{
			foreach($POST as $ms)
			{
				$data = $ms;
				unset($data['desc']);

				$data = $this->prepare_save_price_visible_rules($data);
				$data = $this->prepare_save_price_show_attributes($data);
				$data = $this->prepare_save_special_price($data);
				$ID = $this->sql_add_data($data + array(self::ID_PR => $PID))->sql_save(self::PR_PRICE);
				if($ID && $ID > 0)
				{
					$POSTL = $ms['desc'];
					foreach($langs as $l_key => $l_ms)
					{
						if(isset($POSTL[$l_key]))
						{
							$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_PRICE => $ID);
							$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC);
						}
					}
				}
			}
		}
	}

	protected function prepare_save_price_visible_rules($data)
	{
		if($data['visible_rules'] == 2)
		{
			if(isset($data['m_u_types']) && is_array($data['m_u_types']))
			{
				$m_u_types_temp = $data['m_u_types'];
				unset($data['m_u_types']);
				$data['m_u_types'] = '';

				foreach($m_u_types_temp as $tms)
				{
					$data['m_u_types'] .= $tms.',';
				}
				$data['m_u_types'] = substr($data['m_u_types'],0,-1);
			}
			else
			{
				$data['visible_rules'] = 1;
				$data['m_u_types'] = NULL;
			}
		}
		else
		{
			$data['m_u_types'] = NULL;
		}
		return $data;
	}

	protected function prepare_save_special_price($data)
	{
		$data['special_price'] = floatval($data['special_price']);
		if($data['special_price'] <= 0)
		{
			$data['special_price'] = NULL;
			$data['special_price_from'] = NULL;
			$data['special_price_to'] = NULL;
		}
		else
		{
			if(trim($data['special_price_from']) == '') $data['special_price_from'] = NULL;
			if(trim($data['special_price_to']) == '') $data['special_price_to'] = NULL;
		}
		return $data;
	}

	protected function prepare_save_price_show_attributes($data)
	{
		if($data['show_attributes'] == 2)
		{
			if(isset($data['id_attributes']))
			{
				$attr = $data['id_attributes'];
				unset($data['id_attributes']);
				$data['id_attributes'] = '';
				foreach($attr as $at)
				{
					$data['id_attributes'] .= $at.',';
				}
				$data['id_attributes'] = substr($data['id_attributes'],0,-1);
			}
			else
			{
				$data['id_attributes'] = '';
			}
		}
		else
		{
			$data['id_attributes'] = '';
		}
		return $data;
	}

	protected function save_types($POST, $PID, $edit = FALSE)
	{
		if($edit)
		{
			if($POST['product_types'] == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::NTYPES);
				return TRUE;
			}
			$this->db->select("A.*")
					->from("`".self::NTYPES."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$tdata['product_types'][$ms[self::ID_PR_TYPES]] = $ms[self::ID_PR_TYPES];
				$tdata['product_properties'][$ms[self::ID_PR_TYPES]][$ms[self::ID_PR_PROPERTIES]] = $ms[self::ID_PR_PROPERTIES];
			}

			foreach($POST['product_types'] as $ms)
			{
				if(isset($tdata['product_types'][$ms]))
				{
					unset($tdata['product_types'][$ms]);
				}

				if(isset($POST['product_properties'][$ms]))
				{
					foreach($POST['product_properties'][$ms] as $pr)
					{
						if(isset($tdata['product_properties'][$ms][$pr]))
						{
							unset($tdata['product_properties'][$ms][$pr]);
						}
						else
						{
							$data = array(self::ID_PR_TYPES => $ms, self::ID_PR_PROPERTIES => $pr, self::ID_PR => $PID);
							$this->sql_add_data($data)->sql_save(self::NTYPES);
						}
					}
				}
			}

			//DELETE
			$del_types_array = FALSE;
			$del_prop_array = FALSE;
			if(isset($tdata['product_types']))
			{
				foreach($tdata['product_types'] as $ms)
				{
					$del_types_array[] = $ms;
					if(isset($tdata['product_properties'][$ms])) unset($tdata['product_properties'][$ms]);
				}
			}
			if(isset($tdata['product_properties']))
			{
				foreach($tdata['product_properties'] as $pr)
				{
					if(is_array($pr))
					{
						foreach($pr as $ms)
						{
							$del_prop_array[] = $ms;
						}
					}
				}
			}
			if($del_types_array)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->where_in("`".self::ID_PR_TYPES."`", $del_types_array);
				$this->db->delete(self::NTYPES);
			}
			if($del_prop_array)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->where_in("`".self::ID_PR_PROPERTIES."`", $del_prop_array);
				$this->db->delete(self::NTYPES);
			}
			//------------------------------------
		}
		else
		{
			if($POST['product_types'] == FALSE) return FALSE;
			if(!is_array($POST['product_types'])) return FALSE;

			foreach($POST['product_types'] as $ms)
			{
				if(isset($POST['product_properties'][$ms]))
				{
					foreach($POST['product_properties'][$ms] as $pr)
					{
						$data = array(self::ID_PR_TYPES => $ms, self::ID_PR_PROPERTIES => $pr, self::ID_PR => $PID);
						$this->sql_add_data($data)->sql_save(self::NTYPES);
					}
				}
			}
		}
		return TRUE;
	}

	protected function save_attributes($POST, $PID, $edit = FALSE)
	{
		if($edit)
		{
			if($POST['product_attributes'] == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::NATTRIBUTES);
				return TRUE;
			}
			$this->db->select("A.*")
					->from("`".self::NATTRIBUTES."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$tdata['product_attributes'][$ms[self::ID_PR_ATTRIBUTES]] = $ms[self::ID_PR_ATTRIBUTES];
				$tdata['product_attributes_options'][$ms[self::ID_PR_ATTRIBUTES]][$ms[self::ID_PR_ATTRIBUTES_OPTIONS]] = $ms[self::ID_PR_ATTRIBUTES_OPTIONS];
			}

			foreach($POST['product_attributes'] as $ms)
			{
				if(isset($tdata['product_attributes'][$ms]))
				{
					unset($tdata['product_attributes'][$ms]);
				}

				if(isset($POST['product_attributes_options'][$ms]))
				{
					foreach($POST['product_attributes_options'][$ms] as $pr)
					{
						if(isset($tdata['product_attributes_options'][$ms][$pr]))
						{
							unset($tdata['product_attributes_options'][$ms][$pr]);
						}
						else
						{
							$data = array(self::ID_PR_ATTRIBUTES => $ms, self::ID_PR_ATTRIBUTES_OPTIONS => $pr, self::ID_PR => $PID);
							$this->sql_add_data($data)->sql_save(self::NATTRIBUTES);
						}
					}
				}
			}

			//DELETE
			$del_attr_array = FALSE;
			$del_opt_array = FALSE;
			if(isset($tdata['product_attributes']))
			{
				foreach($tdata['product_attributes'] as $ms)
				{
					$del_attr_array[] = $ms;
					if(isset($tdata['product_attributes_options'][$ms])) unset($tdata['product_attributes_options'][$ms]);
				}
			}
			if(isset($tdata['product_attributes_options']))
			{
				foreach($tdata['product_attributes_options'] as $pr)
				{
					if(is_array($pr))
					{
						foreach($pr as $ms)
						{
							$del_opt_array[] = $ms;
						}
					}
				}
			}
			if($del_attr_array)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->where_in("`".self::ID_PR_ATTRIBUTES."`", $del_attr_array);
				$this->db->delete(self::NATTRIBUTES);
			}
			if($del_opt_array)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->where_in("`".self::ID_PR_ATTRIBUTES_OPTIONS."`", $del_opt_array);
				$this->db->delete(self::NATTRIBUTES);
			}
			//------------------------------------
		}
		else
		{
			if($POST['product_attributes'] == FALSE) return FALSE;
			if(!is_array($POST['product_attributes'])) return FALSE;
			foreach($POST['product_attributes'] as $ms)
			{
				if(isset($POST['product_attributes_options'][$ms]))
				{
					foreach($POST['product_attributes_options'][$ms] as $pr)
					{
						$data = array(self::ID_PR_ATTRIBUTES => $ms, self::ID_PR_ATTRIBUTES_OPTIONS => $pr, self::ID_PR => $PID);
						$this->sql_add_data($data)->sql_save(self::NATTRIBUTES);
					}
				}
			}
		}
		return TRUE;
	}

	protected function save_categories($POST, $PID, $sort, $edit = FALSE)
	{
		if($edit)
		{
			if($POST == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::PR_CAT);
				return TRUE;
			}
			$this->db->select("A.`".self::ID_PR_CAT."` AS ID, A.`".self::ID_CAT."` AS CID")
					->from("`".self::PR_CAT."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $this->db->get()->result_array();
			$tdata['product_categories'] = array();

			foreach($result as $ms)
			{
				$tdata['product_categories'][$ms['CID']] = $ms['ID'];
			}
			foreach($POST as $ms)
			{
				if(isset($tdata['product_categories'][$ms]))
				{
					unset($tdata['product_categories'][$ms]);
				}
				else
				{
					$data = array(self::ID_CAT => $ms, self::ID_PR => $PID, 'sort' => $sort);
					$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_CAT);
				}
			}

			//DELETE
			$del_array = FALSE;
			if(isset($tdata['product_categories']))
			{
				foreach($tdata['product_categories'] as $ms)
				{
					$del_array[] = $ms;
				}
			}
			if($del_array)
			{
				$this->db->where_in(self::ID_PR_CAT, $del_array);
				$this->db->delete(self::PR_CAT);
			}
			//------------------------------------

			$POST = $this->input->post('product');
			if(isset($POST['new']))
			{
				$this->sql_add_data(array('new' => $POST['new']))->sql_save(self::PR_CAT, array(self::ID_PR => $PID));
			}
		}
		else
		{
			if($POST == FALSE) return FALSE;
			if(!is_array($POST)) return FALSE;

			foreach($POST as $ms)
			{
				$data = array(self::ID_CAT => $ms, self::ID_PR => $PID, 'sort' => $sort);
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_CAT);
			}
		}

		$POST = $this->input->post('product');
		if(isset($POST['new']))
		{
			$this->sql_add_data(array('new' => $POST['new']))->sql_save(self::PR_CAT, array(self::ID_PR => $PID));
		}
		return TRUE;
	}

	public function save_related($POST, $pid)
	{
		if($POST && is_array($POST))
		{
			foreach($POST as $val)
			{
				if($this->check_isset_pr($val) && $val != $pid)
				{
					$this->sql_add_data(array(self::ID_PR_REL => $val, self::ID_PR => $pid))->sql_save(self::PR_REL);
				}
			}
		}
		return TRUE;
	}

	public function save_similar($POST, $pid)
	{
		if($POST && is_array($POST))
		{
			foreach($POST as $val)
			{
				if($this->check_isset_pr($val) && $val != $pid)
				{
					$this->sql_add_data(array(self::ID_PR_SIM => $val, self::ID_PR => $pid))->sql_save(self::PR_SIM);
				}
			}
		}
		return TRUE;
	}

	public function save_albums($POST, $PID, $edit = FALSE)
	{
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();

		if($edit)
		{
			$this->db->select("A.`".self::ID_PR_ALB."` AS ID, B.`".self::ID_PR_ALB_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_ALB."` AS A")
					->join(	"`".self::PR_ALB_DESC."` AS B",
							"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."`",
							"LEFT")
					->where("A.`".self::ID_PR."`", $PID);

			$result = $this->db->get()->result_array();
			if(count($result) == 0 && is_array($POST))
			{
				$img_not_in_alb = array();
				$this->db->select("*")
						->from("`".self::PR_IMG."`")
						->where("`".self::ID_PR."`", $PID)->where("`".self::ID_PR_ALB."` IS NULL", NULL, FALSE);
				foreach($this->db->get()->result_array() as $ms)
				{
					$img_not_in_alb[] = $ms;
				}
			}
			foreach($result as $ms)
			{
				$qdata['albums'][$ms['ID']] = $ms['ID'];
				$qdata['albums_lang'][$ms['ID']][$ms[self::ID_LANGS]] = $ms[self::ID_PR_ALB_DESC];
			}
			if(!is_array($POST)) $POST = array();
			foreach($POST as $key => $ms)
			{
				if(isset($qdata['albums'][$key]))
				{
					$data = $ms;
					unset($data['desc']);

					$this->sql_add_data($data)->sql_save(self::PR_ALB, $qdata['albums'][$key]);

					if($data['type'] == 'TEXT')
					{
						$POSTL = $ms['desc'];
						foreach($langs as $l_key => $l_ms)
						{
							if(isset($POSTL[$l_key]))
							{
								if(isset($qdata['albums_lang'][$key][$l_key]))
								{
									$data = $POSTL[$l_key];
									$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC, $qdata['albums_lang'][$key][$l_key]);
								}
								else
								{
									$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_ALB => $qdata['albums'][$key]);
									$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC);
								}
							}
						}
					}
					unset($qdata['albums'][$key]);
				}
				else
				{
					$data = $ms;
					unset($data['desc']);

					$ID = $this->sql_add_data($data + array(self::ID_PR => $PID))->sql_save(self::PR_ALB);
					if($ID && $ID > 0)
					{
						$this->sql_add_data(array('sort' => $ID))->sql_save(self::PR_ALB, $ID);
						if($data['type'] == 'TEXT')
						{
							$POSTL = $ms['desc'];
							foreach($langs as $l_key => $l_ms)
							{
								if(isset($POSTL[$l_key]))
								{
									$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_ALB => $ID);
									$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC);
								}
							}
						}
					}
					if(isset($img_not_in_alb) && count($img_not_in_alb)>0)
					{
						$old_url_data = $this->get_upload_img_config($PID);
						$new_url_data = $this->get_upload_img_config($PID, $ID);
						$sql_upd_id = array();
						foreach($img_not_in_alb as $img)
						{
							@rename($old_url_data['upload_path'].$img['image'], $new_url_data['upload_path'].$img['image']);
							@rename($old_url_data['upload_path'].'thumb_'.$img['image'], $new_url_data['upload_path'].'thumb_'.$img['image']);
							$sql_upd_id[] = $ms[self::ID_PR_IMG];
						}
						$this->db->where_in("`".self::ID_PR_IMG."`", $sql_upd_id)->update("`".self::PR_IMG."`", array(self::ID_PR_ALB => $ID));
						unset($img_not_in_alb);
					}
				}
			}
			//DELETE ALBUM

			if(isset($qdata['albums']))
			{
				foreach($qdata['albums'] as $ms)
				{
					$this->delete_pr_album($PID, $ms);
				}
				/*if($del_array)
				{
					$this->db->where_in(self::ID_PR_ALB, $del_array);
					$this->db->delete(self::PR_ALB);
				}*/
			}
			//---------------------------------
		}
		else
		{
			foreach($POST as $ms)
			{
				$data = $ms;
				unset($data['desc']);

				$ID = $this->sql_add_data($data + array(self::ID_PR => $PID))->sql_save(self::PR_ALB);
				$this->sql_add_data(array('sort' => $ID))->sql_save(self::PR_ALB, $ID);
				if($ID && $ID > 0)
				{
					if($data['type'] == 'TEXT')
					{
						$POSTL = $ms['desc'];
						foreach($langs as $l_key => $l_ms)
						{
							if(isset($POSTL[$l_key]))
							{
								$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_ALB => $ID);
								$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC);
							}
						}
					}
				}
			}
		}
	}

	public function delete_pr($id)
	{
		$this->load->helper('agfiles_helper');
		$path = BASE_PATH.'users/'.$this->id_users.self::IMG_FOLDER.$id;
		remove_dir($path);

		$this->db->where("`".self::ID_PR."`",$id)->where("`".self::ID_USERS."`", $this->id_users)->delete("`".self::PR."`");
		$this->delete_sphinx_product($id);
		return TRUE;
	}

	protected function delete_sphinx_product($pr_id)
	{
		$sphinxQL = new Connection();
		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);

		$deleted_rows = $sphinxQL->query("SELECT `id` FROM `".self::SPHINX_INDEX."` WHERE `".self::ID_PR."` = ".$pr_id);
		foreach($deleted_rows as $ms)
		{
			$sphinxQL->query("DELETE FROM `".self::SPHINX_INDEX."` WHERE `id` = ".$ms['id']);
		}
		return TRUE;
	}

	public function delete_related($id_parent, $id_related)
	{
		if($this->check_isset_pr($id_related) && $this->check_isset_pr($id_parent))
		{
			$this->db->where("`".self::ID_PR_REL."`",$id_related)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_REL."`");
			return TRUE;
		}
		return FALSE;
	}

	public function delete_similar($id_parent, $id_similar)
	{
		if($this->check_isset_pr($id_similar) && $this->check_isset_pr($id_parent))
		{
			$this->db->where("`".self::ID_PR_SIM."`",$id_similar)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_SIM."`");
			return TRUE;
		}
		return FALSE;
	}

	public function change_pr_aditional_param($id, $field, $value = 1)
	{
		if(is_array($id))
		{
			$this->db->where("`".self::ID_USERS."`", $this->id_users)->where_in("`".self::ID_PR."`", $id);
		}
		else
		{
			$this->db->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_PR."`", $id);
		}
		$this->db->set("`".$field."`", $value)->update(self::PR);

		if($field == 'new')
		{
			if(is_array($id))
			{
				$this->db->where("`".self::ID_USERS."`", $this->id_users)->where_in("`".self::ID_PR."`", $id);
			}
			else
			{
				$this->db->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_PR."`", $id);
			}
			$this->db->set("`".$field."`", $value)->update(self::PR_CAT);
		}

        if($field == 'in_stock' || $field == 'new' || $field == 'sale' || $field == 'bestseller' || $field == 'status') {
    		$sphinxQL = new Connection();
    		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);

    		$sphinx_query = "UPDATE `".self::SPHINX_INDEX."` SET
    			`".$field."` = ".$value."
    		";
    		if(is_array($id))
    		{
    			$sphinx_query .= "WHERE `".self::ID_PR."` IN(".implode(',', $id).")";
    		}
    		else
    		{
    			$sphinx_query .= "WHERE `".self::ID_PR."` = ".$id;
    		}
    		$sphinxQL->query($sphinx_query);
        }
	}

	public function check_isset_pr_album($id, $album_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR_ALB."`")
				->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ALB."`", $album_id);
		$q = $this->db->get()->row_array();
		if($q['COUNT'] > 0) return TRUE;
		return FALSE;
	}

	public function edit_pr_img($id, $album_id = FALSE)
	{
		if(!$this->check_isset_pr($id)) return FALSE;
		if($album_id)
		{
			if(!$this->check_isset_pr_album($id, $album_id)) return FALSE;
		}
		$data = array();
		$this->db->select("sku")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $id)->limit(1);
		$result = $this->db->get()->row_array();
		$this->template->add_title(' | '.$result['sku']);
		$this->template->add_navigation($result['sku'], set_url('*/*/edit/id/'.$id));
		$this->template->add_title(' | Изображения продукта');
		$this->template->add_navigation('Изображения продукта');
		if($album_id)
		{
			$this->db->select("`alias`")
					->from("`".self::PR_ALB."`")
					->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ALB."`", $album_id);
			$result = $this->db->get()->row_array();
			$this->template->add_title(' | Альбом '.$result['alias']);
			$this->template->add_navigation('Альбом '.$result['alias']);
		}

		$this->load->helper('catalogue/products_save_helper');
		$this->load->model('langs/mlangs');

		$this->db->select("`".self::ID_PR_ALB."` AS ID, `alias`")
				->from("`".self::PR_ALB."`")
				->where("`".self::ID_PR."`", $id);
		if(count($result = $this->db->get()->result_array())>0)
		{
			foreach($result as $ms)
			{
				$data['albums'][$ms['ID']] = $ms;
			}
		}

		$data += $this->get_pr_img($id, $album_id);
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$data[self::ID_USERS] = $this->id_users;
		if($album_id)
		{
			$data['product_attributes'] = array();
			$data['product_attributes_options'] = array();
			$this->db->select("A.`".self::ID_PR_ATTRIBUTES."`, A.`".self::ID_PR_ATTRIBUTES_OPTIONS."`, B.`name` AS aname, C.`name` AS opname")
				->from("`".self::NATTRIBUTES."` AS A")
				->join("`".self::PR_ATTRIBUTES."` AS AT",
						"AT.`".self::ID_PR_ATTRIBUTES."` = A.`".self::ID_PR_ATTRIBUTES."`",
						"LEFT")
				->join("`".self::PR_ATTRIBUTES_OPTIONS."` AS OP",
						"OP.`".self::ID_PR_ATTRIBUTES_OPTIONS."` = A.`".self::ID_PR_ATTRIBUTES_OPTIONS."`",
						"LEFT")
				->join("`".self::PR_ATTRIBUTES_DESC."` AS B",
						"B.`".self::ID_PR_ATTRIBUTES."` = A.`".self::ID_PR_ATTRIBUTES."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->join("`".self::PR_ATTRIBUTES_OPTIONS_DESC."` AS C",
						"C.`".self::ID_PR_ATTRIBUTES_OPTIONS."` = A.`".self::ID_PR_ATTRIBUTES_OPTIONS."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_PR."`", $id)->where("A.`".self::ID_PR_ATTRIBUTES_OPTIONS."` IS NOT NULL", NULL, FALSE)->order_by("AT.`sort`")->order_by("OP.`sort`");
			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$data['product_attributes'][$ms[self::ID_PR_ATTRIBUTES]] = $ms['aname'];
				$data['product_attributes_options'][$ms[self::ID_PR_ATTRIBUTES]][$ms[self::ID_PR_ATTRIBUTES_OPTIONS]] = $ms['opname'];
			}

			$this->db->select("A.`".self::ID_PR_ALB."` AS ID, A.`alias`, A.`type`, A.`color`, A.`".self::ID_PR_ATTRIBUTES."`, A.`".self::ID_PR_ATTRIBUTES_OPTIONS."`, A.`active`, B.`name`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_ALB."` AS A")
					->join("`".self::PR_ALB_DESC."` AS B",
							"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."`",
							"LEFT")
					->where("A.`".self::ID_PR."`", $id)->where("A.`".self::ID_PR_ALB."`", $album_id);
			foreach($this->db->get()->result_array() as $ms)
			{
				if(!isset($data['album_data']['product_album']))
				{
					$data['album_data']['product_album'] = $ms;
				}
				$data['album_attributes']['album_attributes'] = $ms[self::ID_PR_ATTRIBUTES];
				$data['album_attributes']['album_attributes_options'] = $ms[self::ID_PR_ATTRIBUTES_OPTIONS];
				$data['album_data']['product_album']['desc'][$ms[self::ID_LANGS]] = array('name' => $ms['name'], self::ID_LANGS => $ms[self::ID_LANGS]);
			}
			unset($data['album_data']['product_album']['name']);
			unset($data['album_data']['product_album'][self::ID_LANGS]);

			products_img_in_album_form($id, $album_id, $data, $save_param = '/id/'.$id.'/album_id/'.$album_id);
		}
		else
		{
			products_img_form($id, $data, $save_param = '/id/'.$id);
		}
		return $data;
	}

	public function get_pr_img($id, $album_id = FALSE)
	{
		$this->db->select("A.*, A.sort AS SORT, B.`name`, B.`title`, B.`alt`, B.`".self::ID_LANGS."`")
				->from("`".self::PR_IMG."` AS A")
				->join(
						"`".self::PR_IMG_DESC."` AS B",
						"B.`".self::ID_PR_IMG."` = A.`".self::ID_PR_IMG."`",
						"LEFT"
				)
				->where("A.`".self::ID_PR."`", $id)
				->order_by("SORT");
		if($album_id)
		{
			$this->db->where("A.`".self::ID_PR_ALB."`", $album_id);
		}

		$result = $this->db->get()->result_array();
		$data = array();
		foreach($result as $ms)
		{
			if($album_id)
			{
				$data['preview'][$ms[self::ID_PR_IMG]]['preview'] = $ms['preview'];
				$data['preview'][$ms[self::ID_PR_IMG]]['album_preview'] = $ms['album_preview'];
				$data['img'][$ms[self::ID_PR_IMG]] = array(self::ID_PR_IMG => $ms[self::ID_PR_IMG], 'timage' => $this->img_path.$id.'/'.$album_id.'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$id.'/'.$album_id.'/'.$ms['image']);
				$data['img_desc'][$ms[self::ID_PR_IMG]][$ms[self::ID_LANGS]] = array('name' => $ms['name'], 'title' => $ms['title'], 'alt' => $ms['alt']);
			}
			else
			{
				$data['preview'][$ms[self::ID_PR_IMG]]['preview'] = $ms['preview'];
				$data['img'][$ms[self::ID_PR_IMG]] = array(self::ID_PR_IMG => $ms[self::ID_PR_IMG], 'timage' => $this->img_path.$id.'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$id.'/'.$ms['image']);
				$data['img_desc'][$ms[self::ID_PR_IMG]][$ms[self::ID_LANGS]] = array('name' => $ms['name'], 'title' => $ms['title'], 'alt' => $ms['alt']);
			}
		}
		return $data;
	}

	public function save_pr_img($id, $data, $album_id = FALSE)
	{
		$POST = array('id_m_c_products' => $id, 'image' => $data['file_name']);
		if($album_id) $POST[self::ID_PR_ALB] = $album_id;

		$POST['preview'] = 0;
		$POST['album_preview'] = 0;

		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR_IMG."`")
				->where("`".self::ID_PR."`", $id);
		$res = $this->db->get()->row_array();
		if($res['COUNT'] == 0)
		{
			$POST['preview'] = 1;
		}

		if($album_id)
		{
			$this->db->select("COUNT(*) AS COUNT")
					->from("`".self::PR_IMG."`")
					->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ALB."`", $album_id);
			$res = $this->db->get()->row_array();
			if($res['COUNT'] == 0)
			{
				$POST['album_preview'] = 1;
			}
		}
		$this->db->trans_start();
		$ID = $this->sql_add_data($POST)->sql_save(self::PR_IMG);
		if($ID)
		{
			$this->sql_add_data(array('sort' => $ID))->sql_save(self::PR_IMG, $ID);
		}
		$output['preview']['preview'] = $POST['preview'];
		$output['preview']['album_preview'] = $POST['album_preview'];


		$this->db->select("`name`, `".self::ID_LANGS."`")
				->from("`".self::PR_DESC."`")
				->where("`".self::ID_PR."`", $id);
		$result = $this->db->get()->result_array();
		$d_ar = array();
		foreach($result as $ms)
		{
			$d_ar[$ms[self::ID_LANGS]] = $ms;
		}
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();

		foreach($langs as $key => $ms)
		{
			if(isset($d_ar[$key]))
			{
				$output['img_desc'][$ID][$key] = array('name' => $d_ar[$key]['name'], 'title' => $d_ar[$key]['name'], 'alt' => $d_ar[$key]['name']);
				$data = array(self::ID_LANGS => $key) + array(self::ID_PR_IMG => $ID) + array('name' => $d_ar[$key]['name'], 'title' => $d_ar[$key]['name'], 'alt' => $d_ar[$key]['name']);
				$this->sql_add_data($data)->sql_save(self::PR_IMG_DESC);
			}
		}

		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			return array($ID, $output);
		}
		return FALSE;
	}

	public function save_pr_img_desc($id)
	{
		$this->db->trans_start();
		if($IPOST = $this->input->post('img_desc'))
		{
			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();

			$this->db->select("A.`".self::ID_PR_IMG."` AS ID , B.`".self::ID_PR_IMG_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_IMG."` AS A")
					->join(	"`".self::PR_IMG_DESC."` AS B",
							"B.`".self::ID_PR_IMG."` = A.`".self::ID_PR_IMG."`",
							"left")
					->where("`".self::ID_PR."`", $id);

			$result = $this->db->get()->result_array();
			if(count($result)>0)
			{
				$images = array();
				foreach($result as $ms)
				{
					$images[$ms['ID']][$ms[self::ID_LANGS]] = $ms;
				}

				foreach($IPOST as $ikey => $ims)
				{
					if(isset($images[$ikey]))
					{
						$POST = $ims;
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								if(isset($images[$ikey][$key]))
								{
									$data = $POST[$key];
									$this->sql_add_data($data)->sql_save(self::PR_IMG_DESC, $images[$ikey][$key][self::ID_PR_IMG_DESC]);
								}
								else
								{
									$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_PR_IMG => $ikey);
									$this->sql_add_data($data)->sql_save(self::PR_IMG_DESC);
								}
							}
						}
					}
				}
				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return TRUE;
				}
				return FALSE;
			}
		}
		return FALSE;
	}

	public function save_pr_album_img_desc($id, $album_id)
	{
		$POST = $this->input->post('product_album');
		$attr = $this->input->post('album_attributes');
		$attr_opt = $this->input->post('album_attributes_options');
		$POST[self::ID_PR_ATTRIBUTES] = NULL;
		$POST[self::ID_PR_ATTRIBUTES_OPTIONS] = NULL;
		unset($POST['desc']);
		if($attr && $attr_opt)
		{
			$this->db->select("COUNT(*) AS COUNT")
					->from("`".self::NATTRIBUTES."`")
					->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ATTRIBUTES."`", $attr)->where("`".self::ID_PR_ATTRIBUTES_OPTIONS."`", $attr_opt);
			$res = $this->db->get()->row_array();
			if($res['COUNT'] == 1)
			{
				$POST[self::ID_PR_ATTRIBUTES] = $attr;
				$POST[self::ID_PR_ATTRIBUTES_OPTIONS] = $attr_opt;
			}
			else
			{
				$POST[self::ID_PR_ATTRIBUTES] = NULL;
				$POST[self::ID_PR_ATTRIBUTES_OPTIONS] = NULL;
			}
		}
		$this->sql_add_data($POST)->sql_save(self::PR_ALB, $album_id);
		if($POST['type'] == 'TEXT')
		{
			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();

			$this->db->select("B.`".self::ID_PR_ALB_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_ALB."` AS A")
					->join(	"`".self::PR_ALB_DESC."` AS B",
							"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."`",
							"LEFT")
					->where("A.`".self::ID_PR_ALB."`", $album_id);
			foreach($this->db->get()->result_array() as $ms)
			{
				$qdata[$ms[self::ID_LANGS]] = $ms[self::ID_PR_ALB_DESC];
			}
			$POSTL = $this->input->post('product_album');
			$POSTL = $POSTL['desc'];
			foreach($langs as $l_key => $l_ms)
			{
				if(isset($POSTL[$l_key]))
				{
					if(isset($qdata[$l_key]))
					{
						$data = $POSTL[$l_key];
						$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC, $qdata[$l_key]);
					}
					else
					{
						$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_ALB => $album_id);
						$this->sql_add_data($data)->sql_save(self::PR_ALB_DESC);
					}
				}
			}
		}

		$this->save_pr_img_desc($id);
		return TRUE;
	}

	public function set_preview($id, $img_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR_IMG."`")
				->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_IMG."`", $img_id);
		$res = $this->db->get()->row_array();
		if($res['COUNT'] == 0) return FALSE;
		$this->db->where("`".self::ID_PR."`", $id)->update("`".self::PR_IMG."`", array('preview' => 0));
		$this->db->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_IMG."`", $img_id)->update("`".self::PR_IMG."`", array('preview' => 1));
		return TRUE;
	}

	public function set_album_preview($id, $img_id, $album_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::PR_IMG."`")
				->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_IMG."`", $img_id)->where("`".self::ID_PR_ALB."`", $album_id);
		$res = $this->db->get()->row_array();
		if($res['COUNT'] == 0) return FALSE;
		$this->db->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ALB."`", $album_id)->update("`".self::PR_IMG."`", array('album_preview' => 0));
		$this->db->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_IMG."`", $img_id)->update("`".self::PR_IMG."`", array('album_preview' => 1));
		return TRUE;
	}

	public function upload_pr_img($ID, $album_id = FALSE)
	{
		$config = $this->get_upload_img_config($ID, $album_id);

		$this->load->library('upload', $config);
		if($this->upload->do_upload('Filedata'))
		{
			$file_data = $this->upload->data();
			$this->crop_img($config['upload_path'].$file_data['file_name']);

			if($img_array = $this->save_pr_img($ID, $file_data, $album_id))
			{
				$img_id = $img_array[0];
				$img_desc = $img_array[1];
				$this->load->helper('catalogue/products_save_helper');
				$this->load->model('langs/mlangs');
				$data['on_langs'] = $this->mlangs->get_active_languages();

				$data['PID'] = $ID;
				$data['album_id'] = $album_id;
				$data['form_id'] = 'products_img_form';
				$data[self::ID_USERS] = $this->id_users;
				$data['id'] = $img_id;
				$data['values']['img_desc'] = $img_desc['img_desc'];
				$data['preview'] = $img_desc['preview'];
				if($album_id)
				{
					$data['timage'] = $this->img_path.$ID.'/'.$album_id.'/thumb_'.$file_data['file_name'];
					$data['bimage'] = $this->img_path.$ID.'/'.$album_id.'/'.$file_data['file_name'];
				}
				else
				{
					$data['timage'] = $this->img_path.$ID.'/thumb_'.$file_data['file_name'];
					$data['bimage'] = $this->img_path.$ID.'/'.$file_data['file_name'];
				}
				$data['ajax'] = TRUE;

				echo json_encode(array('id' => $img_id, 'html' => pr_img_desc_form($data), 'files' => [$data]));
				return TRUE;
			}
			return FALSE;
		}
		else
		{
			$this->upload->display_errors();
			return FALSE;
		}
	}

	protected function crop_img($img)
	{
		$this->load->model('mproducts_settings');
		$config = $this->mproducts_settings->get_image_settings();

		$Lconfig['source_image'] = $img;
		$Lconfig['width'] = $config['img_width_thumbs'];
		$Lconfig['height'] = $config['img_height_thumbs'];
		$Lconfig['create_thumb'] = TRUE;
		$Lconfig['quality'] = $config['img_quality'];

		$Bconfig['source_image'] = $img;
		$Bconfig['width'] = $config['img_width'];
		$Bconfig['height'] = $config['img_height'];
		$Bconfig['create_thumb'] = FALSE;
		$Bconfig['quality'] = $config['img_quality'];

		$this->load->library('image_lib', $Lconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		$this->image_lib->initialize($Bconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		if($config['img_wm'])
		{
			$Cconfig['quality'] = 			$config['img_quality'];
			$Cconfig['source_image'] = 		$img;
			$Cconfig['wm_vrt_alignment'] = 	$config['img_wm_valign'];
			$Cconfig['wm_hor_alignment'] = 	$config['img_wm_align'];
			$Cconfig['wm_opacity'] = 		$config['img_wm_opacity'];
			$Cconfig['wm_text'] = 			$config['img_wm_text'];
			$Cconfig['wm_font_size'] = 		$config['img_wm_text_size'];
			$Cconfig['wm_font_color'] = 	$config['img_wm_text_color'];
			$Cconfig['wm_shadow_color'] = 	$config['img_wm_text_shadow_color'];
			$Cconfig['wm_shadow_distance'] = $config['img_wm_text_shadow_padding'];
			$Cconfig['wm_font_path'] = 		BASE_PATH.'fonts/TIMCYRB.TTF';

			$Cconfig['wm_padding'] = 		0;
			$Cconfig['wm_hor_offset'] = 	1;
			$Cconfig['wm_vrt_offset'] = 	1;

			$this->image_lib->initialize($Cconfig);
			$this->image_lib->watermark();
			$this->image_lib->clear();
		}
	}

	public function delete_pr_img($id, $album_id = FALSE)
	{
		$query = $this->db->select("A.*")
				->from("`".self::PR_IMG."` AS A")
				->where("A.`".self::ID_PR_IMG."`", $id);
		if($album_id)
		{
			$query->where("A.`".self::ID_PR_ALB."`", $album_id);
		}
		$query = $query->get();
		if($query->num_rows() == 1)
		{
			$result = $query->row_array();
			$file = $result['image'];
			$p_id = $result[self::ID_PR];
			$config = $this->get_upload_img_config($p_id, $album_id);

			@unlink($config['upload_path'].$file);
			@unlink($config['upload_path'].'thumb_'.$file);
			$this->db->where(self::ID_PR_IMG, $id);
			$this->db->delete(self::PR_IMG);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function delete_pr_album($id, $album_id)
	{
		if(!$this->check_isset_pr_album($id, $album_id)) return FALSE;
		$this->load->helper('agfiles_helper');
		$path = BASE_PATH.'users/'.$this->id_users.self::IMG_FOLDER.$id.'/'.$album_id;
		remove_dir($path);

		$this->db->where("`".self::ID_PR."`", $id)->where("`".self::ID_PR_ALB."`", $album_id)->delete("`".self::PR_ALB."`");
		return TRUE;
	}

	public function change_pr_img_position($id, $pid, $type, $album_id = FALSE)
	{
		if($type == 'up' || $type == 'down')
		{
			switch($type)
			{
				case "up":
					if($c_id = $this->change_pr_img_position_query('<=', $id, $pid, $album_id))
					{
						return TRUE;
					}
					return FALSE;
				break;
				case "down":
					if($c_id = $this->change_pr_img_position_query('>=', $id, $pid, $album_id))
					{
						return TRUE;
					}
					return FALSE;
				break;
			}
		}
		return true;
	}

	private function change_pr_img_position_query($type, $id, $pid, $album_id = FALSE)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	->select("DISTINCT(A.`".self::ID_PR_IMG."`) AS ID, A.`sort` AS SORT, A.`".self::ID_PR."` AS PARENT")
							->from("`".self::PR_IMG."` AS A")
							->where("A.`".self::ID_PR."`", $pid)->where("A.`sort` ".$type." (SELECT `sort` FROM `".self::PR_IMG."` WHERE `".self::ID_PR_IMG."` = '".$id."')", NULL, FALSE)
							->order_by('SORT',$OB)->limit(2);
		if($album_id)
		{
			$query->where("A.`".self::ID_PR_ALB."`", $album_id);
		}

		$query = $query->get();

		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			if($result[0]['PARENT'] == $result[1]['PARENT'])
			{
				$ID = $result[0]['ID'];
				$SORT = $result[0]['SORT'];

				$id = $result[1]['ID'];
				$sort = $result[1]['SORT'];

				$this->db->trans_start();
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::PR_IMG, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::PR_IMG, $ID);
				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return TRUE;
				}
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}

	protected function get_upload_img_config($id, $album_id = FALSE)
	{
		$dir = BASE_PATH.'users/'.$this->id_users.'/media/catalogue/products/'.$id;
		if($album_id) $dir .= '/'.$album_id;
		if(!is_dir($dir))
		{
			$this->load->helper('agfiles_helper');
			create_dir($dir, 2);
		}
		$config['upload_path'] = BASE_PATH.'users/'.$this->id_users.'/media/catalogue/products/'.$id.'/';
		if($album_id) $config['upload_path'] .= $album_id.'/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['max_size']	= '11000';
		$config['encrypt_name'] = TRUE;

		return $config;
	}
	public function send_waitlist_message($pr_id) {
	  $res = $this->db->select('*')
	    ->from(self::M_U_WAIT)
	    ->where(self::ID_PR, $pr_id)
	     ->get()->result_array();

	  if(count($res) > 0) {

	   $this->load->model('users/musers');
	   $this->load->model('catalogue/mproducts');
	   $this->load->library('email');
	   $product = $this->mproducts->get_product($pr_id);
	   $user = $this->musers->get_user();
	   $product['site'] = $user['domain'];
	   $lang = $this->mlangs->get_language($this->mlangs->id_langs);

	   foreach($res as $cust) {
	    $config['protocol'] = 'sendmail';
	    $config['wordwrap'] = FALSE;
	    $config['mailtype'] = 'html';
	    $config['charset'] = 'utf-8';
	    $config['priority'] = 1;

	    $this->email->initialize($config);
	    $this->email->from('no-reply@'.$user['domain'], $user['domain']);
	    $this->email->to($cust['email']);
	    $this->email->subject('');
	    $this->email->message($this->load->view('catalogue/products/letters/'.$lang['language'].'/waitlist_mail', array('product' => $product), TRUE));
	    $this->email->send();
	    $this->email->clear();

	    $this->db->where(self::ID_PR, $pr_id)->where('`email`', $cust['email'])->delete(self::M_U_WAIT);
	   }
	  }
	 }
}
?>