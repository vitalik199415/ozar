<?php
class Mproducts_types_set extends AG_Model
{
	const PR_TYPES_SET = 'm_c_products_types_set';
	const ID_PR_TYPES_SET = 'id_m_c_products_types_set';

	const PR_TYPES_SET_ALIAS = 'm_c_products_types_set_alias';

	const PR_TYPES_SET_DESC = 'm_c_products_types_set_description';
	const ID_PR_TYPES_SET_DESC = 'id_m_c_products_types_set_description';

	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';

	const TYPES 		= 'm_c_products_types';
	const ID_TYPES 		= 'id_m_c_products_types';
	const TYPES_DESC 	= 'm_c_products_types_description';

	const PROP 		= 'm_c_products_properties';
	const ID_PROP 	= 'id_m_c_products_properties';
	const PROP_DESC = 'm_c_products_properties_description';

	const ID_PR = 'id_m_c_products';
	const PR_CAT = 'm_c_productsNcategories';
	const PR_TYPES = 'm_c_productsNtypes';

	function __construct()
	{
		parent::__construct();
	}

	public function render_types_set_grid()
	{
		$categories = $this->get_types_set_categories_to_select();
		$types_url = $this->get_types_set_url();

		$this->load->library("grid");
		$this->grid->_init_grid("products_types_set_grid");
		$this->grid->db
			->select("A.`".self::ID_PR_TYPES_SET."` AS ID, A.`".self::ID_CAT."` AS CAT_ID, A.`show_id`,  A.`set_name`, A.`set_description`,
			B.`name` AS cat_name, IFNULL(A.`url`, A.`types_set_url`) AS url")
			->from("`".self::PR_TYPES_SET."` AS A")
			->join("`".self::CAT_DESC."` AS B",
				"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$this->load->helper('products_types_set');
		helper_products_types_set_grid_build($this->grid, $categories);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('url', $types_url);

		$this->grid->render_grid();
	}

	public function select_category()
	{
		$this->load->helper('aggrid_tree_helper');
		$Grid = new Aggrid_tree_Helper('products_types_set_categories_grid');

		$Grid->db->select("A.`".self::ID_CAT."` AS ID, A.`id_parent`, A.`level`, A.`sort` AS sort, A.`active`, A.`create_date`, A.`update_date`, B.`name`,
							(SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS PARENT_COUNT")
			->from("`".self::CAT."` AS A")
			->join(	"`".self::CAT_DESC."` AS B",
				"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
				"LEFT")
			->where("A.`".self::ID_USERS."`",$this->id_users)->order_by('sort');

		$this->load->helper('products_types_set');

		$Grid = categories_grid_build($Grid);
		$Grid->createDataArray();
		$Grid->updateGridValues('active',array('0' => 'Нет', '1' => 'Да'));
		$Grid->renderGrid();
	}

	public function add($cat_id)
	{
		if(!$this->check_isset_category($cat_id)) return FALSE;

		$cat_desc = $this->get_category_description($cat_id);
		$this->template->add_navigation('Выбор категории', set_url('*/*/select_category'));

		$this->template->add_title('Категория: '.$cat_desc['name']);
		$this->template->add_navigation('Категория: '.$cat_desc['name']);

		$this->template->add_title(' | Добавить набор');
		$this->template->add_navigation('Добавить набор');

		$data = array();
		$data['cat_id'] = $cat_id;
		$data['cat_desc'] = $cat_desc;

		$data['category_properties'] = $this->get_category_properties($cat_id);

		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();

		$this->load->helper('products_types_set');
		helper_products_types_set_add_edit_form($data);

		return TRUE;
	}

	public function edit($cat_id, $id)
	{
		if(!$this->check_isset_category($cat_id)) return FALSE;

		$cat_desc = $this->get_category_description($cat_id);
		$this->template->add_navigation('Выбор категории', set_url('*/*/select_category'));

		$this->template->add_title('Категория: '.$cat_desc['name']);
		$this->template->add_navigation('Категория: '.$cat_desc['name']);

		$this->template->add_title(' | Редактировать набор');
		$this->template->add_navigation('Редактировать набор');

		$data = array();
		$data['cat_id'] = $cat_id;
		$data['cat_desc'] = $cat_desc;

		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();

		$this->db->select("A.`set_name`, A.`set_description`, A.`url`,
		 			D.`".self::ID_LANGS."`, D.`seo_title`, D.`seo_description`, D.`seo_keywords`, D.`description`")
			->from(" `".self::PR_TYPES_SET."` AS A")
			->join("`".self::PR_TYPES_SET_DESC."` AS D",
					"A.`".self::ID_PR_TYPES_SET."` = D.`".self::ID_PR_TYPES_SET."`",
					"LEFT")
			->where("A.`".self::ID_PR_TYPES_SET."`", $id)
			->where("A.`".self::ID_CAT."`", $cat_id)
			->limit(count($data['on_langs']));

		$res = $this->db->get()->result_array();
		if(count($res) == 0) return FALSE;

		foreach($res as $val)
		{
			$data['main'] = $val;
			$data['seo_desc'][$val[self::ID_LANGS]] = $val;
		}

		$this->db->select("A.`".self::ID_PROP."`, B.`".self::ID_TYPES."`")
			->from("`".self::PR_TYPES_SET_ALIAS."` AS A")
			->join("`".self::PROP."` AS B",
				"B.`".self::ID_PROP."` = A.`".self::ID_PROP."`",
				"INNER")
			->where("`".self::ID_PR_TYPES_SET."`", $id);
		$prop_selected = $this->db->get()->result_array();

		$data['category_properties'] = $this->get_category_properties($cat_id);
		$data['edit_properties']['product_types'] = array();
		$data['edit_properties']['product_properties'] = array();
		foreach($prop_selected as $ms)
		{
			$data['edit_properties']['product_types'][$ms[self::ID_TYPES]] = $ms[self::ID_TYPES];
			$data['edit_properties']['product_properties'][$ms[self::ID_TYPES]][$ms[self::ID_PROP]] = $ms[self::ID_PROP];
		}

		$this->load->helper('products_types_set');
		helper_products_types_set_add_edit_form($data, '/id/'.$id);
		return TRUE;
	}

	public function get_category_properties($category_id)
	{
		$properties['properties'] = array();
		$properties['types'] = array();

		$this->db->select("A.`".self::ID_TYPES."` AS ID, A.`alias` AS talias, AD.`name` AS tname, B.`".self::ID_PROP."` AS PID, B.`alias` AS palias, BD.`name` AS pname,
					(SELECT COUNT(*) FROM `".self::PR_TYPES."` AS QTY
					INNER JOIN `".self::PR_CAT."` AS Q_PRC
					ON Q_PRC.`".self::ID_CAT."` = '".$category_id."' && Q_PRC.`".self::ID_PR."` = QTY.`".self::ID_PR."`
					WHERE
					QTY.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` &&
					QTY.`".self::ID_PROP."` = B.`".self::ID_PROP."`) AS pr_qty ")
			->from("`".self::TYPES."` AS A")
			->join("`".self::TYPES_DESC."` AS AD",
				"AD.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && AD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
				"LEFT")
						  ->join("`".self::PROP."` AS B",
				"B.`".self::ID_TYPES."` = A.`".self::ID_TYPES."`",
				"LEFT")
						  ->join("`".self::PROP_DESC."` AS BD",
				"BD.`".self::ID_PROP."` = B.`".self::ID_PROP."` && BD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->where("A.`active`", 1)->where("B.`active`", 1)
			->where("
				EXISTS(
				SELECT EA.`".self::ID_TYPES."`, EA.`".self::ID_PROP."` FROM `".self::PR_TYPES."` AS EA
				INNER JOIN `".self::PR_CAT."` AS EB
					ON EB.`".self::ID_CAT."` = ".$category_id." && EB.`".self::ID_PR."` = EA.`".self::ID_PR."`
				WHERE EA.`".self::ID_PROP."` IS NOT NULL && EA.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && EA.`".self::ID_PROP."` = B.`".self::ID_PROP."`
				)
				", NULL, FALSE)
			->order_by("A.`sort`, B.`sort`");

		$result = $this->db->get()->result_array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$properties['types'][$ms['ID']] = $ms['tname'].' ('.$ms['talias'].')';
				$properties['properties'][$ms['ID']][$ms['PID']] = $ms['pname'].' ('.$ms['palias'].')'.' <span style="font-size:10px;color:#AAAAAA;">('.$ms['pr_qty'].')</span>';


			}
		}
		return $properties;
	}

	public function get_category_description($cat_id)
	{
		$this->db->select("B.*")
			->from("`".self::CAT."` AS A")
			->join("`".self::CAT_DESC."` AS B",
				"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
				"LEFT")
			->where("A.`".self::ID_CAT."`", $cat_id)->limit(1);
		$desc = $this->db->get()->row_array();
		return $desc;
	}

	public function check_isset_category($cat_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
			->from("`".self::CAT."`")->where("`".self::ID_CAT."`", $cat_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$res = $this->db->get()->row_array();
		if($res['COUNT'] > 0) return TRUE;
		return FALSE;
	}

	public function save_validation($cat_id)
	{
		if(!$this->check_isset_category($cat_id)) return FALSE;
		return TRUE;
	}

	public function save($cat_id, $id = false)
	{
		if(!$this->save_validation($cat_id)) return FALSE;
		if($id)
		{
			$POST = $this->input->post('main');

			$this->db->trans_start();
			if(trim($POST['url']) == '') $POST['url'] = NULL;
			$this->sql_add_data($POST)->sql_using_user()->sql_save(self::PR_TYPES_SET, $id);

			$pr_types = $this->input->post('product_types');
			$pr_prop = $this->input->post('product_properties');

			$params_str = '';
			if(is_array($pr_types) && is_array($pr_prop))
			{
				$tdata = array();
				$this->db->select("A.`".self::ID_PROP."`")
						 ->from("`".self::PR_TYPES_SET_ALIAS."` AS A")
						 ->where("A.`".self::ID_PR_TYPES_SET."`", $id);
				$result = $this->db->get()->result_array();
				foreach($result as $ms)
				{
					$tdata[$ms[self::ID_PROP]] = $ms[self::ID_PROP];
				}

				foreach($pr_types as $ms)
				{
					if(isset($pr_prop[$ms]))
					{
						$params_str .= $ms.'=';
						foreach($pr_prop[$ms] as $pr)
						{
							$params_str .= $pr.',';
							if(isset($tdata[$pr]))
							{
								unset($tdata[$pr]);
							}
							else
							{
								$data = array(self::ID_PROP => $pr, self::ID_PR_TYPES_SET => $id);
								$this->sql_add_data($data)->sql_save(self::PR_TYPES_SET_ALIAS);
							}
						}
						$params_str = substr($params_str, 0, -1);
						$params_str .= ';';
					}
				}
				$params_str = substr($params_str, 0, -1);
				$this->sql_add_data(array('types_set_url' => $params_str))->sql_using_user()->sql_save(self::PR_TYPES_SET, $id);

				if(isset($tdata) && is_array($tdata) && count($tdata) > 0)
				{
					$this->db->where("`".self::ID_PR_TYPES_SET."`", $id)->where_in("`".self::ID_PROP."`", $tdata);
					$this->db->delete(self::PR_TYPES_SET_ALIAS);
				}
			}
			else
			{
				$this->db->where("`".self::ID_PR_TYPES_SET."`", $id)->delete(self::PR_TYPES_SET_ALIAS);
				$this->sql_add_data(array('types_set_url' => ''))->sql_using_user()->sql_save(self::PR_TYPES_SET, $id);
			}

			$POST = $this->input->post('seo_desc');

			$this->db->select("A.`".self::ID_PR_TYPES_SET_DESC."`, A.`".self::ID_LANGS."`")
				->from("`".self::PR_TYPES_SET_DESC."` AS A")
				->where("A.`".self::ID_PR_TYPES_SET."`", $id);
			$lang_result_temp = $this->db->get()->result_array();

			$lang_result = array();
			foreach($lang_result_temp as $ms)
			{
				$lang_result[$ms['id_langs']] = $ms;
			}

			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();

			foreach($langs as $key => $ms)
			{
				if(isset($POST[$key]))
				{
					if(isset($lang_result[$key]))
					{
						$this->sql_add_data($POST[$key])->sql_save(self::PR_TYPES_SET_DESC, $lang_result[$key][self::ID_PR_TYPES_SET_DESC]);
					}
					else
					{
						$this->sql_add_data($POST[$key]+array(self::ID_PR_TYPES_SET => $id, self::ID_LANGS => $key))->sql_save(self::PR_TYPES_SET_DESC);
					}
				}
			}

			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return $id;
			}
			return FALSE;
		}
		else
		{
			$POST = $this->input->post('main');

			$this->db->select("MAX(`show_id`) AS show_id")
					 ->from("`".self::PR_TYPES_SET."`")
					 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$MAX = $this->db->get()->row_array();
			$max = 1;
			if(count($MAX)>0)
			{
				$max = $MAX['show_id'] + 1;
			}

			$this->db->trans_start();
			if(trim($POST['url']) == '') $POST['url'] = NULL;

			$ID = $this->sql_add_data($POST + array(self::ID_CAT => $cat_id, 'show_id' => $max))->sql_using_user()->sql_save(self::PR_TYPES_SET);
			if($ID)
			{
				$params_str = '';

				$pr_types = $this->input->post('product_types');
				$pr_prop = $this->input->post('product_properties');
				if(is_array($pr_types) && is_array($pr_prop))
				{
					foreach($pr_types as $ms)
					{
						if(isset($pr_prop[$ms]))
						{
							$params_str .= $ms.'=';
							foreach($pr_prop[$ms] as $pr)
							{
								$params_str .= $pr.',';

								$data = array(self::ID_PROP => $pr, self::ID_PR_TYPES_SET => $ID);
								$this->sql_add_data($data)->sql_save(self::PR_TYPES_SET_ALIAS);
							}
							$params_str = substr($params_str, 0, -1);
							$params_str .= ';';
						}
					}
					$params_str = substr($params_str, 0, -1);
					$this->sql_add_data(array('types_set_url' => $params_str))->sql_using_user()->sql_save(self::PR_TYPES_SET, $ID);
				}

				if($POST = $this->input->post('seo_desc'))
				{
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->get_active_languages();
					foreach($langs as $key => $ms)
					{
						if(isset($POST[$key]))
						{
							$data = $POST[$key] + array(self::ID_LANGS => $key, self::ID_PR_TYPES_SET => $ID);
							$this->sql_add_data($data)->sql_save(self::PR_TYPES_SET_DESC);
						}
					}
				}
			}

			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return $ID;
			}
			return FALSE;
		}
	}

	public function delete($id)
	{
		$this->db->where("`".self::ID_PR_TYPES_SET."`", $id)->where("`".self::ID_USERS."`", $this->id_users);
		if($this->db->delete(self::PR_TYPES_SET))
		{
			return true;
		}
	}

	public function get_types_set_categories_to_select()
	{
		$cat = array();
		$this->db->select("DISTINCT A.`".self::ID_CAT."`, B.`name`")
			->from("`".self::PR_TYPES_SET."`  AS A")
			->join("`".self::CAT."` AS C",
				"A.`".self::ID_CAT."` = C.`".self::ID_CAT."`")
			->join(	"`".self::CAT_DESC."` AS B",
				"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->result_array();
		foreach($result as $val)
		{
			$cat[$val[self::ID_CAT]] = $val['name'];
		}
		return $cat;
	}

	public function get_types_set_url()
	{
		$data = array();
		$userdata = $this->musers->get_user();
		$this->db->select("A.`".self::ID_PR_TYPES_SET."` AS ID, A.`".self::ID_CAT."` AS id_cat, A.`url`, A.`types_set_url`, IFNULL(A.`url`, A.`types_set_url`) AS set_url, IFNULL(C.`url`, A.`".self::ID_CAT."`) AS category_url")
			->from("`".self::PR_TYPES_SET."`  AS A")
			->join("`".self::CAT."` AS C",
				"A.`".self::ID_CAT."` = C.`".self::ID_CAT."`")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->result_array();

		foreach($result as $val)
		{
			if(is_null($val['url']))
			{
				$data[$val['set_url']] = 'http://'.$userdata['domain'].'/category-'.$val['category_url'].'/filters/'.$val['types_set_url'];
			}
			else
			{
				$data[$val['set_url']] = 'http://'.$userdata['domain'].'/category-'.$val['category_url'].'/filters/filters-'.$val['url'];
			}
		}
		return $data;
	}
} 