<?php
class Mtextpage extends AG_Model
{
	 const MID = 3;
 
	 const TEXTPAGE = 'm_textpage';
	 const ID_TEXTPAGE = 'id_m_textpage';
	 const TEXTPAGE_DESCRIPTION = 'm_textpage_description';
	 
	 private $segment = FALSE;
	 
	 function __construct()
	 {

	  $this->segment = $this->uri->segment(self::MID);
	  parent::__construct();
	 }
	 
	 public function getCollectionToHtml()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("textpage_grid", array(), FALSE);
		
		$this->grid->db	->select("A.`".self :: ID_TEXTPAGE."` AS ID, A.`active`, B.`name`, A.`show`, A.`create_date`, A.`update_date`")
				   		->from("`".self :: TEXTPAGE."` AS A")
				   		->join("`".self :: TEXTPAGE_DESCRIPTION."` AS B","B.`".self :: ID_TEXTPAGE."` = A.`".self::ID_TEXTPAGE."` && B.`".self :: ID_LANGS."` = '1'","left")
				   		->where("A.`id_users_modules`", $this->segment);
		
		$this->load->helper('textpage/textpage_helper');
		helper_textpage_grid_build($this->grid);		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('show',array('0'=>'Показывать только заголовок','1'=>'Показыть весь текст'));
		$this->grid->render_grid();			
	}
	
	public function save($id = FALSE)
	{
		if($id)
		{
			if(isset($_POST['main']['active']))
			{
				$database_data = $this->getEditQuery($id, TRUE);
				$database_data = $database_data->get()->result_array();
				
				if(count($database_data)>0)
				{
					foreach($database_data as $ms)
					{
						$database_data[$ms['id_langs']] = $ms;
					}
					
					$this->db->trans_start();
					$result = $this->sql_add_data($_POST['main'])->sql_update_date()->sql_using_user()->sql_save(self :: TEXTPAGE, $id);
					
					if($result && isset($_POST['langs']))
					{
						$POST = $_POST['langs'];
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								if(isset($POST[$key]['id_m_textpage_description']))
								{
									if(!isset($database_data[$key]))
									{
										$database_data[$key]['DID'] = 0;
									}
									$DID = intval($POST[$key]['id_m_textpage_description']);
									if($DID > 0 && $DID == $database_data[$key]['DID'])
									{
										$data = $POST[$key];
										$this->sql_add_data($data)->sql_save(self :: TEXTPAGE_DESCRIPTION, $DID);
									}
									else
									{
										$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_TEXTPAGE => $id);
										$this->sql_add_data($data)->sql_save(self :: TEXTPAGE_DESCRIPTION);
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
		}
		else
		{
			if(isset($_POST['main']['active']))
			{
				$this->db->trans_start();
				$ID = $this->sql_add_data($_POST['main']+array('id_users_modules' => $this -> segment))->sql_update_date()->sql_using_user()->sql_save(self :: TEXTPAGE);
				 $this->sql_add_data(array('sort' => $ID))->sql_save(self :: TEXTPAGE, $ID);
				if($ID && $ID > 0 && isset($_POST['langs']))
				{
					$POST = $_POST['langs'];
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->get_active_languages();
					foreach($langs as $key => $ms)
					{
						if(isset($POST[$key]))
						{
							$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_TEXTPAGE => $ID);
							$this->sql_add_data($data)->sql_save(self :: TEXTPAGE_DESCRIPTION);
						}
					}
					$this->db->trans_complete();
					if($this->db->trans_status()) 
					{
						return $ID;
					}
					return false;
				}
			}	
		}
	}
	
	public function add()
	{
		$this->load->helper('textpage/textpage_helper');
		
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		helper_textpage_form_build($data);
		
	}
	
	private function getEditQuery($id, $id_langs = FALSE)
	{
	
		if($id_langs)
		{
			$select = "B.`".self::ID_LANGS."`, B.id_m_textpage_description AS DID";
		}
		else
		{
			$select = "A.`".self :: ID_TEXTPAGE."` AS ID, A.`active`, A.`show`, B.`name`,  B.`text`, B.`".self :: ID_LANGS."`, B.`id_m_textpage_description`";
		}
		$result = $this->db	->select($select)
							->from("`".self :: TEXTPAGE."` AS A")
							->join("`".self :: TEXTPAGE_DESCRIPTION."` AS B","B.`".self :: ID_TEXTPAGE."` = '".$id."'","left")
							->where("A.`".self :: ID_TEXTPAGE."`",$id)->where("A.`".self::ID_USERS."`", $this->id_users);
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
				$data['base']['main']['show'] = $ms['show'];
				$data['desc']['langs'][$ms['id_langs']] = $ms;
				unset($data['desc']['langs'][$ms['id_langs']]['ID']);
				unset($data['desc']['langs'][$ms['id_langs']]['active']);
				unset($data['desc']['langs'][$ms['id_langs']]['show']);
			}

			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('textpage/textpage_helper');
			
			helper_textpage_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	
	public function delete($id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self :: ID_TEXTPAGE, $id)->where(self::ID_USERS, $this->id_users);
			$this->db->delete(self :: TEXTPAGE);
			return TRUE;
		}
		
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: TEXTPAGE."` AS A")
							->where("A.`".self :: ID_TEXTPAGE."`",$id)->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self :: ID_TEXTPAGE, $id)->where(self::ID_USERS, $this->id_users);
			if($this->db->delete(self :: TEXTPAGE))
			{
				return TRUE;
			}
			return FALSE;
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
				$this->sql_add_data($data)->sql_save(self :: TEXTPAGE, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
}
?>