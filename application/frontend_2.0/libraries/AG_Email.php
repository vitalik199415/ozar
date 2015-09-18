<?php
require_once "./system/libraries/Email.php";
class AG_Email extends CI_Email {

	public function sender($sender)
	{
		$this->_set_header('Sender', $this->clean_email($sender));
		$this->_set_header('X-Sender', $this->clean_email($sender));
	}

	public function return_path($from)
	{
		$this->_set_header('Return-Path', '<'.$from.'>');
	}

}