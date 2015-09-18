<?php
class Products_waitlist extends AG_Controller
{

	function __construct()
		{
			parent::__construct();
            $this->template->add_title('Каталог продукции | Заявки о наличии');
            $this->template->add_navigation('Каталог продукции')->add_navigation('Заявки о наличии', set_url('*/*'));
            $this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		}

    public function index()
    {
        $this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
        $this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
        $this->template->add_js('highslide.min', 'highslide');
        $this->template->add_css('highslide', 'highslide');
        $this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
        $this->load->model('catalogue/mproducts_waitlist');
        $this->mproducts_waitlist->render_product_grid();
        if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/products_grid_js', array());
    }


    function view_waitlist_customers()
    {
        $this->load->model('catalogue/mproducts_waitlist');

        $URI = $this->uri->uri_to_assoc(4);
        if(isset($URI['id']) &&  intval($URI['id'])>0)
        {
            $ID = intval($URI['id']);
            if(!$this->mproducts_waitlist->view_product_waitlist($ID))
            {
                $this->messages->add_error_message('Возникли ошибки просмотра запросов наличия продукта!');
                $this->_redirect(set_url('*/*'));
            }
        }
        else
        {
            $this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
            $this->_redirect(set_url('*/*'));
        }
    }
}
?>