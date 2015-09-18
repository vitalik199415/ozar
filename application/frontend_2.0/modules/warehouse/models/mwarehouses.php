<?php
class Mwarehouses extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	
	const WH_PR 	= 'wh_products';
	
	private $shop_wh = FALSE;
	private $shop_wh_id = FALSE;
	private $first_get_wh_shop_query = TRUE;

	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_shop_wh_id()
	{
		if($this->first_get_wh_shop_query === FALSE) return $this->shop_wh_id;
		$this->first_get_wh_shop_query = FALSE;
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0) return FALSE;

		$this->load->model("warehouse/mwh_settings");
		$wh_settings = $this->mwh_settings->get_settings();
		if($wh_settings['wh_active'] == 0) return FALSE;

		$this->db->select("*")
			->from("`".self::WH."`")
			->where("`".self::ID_USERS."`", $this->id_users)
			->where("`i_s_wh`", 1)->limit(1);
		if(count($WH = $this->db->get()->row_array()) > 0)
		{
			$this->shop_wh = $WH;
			$this->shop_wh_id = $WH[self::ID_WH];
			return $this->shop_wh_id;
		}
		return FALSE;
	}
}
?>