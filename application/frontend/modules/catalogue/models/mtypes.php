<?php
class Mtypes extends AG_Model
{
	const TYPES 		= 'm_c_products_types';
	const ID_TYPES 		= 'id_m_c_products_types';
	const TYPES_DESC 	= 'm_c_products_types_description';
	
	const PROP 		= 'm_c_products_properties';
	const ID_PROP 	= 'id_m_c_products_properties';
	const PROP_DESC = 'm_c_products_properties_description';
	
	const CAT 		= 'm_c_categories';
	const ID_CAT 	= 'id_m_c_categories';
	
	const PR_CAT = 'm_c_productsNcategories';
	
	const PR_TYPES = 'm_c_productsNtypes';
	
	const ID_PR = 'id_m_c_products';
	
	public $filters_options = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}
	
	public function _init()
	{
		$keep_flashdata = FALSE;
		$categories_id = $this->variables->get_vars('categorie_id');
		if($this->session->flashdata('products_filters_categorie_id') && $categories_id == $this->session->flashdata('products_filters_categorie_id'))
		{
			$keep_flashdata = TRUE;
		}
		if(($filters = $this->session->flashdata('products_filters')) && $keep_flashdata)
		{
			foreach($filters as $key => $ms)
			{
				if(intval($ms) > 0)
				{
					$this->filters_options[$key] = $ms;
				}
			}
		}
		if($this->input->post('products_filters_clear'))
		{
			$this->filters_options = array();
			$keep_flashdata = FALSE;
		}
		if($filters = $this->input->post('products_filters'))
		{
			$this->filters_options = array();
			$F = FALSE;
			foreach($filters as $key => $ms)
			{
				if(intval($ms) > 0)
				{
					$this->filters_options[$key] = $ms;
					$F = TRUE;
				}
			}
			if($F)
			{
				$this->session->set_flashdata('products_filters', $this->filters_options);
				$this->session->set_flashdata('products_filters_categorie_id', $categories_id);
			}	
		}
		if($keep_flashdata) $this->keep_flashdata();
	}
	
	protected function keep_flashdata()
	{
		$this->session->keep_flashdata('products_filters');
		$this->session->keep_flashdata('products_filters_categorie_id');
	}
	
	protected function get_filter_option($types_id)
	{
		if(isset($this->filters_options[$types_id]))
		{
			return $this->filters_options[$types_id];
		}
		return FALSE;
	}
	
	protected function get_categories_types_query($categories_id)
	{
		$types_filter = '';
		/*if(count($this->filters_options)>0)
		{
			$P_IN = '';
			$T_IN = '';
			foreach($this->filters_options as $key => $ms)
			{
				$P_IN .= $ms.',';
				$T_IN .= $key.',';
			}
			$P_IN = substr($P_IN, 0, -1);
			$T_IN = substr($T_IN, 0, -1);
			if($P_IN != '')
			{
				$types_filter = " INNER JOIN `".self::PR_TYPES."` AS TF ON (TF.`".self::ID_TYPES."` IN(".$T_IN.") || (TF.`".self::ID_TYPES."` NOT IN(".$T_IN.") && TF.`".self::ID_PROP."` IN(".$P_IN."))) && TF.`".self::ID_PR."` = EA.`".self::ID_PR."`";
			}
		}*/	
		$query = $this->db->select("A.`".self::ID_TYPES."` AS ID, AD.`name` AS tname, B.`".self::ID_PROP."` AS PID, BD.`name` AS pname")
				->from("`".self::TYPES."` AS A")
				->join("`".self::TYPES_DESC."` AS AD",
						"AD.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && AD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->join("`".self::PROP."` AS B",
						"B.`".self::ID_TYPES."` = A.`".self::ID_TYPES."`",
						"LEFT")
				->join("`".self::PROP_DESC."` AS BD",
						"BD.`".self::ID_PROP."` = B.`".self::ID_PROP."` && BD.`".self::ID_LANGS."` = ".$this->mlangs->id_langs,
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->where("A.`active`", 1)->where("B.`active`", 1)
				->where("
				EXISTS(
				SELECT EA.`".self::ID_TYPES."`, EA.`".self::ID_PROP."` FROM `".self::PR_TYPES."` AS EA 
				INNER JOIN `".self::PR_CAT."` AS EB 
					ON EB.`".self::ID_CAT."` = ".$categories_id." && EB.`".self::ID_PR."` = EA.`".self::ID_PR."`".$types_filter."
				WHERE EA.`".self::ID_PROP."` IS NOT NULL && EA.`".self::ID_TYPES."` = A.`".self::ID_TYPES."` && EA.`".self::ID_PROP."` = B.`".self::ID_PROP."`
				)
				", NULL, FALSE)
				->order_by("A.`sort`, B.`sort`");
		//echo $query->_compile_select();
		return $query;
	}
	
	public function get_categories_types_array($categories_id = FALSE)
	{
		if(!$categories_id) $categories_id = $this->variables->get_vars('categorie_id');
		if(!$categories_id) return FALSE;
		$categorie_url = $this->variables->get_vars('categorie_url');
		$select_array = array();
		$options_array = array();
		$query = $this->get_categories_types_query($categories_id);
		$result = $query->get()->result_array();
		if(count($result)==0) return FALSE;
		foreach($result as $ms)
		{
			$select_array[$ms['ID']] = $ms['tname'];
			$options_array[$ms['ID']][$ms['PID']] = $ms['pname'];
			$options_active[$ms['ID']] = '';
			if($this->get_filter_option($ms['ID'])) $options_active[$ms['ID']] = $this->get_filter_option($ms['ID']);
		}
		return array('select_array' => $select_array, 'options_array' => $options_array, 'options_active' => $options_active, 'categorie_url' => $categorie_url);
	}
	
	public function update_products_query($query)
	{
		if(count($this->filters_options) == 0) return $query;
		$IN = '';
		$count = 0;
		foreach($this->filters_options as $key => $ms)
		{
			$IN .= $ms.',';
			//$count++;
			/*$query->join("`".self::PR_TYPES."` AS PRTYPES".$count,
							"PRTYPES".$count.".`".self::ID_PR."` = A.`".self::ID_PR."` && PRTYPES".$count.".`".self::ID_PROP."` = ".$ms,
							"INNER");*/
			$count++;				
		}
		$IN = substr($IN,0,-1);
		/*$query->join("`".self::PR_TYPES."` AS PRTYPES",
							"PRTYPES.`".self::ID_PR."` = A.`".self::ID_PR."` && PRTYPES.`".self::ID_PROP."` IN(".$IN.") && (SELECT COUNT(*) FROM `".self::PR_TYPES."` AS PRTYPES WHERE PRTYPES.`".self::ID_PR."` = A.`".self::ID_PR."` && PRTYPES.`".self::ID_PROP."` IN(".$IN.")) = ".$count,
							"INNER");*/
		//$query->having("(SELECT COUNT(*) FROM `".self::PR_TYPES."` AS PRTYPES WHERE PRTYPES.`".self::ID_PR."` = A.`".self::ID_PR."` && PRTYPES.`".self::ID_PROP."` IN(".$IN.")) = ", $count, FALSE);					
		$query->where("A.`".self::ID_PR."` IN (SELECT DZ.`".self::ID_PR."` FROM `".self::PR_TYPES."` AS DZ WHERE DZ.`".self::ID_PROP."` IN (".$IN.") && (SELECT COUNT(*) FROM `".self::PR_TYPES."` AS DZZ WHERE DZZ.`".self::ID_PROP."` IN (".$IN.") && DZZ.`".self::ID_PR."` = DZ.`".self::ID_PR."`) = ".$count.")", NULL, FALSE);	
			//echo $query->_compile_select().'<br><br>';
		return $query;
	}
}
?>