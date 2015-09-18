<?php
class Mzabava_catalogue extends AG_Model
{
	const MID = 3;
	
	const CAT 			= 'm_zabava_catalogue';
	const ID_CAT 		= 'id_m_zabava_catalogue';
	const CAT_DESCRIPTION = 'm_zabava_catalogue_description';
	const ID_CAT_DESCRIPTION = 'id_m_zabava_catalogue_description';
	
	const CP 			= 'm_zabava_catalogue_photos';
	const ID_CP 		= 'id_m_zabava_catalogue_photos';
	const CP_DESC 		= 'm_zabava_catalogue_photos_description';
	const ID_CP_DESC 	= 'id_m_zabava_catalogue_photos_description';
	
	const AD			= 'm_zabava_additional_block';
	const ID_AD			= 'id_m_zabava_additional_block';
	const AD_ALIAS		= 'm_zabava_additional_block_alias';
	const ID_AD_ALIAS	= 'id_m_zabava_additional_block_alias';
	
	private $segment = FALSE;
	const IMG_FOLDER = '/zabava_catalogue_album/';
	const FILE_FOLDER = '/zabava_catalogue_file/';
	private $img_path = FALSE;
	private $img_br_path = FALSE;
	private $file_path = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->segment = $this->uri->segment(self::MID);
		$this->img_path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER;
		$this->img_br_path = IMG_PATH.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER;
		$this->file_path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.self::FILE_FOLDER;
	}
	
	public function get_collection_to_html()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("zabava_catalogue_grid", array(), FALSE);
		
		$this->grid->db	->select("A.`".self::ID_CAT."` AS ID, A.`active`, B.`name`, A.`create_date`, A.`update_date`, A.`sort`")
						->from("`".self::CAT."` AS A")
						->join("`".self::CAT_DESCRIPTION."` AS B",
								"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = '1'",
								"LEFT")
						->where("A.`id_users_modules`", $this->segment)->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('zabava_catalogue/zabava_catalogue_helper');
		helper_zabava_catalogue_grid_build($this->grid);
		$this->grid->add_extra_sort('sort');
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->update_grid_data_using_string("sort", "<a class='arrow_down' href='".set_url('*/*/*/change_position/')."id/$1/type/down' title='Смена позиции: Опустить'></a><a class='arrow_up' href='".set_url('*/*/*/change_position/')."id/$1/type/up' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$this->grid->render_grid();			
	}
	
	public function save($id = FALSE)
	{
		if($id)
		{
			if($this->check_isset($id))
			{
				$this->db->trans_start();
				$data = $this->input->post('main');
				$this->sql_add_data($data)->sql_update_date()->sql_using_user()->sql_save(self :: CAT, $id);
				
				$POST = $this->input->post('langs');
				
				$query = $this->db->select(self::ID_CAT_DESCRIPTION.", ".self::ID_LANGS)
						->from(self::CAT_DESCRIPTION)
						->where(self::ID_CAT, $id);
				$result = $query->get()->result_array();		
				$desc_array = array();
				foreach($result as $ms)
				{
					$desc_array[$ms[self::ID_LANGS]] = $ms[self::ID_CAT_DESCRIPTION];
				}
				
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						if(isset($desc_array[$key]))
						{
							$data = $POST[$key];
							$this->sql_add_data($data)->sql_save(self :: CAT_DESCRIPTION, $desc_array[$key]);
						}
						else
						{
							$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_CAT => $id);
							$this->sql_add_data($data)->sql_save(self :: CAT_DESCRIPTION);
						}
					}
				}
				
				$query = $this->db->select(self::ID_AD.", ".self::ID_AD_ALIAS.", ".self::ID_LANGS)
						->from(self::AD)
						->where(self::ID_CAT, $id);
				$result = $query->get()->result_array();
				$additionl_array = array();
				foreach($result as $ms)
				{
					$additionl_array[$ms[self::ID_AD_ALIAS]][$ms[self::ID_LANGS]] = $ms[self::ID_AD];
				}
				
				
				$query = $this->db->select(self::ID_AD_ALIAS.", `alias`, `type`")
						->from(self::AD_ALIAS)->order_by("`sort`");
				$result = $query->get()->result_array();
				$ad_data = $this->input->post('additional');
				$original_files = $_FILES;
				foreach($result as $ms)
				{
					$data = $ad_data[$ms[self::ID_AD_ALIAS]];
					foreach($langs as $key => $ls)
					{
						if(isset($data[$key]))
						{
							if($ms[self::ID_AD_ALIAS] == 4)
							{
								if(isset($original_files['additional']['name'][4][$key]['data']) && is_uploaded_file($original_files['additional']['tmp_name'][4][$key]['data']) && $original_files['additional']['error'][4][$key]['data'] == 0)
								{
									$query = $this->db->select('data')
											->from(self::AD)
											->where(self::ID_CAT, $id)->where(self::ID_AD_ALIAS, 4)->where(self::ID_LANGS, $key)->limit(1);
									$F = $query->get()->row_array();
									if(count($F)>0)
									{
										@unlink($this->file_path.$id.'/'.$F['data']);
									}
									
									$_FILES = array(
										'additional' => array(
											'name' => $original_files['additional']['name'][4][$key]['data'],
											'type' => $original_files['additional']['type'][4][$key]['data'],
											'tmp_name' => $original_files['additional']['tmp_name'][4][$key]['data'],
											'error'	=> $original_files['additional']['error'][4][$key]['data'],
											'size' => $original_files['additional']['size'][4][$key]['data']
										)
									);
									$dir = $this->file_path.$id;
									$name = $key.'_'.$original_files['additional']['name'][4][$key]['data'];
									if(!is_dir($dir))
									{
										$this->load->helper('agfiles_helper');
										create_dir($dir, 2);
									}
									$config = array(
										'upload_path' => $dir,
										'allowed_types' => 'doc|docx|pdf',
										'max_size'	=> '15000',
										'file_name' => $name,
										'overwrite' => TRUE
									);
									$this->load->library('upload');
									$this->upload->initialize($config);
									if($this->upload->do_upload('additional'))
									{
										$fdata = $this->upload->data();
										if(isset($additionl_array[$ms[self::ID_AD_ALIAS]][$key]))
										{
											$a_data = array('data' => $fdata['file_name'], 'active' => $data[$key]['active']);
											$this->sql_add_data($a_data)->sql_save(self::AD, $additionl_array[$ms[self::ID_AD_ALIAS]][$key]);
										}
										else
										{
											$a_data = array('data' => $fdata['file_name'], 'active' => $data[$key]['active']) + array(self::ID_LANGS => $key, self::ID_CAT => $id, self::ID_AD_ALIAS => $ms[self::ID_AD_ALIAS]);
											$this->sql_add_data($a_data)->sql_save(self::AD);
										}
									}
									$this->load->library('upload', $config);
								}
							}
							else
							{
								if(isset($additionl_array[$ms[self::ID_AD_ALIAS]][$key]))
								{
									$a_data = $data[$key];
									$this->sql_add_data($a_data)->sql_save(self::AD, $additionl_array[$ms[self::ID_AD_ALIAS]][$key]);
								}
								else
								{
									$a_data = $data[$key] + array(self::ID_LANGS => $key, self::ID_CAT => $id, self::ID_AD_ALIAS => $ms[self::ID_AD_ALIAS]);
									$this->sql_add_data($a_data)->sql_save(self::AD);
								}
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
			return FALSE;
		}
		else
		{
			
			
			if(isset($_POST['main']['active']))
			{
				$this->db->trans_start();
				$data = $this->input->post('main');
				$ID = $this->sql_add_data($data + array('id_users_modules' => $this->segment))->sql_update_date()->sql_using_user()->sql_save(self::CAT);
				if($ID && $ID > 0)
				{
					$this->sql_add_data(array('sort' => $ID))->sql_save(self::CAT, $ID);
					$POST = $data = $this->input->post('langs');
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->get_active_languages();
					foreach($langs as $key => $ms)
					{
						if(isset($POST[$key]))
						{
							$data = $POST[$key] + array(self::ID_LANGS => $key) + array(self::ID_CAT => $ID);
							$this->sql_add_data($data)->sql_save(self::CAT_DESCRIPTION);
						}
					}
					
					$query = $this->db->select(self::ID_AD_ALIAS.", `alias`, `type`")
							->from(self::AD_ALIAS)->order_by("`sort`");
					$result = $query->get()->result_array();
					$ad_data = $this->input->post('additional');
					$original_files = $_FILES;
					foreach($result as $ms)
					{
						$data = $ad_data[$ms[self::ID_AD_ALIAS]];
						foreach($langs as $key => $ls)
						{
							if(isset($data[$key]))
							{
								if($ms[self::ID_AD_ALIAS] == 4)
								{
									if(isset($original_files['additional']['name'][4][$key]['data']) && is_uploaded_file($original_files['additional']['tmp_name'][4][$key]['data']) && $original_files['additional']['error'][4][$key]['data'] == 0)
									{
										$_FILES = array(
											'additional' => array(
												'name' => $original_files['additional']['name'][4][$key]['data'],
												'type' => $original_files['additional']['type'][4][$key]['data'],
												'tmp_name' => $original_files['additional']['tmp_name'][4][$key]['data'],
												'error'	=> $original_files['additional']['error'][4][$key]['data'],
												'size' => $original_files['additional']['size'][4][$key]['data']
											)
										);
										$dir = $this->file_path.$ID;
										$name = $key.'_'.$original_files['additional']['name'][4][$key]['data'];
										if(!is_dir($dir))
										{
											$this->load->helper('agfiles_helper');
											create_dir($dir, 2);
										}
										$config = array(
											'upload_path' => $dir,
											'allowed_types' => 'doc|docx|pdf',
											'max_size'	=> '15000',
											'file_name' => $name,
											'overwrite' => TRUE
										);
										$this->load->library('upload');
										$this->upload->initialize($config);
										if($this->upload->do_upload('additional'))
										{
											$fdata = $this->upload->data();
											$a_data = array('data' => $fdata['file_name'], 'active' => $data[$key]['active']) + array(self::ID_LANGS => $key, self::ID_CAT => $ID, self::ID_AD_ALIAS => $ms[self::ID_AD_ALIAS]);
											$this->sql_add_data($a_data)->sql_save(self::AD);
										}
										$this->load->library('upload', $config);
									}
								}
								else
								{
									$a_data = $data[$key] + array(self::ID_LANGS => $key, self::ID_CAT => $ID, self::ID_AD_ALIAS => $ms[self::ID_AD_ALIAS]);
									$this->sql_add_data($a_data)->sql_save(self::AD);
								}
							}
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
		$this->load->helper('zabava_catalogue/zabava_catalogue_helper');
		
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		helper_zabava_catalogue_form_build($data);
	}
	/*private function get_edit_query($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`".self::ID_LANGS."`, B.id_m_news_description AS DID";
		}
		else
		{
			$select = "A.`".self::ID_CAT."` AS ID, A.`active`, A.`url`, B.`name`, B.`short_description`, B.`full_description`, B.`".self::ID_LANGS."`, B.`id_m_news_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`";
		}
		$result = $this->db	->select($select)
							->from("`".self :: CAT."` AS A")
							->join("`".self :: CAT_DESCRIPTION."` AS B","B.`".self :: ID_CAT."` = '".$id."'","left")
							->where("A.`".self :: ID_CAT."`",$id)->where("A.`id_users`", $this->id_users);
		return $result;					
	}*/
	public function edit($id)
	{
		if($this->check_isset($id))
		{
			$query = $this->db->select("A.`".self::ID_CAT."` AS ID, A.`active`, A.`url`, B.`name`, B.`short_description`, B.`full_description`, B.`".self::ID_LANGS."`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`")
					->from("`".self::CAT."` AS A")
					->join("`".self::CAT_DESCRIPTION."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."`",
							"LEFT")
					->where("A.`".self::ID_CAT."`", $id);
			$result = $query->get()->result_array();
			$data = array();
			foreach($result as $ms)
			{
				$data['base']['main']['url'] 	= $ms['url'];
				$data['base']['main']['active'] = $ms['active'];
				$data['desc']['langs'][$ms['id_langs']] = $ms;
				unset($data['desc']['langs'][$ms['id_langs']]['date']);
				unset($data['desc']['langs'][$ms['id_langs']]['ID']);
				unset($data['desc']['langs'][$ms['id_langs']]['active']);
			}
			
			$query = $this->db->select("*")
					->from(self::AD)
					->where(self::ID_CAT, $id);
			$result = $query->get()->result_array();
			foreach($result as $ms)
			{
				$data['additional']['additional'][$ms[self::ID_AD_ALIAS]][$ms[self::ID_LANGS]] = array('active' => $ms['active'], 'data' => $ms['data']);
			}
			
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('zabava_catalogue/zabava_catalogue_helper');
			helper_zabava_catalogue_form_build($data, '/id/'.$id);
			
			return TRUE;
		}
		return FALSE;
	}
	
	public function delete($id)
	{
		$this->load->helper('agfiles_helper');
		if(is_array($id))
		{
			foreach($id as $ms)
			{
				$path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER.$ms;
				remove_dir($path);
			}
			$this->db->where_in(self::ID_CAT, $id)->where(self::ID_USERS, $this->id_users)->delete("`".self::CAT."`");
			return TRUE;
		}
		
		$path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER.$id;
		remove_dir($path);
			
		$this->db->where(self :: ID_CAT, $id)->where(self::ID_USERS, $this->id_users);
		if($this->db->delete(self::CAT))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset($id)
	{
		$query = $this->db	->select("COUNT(*) AS COUNT")
							->from("`".self::CAT."`")
							->where("`".self::ID_CAT."`", $id)
							->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
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
				$this->sql_add_data($data)->sql_save(self :: CAT, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
	
 	public function edit_photo($id)
	{
		$this->load->helper('zabava_catalogue/zabava_catalogue_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$data['id_users'] = $this->id_users;
		$query = $this->db	->select("A.*, A.`sort` AS SORT, B.`name`, B.`title`, B.`alt`, B.`".self::ID_LANGS."`, B.`".self::ID_CP_DESC."`")
							->from("`".self::CP."` AS A")
							->join("`".self::CP_DESC."` AS B", 
									"B.`".self::ID_CP."` = A.`".self::ID_CP."`",
									"LEFT")
							->where("A.`".self::ID_CAT."`", $id)
							->order_by("SORT");

		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$data['image'][$ms[self::ID_CP]] = array(self::ID_CP => $ms[self::ID_CP], 'image' => $this->img_br_path.$ms[self::ID_CAT].'/thumb_'.$ms['image']);
			$data['img_desc'][$ms[self::ID_CP]][$ms['id_langs']] = array(self::ID_CP_DESC => $ms[self::ID_CP_DESC], 'name' => $ms['name'], 'title' => $ms['title'], 'alt' => $ms['alt']);
		}
		
		helper_zabava_catalogue_photo_form($id, $data, $save_param = '/id/'.$id);
	}
	
	public function save_photo_desc($id)
	{
		$this->db->trans_start();
		if($this->input->post('img_desc'))
		{	
			$query = $this->db	->select("A.`".self::ID_CP."` AS ID , B.`".self::ID_CP_DESC."`, B.`".self::ID_LANGS."`")
								->from("`".self::CP."` AS A")
								->join(	"`".self::CP_DESC."` AS B",
										"B.`".self::ID_CP."` = A.`".self::ID_CP."`",
										"LEFT")
								->where("`".self::ID_CAT."`", $id);
			$result = $query->get()->result_array();
			
			$photos_desc_array = array();
			foreach($result as $ms)
			{
				if($ms[self::ID_CP_DESC] != NULL)
				{
					$photos_desc_array[$ms['ID']][$ms[self::ID_LANGS]] = $ms[self::ID_CP_DESC];
				}
				else
				{
					$photos_desc_array[$ms['ID']] = FALSE;
				}
			}
			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();
			$data_desc = $this->input->post('img_desc');

			foreach($photos_desc_array as $pkey => $ms)
			{
				foreach($langs as $key => $ls)
				{
					if(isset($data_desc[$pkey][$key]))
					{
						$data = $data_desc[$pkey][$key];
						if(isset($photos_desc_array[$pkey][$key]))
						{
							$this->sql_add_data($data)->sql_save(self::CP_DESC, $photos_desc_array[$pkey][$key]);
						}
						else
						{
							$data = $data + array(self::ID_CP => $pkey, self::ID_LANGS => $key);
							$this->sql_add_data($data)->sql_save(self::CP_DESC);
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
		return FALSE;
	}
	
	public function save_photo($id, $data)
	{
		$POST = array(self::ID_CAT => $id, 'image' => $data['file_name']);
		
		$this->db->trans_start();
		$ID = $this->sql_add_data($POST)->sql_save(self::CP);
		if($ID)
		{
			$this->sql_add_data(array('sort' => $ID))->sql_save(self::CP, $ID);
		}
		$this->db->trans_complete();
		if($this->db->trans_status()) 
		{
			return $ID; 
		}
		return FALSE;
	}
	
	private function get_upload_config($ID)
	{
		$dir = $this->img_path.$ID;
		if(!is_dir($dir))
		{
			$this->load->helper('agfiles_helper');
			create_dir($dir, 2);
		}
		$config['upload_path'] = $dir;
		$config['allowed_types'] = 'jpg|jpeg';
		$config['max_size']	= '4092';
		$config['encrypt_name'] = TRUE;
		
		return $config;
	}
	
	public function upload_photo($ID)
	{
		$config = $this->get_upload_config($ID);
		$this->load->library('upload', $config);
		if($this->upload->do_upload('Filedata'))
		{
			$file_data = $this->upload->data();
			$this->crop_img($config['upload_path'].'/'.$file_data['file_name']);
			
			if($img_id = $this->save_photo($ID, $file_data))
			{
				$this->load->helper('zabava_catalogue/zabava_catalogue_helper');
				$this->load->model('langs/mlangs');
				$data['on_langs'] = $this->mlangs->get_active_languages();
				
				$data['PID'] = $ID;
				$data['form_id'] = 'zabava_catalogue_form';
				$data['id_users'] = $this->id_users;
				$data['id'] = $img_id;
				$data['image'] = $this->img_br_path.$ID.'/thumb_'.$file_data['file_name'];
				$data['values'] = FALSE;
				$data['ajax'] = TRUE;
				
				echo json_encode(array('id' => $img_id, 'html' => helper_zabava_catalogue_photo_desc_form($data)));
				return TRUE;
			}
			return FALSE;
		}
		else
		{
			echo $this->upload->display_errors();
			return FALSE;
		}
	}
	
	public function crop_img($img)
	{
		//$this->load->model('mnews_settings');
		//$config = $this->mnews_settings->get_news_settings(TRUE);
		
		$Lconfig['source_image'] = $img;
		$Lconfig['width'] = 200;
		$Lconfig['height'] = 200;
		$Lconfig['create_thumb'] = TRUE;
		$Lconfig['quality'] = 90;
		
		$Bconfig['source_image'] = $img;
		$Bconfig['width'] = 1024;
		$Bconfig['height'] = 1024;
		$Bconfig['create_thumb'] = FALSE;
		$Bconfig['quality'] = 90;
		
		$this->load->library('image_lib', $Lconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		$this->image_lib->initialize($Bconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		
			$Cconfig['quality'] = 			90;
			$Cconfig['source_image'] = 		$img;
			$Cconfig['wm_vrt_alignment'] = 	"M";
			$Cconfig['wm_hor_alignment'] = 	"C";
			$Cconfig['wm_opacity'] = 		35;
			$Cconfig['wm_text'] = 			"zabava.bz";
			$Cconfig['wm_font_size'] = 		120;
			$Cconfig['wm_font_color'] = 	"#EEEEEE";
			$Cconfig['wm_shadow_color'] = 	"#000000";
			$Cconfig['wm_shadow_distance'] = "3";
			$Cconfig['wm_font_path'] = 		BASE_PATH.'fonts/TIMCYRB.TTF';
			
			$Cconfig['wm_padding'] = 		0;
			$Cconfig['wm_hor_offset'] = 	1;
			$Cconfig['wm_vrt_offset'] = 	1;
			
			$this->image_lib->initialize($Cconfig);
			$this->image_lib->watermark();
			$this->image_lib->clear();
	}
	
	public function change_position_photo($id, $type)
	{
		if($type == 'up' || $type == 'down')
		{
			switch($type)
			{
				case "up":
					if($c_id = $this->change_photo_position_query('<=', $id))
					{
						return TRUE;
					}
					return FALSE;
				break;
				case "down":
					if($c_id = $this->change_photo_position_query('>=', $id))
					{
						return TRUE;
					}
					return FALSE;
				break;
			}
		}
		return true;
	}
	
	private function change_photo_position_query($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db	->select("DISTINCT(A.`".self::ID_CP."`) AS ID, A.`sort` AS SORT, A.`".self::ID_CAT."` AS PARENT")
							->from("`".self::CP."` AS A")
							->where("A.`".self::ID_CAT."` = (SELECT `".self::ID_CAT."` FROM `".self::CP."` WHERE `".self::ID_CP."` = '".$id."' LIMIT 1)", NULL, FALSE)
							->where("A.`sort` ".$type." (SELECT `sort` FROM `".self::CP."` WHERE `".self::ID_CP."` = '".$id."' LIMIT 1)", NULL, FALSE)
							->order_by('sort',$OB)->limit(2);
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
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::CP, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::CP, $ID);
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
	
	public function delete_photo($id)
	{
		$query = $this->db->select("A.*")
				->from("`".self::CP."` AS A")
				->where("A.`".self::ID_CP."`", $id);
		$query = $query->get();
		if($query->num_rows()==1)
		{
			$result = $query->row_array();
			$file = $result['image'];
			$config = $this->get_upload_config($result[self::ID_CAT]);
			
			@unlink($config['upload_path'].'/'.$file);
			@unlink($config['upload_path'].'/'.'thumb_'.$file);
			$this->db->where(self::ID_CP, $id);
			$this->db->delete(self::CP);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function change_position($id, $type)
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
	
	protected function _change_position_query($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db
			->select("DISTINCT(A.`".self::ID_CAT."`) AS ID, A.`sort` AS SORT, A.`id_users_modules`")
			->from("`".self::CAT."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->where("A.`id_users_modules` <=> (SELECT `id_users_modules` FROM `".self::CAT."` WHERE `".self::ID_CAT."` = '".$id."' LIMIT 1) && A.`sort` ".$type." (SELECT `sort` FROM `".self::CAT."` WHERE `".self::ID_CAT."` = ".$id." LIMIT 1)")
			->order_by("A.`sort`", $OB)->limit(2);
		//echo $this->db->_compile_select();					

		$query = $query->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
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
}
?>