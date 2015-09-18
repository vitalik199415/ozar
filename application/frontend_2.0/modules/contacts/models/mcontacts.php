<?php
class Mcontacts extends AG_Model
{
	const CONTACTS = 'm_contacts';
	const ID_CONTACTS = 'id_m_contacts';
	const CONTACTS_DESC = 'm_contacts_description';
	
	protected $id_users_modules = FALSE;
	protected $settings = FALSE;
	
	protected $output_settings = array();

	function __construct()
	{
		parent::__construct();
	}
	
	public function _init($id_users_modules, $settings = FALSE)
	{
		$this->id_users_modules = $id_users_modules;
		$this->settings = $settings;
	}
	
	public function get_contacts_collection()
	{
		$query = $this->db
				->select("A.`".self::ID_CONTACTS."` AS ID, A.`email`, A.`show_form`, B.`name`, B.`text`")
				->from("`".self::CONTACTS."` AS A")
				->join(	"`".self::CONTACTS_DESC."` AS B",
						"B.`".self::ID_CONTACTS."` = A.`".self::ID_CONTACTS."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"inner")
				->where("A.`id_users_modules`", $this->id_users_modules)->where("A.`active`", 1)->order_by("A.`sort`");
				
		return array('contacts' => $query->get()->result_array());
	}
	
	public function write_admin($id, $POST = array())
	{
		$query = $this->db->select("`email`")
				->from("`".self::CONTACTS."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CONTACTS."`", $id)->limit(1);
		$result = $query->get()->row_array();		
		if(count($result)>0)
		{
			$data['site'] = $_SERVER['SERVER_NAME'];
			$data['email'] = $POST['email'];
			$data['name'] = $POST['name'];
			$data['phone'] = $POST['phone'];
			$data['message'] = $POST['text'];
			
			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;
			
			$data['admin_email'] = $result['email']; 
			
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->sender('noreply@'.$data['site']);
			$this->email->from('noreply@'.$data['site'], $data['name']);
			$this->email->to($data['admin_email']);
			$this->email->reply_to($data['email'], $data['name']);
			
			$this->email->subject($data['site'].' New message from '.$data['name'].'!');
			
			$message = "From : ".$data['name']." ".$data['email']." - ".$data['phone']."<br><br>".$data['message'];
			
			$this->email->message($message);
			$this->email->send();
			$this->email->clear();
			return TRUE;
		}
		return TRUE;
	}
}
?>