<?php
class Mreviews extends AG_Model
{
	const MID 		= 3;
	const RV		= 'm_reviews';	
	const ID_RV		= 'id_m_reviews';

	const U_MOD		= 'users_modules';
	const ID_U_MOD	= 'id_users_modules';

	const MENU 		= 'm_menu';
	const ID_MENU 	= 'id_m_menu';
	const U_M_M 	= 'users_menu_modules';
	const ID_U_M_M  = 'id_users_menu_modules';

	protected $segment = FALSE;
	
	function __construct()
	{
		$this->segment = $this->uri->segment(self :: MID);
		parent::__construct();
	}
	
	public function render_reviews_collection()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("reviews_grid", array(), FALSE);
		
		$this->grid->db	->select("A.`".self :: ID_RV."` AS ID, A.`active`, A.`review`, A.`answer`, A.`email`, A.`name`, A.`create_date`, A.`update_date`,
		 	A.`new_comment`, A.`is_answer` ")
			->from("`".self :: RV."` AS A")
			->where("A.`id_users_modules`", $this->segment)->order_by("A.`sort`", "DESC");
		
		$this->load->helper('reviews/reviews');
		helper_reviews_grid_build($this->grid);	
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('new_comment', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('is_answer', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();					
	}
		
	public function add()
	{
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$this->load->helper('reviews/reviews');
		helper_reviews_form_build($data);
	}

	public function edit($id)
	{
		$this->db->select("A.`".self::ID_RV."` AS ID, A.`review`,  A.`answer`, A.`name`, A.`email`,  A.`".self::ID_LANGS."`, A.`active`")
				  ->from("`".self::RV."` AS A")
				  ->where("A.`".self::ID_RV."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $this->db->get()->row_array();
		$this->load->model('langs/mlangs');
		$result['on_langs'] = $this->mlangs->get_active_languages();
		if(count($result) > 0)
		{
			$this->load->helper('reviews/reviews');
			helper_reviews_form_build($result, '/id/'.$id);

			$this->db->where("`".self::ID_RV."`", $id);
			$this->db->update("`".self::RV."`", array('new_comment' => '0'));
			return TRUE;
		}
		return FALSE;
	}
	
	public function save($id = FALSE)
	{
		$is_answer = 0;
		if($id)
		{
			if($POST = $this->input->post('main'))
			{
				$this->db->select("*")
					->from("`".self::RV."`")
					->where("`".self::ID_RV."`", $id)->limit(1);
				$review = $this->db->get()->row_array();
				if(count($review) == 0) return FALSE;
				if((isset($POST['answer'])) && (strlen($POST['answer'])>0)) $is_answer = 1;

				$this->load->model('reviews/mreviews_settings');
				$settings = $this->mreviews_settings->get_settings();
				if($is_answer = 1) $POST['admin_name'] = $settings['reviews_admin_name'];
				$this->db->trans_start();
				$this->sql_add_data($POST + array('is_answer' => $is_answer))->sql_update_date()->sql_using_user()->sql_save(self::RV, $id);

				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					if($review['mail_notification'] == 1 && $review['is_answer'] == 0 && $is_answer == 1)
					{
						$this->send_answer_email($POST + array(self::ID_LANGS => $review[self::ID_LANGS]));
					}
					return TRUE;
				}
				return FALSE;
			}
			return FALSE;
		}
		else
		{
			if($POST = $this->input->post('main'))
			{
				if((isset($POST['answer'])) && (strlen($POST['answer'])>0)) $is_answer = 1;

				$this->db->select("MAX(`sort`) AS MAX")->from("`".self::RV."`")->where("`".self::ID_U_MOD."`", $this->segment);
				$max_sort = $this->db->get()->row_array();
				$max_sort = $max_sort['MAX'];
				if(is_null($max_sort)) $max_sort = 1; else $max_sort++;

				$this->db->trans_start();
				$ID = $this->sql_add_data($POST + array('id_users_modules' => $this->segment, 'sort' => $max_sort, 'new_comment' => '0', 'is_answer' => $is_answer))->sql_update_date()->sql_using_user()->sql_save(self::RV);
				if($ID && $ID > 0)
				{
					$this->db->trans_complete();
					if($this->db->trans_status()) 
					{
						return $ID;
					}
					return false;
				}
				return false;
			}
			return false;
		}
	}
	
	public function delete($id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self::ID_RV, $id)->where(self::ID_USERS, $this->id_users);
			$this->db->delete(self::RV);
			return TRUE;
		}
		
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::RV."`")
				->where("`".self::ID_RV."`",$id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self::ID_RV, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self::RV))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
		
	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $active);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_save(self::RV, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}

	protected function send_answer_email($data)
	{
		$this->load->model('users/musers');
		$user = $this->musers->get_user();

		$this->load->model('langs/mlangs');
		$letter_lang = $this->mlangs->get_language($data[self::ID_LANGS]);

		$letter_data_array = array();
		$letter_data_array['name'] = $data['name'];
		$letter_data_array['message'] = $data['review'];
		$letter_data_array['answer'] = $data['answer'];
		$letter_data_array['site'] = $user['domain'];

		$this->db->select("A.`url`")
			->from("`".self::MENU."` AS A")
			->join("`".self::U_M_M."` AS B", "B.`".self::ID_MENU."` = A.`".self::ID_MENU."`", "left")
			->where("B.`id_users_modules`", $this->segment)
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->limit(1);

		$module_url = $this->db->get()->row_array();
		$letter_data_array['reviews_url'] = 'http://'.$letter_data_array['site'].'/'.$module_url['url'].'/lang-'.$letter_lang['code'];

		$this->load->model('reviews/mreviews_settings');
		$letter_html = $this->load->view('reviews/letters/'.$letter_lang['language'].'/reviews_answer', array('data' => $letter_data_array), TRUE);

		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		$send_email = $data['email'];

		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('no-reply@'.$user['domain'], $user['domain']);
		$this->email->to($send_email);
		$this->email->subject('Answer to your review.');
		$this->email->message($letter_html);
		$this->email->send();
		$this->email->clear();
	}
}
?>