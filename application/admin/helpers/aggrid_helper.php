<?php
class Aggrid_Helper extends AG_Model
{
	public  $GridName = '';
	private $CollectionObject = FALSE;
	private $GridButton = FALSE;
	private $I = 0;
	private $CheckboxActions = FALSE;
	private $GridObjects = array();
	private $GridValues = FALSE;
	private $GridRowCount = array();
	public  $AjaxOutput = FALSE;
	private $outputSearch = array();
	private $outputPages = array();
	private $Options = array(
			'limit'		=>	'10',
			'sort'		=>	'ID',
			'desc'		=>	'',
			'page'		=>	'1',
			'search'	=>	array(),
			'url'		=>  '',
			'init_fixed_buttons' => 1
	);
	private $extraSort = FALSE;
	
	function __construct($GridName = 'grid', $SES = TRUE, $options = FALSE)
		{
			if($options)
			{
				foreach($options as $key => $ms)
				{
					$this->setOptions($key, $ms);
				}
			}
			$this->GridName = $GridName;
			$this->load->helper('pages');
			if(isset($_GET['ajax']))
			{
				$this->AjaxOutput = true;
				$session_data = $this->Options;
				if($SES && !$session_data = $this->session->flashdata('GRID_'.$this->GridName))
				{
					$session_data = $this->Options;
				}
			}
			else
			{
				$session_data = $this->Options;
				if($SES && !$session_data = $this->session->flashdata('GRID_'.$this->GridName))
				{
					$session_data = $this->Options;
				}	
			}
			if(isset($_GET['limit']))
			{
				if(intval($_GET['limit'])>0)
				{
					$this->setOptions('limit', intval($_GET['limit']));
					$session_data['limit'] = intval($_GET['limit']);
				}
				else
				{
					$session_data['limit'] = $this->getOptions('limit');
				}
			}
			else
			{
				$this->setOptions('limit', $session_data['limit']);
			}
			if(isset($_GET['sort']))
			{
				$arr = explode('/', $_GET['sort']);
				if(is_array($arr) && count($arr)>1)
				{
					$this->setOptions('sort', $arr[0]);
					$this->setOptions('desc', $arr[1]);
					$session_data['sort'] = $arr[0];
					$session_data['desc'] = $arr[1];
				}
				else
				{
					$this->setOptions('sort', $_GET['sort']);
					$session_data['sort'] = $_GET['sort'];
					$session_data['desc'] = '';
				}
			}
			else
			{
				$this->setOptions('sort', $session_data['sort']);
				$this->setOptions('desc', $session_data['desc']);
			}
			if(isset($_GET['page']))
			{
				if(intval($_GET['page'])>0)
				{
					$this->setOptions('page', intval($_GET['page']));
					$session_data['page'] = intval($_GET['page']);
				}
				else
				{
					$session_data['page'] = $this->getOptions('page');
				}
			}
			else
			{
				$this->setOptions('page', $session_data['page']);
			}
			if(isset($_POST['search']))
			{
				$search_array = array();
				$search_string = rawurldecode(base64_decode(trim($_POST['search'])));
				$session_data['search'] = trim($_POST['search']);
				parse_str($search_string, $search_array);
				$this->setOptions('search', $search_array);
				$session_data['search'] = $_POST['search'];
			}
			else
			{
				$search_array = array();
				if(!is_array($session_data['search']))
				{
					$search_string = rawurldecode(base64_decode($session_data['search']));
					parse_str($search_string, $search_array);
				}
				$this->setOptions('search', $search_array);
			}
			$this->session->set_flashdata('GRID_'.$this->GridName, $session_data);
			
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
	public function renderGrid($return = FALSE, $return_when_ajax = FALSE)
	{
		if($this->GridValues === false)
		{
			$this->createDataArray();		
		}
		
			$this->template->addJs('jquery.gbcgrid');
			$this->template->addCss('grid');
			$this->template->addCss('pages');
			if($return && (!$this->AjaxOutput || $return_when_ajax))
			{
				return $this->load->view('base/grid/grid', array('Grid'=>$this), TRUE);
			}
			$this->template->addTemplate('base/grid/grid', array('Grid'=>$this), $this->GridName);
		
		if($this->AjaxOutput)
		{
			$this->template->is_ajax(TRUE);
		}	
	}
	//----------------------------------------------------------
	
	/**
	 * createDataArray
	 *
	 * Функция запускае функции сортировок и поиска и записывает в переменную массив данных для грида с БД
	 *
	 * @param	
	 * @return	$this
	 */
	public function createDataArray()
	{
		$this->GridValues = array();
		$this->setSearch();
		$this->setLimit();
		$this->setSort();
		try
		{
			//echo $this->db->_compile_select();
			$this->GridValues = $this->db->get()->result_array();
		}
		catch(Exeption $e)
		{
			$this->session->unset_flashdata('GRID_'.$this->GridName);
		}
		
		$this->createActions();
		return $this;
	}
	//----------------------------------------------------------
	
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
	
	/**
	 * setLimit
	 *
	 * Добавляет лимит для запроса в БД
	 *
	 * @param   	
	 * @return 	$this
	 */
	private function setLimit()
	{
		$limit = $this->getOptions('limit');
		$this_c = clone $this->db;
		$count = $this_c->count_all_results();
		unset($this_c);
		
		if($limit*$this->getOptions('page')>$count)
		{
			$this->setOptions('page',ceil($count/$limit));
		}
		if($this->getOptions('page')==0 || $this->getOptions('page')=='')
		{
			$this->setOptions('page',1);
		}
		$offset = ($this->getOptions('page')-1)*$limit;
		$showcount = $limit;
		$this->db->limit($showcount, $offset);
		
		$this->outputPages['count'] = $count;
		$this->outputPages['active'] = $this->getOptions('page');
		$this->outputPages['limit'] = $this->getOptions('limit');
		
		return $this;	
	}
	//----------------------------------------------------------
	
	/**
	 * setSort
	 *
	 * Добавляет сортировку для запроса в БД
	 *
	 * @param   	
	 * @return 	$this
	 */
	private function setSort()
	{
		$sort = $this->getOptions('sort');
		$desc = $this->getOptions('desc');
		$this->db->order_by($sort, $desc);
		if($this->extraSort)
		{
			foreach($this->extraSort as $ms)
			{
				$this->db->order_by($ms[0], $ms[1]);
			}	
		}
	}
	//----------------------------------------------------------
	
	/**
	 * addExtraSort
	 *
	 * Добавляет дополнительную сортировку для запроса в БД после основной сортировки
	 *
	 * @param $field - поле в БД, $desc тип сортировки  	
	 * @return 	$this
	 */
	public function addExtraSort($field, $desc = '')
	{
		$this->extraSort[] = array($field, $desc);
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * setSearch
	 *
	 * Добавляет поиск для запроса в БД
	 *
	 * @param   	
	 * @return 	$this
	 */
	private function setSearch()
	{
		$sort_array = $this->getOptions('search');
		foreach($sort_array as $key=>$ms)
		{
			if(strpos($key,'&'))
			{
				$srch = explode('&',$key);
				if($srch[1] == 'from')
				{
					$this->db->where($this->getSearchTable($srch[0]) .' >=', $ms);
					$this->outputSearch[$srch[0]][0] = $ms;
				}
				if($srch[1] == 'to')
				{
					$this->db->where($this->getSearchTable($srch[0]) .' <=', $ms);
					$this->outputSearch[$srch[0]][1] = $ms;
				}
				if($srch[1] == 'Dfrom')
				{
					$this->db->where('DATE('.$this->getSearchTable($srch[0]) .') >=', $ms);
					$this->outputSearch[$srch[0]][0] = $ms;
				}
				if($srch[1] == 'Dto')
				{
					$this->db->where('DATE('.$this->getSearchTable($srch[0]) .') <=', $ms);
					$this->outputSearch[$srch[0]][1] = $ms;
				}				
			}
			else
			{
				$this->db->where($this->getSearchTable($key) .' LIKE',"%".$ms."%");
				$this->outputSearch[$key] = $ms;
			}		
		}
	}
	//----------------------------------------------------------
	
	/**
	 * getSearchTable
	 *
	 * Ищет столбец с нужными опциями для регенации строки поиска вида ИМЯ подя, или Таблицы.Имя подя, или Индификатор таблицы.Имя поля
	 *
	 * @param $name  	
	 * @return 	string
	 */
	private function getSearchTable($name)
	{
		foreach($this->getGridObjects() as $ms)
		{
			if($ms->getOption('searchname')==$name || $ms->getOption('index')==$name)
			{
				if($table = $ms->getOption('searchtable'))
				{
					return $table.'.`'.$name.'`';
				}
				else
				{
					return '`'.$name.'`';
				}
			}
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
		if(isset($this->Options[$key]))
		{
			$this->Options[$key] = $value;
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
			if(isset($this->Options[$key]))
			{
				return $this->Options[$key];
			}
			return false;	
		}
		return $this->Options;
	}
	//----------------------------------------------------------
	
	/**
	 * getRowCount
	 *
	 * Возращает количество count() массива данных, переменная генерирется в функцие setLimit
	 *
	 * @param 
	 * @return 	int
	 */
	public function getRowCount()
	{
		return $this->GridRowCount;
	}
	//----------------------------------------------------------
	
	/**
	 * getActiveSort
	 *
	 * Возращает данные для постройки активной сортировки
	 *
	 * @param 
	 * @return 	int
	 */
	public function getActiveSort(GridObject $class)
	{
		if($class->getOption('index') == $this->getOptions('sort'))
		{
			if($this->getOptions('desc')=='')
			{
				return 1;
			}
			else if($this->getOptions('desc')=='DESC' || $this->getOptions('desc')=='desc')
			{
				return 2;
			}	
		}
		else
		{
			return false;
		}	
	}
	//----------------------------------------------------------
	
	
	public function getOutputSearch()
	{
		return $this->outputSearch;
	}
	public function getPagesHtml()
	{
		$pages_array = getPagesArray($this->outputPages['count'], $this->outputPages['active'], $this->outputPages['limit']);
		return $this->load->view('base/grid/pages',array('GridPages'=>$pages_array),true);
	}
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
	
	public function addGridValues($fkey, $key, $value, $variables = FALSE)
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
				$this->GridValues[$gkey][$fkey] = $val;
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
		'tdwidth'	=> false,
		'sortable'  => false,
		'filter'    => false
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
public function getHtmlSearch($data)
	{
		if($this->getOption('filter'))
		{
			$type = $this->getOption('type');
			if($type == 'text')
			{
				return $this->getHtmlSearchText($data);
			}
			else if($type == 'number')
			{
				return $this->getHtmlSearchNumber($data);
			}
			else if($type == 'select')
			{
				return $this->getHtmlSearchSelect($data);
			}
			else if($type = 'date')
			{
				return $this->getHtmlSearchDate($data);
			}
			else
			{
				return $this->getHtmlSearchText($data);
			}				
		}
		if($this->getOption('type') == 'action')
		{
			return anchor('#','Поиск',array('class'=>'search_button','rel'=>'search')).anchor('#','Очистить',array('class'=>'search_button','rel'=>'clear'));
		}
		return '';
	}
private function getHtmlSearchText($data)
	{
		$val1 = '';
		if(isset($data[$this->getSearchFieldName()])){$val1=$data[$this->getSearchFieldName()];}
		return '<div class="input">'.form_input(array('name'=>$this->getSearchFieldName(), 'value'=>$val1, 'autocomplete'=>'off')).'</div>';
	}
private function getHtmlSearchNumber($data)
	{
		$val1 = '';
		$val2 = '';
		if(isset($data[$this->getSearchFieldName()][0])){$val1=$data[$this->getSearchFieldName()][0];}
		if(isset($data[$this->getSearchFieldName()][1])){$val2=$data[$this->getSearchFieldName()][1];}
		return '<div class="input_interval">От: '.form_input(array('name'=>$this->getSearchFieldName().'&from', 'value'=>$val1, 'autocomplete'=>'off')).'</div><div class="input_interval">До: '.form_input(array('name'=>$this->getSearchFieldName().'&to', 'value'=>$val2, 'autocomplete'=>'off')).'</div>';
	}
private function getHtmlSearchSelect($data)
	{
		$val1 = '';
		if(isset($data[$this->getSearchFieldName()])){$val1=$data[$this->getSearchFieldName()];}
		return '<div class="input">'.form_dropdown($this->getSearchFieldName(), $this->getOption('options'), $val1, 'autocomplete="off"').'</div>';
	}
private function getHtmlSearchDate($data)
	{
		$val1 = '';
		$val2 = '';
		if(isset($data[$this->getSearchFieldName()][0])){$val1=$data[$this->getSearchFieldName()][0];}
		if(isset($data[$this->getSearchFieldName()][1])){$val2=$data[$this->getSearchFieldName()][1];}
		return '<div class="input_interval">От: '.form_input(array('name'=>$this->getSearchFieldName().'&Dfrom', 'value'=>$val1, 'autocomplete'=>'off', 'class'=>'datepicker', 'readonly'=>'readonly')).'</div><div class="input_interval">До: '.form_input(array('name'=>$this->getSearchFieldName().'&Dto', 'value'=>$val2, 'autocomplete'=>'off', 'class'=>'datepicker', 'readonly'=>'readonly')).'</div>';
	}
private function getSearchFieldName()
	{
		if($name = $this->getOption('searchname'))
		{
			return $name;
		}
		else
		{
			return $this->getOption('index');
		}	
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