<?php
class Mproducts_comments extends AG_Model
{

	const PR_COMM 				= 'm_c_products_comments';
	const ID_PR_COMM 			= 'id_m_c_products_comments';
    const PR 			        = 'm_c_products';
    const ID_PR 		        = 'id_m_c_products';
    const PR_DESC 		= 'm_c_products_description';
    const ID_PR_DESC 	= 'id_m_c_products_description';

	function __construct()
	{
		parent::__construct();
	}

    public function render_product_grid()
    {
        $this->load->library('grid');
        $this->grid->_init_grid('products_grid');

        if($extra_search = $this->grid->get_options('search'))
        {
            if(isset($extra_search['new_comment']))
            {
                $temp_extra_search = $extra_search;
                unset($temp_extra_search['new_comment']);
                $this->grid->set_options('search', $temp_extra_search);
                $update_select_types = $extra_search['new_comment'];
            }
        }

        $this->grid->db
            ->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`,
                        (SELECT COUNT(*) FROM `".self::PR_COMM."` AS K WHERE K.`".self::ID_PR."` = A.`".self::ID_PR."` AND K.`new_comment` = 1) AS new_comment" )
            ->from("`".self::PR."` AS A")
            ->join(	"`".self::PR_DESC."` AS B",
                "B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
                "LEFT")
            //->where("A.`".self::ID_PR."` IN (SELECT DISTINCT `".self::ID_PR."` FROM `".self::PR_COMM."` WHERE `".self::ID_USERS."` = '".$this->id_users."' && `new_comment` = 1)", NULL, FALSE)

        ->where("A.`".self::ID_USERS."`", $this->id_users);
        //->where("A.`".self::ID_PR."` IN (SELECT DISTINCT `".self::ID_PR."` FROM `".self::PR_COMM."` WHERE `".self::ID_USERS."` = '".$this->id_users."' && `new_comment` = 1)", NULL, FALSE);
        if(isset($update_select_types))
        {
            $update_select_types = intval($update_select_types);
            if($update_select_types > 0)
            {
                $this->grid->db->where("A.`".self::ID_PR."` IN (SELECT DISTINCT `".self::ID_PR."` FROM `".self::PR_COMM."` WHERE `".self::ID_USERS."` = '".$this->id_users."' && `new_comment` = 1)", NULL, FALSE);

            }

        }

        $this->load->helper('catalogue/products_comments_helper');
        helper_products_grid_build($this->grid);

        $this->grid->create_grid_data();
        $this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
        $this->grid->update_grid_data('new_comment',array('0'=>'Нет новых'));

        if(isset($update_select_types))
        {
            $extra_search = $this->grid->get_options('search');
            $extra_search['new_comment'] = $update_select_types;
            $this->grid->set_search_manualy('new_comment', $update_select_types);
            $this->grid->set_options('search', $extra_search);
        }
        $this->grid->render_grid();

    }

	public function product_comments_grid($id_pr)
	{
        $this->load->library('grid');
        $this->grid->_init_grid('products_comments_grid_'.$id_pr, array('limit' => 50));
		$this->grid->db->select("A.`".self::ID_PR_COMM."` AS ID, A.`sort` AS sort, A.`is_answer`, A.`active`, A.`create_date`, A.`update_date`, A.`message`, A.`answer`, A.`name`, A.`email`")
					->from("`".self::PR_COMM."` AS A")
                    ->where("A.`".self::ID_PR."`", $id_pr)
					->where("A.`".self::ID_USERS."`", $this->id_users)
					->order_by('sort', 'DESC');

		$this->load->helper('catalogue/products_comments_helper');

        comments_grid_build($this->grid, $id_pr);

        $this->grid->create_grid_data();
        $this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
        $this->grid->update_grid_data('is_answer', array('0' => 'Нет', '1' => 'Да'));
        return $this->grid->render_grid();

	}

    public function view_product_comments($id_pr)
    {
        $id_pr = intval($id_pr);
        $this->load->model('catalogue/mproducts');
        if(!$this->mproducts->check_isset_pr($id_pr)) return FALSE;

        $query = $this->db->select("A.`sku`")
            ->from("`".self::PR."` AS A")
            ->where("A.`".self::ID_PR."`", $id_pr)
            ->where("A.`".self::ID_USERS."`", $this->id_users)
            ->limit(1);
        $result = $query->get()->row_array();

        $this->template->add_navigation("Отзывы к ".$result['sku']);
        $this->product_comments_grid($id_pr);

        $this->db->where("`".self::ID_PR."`", $id_pr);
        $this->db->update("`".self::PR_COMM."`", array('new_comment' => '0'));
        return TRUE;
    }
	
	public function add($id_pr)
	{
        $this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id_pr)) return FALSE;
		$this->load->helper('catalogue/products_comments_helper');
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		$query = $this->db->select("A.`sku`")
            ->from("`".self::PR."` AS A")
            ->where("A.`".self::ID_PR."`", $id_pr)
            ->where("A.`".self::ID_USERS."`", $this->id_users)
            ->limit(1);
        $result = $query->get()->row_array();

        $this->template->add_navigation('Отзывы к '.$result['sku'], set_url('*/*/view_product_comments/id/'.$id_pr));
		$this->template->add_title(' | Отзывы к '.$result['sku']);
		
		$this->template->add_title(' | Добавить отзыв');
        $this->template->add_navigation('Добавить отзыв');
	
		helper_add_edit_comment_form_build($data, $id_pr);
	}

	public function edit($id_pr, $id_c)
    {
        $result = $this->getEditQuery($id_c);
        $result = $result->get()->result_array();

        $data = array();
        if(count($result) > 0)
        {
            foreach($result as $ms)
            {
                $data['main']['active'] = $ms['active'];
                $data['main']['email'] = $ms['email'];
                $data['main']['name'] = $ms['name'];
                $data['main']['message'] = $ms['message'];
                $data['main']['id_langs'] = $ms['id_langs'];
                $data['answer']['message'] = $ms['answer'];

            }
            $this->load->model('langs/mlangs');
			
			$query = $this->db->select("A.`sku`")
				->from("`".self::PR."` AS A")
				->where("A.`".self::ID_PR."`", $id_pr)
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->limit(1);
			$result = $query->get()->row_array();

			$this->template->add_navigation('Отзывы к '.$result['sku'], set_url('*/*/view_product_comments/id/'.$id_pr));
			$this->template->add_title(' | Отзывы к '.$result['sku']);
			
			$this->template->add_title(' | Редактировать отзыв');
			$this->template->add_navigation('Редактировать отзыв');

            $data['on_langs'] = $this->mlangs->get_active_languages();
            $this->load->helper('catalogue/products_comments_helper');

            helper_add_edit_comment_form_build($data, $id_pr, '/id_c/'.$id_c);
            return TRUE;
        }
        return FALSE;
    }
	
	private function getEditQuery($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "A.`".self::ID_LANGS."`, A.`".self :: ID_PR_COMM."` AS DID";
		}
		else
		{
			$select = "A.`".self :: ID_PR_COMM."` AS ID, A.`active`, A.`email`, A.`mail_notification`, A.`name`, A.`".self::ID_LANGS."`, A.`message`, A.`answer`";
		}
		$result = $this->db	->select($select)
							->from("`".self :: PR_COMM."` AS A")
							->where("A.`".self :: ID_PR_COMM."`",$id)->where("A.`id_users`", $this->id_users);
		return $result;					
	}
	
    public function save($id, $id_c = FALSE)
    {
        if($id_c)
        {
            if($data_main = $this->input->post('main'))
            {
                $query = $this->db->select("*")
						->from("`".self::PR_COMM."`")
						->where("`".self::ID_PR_COMM."`", $id_c)->limit(1);
                $comment = $query->get()->row_array();
				
				$this->load->model('catalogue/mproducts_settings');
				$settings = $this->mproducts_settings->get_settings();
				
				if($data_answer = $this->input->post('answer'))
                {
                    if(strip_tags($data_answer['message']) != '')
                    {
                        $data_main['answer'] = $data_answer['message'];
                        $data_main['is_answer'] = 1;
						$data_main['admin_name'] = $settings['reviews_admin_name'];
                    }
					else
					{
						$data_main['answer'] = NULL;
						$data_main['is_answer'] = 0;
						$data_main['admin_name'] = NULL;
					}
                }
				
				$data_main[self::ID_LANGS] = $comment[self::ID_LANGS];
				$data_main[self::ID_PR] = $comment[self::ID_PR];
				
                $this->db->trans_start();
                $this->sql_add_data($data_main)->sql_update_date()->sql_using_user()->sql_save(self::PR_COMM, $id_c);

                $this->db->trans_complete();
                if($this->db->trans_status())
                {
					if($settings['reviews_publication_immediately'] == 0 && $comment['active'] == 0 && $data_main['active'] == 1)
					{
						$this->send_active_email($data_main);
					}
					if($comment['is_answer'] == 0 && $data_main['is_answer'] == 1)
					{
						$this->send_answer_email($data_main);
					}
					return $id_c;
                }
                else
                {
                    return FALSE;
                }
            }
            return FALSE;
        }
        else
        {
            if($data_main = $this->input->post('main'))
            {	
                $this->load->model('catalogue/mproducts_settings');
				$settings = $this->mproducts_settings->get_settings();
				
				$this->db->trans_start();
                $sql_data = array(
                    'id_langs' => $data_main['id_langs'],
                    'name' => $data_main['name'],
                    'message' => $data_main['message'],
                    'email' => $data_main['email'],
                    self::ID_PR => $id
				);

                if($data_answer = $this->input->post('answer'))
                {
                    if(strip_tags($data_answer['message']) != '')
                    {
                        $sql_data['answer'] = $data_answer['message'];
                        $sql_data['is_answer'] = 1;
						$sql_data['admin_name'] = $settings['reviews_admin_name'];
                    }
					else
					{
						$sql_data['answer'] = NULL;
						$sql_data['is_answer'] = 0;
						$sql_data['admin_name'] = NULL;
					}
                }

                $ID_C = $this->sql_add_data($sql_data)->sql_update_date()->sql_using_user()->sql_save(self::PR_COMM);
                if($ID_C && $ID_C > 0)
                {
                    $this->sql_add_data(array('sort' => $ID_C))->sql_save(self::PR_COMM, $ID_C);

                    $this->db->trans_complete();
                    if($this->db->trans_status())
                    {
						return $ID_C;
                    }
                    else
                    {
                        return FALSE;
                    }
                }
                return FALSE;
            }
        }
    }
	
	protected function send_active_email($data)
	{
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		
		$this->load->model('langs/mlangs');
		$letter_lang = $this->mlangs->get_language($data[self::ID_LANGS]);
		
		$query = $this->db->select("A.`sku`, A.`url_key`, B.`name`")
				->from("`".self::PR."` AS A")
				->join("`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`id_langs` = ".$letter_lang[self::ID_LANGS],
						"LEFT")
				->where("A.`".self::ID_PR."`", $data[self::ID_PR])->limit(1);
		$product = $query->get()->row_array();
		$product_url = $data[self::ID_PR];
		if($product['url_key'] != '')
		{
			$product_url = trim($product['url_key']);
		}
		$product['url'] = 'http://'.$user['domain'].'/product-'.$product_url.'/lang-'.$letter_lang['code'];
		
		$letter_data_array = array();
		$letter_data_array['name'] = $data['name'];
		$letter_data_array['message'] = $data['message'];
		$letter_data_array['answer'] = $data['answer'];
		$letter_data_array['site'] = $user['domain'];
		
		$letter_data_array['product_url'] = $product['url'];
		$letter_data_array['product_name'] = $product['name'];
		$letter_data_array['product_sku'] = $product['sku'];
		
		$letter_html = $this->load->view('catalogue/products/letters/'.$letter_lang['language'].'/comment_active', array('data' => $letter_data_array), TRUE);
		
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
		$this->email->subject('Activation of your comment.');
		$this->email->message($letter_html);
		$this->email->send();
		$this->email->clear();
	}
	
	protected function send_answer_email($data)
	{
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		
		$this->load->model('langs/mlangs');
		$letter_lang = $this->mlangs->get_language($data[self::ID_LANGS]);
		
		$query = $this->db->select("A.`sku`, A.`url_key`, B.`name`")
				->from("`".self::PR."` AS A")
				->join("`".self::PR_DESC."` AS B",
						"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`id_langs` = ".$letter_lang[self::ID_LANGS],
						"LEFT")
				->where("A.`".self::ID_PR."`", $data[self::ID_PR])->limit(1);
		$product = $query->get()->row_array();
		$product_url = $data[self::ID_PR];
		if($product['url_key'] != '')
		{
			$product_url = trim($product['url_key']);
		}
		$product['url'] = 'http://'.$user['domain'].'/product-'.$product_url.'/lang-'.$letter_lang['code'];
		
		$letter_data_array = array();
		$letter_data_array['name'] = $data['name'];
		$letter_data_array['message'] = $data['message'];
		$letter_data_array['answer'] = $data['answer'];
		$letter_data_array['site'] = $user['domain'];
		
		$letter_data_array['product_url'] = $product['url'];
		$letter_data_array['product_name'] = $product['name'];
		$letter_data_array['product_sku'] = $product['sku'];
		
		$letter_html = $this->load->view('catalogue/products/letters/'.$letter_lang['language'].'/comment_answer', array('data' => $letter_data_array), TRUE);
		
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
		$this->email->subject('Answer to your comment.');
		$this->email->message($letter_html);
		$this->email->send();
		$this->email->clear();
	}

    public function answer($id, $id_c)
	{	
			$result = $this->getEditQuery($id_c);
			$result = $result->get()->result_array();
			$data = array();
			if(count($result) > 0)
			{
				foreach($result as $ms)
				{
					$data['main']['active'] = $ms['active'];
					$data['main']['mail_notification'] = $ms['mail_notification'];
					$data['main']['email'] = $ms['email'];
					$data['main']['name'] = $ms['name'];
					$data['main']['message'] = $ms['message'];
					//$data['main'][$ms['id_langs']] = $ms;

					$data['main']['id_langs'] = $ms['id_langs'];
				}
												
				$this->load->helper('catalogue/products_comments_helper');
				
				helper_answer_form_build($data, $id, '/id_c/'.$id_c);
				return TRUE;
			}
			return FALSE;

	}

	public function delete($id)
	{	
		if(is_array($id))
		{	
			$this->db->where_in(self::ID_PR_COMM, $id)->where(self::ID_USERS, $this->id_users)->delete("`".self::PR_COMM."`");

			return TRUE;
		}

		$this->db->where(self :: ID_PR_COMM, $id)->where(self::ID_USERS, $this->id_users);
		if($this->db->delete(self::PR_COMM) )
		{	
			return TRUE;
		}
		return FALSE;
	}

    public function check_isset_comment($id)
    {
        $query = $this->db	->select("COUNT(*) AS COUNT")
            ->from("`".self::PR_COMM."`")
            ->where("`".self::ID_PR_COMM."`", $id)
            ->where("`".self::ID_USERS."`", $this->id_users);
        $result = $query->get()->row_array();
        if($result['COUNT'] == 1)
        {
            return TRUE;
        }
        return FALSE;
    }
	



    public function getId($id, $parent = false)
	{	
		if($parent)
		{
			$select = "A.`id_parent` AS id ";
			$where = "A.`".self::ID_PR_COMM."` = ".$id;
		}
		else
		{
			$select = "A.`".self::ID_PR_COMM."` AS id ";
			$where = "A.`id_parent` = ".$id;
		}
		$query = $this->db->select($select)
				->from("`".self::PR_COMM."` AS A")
				->where($where)
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$res = $query->get()->result_array();
		foreach($res as $ms)
			{
				$ID = $ms['id'];
			}
		if(isset($ID) && intval($ID > 0))
		{
			return $ID;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function isAnswer($id, $q = FALSE)
	{
		$query = $this->db->select("A.`is_answer` ")
						->from("`".self::PR_COMM."` AS A")
						->where("A.`".self::ID_PR_COMM."`", $id)
						->where("A.`".self::ID_USERS."`", $this->id_users);
		$res = $query->get()->result_array();
		foreach($res as $ms)
				{
					$answer = intval($ms['is_answer']);
				}
		if($q)
		{
			if($answer == 2)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		if($answer != 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	

	
	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $active);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_save(self :: PR_COMM, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}

}
?>