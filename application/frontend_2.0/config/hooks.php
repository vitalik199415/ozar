<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$hook['post_controller'] = array( 
		'class'    => 'Af_controller', 
		'function' => 'do_after_controller', 
		'filename' => 'af_controller.php', 
		'filepath' => 'hooks', 
		'params'   => array() 
	);
?>