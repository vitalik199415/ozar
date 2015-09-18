<?php
function setUrl($array)
{
	return set_url($array);
}
function set_url($array)
{
	if(!is_array($array))
	{
		$array = explode('/',$array);
	}
	$url = '';
	$CI = & get_instance();
	$segment_array = $CI->uri->segment_array();
	foreach($array as $key=>$ms)
	{
		if($ms == '*')
		{
			if(isset($segment_array[$key+1]))
			{
				$url .= '/'.$segment_array[$key+1];
			}
		}
		else
		{
			$url .= '/'.$ms;
		}
	}
	return base_url().substr($url,1);
}
?>