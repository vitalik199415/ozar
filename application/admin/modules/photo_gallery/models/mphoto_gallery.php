<?php
class Mphoto_gallery extends AG_Model
{
	const MID 			= 3;
	const GALL 			= 'm_photo_gallery_albums';
	const ID_GALL 		= 'id_m_photo_gallery_albums';
	const GALL_DESC 	= 'm_photo_gallery_albums_description';
	
	const PGP			= 'm_photo_gallery_photos';
	const ID_PGP		= 'id_m_photo_gallery_photos';
	const PGP_DESC		= 'm_photo_gallery_photos_description';
	const ID_PGP_DESC	= 'id_m_photo_gallery_photos_description';
	
	private $segment = FALSE;
	
	const IMG_FOLDER = '/photo_album/';
	private $img_path = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		$this->segment = $this->uri->segment(self :: MID);
		$this->img_path = IMG_PATH.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER;
	}
	
	public function render_albums()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("photo_gallery_grid", array(), FALSE);
		
		$this->grid->db		->select("A.`".self :: ID_GALL."` AS ID, A.`active`, B.`name`, A.`create_date`, A.`update_date`")
							->from("`".self :: GALL."` AS A")
							->join("`".self :: GALL_DESC."` AS B","B.`".self :: ID_GALL."` = A.`".self :: ID_GALL."` && B.`".self::ID_LANGS."` = '1'","left")
							->where("A.`id_users_modules`", $this->segment);
		
		$this->load->helper('photo_gallery/photo_gallery_helper');
		helper_photo_gallery_grid_build($this->grid);	
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
		
	}
	public function add()
	{
		$this->load->helper('photo_gallery/photo_gallery_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		helper_photo_gallery_form_build($data);	
	}
	
	private function getEditQuery($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`".self::ID_LANGS."`, B.id_m_photo_gallery_albums_description AS DID";
		}
		else
		{
			$select = "A.`".self :: ID_GALL."` AS ID, A.`active`, A.`url`, A.`create_date`, B.`name`, B.`short_description`, B.`full_description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`, B.`".self::ID_LANGS."`, B.`id_m_photo_gallery_albums_description`";
		}
		$result = $this->db	->select($select)
							->from("`".self :: GALL."` AS A")
							->join("`".self :: GALL_DESC."` AS B","B.`".self :: ID_GALL."` = '".$id."'","left")
							->where("A.`".self :: ID_GALL."`",$id)->where("A.`".self::ID_USERS."`", $this->id_users);
		return $result;					
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
					$result = $this->sql_add_data($_POST['main'])->sql_update_date()->sql_using_user()->sql_save(self :: GALL, $id);
					if($result && isset($_POST['langs']))
					{
						$POST = $_POST['langs'];
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								if(isset($POST[$key]['id_m_photo_gallery_albums_description']))
								{
									if(!isset($database_data[$key]))
									{
										$database_data[$key]['DID'] = 0;
									}
									$DID = intval($POST[$key]['id_m_photo_gallery_albums_description']);
									if($DID > 0 && $DID == $database_data[$key]['DID'])
									{
										$data = $POST[$key];
										$this->sql_add_data($data)->sql_save(self :: GALL_DESC, $DID);
									}
									else
									{
										$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_GALL => $id);
										$this->sql_add_data($data)->sql_save(self :: GALL_DESC);
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
				$ID = $this->sql_add_data($_POST['main']+array('id_users_modules' => $this->segment))->sql_update_date()->sql_using_user()->sql_save(self :: GALL);
				if($ID && $ID > 0 && isset($_POST['langs']))
				{
					$this->sql_add_data(array('sort' => $ID))->sql_save(self :: GALL, $ID);
					$POST = $_POST['langs'];
					$this->load->model('langs/mlangs');
					$langs = $this->mlangs->get_active_languages();
					foreach($langs as $key => $ms)
					{
						if(isset($POST[$key]))
						{
							$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_GALL => $ID);
							$this->sql_add_data($data)->sql_save(self :: GALL_DESC);
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
	
	public function edit($id)
	{
		$result = $this->getEditQuery($id);
		$result = $result->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['base']['main']['create_date'] = $ms['create_date'];
				$data['base']['main']['url'] = $ms['url'];
				$data['base']['main']['active'] = $ms['active'];
				$data['desc']['langs'][$ms['id_langs']] = $ms;
				$data['seo']['langs'][$ms['id_langs']] = $ms;
				unset($data['desc']['langs'][$ms['id_langs']]['create_date']);
				unset($data['desc']['langs'][$ms['id_langs']]['ID']);
				unset($data['desc']['langs'][$ms['id_langs']]['active']);
				unset($data['seo']['langs'][$ms['id_langs']]['seo']);
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('photo_gallery/photo_gallery_helper');
			
			helper_photo_gallery_form_build($data, '/id/'.$id);
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
			$this->db->where_in(self::ID_GALL, $id)->where(self::ID_USERS, $this->id_users)->delete("`".self::GALL."`");
			return TRUE;
		}
		
		$path = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.self::IMG_FOLDER.$id;
		remove_dir($path);
			
		$this->db->where(self :: ID_GALL, $id)->where(self::ID_USERS, $this->id_users);
		if($this->db->delete(self :: GALL))
		{
			return TRUE;
		}
		return FALSE;
	}
		
		
	/*------------------ ФОТОГРАФИИ----------------------*/	
	
	
	public function check_isset_album($id)
	{
		$query = $this->db	->select("COUNT(*) AS COUNT")
							->from("`".self::GALL."`")
							->where("`".self::ID_GALL."`", $id)
							->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
		
	public function edit_photo($id)
	{
		$this->load->helper('photo_gallery/photo_gallery_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$data['id_users'] = $this->id_users;
		$query = $this->db	->select("A.*, A.`sort` AS SORT, B.`name`, B.`title`, B.`alt`, B.`".self::ID_LANGS."`, B.`".self::ID_PGP_DESC."`")
							->from("`".self::PGP."` AS A")
							->join("`".self::PGP_DESC."` AS B",	"B.`".self::ID_PGP."` = A.`".self::ID_PGP."`", "left")
							->where("A.`".self::ID_GALL."`", $id)
							->order_by("SORT");

		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$data['image'][$ms[self::ID_PGP]] = array(self::ID_PGP => $ms[self::ID_PGP], 'image' => $this->img_path.$ms[self::ID_GALL].'/thumb_'.$ms['image']);
			$data['img_desc'][$ms[self::ID_PGP]][$ms['id_langs']] = array(self::ID_PGP_DESC => $ms[self::ID_PGP_DESC], 'name' => $ms['name'], 'title' => $ms['title'], 'alt' => $ms['alt']);
		}
		
		helper_photo_gallery_form($id, $data, $save_param = '/id/'.$id);
	}
	
	public function save_photo_desc($id)
	{
		$this->db->trans_start();
		if(isset($_POST['img_desc']))
		{
			$IPOST = $_POST['img_desc'];
			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();
			
			$query = $this->db	->select("A.`".self::ID_PGP."` AS ID , B.`".self::ID_PGP_DESC."`, B.`".self::ID_LANGS."`")
								->from("`".self::PGP."` AS A")
								->join(	"`".self::PGP_DESC."` AS B", "B.`".self::ID_PGP."` = A.`".self::ID_PGP."`",	"left")
								->where("`".self::ID_GALL."`", $id);
					
			$result = $query->get()->result_array();
			if(count($result)>0)
			{
				$images = array();
				foreach($result as $ms)
				{
					$images[$ms['ID']][$ms['id_langs']] = array('ID' => $ms['ID'], self::ID_PGP_DESC => $ms[self::ID_PGP_DESC], 'id_langs' => $ms['id_langs']);
				}
				
				foreach($IPOST as $ikey => $ims)
				{	
					if(isset($images[$ikey]))
					{
						$POST = $ims;
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{	
								if(isset($POST[$key][self::ID_PGP_DESC]) && intval($POST[$key][self::ID_PGP_DESC])>0 && isset($images[$ikey][$key][self::ID_PGP_DESC]) && $images[$ikey][$key][self::ID_PGP_DESC] == $POST[$key][self::ID_PGP_DESC])
								{
									$data = $POST[$key];
									$this->sql_add_data($data)->sql_save(self::PGP_DESC, $POST[$key][self::ID_PGP_DESC]);
								}
								else if(!isset($images[$ikey][$key][self::ID_PGP_DESC]))
								{
									$data = $POST[$key] + array('id_langs' => $key) + array(self::ID_PGP => $ikey);
									$this->sql_add_data($data)->sql_save(self::PGP_DESC);
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
			}
		}	
		return FALSE;
	}
	
	public function save_photo($id, $data)
	{
		$POST = array(self::ID_GALL => $id, 'image' => $data['file_name']);
		
		$this->db->trans_start();
		$ID = $this->sql_add_data($POST)->sql_save(self::PGP);
		if($ID)
		{
			$this->sql_add_data(array('sort' => $ID))->sql_save(self::PGP, $ID);
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
		$dir = BASE_PATH.'users/'.$this->id_users.'/media/module_'.$this->segment.'/photo_album/'.$ID;
		if(!is_dir($dir))
		{
			$this->load->helper('agfiles_helper');
			create_dir($dir, 2);
		}
		$config['upload_path'] = $dir.'/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['max_size']	= '11000';
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
			$this->crop_img($config['upload_path'].$file_data['file_name']);
			
			if($img_id = $this->save_photo($ID, $file_data))
			{
				$this->load->helper('photo_gallery/photo_gallery_helper');
				$this->load->model('langs/mlangs');
				$data['on_langs'] = $this->mlangs->get_active_languages();
				
				$data['PID'] = $ID;
				$data['form_id'] = 'photo_gallery_form';
				$data['id_users'] = $this->id_users;
				$data['id'] = $img_id;
				$data['image'] = $this->img_path.$ID.'/thumb_'.$file_data['file_name'];
				$data['values'] = FALSE;
				$data['ajax'] = TRUE;
				
				echo json_encode(array('id' => $img_id, 'html' => helper_photo_desc_form($data), 'files' => [$data]));
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
		$this->load->model('mphoto_gallery_settings');
		$config = $this->mphoto_gallery_settings->get_photo_gallery_settings(TRUE);
		
		$Lconfig['source_image'] = $img;
		$Lconfig['width'] = $config['img_width_thumbs'];
		$Lconfig['height'] = $config['img_height_thumbs'];
		$Lconfig['create_thumb'] = TRUE;
		$Lconfig['quality'] = $config['img_quality'];
		
		$Bconfig['source_image'] = $img;
		$Bconfig['width'] = $config['img_width'];
		$Bconfig['height'] = $config['img_height'];
		$Bconfig['create_thumb'] = FALSE;
		$Bconfig['quality'] = $config['img_quality'];
		
		$this->load->library('image_lib', $Lconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		$this->image_lib->initialize($Bconfig);
		$this->image_lib->resize();
		$this->image_lib->clear();
		if($config['img_wm'])
		{
			$Cconfig['quality'] = 			$config['img_quality'];
			$Cconfig['source_image'] = 		$img;
			$Cconfig['wm_vrt_alignment'] = 	$config['img_wm_valign'];
			$Cconfig['wm_hor_alignment'] = 	$config['img_wm_align'];
			$Cconfig['wm_opacity'] = 		$config['img_wm_opacity'];
			$Cconfig['wm_text'] = 			$config['img_wm_text'];
			$Cconfig['wm_font_size'] = 		$config['img_wm_text_size'];
			$Cconfig['wm_font_color'] = 	$config['img_wm_text_color'];
			$Cconfig['wm_shadow_color'] = 	$config['img_wm_text_shadow_color'];
			$Cconfig['wm_shadow_distance'] = $config['img_wm_text_shadow_padding'];
			$Cconfig['wm_font_path'] = 		BASE_PATH.'fonts/TIMCYRB.TTF';
			
			$Cconfig['wm_padding'] = 		0;
			$Cconfig['wm_hor_offset'] = 	1;
			$Cconfig['wm_vrt_offset'] = 	1;
			
			$this->image_lib->initialize($Cconfig);
			$this->image_lib->watermark();
			$this->image_lib->clear();
		}
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
		$query = $this->db	->select("DISTINCT(A.`".self::ID_PGP."`) AS ID, A.`sort` AS SORT, A.`".self::ID_GALL."` AS PARENT")
							->from("`".self::PGP."` AS A")
							->join(	"`".self::GALL."` AS B",
									"A.`".self::ID_GALL."` = B.`".self::ID_GALL."` && A.`sort` ".$type." (SELECT `sort` FROM `".self::PGP."` WHERE `".self::ID_PGP."` = ".$id." LIMIT 1)"
							)
							->where("A.`".self::ID_GALL."` = (SELECT `".self::ID_GALL."` FROM `".self::PGP."` WHERE `".self::ID_PGP."` = ".$id." LIMIT 1)")
							->order_by('sort', $OB)->limit(2);
		$query = $query->get();
		
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();

			$ID = $result[0]['ID'];
			$SORT = $result[0]['SORT'];

			$id = $result[1]['ID'];
			$sort = $result[1]['SORT'];

			$this->db->trans_start();
			$this->sql_add_data(array('sort' => $SORT))->sql_save(self::PGP, $id);
			$this->sql_add_data(array('sort' => $sort))->sql_save(self::PGP, $ID);
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}


			return FALSE;
		}
		return FALSE;
	}
	
	public function delete_photo($id)
	{
		$query = $this->db->select("A.*")
				->from("`".self::PGP."` AS A")
				->where("A.`".self::ID_PGP."`", $id);
		$query = $query->get();
		if($query->num_rows()==1)
		{
			$result = $query->row_array();
			$file = $result['image'];
			$config = $this->get_upload_config($result[self::ID_GALL]);
			
			@unlink($config['upload_path'].$file);
			@unlink($config['upload_path'].'thumb_'.$file);
			$this->db->where(self::ID_PGP, $id);
			$this->db->delete(self::PGP);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $active);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_save(self :: GALL, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
	
}
?>