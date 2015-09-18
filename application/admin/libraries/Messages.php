<?php
Class Messages
{
	private $error_messages = false;
	private $success_messages = false;

	private $render = false;
	public function __construct()
	{

	}

	public function add_error_message($message)
	{
		$this->error_messages[] = $message;
		$this->render = true;
		return $this;
	}

	public function add_success_message($message)
	{
		$this->success_messages[] = $message;
		$this->render = true;
		return $this;
	}

	public function add_ajax_error_message($message)
	{
		return $this->add_error_message($message);
	}

	public function add_ajax_success_message($message)
	{
		return $this->add_success_message($message);
	}

	public function set_messages($toSession = FALSE)
	{
		$messages = array();
		if($this->render && $toSession)
		{
			$messages = $this->_create_message_array();
			$ci = & get_instance();
			$ci->session->set_flashdata('messages', $messages);
		}
		else if($this->render)
		{
			$messages = $this->_create_message_array();
			$ci = & get_instance();
			$ci->template->add_header('messages', $messages, 'messages');
		}
		else
		{
			$ci = & get_instance();
			if($messages = $ci->session->flashdata('messages'))
			{
				$ci->template->add_header('messages', $messages, 'messages');
			}
		}
		return $this;
	}
	private function _create_message_array()
	{
		$messages = array();
		if($this->error_messages)
		{
			$messages['error_messages'] = $this->error_messages;
		}
		if($this->success_messages)
		{
			$messages['success_messages'] = $this->success_messages;
		}
		return $messages;
	}
}
?>