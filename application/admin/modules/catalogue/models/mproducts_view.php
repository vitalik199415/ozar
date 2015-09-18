<?php
class Mproducts_view extends AG_Model
{
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';

	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';

	const PR_IMG 			= 'm_c_products_images';
	const ID_PR_IMG 		= 'id_m_c_products_images';
	const PR_IMG_DESC 		= 'm_c_products_images_description';

	const PR_ALB 			= 'm_c_products_albums';
	const ID_PR_ALB 		= 'id_m_c_products_albums';
	const PR_ALB_DESC 		= 'm_c_products_albums_description';

	const PR_SETTINGS = 'm_c_products_settings';

	const CAT 		= 'm_c_categories';
	const ID_CAT 	= 'id_m_c_categories';
	const CAT_DESC 		= 'm_c_categories_description';

	const PR_CAT 		= 'm_c_productsNcategories';
	const ID_PR_CAT 	= 'id_m_c_productsNcategories';
	const CAT_LINK 		= 'm_c_categories_link';

	const CUR 		= 'm_c_currency';
	const ID_CUR 	= 'id_m_c_currency';
	const UCUR 		= 'm_c_users_currency';

	const ATTR 			= 'm_c_products_attributes';
	const ID_ATTR 		= 'id_m_c_products_attributes';
	const ATTR_DESC 	= 'm_c_products_attributes_description';
	const OP 			= 'm_c_products_attributes_options';
	const ID_OP 		= 'id_m_c_products_attributes_options';
	const OP_DESC 		= 'm_c_products_attributes_options_description';
	const PNA = 'm_c_productsNattributes';
	const PNT = 'm_c_productsNtypes';

	const PR_REL		= 'm_c_products_related';
	const ID_PR_REL		= 'id_m_c_products_related';

	const PR_SIM		= 'm_c_products_similar';
	const ID_PR_SIM		= 'id_m_c_products_similar';

	const PR_COMM 		= 'm_c_products_comments';
	const ID_PR_COMM 	= 'id_m_c_products_comments';

	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	const WH_PR 	= 'wh_products';
	const ID_WH_PR 	= 'id_wh_products';

	const IMG_FOLDER = '/media/catalogue/products/';
	private $img_path = FALSE;

	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}

	public function get_product($id)
	{
		$product_array = $this->build_products_detail_array($id);
		return $product_array;
	}

	protected function build_products_detail_array($id)
	{
		if($product = $this->build_product_detail_data($id))
		{
			$product_array['product'] = $product;
			$pr_id = $product['ID'];
			$product_array += $this->build_product_detail_images_array($pr_id);
			$product_array += $this->build_product_detail_prices_array($pr_id);
			$product_array += $this->build_product_detail_attributes_array($pr_id, $product_array['selected_price']);
			/*if($this->settings['related_on'] == 1) $product_array['related_products'] = $this->get_product_detail_related_products($pr_id);
			if($this->settings['similar_on'] == 1) $product_array['similar_products'] = $this->get_product_detail_similar_products($pr_id);
			if($this->settings['reviews_on'] == 1) $product_array['comments_array'] = $this->get_product_detail_comments($pr_id);*/
			//$product_array['product_settings'] = $this->settings;

			//$this->build_product_detail_navigation($pr_id, $product_array['product']['name']);

			return $product_array;
		}
		return FALSE;
	}

	protected function build_product_detail_data($id)
	{
		$product_array = array();
		$query = $this->get_product_detail_query($id);
		$product_array = $query->get()->row_array();
		if(count($product_array) == 0) return FALSE;
		return $product_array;
	}

	protected function get_product_detail_query($id)
	{
		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if($wh_id)
		{
			$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`bestseller`, A.`sale`, A.`new`, B.`name`, B.`short_description`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`,
						IF(A.`in_stock` > 0, IF(WHP.`qty` > 0, 1, 0), 0) AS `in_stock`")
				->from("`".self::PR."` AS A")
				->join( "`".self::WH_PR."` AS WHP",
						"WHP.`".self::ID_PR."` = A.`".self::ID_PR."` && WHP.`".self::ID_WH."` = ".$wh_id,
						"LEFT")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_PR."`", $id)->limit(1);
			return $query;
		}
		$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, B.`name`, B.`short_description`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_PR."`", $id)->limit(1);
		return $query;
	}

	protected function build_product_detail_images_array($product_id)
	{
		$albums_array = array();
		$query = $this->get_product_detail_albums_query($product_id);
		$album_temp_array = $query->get()->result_array();
		foreach($album_temp_array as $ms)
		{
			$albums_array[$ms['ALBUM_ID']] = $ms;
		}

		$images_array = array();
		$images_in_album_array = array();
		$query = $this->get_product_detail_images_query($product_id);
		$temp_array = $query->get()->result_array();
		if(count($albums_array)>0)
		{
			foreach($temp_array as $ms)
			{
				$images_in_album_array[$ms['ALBUM_ID']][] = $ms + array('timage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/'.$ms['image']);
				$images_array[] = $ms + array('timage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['ALBUM_ID'].'/'.$ms['image']);
			}
		}
		else
		{
			foreach($temp_array as $ms)
			{
				$images_array[] = $ms + array('timage' => $this->img_path.$ms['ID'].'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].'/'.$ms['image']);
			}
		}
		return array('images_array' => $images_array, 'albums_array' => $albums_array, 'images_in_album_array' => $images_in_album_array);
	}

	protected function get_product_detail_images_query($product_id)
	{
		$query = $this->db
				->select("A.`image`, A.`".self::ID_PR_ALB."` AS ALBUM_ID, A.`".self::ID_PR."` AS ID, B.`name` AS image_name, B.`title` AS image_title, B.`alt` AS image_alt")
				->from("`".self::PR_IMG."` AS A")
				->join(	"`".self::PR_IMG_DESC."` AS B",
						"B.`".self::ID_PR_IMG."` = A.`".self::ID_PR_IMG."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				->where("A.`".self::ID_PR."`", $product_id)->order_by("A.`sort`");
		return $query;
	}

	protected function get_product_detail_albums_query($product_id)
	{
		$query = $this->db->select("A.`".self::ID_PR_ALB."` AS ALBUM_ID, A.`type`, A.`color`, A.`".self::ID_ATTR."` AS ATTR_ID, A.`".self::ID_OP."` AS OPT_ID, B.`name`")
				->from("`".self::PR_ALB."` AS A")
				->join("`".self::PR_ALB_DESC."` AS B",
						"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_PR."`", $product_id)->where("A.`active`", 1)->order_by("A.`sort`");
		return $query;
	}

	protected function build_product_detail_prices_array($product_id)
	{
		$query = $this->get_product_detail_prices_query($product_id);
		$prices_array = $this->build_product_detail_prices_data($query->get()->result_array());

		return $prices_array;
	}

	protected function build_product_detail_prices_data($prices_temp_array)
	{
		$prices_array['prices_array'] = array();
		$prices_array['selected_price'] = FALSE;
		$prices_array['prices'] = FALSE;

		$today = mktime();

		$first_price = TRUE;
		$selected_price = TRUE;

		foreach($prices_temp_array as $ms)
		{
			$original_special_price = $ms['original_special_price'];
			$special_price = $ms['special_price_rate'];
			$special_price_to_date = FALSE;
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
						$original_special_price = FALSE;
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
						$original_special_price = FALSE;
						$special_price = FALSE;
						$special_price_to_date = FALSE;
					}
				}
			}
			else
			{
				$original_special_price = FALSE;
				$special_price = FALSE;
			}
			$ms['original_special_price'] = $original_special_price;
			$ms['special_price_rate'] = $special_price;
			$ms['special_price_to_date'] = $special_price_to_date;

			$ms['price_attributes_js_array']['show_attributes'] = $ms['show_attributes'];
			$ms['price_attributes_js_array']['id_attributes'] = array();
			if($ms['show_attributes'] == 2)
			{
				foreach(explode(',', $ms['id_attributes']) as $ms1)
				{
					if($ms1 != '')
					{
						$ms['price_attributes_js_array']['id_attributes'][$ms1] = $ms1;
					}
				}
			}
			if($selected_price)
			{
				$ms['selected_price'] = TRUE;
				$selected_price = FALSE;
				$prices_array['selected_price'] = $ms;
			}
			else
			{
				$ms['selected_price'] = FALSE;
			}
			$ms['original_price_string'] = number_format($ms['original_price'], 2, ',', ' ');
			$ms['price_rate_string'] = number_format($ms['price_rate'], 2, ',', ' ');
			if($special_price)
			{
				$ms['original_price'] = $ms['original_special_price'];
				$ms['original_special_price_string'] = number_format($ms['original_special_price'], 2, ',', ' ');
				$ms['special_price_rate_string'] = number_format($ms['special_price_rate'], 2, ',', ' ');
			}
			if($ms['price_name'] != '') $ms['price_name'] .= ' ';
			$prices_array['prices_array'][$ms['PRICE_ID']] = $ms;
		}
		return $prices_array;
	}

	protected function get_product_detail_prices_query($product_id, $price_id = FALSE)
	{
		$this->load->model('catalogue/mcurrency');
		if(!$currency = $this->mcurrency->get_users_default_currency()) return FALSE;

		$query = $this->db
				->select("PRICE.`".self::ID_PR_PRICE."` AS `PRICE_ID`, PRICE.`".self::ID_PR."` AS `PR_ID`,
				PRICE.`price` AS `original_price` ,(PRICE.`price` * ".$currency['rate'].") AS `price_rate`, PRICE.`special_price` AS `original_special_price`, (PRICE.`special_price` * ".$currency['rate'].") AS `special_price_rate`, PRICE.`special_price_from`, PRICE.`special_price_to`, PRICE.`visible_rules` AS `price_visible_rules`, PRICE.`m_u_types` AS `price_m_u_types`, PRICE.`show_attributes`, PRICE.`id_attributes`, PRICE.`min_qty`, PRICE.`alias` AS `price_alias`, PRICE.`real_qty`, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS `price_description`,
				'".$currency['name']."' AS `currency_name`")
				->from("`".self::PR_PRICE."` AS PRICE")
				->join( "`".self::PR_PRICE_DESC."` AS PRICE_DESC",
						"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where_in("PRICE.`".self::ID_PR."`", $product_id)->where("PRICE.`show_in_detail`", 1)->order_by("PRICE.`".self::ID_PR_PRICE."`")->limit(16);
		if($price_id) $query->where("PRICE.`".self::ID_PR_PRICE."`", $price_id);
		return $query;
	}

	protected function build_product_detail_attributes_array($product_id, $selected_price = FALSE)
	{
		$attributes_array['attributes_array'] = array();
		$attributes_array['attributes'] = '';
		$query = $this->get_product_detail_attributes_query($product_id);
		if($selected_price)
		{
			foreach($query->get()->result_array() as $ms)
			{
				if($selected_price['price_attributes_js_array']['show_attributes'] == 1) $ms['visible'] = 1;
				if($selected_price['price_attributes_js_array']['show_attributes'] == 0) $ms['visible'] = 0;
				if($selected_price['price_attributes_js_array']['show_attributes'] == 2)
				{
					if(isset($selected_price['price_attributes_js_array']['id_attributes'][$ms['ID']])) $ms['visible'] = 1;
					else $ms['visible'] = 0;
				}
				$attributes_array['attributes_array']['attributes'][$ms['ID']] = $ms;
				$attributes_array['attributes_array']['options'][$ms['ID']][$ms['ID_OP']] = $ms['o_name'];
			}
		}
		else
		{
			foreach($query->get()->result_array() as $ms)
			{
				$ms['visible'] = 1;
				$attributes_array['attributes_array']['attributes'][$ms['ID']] = $ms;
				$attributes_array['attributes_array']['options'][$ms['ID']][$ms['ID_OP']] = $ms['o_name'];
			}
		}
		return $attributes_array;
	}

	protected function get_product_detail_attributes_query($product_id)
	{
		$query = $this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($product_id))
		{
			$IN = '';
			foreach($id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
			unset($IN);
		}
		else if($product_id>0)
		{
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".$product_id."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"INNER");
		}
		$query
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->where("A.`id_users`", $this->id_users);
		return $query;
	}

	protected function generate_price_html($prices_array, $short_price_access = FALSE)
	{
		if($short_price_access)
		{
			$str = '';
			$str = $this->load->view('catalogue/products/prices_short', array('PRS_price_array' => $prices_array, 'PRS_price_access' => $short_price_access), TRUE);
			return $str;
		}
		$str = '';
		$str = $this->load->view('catalogue/products/prices_detail', array('pr_prices_detail_array' => $prices_array), TRUE);
		//$str .= $this->load->view('catalogue/products/price_attributes_detail_js', array('pr_prices_detail_array' => $price_array), TRUE);
		return $str;
	}

	public function get_product_detail_comments($pr_id)
	{
		$comments_array = array();
		$count = $this->get_product_detail_comments_count($pr_id);
		if($count == 0) return array('comments' => $comments_array, 'pages' => FALSE);
		$page = 1;
		if((int) $this->variables->get_url_vars('product_comments_page')>0)
		{
			$page = (int) $this->variables->get_url_vars('product_comments_page');
		}

		$query = $this->get_product_detail_comments_query($pr_id);
		$pages_data = $this->set_product_detail_comments_limit($query, $count, $page);

		if($pages_data && count($pages_data)>0)
		{
			$this->load->helper('pages');
			$pages_array = get_pages_array($pages_data[0], $pages_data[1], $pages_data[2], array('product_comments_page_lang', array('pr_id' => $pr_id, 'page' => '', 'lang' => $this->mlangs->lang_code)));
		}

		$comments_array = $this->build_product_detail_comments_data($query, $pr_id);
		return array('comments' => $comments_array, 'pages' => $pages_array);
	}

	protected function build_product_detail_comments_data($query, $pr_id)
	{
		$comments = $query->get()->result_array();
		return $comments;
	}

	protected function get_product_detail_comments_count($pr_id)
	{
		$query = $this->db->select("COUNT(*) as COUNT")
				->from("`".self::PR_COMM."`")
				->where("`".self::ID_PR."`", $pr_id);
		$count = $query->get()->row_array();
		$count = $count['COUNT'];
		return $count;
	}

	protected function get_product_detail_comments_query($pr_id)
	{
		$query = $this->db->select("`name`, `email`, `message`, `answer`, `admin_name`, `is_answer`, `create_date`, `update_date`, `id_langs`")
				->from("`".self::PR_COMM."`")
				->where("`".self::ID_PR."`", $pr_id)->where("`active`", 1)->order_by("`sort`", "DESC");
		return $query;
	}

	protected function set_product_detail_comments_limit($query, $count, $page = 1)
	{
		$limit = $this->settings['reviews_count_to_page'];
		if($page > ceil($count/$limit))
		{
			redirect($this->router->build_url('categories_page_lang', array('page' => ceil($count/$limit), 'categorie_url' => $this->variables->get_vars('categorie_url'), 'lang' => $this->langs->id_langs)) ,301);
		}

		if($page == 0)
		{
			$page = 1;
		}
		$offset = ($page-1)*$limit;
		$showcount = $limit;
		$query->limit($showcount, $offset);

		return array($count, $page, $limit);
	}

	public function add_product_detail_comment($pr_id)
	{
		if(!$this->check_isset_pr($pr_id)) return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => '<p>Error add comment!</p>'), TRUE));

		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Имя', 'required|min_length[4]');
		$this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
		$this->form_validation->set_rules('message', 'Комментарий', 'required|min_length[10]');

		if(!$this->form_validation->run())
		{
			return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => validation_errors()), TRUE));
		}

		$POST = $this->input->post();
		$data = array(self::ID_PR => $pr_id, 'name' => $POST['name'], 'email' => $POST['email'], 'message' => $POST['message'], 'new_comment' => 1, 'id_langs' => $this->mlangs->id_langs, self::ID_USERS => $this->id_users);
		if($this->settings['reviews_publication_immediately'] == 0)
		{
			$data['active'] = 0;
		}
		$id = $this->sql_add_data($data)->sql_update_date()->sql_using_user()->sql_save(self::PR_COMM);
		if($id)
		{
			$this->sql_add_data(array('sort' => $id))->sql_save(self::PR_COMM, $id);
			$this->products_detail_comment_send_mail_to_admin($pr_id, $data);

			if($this->settings['reviews_publication_immediately'] == 1)
			{
				return array('success' => 1, 'messages' => $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$this->lang->line('products_comments_success_add_comment').'</p>'), TRUE));
			}
			return array('success' => 1, 'messages' => $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$this->lang->line('products_comments_success_add_admin_confirm').'</p>'), TRUE));
		}
		return array('success' => 0, 'messages' => $this->load->view('site_messages/site_messages', array('error_message' => '<p>Error add comment!</p>'), TRUE));
	}

	protected function products_detail_comment_send_mail_to_admin($pr_id, $mail_data)
	{
		if($this->settings['reviews_admin_notice'] == 1)
		{
			$query = $this->db->select("A.`sku`, A.`url_key`, B.`name`")
					->from("`".self::PR."` AS A")
					->join("`".self::PR_DESC."` AS B",
							"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`id_langs` = ".$this->mlangs->id_langs,
							"LEFT")
					->where("A.`".self::ID_PR."`", $pr_id)->limit(1);
			$product = $query->get()->row_array();
			$product_url = $pr_id;
			if($product['url_key'] != '')
			{
				$product_url = trim($product['url_key']);
			}
			$product['url'] = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));

			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;

			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['products_url'] = $product['url'];
			$data['products_name'] = $product['name'];
			$data['products_sku'] = $product['sku'];
			$data['name'] = $mail_data['name'];
			$data['email'] = $mail_data['email'];
			$data['message'] = $mail_data['message'];
			if($this->settings['reviews_admin_email'] != '')
			{
				$letter_html = $this->load->view('catalogue/products/letters/'.$this->mlangs->language.'/products_detail_new_comment', array('data' => $data), TRUE);
				$this->load->library('email');
				$this->email->initialize($config);
				$this->email->from('no-reply@'.$data['site'], $data['site']);
				$this->email->to($this->settings['reviews_admin_email']);
				$this->email->subject('New comment to product SKU '.$data['products_sku'].'.');
				$this->email->message($letter_html);
				$this->email->send();
				$this->email->clear();
			}
		}
		return TRUE;
	}

	public function get_product_detail_related_products($pr_id)
	{
		if($this->settings['related_on'])
		{
			return $this->build_product_detail_related_products_data($pr_id);
		}
		return FALSE;
	}

	protected function build_product_detail_related_products_data($pr_id)
	{
		$query = $this->get_product_detail_related_products_query($pr_id);
		if(count($result = $query->get()->result_array()) > 0)
		{
			$related_products_array = array();
			foreach($result as $ms)
			{
				$related_id[] = $ms['PR_REL_ID'];
			}
			$rand = FALSE;
			if($this->settings['related_random'] == 1) $rand = TRUE;
			$products = $this->get_products_by_id($related_id, $rand, TRUE);

			$related_products_array['related_products'] = $products['products'];
			$block_count = $this->settings['related_show_count'];
			$block = 0;
			$i = 1;
			foreach($products['products'] as $ms)
			{
				if($block_count <= $i)
				{
					$related_products_array['related_products_block'][$block][] = $ms;
					$i++;
				}
				else
				{
					$i=1;
					$block++;
				}
			}
			return $related_products_array;
		}
		return FALSE;
	}

	protected function get_product_detail_related_products_query($pr_id)
	{
		$query = $this->db->select("`".self::ID_PR_REL."` AS PR_REL_ID")
				->from("`".self::PR_REL."`")
				->where("`".self::ID_PR."`", $pr_id);
		if($this->settings['related_random'] == 1) $query->order_by('RAND()');
		else $query->order_by('sort', 'DESC');
		if(($rel_count = $this->settings['related_count']) > 0) $query->limit($rel_count);
		return $query;
	}

	public function get_product_detail_similar_products($pr_id)
	{
		if($this->settings['similar_on'])
		{
			return $this->build_product_detail_similar_products_data($pr_id);
		}
		return FALSE;
	}

	protected function build_product_detail_similar_products_data($pr_id)
	{
		$query = $this->get_product_detail_similar_products_query($pr_id);
		if(count($result = $query->get()->result_array()) > 0)
		{
			$similar_products_array = array();
			foreach($result as $ms)
			{
				$similar_id[] = $ms['PR_SIM_ID'];
			}
			$rand = FALSE;
			if($this->settings['similar_random'] == 1) $rand = TRUE;
			$products = $this->get_products_by_id($similar_id, $rand, TRUE);

			$similar_products_array['similar_products'] = $products['products'];
			$block_count = $this->settings['similar_show_count'];
			$block = 0;
			$i = 1;
			foreach($products['products'] as $ms)
			{
				if($block_count <= $i)
				{
					$similar_products_array['similar_products_block'][$block][] = $ms;
					$i++;
				}
				else
				{
					$i=1;
					$block++;
				}
			}
			return $similar_products_array;
		}
		return FALSE;
	}

	protected function get_product_detail_similar_products_query($pr_id)
	{
		$query = $this->db->select("`".self::ID_PR_SIM."` AS PR_SIM_ID")
				->from("`".self::PR_SIM."`")
				->where("`".self::ID_PR."`", $pr_id);
		if($this->settings['similar_random'] == 1) $query->order_by('RAND()');
		else $query->order_by('sort', 'DESC');
		if(($rel_count = $this->settings['similar_count']) > 0) $query->limit($rel_count);
		return $query;
	}

	public function get_product_price($pr_id, $price_id)
	{
		if(($price_id = intval($price_id)) == 0) return FALSE;
		$query = $this->get_product_detail_prices_query($pr_id, $price_id);
		$price = $query->get()->row_array();
		if(count($price) == 0) return FALSE;
		$price_array = $this->build_product_detail_prices_data(array($price));

		return current($price_array['prices_array']);
	}

	public function get_product_attributes_and_options($id)
	{
		$query = $this->db
			->select("A.`".self::ID_ATTR."` AS ID, B.`".self::ID_OP."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTR."` AS A");
		if(is_array($id))
		{
			$IN = '';
			foreach($id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` IN (".$IN.") && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"inner");
			unset($IN);
		}
		else if(intval($id)>0)
		{
			$query	->join(	"`".self::OP."` AS B",
							"B.`".self::ID_OP."` IN(SELECT `".self::ID_OP."` FROM `".self::PNA."` WHERE `".self::ID_PR."` = '".intval($id)."' && `".self::ID_OP."` IS NOT NULL) && B.`".self::ID_ATTR."` = A.`".self::ID_ATTR."`",
							"inner");
		}
		$query
			->join(	"`".self::ATTR_DESC."` AS C",
					"C.`".self::ID_ATTR."` = A.`".self::ID_ATTR."` && C.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->join(	"`".self::OP_DESC."` AS D",
					"D.`".self::ID_OP."` = B.`".self::ID_OP."` && D.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
					"LEFT")
			->where("A.`id_users`", $this->id_users);
		$result = $query->get()->result_array();
		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']][$ms['ID_OP']] = $ms;
		}
		unset($result);
		return $array;
	}




	public function get_products_by_id($id)
	{
		$products = array('products' => array());
		if(is_array($id))
		{
			if(count($id)>0)
			{
				$query = $this->get_products_by_id_query($id);
				$products = $this->get_products_by_id_array($query->get()->result_array());
				return $products;
			}
		}
		return $products;
	}

	protected function get_products_by_id_query($id)
	{
		$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`url_key`, A.`in_stock`, A.`bestseller`, A.`new`, A.`sale`, B.`name`, B.`short_description`,
				IMG.`image`, IMG.`".self::ID_PR_ALB."` AS ALB_ID, IMG_DESC.`name` AS image_name, IMG_DESC.`title` AS image_title, IMG_DESC.`alt` AS image_alt")
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"left")
				//Select Images
				->join(	"`".self::PR_IMG."` AS IMG",
						"IMG.`".self::ID_PR."` = A.`".self::ID_PR."` && IMG.`preview` = 1",
						"LEFT")
				//Select Images Description
				->join(	"`".self::PR_IMG_DESC."` AS IMG_DESC",
						"IMG_DESC.`".self::ID_PR_IMG."` = IMG.`".self::ID_PR_IMG."` && IMG_DESC.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where_in("A.`".self::ID_PR."`", $id)->where("A.`status`", 1);
		return $query;
	}

	protected function get_products_by_id_array($result = array())
	{

		$array = array();
		foreach($result as $ms)
		{
			if($ms['image'] != NULL)
			{
				$alb_seg = '';
				if($ms['ALB_ID'] != NULL)
				{
					$alb_seg = '/'.$ms['ALB_ID'];
				}
				$ms += array('timage' => $this->img_path.$ms['ID'].$alb_seg.'/thumb_'.$ms['image'], 'bimage' => $this->img_path.$ms['ID'].$alb_seg.'/'.$ms['image']);
			}
			else
			{
				$ms['timage'] = FALSE;
			}
			$array[$ms['ID']] = $ms;
		}
		$products = $array;
		return $products;
	}
}
?>