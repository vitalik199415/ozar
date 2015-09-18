<?php
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
}
?>