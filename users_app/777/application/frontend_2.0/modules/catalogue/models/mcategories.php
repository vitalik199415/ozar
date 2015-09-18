<?php
require_once "./application/frontend_2.0/modules/catalogue/models/mcategories.php";
class Users_mcategories extends Mcategories
{
	public function get_categories_tree_collection_lvl2()
	{
		$query = $this->get_categories_collection_query();
		$query->where("A.`level` < 4", NULL, FALSE);
		$result = $this->get_categories_tree_collection_array($query->get()->result_array());
		return array('categories' => $result[1]);
	}
}