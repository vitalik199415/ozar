<?php
class Af_controller
{	
	public function do_after_controller()
	{
		$CI = & get_instance();
		if($redirect = $CI->redirect)
		{
			$CI->messages->set_messages(TRUE);
			redirect($redirect);
		}
		else
		{
			$CI->messages->set_messages();
		}
		$CI->template->render();
	}
}
?>