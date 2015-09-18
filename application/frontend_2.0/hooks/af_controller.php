<?php
class Af_controller
{	
	public function do_after_controller()
	{
		$CI = & get_instance();
		if($redirect = $CI->redirect)
		{
			redirect($redirect);
		}
		$CI->template->render();
	}
}
?>