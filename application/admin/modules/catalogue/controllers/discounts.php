<?php
class Discounts extends AG_Controller {

    function __construct()
    {
        parent::__construct();
        $this->template->add_title("Продажи - ")->add_title("Скидки на покупку - ");
        $this->template->add_navigation("Продажи")->add_navigation("Скидки на покупки", set_url("*/*/"));
    }


    public function index()
    {
        
        $this->load->model("mdiscounts");

        if($select = $this->input->post('discounts_grid_select'))
        {
            if($checkbox = $this->input->post('discounts_grid_checkbox'))
            {
                $data_id = array();
                foreach($checkbox as $ms)
                {
                    $data_id[] = $ms;
                }
                switch ($select)
                {
                    case "on":
                        $this->mdiscounts->activate($data_id);
                        $this->messages->add_success_message('Активация выбраных позиций прошла успешно!');
                    break;
                    case "off":
                        $this->mdiscounts->activate($data_id, 0);
                        $this->messages->add_success_message('Деактивация выбраных позиций прошла успешно!');
                    break;
                    case "delete":
                        $this->mdiscounts->delete($data_id);
                        $this->messages->add_success_message('Удаление выбраных позиций прошло успешно!');
                    break;
                }
            }
        }
        $this->mdiscounts->render_discounts_grid();
    }

    public function add()
    {
        $this->template->add_title("Добавление");
        $this->template->add_navigation("Добавление");

        $this->load->model("mdiscounts");
        $this->mdiscounts->add();
    }

    public function edit()
    {
        $this->template->add_title("Редактирование");
        $this->template->add_navigation("Редактирование");

        $this->load->model("mdiscounts");
        $URI = $this->uri->uri_to_assoc(4);
        if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
        {
            if(!$this->mdiscounts->edit($ID))
            {
                $this->messages->add_error_message('Возникли ошибки при редактировании скидок');
                $this->_redirect(set_url('*/*/'));
            }
        }
        else
        {
            $this->messages->add_error_message('Параметр ID отсутсвует! Редактирование не возможно!');
            $this->_redirect(set_url('*/*/'));
        }
    }

    public function save()
    {
        if(isset($_POST))
        {
            $this->load->model('mdiscounts');
            $URI = $this->uri->uri_to_assoc(4);
            if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
            {
                if($this->mdiscounts->save($ID))
                {
                    $this->messages->add_success_message('Скидку успешно отредактировано');
                    $this->_redirect(set_url('*/*/'));
                }
                else
                {
                    $this->messages->add_error_message('Возникли ошибки при редактировании скидки!');
                    $this->_redirect(set_url('*/*/'));
                }
                if(isset($_GET['return']))
                {
                    $this->_redirect(set_url('*/*/edit/id/'.$ID));
                }
            }
            else
            {
                if($ID = $this->mdiscounts->save())
                {
                    $this->messages->add_success_message('Скидку успешно добавлено');
                    $this->_redirect(set_url('*/*/'));

                    if(isset($_GET['return']))
                    {
                        $this->_back_to_tab();
                        $this->_redirect(set_url('*/*/edit/id/'.$ID));
                    }
                }
                else
                {
                    $this->messages->add_error_message('Возникла ошибка при добавлении новой скидки!');
                    $this->_redirect(set_url('*/*/'));
                }
            }
        }
        else
        {
            $this->_redirect(set_url('*/*/'));
        }
    }

    public function delete()
    {
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
        {
            $this->load->model("mdiscounts");
            if($this->mdiscounts->delete($ID))
            {
                $this->messages->add_success_message('Скидка успешно удалена');
                $this->_redirect(set_url('*/*/'));
            }
            else
            {
                $this->messages->add_error_message('Скидка с ID='.$ID.' не существует, или произошла ошибка при удалении!');
                $this->_redirect(set_url('*/*/'));
            }
        }
        else
        {
            $this->messages->add_error_message('Параметр ID отсутствует! Процесс удаления не возможен!');
            $this->_redirect(set_url('*/*/'));
        }
    }
}

?>