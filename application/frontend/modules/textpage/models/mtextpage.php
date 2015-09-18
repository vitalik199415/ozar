<?php
class Mtextpage extends AG_Model
{
	const TEXTPAGE = 'm_textpage';
	const ID_TEXTPAGE = 'id_m_textpage';
	const TEXTPAGE_DESCRIPTION = 'm_textpage_description';
	
	protected $id_users_modules = FALSE;
	protected $settings = FALSE;
	
	protected $output_settings = array();

	function __construct()
	{
		parent::__construct();
	}
	
	public function _init($id_users_modules, $settings = FALSE)
	{
		$this->id_users_modules = $id_users_modules;
		$this->settings = $settings;
	}
	
	public function get_textpage_collection()
	{
		$query = $this->db
				->select("A.`".self::ID_TEXTPAGE."` AS ID, A.`sort` AS SORT, A.`show`, B.`name`, B.`text`")
				->from("`".self::TEXTPAGE."` AS A")
				->join(	"`".self::TEXTPAGE_DESCRIPTION."` AS B",
						"B.`".self::ID_TEXTPAGE."` = A.`".self::ID_TEXTPAGE."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"inner")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->order_by("SORT");
				
		return array('textpage' => $query->get()->result_array());
	}
}
?>