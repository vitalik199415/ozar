<?php

class Permissions_modules extends AG_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->template->add_title('Система - ')->add_title('Модули системы');
        $this->template->add_navigation('Система')->add_navigation('Модули системы', set_url('*/*/'));
    }
    
    public function index() {
        $this->load->model('mpermissions_modules');
        $this->mpermissions_modules->render_permissions_modules_grid();
    }
    
    public function add() {
        $this->load->model('mpermissions_modules');
        $this->template->add_navigation('Добавление модуля');
        $this->mpermissions_modules->add();
    }
    
    public function edit(){
        $this->template->add_title('Редактирование');
        $this->template->add_navigation('Редактирование купона');

        $this->load->model('mpermissions_modules');
        $URI = $this->uri->uri_to_assoc(4);

        if (isset($URI['id']) && ($ID = intval($URI['id'])) > 0) {
            if (!$this->mpermissions_modules->edit($ID)) {
                $this->messages->add_error_message('Возникли проблемы при редактировании модуля!');
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
            $this->load->model('mpermissions_modules');
            $URI = $this->uri->uri_to_assoc(4);
            if (isset($URI['id']) && ($ID = intval($URI['id'])) > 0) {
                if ($this->mpermissions_modules->save($ID)) {
                    $this->messages->add_success_message('Модуль успешно отредактировано!');
                    $this->_redirect(set_url('*/*'));
                } else {
                    $this->messages->add_error_message('Ошибка при редактировании модуля!');
                    $this->_redirect(set_url('*/*'));
                }
                if (isset($_GET['return'])) {
                    //$this->messages->add_success_message('Купон успешно отредактированo!');
                    $this->_redirect(set_url('*/*/edit/id/' . $ID));
                }
            } else {
                if ($ID = $this->mpermissions_modules->save()) {
                    $this->messages->add_success_message('Модуль успешно добавлено');
                    $this->_redirect(set_url('*/*'));

                    if (isset($_GET['return'])) {
                        $this->_redirect(set_url('*/*/edit/id/' . $ID));
                    }
                } else {
                    $this->messages->add_error_message('Ошибка при добавлении модуля!');
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
            $this->load->model("mpermissions_modules");
            if($this->mpermissions_modules->delete($ID))
            {
                $this->messages->add_success_message('Модуль успешно удалена');
                $this->_redirect(set_url('*/*/'));
            }
            else
            {
                $this->messages->add_error_message('Модуль с ID='.$ID.' не существует, или произошла ошибка при удалении!');
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

/*  End of file permissions_modules.php  */