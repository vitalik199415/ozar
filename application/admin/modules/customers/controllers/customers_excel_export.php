<?php
class Customers_excel_export extends AG_Controller{

    function __construct()
    {
        parent::__construct();
        $this->template->add_title('Експорт покупателей');
        $this->template->add_navigation('Покупатели', set_url('customers/customers'))->add_navigation('Експорт покупателей');
    }

    public function index()
    {
        $this->load->model('customers/mcustomers_excel_export');
        $this->mcustomers_excel_export->render_customers_form();
    }

    public function export()
    {
        $this->load->model('customers/mcustomers_excel_export');
        $this->mcustomers_excel_export->export();

    }

}
