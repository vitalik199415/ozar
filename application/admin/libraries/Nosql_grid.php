<?php
class Nosql_grid extends AG_Model
{
	private $grid_key = 'GRID_';
	private	$grid_name = '';
	private $grid_buttons = FALSE;
	private $select_checkbox_actions = array();
	
	private $grid_columns = array();
	
	private $grid_data = FALSE;
	private $grid_row_count = array();
	
	private $grid_search 	= array();
	private $grid_pages 	= array();
	
	private $grid_options = array(
			'limit'		=>	'10',
			
			'desc'		=>	'',
			'page'		=>	'1',
			'search'	=>	array(),
			'url'		=>  ''
	);
	
	public $init_fixed_buttons = 1;
	public $filter_block = FALSE;
	
	private $extra_sort = FALSE;
	private $extra_select_qty = FALSE;
	public  $ajax_output = FALSE;
	
	function __construct()
	{
		$this->template->add_js('jquery.aggrid', 'libraries');
		$this->template->add_css('grid');
		//$this->template->add_css('pages');
		parent::__construct();
	}
	private function refresh_grid()
	{
		$this->grid_key = 'GRID_';
		$this->grid_name = '';
		$this->grid_buttons = FALSE;
		$this->select_checkbox_actions = array();
		
		$this->grid_columns = array();
		
		$this->grid_data = FALSE;
		$this->grid_row_count = array();
		
		$this->grid_search 	= array();
		$this->grid_pages 	= array();
		
		$this->grid_options = array(
				'url'		=>  '',
				'search'	=>	array()
		);
		
		$this->init_fixed_buttons = 0;
		
		$this->ajax_output = FALSE;
	}
	
	/**
	 * _init_grid
	 *
	 * Инициализация грида
	 *
	 * @param	$grid_name - название грида, должно быть уникальное для всех гридов панели
	 * @param	$options - массив опций сортировки поиска и т.д.
	 * @param	$debug - установить в TRUE для отключения сохранения данных сортировки в сесие для отладки запросов в базу данных.
	 * @return	none
	 */
	public function _init_grid($grid_name, $options = array())
	{
		$this->refresh_grid();
		$this->grid_name = $grid_name;
		foreach($options as $key => $ms)
		{
			$this->set_options($key, $ms);
		}
		if($this->input->post('ajax'))
		{
			$this->ajax_output = TRUE;
			$this->set_grid_options_data();
		}
	}
	/**
	 * set_grid_options_data
	 *
	 * Инициализация опций сортировки и поиска
	 *
	 * @param	
	 * @return	none
	 */
	private function set_grid_options_data()
	{
		if($search = $this->input->post('search'))
		{
			$search_array = array();
			$search_string = rawurldecode(base64_decode(trim($search)));
			parse_str($search_string, $search_array);
			$grid_options_data['search'] = $search_array;
		}
		//set_data
		if(isset($grid_options_data) && is_array($grid_options_data))
		{
			foreach($grid_options_data as $key => $ms)
			{
				$this->set_options($key, $ms);
			}
		}
	}
	
	public function get_grid_name()
	{
		return $this->grid_name;
	}
	
	/**
	 * set_checkbox_actions
	 *
	 * Создает объект с чекбоксами и список действий для них
	 *
	 * @param	$array
	 * @return	none
	 */
	public function set_checkbox_actions($index, $ch_name, array $select, array $options = array())
	{
		if(isset($select['name']) && isset($select['options']))
		{
			$this->select_checkbox_actions['options'] 	= $select['options'];
			$this->select_checkbox_actions['name'] 		= $select['name'];
		}
		else
		{
			$this->select_checkbox_actions['options'] 	= NULL;
			$this->select_checkbox_actions['name'] 		= NULL;
		}
		$this->add_column(
			array
			(
				'index'		 => $index,
				'type'		 => 'checkbox',
				'name' => $ch_name,
				'tdwidth' => '3%',
				'option_string' => 'align="center"',
				'tdalign' => 'center',
				'value' => 'ID',
				'options' => $options
			)
		);
	}
	//----------------------------------------------------------
	
	/**
	 * get_select_actions_for_checkbox
	 *
	 * Генерирует select со списком возможных действий с выбраными элементами грида
	 *
	 * @return	none
	 */
	public function render_select_actions_for_checkbox()
	{
		if(array_key_exists('name', $this->select_checkbox_actions) && array_key_exists('options', $this->select_checkbox_actions))
		{
			if($this->select_checkbox_actions['name'] == NULL || $this->select_checkbox_actions['options'] == NULL)
			{
				return TRUE;
			}
			$actions = array('' => 'Выберите действие');
			$actions += $this->select_checkbox_actions['options'];
			return form_dropdown($this->select_checkbox_actions['name'], $actions, '', 'id="action_submit" autocomplete="off"');
		}
		return false;
	}
	//----------------------------------------------------------
	
	/**
	 * addGridColumn
	 *
	 * Добавляет столбик грида.
	 *
	 * @param	array $array
	 * @param	string $label
	 * @return	$this
	 */
	public function add_column($array, $label = '')
	{
		$this->grid_columns[] = new Nosql_grid_column($array, $label);
		if(isset($array['filter']) && $array['filter'] == TRUE) $this->filter_block = TRUE;
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * get_columns
	 *
	 * Функция для получения массива объектов столбцов 
	 *
	 * @param	
	 * @return	array Nosql_grid_column $this->grid_columns
	 */
	public function get_columns()
	{
		return $this->grid_columns;
	}
	//----------------------------------------------------------
	
	/**
	 * render_grid
	 *
	 * Функция генерации грида
	 *
	 * @param	
	 * @return	$this->load->view - сгенерированый HTML грида
	 */
	public function render_grid($return = FALSE, $return_when_ajax = FALSE)
	{
		if($this->grid_data !== false)
		{
			$this->create_grid_actions();
			$this->set_search();
			$data = clone($this);
			if($return)
			{
				$this->refresh_grid();
				return $this->load->view('libraries/grid/nosql_grid', array('grid' => $data), TRUE);
			}
			$this->template->add_template('libraries/grid/nosql_grid', array('grid' => $data), $this->grid_name);
			
			unset($data);
			if($this->ajax_output)
			{
				$this->template->is_ajax(TRUE);
			}
			$this->refresh_grid();
		}
	}
	//----------------------------------------------------------
	
	/**
	 * get_grid_data
	 *
	 * Возвращает массив данных с базы данных по ключу массива или в полном обьеме, содержир данные только после запуска create_grid_data
	 *
	 * @param	$key = false
	 * @return	array
	 */
	public function get_grid_data($key = false)
	{
		if($key)
		{
			if(isset($this->grid_data[$key]))
			{
				return $this->grid_data[$key];
			}
			else
			{
				return false;
			}
		}
		return $this->grid_data;
	}
	//----------------------------------------------------------
	
	public function set_grid_data($data)
	{
		$this->grid_data = $data;
		return $this;
	}
	
	/**
	 * update_grid_data
	 *
	 * Функция устанавливает обновляет данные для вывода. $key - ключ в массиве данных, $array - массив вида array('Какое значение заменяем' => 'На какое значение меняем')
	 *
	 * @param   string $key 
	 * @param   array $array	
	 * @return	$this
	 */
	public function update_grid_data($key, $array)
	{
		if(!$this->grid_data) return FALSE;
		foreach($this->grid_data as $gkey => $ar)
		{
			if(isset($ar[$key]) && isset($array[$ar[$key]]))
			{
				$this->grid_data[$gkey][$key] = $array[$ar[$key]];
			}
		}
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * update_grid_data_using_string
	 *
	 * Функция обновляет значение колонки с ключем $key строкой $string, в которой все вхождедия ключей массива $variables будут заменены на значения колонки с ключами значений массива $variables 
	 * Пример update_grid_data_using_string("sort", "<a class='arrow_down' href='$1' title='Смена позиции: Опустить'></a><a class='arrow_up' href='$1' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
	 * заменит значения с ключем sort на строку, в которой $1 будет заменен на значение с массива строки грида с ключем ID
	 *
	 * @param   string $key 
	 * @param   string $string	
	 * @param   array $variables	
	 * @return	$this
	 */
	public function update_grid_data_using_string($key, $string, array $variables)
	{
		if(!$this->grid_data) return FALSE;
		foreach($this->grid_data as $gkey=>$ar)
		{
			if(isset($ar[$key]))
			{
				$val = $this->ugd_value($string, $ar, $variables);
				$this->grid_data[$gkey][$key] = $val;
			}
		}
		return $this;
	}
	//----------------------------------------------------------
	
	/**
	 * add_grid_data_using_string
	 *
	 * Функция добавит колонку с ключем $fkey со строкой $string, в которой все вхождедия ключей массива $variables будут заменены на значения колонки с ключами значений массива $variables 
	 * Пример add_grid_data_using_string("str", "sort", "<a class='arrow_down' href='$1' title='Смена позиции: Опустить'></a><a class='arrow_up' href='$1' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
	 * добавит колонку с ключем str со строкой $string, в которой $1 будет заменен на значение с массива строки грида с ключем ID
	 *
	 * @param   string $fkey 
	 * @param   string $key 
	 * @param   string $string	
	 * @param   array $variables	
	 * @return	$this
	 */
	public function add_grid_data_using_string($fkey, $key, $string, array $variables)
	{
		if(!$this->grid_data) return $this;
		foreach($this->grid_data as $gkey=>$ar)
		{
			if(isset($ar[$key]))
			{
				$val = $this->ugd_value($string, $ar, $variables);
				$this->grid_data[$gkey][$fkey] = $val;
			}
		}
		return $this;
	}
	//----------------------------------------------------------
	
	private function ugd_value($val, $ar, $variables)
	{
		foreach($variables as $kms => $ms)
		{
			$val = str_replace($kms, $ar[$ms], $val);
		}
		return $val;
	}
	
	/**
	 * create_grid_actions
	 *
	 * Создает блок с действиями и чекбоксами для строки вывода
	 *
	 * @param   	
	 * @return	
	 */
	private function create_grid_actions()
	{
		foreach($this->get_columns() as $ms)
		{
			if($ms->get_options('type') == 'action')
			{
				foreach($this->grid_data as $key => $row)
				{
					$html_actions = $ms->render_row_actions($row);
					$index = $ms->get_options('index');
					if(isset($this->grid_data[$key][$index]))
					{
						$this->grid_data[$key][$index] .= $html_actions;
					}
					else
					{
						$this->grid_data[$key][$index] = $html_actions;
					}
				}	
			}
			if($ms->get_options('type') == 'checkbox')
			{
				$index = $ms->get_options('index');	
				foreach($this->grid_data as $key=>$row)
				{
					$htmlActions = $ms->render_row_actions($row);
					if(isset($this->grid_data[$key]['checkbox'.$index]))
					{
						$this->grid_data[$key]['checkbox'.$index] .= $htmlActions;
					}
					else
					{
						$this->grid_data[$key]['checkbox'.$index] = $htmlActions;
					}
				}
				$ms->set_options('index', 'checkbox'.$index);	
			}
		}
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
	private function set_search()
	{
		$search_array = $this->get_options('search');
		foreach($search_array as $key => $ms)
		{
			if(strpos($key,'-'))
			{
				$srch = explode('-', $key);
				if($srch[1] == 'from')
				{
					if($S = $this->get_search_row($srch[0]))
					{
						$this->grid_search[$srch][0] = $ms;
						foreach($this->grid_data as $skey => $sms)
						{
							if($sms[$S] < $ms)
							{
								unset($this->grid_data[$skey]);
							}
						}
					}
				}
				if($srch[1] == 'to')
				{
					if($S = $this->get_search_row($srch[0]))
					{
						$this->grid_search[$srch][1] = $ms;
						foreach($this->grid_data as $skey => $sms)
						{
							if($sms[$S] > $ms)
							{
								unset($this->grid_data[$skey]);
							}
						}
					}
				}
				/*if($srch[1] == 'date_from')
				{
					if($SF = $this->get_search_table($srch[0]))
					{
						$this->db->where('DATE('. $SF .') >=', $ms);
						$this->grid_search[$srch[0]][0] = $ms;
						if($this->extra_select_qty != FALSE)
						{
							$this->extra_select_qty->where('DATE('. $SF .') >=', $ms);
						}
					}	
				}
				if($srch[1] == 'date_to')
				{
					if($SF = $this->get_search_table($srch[0]))
					{
						$this->db->where('DATE('. $SF .') <=', $ms);
						$this->grid_search[$srch[0]][1] = $ms;
						if($this->extra_select_qty != FALSE)
						{
							$this->extra_select_qty->where('DATE('. $SF .') <=', $ms);
						}
					}	
				}*/				
			}
			else
			{
				$srch = $key;
				if($S = $this->get_search_row($srch))
				{
					$this->grid_search[$srch] = $ms;
					foreach($this->grid_data as $skey => $sms)
					{
						if(stripos($sms[$S], $ms) === FALSE)
						{
							unset($this->grid_data[$skey]);
						}
					}
				}
			}		
		}
	}
	//----------------------------------------------------------
	
	/**
	 * get_search
	 *
	 * 
	 *
	 * @param 
	 * @return 	int
	 */
	public function get_search()
	{
		return $this->grid_search;
	}
	//----------------------------------------------------------
	
	/**
	 * get_search_row
	 *
	 * Ищет столбец с нужными опциями для поиска
	 *
	 * @param $name  	
	 * @return 	string
	 */
	private function get_search_row($name)
	{
		foreach($this->get_columns() as $ms)
		{
			if($ms->get_options('searchname') == $name || $ms->get_options('index') == $name)
			{
				return $name;
			}
		}
		return FALSE;
	}
	//----------------------------------------------------------
	
	/**
	 * set_options
	 *
	 * Устанавливает опции сортировки, лимита и поиска
	 *
	 * @param $key, $value  	
	 * @return 	true/false
	 */
	public function set_options($key, $value)
	{
		$this->grid_options[$key] = $value;
		return true;
	}
	//----------------------------------------------------------
	
	/**
	 * get_options
	 *
	 * Возращает опцию или весь массив опций сортировки, поиска и лимита
	 *
	 * @param $key
	 * @return 	опция
	 */
	public function get_options($key = false)
	{
		if($key)
		{
			if(isset($this->grid_options[$key]))
			{
				return $this->grid_options[$key];
			}
			return false;	
		}
		return $this->grid_options;
	}
	//----------------------------------------------------------
	
	/**
	 * get_all_data_count
	 *
	 * Возращает количество count() массива данных, переменная генерирется в функцие setLimit
	 *
	 * @param 
	 * @return 	int
	 */
	public function get_all_data_count()
	{
		return $this->grid_pages['count'];
	}
	//----------------------------------------------------------
	
	/**
	 * render_pages
	 *
	 * Генерирует html постраничного вывода
	 *
	 * @return 	str
	 */
	public function render_pages()
	{
		$this->load->helper('agpages_helper');
		$pages_array = get_pages_array($this->grid_pages['count'], $this->grid_pages['active'], $this->grid_pages['limit']);
		return $this->load->view('libraries/grid/pages',array('grid_pages' => $pages_array), true);
	}
	//----------------------------------------------------------
	
	
	public function add_button($label, $href = '', array $options = array())
	{
		$this->grid_buttons[] = array('label' => $label, 'href' => $href, 'options' => $options);
	}
	
	public function init_fixed_buttons($t = TRUE)
	{
		if($t)
		{
			$this->init_fixed_buttons = 1;
		}
		else
		{
			$this->init_fixed_buttons = 0;
		}
	}
	
	public function	render_buttons()
	{
		if($this->grid_buttons)
		{
			$html = '';
			foreach($this->grid_buttons as $ms)
			{
				if(!isset($ms['options']))
				{
					$ms['options'] = array();
				}
				$html .= anchor($ms['href'], $ms['label'], $ms['options']);
			}
			return $html;
		}
		return false;
	}
}




class Nosql_grid_column
{
	private $title = '';
	private $options = array(
		'header'	=> 'Field',
		'type' 		=> 'text',
		'tdwidth'	=> false,
		'filter'    => false
	);
	
	function __construct($array, $label = '')
	{
		if(!is_array($array)) return FALSE;
		$this->title = $label;
		$this->options = $array;
	}
	public function get_title()
	{
		return $this->title;
	}	
	public function get_options($key)
	{
		if(isset($this->options[$key]))
		{
			return $this->options[$key];
		}
		return false;	
	}
	public function set_options($key, $value)
	{
		$this->options[$key] = $value;
	}	
	public function render_title()
	{
		return "<div>".$this->get_title()."</div>";
	}
	public function render_search($data)
	{
		if($this->get_options('filter'))
		{
			$type = $this->get_options('type');
			if($type == 'text')
			{
				return $this->render_search_text($data);
			}
			else if($type == 'number')
			{
				return $this->render_search_number($data);
			}
			else if($type == 'select')
			{
				return $this->render_search_select($data);
			}
			else if($type = 'date')
			{
				return $this->render_search_date($data);
			}
			else
			{
				return $this->getHtmlSearchText($data);
			}				
		}
		if($this->get_options('type') == 'action')
		{
			return anchor('#','Поиск',array('class'=>'search_button','rel'=>'search')).anchor('#','Очистить',array('class'=>'search_button','rel'=>'clear'));
		}
		return NULL;
	}
	private function render_search_text($data)
	{
		$val1 = NULL;
		if(isset($data[$this->get_search_field_name()])){$val1=$data[$this->get_search_field_name()];}
		return '<div class="input">'.form_input(array('name'=>$this->get_search_field_name(), 'value'=>$val1, 'autocomplete'=>'off')).'</div>';
	}
	private function render_search_number($data)
	{
		$val1 = NULL;
		$val2 = NULL;
		if(isset($data[$this->get_search_field_name()][0])){$val1=$data[$this->get_search_field_name()][0];}
		if(isset($data[$this->get_search_field_name()][1])){$val2=$data[$this->get_search_field_name()][1];}
		return '<div class="input_interval">От: '.form_input(array('name'=>$this->get_search_field_name().'-from', 'value'=>$val1, 'autocomplete'=>'off')).'</div><div class="input_interval">До: '.form_input(array('name'=>$this->get_search_field_name().'-to', 'value'=>$val2, 'autocomplete'=>'off')).'</div>';
	}
	private function render_search_select($data)
	{
		$val1 = NULL;
		if(isset($data[$this->get_search_field_name()])){$val1=$data[$this->get_search_field_name()];}
		return '<div class="input">'.form_dropdown($this->get_search_field_name(), $this->get_options('options'), $val1, 'autocomplete="off"').'</div>';
	}
	private function render_search_date($data)
	{
		$val1 = NULL;
		$val2 = NULL;
		if(isset($data[$this->get_search_field_name()][0])){$val1=$data[$this->get_search_field_name()][0];}
		if(isset($data[$this->get_search_field_name()][1])){$val2=$data[$this->get_search_field_name()][1];}
		return '<div class="input_interval">От: '.form_input(array('name'=>$this->get_search_field_name().'-date_from', 'value'=>$val1, 'autocomplete'=>'off', 'class'=>'datepicker', 'readonly'=>'readonly')).'</div><div class="input_interval">До: '.form_input(array('name'=>$this->get_search_field_name().'-date_to', 'value'=>$val2, 'autocomplete'=>'off', 'class'=>'datepicker', 'readonly'=>'readonly')).'</div>';
	}
	private function get_search_field_name()
	{
		if($name = $this->get_options('searchname'))
		{
			return $name;
		}
		else
		{
			return $this->get_options('index');
		}	
	}
	public function render_row_actions($values)
	{
		$html_actions = '';
		if($this->get_options('type') == 'checkbox')
		{
			$html_actions = $this->render_row_checkbox($values);
		}
		else
		{
			foreach($this->get_options('actions') as $ms)
			{
				if($ms['type'] == 'link')
				{
					$html_actions .= $this->render_row_link($ms, $values);
				}
			}
		}	
		return $html_actions;
	}
	private function render_row_checkbox($values)
	{
		$array = array(
			'name' => $this->get_options('name'),
			'value' => $values[$this->get_options('index')]
		);
		if(is_array($this->get_options('options')))
		{
			$array += $this->get_options('options');
		}
		$array += array(
			'id'=>'action_checkbox',
			'autocomplete'=>'off'
		);	
		return form_checkbox($array);
	}
	private function render_row_link($link_array, $values)
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