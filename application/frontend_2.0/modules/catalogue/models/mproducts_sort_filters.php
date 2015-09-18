<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;

class Mproducts_sort_filters extends AG_Model
{
	const ID_PR = "id_m_c_products";
	const SPHINX_INDEX = SPHINX_INDEX;

	private $sphinx_pr_where = FALSE;
	private $sphinx_pr_order_by = FALSE;

	private $sphinxQL;

	public function __construct()
	{
		$this->sphinxQL = new Connection();
		$this->sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);
		parent::__construct();
	}

	public function get_sphinx_products()
	{
		$category_id = $this->variables->get_vars('category_id');
		$sphinx_products = array();
		$sphinx_sort = FALSE;
		$sphinx_order_by = $this->get_sphinx_sort_string();
		$sphinx_where = $this->get_sphinx_filters_string();
		if($sphinx_order_by != '') $sphinx_sort = TRUE;

		if($sphinx_where != '' || $sphinx_order_by != '')
		{
			$query = "SELECT `id_m_c_products`
					FROM `".self::SPHINX_INDEX."`
					WHERE `id_users` = ".$this->id_users." AND `status` = 1
					AND `id_m_c_categories` = ".$category_id." ".$sphinx_where." ".$sphinx_order_by."
					LIMIT 0, 2000
				";

			$sphinx_result = $this->sphinxQL->query($query);
			if(count($sphinx_result) > 0)
			{
				foreach($sphinx_result as $ms)
				{
					$sphinx_products[$ms[self::ID_PR]] = $ms[self::ID_PR];
				}
			}
			else
			{
				$sphinx_products[0] = 0;
			}
		}
		return array($sphinx_products, $sphinx_sort);
	}

	public function get_sphinx_sort_string()
	{
		if($this->sphinx_pr_order_by) return $this->sphinx_pr_order_by;
		$this->sphinx_pr_order_by = $this->mcatalogue_sort->get_sphinx_products_order_by();
		return $this->sphinx_pr_order_by;
	}

	public function get_sphinx_filters_string()
	{
		if($this->sphinx_pr_where) return $this->sphinx_pr_where;
		$this->sphinx_pr_where = $this->mtypes->get_sphinx_products_where();
		return $this->sphinx_pr_where;
	}
}
?>