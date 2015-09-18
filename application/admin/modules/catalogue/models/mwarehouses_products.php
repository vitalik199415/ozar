<?php
class Mwarehouses_products extends AG_Model
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
	
	const ID_PR_ATTRIBUTES 			= 'id_m_c_products_attributes';
	const ID_PR_ATTRIBUTES_OPTIONS 	= 'id_m_c_products_attributes_options';
	
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
	
	const IMG_FOLDER = '/media/catalogue/products/';
	private $img_path = FALSE;
	
	public $id_pr = FALSE;
	
	const save_flashdata = 'products_save_flashdata';
	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}
	
	public function render_pr_grid()
	{	
		$this->load->library('grid');
		$this->grid->_init_grid('products_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('catalogue/products_helper');
		helper_products_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
	}
	
	public function get_wh_pr_grid($wh_id)
	{
		$this->load->library('grid');
		$this->grid->_init_grid('wh_products_grid', array('limit' => 50, 'url' => set_url('*/*/actions/id/'.$wh_id)), TRUE);
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, C.`qty`")
			->from("`".self::WH_PR."` AS C")
			->join("`".self::PR."` AS A",
					"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("C.`".self::ID_WH."`",$wh_id);

		$this->load->helper('warehouses/warehouses_helper');
		helper_wh_products_grid_build($this->grid, $wh_id);
	
		$this->grid->create_grid_data();
		
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function render_pr_additionally_grid()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('products_additionally_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`new`, A.`bestseller`, A.`sale`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('catalogue/products_helper');
		helper_products_additionally_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('new', array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('bestseller', array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('sale', array('0'=>'Нет','1'=>'Да'));
		
		$this->grid->update_grid_data('in_stock', array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status', array('0'=>'Нет','1'=>'Да'));
		$this->grid->render_grid();
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
	
	protected function add_edit_base()
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
	
	protected function save_pr_validation($pr_id = FALSE)
	{
		if($pr_id)
		{
			if(!$this->check_isset_pr($pr_id)) return FALSE;
			$this->id_pr = $pr_id;
		}
		
		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_pr_sku', 'mwarehouses_products');
		$this->form_validation->add_callback_function_class('check_isset_pr_url', 'mwarehouses_products');
		$this->form_validation->add_callback_function_class('is_0_or_1', 'mwarehouses_products');
		
		$this->form_validation->set_rules('products[sku]', 'Артикул', 'trim|required|callback_check_isset_pr_sku');
		$this->form_validation->set_message('check_isset_pr_sku', 'Продукт с указанным артикулом уже существует!');
		$this->form_validation->set_rules('products[url_key]', 'Сегмент URL', 'trim|required|callback_check_isset_pr_url');
		$this->form_validation->set_message('check_isset_pr_url', 'Продукт с указанным сегментом URL уже существует!');
		
		$this->form_validation->set_rules('products[status]', 'Включен в поискс', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('products[in_stock]', 'В наличии', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('products[new]', 'Новинка', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('products[bestseller]', 'Хит продаж', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('products[sale]', 'Акция | Распродажа', 'required|callback_is_0_or_1');
		
		$this->form_validation->set_message('is_0_or_1', 'Не верное значение поля "%s"!');
		
		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }
		
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
	
	public function save_pr($id = FALSE)
	{ 
		if(!$this->input->post('products')) return FALSE;
		if($id)
		{
			$id = intval($id);
		}
		
		if(!$this->form_validation->run($id)) return FALSE;
		
		$this->db->trans_start();
		if($id)
		{
			$POST = $this->input->post('products');
			$this->save_product($POST, $id);
			
			$POST = $this->input->post('products_desc');
			$this->save_product_desc($POST, $id, TRUE);
			
			$POST = $this->input->post('products_price');
			$this->save_price($POST, $id, TRUE);
								
			$POST = array('products_types' => $this->input->post('products_types'), 'products_properties' => $this->input->post('products_properties'));
			$this->save_types($POST, $id, TRUE);
			
			$POST = array('products_attributes' => $this->input->post('products_attributes'), 'products_attributes_options' => $this->input->post('products_attributes_options'));
			$this->save_attributes($POST, $id, TRUE);
			
			$POST = $this->input->post('products_categories');
			$this->save_categories($POST, $id, TRUE);
			
		}
		else
		{
			$POST = $this->input->post('products');
			if($id = $this->save_product($POST))
			{
				$POST = $this->input->post('products_desc');
				$this->save_product_desc($POST, $id);
				
				if($this->input->post('products_price'))
				{
					$POST = $this->input->post('products_price');
					$this->save_price($POST, $id);
				}
				if($this->input->post('products_types'))
				{
					$POST =  array('products_types' => $this->input->post('products_types'), 'products_properties' => $this->input->post('products_properties'));
					$this->save_types($POST, $id);
				}
				if($this->input->post('products_attributes'))
				{
					$POST =  array('products_attributes' => $this->input->post('products_attributes'), 'products_attributes_options' => $this->input->post('products_attributes_options'));
					$this->save_attributes($POST, $id);
				}
				if($POST = $this->input->post('products_categories'))
				{
					$this->save_categories($POST, $id);
				}	
			}
			else
			{
				$this->set_post_to_session();
				return FALSE;
			}	
		}
		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			return $id;
		}
		else
		{
			$this->set_post_to_session();
			return FALSE;
		}
	}
	
	public function set_post_to_session()
	{
		$this->session->set_flashdata('products_add_edit_form', $this->input->post());
		return $this;
	}
	
	protected function save_product($POST, $id = FALSE)
	{
		if($id)
		{
			$this->sql_add_data($POST)->sql_update_date()->sql_using_user()->sql_save(self::PR, $id);
			$this->sql_add_data(array('new' => $POST['new']))->sql_save(self::PR_CAT, array(self::ID_PR => $id));
			return $id;
		}
		else
		{
			$id = $this->sql_add_data($POST)->sql_update_date()->sql_using_user()->sql_save(self::PR);
			$this->sql_add_data(array('sort' => $id))->sql_save(self::PR, $id);
			return $id;
		}
	}
	
	protected function save_product_desc($POST, $PID, $edit = FALSE)
	{
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();
		
		if($edit)
		{
			$query = $this->db
					->select("A.`".self::ID_PR."`, B.`".self::ID_PR_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR."` AS A")
					->join(	"`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."`",
							"left")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $query->get()->result_array();
			$products_desc_data = array();
			foreach($result as $ms)
			{
				$products_desc_data[$ms[self::ID_LANGS]] = $ms;
			}
			
			foreach($langs as $key => $ms)
			{
				if(isset($POST[$key]))
				{
					if(isset($POST[$key][self::ID_PR_DESC]) && isset($products_desc_data[$key]) && $products_desc_data[$key][self::ID_PR_DESC] == $POST[$key][self::ID_PR_DESC])
					{
						$data = $POST[$key];
						$this->sql_add_data($data)->sql_save(self::PR_DESC, $POST[$key][self::ID_PR_DESC]);
					}
					else if(!isset($products_desc_data[$key]))
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
	}
	
	protected function save_price($POST, $PID, $edit = FALSE)
	{	
		$this->load->model('langs/mlangs');
		$langs = $this->mlangs->get_active_languages();
		
		if($edit)
		{
			$query = $this->db->select("A.`".self::ID_PR_PRICE."` AS ID, B.`".self::ID_PR_PRICE_DESC."`, B.`".self::ID_LANGS."`")
					->from("`".self::PR_PRICE."` AS A")
					->join(	"`".self::PR_PRICE_DESC."` AS B",
							"B.`".self::ID_PR_PRICE."` = A.`".self::ID_PR_PRICE."`",
							"left")
					->where("A.`".self::ID_PR."`", $PID);
					
			$result = $query->get()->result_array();
			foreach($result as $ms)
			{
				$qdata['prices'][$ms['ID']] = $ms['ID'];
				$qdata['prices_lang'][$ms['ID']][$ms[self::ID_LANGS]] = $ms;
			}
			
			foreach($POST as $key => $ms)
			{
				if(isset($qdata['prices'][$ms[self::ID_PR_PRICE]]))
				{
					unset($qdata['prices'][$ms[self::ID_PR_PRICE]]);
					$data = $ms;
					$data['visible_rules'] = intval($data['visible_rules']);
					if($data['visible_rules'] == 2)
					{
						if(isset($data['m_u_types']) && is_array($data['m_u_types']))
						{
							$m_u_types_temp = $data['m_u_types'];
							unset($data['m_u_types']);
							$data['m_u_types'] = '';
							
							foreach($m_u_types_temp as $tkey => $tms)
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
					
					if($data['show_attributes'] == 2)
					{
						if(isset($data['id_attributes']))
						{
							if(is_array($data['id_attributes']))
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
					
					unset($data['desc']);
					unset($data[self::ID_PR_PRICE]);
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
					$this->sql_add_data($data)->sql_save(self::PR_PRICE, $ms[self::ID_PR_PRICE]);
					
					$POSTL = $ms['desc'];
					$PD = $qdata['prices_lang'][$ms[self::ID_PR_PRICE]];
					
					foreach($langs as $l_key => $l_ms)
					{
						if(isset($POSTL[$l_key]))
						{
							if(isset($POSTL[$l_key][self::ID_PR_PRICE_DESC]) && isset($PD[$l_key]) && $PD[$l_key][self::ID_PR_PRICE_DESC] == $POSTL[$l_key][self::ID_PR_PRICE_DESC])
							{
								$data = $POSTL[$l_key];
								unset($data[self::ID_PR_PRICE_DESC]);
								$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC, $POSTL[$l_key][self::ID_PR_PRICE_DESC]);
							}
							else if(!isset($PD[$l_key]))
							{
								$data = $POSTL[$l_key] + array(self::ID_LANGS => $l_key) + array(self::ID_PR_PRICE => $ms[self::ID_PR_PRICE]);
								$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC);
							}
						}
					}
				}
				else
				{
					$data = $ms;
					
					$data['visible_rules'] = intval($data['visible_rules']);
					if($data['visible_rules'] == 2)
					{
						if(isset($data['m_u_types']) && is_array($data['m_u_types']))
						{
							$m_u_types_temp = $data['m_u_types'];
							unset($data['m_u_types']);
							$data['m_u_types'] = '';
							
							foreach($m_u_types_temp as $tkey => $tms)
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
					
					unset($data['desc']);
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
					$ID = $this->sql_add_data($data + array(self::ID_PR => $PID))->sql_save(self::PR_PRICE);
					if($ID)
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
			}
			if($del_array)
			{
				$this->db->where_in(self::ID_PR_PRICE, $del_array);  
				$this->db->delete(self::PR_PRICE);
			}
			//---------------------------------
		}
		else
		{
			foreach($POST as $key => $ms)
			{
				$data = $ms;
				unset($data['desc']);
				
				$data['visible_rules'] = intval($data['visible_rules']);
				if($data['visible_rules'] == 2)
				{
					if(isset($data['m_u_types']) && is_array($data['m_u_types']))
					{
						$m_u_types_temp = $data['m_u_types'];
						unset($data['m_u_types']);
						$data['m_u_types'] = '';
						
						foreach($m_u_types_temp as $tkey => $tms)
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
				$ID = $this->sql_add_data($data+array(self::ID_PR => $PID))->sql_save(self::PR_PRICE);
				if($ID && $ID > 0)
				{
					foreach($ms['desc'] as $l_key => $l_ms)
					{
						if(isset($langs[$l_key]))
						{
							$data = $l_ms + array(self::ID_LANGS => $l_key) + array(self::ID_PR_PRICE => $ID);
							$this->sql_add_data($data)->sql_save(self::PR_PRICE_DESC); 
						}
					}
				}
			}
		}	
	}
	
	protected function save_types($POST, $PID, $edit = FALSE)
	{
		if($edit)
		{
			if($POST['products_types'] == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::NTYPES);
				return TRUE;
			}
			$query = $this->db->select("A.*")
					->from("`".self::NTYPES."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $query->get()->result_array();
			foreach($result as $ms)
			{
				if($ms[self::ID_PR_PROPERTIES] == NULL)
				{
					$tdata['products_types'][$ms[self::ID_PR_TYPES]] = $ms['id_m_c_productsNtypes'];
				}
				else
				{
					$tdata['products_properties'][$ms[self::ID_PR_TYPES]][$ms[self::ID_PR_PROPERTIES]] = $ms['id_m_c_productsNtypes'];
				}
			}
			
			foreach($POST['products_types'] as $ms)
			{
				if(isset($tdata['products_types'][$ms]))
				{
					unset($tdata['products_types'][$ms]);
				}
				else
				{
					$data = array(self::ID_PR_TYPES => $ms, self::ID_PR_PROPERTIES => NULL, self::ID_PR => $PID);
					$this->sql_add_data($data)->sql_save(self::NTYPES);
				}
				if(isset($POST['products_properties'][$ms]))
				{
					foreach($POST['products_properties'][$ms] as $pr)
					{
						if(isset($tdata['products_properties'][$ms][$pr]))
						{
							unset($tdata['products_properties'][$ms][$pr]);
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
			$del_array = FALSE;
			if(isset($tdata['products_types']))
			{
				foreach($tdata['products_types'] as $key => $ms)
				{
					$del_array[] = $ms;
					if(isset($tdata['products_properties'][$key]))
					{
						foreach($tdata['products_properties'][$key] as $pr)
						{
							$del_array[] = $pr;
						}
					}
				}
			}
			if(isset($tdata['products_properties']))
			{
				foreach($tdata['products_properties'] as $key => $pr)
				{
					if(is_array($pr))
					{
						foreach($pr as $ms)
						{
							$del_array[] = $ms;
						}
					}
				}
			}
			if($del_array)
			{
				$this->db->where_in('id_m_c_productsNtypes', $del_array);  
				$this->db->delete(self::NTYPES);
			}	
			//------------------------------------
		}
		else
		{
			if($POST['products_types'] == FALSE) return FALSE;
			foreach($POST['products_types'] as $ms)
			{
				$data = array(self::ID_PR_TYPES => $ms, self::ID_PR_PROPERTIES => NULL, self::ID_PR => $PID);
				$this->sql_add_data($data)->sql_save(self::NTYPES);
				
				if(isset($POST['products_properties'][$ms]))
				{
					foreach($POST['products_properties'][$ms] as $pr)
					{
						$data = array(self::ID_PR_TYPES => $ms, self::ID_PR_PROPERTIES => $pr, self::ID_PR => $PID);
						$this->sql_add_data($data)->sql_save(self::NTYPES);
					}
				}
			}
		}	
	}
	
	protected function save_attributes($POST, $PID, $edit = FALSE)
	{
		if($edit)
		{
			if($POST == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::NATTRIBUTES);
				return TRUE;
			}
			$query = $this->db->select("A.*")
					->from("`".self::NATTRIBUTES."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $query->get()->result_array();
			foreach($result as $ms)
			{
				if($ms[self::ID_PR_ATTRIBUTES] == NULL)
				{
					$tdata['products_attributes'][$ms[self::ID_PR_ATTRIBUTES]] = $ms['id_m_c_productsNattributes'];
				}
				else
				{
					$tdata['products_attributes_options'][$ms[self::ID_PR_ATTRIBUTES]][$ms[self::ID_PR_ATTRIBUTES_OPTIONS]] = $ms['id_m_c_productsNattributes'];
				}
			}
			
			foreach($POST['products_attributes'] as $ms)
			{
				if(isset($tdata['products_attributes'][$ms]))
				{
					unset($tdata['products_attributes'][$ms]);
				}
				else
				{
					$data = array(self::ID_PR_ATTRIBUTES => $ms, self::ID_PR_ATTRIBUTES_OPTIONS => NULL, self::ID_PR => $PID);
					$this->sql_add_data($data)->sql_save(self::NATTRIBUTES);
				}
				if(isset($POST['products_attributes_options'][$ms]))
				{
					foreach($POST['products_attributes_options'][$ms] as $pr)
					{
						if(isset($tdata['products_attributes_options'][$ms][$pr]))
						{
							unset($tdata['products_attributes_options'][$ms][$pr]);
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
			$del_array = FALSE;
			if(isset($tdata['products_attributes']))
			{
				foreach($tdata['products_attributes'] as $key => $ms)
				{
					$del_array[] = $ms;
					if(isset($tdata['products_attributes_options'][$key]))
					{
						foreach($tdata['products_attributes_options'][$key] as $pr)
						{
							$del_array[] = $pr;
						}
					}
				}
			}
			if(isset($tdata['products_attributes_options']))
			{
				foreach($tdata['products_attributes_options'] as $key => $pr)
				{
					if(is_array($pr))
					{
						foreach($pr as $ms)
						{
							$del_array[] = $ms;
						}
					}
				}
			}
			if($del_array)
			{
				$this->db->where_in('id_m_c_productsNattributes', $del_array);  
				$this->db->delete(self::NATTRIBUTES);
			}	
			//------------------------------------
		}
		else
		{
			if($POST['products_attributes'] == FALSE) return FALSE;
			foreach($POST['products_attributes'] as $ms)
			{
				$data = array(self::ID_PR_ATTRIBUTES => $ms, self::ID_PR_ATTRIBUTES_OPTIONS => NULL, self::ID_PR => $PID);
				$this->sql_add_data($data)->sql_save(self::NATTRIBUTES);
				
				if(isset($POST['products_attributes_options'][$ms]))
				{
					foreach($POST['products_attributes_options'][$ms] as $pr)
					{
						$data = array(self::ID_PR_ATTRIBUTES => $ms, self::ID_PR_ATTRIBUTES_OPTIONS => $pr, self::ID_PR => $PID);
						$this->sql_add_data($data)->sql_save(self::NATTRIBUTES);
					}
				}
			}
		}	
	}
	
	protected function save_categories($POST, $PID, $edit = FALSE)
	{
		if($edit)
		{
			if($POST == FALSE)
			{
				$this->db->where("`".self::ID_PR."`", $PID)->delete(self::PR_CAT);
				return TRUE;
			}
			$query = $this->db->select("A.`".self::ID_PR_CAT."` AS ID, A.`".self::ID_CAT."` AS CID")
					->from("`".self::PR_CAT."` AS A")
					->where("A.`".self::ID_PR."`", $PID);
			$result = $query->get()->result_array();
			$tdata['products_categories'] = array();
			foreach($result as $ms)
			{
					$tdata['products_categories'][$ms['CID']] = $ms['ID'];
			}
			
			foreach($POST as $ms)
			{
				if(isset($tdata['products_categories'][$ms]))
				{
					unset($tdata['products_categories'][$ms]);
				}
				else
				{
					$data = array(self::ID_CAT => $ms, self::ID_PR => $PID, 'sort' => $PID);
					$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_CAT);
				}
			}
			//DELETE
			$del_array = FALSE;
			if(isset($tdata['products_categories']))
			{
				foreach($tdata['products_categories'] as $key => $ms)
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
		}
		else
		{
			if($POST == FALSE) return FALSE;
			foreach($POST as $ms)
			{
				$data = array(self::ID_CAT => $ms, self::ID_PR => $PID, 'sort' => $PID);
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_CAT);
			}
		}
		
		$POST = $this->input->post('products');
		if(isset($POST['new']))
		{
			$this->sql_add_data(array('new' => $POST['new']))->sql_save(self::PR_CAT, array(self::ID_PR => $PID));
		}
	}
}
?>