<?php
class Aggrid_tree_Helper extends AG_Model
{
	public  $GridName = '';
	private $CollectionObject = FALSE;
	private $GridButton = FALSE;
	private $I = 0;
	private $CheckboxActions = FALSE;
	private $GridObjects = array();
	private $GridValues = FALSE;
	private $T_PID = FALSE;
	private $PID = array();
	
	public $AjaxOutput = FALSE;
	public $AjaxShowAll = FALSE;
	
	private $recursVal = array();
	
	private $options = array('ID' => 'ID', 'id_parent' => 'id_parent', 'level' => 'level');
	
	function __construct($GridName = 'grid_tree', $options = array())
		{	
			$this->GridName = $GridName;	
			foreach($options as $key => $ms)
			{
				$this->options[$key] = $ms;
			}
			
			if(isset($_GET['ajax']))
			{
				$this->AjaxOutput = TRUE;
			}
			
			if(isset($_GET['ajax_show_all']))
			{
				$this->AjaxShowAll = TRUE;
			}
			
			/*if(isset($_GET['no_template_load']))
			{
				if($this->input->post('id'))
				{
					$this->session->unsetData(array('TREE_GRID_'.$this->GridName, $this->input->post('id')), FALSE);
				}	
				exit;
			}*/
			//$this->PID = $this->session->getData('TREE_GRID_'.$this->GridName, FALSE);
			parent::__construct();
		}
	
	/**
	 * setCheckboxActions
	 *
	 * Создает объект с чекбоксами и список действий для них
	 *
	 * @param	$array
	 * @return	none
	 */
	public function setCheckboxActions($array)
	{
		if(isset($array['actions']))
		{
			$this->CheckboxActions['actions'] = $array['actions'];
			$this->CheckboxActions['select_name'] = $array['select_name'];
			
			$index = $array['index'];
			$checkbox_name = 'grid_checkbox[]';
			$options = '';
			if(isset($array['checkbox_name']))
			{
				$checkbox_name = $array['checkbox_name'].'[]';
			}
			if(isset($array['options']))
			{
				$options = $array['options'];
			}
			$this->addGridColumn(
				array(
					'',
					array
						(
							'index'		 => $index,
							'type'		 => 'checkbox',
							'checkbox_name' => $checkbox_name,
							'tdwidth' => '3%',
							'option_string' => 'align="center"',
							'tdalign' => 'center',
							'checkbox_value' => 'ID',
							'options' => $options
						)
					)
			);
		}
	}
	//----------------------------------------------------------
	
	/**
	 * addGridColumn
	 *
	 * Добавляет столбик для вывода
	 *
	 * @param	$array
	 * @return	$this
	 */
	public function addGridColumn($array)
	{
		$this->I++;
		$this->GridObjects[$this->I] = new GridObject($array);
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * getGridObjects
	 *
	 * Функция для получения массива объектов столбцов 
	 *
	 * @param	
	 * @return	$this->GridObjects
	 */
	public function getGridObjects()
	{
		return $this->GridObjects;
	}
	//----------------------------------------------------------
	
	/**
	 * renderGrid
	 *
	 * Функция генерации грида
	 *
	 * @param	
	 * @return	$this->load->view - сгенерированый HTML грида
	 */
	public function renderGrid()
	{
		if($this->GridValues === false)
		{
			$this->createDataArray();	
		}
		
			$this->template->add_js('jquery.gbcgrid_tree');
			$this->template->add_css('grid');
			
			$this->template->add_template('base/grid_tree/grid_tree', array('Grid'=>$this), $this->GridName);
		
		if($this->AjaxOutput || $this->AjaxShowAll)
		{
			$this->template->is_ajax(TRUE);
		}
	}
	//----------------------------------------------------------
	
	/**
	 * setOptions
	 *
	 * Устанавливает опции сортировки, лимита и поиска
	 *
	 * @param $key, $value  	
	 * @return 	true/false
	 */
	public function setOptions($key, $value)
	{
		if(isset($this->options[$key]))
		{
			$this->options[$key] = $value;
			return true;
		}
		return false;	
	}
	//----------------------------------------------------------
	
	/**
	 * getOptions
	 *
	 * Возращает опцию или весь массив опций сортировки, поиска и лимита
	 *
	 * @param $key
	 * @return 	опция
	 */
	public function getOptions($key = false)
	{
		if($key)
		{
			if(isset($this->options[$key]))
			{
				return $this->options[$key];
			}
			return false;	
		}
		return $this->options;
	}
	//----------------------------------------------------------
	
	/**
	 * createDataArray
	 *
	 * Функция записывает в переменную массив данных для грида с БД
	 *
	 * @param	
	 * @return	$this
	 */
	public function createDataArray()
	{
		$this->GridValues = array();
		try
		{
			//echo var_dump($_SESSION);
			$this->updateWhere();
			//echo $this->db->_compile_select();
			$VAL = $this->db->get()->result_array();
			$in_session = FALSE;
			foreach($VAL as $ms)
			{
				if($ms['id_parent'] !== NULL)
				{
					$this->recursVal['parent'][$ms[$this->getOptions('id_parent')]][] = $ms;
				}
				else
				{
					$this->recursVal['main'][] = $ms;
				}
				if($this->AjaxOutput && $this->input->post('id') && $ms[$this->getOptions('ID')] == $this->input->post('id')) $in_session = array($ms[$this->getOptions('level')], $ms[$this->getOptions('id_parent')], $ms[$this->getOptions('ID')]);
			}
			if(isset($this->recursVal['main']))
			{
				$this->recursBuildTree();
			}
			if($this->AjaxOutput && $in_session)
			{
				if($in_session[1] === NULL)
				{
					$this->session->set_userdata(array('TREE_GRID_'.$this->GridName, $in_session[0], $in_session[2], $in_session[2]), 1);
				}
				else
				{
					$this->session->set_userdata(array('TREE_GRID_'.$this->GridName, $in_session[0], $in_session[1], $in_session[2]), 1);
				}
			}
		}
		catch(Exeption $e)
		{
			//$this->session->unset_flashdata('GRID_'.$this->GridName);
		}
		$this->createActions();
		return $this;
	}
	//----------------------------------------------------------
	
	private function recursBuildTree($id_parent = FALSE)
	{
		if($id_parent)
		{
			if(!isset($this->recursVal['parent'][$id_parent])) return FALSE;
			$array = $this->recursVal['parent'][$id_parent];
		}
		else
		{
			$array = $this->recursVal['main'];
		}
		foreach($array as $ms)
		{
			$this->GridValues[] = $ms;
			$num = count($this->GridValues)-1;
			if($this->recursBuildTree($ms[$this->getOptions('ID')]))
			{
				$this->GridValues[$num]['have_chield'] = 'unload icon_minus';
			}
			else
			{
				$this->GridValues[$num]['have_chield'] = 'load icon_plus';
			}
		}
		return TRUE;
	}
	
	private function deleteParents($id_parent)
	{
		if($array = $this->session->userdata('TREE_GRID_'.$this->GridName))
		{
			$B = FALSE;
			for($i=1; $i<=count($array); $i++)
			{
				foreach($array[$i] as $mkey => $ms)
				{
					foreach($ms as $key => $val)
					{
						if($key == $id_parent)
						{
							$this->session->unset_userdata(array('TREE_GRID_'.$this->GridName, $i, $mkey, $key));
							if(count($this->session->userdata(array('TREE_GRID_'.$this->GridName, $i, $mkey)))==0)
							{
								$this->session->unset_userdata(array('TREE_GRID_'.$this->GridName, $i, $mkey));
								if(count($this->session->userdata(array('TREE_GRID_'.$this->GridName, $i)))==0)
								{
									$this->session->unset_userdata(array('TREE_GRID_'.$this->GridName, $i));
								}
							}
							$B = TRUE;
							break;
						}
					}
					if($B) break;
				}
				if($B) break;
			}
		}
	}
	
	private function recursGetParents($level = 1, $id_parent = FALSE)
	{
		if($id_parent)
		{
			if($array = $this->session->userdata(array('TREE_GRID_'.$this->GridName, $level, $id_parent)))
			{
				$plevel = $level+1;
				foreach($array as $key => $ms)
				{
					$this->PID[] = $key;
					$this->recursGetParents($plevel , $key);
				}
			}
			if($this->T_PID && $this->session->userdata(array('TREE_GRID_'.$this->GridName, $level+1, $this->T_PID))) 
			{
				$this->recursGetParents($level+1 , $this->T_PID);
				$this->T_PID = FALSE;
			}
		}
		else
		{
			if($array = $this->session->userdata(array('TREE_GRID_'.$this->GridName, $level)))
			{
				$plevel = $level+1;
				foreach($array as $key => $ms)
				{
					if(count($ms)>0)
					{
						$this->PID[] = $key;
						$this->recursGetParents($plevel , $key);
					}	
				}
			}
			if($this->T_PID && $this->session->userdata(array('TREE_GRID_'.$this->GridName, $level+1, $this->T_PID))) 
			{
				$this->recursGetParents($level+1 , $this->T_PID);
				$this->T_PID = FALSE;
			}	
		}
	}
	
	private function updateWhere()
	{
		$this->db->where('level',1);
		if($this->input->post('id_unload'))
		{
			$this->deleteParents($this->input->post('id_unload'));
		}
		if($this->input->post('id'))
		{
			$this->PID[] = $this->input->post('id');
			$this->T_PID = $this->input->post('id');
		}
		$this->recursGetParents(1);
		if(count($this->PID)>0)
		{
			$this->db->or_where_in($this->getOptions('id_parent'), $this->PID);
		}
	}
	
	/**
	 * getDataArray
	 *
	 * Возвращает массив данных с базы данных по ключу массива или в полном обьеме, содержир данные только после запуска createDataArray
	 *
	 * @param	$key = false
	 * @return	array
	 */
	public function getDataArray($key = false)
	{
		if($key)
			{
				if(isset($this->GridValues[$key]))
				{
					return $this->GridValues[$key];
				}
				else
				{
					return false;
				}
			}
		return $this->GridValues;	
	}
	//----------------------------------------------------------
	
	/**
	 * setDataArray
	 *
	 * Функция устанавливает данные для вывода, не доработана!!
	 *
	 * @param   $value,$key = false	
	 * @return	$this
	 */
	public function setDataArray($value,$key = false)
	{
		if($key)
		{
			$this->GridValues[$key] = $value;
		}
		else if(is_array($value))
		{
			$this->GridValues = $value;
		}
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * createActions
	 *
	 * Создает блок с действиями и чекбоксами для строки вывода
	 *
	 * @param   	
	 * @return	
	 */
	private function createActions()
	{
		foreach($this->getGridObjects() as $ms)
		{
			if($ms->getOption('type') == 'action')
			{
				foreach($this->GridValues as $key=>$row)
				{
					$htmlActions = $ms->createRowActions($row);
					$index = $ms->getOption('index');
					if(isset($this->GridValues[$key][$index]))
					{
						$this->GridValues[$key][$index] .= $htmlActions;
					}
					else
					{
						$this->GridValues[$key][$index] = $htmlActions;
					}
				}	
			}
			if($ms->getOption('type') == 'checkbox')
			{
				$index = $ms->getOption('index');	
				foreach($this->GridValues as $key=>$row)
				{
					$htmlActions = $ms->createRowActions($row);
					if(isset($this->GridValues[$key]['checkbox'.$index]))
					{
						$this->GridValues[$key]['checkbox'.$index] .= $htmlActions;
					}
					else
					{
						$this->GridValues[$key]['checkbox'.$index] = $htmlActions;
					}
				}
				$ms->setOption('index', 'checkbox'.$index);	
			}
		}
	}
	//----------------------------------------------------------
	
	
	public function getSelectCheckboxActions()
	{
		if($this->CheckboxActions['actions'])
		{
			$actions = array(''=>'Выберите действие');
			$actions += $this->CheckboxActions['actions'];
			return form_dropdown($this->CheckboxActions['select_name'], $actions, '', 'id="action_submit" autocomplete="off"');
		}
		return false;
	}
	public function addButton($array)
	{
		$this->GridButton[] = $array;
	}
	public function	createButtons()
	{
		if($this->GridButton)
		{
			$html = '';
			foreach($this->GridButton as $ms)
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
	public function updateGridValues($key, $array)
	{
		if(!$this->GridValues) return $this;
		foreach($this->GridValues as $gkey=>$ar)
		{
			if(isset($ar[$key]) && isset($array[$ar[$key]]))
			{
				$this->GridValues[$gkey][$key] = $array[$ar[$key]];
			}
		}
		return $this;
	}
	
	public function setGridValues($key, $value, $variables = FALSE)
	{
		if(!$this->GridValues) return $this;
		foreach($this->GridValues as $gkey=>$ar)
		{
			if(isset($ar[$key]))
			{
				$val = $value;
				if($variables && is_array($variables))
				{
					foreach($variables as $kms => $ms)
					{
						$val = str_replace($kms, $ar[$ms], $val);
					}
				}
				$this->GridValues[$gkey][$key] = $val;
			}
		}
		return $this;
	}
}












class GridObject
{
	private $title = '';
	private $options = array(
		'header'	=> 'Field',
		'type' 		=> 'text',
		'tdwidth'	=> false
	);
function __construct($array)
	{
		if(!is_array($array)) return false;
		if(isset($array[1]) && is_array($array[1]))
		{
			$this->title = $array[0];
			$this->options = $array[1];
		}	
	}
public function getTitle()
	{
		return $this->title;
	}	
public function getOption($key)
	{
		if(isset($this->options[$key]))
		{
			return $this->options[$key];
		}
		return false;	
	}
public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}	
public function getHtmlTitle()
	{
		return "<div>".$this->getTitle()."</div>";
	}

public function createRowActions($values)
	{
		$htmlActions = '';
		if($this->getOption('type') == 'checkbox')
		{
			$htmlActions = $this->createRowCheckbox($values);
		}
		else
		{
			foreach($this->getOption('actions') as $ms)
			{
				if($ms['type'] == 'link')
				{
					$htmlActions .= $this->createRowLink($ms, $values);
				}
			}
		}	
		return $htmlActions;
	}
private function createRowCheckbox($values)
	{
		$array = array(
			'name' => $this->getOption('checkbox_name'),
			'value' => $values[$this->getOption('index')]
		);
		if(is_array($this->getOption('options')))
		{
			$array += $this->getOption('options');
		}
		$array += array(
			'id'=>'action_checkbox',
			'autocomplete'=>'off'
		);	
		return form_checkbox($array);
	}
private function createRowLink($link_array, $values)
	{
		$href = $link_array['href'];
		$href_values = $link_array['href_values'];
		foreach($href_values as $key=>$ms)
		{
			$href = str_replace('$'.($key+1), $values[$ms], $href);
		}
		if(!isset($link_array['html'])) $link_array['html'] = '';
		if(!isset($link_array['options'])) $link_array['options'] = '';
		return anchor($href, $link_array['html'], $link_array['options']);
	}
}
?>