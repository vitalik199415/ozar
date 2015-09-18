<?php
class Mhome extends AG_Model
{
	const HM 				= 'users_menu_modules';
	const ID_HM 			= 'id_users_menu_modules';
	const UM 				= 'users_modules';
	const ID_UM 			= 'id_users_modules';
	const UHD				= 'm_menu_description';
	const ID_UHD			= 'id_m_menu_description';
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function edit()
	{
		$data = array();
		$query = $this->db	->select("A.*, A.`".self::ID_UM."` AS ID, B.`sort` AS SORT")
							->from("`".self::UM."` AS A")
							->join(	"`".self::HM."` AS B",
									"B.`".self::ID_UM."` = A.`".self::ID_UM."` && B.`id_m_menu` IS NULL",
									"left")
							->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("B.`sort`")->order_by("ID");
		$result = $query->get()->result_array();
		
		foreach($result as $ms)
		{
			if($ms['SORT'] == NULL) 
			{
				$data['checkbox'][$ms[self::ID_UM]] = array(self::ID_UM => $ms[self::ID_UM], 'alias' => $ms['alias']);
			}
			else
			{
				$data['checkbox_checked'][$ms[self::ID_UM]] = $ms;
			}
		}				
		
		$query = $this->db
				->select("*")
				->from("`".self::UHD."`")
				->where("`".self::ID_USERS."`", $this->id_users);
				
		foreach($query->get()->result_array() as $ms)
		{
			$data['home_desc']['home_desc'][$ms['id_langs']] = $ms;
		}
		$this->load->helper('home/home_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		helper_menu_modules_form($data);
	}
	
	public function save()
	{
		$POST = $this->input->post();
		if(isset($POST['save']))
		{
			$this->db->trans_start();
			if(isset($POST[self::ID_UM]))
			
				foreach($POST[self::ID_UM] as $key => $ms)
				{
					$data = array(self::ID_UM => $ms);
					$ID = $this->sql_add_data($data)->sql_using_user()->sql_save(self::HM);
					$data = array('sort' => $ID);
					$this->sql_add_data($data)->sql_save(self::HM, $ID);					
				}
			}
			
			$query = $this->db
					->select("`".self::ID_UHD."` AS ID, `".self::ID_LANGS."`")
					->from("`".self::UHD."`")
					->where("`".self::ID_USERS."`", $this->id_users);
			$temp = $query->get()->result_array();
			foreach($temp as $ms)
			{
				$res[$ms[self::ID_LANGS]] = $ms['ID'];
			}
			if(isset($POST['home_desc']))
			{
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();
				foreach($langs as $key => $ms)
				{
					if(isset($POST['home_desc'][$key]))
					{
						if(isset($res[$key]))
						{
							$this->sql_add_data($POST['home_desc'][$key])->sql_using_user()->sql_save(self::UHD, $res[$key]);
						}
						else
						{
							$this->sql_add_data($POST['home_desc'][$key]+array(self::ID_LANGS => $key))->sql_using_user()->sql_save(self::UHD);
						}
					}
				}
			}
			
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			else
			{
				$this->messages->addErrorMassage('Системная ошибка!');
				return FALSE;
			}
			/*else
			{
				$this->db->where("`id_users`",$this->id_users);
				$this->db->delete("`".self::U_M_M."`");
				return TRUE;
			}*/	
	}
	
	public function change_position_module($id_module, $type)
	{
		if($type == 'up' || $type == 'down')
		{
			switch($type)
			{
				case "up":
					if($c_id = $this->changemodulePositionQuery('<=', $id_module))
					{
						return TRUE;
					}
					return FALSE;
				break;
				case "down":
					if($c_id = $this->changemodulePositionQuery('>=', $id_module))
					{
						return TRUE;
					}
					return FALSE;
				break;
			}
		}
		return true;
	}
	
	private function changemodulePositionQuery($type, $id_module)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	
				->select("A.`".self::ID_HM."`, A.`sort`")
				->from("`".self::HM."` AS A")
				->join(" `".self::HM."` AS B",
						"B.`".self::ID_HM."` = A.`".self::ID_HM."` && B.`id_m_menu` IS NULL && B.`sort` ".$type." (SELECT `sort` FROM `".self::HM."` WHERE `".self::ID_UM."` = ".$id_module." && `id_m_menu` IS NULL) &&  B.`".self::ID_USERS."` = ".$this->id_users, "inner")
				->order_by('sort', $OB)->limit(2);
		$query = $query->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			if($result[0][self::ID_HM] && $result[1][self::ID_HM])
			{
				$ID = $result[0][self::ID_HM];
				$SORT = $result[0]['sort'];
				
				$id = $result[1][self::ID_HM];
				$sort = $result[1]['sort'];

				$this->db->trans_start();
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::HM, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::HM, $ID);
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
	
	public function delete_menu_modul($id_module)
	{	
		$this->db->where(self::ID_UM , $id_module)->where("`id_m_menu` IS NULL", NULL, FALSE)->where(self::ID_USERS, $this->id_users)->delete(self::HM);
		return TRUE;
	}
}	