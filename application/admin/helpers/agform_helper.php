<?php
class Agform
{
	private $values = false;
	private $name;
	public $id;
	public $href;
	private $tabs = false;
	private $block = array();
	private $CI;
	private $CKE = FALSE;
	
	private $formButtons = array();
	private $js_code = '';
	private $html_code = '';
	
function __construct($name, $id, $href)
	{
		$this->CI = & get_instance();
		$this->name = $name;
		$this->id = $id;
		$this->href = $href;
	}
public function enableCKE()
{
	$this->CKE = TRUE;
}
public function getId()
	{
		return $this->id;
	}
public function getHref()
	{
		return $this->href;
	}	
public function addButton($array)
	{
		$this->formButtons[] = $array;
	}
public function createButtons()
	{
		if($this->formButtons)
		{
			$html = '';
			foreach($this->formButtons as $ms)
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
public function getTabs()
{
	if($this->tabs) return $this->tabs;
	return false;
}

public function getBlock($key = false)
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
	
public function addTabs($key, $name)
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

public function addBlockToTabs($tabs_key, Agform_block $object)
	{
		$this->block[$tabs_key][] = $object;
		return $this;
	}
public function addBlock($object)
	{
		$this->block[] = $object;
		return $this;
	}	

public function addJsCode($code)
{
	$this->js_code .= $code;
}
public function getJsCode()
{
	return $this->js_code;
}
public function addHtmlCode($code)
{
	$this->html_code .= $code;
}
public function getHtmlCode()
{
	return $this->html_code;
}
	
public function renderForm()
	{
		if($this->CKE)
		{
			$this->CI->template->addJs('ckeditor','ckeditor');
			$this->CI->template->addJs('start_CKEditor','ckeditor');
		}
		$this->CI->template->addJs('jquery.gbcform');
		$this->CI->template->addCss('form');
		$this->CI->template->addTemplate('base/form/form',array('Form'=>$this),$this->name);
	}
}





class Agform_block
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
			if($langs)
			{
				foreach($langs as $key=>$ms)
				{
					$this->langs[$key] = $ms;
				}
			}
			if($values)
			{
				$this->values_prepare = $values;
				$this->createValuesArray();
			}
		}
	private function createValuesArray($p_key = '', $r_key = FALSE, $position = 1)
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
				$this->createValuesArray($part_key, $real_rey, $rec_pos);
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
	public function getObjects($key = FALSE, $key1 = FALSE)
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
	public function getOptions($key = false, $key1 = false)
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
	public function getValues($key = false)
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
	
	public function addObject($type, $name, $label = '', $options = array())
	{
		$this->objects[$this->i] = array($type, $name, $label);
		$this->options[$this->i] = $options;
		$this->i++;
		return $this->i-1;
	}
	public function addObjectTo($key, $type, $name, $label = '', $options = array())
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
	public function addView($name, $values = array())
	{
		$this->objects[$this->i] = array('view', $name, $values);
		$this->i++;
		return $this->i-1;
	}
	public function addViewTo($key, $name, $values = array())
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
	public function addHtml($html)
	{
		$this->objects[$this->i] = array('html', $html);
		$this->i++;
		return $this->i-1;
	}
	public function addHtmlTo($key, $html)
	{
		if(isset($this->objects[$key]))
		{
			$this->objects[$key]['chield'][$this->i] = array('html', $hrml);
		}
		else
		{
			$this->objects[$this->i] = array('html', $hrml);
		}
		$this->i++;
		return $this->i-1;
	}
	public function addJs($js)
	{
		$this->objects[$this->i] = array('script', $js);
		$this->i++;
		return $this->i-1;
	}
	public function BlockToHTML($id_form = 'def_form', $dkey = 'def_block', $ajax = FALSE)
	{
		$output = '<div id = "'.$dkey.'">';
		$this->HTML = '';
		if($this->langs)
		{
			
			$output .= $this->createTabs();
			$output_block = '';
			foreach($this->langs as $key => $ms)
			{
				$this->HTML = '';
				$output_block = $this->createBlock($key);
				$output .= $this->createTabsBlocks($output_block);
			}
			$output .= '</div>';
			if(!$ajax)
			{
				$output .= '<script>$("#'.$id_form.' #'.$dkey.' .langs_tabs ul").tabs("#'.$id_form.' #'.$dkey.' div.langs_tabs_block");</script>';
			}	
			return $output;
		}
		$output = $this->createBlock();
		return $output;
	}
	public function	createTabsBlock_NL()
	{
		$this->HTML = '';
		return $this->createTabsBlocks($this->createBlock()); 
	}
	public function	createTab($name)
	{
		return '<li><a href="#" class="href">'.$name.'</a></li>';;
	}	
	private function createTabs()
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
	private function createTabsBlocks($input)
	{
		$output = '<div class="langs_tabs_block"><div class="langs_tabs_block_padding">';
		$output .= $input;
		$output .= '</div></div>';
		return $output;
	}	
	private function createBlock($name = '', $ckey = FALSE)
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
					$this->HTML .= Agform_part::typeText($this, array($key,$ms), $name);
				break;
				case "textarea":
					$this->HTML .= Agform_part::typeTextarea($this, array($key,$ms), $name);
				break;
				case "select":
					$this->HTML .= Agform_part::typeSelect($this, array($key,$ms), $name);
				break;
				case "checkbox":
					$this->HTML .= Agform_part::typeCheckbox($this, array($key,$ms), $name);
				break;
				case "radio":
					$this->HTML .= Agform_part::typeRadio($this, array($key,$ms), $name);
				break;
				case "hidden":
					$this->HTML .= Agform_part::typeHidden($this, array($key,$ms), $name);
				break;
				case "view":
					$this->HTML .= Agform_part::typeView($this, array($key,$ms));
				break;
				case "html":
					$this->HTML .= Agform_part::typeHtml($this, array($key,$ms));
				break;
				case "js":
					$this->HTML .= Agform_part::typeJs($this, array($key,$ms));
				break;
			}
			if($ms[0] == 'fieldset')
			{
				$this->HTML .= Agform_part::typeFieldsetOpen($this, array($key,$ms));
				if(isset($ms['chield']))
				{
					$this->createBlock($name, $key);
				}
				$this->HTML .= Agform_part::typeFieldsetClose();
			}
			else
			{
				if(isset($ms['chield']))
				{
					$this->createBlock($name, array($key,$ms));
				}
			}
			if($this->background_i == 0) $this->background_i = 1; else $this->background_i = 0;
		}
		return $this->HTML;
	}	
}


class Agform_part
{
	public function createFieldName($name, $replase = '', $R = '$')
	{
		if($replase != '')
		{
			$name = str_replace($R, $replase, $name);
			return $name;
		}
		return $name;
	}
	public function typeFieldsetOpen($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = $obj[2];
		$options = $THIS->getOptions($key);
		return form_fieldset($name, $options);
	}
	public function typeFieldsetClose()
	{
		return form_fieldset_close();
	}	
	public function typeText($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->getOptions($key);
		if(!isset($options['value'])) $options['value'] = '';
		if(($value = $THIS->getValues($name)) !== FALSE) $options['value'] = $value;
		if(!isset($options['option'])) $options['option'] = array();
		
		$value_array = array_merge(array('name'=>$name),array('value'=>$options['value']),$options['option']);
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_input($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function typeHidden($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
	   
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->getOptions($key);
		$value = '';
		if(isset($options['value'])) $value = $options['value'];
		if(($value_t = $THIS->getValues($name)) !== FALSE) $value = $value_t;

		return form_hidden($name, $value);
	}
	public function typeTextarea($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		$options = $THIS->getOptions($key);
		if(!isset($options['value'])) $options['value'] = '';
		if(($value = $THIS->getValues($name)) !== FALSE) $options['value'] = $value;
		if(!isset($options['option'])) $options['option'] = array();
		
		$value_array = array_merge(array('name'=>$name),array('value'=>$options['value']),$options['option']);
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_textarea($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function typeSelect($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		
		$options = $THIS->getOptions($key);
		if(!isset($options['options'])) $soptions = array(); else $soptions = $options['options'];
		if(!isset($options['option'])) $options['option'] = array();
		if(!isset($options['value'])) $options['value'] = '';
		if(($value = $THIS->getValues($name)) !== FALSE) $options['value'] = $value;
		$srt = '';
		foreach($options['option'] 	as $key => $ms)
		{
			$srt .= ' '.$key.'="'.$ms.'" ';
		}
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div class="field">'.form_dropdown($name, $soptions, $options['value'], $srt).'</div><div class="CB"></div></div></div>';
	}
	public function typeCheckbox($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		$options = $this->getOptions($key);
		if(!isset($options['option'])) $options['option'] = array();
		if(($value = $THIS->getValues($name)) !== FALSE) $options['option']['checked'] = "checked";
		
		if(!isset($options['value'])) $options['value'] = 'null';
		$value_array = array('name'=>$name)+array('value'=>$options['value'])+$options['option'];
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div>'.form_checkbox($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function typeRadio($THIS, $keyms, $lang = '')
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = self::createFieldName($obj[1], $lang);
		$label = $obj[2];
		$options = $this->getOptions($key);
		if(!isset($options['option'])) $options['option'] = array();
		$value_array = array('name'=>$name)+$options['option'];
		if(($value = $THIS->getValues($name)) !== FALSE)
		{
			if(isset($options['option']['value']) && $options['option']['value'] == $value)
			{
				$value_array += array('checked' => 'checked');
			}
		}
		return '<div class="block_w_field_main"><div class="'.$THIS->background[$THIS->background_i].'"><label for="'.$name.'">'.$label.'</label><div>'.form_radio($value_array).'</div><div class="CB"></div></div></div>';
	}
	public function typeView($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		
		$name = $obj[1];
		$values = $obj[2];
		$CI = & get_instance();
		return $CI->load->view($name,$values,true);
	}
	public function typeHtml($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		return $obj[1];
	}
	public function typeJs($THIS, $keyms)
	{
		$key = $keyms[0];
		$obj = $keyms[1];
		return '<script>'.$obj[1].'</script>';
	}
}
/*
options:
type=text
{
value=>value
option = array();
}
type=select
{
'options'=>array(),
'value'=>value,
option = array();
}
type=checkbox
{
value=>value
option = array();
}
type=radio
{
value=>value
option = array();
}
type=textarea
{
value=>value
option = array();
}
*/
?>