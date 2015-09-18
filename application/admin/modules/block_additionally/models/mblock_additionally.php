<?php
class Mblock_additionally extends AG_Model
{
	const BLOCK 		= 'users_block_additionally';
	const ID_BLOCK 		= 'id_users_block_additionally';
	
	private $segment = FALSE;
	
	private $tree_array = array();
		
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_collection_to_html($block_id)
	{
		$this->load->library("grid");
		$this->grid->_init_grid("block_additionally_grid_".$block_id, array(), FALSE);
		
		$this->grid->db	->select("`".self :: ID_BLOCK."` AS ID, `alias`, `active`, `sort`")
						->from("`".self :: BLOCK."`")
						->where(self::ID_USERS, $this->id_users)->where('block', $block_id);
		
		$this->load->helper('block_additionally/block_additionally_helper');
		helper_block_additionally_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->render_grid();				
	}
	
	public function add($block_id)
	{
		$this->load->helper('block_additionally/block_additionally_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		if($block_id == 1)
		{
			$data['base']['main']['code'] = '<div class="inline_block">

</div>';
		}
		helper_block_additionally_form_build($data);
	}
	
	public function edit($block_id, $id)
	{
		$query = $this->db->select("`".self::ID_BLOCK."` AS ID, `active`, `alias`, `code`")
					->from("`".self::BLOCK."`")
					->where("`".self::ID_BLOCK."`",$id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['base']['main']['active'] = $ms['active'];
				$data['base']['main']['alias'] = $ms['alias'];
				$data['base']['main']['code'] = $ms['code'];
			}
			$this->load->helper('block_additionally/block_additionally_helper');
			helper_block_additionally_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	public function save($block_id, $id = FALSE)
	{
		$POST = $this->input->post('main');
		if($POST && isset($POST['active']))
		{
			if($id)
			{
				$this->db->trans_start();
				$result = $this->sql_add_data($POST + array('block' => $block_id))->sql_using_user()->sql_save(self::BLOCK, $id);				
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return TRUE;
				}
				return FALSE;
			}
			else
			{
				$this->db->trans_start();
				$ID = $this->sql_add_data($POST + array('block' => $block_id))->sql_using_user()->sql_save(self::BLOCK);
				$this->sql_add_data(array('sort' => $ID))->sql_save(self::BLOCK, $ID);
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return $ID;
				}
				return FALSE;
			}
		}
		return FALSE;
	}	
	
	public function delete($block_id, $id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self::ID_BLOCK, $id)->where(self::ID_USERS, $this->id_users);
			if($this->db->delete(self::BLOCK))
			{
				return TRUE;
			}
			return FALSE;
		}
				
		$this->db->where(self::ID_BLOCK, $id)->where(self::ID_USERS, $this->id_users);
		if($this->db->delete(self::BLOCK))
		{
			return TRUE;
		}
		return FALSE;
	}

	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$this->db->where("`".self::ID_USERS."`", $this->id_users)->where_in("`".self::ID_BLOCK."`", $id);
			$this->db->set("`active`", $active)->update(self::BLOCK);
			return TRUE;
		}
		return false;
	}
}
?>