<?php
class Mblock_additionally extends AG_Model
{
	const BLOCK 		= 'users_block_additionally';
	const ID_BLOCK 		= 'id_users_block_additionally';
		
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_mblock_additionally()
	{	
		$query = $this->db->select("`".self :: ID_BLOCK."` AS ID, `alias`, `code`, `block`")
				->from("`".self :: BLOCK."`")
				->where(self::ID_USERS, $this->id_users)->where("active", 1)->order_by("sort");
		$result = $query->get()->result_array();
		$header = '';
		$footer = '';
		foreach($result as $ms)
		{
			if($ms['block'] == 0)
			{
				$header .= $ms['code'];
			}
			if($ms['block'] == 1)
			{
				$footer .= $ms['code'];
			}
		}
		$this->variables->set_vars('block_additionally_header', $header);
		$this->variables->set_vars('block_additionally_footer', $footer);
		$this->template->add_view_to_template('block_additionally_footer', 'block_additionally/additionally_footer', array('additionally_footer' => $footer));
	}
}
?>