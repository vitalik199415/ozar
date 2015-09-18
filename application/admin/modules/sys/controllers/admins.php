<?php

class Admins extends AG_Controller {
    function __construct(){
        parent::__construct();
        $this->template->add_title('Система - ')->add_title('Администраторы системы');
        $this->template->add_navigation('Система')->add_navigation('Администраторы системы', set_url('*/*/'));
    }

    public function index() {
        $this->load->model('madmins');
        $this->madmins->render_admins_grid();
    }

    public function add() {
        $this->load->model('madmins');
        $this->template->add_navigation('Добавление администратора');

        if(!$this->madmins->add()) {
            $this->messages->add_error_message('У Вас нет прав для этой операции!');
            $this->_redirect(set_url('*/*'));
        }
    }

    public function edit(){
        $this->template->add_title('Редактирование');
        $this->template->add_navigation('Редактирование');

        $this->load->model('madmins');
        $URI = $this->uri->uri_to_assoc(4);

        if (isset($URI['id']) && ($ID = intval($URI['id'])) > 0) {
            if (!$this->madmins->edit($ID)) {
                $this->messages->add_error_message('Возникли проблемы при редактировании или у Вас не достаточно прав!');
                $this->_redirect(set_url('*/*'));
            }
        } else {
            $this->messages->add_error_message('Параметр ID не установлено, редактирование невозможно!');
            $this->_redirect(set_url('*/*'));
        }
    }

    public function save()
    {
        if (isset($_POST)) {
            $this->load->model('madmins');
            $URI = $this->uri->uri_to_assoc(4);
            if (isset($URI['id']) && ($ID = intval($URI['id'])) > 0) {
                if ($this->madmins->save($ID)) {
                    $this->messages->add_success_message('Информация успешно отредактирована!');
                    $this->_redirect(set_url('*/*'));
                } else {
                    $this->messages->add_error_message('Ошибка при редактировании!');
                    $this->_redirect(set_url('*/*'));
                }
                if (isset($_GET['return'])) {
                    //$this->messages->add_success_message('Информация успешно отредактирована!');
                    $this->_redirect(set_url('*/*/edit/id/' . $ID));
                }
            } else {
                if ($ID = $this->madmins->save()) {
                    $this->messages->add_success_message('Администратора успешно добавлено');
                    $this->_redirect(set_url('*/*'));

                    if (isset($_GET['return'])) {
                        $this->_redirect(set_url('*/*/edit/id/' . $ID));
                    }
                } else {
                    $this->messages->add_error_message('Ошибка при добавлении администратора!');
                    $this->_redirect(set_url('*/*'));
                }
            }
        } else {
            $this->_redirect(set_url('*/*'));
        }
    }

    public function delete()
    {
        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
        {
            $this->load->model("madmins");
            if($this->madmins->delete($ID))
            {
                $this->messages->add_success_message('Администратор успешно удален');
                $this->_redirect(set_url('*/*/'));
            }
            else
            {
                $this->messages->add_error_message('Администратор с ID='.$ID.' не существует, или произошла ошибка при удалении!');
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

/*  End of file admins.php  */