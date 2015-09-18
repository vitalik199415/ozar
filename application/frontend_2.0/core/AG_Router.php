<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH."third_party/MX/Router.php";

class AG_Router extends MX_Router {
    const MAIN = 'main';
    const ROUTE = 'route';
    const NAME = 'name';
    const URL = 'url';

    /**
     * Search in Array
     *
     * @param array $arr
     * @param string $key
     * @param string $value
     * @return int index of element
     */
    function _search_by_key(&$arr, $key, $value)
    {
        $ret = false;
        foreach($arr as $k => $v)
        {
            if(!is_int($k)) continue;

            if(is_array($v))
            {
                if($v[$key] == $value)
                {
                    $ret = $k;
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     *  Parse Routes
     *
     * This function matches any routes that may exist in
     * the config/routes.php file against the URI to
     * determine if the class/method need to be remapped.
     *
     * @access      private
     * @return      void
     */
    function _parse_routes()
    {
            // Do we even have any custom routing to deal with?
            // There is a default scaffolding trigger, so we'll look just for 1
            if (count($this->routes) == 1)
            {
                    $this->_set_request($this->uri->segments);
                    return;
            }

            // Turn the segment array into a URI string
            $uri = implode('/', $this->uri->segments);

            // Is there a literal match?  If so we're done
        if (isset($this->routes[$uri]))
            {
                    $this->_set_request(explode('/', $this->routes[$uri]));
                    return;
            }

        //Art
        $i = $this->_search_by_key($this->routes, self::MAIN, $uri);
        if ($i !== FALSE)
        {
                $this->_set_request(explode('/', $this->routes[$i][self::ROUTE]));
                return;
        }

        // Loop through the route array looking for wild-cards
        foreach ($this->routes as $key => $val)
        {
                if(is_int($key))
                {
                    $key = $val[self::MAIN];
                    $val = $val[self::ROUTE];
                }
                // Convert wild-cards to RegEx
                $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

                // Does the RegEx match?
                if (preg_match('#^'.$key.'$#', $uri))
                {
                    // Do we have a back-reference?
                    if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                    {
                        $val = preg_replace('#^'.$key.'$#', $val, $uri);
                    }
                    $this->_set_request(explode('/', $val));
                    return;
                }
        }

        // If we got this far it means we didn't encounter a
        // matching route so we'll set the site default route
        $this->_set_request($this->uri->segments);
    }


    /**
     * Build the url using params in config by name of rule
     *
     * @param string Name of rule
     * @param array associative array of params
     * @return string url
     */
    function build_url($name, $array = array())
    {
        $i = 0;
        $i = $this->_search_by_key($this->routes, self::NAME, $name);
        
        if($i === FALSE)
        {
            log_message('ERROR', 'Try to create undefined url with name: '.$name);
            return $this->config->site_url();
        }

        $rule = $this->routes[$i];
        
        if(is_null($array)){
            return $rule[self::URL];
        }

        //v1.5 ������������ ��������
        if(preg_match_all("#\[([\w_]+)\]#", $rule[self::URL], $mas)){
            $l = count($mas[1]);
            
            for($i = 0; $i < $l; $i++){
                $j = $this->_search_by_key($this->routes, self::NAME, $mas[1][$i]);
                if($j !== FALSE){
                    $parent_rule = $this->routes[$j];
                    $rule[self::URL] = str_replace('[' . $mas[1][$i] . ']', $parent_rule[self::URL], $rule[self::URL]);
                }
            }
        }
        
		//AG EDIT
		$rule_array = explode('/', $rule[self::URL]);
		foreach($rule_array as $key => $ms)
		{
			if($ms == '') unset($rule_array[$key]);
			if(($pos = strpos($ms, ':')) !== FALSE)
			{
				$u_key = substr($ms, $pos+1);
				if(isset($array[$u_key]) && $array[$u_key] !== FALSE)
				{
					$rule_array[$key] = str_replace(':'.$u_key , $array[$u_key] , $ms);
				}
				else
				{
					unset($rule_array[$key]);
				}
			}
		}
		$rule[self::URL] = '';
		if(count($rule_array)>0)
		{
			foreach($rule_array as $ms)
			{
				$rule[self::URL] .= $ms.'/';
			}
			$rule[self::URL] = substr($rule[self::URL], 0, -1);
		}
		/*
        foreach($array as $k => $v)
        {
            $rule[self::URL] = str_replace(':'.$k . '/', $v . '/', $rule[self::URL]);
        }
        */
        //����������� ���������� �������, ����� �������� ���������� �� ������������� ������.
        /*if(preg_match("@:\w+$@", $rule[self::URL])){
            foreach($array as $k => $v){
                $rule[self::URL] = str_replace(':'.$k, $v, $rule[self::URL]);
            }
        }*/
        return $this->config->site_url($rule[self::URL]);
    }
	function buildUrl($name, $array = null)
	{
		return $this->build_url($name, $array);
	}
}