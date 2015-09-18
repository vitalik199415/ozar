<?php
class Madmin_modules extends AG_Model
{
	const ADMIN = 'modules';
	const ID_ADMIN = 'id_modules';
	const ADMIN_DESCRIPTION = 'modules_description';
	const ID_ADMIN_DESCRIPTION = 'id_modules_description';
	
	private $id_lang = 1;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function getCollectionToHtml()
	{
		$users_rang = $this->session->userdata('rang');
		$this->load->helper('aggrid_helper');
		$Grid = new Aggrid_Helper('admin_modules_grid', false);
		
		$Grid->db	->select('A.`'.self :: ID_ADMIN.'` AS ID, A.`alias`, A.`active`, A.`rang`, B.`name`, B.`description`')
					->from('`'.self :: ADMIN.'` AS A')
					->join('`'.self :: ADMIN_DESCRIPTION.'` AS B', 'B.`'.self :: ID_ADMIN.'` = A.`'.self :: ID_ADMIN.'` && B.`id_langs` = '.$this->id_lang, 'left');
					
				
		$this->load->helper('admin_modules/admin_modules_helper');
		$Grid = admin_modules_grid_build($Grid);
		$Grid->createDataArray( );
		$Grid->updateGridValues('active', array("0"=>"Нет", "1"=>"Да"));
		$Grid->renderGrid( );
	}
	
	public function add()
	{
		$this->load->helper('agform_helper');
		$this->load->helper('admin_modules/admin_modules_helper');	
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->getUsersLanguages(TRUE);
		admin_modules_form_build($data);
	}
	
	public function save($id = false)
	{
		if ($id)
		{
			if (isset($_POST['main']['alias']));
			{
				$database_data = $this->getEditQuery($id, TRUE);
				$database_data = $database_data->get()->result_array();
				
				if (count($database_data)>0)
				{
					foreach	($database_data as $ms)
					{
						$database_data[$ms['id_langs']] = $ms;
					}
					$this->db->trans_start();
					$result = $this->_addData($_POST['main'])->_save(self :: ADMIN, $id);
					if($result && isset($_POST['langs']))
					{
						$POST = $_POST['langs'];
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->getUsersLanguages(TRUE);
						foreach ($langs as $key => $ms)
						{
							if (isset($POST[$key]))
							{
								if(isset($POST[$key]['id_modules_description'])) 
								{
									if(!isset($database_data[$key]))
									{
										$database_data[$key]['DID'] = 0;
									}	
									$DID = intval($POST[$key]['id_modules_description']);
									
									if($DID > 0 && $DID == $database_data[$key]['DID'])
									{
										$data = $POST[$key]; 
										$this->_addData($data)->_save(self :: ADMIN_DESCRIPTION, $DID);
									}
									else
									{
										$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_ADMIN => $id);
										$this->_addData($data)->_save(self :: ADMIN_DESCRIPTION);
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
				return false;
			}
		}
		else 
		{			
			if(isset($_POST['main']['alias']))
			{
				$this->db->trans_start();
				$ID = $this->_addData($_POST['main'])->_save(self :: ADMIN);
				if($ID && $ID > 0 && isset($_POST['langs']))
				{
					$this->_addData(array('rang' => $ID))->_save(self :: ADMIN, $ID);
					
					$POST = $_POST['langs'];
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->getUsersLanguages(TRUE);
					foreach($langs as $key => $ms)
					{
						if(isset($POST[$key]))
						{
							$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_ADMIN => $ID);
							$this->_addData($data)->_save(self :: ADMIN_DESCRIPTION);
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
	
	public function delete($id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self :: ID_ADMIN, $id);
			$this->db->delete(self :: ADMIN);
			return TRUE;
		}
		
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: ADMIN."` AS A")
							->where("A.`".self :: ID_ADMIN."`", $id);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self :: ID_ADMIN, $id);
			if($this->db->delete(self :: ADMIN))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	private function getEditQuery($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`id_langs`, B.`".self :: ID_ADMIN_DESCRIPTION."` AS DID";
		}
		else 
		{
			$select = "A.`".self :: ID_ADMIN."` AS id, A.`alias`, A.`active`, B.`name`, B.`description`, B.`id_langs`, B.`".self :: ID_ADMIN_DESCRIPTION."`, A.`rang`";
		}
	   	$result = $this->db -> select($select)
							-> from("`" .self :: ADMIN."` AS A")
							-> join("`" .self :: ADMIN_DESCRIPTION."` AS B", "B.`" .self :: ID_ADMIN."` = A.`" .self :: ID_ADMIN."`", "left")
							-> where("A.`".self :: ID_ADMIN."`", $id);
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
				$data['base']['main']['alias'] = $ms['alias'];
				$data['base']['main']['active'] = $ms['active'];
				$data['base']['main']['rang'] = $ms['rang'];
				$data['desc']['langs'][$ms['id_langs']] = $ms;
				unset($data['desc']['langs'][$ms['id_langs']]['ID']);
				unset($data['desc']['langs'][$ms['id_langs']]['alias']);
				unset($data['desc']['langs'][$ms['id_langs']]['active']);
				unset($data['desc']['langs'][$ms['id_langs']]['rang']);
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->getUsersLanguages(TRUE);
			$this->load->helper('agform_helper');
			$this->load->helper('admin_modules/admin_modules_helper');
			
			admin_modules_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}

}
?>