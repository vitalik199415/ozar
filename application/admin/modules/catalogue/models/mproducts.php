<?php
class Mproducts extends AG_Model
{
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

	const ATTR 			= 'm_c_products_attributes';
	const ID_ATTR 		= 'id_m_c_products_attributes';
	const ATTR_DESC 	= 'm_c_products_attributes_description';
	const OP 			= 'm_c_products_attributes_options';
	const ID_OP 		= 'id_m_c_products_attributes_options';
	const OP_DESC 		= 'm_c_products_attributes_options_description';
	const PNA 			= 'm_c_productsNattributes';

	const CAT = 'm_c_categories';
	const ID_CAT = 'id_m_c_categories';

	const PR_CAT = 'm_c_productsNcategories';
	const ID_PR_CAT = 'id_m_c_productsNcategories';

	const CUR = 'm_c_currency';
	const ID_CUR = 'id_m_c_currency';
	const UCUR = 'm_c_users_currency';

	const IMG_FOLDER = '/media/catalogue/products/';
	private $img_path = FALSE;

	public $id_product = FALSE;

	const save_flashdata = 'products_save_flashdata';
	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}
	public function render_product_grid()
	{
		$this->load->model("sys/madmins");
		$cat_perm = $this->madmins->get_cat_perm();

		$this->load->library('grid');
		$this->grid->_init_grid('products_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_CAT."` AS PR_CAT",
				"PR_CAT.`".self::ID_PR."` = A.`".self::ID_PR."`",
				"LEFT")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		if($cat_perm)
			$this->grid->db->where_in("PR_CAT.`".self::ID_CAT."`",$cat_perm);
		$this->load->helper('catalogue/products_helper');
		helper_products_grid_build($this->grid);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		$this->grid->render_grid();
	}

	public function render_product_additionally_grid()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('products_additionally_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`new`, A.`bestseller`, A.`sale`, A.`action`, A.`different_colors`, A.`super_price`, A.`restricted_party`, A.`customised_product`")
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
        $this->grid->update_grid_data('action', array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('different_colors', array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('super_price', array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('restricted_party', array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('customised_product', array('0'=>'Нет','1'=>'Да'));
		$this->grid->render_grid();
	}

	public function get_product($pr_id)
	{
		$query = $this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`sku`, A.`url_key`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, B.`name`, B.`full_description`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		if(is_array($pr_id))
		{
			$query->where_in("A.`".self::ID_PR."`", $pr_id);
			return $query->get()->result_array();
		}
		else
		{
			$query->where("A.`".self::ID_PR."`", $pr_id)->limit(1);
			return $query->get()->row_array();
		}
	}

	public function prepare_products_grid_query($grid_name = 'products_grid')
	{
		$this->load->library('grid');
		$this->grid->_init_grid($grid_name);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);

		return $this->grid;
	}

	public function get_view_product($id, $options = array())
	{
		$array = array();
		$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, B.`name`, B.`full_description`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_PR."`", $id)->limit(1);
		$product = $query->get()->row_array();
		if(count($product)>0)
		{
			$ID = $product['ID'];
			$array['product'] = $product + $options;
			$array += $this->get_view_product_prices($ID, $options);
			$array['images'] = $this->get_view_product_images($ID);
			return $array;
		}
		return FALSE;
	}

	protected function get_view_product_prices_query($pr_id)
	{
		$query = $this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS ID_PRICE, PRICE.`price`, PRICE.`special_price`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`visible_rules` AS price_visible_rules, PRICE.`m_u_types` AS price_m_u_types, PRICE.`show_attributes`, PRICE.`id_attributes`, PRICE.`alias` AS price_alias, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS price_description")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join(	"`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->where("PRICE.`".self::ID_PR."`", $pr_id)->order_by("PRICE.`".self::ID_PR_PRICE."`");
		return $query;
	}

	public function get_view_product_prices($pr_id, $options = array())
	{
		$query = $this->get_view_product_prices_query($pr_id);
		if(isset($options['currency_rate']))
		{
			$query->select("(PRICE.`price` * ".$options['currency_rate'].") AS price_currency, (PRICE.`special_price` * ".$options['currency_rate'].") AS special_price_currency, ('".$options['currency_name']."') AS currency_name");
		}
		else
		{
			$query->select("(PRICE.`price` * CURRENCY.`rate`) AS price_currency, (PRICE.`special_price` * CURRENCY.`rate`) AS special_price_currency, CURRENCY.`name` AS currency_name")
				->join( "`".self::UCUR."` AS CURRENCY",
						"CURRENCY.`".self::ID_USERS."` = '".$this->id_users."' && CURRENCY.`default` = '1'",
						"LEFT");
		}
		$temp_array = array();
		$today = mktime();
		$first_price = TRUE;
		foreach($query->get()->result_array() as $ms)
		{
			$ms['price_visible'] = TRUE;

			$ms['price_attributes_js'] = "";
			if($ms['price_visible'])
			{
				$ms['price_attributes_js'] = "
				price_attributes[".$ms['ID_PRICE']."] = {};
				price_attributes[".$ms['ID_PRICE']."]['show_attributes'] = '".$ms['show_attributes']."';
				";
				if($ms['show_attributes'] == 2)
				{
					$ms['price_attributes_js'] .= "
					price_attributes[".$ms['ID_PRICE']."]['id_attributes'] = {};
					";
					foreach(explode(',', $ms['id_attributes']) as $ms1)
					{
						if($ms1 != '')
						{
							$ms['price_attributes_js'] .= "
							price_attributes[".$ms['ID_PRICE']."]['id_attributes'][".$ms1."] = '".$ms1."';
							";
						}
					}
				}
			}
			$temp_array[$ms['ID_PRICE']] = $ms;
			if($first_price)
			{
				$array['attributes'] = $this->get_view_product_attributes_and_options($pr_id, $ms);
				$first_price = FALSE;
			}
		}
		$array['prices_array'] = $temp_array;
		$array['prices'] = $this->generate_price_html($temp_array);
		return $array;
	}

	public function get_product_price($pr_id, $price_id, $currency_array = FALSE)
	{
		$query = $this->get_view_product_prices_query($pr_id);

		if(is_array($currency_array))
		{
			$query->select("(PRICE.`price` * ".$currency_array['currency_rate'].") AS price_currency, (PRICE.`special_price` * ".$currency_array['currency_rate'].") AS special_price_currency, A.`real_qty`, ('".$currency_array['currency_name']."') AS currency_name");
		}
		else
		{
			$query->select("(PRICE.`price` * CURRENCY.`rate`) AS price_currency, (PRICE.`special_price` * CURRENCY.`rate`) AS special_price_currency, A.`real_qty`, CURRENCY.`name` AS currency_name")
				->join( "`".self::UCUR."` AS CURRENCY",
						"CURRENCY.`".self::ID_USERS."` = '".$this->id_users."' && CURRENCY.`default` = '1'",
						"LEFT");
		}

		$query->where("PRICE.`".self::ID_PR_PRICE."`", $price_id)->limit(1);
		$price = $query->get()->row_array();
		if(count($price)>0)
		{
			$special_price = $price['special_price'];
			$special_price_currency = $price['special_price_currency'];

			$price['original_price'] = $price['price'];
			$price['original_price_currency'] = $price['price_currency'];

			$price['special_price'] = FALSE;
			$price['special_price_currency'] = FALSE;

			if((float) $special_price > 0)
			{
				$today = mktime();
				$special_price_to_date = FALSE;
				if($price['special_price_from'] != NULL)
				{
					$spfrom = explode("-", $price['special_price_from']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					if($today < $spfrom)
					{
						$special_price = FALSE;
					}
				}
				if($price['special_price_to'] != NULL)
				{
					$spfrom = explode("-", $price['special_price_to']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					$special_price_to_date = $price['special_price_to'];
					if($today > $spfrom)
					{
						$special_price = FALSE;
						$special_price_to_date = FALSE;
					}
				}
			}
			else
			{
				$special_price = FALSE;
			}

			if($special_price)
			{
				$price['price'] = $special_price;
				$price['price_currency'] = $special_price_currency;
			}
			return $price;
		}
		return FALSE;
	}

	public function get_view_product_images($pr_id)
	{
		$array = array();
		$query = $this->db
				->select("A.`image`, A.`".self::ID_PR."` AS ID, B.`name` AS image_name, B.`title` AS image_title, B.`alt` AS image_alt")
				->from("`".self::PR_IMG."` AS A")
				->join(	"`".self::PR_IMG_DESC."` AS B",
						"B.`".self::ID_PR_IMG."` = A.`".self::ID_PR_IMG."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"left")
				->where("A.`".self::ID_PR."`", $pr_id)->order_by("A.`sort`");
		$temp = $query->get()->result_array();
		foreach($temp as $ms)
		{
			$array[] = $ms + array('timage' => $this->img_path.$ms['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['image']);
		}
		return $array;
	}

	public function get_view_product_attributes_and_options($pr_id, $first_price = FALSE)
	{
		$query = $this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($pr_id))
		{
			$IN = '';
			foreach($pr_id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
			unset($IN);
		}
		else if(intval($pr_id)>0)
		{
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".intval($pr_id)."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
		}
		$query
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']][$ms['ID_OP']] = $ms;
		}
		unset($result);
		if($first_price !== FALSE)
		{
			$attributes_html = $this->generate_attributes_html($array, $first_price);
			return $attributes_html;
		}
		else
		{
			return $array;
		}
	}

	protected function generate_price_html($price_array)
	{
		$str = '<script>var price_attributes = {};</script>';
		if(count($price_array) > 0)
		{
			$str .= '<div class="price_block">';
			if(count($price_array)>1) $str .= '<div class="select_price">Выбор варианта</div>';
			$hidden_radio = '';

			if(count($price_array) == 1)
			{
				$hidden_radio = ' hidden';
			}
			$first_price = TRUE;
			foreach($price_array as $ms)
			{
				if($ms['price_visible'])
				{
					$hidden_radio_m = $hidden_radio;

					$str .= '<div class="price"><input type="radio" class="price_select '.$hidden_radio_m.'" name="price_id"';
					if($first_price)
					{
						$str .= 'checked="checked" ';
						$first_price = FALSE;
					}
					$str .= 'value="'.$ms['ID_PRICE'].'" />';

					if($ms['price_name'] != '')
					{
						$str .= $ms['price_name'].' ';
					}
					else
					{
						$str .= 'Цена'.': ';
					}

					$special_price = $ms['special_price_currency'];
					if((float) $special_price > 0)
					{
						$today = mktime();
						$special_price_to_date = FALSE;
						if($ms['special_price_from'] != NULL)
						{
							$spfrom = explode("-", $ms['special_price_from']);
							$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
							if($today < $spfrom)
							{
								$special_price = FALSE;
							}
						}
						if($ms['special_price_to'] != NULL)
						{
							$spfrom = explode("-", $ms['special_price_to']);
							$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
							$special_price_to_date = $ms['special_price_to'];
							if($today > $spfrom)
							{
								$special_price = FALSE;
								$special_price_to_date = FALSE;
							}
						}
					}
					else
					{
						$special_price = FALSE;
					}

					if($special_price)
					{
						$str .= '<span class="special_price_s"><s>'.number_format($ms['price_currency'], 2, ',', ' ').'</s></span>&nbsp<span class="special_price">'.number_format($special_price, 2, ',', ' ').' '.$ms['currency_name'].'</span>';
						if($special_price_to_date)
						{
							$str .= ' <span class="special_price_to_date">'.' до '.$special_price_to_date.'</span>';
						}
					}
					else
					{
						$str .= '<span>'.number_format($ms['price_currency'], 2, ',', ' ').' '.$ms['currency_name'].'</span>';
					}
				}
				else
				{
					$hidden_radio_m = ' visibility_hidden';


					$str .= '<div class="price"><input type="radio" class="price_select'.$hidden_radio_m.'" name="price_id" ';
					/*if($first_price)
					{
						$str .= 'checked="checked" ';
						$first_price = FALSE;
					}*/
					$str .= 'value="'.$ms['ID_PRICE'].'" />';

					if($ms['price_name'] != '')
					{
						$str .= $ms['price_name'].' ';
					}
					else
					{
						$str .= 'Цена '.': ';
					}
				}
				$str .= '</div>';
				if($ms['price_attributes_js'] != '')
				{
					$str .= '<script>
						'.$ms['price_attributes_js'].'
					</script>';
				}
			}
			$str .= '</div>';
		}
		return $str;
	}

	protected function generate_attributes_html($attributes, $price)
	{
		$str = '';
		if(count($attributes)>0)
		{
			$str .= '<div class="attributes_block">
			<table cellspacing="0" cellpadding="0" border="0">';
			$show_attributes = $price['show_attributes'];
			$show_id_attributes = explode(',', $price['id_attributes']);

			foreach($attributes as $ms)
			{
				$select_array = array();
				foreach($ms as $op)
				{
					$select_text = $op['a_name'];
					$select_name = 'attributes_id['.$op['ID'].']';
					$select_id = $op['ID'];
					$select_array[$op['ID_OP']] = $op['o_name'];
				}

				if(($show_attributes == 0) || ($show_attributes == 2 && !in_array($select_id, $show_id_attributes)))
				{
					$class = 'class="hidden"';
				}
				else if(($show_attributes == 1) || ($show_attributes == 2 && in_array($select_id, $show_id_attributes)))
				{
					$class = '';
				}
				$str .= '<tr '.$class.'><td width="1" valign="middle"><div class="attributes_name" rel="'.$select_id.'"><pre>'.$select_text.':</pre></div></td><td valign="middle"><div class="attributes_select" rel="'.$select_id.'">'.form_dropdown($select_name, $select_array, '', 'rel="'.$select_id.'" class="select_attributes"').'</div></td></tr>';
			}
			$str .= '</table></div>';
		}
		return $str;
	}










	public function view($id)
	{
		$array = $this->edit($id, TRUE);
		$array['img'] = $this->get_img($id);
		$array['id_users'] = $this->id_users;
		$this->load->helper('catalogue/products_helper');
		products_view($array);
	}

	public function get_product_view_dada($id)
	{
		$array = array();
		$query = $this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`status`, A.`in_stock`, B.`name`, B.`full_description`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"inner")
			->where("A.`".self::ID_PR."`", $id)
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();

		$array['products'] = $result;



		$query = $this->db
			->select("A.`id_m_c_currency` AS ID, A.`rate`, B.`name`")
			->from("`".self::UCUR."` AS A")
			->join(	"`".self::CUR."` AS B",
					"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
					"inner")
			->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("ID", "DESC");
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$array['currency']['name'][$ms['ID']] = $ms['name'];
			$array['currency']['rate'][$ms['ID']] = array($ms['rate'], $ms['name']);
		}

		$array['price'] = $this->get_product_prices($id);

		$array += $this->get_img($id);
		return $array;
	}

	protected function get_product_prices($id)
	{
		$query = $this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS ID_PRICE, PRICE.`price`, PRICE.`special_price`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`real_qty`, PRICE.`show_attributes`, PRICE.`id_attributes`, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS price_description, CURRENCY.`name` AS currency_name")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join(	"`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT")
				->join( "`".self::UCUR."` AS CURRENCY",
						"CURRENCY.`".self::ID_USERS."` = '".$this->id_users."' && CURRENCY.`default_selected` = 1",
						"LEFT")
				->where("PRICE.`".self::ID_PR."`", $id)->order_by("PRICE.`".self::ID_PR_PRICE."`")->limit(5);
		$array = array();
		$today = mktime();
		foreach($query->get()->result_array() as $ms)
		{
			if((int) $ms['special_price'] > 0)
			{
				$ms['special_price_to_date'] = FALSE;
				$special_price = $ms['special_price'];
				if($ms['special_price_from'] != NULL)
				{
					$spfrom = explode("-", $ms['special_price_from']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					if($today < $spfrom)
					{
						$ms['special_price'] = FALSE;
					}
				}
				if($ms['special_price_to'] != NULL)
				{
					$spfrom = explode("-", $ms['special_price_to']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					$ms['special_price_to_date'] = $ms['special_price_to'];
					if($today > $spfrom)
					{
						$ms['special_price'] = FALSE;
						$ms['special_price_to_date'] = FALSE;
					}
				}
			}
			else
			{
				$ms['special_price'] = FALSE;
			}

			$array[$ms['ID_PRICE']] = $ms;
		}
		return $array;
	}

	/*public function get_product_price($id, $id_price)
	{
		$query = $this->db
			->select("A.`price`, A.`special_price`, A.`special_price_from`, A.`special_price_to`, A.`show_attributes`, A.`id_attributes`")
			->from("`".self::PR_PRICE."` AS A")
			->where("A.`".self::ID_PR."`", $id)->where("A.`".self::ID_PR_PRICE."`", $id_price)->limit(1);
		$result = $query->get()->row_array();
		$today = mktime();
		if(isset($result['price']))
		{
			if((int) $result['special_price'] > 0)
			{
				$result['special_price_to_date'] = FALSE;
				$special_price = $result['special_price'];
				if($result['special_price_from'] != NULL)
				{
					$spfrom = explode("-", $result['special_price_from']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					if($today < $spfrom)
					{
						$result['special_price'] = FALSE;
					}
				}
				if($result['special_price_to'] != NULL)
				{
					$spfrom = explode("-", $result['special_price_to']);
					$spfrom = mktime(0, 0, 0, $spfrom[1], $spfrom[2], $spfrom[0]);
					$result['special_price_to_date'] = $result['special_price_to'];
					if($today > $spfrom)
					{
						$result['special_price'] = FALSE;
						$result['special_price_to_date'] = FALSE;
					}
				}
			}
			return $result;
		}
		return false;
	}*/

	public function get_products_with_prices($id_products, $id_price = FALSE)
	{
		$query = $this->db
			->select("A.`".self::ID_PR."` AS ID, C.`".self::ID_PR_PRICE."` AS IDP, A.`sku`, B.`name`, C.`price`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"inner");
		if(!$id_price)
		{
			$query	->join(	"`".self::PR_PRICE."` AS C",
							"C.`".self::ID_PR."` = A.`".self::ID_PR."`",
							"left")
					->join(	"`".self::PR_PRICE_DESC."` AS D",
							"D.`".self::ID_PR_PRICE."` = C.`".self::ID_PR_PRICE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
							"left");
		}
		else if(is_array($id_price))
		{
			$IN = '';
			foreach($id_price as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::PR_PRICE."` AS C",
							"C.`".self::ID_PR."` = A.`".self::ID_PR."` && C.`".self::ID_PR_PRICE."` IN (".$IN.")",
							"left")
					->join(	"`".self::PR_PRICE_DESC."` AS D",
							"D.`".self::ID_PR_PRICE."` = C.`".self::ID_PR_PRICE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
							"left");
			unset($IN);
		}
		else if(intval($id_price)>0)
		{
			$query	->join(	"`".self::PR_PRICE."` AS C",
							"C.`".self::ID_PR."` = A.`".self::ID_PR."` && C.`".self::ID_PR_PRICE."` = '".intval($id_price)."'",
							"left")
					->join(	"`".self::PR_PRICE_DESC."` AS D",
							"D.`".self::ID_PR_PRICE."` = C.`".self::ID_PR_PRICE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
							"left");
		}

		if(is_array($id_products))
		{
			$query->where_in("A.`".self::ID_PR."`", $id_products);
		}
		else if(intval($id_products)>0)
		{
			$query->where("A.`".self::ID_PR."`", intval($id_products));
		}
		$result = $query->get()->result_array();
		return $result;
	}

	public function get_product_attributes($pr_id)
	{
		$query = $this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($pr_id))
		{
			$IN = '';
			foreach($pr_id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
			unset($IN);
		}
		else if(intval($pr_id)>0)
		{
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".intval($pr_id)."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
		}
		$query
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();

		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']][$ms['ID_OP']] = $ms;
		}
		return $array;
	}

	public function check_isset_pr($id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")
					->from("`".self::PR."`")
					->where("`".self::ID_PR."`", $id)
					->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
}
?>