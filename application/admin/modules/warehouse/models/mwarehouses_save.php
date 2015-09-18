<?php
class Mwarehouses_save extends AG_Model
{
	const WH 			= 'wh';
	const ID_WH 		= 'id_wh';
	const WH_SH 		= 'wh_shops';
	const ID_WH_SH 		= 'id_wh_shops';
	const WHNSH			= 'wh_whNshops';
	
	const WH_PR = 'wh_products';
	
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	
	public $id_wh = FALSE;
	public $id_wh_shop = FALSE;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function add_wh()
	{
		$this->load->helper('warehouses');
		helper_wh_form_build();
	}
	
	public function edit_wh($wh_id)
	{
		$wh_id = intval($wh_id);
		$this->db->select("`".self::ID_WH."` AS ID, `alias`, `active`, `i_s_wh`")
				->from("`".self::WH."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($result = $this->db->get()->row_array()) > 0)
		{
			$data = array();
			$data['main'] = $result;
			$this->load->helper('warehouses');
			helper_wh_form_build($data, '/wh_id/'.$wh_id);
			return TRUE;
		}
		return FALSE;
	}
	
	protected function save_wh_validation($wh_id = FALSE)
	{
		if($wh_id)
		{
			if(!$this->check_isset_wh($wh_id)) return FALSE;
			$this->id_wh = $wh_id;
		}
		
		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_wh_alias', 'mwarehouses_save');
		$this->form_validation->add_callback_function_class('is_0_or_1', 'mwarehouses_save');
		
		$this->form_validation->set_rules('main[alias]', 'Идентификатор', 'trim|required|callback_check_isset_wh_alias');
		$this->form_validation->set_message('check_isset_wh_alias', 'Склад указанным идентификатором уже существует!');
		
		$this->form_validation->set_rules('main[active]', 'Активность', 'required|callback_is_0_or_1');
		$this->form_validation->set_rules('main[i_s_wh]', 'Склад интернет-магазина', 'required|callback_is_0_or_1');
		$this->form_validation->set_message('is_0_or_1', 'Не верное значение поля "%s"!');
		
		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }
		
		return TRUE;
	}
	
	public function check_isset_wh($wh_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::WH."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
	
	public function check_isset_wh_alias($alias)
	{
		$alias = trim($alias);
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::WH."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_wh)
		{
			$this->db->where("`".self::ID_WH."` <>", $this->id_wh);
		}
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function save_wh($wh_id = FALSE)
	{
		if(!$this->input->post('main')) return FALSE;
		if($wh_id)
		{
			if(!$this->save_wh_validation($wh_id)) return FALSE;
			
			$POST = $this->input->post('main');

			$this->db->trans_start();
			if($POST['i_s_wh'] == 1)
			{
				$this->sql_add_data(array('i_s_wh' => 0))->sql_using_user()->sql_save(self::WH, array('id_users' => $this->id_users));
			}
			$this->sql_add_data($POST)->sql_using_user()->sql_save(self::WH, $wh_id);
			$this->db->trans_complete();
			
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
		else
		{
			if(!$this->save_wh_validation()) return FALSE;
			
			$POST = $this->input->post('main');
			$this->db->trans_start();
			if($POST['i_s_wh'] == 1)
			{
				$this->sql_add_data(array('i_s_wh' => 0))->sql_using_user()->sql_save(self::WH, array('id_users' => $this->id_users));
			}
			$wh_id = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::WH);
			$this->sql_add_data(array('sort' => $wh_id))->sql_save(self::WH, $wh_id);
			$this->db->trans_complete();
			
			if($this->db->trans_status())
			{
				return $wh_id;
			}
			return FALSE;
		}
	}
	
	public function add_wh_shop()
	{
		if(count($wh = $this->get_wh_to_select()) > 0)
		{
			$data = array();
			$data['warehouses'] = $wh;
			$this->load->helper('warehouses');
			helper_wh_shops_form_build($data);
			return TRUE;
		}
		return FALSE;
	}
	
	public function edit_wh_shop($wh_shop_id)
	{
		$wh_shop_id = intval($wh_shop_id);
		if(!$this->check_isset_wh_shop($wh_shop_id)) return FALSE;
		$this->db->select("`".self::ID_WH_SH."` AS ID, `active`, `alias`")
				->from("`".self::WH_SH."`")
				->where("`".self::ID_WH_SH."`", $wh_shop_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $this->db->get()->row_array();
		$data['main'] = $result;
		
		$this->db->select("`".self::ID_WH."`")
				->from("`".self::WHNSH."`")
				->where("`".self::ID_WH_SH."`", $wh_shop_id);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$data['main_warehouses'][$ms[self::ID_WH]] = $ms[self::ID_WH];
		}
		
		if(count($wh = $this->get_wh_to_select()) == 0) return FALSE;
		$data['warehouses'] = $wh;
		
		$this->load->helper('warehouses');
		helper_wh_shops_form_build($data, '/wh_whop_id/'.$wh_shop_id);
		return TRUE;
	}
	
	protected function save_wh_shop_validation($wh_shop_id = FALSE)
	{
		if($wh_shop_id)
		{
			if(!$this->check_isset_wh_shop($wh_shop_id)) return FALSE;
			$this->id_wh_shop = $wh_shop_id;
		}
		
		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_wh_shop_alias', 'mwarehouses_save');
		$this->form_validation->add_callback_function_class('is_0_or_1', 'mwarehouses_save');
		
		$this->form_validation->set_rules('main[alias]', 'Идентификатор', 'trim|required|callback_check_isset_wh_shop_alias');
		$this->form_validation->set_message('check_isset_wh_shop_alias', 'Точка продаж с указанным идентификатором уже существует!');
		
		$this->form_validation->set_rules('main[active]', 'Активность', 'required|callback_is_0_or_1');
		$this->form_validation->set_message('is_0_or_1', 'Не верное значение - "Активность"!');
		
		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }
		
		if(!($WPOST = $this->input->post('main_warehouses')) && !is_array($WPOST)) { $this->messages->add_error_message('Не был выбран склад точки продаж!'); return FALSE; }
		if(count($WPOST) == 0 || count($wh = $this->get_wh_to_select()) == 0) return FALSE;
		foreach($WPOST as $ms)
		{
			if(!isset($wh[$ms])) return FALSE;
		}
		return TRUE;
	}
	
	public function check_isset_wh_shop($wh_shop_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::WH_SH."`")
				->where("`".self::ID_WH_SH."`", $wh_shop_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
	
	public function check_isset_wh_shop_alias($alias)
	{
		$alias = trim($alias);
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::WH_SH."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_wh_shop)
		{
			$this->db->where("`".self::ID_WH_SH."` <>", $this->id_warehouses_shop);
		}
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function save_shop($wh_shop_id = FALSE)
	{
		if(!$this->input->post('main')) return FALSE;
		if($wh_shop_id)
		{
			if(!$this->save_shop_validation($wh_shop_id)) return FALSE;
			$POST = $this->input->post('main');
			$WPOST = $this->input->post('main_warehouses');

			$this->db->trans_start();
			$this->sql_add_data($POST)->sql_using_user()->sql_save(self::WH_SH, $wh_shop_id);
			
			$this->save_shop_warehouses($wh_shop_id, $WPOST, TRUE);
			
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
		else
		{
			if(!$this->save_shop_validation()) return FALSE;
			$POST = $this->input->post('main');
			$WPOST = $this->input->post('main_warehouses');
			
			$this->db->trans_start();
			$wh_shop_id = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::WH_SH);
			$this->sql_add_data(array('sort' => $wh_shop_id))->sql_save(self::WH_SH, $wh_shop_id);
			
			$this->save_shop_wh($wh_shop_id, $WPOST);
			
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
	}
	
	protected function save_shop_wh($wh_shop_id, $POST, $edit = FALSE)
	{
		if($edit)
		{
			$this->db->select("`".self::ID_WH."`")
					->from("`".self::WHNSH."`")
					->where("`".self::ID_WH_SH."`", $wh_shop_id);
			$result = $this->db->get()->result_array();
			$sh_wh_add = $POST;
			$sh_wh_del = array();
			foreach($result as $ms)
			{
				if(!isset($POST[$ms[self::ID_WH]]))
				{
					$sh_wh_del[$ms[self::ID_WH]] = $ms[self::ID_WH];
				}
				else
				{
					unset($sh_wh_add[$ms[self::ID_WH]]);
				}
			}
			//ADD
			if(count($sh_wh_add) > 0)
			{
				foreach($sh_wh_add as $ms)
				{
					$this->sql_add_data(array(self::ID_WH_SH => $wh_shop_id, self::ID_WH => $ms))->sql_save(self::WHNSH);
				}
			}
			//DEL
			if(count($sh_wh_del) > 0)
			{
				$this->db->where("`".self::ID_WH_SH."`", $wh_shop_id)->where_in("`".self::ID_WH."`", $sh_wh_del);  
				$this->db->delete(self::WHNSH);
			}
		}
		else
		{
			foreach($POST as $ms)
			{
				$this->sql_add_data(array(self::ID_WH_SH => $wh_shop_id, self::ID_WH => $ms))->sql_save(self::WHNSH);
			}
		}
	}
}
?>