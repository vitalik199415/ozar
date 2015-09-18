<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;

class Catalogue_search extends AG_Controller
{
	public function search()
	{
		$search_string = $this->variables->get_url_vars('search_string');
		$search_string = rawurldecode($search_string);
		if($search_string && strlen($search_string = trim($search_string)) >= 3)
		{
			$this->load->model('catalogue/mcatalogue_search');
			if($search_result = $this->mcatalogue_search->search_products($search_string))
			{
				call_user_func_array('modules::run', array('catalogue/products/build_search_products_template_blocks', $search_result));
			}
		}
	}
	
	/*public function test()
	{
		$sphinxQL = new Connection();
		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);

		$search_query = "SELECT MAX(price) AS max_p, MIN(price) AS min_p
		FROM `".SPHINX_INDEX."`
		WHERE `id_users` = 12126 AND `status` = 1 AND `sale` = 1
		AND `id_m_c_categories` = 5049 AND id_m_c_products_properties IN (1372,1373) AND id_m_c_products_properties IN (1367,1368,1369)
		LIMIT 1
		";

		$sphinx_result = $sphinxQL->query($search_query);
		echo var_dump($sphinx_result);
		
		exit;
	}
	
	public function update_prices()
	{
		$sphinxQL = new Connection();
		$sphinxQL->setConnectionParams(SPHINX_IP, SPHINX_PORT);
		
		$sql_query = '
			SELECT `id_m_c_products`,
			(SELECT `price` FROM `m_c_products_price` WHERE `id_m_c_products` = A.`id_m_c_products` ORDER BY `id_m_c_products` LIMIT 1) AS price
			FROM `m_c_products` AS A
			ORDER BY A.`id_m_c_products`';

		$DB = $this->db->query($sql_query);
		foreach($DB->result_array() as $ms)
		{
			$sphinxQL->query(
			"UPDATE `".SPHINX_INDEX."` SET
			`price` = ".$ms['price']."
			WHERE `id_m_c_products` = ".$ms['id_m_c_products']
			);
		}
	}*/
}
?>