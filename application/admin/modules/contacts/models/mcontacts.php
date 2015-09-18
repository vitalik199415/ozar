<?php
class Mcontacts extends AG_Model
{
	const MID 			= 3;
	
	const CONTACTS		= 'm_contacts';	
	const ID_CONTACTS	= 'id_m_contacts';
	const CONTACTS_DESC = 'm_contacts_description';
	const ID_CONTACTS_DESC = 'id_m_contacts_description';

	private $segment = FALSE;
		function __construct()
		{
			$this->segment = $this->uri->segment(self :: MID);
			parent::__construct();
		}
		
		public function get_collection()
		{
			$this->load->library("grid");
			$this->grid->_init_grid("contacts_grid", array(), FALSE);
			
			$this->grid->db	->select("A.`".self :: ID_CONTACTS."` AS ID, A.`email`, A.`active`, A.`show_form`, B.`name`")
							->from("`".self :: CONTACTS."` AS A")
							->join("`".self :: CONTACTS_DESC."` AS B",
									"B.`".self :: ID_CONTACTS."` = A.`".self :: ID_CONTACTS."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
									"LEFT")
							->where("A.`id_users_modules`", $this->segment)->where("A.`id_users`", $this->id_users);;
			
			$this->load->helper('contacts/contacts_helper');
			helper_contacts_grid_build($this->grid);	
			$this->grid->create_grid_data();
			$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
			$this->grid->update_grid_data('show_form', array('0'=>'Нет', '1'=>'Да'));
			$this->grid->render_grid();				
		}
		
	public function add()
	{
		$this->load->helper('contacts/contacts_helper');
		
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		helper_contacts_form_build($data);	
	}
	
	public function edit($id)
	{
		$result = $this->get_edit_query($id);
		$result = $result->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['base']['main']['email'] = $ms['email'];
				$data['base']['main']['active'] = $ms['active'];
				$data['base']['main']['show_form'] = $ms['show_form'];
				$data['base']['langs'][$ms['id_langs']] = $ms;
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('contacts/contacts_helper');
			
			helper_contacts_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	
	protected function get_edit_query($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`id_langs`, B.`id_m_contacts_description`";
		}
		else
		{
			$select = "A.`".self::ID_CONTACTS."` AS ID, A.`email`, A.`show_form`, A.`active`, B.`name`, B.`text`, B.`id_langs`, B.`id_m_contacts_description`";
		}
		$query = $this->db	->select($select)
							->from("`".self::CONTACTS."` AS A")
							->join("`".self::CONTACTS_DESC."` AS B",
									"B.`".self :: ID_CONTACTS."` = A.`".self :: ID_CONTACTS."`",
									"LEFT")
							->where("A.`".self::ID_CONTACTS."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
		return $query;
	}
	
	public function save($id = FALSE)
	{
		if($id)
		{
			$POST = $this->input->post();
			if(isset($POST['main']['email']))
			{
				$database_data = $this->get_edit_query($id, TRUE);
				$database_data = $database_data->get()->result_array();
				
				if(count($database_data)>0)
				{
					foreach($database_data as $ms)
					{
						$database_data[$ms['id_langs']] = $ms;
					}
					$this->db->trans_start();
					$result = $this->sql_add_data($POST['main'])->sql_using_user()->sql_save(self :: CONTACTS, $id);
					if($result && isset($POST['langs']))
					{
						$POSTD = $POST['langs'];
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POSTD[$key]))
							{
								if(isset($POSTD[$key][self::ID_CONTACTS_DESC]))
								{
									if(!isset($database_data[$key]))
									{
										$database_data[$key][self::ID_CONTACTS_DESC] = 0;
									}
									$DID = intval($POSTD[$key][self::ID_CONTACTS_DESC]);
									if($DID > 0 && $DID == $database_data[$key][self::ID_CONTACTS_DESC])
									{
										$data = $POSTD[$key];
										$this->sql_add_data($data)->sql_save(self::CONTACTS_DESC, $DID);
									}
									else
									{
										$data = $POSTD[$key] + array('id_langs' => $key, self::ID_CONTACTS => $id);
										$this->sql_add_data($data)->sql_save(self :: CONTACTS_DESC);
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
			$POST = $this->input->post();
			if(isset($POST['main']['email']))
			{
				$this->db->trans_start();
				$ID = $this->sql_add_data($POST['main']+array('id_users_modules' => $this->segment))->sql_using_user()->sql_save(self::CONTACTS);
				if($ID && $ID > 0 && isset($POST['langs']))
				{
					$this->sql_add_data(array('sort' => $ID))->sql_save(self::CONTACTS, $ID);
					$POSTD = $POST['langs'];
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->get_active_languages();
					foreach($langs as $key => $ms)
					{
						if(isset($POSTD[$key]))
						{
							$data = $POSTD[$key] + array('id_langs' => $key, self::ID_CONTACTS => $ID);
							$this->sql_add_data($data)->sql_save(self::CONTACTS_DESC);
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
			$this->db->where_in(self :: ID_CONTACTS, $id)->where('id_users', $this->id_users);
			$this->db->delete(self :: CONTACTS);
			return TRUE;
		}
		
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: CONTACTS."` AS A")
							->where("A.`".self :: ID_CONTACTS."`",$id)->where("A.`id_users`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self :: ID_CONTACTS, $id)->where('id_users', $this->id_users);
			if($this->db->delete(self :: CONTACTS))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function set_active($id, $active = 1)
	{
		$data = array('active' => $active);
		if(is_array($id))
		{
			$act_array = array();
			foreach($id as $ms)
			{
				if(($u_id = intval($ms))>0)
				{
					$act_array[] = $u_id;
				}
			}
			if(count($act_array)>0)
			{
				$query = $this->db->where_in(self::ID_CONTACTS, $act_array);
			}	
		}
		else
		{
			if(($u_id = intval($id))>0)
			{
				$query = $this->db->where(self::ID_CONTACTS, $u_id);
			}
		}
		$query->where("`".self::ID_USERS."`", $this->id_users)->update(self::CONTACTS, $data);
	}
}
?>