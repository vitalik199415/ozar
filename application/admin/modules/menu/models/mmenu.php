<?php
class Mmenu extends AG_Model
{
	const MENU 				= 'm_menu';
	const ID_MENU 			= 'id_m_menu';
	const MENU_DESC 			= 'm_menu_description';
	const ID_MENU_DESC 		= 'id_m_menu_description';
	const U_M_M 					='users_menu_modules';
	const ID_U_M_M                   ='id_users_menu_modules';
	const U_M                   ='users_modules';
	const ID_U_M                   ='id_users_modules';
	const MENU_LINK			= 'm_menu_link';
	
	private $tree_array = array();
	
	public $id_menu = FALSE;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_menu_grid()
	{
		$this->load->helper('aggrid_tree_helper');
		$Grid = new Aggrid_tree_Helper('catalogue_categories_grid');
		
		$Grid->db	->select("A.`".self::ID_MENU."` AS ID, A.`id_parent`, A.`url`,  A.`level`, A.`sort` AS sort, A.`active`, A.`clickable`, B.`name`,
					(SELECT COUNT(*) FROM `".self::MENU."` WHERE `id_parent` = A.`".self::ID_MENU."`) AS PARENT_COUNT")
					->from("`".self::MENU."` AS A")
					->join(	"`".self::MENU_DESC."` AS B",
							"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("sort");
		
		$this->load->helper('menu/menu_helper');
		
		$Grid = helper_menu_grid_build($Grid);
		$Grid->createDataArray();
		$Grid	->updateGridValues('active',array('0'=>'Нет', '1'=>'Да'))
				->updateGridValues('clickable',array('0'=>'Нет', '1'=>'Да'))
				->setGridValues("sort", "<a class='arrow_down' href='$1' title='Смена позиции: Опустить'></a><a class='arrow_up' href='$1' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$Grid->renderGrid();
	}
	
	public function get_menu_tree()
	{
		$query = $this	->db->select("A.`".self::ID_MENU."` AS ID, A.`id_parent`, A.`level`, B.`name`")
						->from("`".self::MENU."` AS A")
						->join(	"`".self::MENU_DESC."` AS B",
								"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`id_langs` = ".$this->id_langs,
								"left")
						->where('A.`id_users`',$this->id_users)
						->where('A.`level`' );
		$result = $query->get()->result_array();
		$array = array();
		$result_array = array();
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
		foreach($array[$K] as $key => $ms)
			{
				$this->tree_array[] = $ms;
				if(isset($array[$ms['ID']]))
				{
					$this->_recurs_tree($array, $ms['ID']);
				}
			}
	}
	
	public function isset_menu($id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")
							->from("`".self::MENU."`")
							->where("`".self::ID_MENU."`", $id)
							->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function add()
	{
		$query = $this->db->select("MAX(A.`level`) AS MAX")
				->from("`".self::MENU."` AS A")
				->where("A.`".self::ID_USERS."`", $this->id_users)
									->where("A.`level` < 3");
		$result = $query->get()->row_array();
		$data['data_max_level'][1] = 1;
		
		for($i = 2; $i <= $result['MAX']; $i++)
		{
			$data['data_max_level'][$i] = $i;
		}
		
		$query = $this->db->select("A.`".self::ID_MENU."` AS ID, A.`level`, B.`name`")
				->from("`".self::MENU."` AS A")
				->join(	"`".self::MENU_DESC."` AS B",
						"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->where("A.`id_parent`", NULL);
		$query = $query->get();
		$data['data_parents'] = array();
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $ms)
			{
				$data['data_parents'][$ms['ID']] = 'ID: '.$ms['ID'].' - '.$ms['name'];
			}
		}
		$this->add_edit_base($data);
	}
	
	public function edit($id)
	{
		$data = array();
		$query = $this->db->select("A.`id_parent`, A.`level`, A.`active`, A.`clickable`, A.`url`,  B.*")
				->from("`".self::MENU."` AS A")
				->join(	"`".self::MENU_DESC."` AS B",
						"B.`".self::ID_MENU."` = A.`".self::ID_MENU."`",
						"left")
				->where("A.`".self::ID_MENU."`", $id)
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		if(count($result))
		{
			foreach($result as $ms)
			{
				$data['menu']['menu'] = array(
					'id_parent' 		=> $ms['id_parent'],
					'level' 			=> $ms['level']-1,
					'active' 			=> $ms['active'],
					'clickable' 		=> $ms['clickable'],
					'url' 				=> $ms['url']
				);
				$data['menu_desc']['menu_desc'][$ms['id_langs']] = $ms;
				unset($data['menu_desc']['menu_desc'][$ms['id_langs']]['id_parent']);
				unset($data['menu_desc']['menu_desc'][$ms['id_langs']]['level']);
				unset($data['menu_desc']['menu_desc'][$ms['id_langs']]['active']);
				unset($data['menu_desc']['menu_desc'][$ms['id_langs']]['clickable']);
			}
			
			$query = $this->db->select("MAX(A.`level`) AS MAX")
					->from("`".self::MENU."` AS A")
					->where("A.`".self::ID_USERS."`", $this->id_users)
					->where("A.`".self::ID_MENU."` <>", $id);
			$result = $query->get()->row_array();
			$data['data_max_level'][1] = 1;
			
			for($i = 2; $i <= $result['MAX']; $i++)
			{
				$data['data_max_level'][$i] = $i;
			}
			
			$query = $this->db->select("A.`".self::ID_MENU."` AS ID, A.`level`, B.`name`")
					->from("`".self::MENU."` AS A")
					->join(	"`".self::MENU_DESC."` AS B",
							"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`", $this->id_users)
					->where("A.`".self::ID_MENU."` <>", $id);
			if($data['menu']['menu']['id_parent'] == NULL)
			{
				$query = $query->where("A.`level`", 1);
			}
			else
			{
				$query = $query->where("A.`level`", $data['menu']['menu']['level']);
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
		$this->load->helper('menu/menu_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		menu_form_build($data, $path);
	}
	
	public function check_isset_url($url)
	{
		$url = trim($url);
		if($url == '') return TRUE;
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::MENU."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`url`", $url)->limit(1);
		if($this->id_menu)
		{
			$query->where("`".self::ID_MENU."` <>", $this->id_menu);
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
		$this->form_validation->set_rules('menu[url]','Сермент URL','trim|check_isset_ulr');
		$this->form_validation->set_message('check_isset_url', 'Меню с указанным сегментом URL уже существует!');
	}
	
	public function save($id = FALSE)
	{
		if($this->input->post('menu'))
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
					$POST = $this->input->post('menu');
					unset($POST['level']);
					if(strlen(trim($POST['url'])) < 4) $POST['url'] = 'menu_'.$id;
					
					$query = $this->db->select("`id_parent` AS id_parent, `level`")
								->from("`".self::MENU."`")
								->where("`".self::ID_MENU."`", $id)
								->where("`".self::ID_USERS."`", $this->id_users);
								
					$query = $query->get();
					
					if($query->num_rows() == 1)
					{
						/*$result = $query->row_array();
					
							$result = $query->row_array();
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
											->select("A.`".self::ID_MENU."` AS ID, A.`level`")
											->from("`".self::MENU."` AS A")
											->where("A.`".self::ID_MENU."`", $id_parent)
											->where("A.`".self::ID_MENU."` <>", $id)
											->where("A.`".self::ID_USERS."`", $this->id_users);
								$query = $query->get();
								
								if($query->num_rows() == 1)
								{
									$presult = $query->row_array();
									$POST['id_parent'] = $presult['ID'];
									$POST['level'] = $presult['level'] + 1;
								}
								else
								{
									return FALSE;
								}
							}
							else		
							{
								unset($POST['id_parent']);
							}*/
						
						$this->db->trans_start();
						$this->sql_add_data($POST)->sql_update_date()->sql_using_user()->sql_save(self::MENU, $id);
						
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						
						//DESC
						if(($POST = $this->input->post('menu_desc')) != FALSE)
						{	
							$query = $this->db->select("A.`".self::ID_MENU."`, B.`".self::ID_MENU_DESC."`, B.`".self::ID_LANGS."`")
									->from("`".self::MENU."` AS A")
									->join(	"`".self::MENU_DESC."` AS B",
											"B.`".self::ID_MENU."` = A.`".self::ID_MENU."`",
											"left")
									->where("A.`".self::ID_MENU."`", $id);
							$result = $query->get()->result_array();
							$menu_desc_data = array();
							
							foreach($result as $ms)
							{
								$menu_desc_data[$ms[self::ID_LANGS]] = $ms;
							}
							
							foreach($langs as $key => $ms)
							{
								if(isset($POST[$key]))
								{
									
									if(isset($POST[$key][self::ID_MENU_DESC]) && isset($menu_desc_data[$key]) && $menu_desc_data[$key][self::ID_MENU_DESC] == $POST[$key][self::ID_MENU_DESC])
									{
										$data = $POST[$key];
										$this->sql_add_data($data)->sql_save(self::MENU_DESC, $POST[$key][self::ID_MENU_DESC]);
										
									}
									else if(!isset($menu_desc_data[$key]))
									{
										$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_MENU => $id);
										$this->sql_add_data($data)->sql_save(self::MENU_DESC);
										
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
							$this->set_post_to_session();
							return FALSE;
						}
					}
					return FALSE;
				}
				else
				{
					$POST = $this->input->post('menu');
					unset($POST['level']);
					if(isset($POST['id_parent']))
					{
						if(($id_parent = intval($POST['id_parent']))>0)
						{
							$query = $this->db
										->select("A.`".self::ID_MENU."` AS ID, A.`level`")
										->from("`".self::MENU."` AS A")
										->where("A.`".self::ID_MENU."`", $id_parent)
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
										->select("A.`id_parent`, A.`level`")
										->from("`".self::MENU_LINK."` AS A")
										->where("A.`".self::ID_MENU."`", $POST['id_parent']);
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
								unset($POST['id_parent']);
								$POST['level'] = 1;
								$result_link[] = array('level' => 1); 
							}
						}
						else
						{
							unset($POST['id_parent']);
							$POST['level'] = 1;
							$result_link[] = array('level' => 1);
						}
					}
					else
					{
						$result_link[] = array('level' => 1); 
					}
					$this->db->select("MAX(`sort`) AS MAX")->from("`".self::MENU."`")->where("`".self::ID_USERS."`", $this->id_users);
					$max_sort = $this->db->get()->row_array();
					$max_sort = $max_sort['MAX'];
					if(is_null($max_sort)) $max_sort = 1; else $max_sort++;
					if(strlen(trim($POST['url'])) < 4) $POST['url'] = 'menu_'.$max_sort;

					$this->db->trans_start();
					$ID = $this->sql_add_data($POST + array('sort' => $max_sort))->sql_update_date()->sql_using_user()->sql_save(self::MENU);
					if($ID && $ID > 0)
					{
						if(isset($result_link))
						{
							foreach($result_link as $li)
							{
								$this->sql_add_data(array(self::ID_MENU => $ID)+$li)->sql_save(self::MENU_LINK);
							}
						}
						if(($POST = $this->input->post('menu_desc')) != FALSE)
						{
							$this->load->model('langs/mlangs');
							$langs = $this->mlangs->get_active_languages();
							
							foreach($langs as $key => $ms)
							{
								if(isset($POST[$key]))
								{
									$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_MENU => $ID);
									$this->sql_add_data($data)->sql_save(self::MENU_DESC); 
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
	
	public function set_post_to_session()
	{
		$this->session->set_flashdata('menu_add_edit_form', $this->input->post());
		return $this;
	}
	
	public function load_menu($level, $id = FALSE)
	{
		$query = $this->db->select("A.`".self::ID_MENU."` AS ID, A.`level`, B.`name`")
				->from("`".self::MENU."` AS A")
				->join(	"`".self::MENU_DESC."` AS B",
						"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"left")
				->where("A.`id_users`", $this->id_users)
				->where("A.`level`" , $level);

		if($id)
		{
			$query = $query->where("A.`".self::ID_MENU."` <>", $id);
		}
		$query = $query->get();
		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $ms)
			{
				$data[$ms['ID']] = 'ID: '.$ms['ID'].' - '.$ms['name'];
			}
		}
		$this->load->helper('menu/menu_helper');
		load_menu($data);
	}
	
	public function activate($id, $activate = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $activate); 
			foreach($id as $ms) 
			{
				$this->sql_add_data($data)->sql_save(self::MENU, $ms); 
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function delete($id, $type = FALSE)
	{
		if(is_array($id))
		{
			$this->db->where_in(self::ID_MENU, $id)->where(self::ID_USERS, $this->id_users);  
			$this->db->delete(self::MENU);
			return TRUE;
		}
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self::MENU."` AS A")
							->where("A.`".self::ID_MENU."`", $id)-> where("`A.`".self::ID_USERS."`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self::ID_MENU, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self::MENU))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function change_position($type, $id)
	{
		switch($type)
		{
			case "up":
				if($c_id = $this->_change_position_query('<=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
			case "down":
				if($c_id = $this->_change_position_query('>=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
		}
		return FALSE;
	}
	private function _change_position_query($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	->select("DISTINCT(A.`".self::ID_MENU."`) AS ID, A.`sort` AS SORT, A.`id_parent` AS PARENT")
							->from("`".self::MENU."` AS A")
							->where("A.`".self::ID_USERS."`", $this->id_users)
							->where("A.`id_parent` <=> (SELECT `id_parent` FROM `".self::MENU."` WHERE `".self::ID_MENU."` = '".$id."' LIMIT 1) && A.`sort` ".$type." (SELECT `sort` FROM `".self::MENU."` WHERE `".self::ID_MENU."` = ".$id." LIMIT 1)")
							->order_by('sort', $OB)->limit(2);
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
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::MENU, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::MENU, $ID);
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
	
	
	public function change_position_module($id, $id_module, $type)
	{
		if($type == 'up' || $type == 'down')
		{
			switch($type)
			{
				case "up":
					if($c_id = $this->_change_module_position_query('<=', $id, $id_module))
					{
						return TRUE;
					}
					return FALSE;
				break;
				case "down":
					if($c_id = $this->_change_module_position_query('>=', $id, $id_module))
					{
						return TRUE;
					}
					return FALSE;
				break;
			}
		}
		return true;
	}
	
	private function _change_module_position_query($type, $id, $id_module)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	->select("A.`".self::ID_U_M_M."`, A.`sort`")
									->from("`".self::U_M_M."` AS A")
									->join(" `".self::U_M_M."` AS B",
										"B.`".self::ID_U_M_M."` = A.`".self::ID_U_M_M."` && B.`".self::ID_MENU."` = '".$id."' && B.`sort` ".$type." (SELECT `sort` FROM `".self::U_M_M."` WHERE `".self::ID_MENU."` = ".$id." && `".self::ID_U_M."` = ".$id_module.") &&  B.`".self::ID_USERS."` = ".$this->id_users, "inner")
								    ->order_by('sort', $OB)->limit(2);
		//echo $query->_compile_select();
		$query = $query->get();
		
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			if($result[0][self::ID_U_M_M] && $result[1][self::ID_U_M_M])
			{
				$ID = $result[0][self::ID_U_M_M];
				$SORT = $result[0]['sort'];
				
				$id = $result[1][self::ID_U_M_M];
				$sort = $result[1]['sort'];

				$this->db->trans_start();
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::U_M_M, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::U_M_M, $ID);
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
	
	public function menu_modules($id)
	{
		$query = $this->db
			->select("B.`name`")
			->from("`".self::MENU."` AS A")
			->join(	"`".self::MENU_DESC."` AS B",
					"B.`".self::ID_MENU."` = A.`".self::ID_MENU."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"inner")
			->where("A.`".self::ID_MENU."`",$id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		$menu = $query->get()->row_array();
		$menu = $menu['name'];
		
		$this->template->add_title(' - '.$menu);
		$this->template->add_navigation($menu);
		
		$data = array();
		$query = $this->db->select("A.*, A.`".self::ID_U_M."` AS ID, B.base_module, B.`sort` AS SORT")
				->from("`".self::U_M."` AS A")
				->join(	"`".self::U_M_M."` AS B",
							"B.`".self::ID_U_M."` = A.`".self::ID_U_M."` && B.`".self::ID_MENU."` = '".$id."'",
							"left")
				->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("B.`sort`")->order_by("ID");
		$result = $query->get()->result_array();
		
		foreach($result as $ms)
		{
			if($ms['SORT'] == NULL) 
			{
				$data['checkbox'][$ms[self::ID_U_M]] = array(self::ID_U_M => $ms[self::ID_U_M], 'alias' => $ms['alias']);
			}
			else
			{
				$data['checkbox_checked'][$ms[self::ID_U_M]] = $ms;
			}
		}
		/*		
		$result = $query->get()->result_array();
		
		foreach($result as $ms)
		{
			$data['checkbox'][$ms[self::ID_U_M]] = array(self::ID_U_M => $ms[self::ID_U_M], 'alias' => $ms['alias']);
		}
		
		$query = $this->db->select("A.*")
				->from("`users_menu_modules` AS A")
				->where("A.`".self::ID_MENU."`", $id)
				->where("A.`id_users`", $this->id_users)->order_by('sort');
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$temp_ch[$ms[self::ID_U_M]] = $ms[self::ID_U_M];
				//$data['checkbox_checked'][self::ID_U_M][$ms[self::ID_U_M]] = $ms[self::ID_U_M];
			}
			$temp = $data['checkbox'];
			$data['checkbox'] = array();
			foreach($temp as $ms)
			{
				if(isset($temp_ch[$ms[self::ID_U_M]]))
				{
					$data['checkbox_checked'][$ms[self::ID_U_M]] = $ms;
				}
				else
				{
					 $data['checkbox'][$ms[self::ID_U_M]] = $ms;
				}
			}
		}
		
		*/
		$this->load->helper('menu/menu_helper');
		helper_menu_modules_form($data, $id);
	}
	
	public function modules_save($id) 
	{
		$POST = $this->input->post();
		if(isset($POST['save']))
		{
			if(isset($POST[self::ID_U_M]) && count($POST[self::ID_U_M]) > 0)
			{
				$base_module = 0;
				$query = $this->db
						->select("COUNT(*) AS COUNT")
						->from("`".self::U_M_M."`")
						->where("`".self::ID_MENU."`", $id)->where("`base_module`", 1)->where("`".self::ID_USERS."`", $this->id_users);
				$result = $query->get()->row_array();
				if($result['COUNT'] == 0)
				{
					$base_module = 1;
				}
				$this->db->trans_start();
				foreach($POST[self::ID_U_M] as $key => $ms)
				{
					$data = array(self::ID_U_M => $ms, self::ID_MENU=>$id, 'base_module' => $base_module);
					$ID = $this->sql_add_data($data)->sql_using_user()->sql_save(self::U_M_M);
					$data = array('sort'=>$ID);
					$this->sql_add_data($data)->sql_save(self::U_M_M, $ID);
					$base_module = 0;
				}
				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return TRUE;
				}
				else
				{
					$this->messages->addErrorMassage('Ошибка сохранения!');
					return FALSE;
				}
			}
			return FALSE;
		}	
		return FALSE;
	}
	
	public function change_base_module($id_menu, $id)
	{
		$this->sql_add_data(array('base_module' => '0'))->sql_using_user()->sql_save(self::U_M_M, array(self::ID_MENU => $id_menu));
		$this->sql_add_data(array('base_module' => '1'))->sql_using_user()->sql_save(self::U_M_M, array(self::ID_MENU => $id_menu, self::ID_U_M => $id));
		echo "Основной модуль изменен";
	}
	
	public function delete_menu_modul($id, $id_module)
	{	
		$this->db->where(self::ID_U_M , $id_module)->where(self::ID_USERS, $this->id_users)->where(self::ID_MENU, $id)->delete(self::U_M_M);
		return TRUE;
	}

}