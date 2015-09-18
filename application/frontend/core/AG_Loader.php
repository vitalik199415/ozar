<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class AG_Loader extends MX_Loader 
{

/** Load a module model **/
	public function model($model, $object_name = NULL, $connect = FALSE) 
	{
		if (is_array($model)) return $this->models($model);

		($_alias = $object_name) OR $_alias = basename($model);

		if (in_array($_alias, $this->_ci_models, TRUE)) 
			return CI::$APP->$_alias;
			
		/* check module */
		list($path, $_model) = Modules::find(strtolower($model), $this->_module, 'models/');
		
		//AG_ ***************************
		if($path == FALSE)
		{
			$model_e = explode('/', $model, 2);
			if(count($model_e)>1)
			{
				list($path, $_model) = Modules::find(strtolower($model_e[1]), $model_e[0], 'models/');
			}	
		}
		//AG_ ----------------------------
		
		if ($path == FALSE) {
			/* check application & packages */
			parent::model($model, $object_name);
			
		} else {
			
			class_exists('CI_Model', FALSE) OR load_class('Model', 'core');
			
			if ($connect !== FALSE AND ! class_exists('CI_DB', FALSE)) {
				if ($connect === TRUE) $connect = '';
				$this->database($connect, FALSE, TRUE);
			}
			
			Modules::load_file($_model, $path);
			
			$model = ucfirst($_model);
			if(class_exists(users_class_prefix.$model))
			{
				$model = users_class_prefix.$model;
			}
			CI::$APP->$_alias = new $model();
			
			$this->_ci_models[] = $_alias;
		}
		
		return CI::$APP->$_alias;
	}
	public function view($view, $vars = array(), $return = FALSE) 
	{
		list($path, $view) = Modules::find($view, $this->_module, 'views/');
		if($path == FALSE)
		{
			$view_e = explode('/', $view, 2);
			if(count($view_e)>1)
			{
				list($path, $view) = Modules::find(strtolower($view_e[1]), $view_e[0], 'views/');
			}	
		}
		if($path != FALSE)
		{
			$this->_ci_view_path = $path;
		}
		return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}

}