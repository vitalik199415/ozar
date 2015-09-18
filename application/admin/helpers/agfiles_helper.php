<?php
function create_dir($path, $SM = 0)
{
	$dir = $path;
	$arr = explode('/', $dir);
	$str_dir = '';
	foreach($arr as $key => $ms)
	{
		if($ms != '')
		{
			if($key >= $SM)
			{
				if(!is_dir($str_dir.$ms))
				{
					mkdir($str_dir.$ms, 0777);
				}
			}
			$str_dir .= $ms.'/';
		}
	}
}
	
function remove_dir($path)
{
	if(file_exists($path) && is_dir($path))
	{
		$dirHandle = opendir($path);
		while (false !== ($file = readdir($dirHandle))) 
		{
			if ($file!='.' && $file!='..')
			{
				$tmpPath=$path.'/'.$file;
				
				if (is_dir($tmpPath))
	  			{
					remove_dir($tmpPath);
			   	} 
	  			else 
	  			{ 
	  				if(file_exists($tmpPath))
					{
	  					unlink($tmpPath);
					}
	  			}
			}
		}
		closedir($dirHandle);
		if(file_exists($path))
		{
			rmdir($path);
		}
	}
}	
?>