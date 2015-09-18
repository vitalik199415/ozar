<?php
class Mproducts_waitlist extends AG_Model
{

	const PR_WAIT 				= 'm_u_customers_waitlist';
	const ID_PR_WAIT 			= 'id_m_u_customers_waitlist';
    const PR 			        = 'm_c_products';
    const ID_PR 		        = 'id_m_c_products';
	const CUST_ADDR		        = 'm_u_customers_address';
    const PR_DESC 		= 'm_c_products_description';
    const ID_PR_DESC 	= 'id_m_c_products_description';

	function __construct()
	{
		parent::__construct();
	}

    public function render_product_grid()
    {
        $this->load->library('grid');
        $this->grid->_init_grid('products_grid');

        $this->grid->db
            ->select("DISTINCT A.`".self::ID_PR."` AS ID, PR.`sku`, B.`name`,
                        (SELECT COUNT(".self::ID_PR.") FROM `".self::PR_WAIT."`) as count" )
            ->from("`".self::PR_WAIT."` AS A")
            ->join(	"`".self::PR_DESC."` AS B",
                "B.`".self::ID_PR."` = A.`".self::ID_PR."` AND B.`".self::ID_LANGS."` = ".$this->id_langs,
                "LEFT")
			->join(	"`".self::PR."` AS PR",
				"PR.`".self::ID_PR."` = A.`".self::ID_PR."`",
				"LEFT")
        	->where("PR.`".self::ID_USERS."`", $this->id_users);

        $this->load->helper('catalogue/products_waitlist_helper');
        helper_products_grid_build($this->grid);

        $this->grid->create_grid_data();
        $this->grid->render_grid();
    }

	public function product_waitlist_grid($id_pr)
	{
        $this->load->library('grid');
        $this->grid->_init_grid('products_waitlist_grid_'.$id_pr, array('limit' => 50));
		$this->grid->db->select("A.`".self::ID_PR_WAIT."` as ID, A.`email`, B.`name`")
					->from("`".self::PR_WAIT."` AS A")
					->join(	"`".self::CUST_ADDR."` AS B",
						"B.`address_email` = A.`email` AND B.`type` = 'B'",
						"LEFT")
					->join(	"`".self::PR."` AS PR",
						"PR.`".self::ID_PR."` = A.`".self::ID_PR."`",
						"LEFT")
                    ->where("A.`".self::ID_PR."`", $id_pr)
					->where("PR.`".self::ID_USERS."`", intval($this->id_users));

		$this->load->helper('catalogue/products_waitlist_helper');
        waitlist_grid_build($this->grid, $id_pr);

        $this->grid->create_grid_data();
        return $this->grid->render_grid();

	}

    public function view_product_waitlist($id_pr)
    {
        $id_pr = intval($id_pr);
        $this->load->model('catalogue/mproducts');
        if(!$this->mproducts->check_isset_pr($id_pr)) return FALSE;

        $query = $this->db->select("A.`sku`")
            ->from("`".self::PR."` AS A")
            ->where("A.`".self::ID_PR."`", $id_pr)
            ->where("A.`".self::ID_USERS."`", $this->id_users)
            ->limit(1);
        $result = $query->get()->row_array();

        $this->template->add_navigation("Запросы наличия товара ".$result['sku']);
        $this->product_waitlist_grid($id_pr);

        return TRUE;
    }

}
?>