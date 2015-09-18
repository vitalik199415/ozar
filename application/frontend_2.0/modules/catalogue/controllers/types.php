<?php
class Types extends AG_Controller
{
	public function __construct()
	{
		$this->mlangs->load_language_file('modules/products');
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model('catalogue/mtypes');
		$this->mtypes->_init();
		$types_array = $this->mtypes->get_category_types_array();
		if($types_array)
		{
			$this->build_types_temptate_js();
			$this->template->add_css('filters', 'modules/filters');
			$this->build_types_template_blocks($types_array);
		}
	}
	
	protected function build_types_temptate_js()
	{
		$this->template->add_js('jquery.gbc_filters', 'modules_js/catalogue/types');
		$this->template->add_js('jquery.jscrollpane', 'modules_js/catalogue/types');
		$this->template->add_js('jquery.mousewheel', 'modules_js/catalogue/types');
	}
	
	protected function build_types_template_blocks($types_array)
	{
		$this->template->add_view_to_template('types_block', 'catalogue/types/types_block_top_js', array());
		$this->template->add_view_to_template('types_block', 'catalogue/types/types_block', array('category_url' => $types_array['category_url']));
		$active_filters = array();
		foreach($types_array['select_array'] as $key => $ms)
		{
			if(count($types_array['options_array'][$key] > 1))
			{
				switch($types_array['type_kind'][$key])
				{
					case 'checkbox':	
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/checkbox_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'dropdown':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/dropdown_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'dropdown_checkbox':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/dropdown_checkbox_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
						$this->template->add_js('jquery.multiselect', 'modules_js/catalogue/types');
					break;
					case 'color':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/color_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'color_name':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/color_name_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'image':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/image_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'additional':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/additional_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => 'checkbox', 'options_array' => $types_array['options_array'][$key]));
					break;
					case 'price':
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/price_filter', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => $types_array['type_kind'][$key], 'options_array' => $types_array['options_array'][$key], 'options_active' => $types_array['options_active'][$key], 'type_id' => $key, 'group_name' => $ms));
						$this->template->add_view_to_template('types_block_filters', 'catalogue/types/filter_js', array('filter_item_id' => 'filter_item_'.$key, 'filter_type' => 'price', 'options_array' => $types_array['options_array'][$key]));
					break;
				}
				if(count($types_array['options_active'][$key]) > 0)
				{
					$active_filters[$key]['group_name'] = $ms;
					$active_filters[$key]['type_kind'] = $types_array['type_kind'][$key];
					$active_filters[$key]['filter_active_item_id'] = 'filter_active_item_'.$key;
					foreach($types_array['options_array'][$key] as $op_key => $op_ms)
					{
						if(isset($types_array['options_active'][$key][$op_key]))
						{
							$active_filters[$key]['options_active'][$op_key] = $op_ms;
						}
					}
				}
			}
		}
		
		$this->template->add_view_to_template('types_active_block', 'catalogue/types/active_filters', array('active_filters_array' => $active_filters, 'filter_type' => $types_array['type_kind']));
		$this->template->add_view_to_template('types_active_additional_block', 'catalogue/types/active_filters_additional', array('active_filters_array' => $active_filters, 'filter_type' => $types_array['type_kind']));
		$this->template->add_view_to_template('types_block', 'catalogue/types/settings_js', array());
		$this->template->add_view_to_template('types_block', 'catalogue/types/settings_js_additional', array());
		$this->template->add_view_to_template('types_block', 'catalogue/types/types_block_js', array());
		$this->template->add_view_to_template('types_block', 'catalogue/types/types_block_js_additional', array());
	}
	
	public function submit_filters()
	{   
		$URI = $this->uri->uri_to_assoc();
		if(isset($URI['category_url']))
		{
			$category_url = $URI['category_url'];
			$this->load->model('catalogue/mtypes');
			$this->mtypes->submit_filters($category_url);
		}
	}

	public function category_filters()
	{
		$this->load->model('catalogue/mtypes');
		$this->mtypes->_init();
		$this->mtypes->category_filters();

		modules::run('catalogue/products/get_category_products_filtered');
	}

	public function ajax_update_products_qty()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['category_id']))
		{
			$this->load->model('catalogue/mtypes');
			if($filters_pr_qty = $this->mtypes->get_ajax_update_products_qty($URI['category_id']))
			{
				echo json_encode($filters_pr_qty);
			}
			else
			{
				echo json_encode(false);
			}
		}
	}
}
?>