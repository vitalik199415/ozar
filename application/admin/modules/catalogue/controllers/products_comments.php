<?php
class Products_comments extends AG_Controller
{

	function __construct()
		{
			parent::__construct();
            $this->template->add_title('Каталог продукции | Отзывы к продуктам');
            $this->template->add_navigation('Каталог продукции')->add_navigation('Отзывы к продуктам', set_url('*/*'));
		}

    public function index()
    {
        $this->load->model('catalogue/mproducts_comments');

        if(isset($_POST['comment_grid_select']))
        {
            if(isset($_POST['comment_grid_checkbox']))
            {
                $data_ID = array();
                foreach($_POST['comment_grid_checkbox'] as $ms)
                {
                    $data_ID[] = $ms;
                }
                switch($_POST['comment_grid_select'])
                {
                    case "delete":
                        $this->mproducts_comments->delete($data_ID);
                        $this->messages->add_success_message('Удаление выбраных отзывов прошло успешно!');
                        break;
                    case "on":
                        $this->mproducts_comments->activate($data_ID);
                        $this->messages->add_success_message('Активация выбраных отзывов прошла успешно!');
                        break;
                    case "off":
                        $this->mproducts_comments->activate($data_ID, 0);
                        $this->messages->add_success_message('Деактивация выбраных отзывов прошла успешно!');
                        break;
                }
            }
        }
        $this->mproducts_comments->render_product_grid();
    }


    function view_product_comments()
    {
        $this->load->model('catalogue/mproducts_comments');

        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) &&  intval($URI['id'])>0)
        {
            $ID = intval($URI['id']);
            if(!$this->mproducts_comments->view_product_comments($ID))
            {
                $this->messages->add_error_message('Возникли ошибки просмотра отзывов к товарк!');
                $this->_redirect(set_url('*/*'));
            }
        }
        else
        {
            $this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
            $this->_redirect(set_url('*/*'));
        }

    }

    function product_comments()
    {
        $this->load->model('catalogue/mproducts_comments');

        $URI = $this->uri->uri_to_assoc(4);
        echo var_dump($URI);
        if(isset($URI['id']) && ($ID = intval($URI['id'])>0))
        {
            // $ID = intval($URI['id']);
            echo $ID;
            if(!$this->mproducts_comments->view_product_comments($ID))
            {
                $this->messages->add_error_message('Возникли ошибки просмотра отзывов к товарк!');
                $this->_redirect(set_url('*/*'));
            }
        }
        else
        {
            $this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
            $this->_redirect(set_url('*/*'));
        }
    }

    public function add()
    {
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) &&  intval($URI['id'])>0)
        {   $id = intval($URI['id']);
            $this->load->model('catalogue/mproducts_comments');
            $this->mproducts_comments->add($id);
        }
    }

    public function edit()
    {
        $this->load->model('catalogue/mproducts_comments');
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) && intval($URI['id'])>0 )
        {
            $ID = intval($URI['id']);
            if(isset($URI['id_c']) && intval($URI['id_c'])>0)
            {
                $ID_C = intval($URI['id_c']);
                if(!$this->mproducts_comments->edit($ID, $ID_C))
                {
                    $this->messages->add_error_message('Возникли ошибки генерации редактирования отзыва!');
                    $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                }
            }
            else
            {
                $this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
                $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
            }
        }
    }

    public function save()
    {
        if(isset($_POST))
        {
            $this->load->model('catalogue/mproducts_comments');
            $URI = $this->uri->uri_to_assoc(4);
            if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
            {
                if(isset($URI['id_c']) && ($ID_C = intval($URI['id_c']))>0)
                {
                    if($this->mproducts_comments->check_isset_comment($ID_C))
                    {
                        if($this->mproducts_comments->save($ID, $ID_C))
                        {
                            $this->messages->add_success_message('Отзыв успешно отредактирован!');
                            $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                        }
                        else
                        {
                            $this->messages->add_error_message('Возникли ошибки при редактировании отзыва!');
                            $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                        }
                        if(isset($_GET['return']))
                        {
                            $this->_redirect(set_url('*/*/edit/id/'.$ID.'/id_c/'.$ID_C));
                        }
                    }
                    else
                    {
                        $this->messages->add_error_message('Отзыв не существует!');
                        $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                    }
                }

            else
            {
                if($ID_C = $this->mproducts_comments->save($ID))
                {
                    $this->messages->add_success_message('Отзыв успешно добавлен!');
                    $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                }
                else
                {
                    $this->messages->add_error_message('Возникли ошибки при добавлении отзыва!');
                    $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                }
                if(isset($_GET['return']))
                {
                    $this->_redirect(set_url('*/*/edit/id/'.$ID.'/id_c/'.$ID_C));
                }
            }
        }

        }
        else
        {
            $this->_redirect(set_url('*/products'));
        }

    }

    public function answer()
    {
        $this->template->add_title(' Ответ');
        $this->template->add_navigation('Редактирование ответа');

        $this->load->model('catalogue/mproducts_comments');
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) && intval($URI['id'])>0)
        {
            $ID = intval($URI['id']);
            if(isset($URI['id_c']) && intval($URI['id_c'])>0)
                {
                    $ID_C = intval($URI['id_c']);
                    if(!$this->mproducts_comments->answer($ID, $ID_C))
                    {
                        $this->messages->add_error_message('Возникли ошибки генерации редактирования ответа!');
                        $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                    }
                }

            else
            {
                $this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
                $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
            }
        }
    }

	public function delete()
	{		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
            if(isset($URI['id_c']) && intval($URI['id_c'])>0)
            {
                $ID_C = intval($URI['id_c']);

			$this->load->model('catalogue/mproducts_comments');
                if($this->mproducts_comments->check_isset_comment($ID_C))
                {
                    if($this->mproducts_comments->delete($ID_C))
                    {
                        $this->messages->add_success_message('Отзыв успешно удален!');
                        $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                    }
                }
                else
                {
                    $this->messages->add_error_message('Отзыв с ID = '.$ID.' не существует, или произошла ошибка при удалении!');
                    $this->_redirect(set_url('*/*/view_product_comments/id/'.$ID));
                }
                }
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс удаления не возможен!');
			$this->_redirect(set_url('*/products'));
		}	
	}

    /*public function get_ajax_comments()
    {
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) && ($pr_id = intval($URI['id']))>0)
        {
            $this->load->model('catalogue/mproducts_comments');
            echo $this->mproducts_comments->product_comments_grid($pr_id);
        }
    }*/

	public function is_required_settings()
	{
		$this->load->helper('question_answer/question_answer_settings_helper');
		helper_q_a_settings_form_build(array());
		return true;
	}
	
	function settings()
	{
		$this->template->add_navigation('Настройки модуля');
		$this->load->model('question_answer/mquestion_answer_settings');
		$this->mquestion_answer_settings->edit();
	}
	
	public function save_settings()
	{
		if(isset($_POST))
		{	
			$this->load->model('mquestion_answer_settings');
			if($this->mquestion_answer_settings->save())
			{
				$this->messages->add_success_message('Настройки успешно сохранены!');
				$this->_redirect(set_url('*/*/*/settings'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при сохранении настроек!');
				$this->_redirect(set_url('*/*/*/settings'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/*/settings'));
			
		}
	}
	
	
}
?>