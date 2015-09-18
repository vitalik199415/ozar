<?php
class Msite_modules extends AG_Model
{
	const U_M = 'users_modules'; 
	const ID_U_M = 'id_users_modules'; 
	const M = 'modules';
	const ID_M = 'id_modules';
	const M_DESC = 'modules_description';
	
	
	private $id_lang = 1;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function getCollectionToSelect()
	{
		$query = $this->db->select('A.`'.self :: ID_M.'` AS ID, A.`alias`, B.`name`, A.`active`')	
						  ->from('`'.self :: M.'` AS A')
						  ->join('`'.self :: M_DESC.'` AS B', 'B.`'.self :: ID_M.'` = A.`'.self :: ID_M.'` && B.`'.self::ID_LANGS.'` = '.$this->id_langs, 'left')
						  ->where("A.`active`", 1);
						  
		$result = $query->get()->result_array();
		$return = array();
		foreach($result as $ms)
		{
			$return[$ms['ID']] = $ms['alias'].' - '.$ms['name']; 
		}
		return $return;	
	}
	
	public function getCollectionToHtml()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("products_attributes_grid", array(), FALSE);
		
		$this->grid->db	->select('A.`'.self :: ID_U_M.'` AS ID, A.`alias`, A.`active`, B.`alias` AS balias, C.`name`')
						->from('`'.self :: U_M.'` AS A')
						->join('`'.self :: M.'` AS B', 'B.`'.self :: ID_M.'` = A.`'.self :: ID_M.'`', 'left')
						->join('`'.self :: M_DESC.'` AS C', 'C.`'.self :: ID_M.'` = A.`'.self :: ID_M.'` && C.`'.self::ID_LANGS.'` = '.$this->id_langs, 'left')
						->where('A.`'.self::ID_USERS.'`', $this->id_users);
					
		$this->load->helper('site_modules/site_modules_helper');
		helper_site_modules_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
	}
	
	public function add()
	{
		$this->load->helper('site_modules/site_modules_helper');	
		$data['modules_list'] = $this->getCollectionToSelect();
		helper_site_modules_form_build($data);
	}
	
	public function save($id = false)
	{
		if($id)
		{
			if(isset($_POST['main']['alias']))
			{
				$this->db->trans_start();
				$result = $this->sql_add_data($_POST['main'])->sql_using_user()->sql_save(self::U_M, $id);
				if($result && $result > 0)
				{
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
		else
		{
			if(isset($_POST['main']['alias']))
			{
				$this->db->trans_start();
				$ID = $this->sql_add_data($_POST['main'])->sql_using_user()->sql_save(self::U_M);
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return $ID;
				}
				return false;
			}
		}
	}
	
	private function getEditQuery($id)
	{
		$result = $this->db	->select("A.`".self :: ID_U_M."` AS ID, A.`active`, A.`alias`")
							->from("`".self :: U_M."` AS A")
							->where("A.`".self :: ID_U_M."`",$id)->where("A.`".self::ID_USERS."`", $this->id_users);
		return $result;					
	}
	
	public function edit($id)
	{
		$result = $this->getEditQuery($id);
		$result = $result->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['base']['main']['active'] = $ms['active'];
				$data['base']['main']['alias'] = $ms['alias'];
			}
			$this->load->helper('site_modules/site_modules_helper');
			helper_site_modules_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}	
	
	public function delete($id)
	{
		$this->load->helper('agfiles_helper');
		if(is_array($id))
		{
			$this->db->where_in(self :: ID_U_M, $id)->where(self::ID_USERS, $this->id_users);
			$this->db->delete(self :: U_M);
			foreach($id as $ms)
			{
				$path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$ms;
				remove_dir($path);
			}
			return TRUE;
		}
		
		$path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$id;
		remove_dir($path);
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: U_M."` AS A")
							->where("A.`".self :: ID_U_M."`", $id)->where('A.`'.self::ID_USERS.'`', $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self :: ID_U_M, $id)->where(self::ID_USERS, $this->id_users);
			if($this->db->delete(self :: U_M))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function checkIssetModule($id)
	{
		$query = $this->db
				->select("A.`alias` AS alias, B.alias AS Malias")
				->from("`".self::U_M."` AS A")
				->join(	"`".self::M."` AS B", "B.`".self::ID_M."` = A.`".self::ID_M."`", "inner")
				->where("A.`".self::ID_U_M."`",$id)->where('A.`'.self::ID_USERS.'`',$this->id_users)->limit(1);
		$query = $query->get();
		if($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		return FALSE;
	}
	
	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $active);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_save(self :: U_M, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
}
?>