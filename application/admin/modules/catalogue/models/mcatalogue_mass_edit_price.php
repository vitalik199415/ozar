<?php
class Mcatalogue_mass_edit_price extends AG_Model
{
	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';
	const CAT_LINK			= 'm_c_categories_link';
	
	const PR 	= 'm_c_products';
	const ID_PR = 'id_m_c_products';
	const PR_DESC = 'm_c_products_description';
	const PR_PRICE 	= 'm_c_products_price';
	const ID_PR_PRICE = 'id_m_c_products_price';
	
	const PR_CAT = 'm_c_productsNcategories';
	const ID_PR_CAT = 'id_m_c_productsNcategories';
	
	private $tree_array = array();
	
	public $id_categorie = FALSE;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_categories_grid()
	{
		$this->load->helper('aggrid_tree_helper');
		$Grid = new Aggrid_tree_Helper('catalogue_categories_grid');
		
		$Grid->db	->select("A.`".self::ID_CAT."` AS ID, A.`id_parent`, A.`level`, A.`sort` AS sort, A.`active`, A.`create_date`, A.`update_date`, B.`name`, 
							(SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS PARENT_COUNT")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`",$this->id_users)->order_by('sort');
					
		$this->load->helper('catalogue/catalogue_mass_edit_price');
		
		helper_catalogue_mass_edit_price_grid_build($Grid);
		$Grid->createDataArray();
		$Grid->updateGridValues('active', array('0' => 'Нет', '1' => 'Да'));
		$Grid->renderGrid();
	}
	
	public function render_actions($cat_id)
	{
		if(($cat_id = intval($cat_id))>0)
		{
			if($this->check_isset_categorie($cat_id))
			{
				$this->db->select("B.`name`")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"LEFT")
					->where("A.`".self::ID_CAT."`", $cat_id)->limit(1);
				$result = $this->db->get()->row_array();
				
				$this->template->add_navigation($result['name']);
				$this->template->add_title(' | '.$result['name']);

				$data['products'] = $this->get_categories_products_grid($cat_id);
				
				helper_catalogue_mass_edit_price_action_form_build($cat_id, $data);
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_categories_products_grid($cat_id)
	{ 
		$this->load->library('nosql_grid');
		$this->nosql_grid->_init_grid('products_mass_edit_price_grid', array( 'limit' => '', 'url' => setUrl('*/catalogue_mass_edit_price/get_ajax_categories_products/cat_id/'.$cat_id)), TRUE);
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`sale`, PRICE.`price`, PRICE.`special_price`, PRICE.`special_price_from`, PRICE.`special_price_to`")
			->from("`".self::PR."` AS A")
			->join("`".self::PR_CAT."` AS C",
					"C.`".self::ID_CAT."` = '".$cat_id."' && C.`".self::ID_PR."` = A.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->join( "`".self::PR_PRICE."` AS PRICE",
						"PRICE.`".self::ID_PR."` = A.`".self::ID_PR."` && PRICE.`".self::ID_PR_PRICE."` = (SELECT `".self::ID_PR_PRICE."` FROM `".self::PR_PRICE."` WHERE `".self::ID_PR."` = A.`".self::ID_PR."` ORDER BY `".self::ID_PR_PRICE."` LIMIT 1)",
						"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->order_by("A.`".self::ID_PR."`");
		$result = $this->db->get()->result_array();
		$this->load->helper('catalogue/catalogue_mass_edit_price');
		helper_catalogue_mass_edit_price_categorie_products_grid_build($this->nosql_grid, $cat_id);
		$this->nosql_grid->set_grid_data($result);
		
		$this->nosql_grid->update_grid_data('in_stock', array('0' => 'Нет', '1' => 'Да'));
		$this->nosql_grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		$this->nosql_grid->update_grid_data('sale', array('0' => 'Нет', '1' => 'Да'));

		return $this->nosql_grid->render_grid(TRUE);
	}
	
	public function check_isset_categorie($cat_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::CAT."`")
				->where("`".self::ID_USERS."`",$this->id_users)
				->where("`".self::ID_CAT."`", $cat_id);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function save_changes()
	{
		if(($POST = $this->input->post('price_actions')) && ($PROD = $this->input->post('products_checkbox')))
		{
			$r = FALSE;
			switch($POST['type'])
			{
				case "percent_minus":
					$r = $this->percent_minus($POST, $PROD);
				break;
				case "percent_plus":
					$r = $this->percent_plus($POST, $PROD);
				break;
				case "price_minus":
					$r = $this->price_minus($POST, $PROD);
				break;
				case "price_plus":
					$r = $this->price_plus($POST, $PROD);
				break;
				case "cancel_sale":
					$r = $this->cancel_sale($PROD);
				break;
			}
			if($r)
			{
				$this->messages->add_success_message('Процедура прошла успешно!');
				return TRUE;
			}
			$this->messages->add_error_message('Не все поля заполнены верно, дейтсвие не воможно!');
			return FALSE;
		}
		$this->messages->add_error_message('Продукты не выбраны, действие не возможно!');
		return FALSE;
	}

	protected function percent_minus($data, $pr_id)
	{
		$data['percent_minus_value'] = floatval($data['percent_minus_value']);
		if($data['percent_minus_value'] > 0 && $data['percent_minus_value'] < 100)
		{
			if($data['percent_minus_price_options'] == 'sale_price')
			{
				$from_date = $data['percent_minus_special_price_from'] != '' ? "'".$data['percent_minus_special_price_from']."'" : 'NULL';
				$to_date = $data['percent_minus_special_price_to'] != '' ? "'".$data['percent_minus_special_price_to']."'" : 'NULL';

				$products = implode(',', $pr_id);

				$query = "UPDATE `".self::PR_PRICE."` SET
					`special_price` = (`price` - (`price` * ".$data['percent_minus_value']." / 100)),
					`special_price_from` = ".$from_date.",
					`special_price_to` = ".$to_date."
					WHERE `".self::ID_PR."` IN (".$products.")
				";
				$this->db->query($query);

				if($data['percent_minus_sale_sticker'] == 1)
				{
					$query = "UPDATE `".self::PR."` SET
						`sale` = 1
						WHERE `".self::ID_PR."` IN (".$products.")
					";
					$this->db->query($query);
				}
			}
			else
			{
				$products = implode(',', $pr_id);

				$query = "UPDATE `".self::PR_PRICE."` SET
					`price` = (`price` - (`price` * ".$data['percent_minus_value']." / 100))
					WHERE `".self::ID_PR."` IN (".$products.")
				";
				$this->db->query($query);
			}
			return TRUE;
		}
		return FALSE;
	}

	protected function percent_plus($data, $pr_id)
	{
		$data['percent_plus_value'] = floatval($data['percent_plus_value']);
		if($data['percent_plus_value'] > 0)
		{
			$products = implode(',', $pr_id);

			$query = "UPDATE `".self::PR_PRICE."` SET
				`price` = (`price` + (`price` * ".$data['percent_plus_value']." / 100))
				WHERE `".self::ID_PR."` IN (".$products.")
			";
			$this->db->query($query);
			return TRUE;
		}
		return FALSE;
	}

	protected function price_minus($data, $pr_id)
	{
		$data['price_minus_value'] = floatval($data['price_minus_value']);
		if($data['price_minus_value'] > 0)
		{
			if($data['price_minus_price_options'] == 'sale_price')
			{
				$from_date = $data['price_minus_special_price_from'] != '' ? "'".$data['price_minus_special_price_from']."'" : 'NULL';
				$to_date = $data['price_minus_special_price_to'] != '' ? "'".$data['price_minus_special_price_to']."'" : 'NULL';

				$products = implode(',', $pr_id);

				$query = "UPDATE `".self::PR_PRICE."` SET
					`special_price` = IF(`price` > ".$data['price_minus_value'].", `price` - ".$data['price_minus_value'].", NULL),
					`special_price_from` = IF(`price` > ".$data['price_minus_value'].", ".$from_date.", NULL),
					`special_price_to` = IF(`price` > ".$data['price_minus_value'].", ".$to_date.", NULL)
					WHERE `".self::ID_PR."` IN (".$products.")
				";
				$this->db->query($query);

				if($data['price_minus_sale_sticker'] == 1)
				{
					$query = "UPDATE `".self::PR."` SET
						`sale` = 1
						WHERE `".self::ID_PR."` IN (".$products.")
					";
					$this->db->query($query);
				}
			}
			else
			{
				$products = implode(',', $pr_id);

				$query = "UPDATE `".self::PR_PRICE."` SET
					`price` = IF(`price` > ".$data['price_minus_value'].", `price` - ".$data['price_minus_value'].", `price`)
					WHERE `".self::ID_PR."` IN (".$products.")
				";
				$this->db->query($query);
			}
			return TRUE;
		}
		return FALSE;
	}

	protected function price_plus($data, $pr_id)
	{
		$data['price_plus_value'] = floatval($data['price_plus_value']);
		if($data['price_plus_value'] > 0)
		{
			$products = implode(',', $pr_id);

			$query = "UPDATE `".self::PR_PRICE."` SET
				`price` = `price` + ".$data['price_plus_value']."
				WHERE `".self::ID_PR."` IN (".$products.")
			";
			$this->db->query($query);
			return TRUE;
		}
		return FALSE;
	}

	protected function cancel_sale($pr_id)
	{
		$products = implode(',', $pr_id);

		$query = "UPDATE `".self::PR_PRICE."` SET
			`special_price` = NULL,
			`special_price_from` = NULL,
			`special_price_to` = NULL
			WHERE `".self::ID_PR."` IN (".$products.")
		";
		$this->db->query($query);

		$query = "UPDATE `".self::PR."` SET
			`sale` = 0
			WHERE `".self::ID_PR."` IN (".$products.")
		";
		$this->db->query($query);
		return TRUE;
	}
}	
?>