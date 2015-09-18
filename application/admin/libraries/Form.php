<?php
class Form
{
	public $id;
	public $href;
	
	private $values = false;
	private $name;
	private $tabs 	= false;
	private $block 	= array();
	private $groups = array();
	private $CI;
	private $CKE 	= FALSE;
	private $validation = FALSE;
	private $validation_massages = FALSE;
	private $inputmask = FALSE;
	private $inputmask_class = FALSE;
	
	private $form_buttons 	= array();
	private $js_code 		= '';
	private $html_code 		= '';
	
	function __construct(){}
	
	public function _init($name, $id, $href)
	{
		$this->CI = & get_instance();
		$this->name = $name;
		$this->id = $id;
		$this->href = $href;
	}
	public function enable_CKE()
	{
		$this->CKE = TRUE;
	}
	public function get_id()
	{
		return $this->id;
	}
	public function get_href()
	{
		return $this->href;
	}	
	public function add_button($array)
	{
		$this->form_buttons[] = $array;
	}
	public function unset_buttons()
	{
		$this->form_buttons = array();
	}
	public function create_buttons()
	{
		if($this->form_buttons)
		{
			$html = '';
			foreach($this->form_buttons as $ms)
			{
				if(!isset($ms['options']))
				{
					$ms['options'] = array();
				}
				$html .= anchor($ms['href'],$ms['name'],$ms['options']);
			}
			return $html;
		}
		return false;
	}
	
	public function add_tab($key, $name)
	{
		if($key)
		{
			$this->tabs[$key]['name'] = $name;
		}
		else
		{
			$this->tabs[] = array('name' => $name);
		}
		return $this;
	}
	public function get_tabs()
	{
		if($this->tabs) return $this->tabs;
		return false;
	}
	
	
	public function add_block($object)
	{
		$this->block[] = $object;
		return $this;
	}
	public function add_block_to_tab($tabs_key, $object_key)
	{
		$object = $this->group($object_key);
		$this->block[$tabs_key][] = $object;
		return $this;
	}
	public function get_block($key = false)
	{
		if($key)
		{
			if(isset($this->block[$key]))
			{
				return $this->block[$key];
			}
			else
			{
				return array();
			}	
		}
		return $this->block;
	}	

	
	public function add_group($key, $values = FALSE, $langs = FALSE)
	{
		$this->groups[$key] = new Form_block($values, $langs);
		return $this;
	}
	public function group($key)
	{
		if(isset($this->groups[$key]))
		{
			return $this->groups[$key];
		}
		return FALSE;
	}
	
	
	public function add_js_code($code)
	{
		$this->js_code .= $code;
	}
	public function get_js_code()
	{
		return $this->js_code;
	}
	public function add_html_code($code)
	{
		$this->html_code .= $code;
	}
	public function get_html_code()
	{
		return $this->html_code;
	}
	
	public function add_inputmask($name, $fn, $options_str = '')
	{
		$this->inputmask['name'][] = array('name' => $name, 'fn' => $fn, 'options' => $options_str);
		return $this;
	}
	public function add_inputmask_to_class($class, $fn, $options_str = '')
	{
		$this->inputmask['class'][] = array('class' => $class, 'fn' => $fn, 'options' => $options_str);
		return $this;
	}
	public function add_inputmask_to_id($id, $fn, $options_str = '')
	{
		$this->inputmask['id'][] = array('id' => $id, 'fn' => $fn, 'options' => $options_str);
		return $this;
	}
	public function add_inputmask_to_rule($rule, $fn, $options_str = '')
	{
		$this->inputmask['rule'][] = array('rule' => $rule, 'fn' => $fn, 'options' => $options_str);
		return $this;
	}
	protected function prepare_js_inputmask()
	{
		$rules = '';
		if(!$this->inputmask) return $rules = '';
		if(isset($this->inputmask['name']))
		{
			foreach($this->inputmask['name'] as $ms)
			{
				$rules .= '$("#'.$this->get_id().'").find("input[name=\''.$ms['name'].'\']").inputmask("'.$ms['fn'].'"';
				if($ms['options'] != '') $rules .= ', {'.$ms['options'].'}';
				$rules .= ');
				';
			}
		}
		if(isset($this->inputmask['class']))
		{
			foreach($this->inputmask['class'] as $ms)
			{
				$rules .= '$("#'.$this->get_id().'").find(".'.$ms['class'].'").inputmask("'.$ms['fn'].'"';
				if($ms['options'] != '') $rules .= ', {'.$ms['options'].'}';
				$rules .= ');
				';
			}
		}
		if(isset($this->inputmask['id']))
		{
			foreach($this->inputmask['id'] as $ms)
			{
				$rules .= '$("#'.$this->get_id().'").find("#'.$ms['id'].'").inputmask("'.$ms['fn'].'"';
				if($ms['options'] != '') $rules .= ', {'.$ms['options'].'}';
				$rules .= ');
				';
			}
		}
		if(isset($this->inputmask['rule']))
		{
			foreach($this->inputmask['rule'] as $ms)
			{
				$rules .= '$("#'.$this->get_id().'").find("'.$ms['rule'].'").inputmask("'.$ms['fn'].'"';
				if($ms['options'] != '') $rules .= ', {'.$ms['options'].'}';
				$rules .= ');
				';
			}
		}
		return $rules;
	}
	public function render_js_inputmask()
	{
		$js = '
		'.$this->prepare_js_inputmask();
		return $js;
	}
	
	public function add_validation($key, array $options)
	{
		$this->validation[$key] = $options;
		return $this;
	}
	public function add_validation_massages($key, array $options)
	{
		$this->validation_massages[$key] = $options;
		return $this;
	}
	public function prepare_js_validation()
	{
		$rules = FALSE;
		if($this->validation)
		{
			foreach($this->validation as $key => $ms)
			{
				$rules .= '"'.$key.'":{';
				foreach($ms as $type => $val)
				{
					$rules .= $type.': '.$val.',';
				}
				$rules = substr($rules,0,-1);
				$rules .= '},';
			}
			$rules = substr($rules,0,-1);
		}	
		
		$massages = FALSE;
		if($this->validation_massages)
		{
			foreach($this->validation_massages as $key => $ms)
			{
				$massages .= '"'.$key.'":{';
				foreach($ms as $type => $val)
				{
					$massages .= $type.': "'.$val.'",';
				}
				$massages = substr($massages,0,-1);
				$massages .= '},';
			}
			$massages = substr($massages,0,-1);
		}
		return array($rules, $massages);
	}
	public function render_js_validation()
	{
		$js = '';
		$R_M = $this->prepare_js_validation();
		
			$js = '$("#form_'.$this->get_id().'").validate({
			';
			if($R_M[0])
			{	
				$js .= 'rules: {
				'.$R_M[0].'
				},';
				
				if($R_M[1])
				{
					$js .= '
					messages: {
					'.$R_M[1].'
					},';
				}
			}
			$js .= '
			
			errorPlacement: function(error, element) {
				$(element).parent("div").append(error);
				block = $(element).parents(".field_block");
				i = $("#form_'.$this->get_id().'").find(".field_block").index(block);
				$("#form_'.$this->get_id().'").find(".tabs_block").find("ul").find("li").eq(i).find("a").addClass("error");
			}
			,unhighlight: function(element, errorClass, validClass) {
				$(element).removeClass(errorClass);
				block = $(element).parents(".field_block");
				er_elem = $(block).find("input[class=error],select[class=error],textarea[class=error]");
				if($(er_elem).size() == 0)
				{
					i = $("#form_'.$this->get_id().'").find(".field_block").index(block);
					$("#form_'.$this->get_id().'").find(".tabs_block").find("ul").find("li").eq(i).find("a").removeClass("error");
				}
			}
			,ignore: ""
			});';
		return $js;
	}
	
	public function reset()
	{
		$this->id = NULL;
		$this->href = NULL;
		
		$this->values = false;
		$this->name = NULL;
		$this->tabs = false;
		$this->block = array();
		$this->groups = array();
		$this->CKE = FALSE;
		
		$this->form_buttons = array();
		$this->js_code = '';
		$this->html_code = '';
	}
	public function render_form()
	{
		if($this->CKE)
		{
			$this->CI->template->add_js('ckeditor','ckeditor');
			//$this->CI->template->add_js('start_CKEditor','ckeditor');
		}
		$this->CI->template->add_js('jquery.inputmask.bundle', 'inputmask');
		$this->CI->template->add_js('jquery.validate.1.9.min', 'form');
		$this->CI->template->add_js('additional-methods.1.9.min', 'form');
		$this->CI->template->add_js('messages_ru', 'form/massages');
		$this->CI->template->add_js('jquery.ag_form', 'libraries');
		$this->CI->template->add_css('form');
		$this->CI->template->add_template('libraries/form/form', array('form' => clone $this), $this->name);
		if($this->CKE)
		{
			$this->CI->template->add_template('libraries/form/start_CKE', array(), $this->name.'_start_CKE');
		}
		$this->reset();
	}
}





class Form_block
{
	private $i = 0;
	private $objects = array();
	private $options = array();
	private $objects_to = array();
	private $values_prepare = FALSE;
	public $values = FALSE;
	
	private $HTML = '';
	
	private $langs = FALSE;
	
	public $background = array('0'=>'block_w_field','1'=>'block_w_field_bg');
	public $background_i = 0;
	
	function __construct($values = FALSE , $langs = FALSE)
	{
		if($langs !== FALSE)
		{
			$this->langs = array();
			foreach($langs as $key=>$ms)
			{
				$this->langs[$key] = $ms;
			}
		}
		if($values)
		{
			$this->values_prepare = $values;
			$this->create_values_array();
		}
		
	}
	private function create_values_array($p_key = '', $r_key = FALSE, $position = 1)
	{
		$array = $this->values_prepare;
		if($r_key)
		{
			foreach($r_key as $ms)
			{
				$array = $array[$ms];
			}
		}
		else
		{
			$r_key = array();
		}
		
		foreach($array as $mskey => $ms)
		{
			if(is_array($ms))
			{
				if($position == 1)
				{
					$part_key = $mskey;
				}
				else
				{
					$part_key = $p_key.'['.$mskey.']';
				}
				$real_rey = $r_key;
				array_push($real_rey, $mskey);
				$rec_pos = $position + 1;
				$this->create_values_array($part_key, $real_rey, $rec_pos);
			}
			else
			{
				if($position == 1)
				{
					$this->values[$mskey] = $ms;
				}
				else
				{
					$this->values[$p_key.'['.$mskey.']'] = $ms;
				}
			}
		}
	}
	
	public function add_object($type, $name, $label = '', $options = array())
	{
		$this->objects[$this->i] = array($type, $name, $label);
		$this->options[$this->i] = $options;
		$this->i++;
		return $this->i-1;
	}
	public function add_object_to($key, $type, $name, $label = '', $options = array())
	{
		if(isset($this->objects[$key]))
		{
			$this->objects[$key]['chield'][$this->i] = array($type, $name, $label);
		}
		else
		{
			$this->objects[$this->i] = array($type, $name, $label);
		}
		$this->options[$this->i] = $options;
		$this->i++;
		return $this->i-1;
	}
	public function get_objects($key = FALSE, $key1 = FALSE)
	{
		if($key!==false)
		{
			if(isset($this->objects[$key]))
			{
				$res = $this->objects[$key];
				if($key1!==false)
				{
					if(isset($res[$key1]))
					{
						$res = $res[$key1];
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			$res = $this->objects;
		}
		return $res;
	}
	
	
	public function get_options($key = false, $key1 = false)
	{
		if($key!==false)
		{
			if(isset($this->options[$key]))
			{
				$res = $this->options[$key];
				if($key1!==false)
				{
					if(isset($res[$key1]))
					{
						$res = $res[$key1];
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			$res = $this->options;
		}
		return $res;
	}
	public function get_values($key = false)
	{
		if($this->values)
		{
			if($key!==false)
			{
				if(isset($this->values[$key]))
				{
					$res = $this->values[$key];
				}
				else
				{
					return false;
				}
			}
			else
			{
				$res = $this->values;
			}
			return $res;
		}
		return false;
	}
	
	public function add_view($name, $values = array())
	{
		$this->objects[$this->i] = array('view', $name, $values);
		$this->i++;
		return $this->i-1;
	}
	public function add_view_to($key, $name, $values = array())
	{
		if(isset($this->objects[$key]))
		{
			$this->objects[$key]['chield'][$this->i] = array('view', $name, $values);
		}
		else
		{
			$this->objects[$this->i] = array('view', $name, $values);
		}
		$this->i++;
		return $this->i-1;
	}
	
	
	public function add_html($html)
	{
		$this->objects[$this->i] = array('html', $html);
		$this->i++;
		return $this->i-1;
	}
	public function add_html_to($key, $html)
	{
		if(isset($this->objects[$key]))
		{
			$this->objects[$key]['chield'][$this->i] = array('html', $html);
		}
		else
		{
			$this->objects[$this->i] = array('html', $html);
		}
		$this->i++;
		return $this->i-1;
	}
	
	public function add_js($js)
	{
		$this->objects[$this->i] = array('script', $js);
		$this->i++;
		return $this->i-1;
	}
	
	public function block_to_HTML($id_form = 'def_form', $dkey = 'def_block', $ajax = FALSE)
	{
		$output = '<div id = "'.$dkey.'">';
		$this->HTML = '';
		if($this->langs !== FALSE)
		{
			$output .= $this->create_tabs();
			$output_block = '';
			foreach($this->langs as $key => $ms)
			{
				$this->HTML = '';
				$output_block = $this->create_block($key);
				$output .= $this->create_tabs_blocks($output_block);
			}
			$output .= '</div>';
			if(!$ajax)
			{
				$output .= '<script>$("#'.$id_form.' #'.$dkey.' .langs_tabs ul").tabs("#'.$id_form.' #'.$dkey.' div.langs_tabs_block");</script>';
			}	
			return $output;
		}
		$output = $this->create_block();
		return $output;
	}
	
	public function	create_tabs_block_NL()
	{
		$this->HTML = '';
		return $this->create_tabs_blocks($this->create_block()); 
	}
	public function	create_tab($name)
	{
		return '<li><a href="#" class="href">'.$name.'</a></li>';;
	}	
	private function create_tabs()
	{
		$output = '<div class="langs_tabs"><ul>';
		foreach($this->langs as $key => $ms)
		{
			$tname = $ms;
			if(trim($tname) == '') $tname = '#';
			$output .= '<li><a href="#" class="href">'.$tname.'</a></li>';
		}
		$output .= '</ul></div><div class="CB"></div>';
		return $output;
	}
	private function create_tabs_blocks($input)
	{
		$output = '<div class="langs_tabs_block"><div class="langs_tabs_block_padding">';
		$output .= $input;
		$output .= '</div></div>';
		return $output;
	}	
	private function create_block($name = '', $ckey = FALSE)
	{
		if($ckey!==FALSE)
		{
			$OBJ = $this->objects[$ckey]['chield'];
		}
		else
		{
			$OBJ = $this->objects;
		}
		foreach($OBJ as $key=>$ms)
		{
			switch($ms[0])
			{
				case "text":
					$this->HTML .= Form_part::type_text($this, array($key,$ms), $name);
				break;
				case "textarea":
					$this->HTML .= Form_part::type_textarea($this, array($key,$ms), $name);
				break;
				case "select":
					$this->HTML .= Form_part::type_select($this, array($key,$ms), $name);
				break;
				case "checkbox":
					$this->HTML .= Form_part::type_checkbox($this, array($key,$ms), $name);
				break;
				case "radio":
					$this->HTML .= Form_part::type_radio($this, array($key,$ms), $name);
				break;
				case "hidden":
					$this->HTML .= Form_part::type_hidden($this, array($key,$ms), $name);
				break;
				case "file":
					$this->HTML .= Form_part::type_file($this, array($key,$ms), $name);
				break;
				case "view":
					$this->HTML .= Form_part::type_view($this, array($key,$ms));
				break;
				case "html":
					$this->HTML .= Form_part::type_html($this, array($key,$ms));
				break;
				case "js":
					$this->HTML .= Form_part::type_js($this, array($key,$ms));
				break;
			}
			if($ms[0] == 'fieldset')
			{
				$this->HTML .= Form_part::type_fieldset_open($this, array($key,$ms));
				if(isset($ms['chield']))
				{
					$this->create_block($name, $key);
				}
				$this->HTML .= Form_part::type_fieldset_close();
			}
			else
			{
				if(isset($ms['chield']))
				{
					$this->create_block($name, array($key,$ms));
				}
			}
			if($this->background_i == 0) $this->background_i = 1; else $this->background_i = 0;
		}
		return $this->HTML;
	}	
}


class Form_part
{
	public function create_field_name($name, $replase = '', $R = '$')
	{
		if($replase != '')
		{
			$name = str_replace($R, $replase, $name);
			return $name;
		}
		return $name;
	}
	public function type_fieldset_open($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = $obj[2];
		$options = $THIS->get_options($key);
		return form_fieldset($name, $options);
	}
	public function type_fieldset_close()
	{
		return form_fieldset_close();
	}
	public function type_text($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->get_options($key);
		
		if(($value = $THIS->get_values($name)) !== FALSE) $options['option']['value'] = $value;
		if(!isset($options['option'])) $options['option'] = array();
		
		$value_array = array_merge(array('name' => $name), $options['option']);
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_input($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function type_hidden($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
	   
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->get_options($key);
		$value = '';
		if(isset($options['value'])) $value = $options['value'];
		if(($value_t = $THIS->get_values($name)) !== FALSE) $value = $value_t;

		return form_hidden($name, $value);
	}
	public function type_textarea($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->get_options($key);
		
		if(($value = $THIS->get_values($name)) !== FALSE) $options['option']['value'] = $value;
		if(!isset($options['option'])) $options['option'] = array();
		
		$value_array = array_merge(array('name'=>$name), $options['option']);
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_textarea($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function type_select($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		
		$options = $THIS->get_options($key);
		if(!isset($options['options'])) $soptions = array(); else $soptions = $options['options'];
		if(!isset($options['option'])) $options['option'] = array();
		if(!isset($options['value'])) $options['value'] = '';
		if(($value = $THIS->get_values($name)) !== FALSE) $options['value'] = $value;
		$srt = '';
		foreach($options['option'] 	as $key => $ms)
		{
			$srt .= ' '.$key.'="'.$ms.'" ';
		}
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_dropdown($name, $soptions, $options['value'], $srt).'</div><div class="CB"></div></div></div>';
	}
	public function type_checkbox($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $this->get_options($key);
		if(!isset($options['option'])) $options['option'] = array();
		if(($value = $THIS->get_values($name)) !== FALSE) $options['option']['checked'] = "checked";
		
		if(!isset($options['value'])) $options['value'] = 'null';
		$value_array = array('name'=>$name)+array('value'=>$options['value'])+$options['option'];
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div>'.form_checkbox($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function type_radio($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $this->get_options($key);
		if(!isset($options['option'])) $options['option'] = array();
		$value_array = array('name'=>$name)+$options['option'];
		if(($value = $THIS->get_values($name)) !== FALSE)
		{
			if(isset($options['option']['value']) && $options['option']['value'] == $value)
			{
				$value_array += array('checked' => 'checked');
			}
		}
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div>'.form_radio($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function type_file($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::create_field_name($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->get_options($key);
		if(!isset($options['value'])) $options['value'] = '';
		if(($value = $THIS->get_values($name)) !== FALSE) $options['value'] = $value;
		if(!isset($options['option'])) $options['option'] = array();
		
		$value_array = array_merge(array('name'=>$name), array($options['option']));
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.$options['value'].' '.form_upload($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function type_view($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = $obj[1];
		$values = $obj[2];
		$CI = & get_instance();
		return $CI->load->view($name,$values,true);
	}
	public function type_html($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		return $obj[1];
	}
	public function type_js($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		return '<script>'.$obj[1].'</script>';
	}
}
?>