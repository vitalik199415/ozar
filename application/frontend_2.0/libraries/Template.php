<?php
class Template
{
	public $template = 'template';
	public $ajax_template = 'ajax';
	
	private $render = FALSE;
	private $RENDER = TRUE;
	private $ajax = FALSE;
	
	private $css = array();
	private $js = array();
	private $js_code = array();
	private $js_src = array();
	
	private $templates_header  	= array('base' => array(), 'normal' => array());
	private $templates 			= array('base' => array(), 'normal' => array());
	private $templates_footer  	= array('base' => array(), 'normal' => array());
	
	private $templates_ajax = array();
	
	private $tempates_views = array();
	
	public $css_dir = 'css';
	public $js_dir = 'js';
	
	private $navigation_array = array();
	
	private $rel_prev = '';
	private $rel_next = '';

	private $title = '';
	private $description = '';
	private $keywords = '';
	private $additionally = '';

	private $ci = false;
	function __construct()
	{
		$this->ci = & get_instance();
	}
	
	public function add_base_template($template, $values, $key = false)
	{
		return $this->_add_t('t', $template, $values, $key, TRUE);
	}
	
	public function add_template($template, $values, $key = false)
	{
		$this->render = TRUE;
		return $this->_add_t('t', $template, $values, $key);
	}
	
	public function add_template_ajax($template, $values, $key = FALSE)
	{
		$this->ajax = TRUE;
		$this->render = TRUE;
		$this->RENDER = TRUE;
		if($key) $this->templates_ajax[$key] = array('template' => $template, 'values' => $values);
		else $this->templates_ajax[] = array('template' => $template, 'values' => $values);
		return $this;
	}
	
	public function add_base_header($template, $values, $key = false)
	{
		return $this->_add_t('h', $template, $values, $key, TRUE);
	}
	public function add_header($template, $values, $key = false)
	{
		return $this->_add_t('h', $template, $values, $key);
	}
	
	public function add_base_footer($template, $values, $key = false)
	{
		return $this->_add_t('f', $template, $values, $key, TRUE);
	}
	public function add_footer($template, $values, $key = false)
	{
		return $this->_add_t('f', $template, $values, $key);
	}
	
	private function _add_t($type, $template, $values, $key = FALSE, $base = FALSE)
	{
		if($template != '' && is_array($values))
		{
			if(!$key) $key = null;
			$K = 'normal';
			if($base)
			{
				$K = 'base';
			}
			switch ($type)
			{
				case 't':
					$this->templates[$K][$key] = array('template' => $template, 'values' => $values);
				break;
				case 'h':
					$this->templates_header[$K][$key] = array('template' => $template, 'values' => $values);
				break;
				case 'f':
					$this->templates_footer[$K][$key] = array('template' => $template, 'values' => $values);
				break;
			}
			return $this;
		}
		return false;
	}
	
	public function set_view_to_template($value_key, $view, $values)
	{
		$this->render = TRUE;
		$this->tempates_views[$value_key] = array(array($view, $values));
		return $this;
	}
	
	public function add_view_to_template($value_key, $view, $values, $to_top = FALSE)
	{
		$this->render = TRUE;
		if(!isset($this->tempates_views[$value_key]))
		{
			$this->tempates_views[$value_key] = array();
			array_push($this->tempates_views[$value_key], array($view, $values));
			return $this;
		}
		if($to_top)
		{
			array_unshift($this->tempates_views[$value_key], array($view, $values));
			return $this;
		}
		
		array_push($this->tempates_views[$value_key], array($view, $values));
		return $this;
	}

	public function unset_template_view($value_key)
	{
		if(isset($this->tempates_views[$value_key])) unset($this->tempates_views[$value_key]);
		return $this;
	}
	
	public function get_template_view($value_key)
	{
		if(isset($this->tempates_views[$value_key]))
		{
			$html = '';
			foreach($this->tempates_views[$value_key] as $ms)
			{
				$html .= $this->ci->load->view($ms[0], $ms[1], TRUE);
			}
			return $html;
		}
		return false;
	}
	
	public function get_temlate_view($value_key)
	{
		return $this->get_template_view($value_key);
	}
	
	//TEMPLATE
	public function get_template($key)
	{
		return $this->_get_template('t', $key);
	}
	public function get_template_values($key)
	{
		return $this->_get_template('t', $key, 'values');
	}
	
	public function get_base_template($key)
	{
		return $this->_get_template('t', $key, 'template', TRUE);
	}
	public function get_base_template_values($key)
	{
		return $this->_get_template('t', $key, 'values', TRUE);
	}
	//---------------TEMPLATE-----------
	
	//TEMPLATE-HEADER
	public function get_header($key)
	{
		return $this->_get_template('h', $key);
	}
	public function get_header_values($key)
	{
		return $this->_get_template('h', $key, 'values');
	}
	
	public function get_base_header($key)
	{
		return $this->_get_template('h', $key, 'template', TRUE);
	}
	public function get_base_header_values($key)
	{
		return $this->_get_template('h', $key, 'values', TRUE);
	}
	//--------------TEMPLATE-HEADER-----------------
	
	//TEMPLATE-FOOTER
	public function get_footer($key)
	{
		return $this->_get_template('f', $key);
	}
	public function get_footer_values($key)
	{
		return $this->_get_template('f', $key, 'values');
	}
	
	public function get_base_footer($key)
	{
		return $this->_getTemplate('f', $key, 'template', TRUE);
	}
	public function get_base_footer_values($key)
	{
		return $this->_get_template('f', $key, 'values', TRUE);
	}
	//---------------TEMPLATE-FOOTER------------
	
	private function _get_template($type, $key = FALSE, $t_v = 'template', $base = FALSE)
	{
		if($key)
		{
			$K = 'normal';
			if($base)
			{
				$K = 'base';
			}
			switch ($type)
			{
				case 't':
					if(isset($this->templates[$K][$key]))
					{
						if($field && ($field == 'template' || $field == 'values'))
						{
							return $this->templates[$K][$key][$field];
						}
						return FALSE;
					}
					return FALSE;
				break;
				case 'h':
					if(isset($this->templates_header[$K][$key]))
					{
						if($field && ($field == 'template' || $field == 'values'))
						{
							return $this->templates_header[$K][$key][$field];
						}
						return FALSE;
					}
					return FALSE;
				break;
				case 'f':
					if(isset($this->templates_footer[$K][$key]))
					{
						if($field && ($field == 'template' || $field == 'values'))
						{
							return $this->templates_footer[$K][$key][$field];
						}
						return FALSE;
					}
					return FALSE;
				break;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	//TEMPLATE
	public function set_template_values($key, $values)
	{
		return $this->_set_temlate_values('t', $key, $values);
	}
	public function set_base_template_values($key, $values)
	{
		return $this->_set_temlate_values('t', $key, $values, TRUE);
	}
	public function add_template_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('t', $key, $values, FALSE, TRUE, $f_key);
	}
	public function add_base_template_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('t', $key, $values, TRUE, TRUE, $f_key);
	}
	//------------TEMPLATE----------
	
	//TEMPLATE-HEADER
	public function set_header_values($key, $values)
	{
		return $this->_set_temlate_values('h', $key, $values);
	}
	public function set_base_header_values($key, $values)
	{
		return $this->_set_temlate_values('h', $key, $values, TRUE);
	}
	public function add_header_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('h', $key, $values, FALSE, TRUE, $f_key);
	}
	public function add_base_header_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('h', $key, $values, TRUE, TRUE, $f_key);
	}
	//------------TEMPLATE-HEADER----------
	
	//TEMPLATE-FOOTER
	public function set_footer_values($key, $values)
	{
		return $this->_set_temlate_values('f', $key, $values);
	}
	public function set_base_footer_values($key, $values)
	{
		return $this->_set_temlate_values('f', $key, $values, TRUE);
	}
	public function add_footer_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('f', $key, $values, FALSE, TRUE, $f_key);
	}
	public function add_base_footer_values($key, $values, $f_key)
	{
		return $this->_set_temlate_values('f', $key, $values, TRUE, TRUE, $f_key);
	}
	//------------TEMPLATE-FOOTER----------
	
	private function _set_temlate_values($type, $key, $values, $base = FALSE, $add = FALSE, $f_key = FALSE)
	{
		$K = 'normal';
		if($base)
		{
			$K = 'base';
		}

		if($key != '' && is_array($values))
		{
			switch ($type)
			{
				case 't':
					if(isset($this->templates[$K][$key]))
					{
						if($add)
						{
							$this->templates[$K][$key]['values'][$f_key][] = $values;
							return $this;
						}
						$this->templates[$K][$key]['values'] = $values;
						return $this;
					}
				break;
				case 'h':
					if(isset($this->templates_header[$K][$key]))
					{
						if($add)
						{
							$this->templates_header[$K][$key]['values'][$f_key][] = $values;
							return $this;
						}
						$this->templates_header[$K][$key]['values'] = $values;
						return $this;
					}
				break;
				case 'f':
					if(isset($this->templates_footer[$K][$key]))
					{
						if($add)
						{
							$this->templates_footer[$K][$key]['values'][$f_key][] = $values;
							return $this;
						}
						$this->templates_footer[$K][$key]['values'] = $values;
						return $this;
					}
				break;
			}
		}
	}
		
	public function get_last_template_id()
	{
		if(count($this->templates)>0)
		{
			$array = $this->templates;
			end($array);
			return key($array);
		}
		return false;
	}
	
	public function add_css($css, $folder = false)
	{
		if($css != '')
		{
			if($folder)
			{
				$this->css[] = array($css, $folder);
				return $this;
			}
			$this->css[] = $css;
			return $this;
		}
		return false;
	}
	
	public function add_js($js, $folder = false)
	{
		if($js != '')
		{
			if($folder)
			{
				$this->js[] = array($js, $folder);
				return $this;
			}
			$this->js[] = $js;
			return $this;
		}
		return false;
	}
	
	public function add_js_code($js)
	{
		if($js != '')
		{
			$this->js_code[] = $js;
			return $this;
		}
		return false;
	}
	
	public function add_js_src($js)
	{
		if($js != '')
		{
			$this->js_src[] = $js;
			return $this;
		}
		return false;
	}

	public function add_rel_prev($href)
	{
		$this->rel_prev = '<link rel="prev" href="'.$href.'">';
	}

	public function add_rel_next($href)
	{
		$this->rel_next = '<link rel="next" href="'.$href.'">';
	}
	
	public function render($manually = FALSE)
	{
		if(($this->render && $this->RENDER) || $manually)
		{
			$this->ci->load->model('block_additionally/mblock_additionally');
			$this->ci->mblock_additionally->get_mblock_additionally();
			$html = '';
			$head = '';
			if(!$this->ajax)
			{
				$html = '';
				$head = '';
				$head .= $this->_render_prev_next();
				$head .= $this->_render_css();
				$head .= $this->_render_js();
				$head .= $this->_render_js_code();
				
				$html .= $this->_render_html_type('header', TRUE);
				$html .= $this->_render_html_type('content', TRUE);
				$html .= $this->_render_html_type('footer', TRUE);
			}
			else
			{
				foreach($this->templates_ajax as $key => $ms)
				{
					$html .= $this->ci->load->view($ms['template'], array('_key' => $key)+$ms['values'], true);
				}
			}
			$this->_to_template($html, $head);
		}
	}
	
	private function _render_html_type($type = 'content', $base = FALSE)
	{
		$html = '';
		$array = array();
		switch ($type)
		{
			case 'header':
			if($base) $array = $this->templates_header['base'];
				$array += $this->templates_header['normal'];
			break;
			
			case 'footer':
				$array = $this->templates_footer['normal'];
				if($base) $array += $this->templates_footer['base'];
			break;
			
			case 'content':
			if($base) $array = $this->templates['base'];
				$array += $this->templates['normal'];
			break;
		}
		if($array)
		{
			foreach($array as $key => $ms)
			{
				$html .= $this->ci->load->view($ms['template'], array('_key' => $key)+$ms['values'], true);
			}
		}	
		return $html;
	}
	private function _to_template($html, $head = '')
	{
		if(!$this->ajax)
		{
			$array['content'] 		= $html;
			$array['head'] 			= $head;
			$array['title'] 		= $this->ci->variables->get_vars('SEO_first_title').$this->ci->variables->get_vars('SEO_first_TD_separator').$this->title;
			$array['description'] 	= $this->ci->variables->get_vars('SEO_first_description').$this->ci->variables->get_vars('SEO_first_TD_separator').$this->description;
			$array['keywords'] 		= $this->keywords;
			$array['additionally'] 	= $this->ci->variables->get_vars('block_additionally_header');
			$this->ci->load->view($this->template, $array);
		}
		else
		{
			$array['content'] = $html;
			$this->ci->load->view($this->ajax_template, $array);
		}
		$this->render = false;
		$this->RENDER = false;
	}

	private function _render_prev_next()
	{
		return $this->rel_prev.$this->rel_next;
	}

	private function _render_css()
	{
		$css = '';
		foreach($this->css as $ms)
		{
			if(is_array($ms))
			{
				$css .= $this->_css_html($ms[1].'/'.$ms[0]);
			}
			else
			{
				$css .= $this->_css_html($ms);
			}
		}
		return $css;
	}
	private function _css_html($href)
	{
		if(strpos($href, 'http://') !== FALSE)
		{
			return '
			<link href="'.$href.'?'.GBC_CMS_V.'" rel="stylesheet" type="text/css" />';
		}
		if(file_exists(BASE_PATH.'users_app/'.ID_USERS.CSS_PATH.TEMPLATE.$href.'.css'))
		{
			return '
			<link href="/users_app/'.ID_USERS.CSS_PATH.TEMPLATE.$href.'.css?'.GBC_CMS_V.'" rel="stylesheet" type="text/css" />';
		}
		if(file_exists(BASE_PATH.CSS_PATH.TEMPLATE.$href.'.css'))
		{
			return '
			<link href="'.CSS_PATH.TEMPLATE.$href.'.css?'.GBC_CMS_V.'" rel="stylesheet" type="text/css" />';
		}
		return '
			<link href="'.CSS_PATH.$href.'.css?'.GBC_CMS_V.'" rel="stylesheet" type="text/css" />';
	}
	private function _render_js()
	{
		$js = '';
		foreach($this->js as $ms)
		{
			if(is_array($ms))
			{
				$js .= $this->_js_html($ms[1].'/'.$ms[0]);
			}
			else
			{
				$js .= $this->_js_html($ms);
			}
		}
		return $js;
	}
	private function _js_html($href)
	{
		if(file_exists(BASE_PATH.'users_app/'.ID_USERS.JS_PATH.$href.'.js'))
		{
			return '
			<script type="text/javascript" src="/users_app/'.ID_USERS.JS_PATH.$href.'.js?'.GBC_CMS_V.'"></script>';
		}
		return '
		<script type="text/javascript" src="'.JS_PATH.$href.'.js?'.GBC_CMS_V.'"></script>';
	}
	private function _render_js_code()
	{
		$src_code = false;
		$code = false;
		foreach($this->js_src as $ms)
		{
			$src_code .= '<script type="text/javascript" src="'.$ms.'"></script>
			';
		}
		foreach($this->js_code as $ms)
		{
			$code .= $ms.'
			';
		}
		$return = '';
		if($src_code)
		{
			$return .= '
			'.$src_code;
		}
		if($code)
		{
			$return .= '
			<script type="text/javascript">
				'.$code.'
			</script>';
		}
		return $return;
	}
	
	public function add_title($title, $add = false)
	{
		if($add)
		{
			$this->title .= $title;
			return $this;
		}
		$this->title = $title;
		return $this;
	}
	public function get_title()
	{
		return $this->title;
	}
	
	public function add_description($text, $add = false)
	{
		if($add)
		{
			$this->description .= $text;
			return $this;
		}
		$this->description = $text;
		return $this;
	}
	public function get_description()
	{
		return $this->description;
	}
	
	public function add_keywords($text, $add = false)
	{
		if($add)
		{
			$this->keywords .= $text;
			return $this;
		}
		$this->keywords = $text;
		return $this;
	}
	public function get_keywords()
	{
		return $this->keywords;
	}
	
	public function setTDK($array, $add = FALSE){return $this->set_TDK($array, $add);}
	public function set_TDK($array, $add = FALSE)
	{
		if(count($array)>0)
		{
			if(isset($array['seo_title']))
			{
				$this->add_title(quotes_to_entities($array['seo_title']), $add);
			}
			if(isset($array['seo_description']))
			{
				$this->add_description(quotes_to_entities($array['seo_description']), $add);
			}
			if(isset($array['seo_keywords']))
			{
				$this->add_keywords(quotes_to_entities($array['seo_keywords']), $add);
			}
		}
		return $this;
	}

	public function add_TDK($array)
	{
		if(count($array)>0)
		{
			if(isset($array['seo_title']))
			{
				$this->add_title(quotes_to_entities($array['seo_title']), TRUE);
			}
			if(isset($array['seo_description']))
			{
				$this->add_description(quotes_to_entities($array['seo_description']), TRUE);
			}
			if(isset($array['seo_keywords']))
			{
				$this->add_keywords(quotes_to_entities($array['seo_keywords']), TRUE);
			}
		}
		return $this;
	}
	
	public function add_additionally($text, $add = false)
	{
		if($add)
		{
			$this->additionally .= $text;
			return $this;
		}
		$this->additionally = $text;
		return $this;
	}
	public function get_additionally()
	{
		return $this->additionally;
	}

	
	public function is_ajax($ajax = NULL)
	{
		if($ajax != NULL)
		{
			$this->ajax = $ajax;
		}
		return $this->ajax;
	}
	
	public function	add_navigation($name, $href = false, $options = array())
	{
		$array = array('name' => $name, 'href' => $href, 'options' => $options);
		$this->navigation_array[] = $array;
		return $this;
	}
	public function get_navigation()
	{
		return $this->navigation_array;
	}
}
?>