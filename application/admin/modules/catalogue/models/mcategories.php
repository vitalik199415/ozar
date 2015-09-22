<?php
class Mcategories extends AG_Model
{
	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';
	const CAT_LINK			= 'm_c_categories_link';
	const CAT_PERM			= 'm_c_categories_permissions';
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
		
		$Grid->db	->select("A.`".self::ID_CAT."` AS ID, A.`id_parent`, A.`level`, A.`sort` AS sort, A.`active`, A.`show`, A.`create_date`, A.`update_date`, B.`name`, 
							(SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS PARENT_COUNT")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`",$this->id_users)->order_by('sort');
					
		$this->load->helper('categories');
		
		$Grid = categories_grid_build($Grid);
		$Grid->createDataArray();
		$Grid	->updateGridValues('active', array('0'=>'Нет', '1'=>'Да'))->updateGridValues('show', array('0'=>'Нет', '1'=>'Да'))
				->setGridValues("sort", "<a class='arrow_down' href='$1' title='Смена позиции: Опустить'></a><a class='arrow_up' href='$1' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$Grid->renderGrid();
	}
	
	public function get_categories_tree()
	{
		$this->load->model('sys/madmins');
		$cat_perm = $this->madmins->get_cat_perm();

		$query = $this	->db->select("A.`".self::ID_CAT."` AS ID, A.`id_parent`, A.`level`, B.`name`")
						->from("`".self::CAT."` AS A")
						->join(	"`".self::CAT_DESC."` AS B",
								"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
								"left")
						->where("A.`".self::ID_USERS."`",$this->id_users);
		/*if($cat_perm) $query->where_in("A.`".self::ID_CAT."`", $cat_perm)->or_where_in("A.`id_parent`", $cat_perm);*/
		$result = $query->get()->result_array();
		$array = array();
		$result_array = array();

		/*if($cat_perm) {
			foreach($result as $key => $ms) {
				if (!in_array($ms['ID'], $cat_perm)) {
					unset($result[$key]);
				}
			}
		}*/

		foreach($result as $ms)
		{
			if($ms['id_parent'] == NULL)
			{
				$array[0][] = $ms;
			}
			else
			{
				$array[$ms['id_parent']][] = $ms;
			}
		}
		
		$this->_recurs_tree($array);
		return $this->tree_array;
	}
	
	private function _recurs_tree($array, $K = 0)
	{
		if(isset($array[$K]))
		{
			foreach($array[$K] as $key => $ms)
			{
				$this->tree_array[] = $ms;
				if(isset($array[$ms['ID']]))
				{
					$this->_recurs_tree($array, $ms['ID']);
				}
			}
		}	
	}
	
	public function add()
	{
		$query = $this->db->select("MAX(A.`level`) AS MAX")
				->from("`".self::CAT."` AS A")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		$data['data_max_level'][1] = 1;
		
		for($i = 2; $i <= $result['MAX']; $i++)
		{
			$data['data_max_level'][$i] = $i;
		}
		
		$query = $this->db->select("A.`".self::ID_CAT."` AS ID, A.`level`, B.`name`")
				->from("`".self::CAT."` AS A")
				->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->where("A.`id_parent`", NULL);
		$query = $query->get();
		$data['data_parents'] = array();
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $ms)
			{
				$data['data_parents'][$ms['ID']] = 'ID:'.$ms['ID'].' - '.$ms['name'];
			}
		}
		$this->add_edit_base($data);
	}
	
	public function edit($id)
	{
		$data = array();
		$query = $this->db->select("A.`id_parent`, A.`level`, A.`active`, A.`open`, A.`show`, A.`url`, A.`permission`, B.*")
				->from("`".self::CAT."` AS A")
				->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
						"left")
				->where("A.`".self::ID_CAT."`", $id)
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		if(count($result))
		{
			foreach($result as $ms)
			{
				$data['categories']['categories'] = array(
					'id_parent' 		=> $ms['id_parent'],
					'level' 			=> $ms['level']-1,
					'active' 			=> $ms['active'],
					'show' 			=> $ms['show'],
					'open' 			=> $ms['open'],
					'url' 				=> $ms['url']
				);
				$data['categories_desc']['categories_desc'][$ms[self::ID_LANGS]] = $ms;
				unset($data['categories_desc']['categories_desc'][$ms[self::ID_LANGS]]['id_parent']);
				unset($data['categories_desc']['categories_desc'][$ms[self::ID_LANGS]]['level']);
				unset($data['categories_desc']['categories_desc'][$ms[self::ID_LANGS]]['active']);
				unset($data['categories_desc']['categories_desc'][$ms[self::ID_LANGS]]['url']);

				$data['categories']['permissions']['permission'] = $ms['permission'];
			}

			if($data['categories']['permissions']['permission'] == 2){
				$this->db->select("`id_m_u_types`")
					->from("`".self::CAT_PERM."`")
					->where("`".self::ID_CAT."`", $id);
				foreach($this->db->get()->result_array() as $ms){
					$data['categories']['permissions']['m_u_types'][$ms['id_m_u_types']] = $ms['id_m_u_types'];
				}
			}
			
			$query = $this->db->select("MAX(A.`level`) AS MAX")
					->from("`".self::CAT."` AS A")
					->where("A.`".self::ID_USERS."`", $this->id_users)
					->where("A.`".self::ID_CAT."` <>", $id);
			$result = $query->get()->row_array();
			$data['data_max_level'][1] = 1;
			
			for($i = 2; $i <= $result['MAX']; $i++)
			{
				$data['data_max_level'][$i] = $i;
			}
			
			$query = $this->db->select("A.`".self::ID_CAT."` AS ID, A.`level`, B.`name`")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`", $this->id_users)
					->where("A.`".self::ID_CAT."` <>", $id);
			if($data['categories']['categories']['id_parent'] == NULL)
			{
				$query = $query->where("A.`level`", 1);
			}
			else
			{
				$query = $query->where("A.`level`", $data['categories']['categories']['level']);
			}
			
			$query = $query->get();
			$data['data_parents'] = array();
			if($query->num_rows()>0)
			{
				foreach($query->result_array() as $ms)
				{
					$data['data_parents'][$ms['ID']] = 'ID: '.$ms['ID'].' - '.$ms['name'];
				}
			}	
			
			$this->add_edit_base($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	
	private function add_edit_base($data = array(), $path = '')
	{
		$this->load->helper('categories');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();

		$this->load->model('customers/mcustomers_types');
		$data['data_customers_types'] = $this->mcustomers_types->get_customers_types();
		
		helper_categories_form_build($data, $path);
	}
	
	public function check_isset_url($url)
	{
		$url = trim($url);
		if($url == '') return TRUE;
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::CAT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`url`", $url)->limit(1);
		if($this->id_categorie)
		{
			$query->where("`".self::ID_CAT."` <>", $this->id_categorie);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function set_validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('categories[url]','Сермент URL','trim|check_isset_ulr');
		$this->form_validation->set_message('check_isset_url', 'Категория с указанным сегментом URL уже существует!');
	}
	
	public function save($id = FALSE)
	{
		if($this->input->post('categories'))
		{
			if($id)
			{
				$this->id_categorie = $id;
			}
			
			$this->set_validation();
			if($this->form_validation->run())
			{
				if($id)
				{
					$POST = $this->input->post('categories');

					$perm_data = $this->prepare_save_permissions($this->input->post('permissions'));
					//echo var_dump($perm_data);
					//exit;

					unset($POST['level']);
					if(trim($POST['url']) == '') $POST['url'] = NULL;
					
					$query = $this->db->select("`id_parent` AS id_parent")
							->from("`".self::CAT."`")
							->where("`".self::ID_CAT."`", $id)
							->where("`".self::ID_USERS."`", $this->id_users);
					$query = $query->get();

					if($query->num_rows() == 1)
					{
						/*$result = $query->row_array();
						if(intval($POST['id_parent'])>0)
						{
							$POST['id_parent'] = intval($POST['id_parent']);
						}
						else
						{
							$POST['id_parent'] = NULL;
							$POST['level'] = 1;
						}
						
						$id_parent = $POST['id_parent'];
						if($id_parent != $result['id_parent'])
						{
							$query = $this->db
									->select("A.`".self::ID_CAT."` AS ID, A.`level`")
									->from("`".self::CAT."` AS A")
									->where("A.`".self::ID_CAT."`", $id_parent)
									->where("A.`".self::ID_CAT."` <>", $id)
									->where("A.`".self::ID_USERS."`", $this->id_users);
							$query = $query->get();
							if($query->num_rows() == 1)
							{
								$presult = $query->row_array();
								$POST['id_parent'] = $presult['ID'];
								$POST['level'] = $presult['level'] + 1;
								
								if($POST['level']>2)
								{
									$query = $this->db
										->select("A.`id_parent`, A.`level`")
										->from("`".self::CAT_LINK."` AS A")
										->where("A.`".self::ID_CAT."`", $POST['id_parent']);
									$result_link = $query->get()->result_array();
									$result_link[] = array('id_parent' => $POST['id_parent'], 'level' => $POST['level']); 
								}
								else
								{
									$result_link[] = array('level' => $POST['level']); 
									$result_link[] = array('id_parent' => $POST['id_parent'], 'level' => $POST['level']); 
								}
							}
							else
							{
								$POST['id_parent'] = NULL;
								$POST['level'] = 1;
								$result_link[] = array('level' => 1);
							}
							$this->db->where(self::ID_CAT, $id)->delete(self::CAT_LINK);
						}
						else		
						{
							unset($POST['id_parent']);
						}
						*/
						$this->db->trans_start();
						/*if(isset($result_link))
						{
							foreach($result_link as $li)
							{
								$this->sql_add_data(array(self::ID_CAT => $id)+$li)->sql_save(self::CAT_LINK);
							}
						}*/

						if($perm_data['permission'] == 2 && count($perm_data['m_u_types']) > 0)
						{
							$temp_group = array();
							$this->db->select("`id_m_u_types`")
								->from("`".self::CAT_PERM."`")
								->where("`".self::ID_CAT."`", $id);
							$group = $this->db->get()->result_array();
							foreach($group as $ms){
								$temp_group[$ms['id_m_u_types']] = $ms['id_m_u_types'];
							}

							foreach($perm_data['m_u_types'] as $ms){
								if(isset($temp_group[$ms])){
									unset($temp_group[$ms]);
								}
								else{
									$this->sql_add_data(array(self::ID_CAT => $id, 'id_m_u_types' => $ms))->sql_save(self::CAT_PERM);
								}
							}
							if(count($temp_group) > 0){
								$this->db->where_in("id_m_u_types", $temp_group)->where("`".self::ID_CAT ."`", $id)->delete(self::CAT_PERM);
								
							}
						}
						else
						{
							$perm_data['permission'] = 0;
							$this->db->where("`".self::ID_CAT."`", $id)->delete(self::CAT_PERM);
						}

						$this->sql_add_data($POST + array('permission' => $perm_data['permission']))->sql_update_date()->sql_using_user()->sql_save(self::CAT, $id);

						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						
						//DESC
						if($POST = $this->input->post('categories_desc'))
						{
							$query = $this->db->select("A.`".self::ID_CAT."`, B.`".self::ID_CAT_DESC."`, B.`".self::ID_LANGS."`")
									->from("`".self::CAT."` AS A")
									->join(	"`".self::CAT_DESC."` AS B",
											"B.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
											"left")
									->where("A.`".self::ID_CAT."`", $id);
							$result = $query->get()->result_array();
							$categories_desc_data = array();
							foreach($result as $ms)
							{
								$categories_desc_data[$ms[self::ID_LANGS]] = $ms;
							}
							
							foreach($langs as $key => $ms)
							{
								if(isset($POST[$key]))
								{
									if(isset($POST[$key][self::ID_CAT_DESC]) && isset($categories_desc_data[$key]) && $categories_desc_data[$key][self::ID_CAT_DESC] == $POST[$key][self::ID_CAT_DESC])
									{
										$data = $POST[$key];
										$this->sql_add_data($data)->sql_save(self::CAT_DESC, $POST[$key][self::ID_CAT_DESC]);
									}
									else if(!isset($categories_desc_data[$key]))
									{
										$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_CAT => $id);
										$this->sql_add_data($data)->sql_save(self::CAT_DESC);
									}
								}
							}
						}
						//--------------------------
						
						$this->db->trans_complete();
						if($this->db->trans_status()) 
						{
							return TRUE; 
						}
						else
						{
							$this->set_post_to_session();
							return FALSE;
						}
					}
					return FALSE;
				}
				else
				{
					$POST = $this->input->post('categories');
					unset($POST['level']);
					if(trim($POST['url']) == '') $POST['url'] = NULL;
					if(isset($POST['id_parent']))
					{
						if(($id_parent = intval($POST['id_parent']))>0)
						{
							$query = $this->db
										->select("A.`".self::ID_CAT."` AS ID, A.`level`")
										->from("`".self::CAT."` AS A")
										->where("A.`".self::ID_CAT."`", $id_parent)
										->where("A.`".self::ID_USERS."`", $this->id_users);
							$query = $query->get();
							if($query->num_rows() == 1)
							{
								$result = $query->row_array();
								$POST['id_parent'] = $result['ID'];
								$POST['level'] = $result['level'] + 1;
								
								if($POST['level']>2)
								{
									$query = $this->db
										->select("A.`id_parent`")
										->from("`".self::CAT_LINK."` AS A")
										->where("A.`".self::ID_CAT."`", $POST['id_parent']);
									$result_link = $query->get()->result_array();
									$result_link[] = array('id_parent' => $POST['id_parent']); 
								}
								else
								{
									$result_link[] = array(); 
									$result_link[] = array('id_parent' => $POST['id_parent']); 
								}
							}
							else
							{
								unset($POST['id_parent']);
								$POST['level'] = 1;
								$result_link[] = array(); 
							}
						}
						else
						{
							unset($POST['id_parent']);
							$POST['level'] = 1;
							$result_link[] = array(); 
						}
					}
					else
					{
						$result_link[] = array(); 
					}

					$perm_data = $this->prepare_save_permissions($this->input->post('permissions'));

					$this->db->trans_start();
					$ID = $this->sql_add_data($POST + array('permission' => $perm_data['permission']))->sql_update_date()->sql_using_user()->sql_save(self::CAT);
					if($ID && $ID > 0)
					{
						$this->sql_add_data(array('sort' => $ID))->sql_save(self::CAT, $ID);
						if(isset($result_link))
						{
							foreach($result_link as $li)
							{
								$this->sql_add_data(array(self::ID_CAT => $ID)+$li)->sql_using_user()->sql_save(self::CAT_LINK);
							}
						}

						foreach($perm_data['m_u_types'] as $ms)
						{
							$this->sql_add_data(array(self::ID_CAT => $ID, 'id_m_u_types' => $ms))->sql_save(self::CAT_PERM);
						}

						if($POST = $this->input->post('categories_desc'))
						{
							$this->load->model('langs/mlangs');
							$langs = $this->mlangs->get_active_languages();
							
							foreach($langs as $key => $ms)
							{
								if(isset($POST[$key]))
								{
									$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_CAT => $ID);
									$this->sql_add_data($data)->sql_save(self::CAT_DESC); 
								}
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status()) 
						{
							return $ID; 
						}
						else
						{
							$this->set_post_to_session();
							return FALSE;
						}
					}
					return FALSE;
				}
			}
			else
			{
				$this->messages->add_error_message(validation_errors());
				$this->set_post_to_session();
				return FALSE;
			}
		}
	return FALSE;
	}

	protected function prepare_save_permissions($data)
	{
		$return_data = array();
		$return_data['permission'] = $data['permission'];
		$return_data['m_u_types'] = array();
		if($data['permission'] == 2)
		{
			if(isset($data['m_u_types']) && is_array($data['m_u_types']))
			{
				$return_data['m_u_types'] = $data['m_u_types'];
			}
			else
			{
				$return_data['permission'] = 1;
			}
		}
		return $return_data;
	}
	
	public function set_post_to_session()
	{
		$this->session->set_flashdata('categories_add_edit_form', $this->input->post());
		return $this;
	}
	
	public function load_categories($level, $id = FALSE)
	{
		$query = $this->db->select("A.`".self::ID_CAT."` AS ID, A.`level`, B.`name`")
				->from("`".self::CAT."` AS A")
				->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->where("A.`level`", $level)->order_by('sort');
		if($id)
		{
			$query = $query->where("A.`".self::ID_CAT."` <>", $id);
		}
		$query = $query->get();
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $ms)
			{
				$data[$ms['ID']] = $ms['ID'].' - '.$ms['name'];
			}
		}
		$this->load->helper('agform_helper');
		$this->load->helper('categories');
		
		helper_load_categories($data);
	}
	
	public function activate($id, $activate = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $activate); 
			foreach($id as $ms) 
			{
				$this->sql_add_data($data)->sql_save(self::CAT, $ms); 
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function delete($id, $type = FALSE)
	{
		if(is_array($id))
		{
			$this->db->where_in(self::ID_CAT, $id)->where("`".self::ID_USERS."`",$this->id_users);  
			$this->db->delete(self::CAT);
			return TRUE;
		}
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self::CAT."` AS A")
							->where("A.`".self::ID_CAT."`", $id)-> where("`A.`".self::ID_USERS."`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self::ID_CAT, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self::CAT))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function changePosition($type, $id)
	{
		switch($type)
		{
			case "up":
				if($c_id = $this->changePositionQuery('<=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
			case "down":
				if($c_id = $this->changePositionQuery('>=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
		}
		return FALSE;
	}
	private function changePositionQuery($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	->select("DISTINCT(A.`".self::ID_CAT."`) AS ID, A.`sort` AS SORT, A.`id_parent` AS PARENT")
							->from("`".self::CAT."` AS A")
							/*->join(	"`".self::CAT."` AS B",
									"A.`id_parent` = B.`id_parent` && A.`sort` ".$type." (SELECT `sort` FROM `".self::CAT."` WHERE `".self::ID_CAT."` = ".$id." LIMIT 1) && A.`id_users` = ".$this->id_users
							)*/
							->where("A.`".self::ID_USERS."`", $this->id_users)
							->where("A.`id_parent` <=> (SELECT `id_parent` FROM `".self::CAT."` WHERE `".self::ID_CAT."` = '".$id."' LIMIT 1) && A.`sort` ".$type." (SELECT `sort` FROM `".self::CAT."` WHERE `".self::ID_CAT."` = ".$id." LIMIT 1)")
							->order_by('sort',$OB)->limit(2);
		//echo $this->db->_compile_select();					

		$query = $query->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			if($result[0]['PARENT'] == $result[1]['PARENT'])
			{
				$ID = $result[0]['ID'];
				$SORT = $result[0]['SORT'];
				
				$id = $result[1]['ID'];
				$sort = $result[1]['SORT'];

				$this->db->trans_start();
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::CAT, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::CAT, $ID);
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
}