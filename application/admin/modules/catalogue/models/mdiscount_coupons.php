<?php

class Mdiscount_coupons extends AG_Model
{
    const D_COUP         = 'm_c_discount_coupons';
    const DC_PROD         = 'm_c_discount_coupons_products';
    const D_COUP_CUST    = 'm_c_discount_coupons_customers';
    const ID_D_COUP      = 'id_m_c_discount_coupons';
    const ID_D_COUP_CUST = 'id_m_c_discount_coupons_customers';
    const DC_MAIL        = 'm_c_discount_coupons_mail';
    const ID_DC_MAIL     = 'id_m_c_discount_coupons_mail';
    const D_CUST         = 'm_u_customers';
    const ID_CUST        = 'id_m_u_customers';
    const TYPE           = 'm_u_types';
    const ID_TYPES       = 'id_m_u_types';
    const PROD           = 'm_c_products';
    const ID_PROD        = 'id_m_c_products';

    function __construct()
    {
        parent::__construct();
    }

    public function render_discount_coupons_grid()
    {
        $this->load->library('grid');
        $this->grid->_init_grid('discount_coupons_grid', array());

        $this->grid->db
                ->select("`".self::ID_D_COUP."` AS ID, `name`, `description`, `order_sum`, `discount_type`, `discount_sum`, `discount_percent`, `consider_promotional_items`, `is_start`")
                ->from("`".self::D_COUP."`")
                ->where('id_users ='.$this->id_users);

        $this->load->helper('discount_coupons');
        helper_discount_coupons_grid_build($this->grid);
        $this->grid->create_grid_data();
        $this->grid->update_grid_data('discount_type', array('0' => 'Сумма', '1' => 'Процент'));
        $this->grid->update_grid_data('consider_promotional_items', array('0' => 'Нет', '1' => 'Да'));
        $this->grid->update_grid_data('is_start', array('0' => 'Нет', '1' => 'Да'));
        $this->grid->render_grid();
    }

    public function add()
    {
        $data = array();
        $data['perm_lvl'] = FALSE;

        /*if(($p_lvl = $this->mpermissions->get_access_lvl('discounts')) < 1) return FALSE;
        if($p_lvl == 2) $data['perm_lvl'] = TRUE;*/

        $this->load->model('catalogue/mcurrency');
        $data['data_default_currency'] = $this->mcurrency->get_default_currency_name();

        $data['data_customers'] = $this->get_customers();
        $data['products'] = $this->get_products();

        if (count($data['data_customers']) == 0) return FALSE;

        $this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();

        $data['langs'] = array();
        $lang_id = $this->db->select('DISTINCT `id_langs` AS lang_id')
            ->from('`m_u_customers`')
            ->where('`' . self::ID_USERS . '`', $this->id_users)->get()->result_array();
        foreach ($lang_id as $lang) {
            if (isset($langs_temp[$lang['lang_id']])) {
                $data['langs'][$lang['lang_id']] = $langs_temp[$lang['lang_id']];
            }
        }

        $this->load->model('customers/mcustomers_types');
        $data['customers_groups'] = $this->mcustomers_types->get_customers_types();

        $this->load->helper('discount_coupons');
        helper_discount_coupons_form_build($data);
        return TRUE;

    }

    public function edit($id)
    {
        $data = array();
        $data['perm_lvl'] = FALSE;

        if(($p_lvl = $this->mpermissions->get_access_lvl('discounts')) < 1) return FALSE;

        if($p_lvl == 1) {
            if(!$this->check_edit_permission($id)) return FALSE;
        }
        if($p_lvl == 2) $data['perm_lvl'] = TRUE;

        $main = $this->db->select('*')
            ->from('`' . self::D_COUP . '` AS A')
            ->where('A.`' . self::ID_USERS . '`', $this->id_users)
            ->where('A.`' . self::ID_D_COUP . '`', $id)->limit(1)
            ->get()->row_array();

        if(count($main)>0)
        {
            if($main['is_start'] == 0){
                if($main['type_customers'] < 2) $customers = $this->get_customers($id);
            }

            if($main['type_customers'] == 1)
            {
                $selected_group = $this->db->select('`id_m_u_types` AS type')
                                           ->from('`'.self::D_COUP.'_group_temp`')
                                           ->where('`'.self::ID_D_COUP.'`', $id)->get()->result_array();
                foreach($selected_group as $group) {
                    $data['customers_group'][$group['type']] = $group['type'];
                }

            }
            else if($main['type_customers'] == 2)
            {
                $data['selected_customers'] = $this->get_selected_customers($id, $main['is_start']);           // построение таблицы выбраных покупателей
                $customers = $this->get_customers($id);
            }

            $this->load->model('customers/mcustomers_types');
            $this->load->model('mcurrency');

            $data['customers_groups'] = $this->mcustomers_types->get_customers_types();     // построение списка груп покупателей
            $data['data_default_currency'] = $this->mcurrency->get_default_currency_name(); // активные языки для письма
            $data['customers']['type'] = $main['type_customers'];                           // выделение выбранных груп покупателей
            if($main['is_start'] == 0)
            {
                $data['data_customers'] = $customers;                                       // получение списка покупателей, за исключением выбранных
                $data['products'] = $this->get_products($id);
            }

            $data['products_temp'] = $this->get_selected_products($id, $main['is_start']);
            $data['main'] = $main;                                                          // основная информация о купоне


            $this->load->model('mlangs');
            $langs_temp = $this->mlangs->get_active_languages();
            $data['langs'] = array();
            $lang_id = $this->db->select('DISTINCT `id_langs` AS lang_id')
                ->from('`m_u_customers`')
                ->where('`'.self::ID_USERS.'`', $this->id_users)->get()->result_array();
            foreach($lang_id as $lang)
            {
                if(isset($langs_temp[$lang['lang_id']]))
                {
                    $data['langs'][$lang['lang_id']] = $langs_temp[$lang['lang_id']];
                }
            }

            $email = $this->db->select('*')
                              ->from('`'.self::D_COUP.'_mail`')
                              ->where('`'.self::ID_D_COUP.'`', $id)->get()->result_array();

            foreach($email as $mail)
            {
                $data['email'][$mail['id_langs']]['title'] = $mail['title'];
                $data['email'][$mail['id_langs']]['text'] = $mail['text'];
            }

            $this->load->helper('discount_coupons');
            helper_discount_coupons_form_build($data, '/id/'.$id);
            return TRUE;
        }
        return FALSE;
    }

    public function save($id = FALSE)
    {
        if($this->input->post('main'))
        {
            if($id)
            {
                $main = $this->input->post('main');
                $customers_type = $this->input->post('customers');
                $customers_group = $this->input->post('customers_group');
                $customers = $this->input->post('customer');
                $customer_temp = $this->input->post('customer_temp');
                $products = $this->input->post('products');
                $products_temp = $this->input->post('products_temp');
                $email_data = $this->input->post('email');
                $main['type_customers'] = $customers_type['type'];
                $main['admin_id'] = $this->id_admin;

                if ($main['discount_type'] == 0) {
                    $main['discount_percent'] = NULL;
                } else {
                    $main['discount_sum'] = NULL;
                }

                if (!$this->save_validate()) return FALSE;

                $this->db->trans_start();

                $this->db->where('`'.self::ID_D_COUP.'`', $id)->update('`'.self::D_COUP.'`', $main);

                $lang_id = $this->db->select('DISTINCT `id_langs` AS lang_id')
                    ->from('`' . self::D_CUST . '`')
                    ->where('`' . self::ID_USERS . '`', $this->id_users)->get()->result_array();

                foreach ($lang_id as $lang) {
                    if (isset($email_data[$lang['lang_id']])) {
                        $email = array(
                            'title' => $email_data[$lang['lang_id']]['title'],
                            'text' => $email_data[$lang['lang_id']]['text']
                        );
                        $this->db->where('`'.self::ID_D_COUP.'`', $id)->where('`id_langs`', $lang['lang_id'])->update('`'.self::D_COUP.'_mail`', $email);
                    }
                }

                $this->save_products($products, $products_temp, $id);

                switch ($customers_type['type']) {
                    case 1:
                        ///
                        if($customers_group)
                        {
                            $this->save_temp_groups($customers_group, $id);
                        }
                        break;
                    case 2:
                        ///
                        if($customer_temp || $customers)
                        {
                            $this->save_temp_customers($customers, $customer_temp, $id);
                        }
                        break;
                }

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return TRUE;
            }
            else
            {
                $main = $this->input->post('main');
                $customers_type = $this->input->post('customers');
                $customers_group = $this->input->post('customers_group');
                $customers = $this->input->post('customer');
                $email_data = $this->input->post('email');
                $products = $this->input->post('products');
                $products_temp = $this->input->post('products_temp');
                $main['type_customers'] = $customers_type['type'];
                $main['admin_id'] = $_SESSION['id_admin'];

                if ($main['discount_type'] == 0) {
                    $main['discount_percent'] = NULL;
                } else {
                    $main['discount_sum'] = NULL;
                }

                if (!$this->save_validate()) return FALSE;

                $this->db->trans_start();
                $last_id = $this->sql_add_data($main)->sql_using_user()->sql_save(self::D_COUP);

                $lang_id = $this->db->select('DISTINCT `id_langs` AS lang_id')
                    ->from('`' . self::D_CUST . '`')
                    ->where('`' . self::ID_USERS . '`', $this->id_users)->get()->result_array();

                foreach ($lang_id as $lang) {
                    if (isset($email_data[$lang['lang_id']])) {
                        $email = array(
                            self::ID_D_COUP => $last_id,
                            'title' => $email_data[$lang['lang_id']]['title'],
                            'text' => $email_data[$lang['lang_id']]['text'],
                            'id_langs' => $lang['lang_id']
                        );
                        $this->sql_add_data($email)->sql_save(self::DC_MAIL);
                    }
                }

                $this->save_products($products, $products_temp, $last_id);

                switch ($customers_type['type']) {
                    case 1:
                        ///
                        foreach($customers_group as $group)
                        {
                            $selected_group = array(
                                'id_m_u_types'  => $group,
                                self::ID_D_COUP => $last_id
                            );
                            $this->db->insert('m_c_discount_coupons_group_temp', $selected_group);
                        }
                        break;
                    case 2:
                        ///
                        foreach($customers as $customer)
                        {
                            $selected_customer = array(
                                self::ID_CUST   => $customer,
                                self::ID_D_COUP => $last_id
                            );
                            $this->db->insert('m_c_discount_coupons_customers_temp', $selected_customer);
                        }
                        break;
                }

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return $last_id;
            }
        }
    }

    public function save_temp_customers($POST = FALSE, $POST_TEMP = FALSE, $ID)
    {
        $result = $this->db->where('`'.self::ID_D_COUP.'`', $ID)->get('`'.self::D_COUP_CUST.'_temp`')->result_array();

        foreach($result as $cust_id)
        {
            $data[$cust_id[self::ID_CUST]] = $cust_id[self::ID_CUST];
        }

        if($POST) {
            foreach ($POST as $cust) {
                if (isset($data[$cust])) {
                    unset($data[$cust]);
                } else {
                    $data_cust = array(self::ID_CUST => $cust, self::ID_D_COUP => $ID);
                    $this->db->insert('`' . self::D_COUP_CUST . '_temp`', $data_cust);
                }
            }
        }

        $del_data = FALSE;
        if($POST_TEMP)
        {
            foreach($POST_TEMP as $cust_id)
            {
                $del_data[] = $cust_id;
            }
        }

        if($del_data)
        {
            $this->db->where_in(self::ID_CUST, $del_data);
            $this->db->delete(self::D_COUP_CUST.'_temp');
        }
    }

    public function save_temp_groups($POST, $ID)
    {
        $result = $this->db->where('`'.self::ID_D_COUP.'`', $ID)->get('`'.self::D_COUP.'_group_temp`')->result_array();

        foreach($result as $group_id)
        {
            $data[$group_id['id_m_u_types']] = $group_id['id_m_u_types'];
        }

        foreach($POST as $cust)
        {
            if(isset($data[$cust]))
            {
                unset($data[$cust]);
            }
            else
            {
                $group = array('id_m_u_types' => $cust, self::ID_D_COUP => $ID);
                $this->db->insert('`'.self::D_COUP.'_group_temp`', $group);
            }
        }

        $del_data = FALSE;
        if(isset($data))
        {
            foreach($data as $cust_id)
            {
                $del_data[] = $cust_id;
            }
        }
        if($del_data)
        {
            $this->db->where('`'.self::ID_D_COUP.'`', $ID)->where_in('`id_m_u_types`', $del_data);
            $this->db->delete(self::D_COUP.'_group_temp');
        }
    }

    public function save_products($POST = FALSE, $POST_TEMP = FALSE, $ID)
    {
        $result = $this->db->where('`'.self::ID_D_COUP.'`', $ID)->get('`'.self::D_COUP.'_products`')->result_array();

        foreach($result as $prod_id)
        {
            $data[$prod_id[self::ID_PROD]] = $prod_id[self::ID_PROD];
        }

        if($POST) {
            foreach ($POST as $prod) {
                if (isset($data[$prod])) {
                    unset($data[$prod]);
                } else {
                    $data_prod = array(self::ID_PROD => $prod, self::ID_D_COUP => $ID);
                    $this->db->insert('`' . self::D_COUP . '_products`', $data_prod);
                }
            }
        }

        $del_data = FALSE;
        if($POST_TEMP)
        {
            foreach($POST_TEMP as $prod_id)
            {
                $del_data[] = $prod_id;
            }
        }

        if($del_data)
        {
            $this->db->where_in(self::ID_PROD, $del_data);
            $this->db->delete(self::D_COUP.'_products');
        }
    }

    public function activate($ID)
    {
        $type = $this->db->select('`type_customers`')
                         ->from('`'.self::D_COUP.'`')
                         ->where('`'.self::ID_D_COUP.'`', $ID)->get()->row_array();

        $active = array('is_start' => 1);
        $this->db->where('`'.self::ID_D_COUP.'`', $ID)->update('`'.self::D_COUP.'`', $active);

        switch ($type['type_customers']) {
            case 0:
                ///
                if (!$this->coupons_for_all_customers($ID)) return FALSE;
                break;
            case 1:
                ///
                $groups = $this->db->select('`id_m_u_types`')
                                   ->from('`'.self::D_COUP.'_group_temp`')
                                   ->where('`'.self::ID_D_COUP.'`', $ID)->get()->result_array();
                $data = array();
                foreach($groups as $group)
                {
                    $data[] = $group['id_m_u_types'];
                }
                if (!$this->coupons_for_selected_group($data, $ID)) return FALSE;
                break;
            case 2:
                ///
                $customers = $this->db->select('`'.self::ID_CUST.'`')
                                   ->from('`'.self::D_COUP_CUST.'_temp`')
                                   ->where('`'.self::ID_D_COUP.'`', $ID)->get()->result_array();
                $data = array();
                foreach($customers as $customer)
                {
                    $data[] = $customer['id_m_u_customers'];
                }
                if (!$this->coupons_for_selected_customers($data, $ID)) return FALSE;
                break;
        }
        $this->db->where('`'.self::ID_D_COUP.'`', $ID)->delete('`'.self::D_COUP.'_group_temp`');
        $this->db->where('`'.self::ID_D_COUP.'`', $ID)->delete('`'.self::D_COUP_CUST.'_temp`');
        return TRUE;
    }

    public function coupons_for_all_customers($coupon_id)
    {
        $this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();
        $langs = array();

        foreach($langs_temp as $key => $ms)
        {
            $langs[] = $key;
        }

        $customers = $this->db
            ->select('A.`'.self::ID_CUST.'` AS ID, A.`email`, A.`name`, A.`id_langs`')
            ->from('`'.self::D_CUST.'` AS A')
            ->where('`'.self::ID_USERS.'`', $this->id_users)
            ->where_in('`id_langs`', $langs)->get()->result_array();

        $this->generate_coupons($customers, $coupon_id);

        return TRUE;
    }

    public function coupons_for_selected_group($customers_group, $coupon_id)
    {
        $this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();
        $langs = array();

        foreach($langs_temp as $key => $ms)
        {
            $langs[] = $key;
        }

        $customers = $this->db
            ->select('DISTINCT A.`'.self::ID_CUST.'` AS ID, A.`email`, A.`name`, A.`id_langs`')
            ->from('`'.self::D_CUST.'` AS A')
            ->join('`m_u_customers_types` AS B',
                    'A.`'.self::ID_CUST.'`=B.`'.self::ID_CUST.'`')
            ->where('A.`'.self::ID_USERS.'`', $this->id_users)
            ->where_in('A.`id_langs`', $langs)
            ->where_in('B.`id_m_u_types`', $customers_group)->get()->result_array();

        $this->generate_coupons($customers, $coupon_id);

        return TRUE;
    }

    public function coupons_for_selected_customers($customers_array, $coupon_id)
    {
        $this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();
        $langs = array();

        foreach($langs_temp as $key => $ms)
        {
            $langs[] = $key;
        }

        $customers = $this->db
            ->select('`'.self::ID_CUST.'` AS ID, `email`, `name`, `id_langs`')
            ->from('`'.self::D_CUST.'`')
            ->where('`'.self::ID_USERS.'`', $this->id_users)
            ->where_in('`id_langs`', $langs)->where_in('`'.self::ID_CUST.'`', $customers_array)
            ->get()->result_array();

        $this->generate_coupons($customers, $coupon_id);

        return TRUE;
    }

    public function generate_coupons($data, $last_id)
    {
        $number = $this->db->select("MAX(`A`.`number`) AS coupon_number")
            ->from("`".self::D_COUP_CUST."` AS A")
            ->join("`".self::D_COUP."` AS B",
                "B.`".self::ID_USERS."` = ".$this->id_users." && B.`".self::ID_D_COUP."` = A.`".self::ID_D_COUP."`",
                "INNER")
            ->limit(1)->get()->row_array();

        $max = 1;
        if(count($number)>0)
        {
            $max = intval($number['coupon_number']) + 1;
        }

        foreach ($data as $cust)
        {
            $customer_number = $cust['ID'];
            $customer_number_str = ($customer_number).str_repeat("0", 8-strlen($customer_number));

            $max_str = str_repeat("0", 8-strlen($max)).($max);

            $coupons_customer_data = array(
                'full_number'   => $customer_number_str.$max_str,
                'number'        => $max_str,
                self::ID_CUST   => $cust['ID'],
                self::ID_D_COUP => $last_id
            );
            $this->sql_add_data($coupons_customer_data)->sql_save(self::D_COUP_CUST);

            $coupons_number[] = $customer_number_str.$max_str;
            $max += 1;
        }

        $this->send_mail($last_id);
    }

    public function get_customers($id = FALSE)
    {
        $this->load->library('grid');
        $this->grid->_init_grid('discount_coupons_customers_grid', array('url' => set_url('*/*/get_ajax_customers_sort')));
        $this->grid->init_fixed_buttons(FALSE);

        $this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();
        $langs = array();

        foreach($langs_temp as $key => $ms)
        {
            $langs[] = $key;
        }

        if($id) {
            $selected_customers = $this->db->select('`' . self::ID_CUST . '`')
                ->from('`' . self::D_COUP_CUST . '_temp`')
                ->where('`' . self::ID_D_COUP . '`', $id)->get()->result_array();
            $selected_arr = array();
            foreach ($selected_customers as $cust) {
                $selected_arr[] = $cust[self::ID_CUST];
            }

            if (count($selected_arr) > 0) $this->grid->db->where_not_in('`' . self::ID_CUST . '`', $selected_arr);
        }

        $this->grid->db
            ->select('`'.self::ID_CUST.'` AS ID, `email`, `name`')
            ->from('`'.self::D_CUST.'`')
            ->where('`'.self::ID_USERS.'`', $this->id_users)
            ->where_in('`id_langs`', $langs);

        $this->load->helper('discount_coupons');
        select_customers_grid_build($this->grid);

        $this->grid->create_grid_data();

        return $this->grid->render_grid(TRUE);
    }

    public function get_selected_customers($coupon_id, $is_start)
    {
        $this->load->library('nosql_grid');
        $this->nosql_grid->_init_grid('discount_coupons_selected_customers_grid', array('url' => set_url('*/*/get_ajax_selected_customers')));

        $this->load->helper('discount_coupons');
        if($is_start == 0) {
            $result = $this->db
                ->select('B.`'.self::ID_CUST.'` AS ID, A.`name`, A.`email`')
                ->from('`m_u_customers` AS A')
                ->join('`' . self::D_COUP_CUST . '_temp` AS B', 'A.`id_m_u_customers`=B.`id_m_u_customers`', 'INNER')
                ->where('A.`' . self::ID_USERS . '`', $this->id_users)->where('B.`' . self::ID_D_COUP . '`', $coupon_id)->get()->result_array();
            selected_customers_temp_grid_build($this->nosql_grid);
        }
        else
        {
            $result = $this->db
                ->select('B.`'.self::ID_CUST.'` AS ID, A.`name`, A.`email`, IF(B.`is_used`=0, "Нет", "Да") AS is_used')
                ->from('`m_u_customers` AS A')
                ->join('`' . self::D_COUP_CUST . '` AS B', 'A.`id_m_u_customers`=B.`id_m_u_customers`', 'INNER')
                ->where('A.`' . self::ID_USERS . '`', $this->id_users)->where('B.`' . self::ID_D_COUP . '`', $coupon_id)->get()->result_array();
            selected_customers_grid_build($this->nosql_grid);
        }

        $this->nosql_grid->set_grid_data($result);
        return $this->nosql_grid->render_grid(TRUE);
    }

    public function get_products($id = FALSE)
    {
        $this->load->library('grid');
        $this->grid->_init_grid('discount_coupons_products_grid', array('url' => set_url('*/*/get_ajax_products_sort')));
        $this->grid->init_fixed_buttons(FALSE);

        /*$this->load->model('mlangs');
        $langs_temp = $this->mlangs->get_active_languages();
        $langs = array();

        foreach($langs_temp as $key => $ms)
        {
            $langs[] = $key;
        }*/

        if($id) {
            $selected_products = $this->db->select('`'.self::ID_PROD.'`')
                ->from('`' . self::D_COUP . '_products`')
                ->where('`' . self::ID_D_COUP . '`', $id)->get()->result_array();
            $selected_arr = array();
            foreach ($selected_products as $product) {
                $selected_arr[] = $product[self::ID_PROD];
            }

            if (count($selected_arr) > 0) $this->grid->db->where_not_in('A.`' . self::ID_PROD . '`', $selected_arr);
        }

        $this->grid->db
            ->select('A.`'.self::ID_PROD.'` AS ID, B.`name`, A.`sku`, IF(A.`status`=0, "Нет", "Да") AS status, IF(A.`in_stock`=0, "Нет", "Да") AS in_stock')
            ->from('`'.self::PROD.'` AS A')
            ->join('`'.self::PROD.'_description` AS B', 'A.`'.self::ID_PROD.'`=B.`'.self::ID_PROD.'`', 'INNER')
            ->where('`'.self::ID_USERS.'`', $this->id_users)->where('B.`id_langs`', 1);

        $this->load->helper('discount_coupons');
        products_grid_build($this->grid);

        $this->grid->create_grid_data();

        return $this->grid->render_grid(TRUE);
    }

    public function get_selected_products($coupon_id, $is_start)
    {
        $this->load->library('nosql_grid');
        $this->nosql_grid->_init_grid('discount_coupons_selected_products_grid', array('url' => set_url('*/*/get_ajax_selected_products')));

        $this->load->helper('discount_coupons');
        if($is_start == 0) {
            $result = $this->db
                ->select('A.`'.self::ID_PROD.'` AS ID, C.`status`, C.`in_stock`, B.`name`, C.`sku`')
                ->from('`'.self::D_COUP.'_products` AS A')
                ->join('`' . self::PROD . '_description` AS B', 'A.`'.self::ID_PROD.'`=B.`'.self::ID_PROD.'`', 'INNER')
                ->join('`' . self::PROD . '` AS C', 'A.`'.self::ID_PROD.'`=C.`'.self::ID_PROD.'`', 'INNER')
                ->where('A.`' . self::ID_D_COUP . '`', $coupon_id)->where('B.`id_langs`', 1)->get()->result_array();
            selected_products_temp_grid_build($this->nosql_grid);
        }
        else
        {
            $result = $this->db
                ->select('A.`'.self::ID_PROD.'` AS ID, C.`status`, C.`in_stock`, B.`name`, C.`sku`')
                ->from('`'.self::D_COUP.'_products` AS A')
                ->join('`' . self::PROD . '_description` AS B', 'A.`'.self::ID_PROD.'`=B.`'.self::ID_PROD.'`', 'INNER')
                ->join('`' . self::PROD . '` AS C', 'A.`'.self::ID_PROD.'`=C.`'.self::ID_PROD.'`', 'INNER')
                ->where('A.`' . self::ID_D_COUP . '`', $coupon_id)->where('B.`id_langs`', 1)->get()->result_array();
            selected_products_grid_build($this->nosql_grid);
        }

        $this->nosql_grid->set_grid_data($result);
        return $this->nosql_grid->render_grid(TRUE);
    }

    public function send_mail($id)
    {
        $customers = $this->db->select('A.`full_number`, C.`email`, C.`name`, C.`id_langs`, L.`language`')
                              ->from('`'.self::D_COUP_CUST.'` AS A')
                              ->join('`'.self::D_CUST.'` AS C', 'A.`'.self::ID_CUST.'` = C.`'.self::ID_CUST.'`', 'INNER')
                              ->join('`langs` AS L', 'C.`id_langs`=L.`id_langs`', 'INNER')
                              ->where('A.`'.self::ID_D_COUP.'`', $id)->get()->result_array();

        $coupon_data = $this->db->select('*')
            ->from('`'.self::D_COUP.'`')
            ->where('`'.self::ID_D_COUP.'`', $id)->get()->row_array();

        $messages = $this->db->select('*')
                            ->from('`'.self::D_COUP.'_mail`')
                            ->where('`'.self::ID_D_COUP.'`', $id)->get()->result_array();
        $mess = array();
        foreach($messages as $message)
        {
            $mess[$message['id_langs']]['title'] = $message['title'];
            $mess[$message['id_langs']]['text'] = $message['text'];
        }

        $user = $this->musers->get_user();

        $config['protocol'] = 'mail';
        $config['wordwrap'] = FALSE;
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['priority'] = 1;

        $this->load->library('email');

        $letter_data['date_to'] = $coupon_data['date_to'];
        $letter_data['domain'] = $user['domain'];
        foreach($customers as $ms)
        {

            $letter_data['name'] = $ms['name'];
            $letter_data['number'] = $ms['full_number'];
            $letter_data['message'] = $mess[$ms['id_langs']]['text'];

            $letter_html = $this->load->view('catalogue/discount_coupons/letters/'.$ms['language'].'/coupons_mailing', $letter_data, TRUE);

            $this->email->initialize($config);
            $this->email->from('no_reply@'.$user['domain'], $user['domain']);
            $this->email->to($ms['email']);
            $this->email->subject($mess[$ms['id_langs']]['title']);
            $this->email->message($letter_html);
            $this->email->send();
            $this->email->clear();
        }

    }

    public function save_validate()
    {
        $this->load->library("form_validation");

        $this->form_validation->add_callback_function_class('check_date_balance', 'mdiscount_coupons');
        $this->form_validation->add_callback_function_class('is_float_order_sum', 'mdiscount_coupons');
        $this->form_validation->add_callback_function_class('is_float_discount_sum', 'mdiscount_coupons');

        $this->form_validation->set_message('check_date_balance', 'Дата окончания должна быть больше даты начала');
        $this->form_validation->set_message('is_float_order_sum', 'Не верно указаный формат в поле "%s". Укажите все суммы в следующем формате "0000.00"');
        $this->form_validation->set_message('is_float_discount_sum', 'Не верно указаный формат в поле "%s". Укажите все суммы в следующем формате "0000.00"');

        $this->form_validation->set_rules('main[order_sum]', 'Сумма заказа', 'required|callback_is_float_order_sum');
        $this->form_validation->set_rules('main[discount_type]', 'Сумма скидки', 'callback_is_float_discount_sum');



        $this->form_validation->set_rules('main[date_from]', 'Дата начала', 'trim|callback_check_date_balance');

        if(!$this->form_validation->run())
        {
            $this->messages->add_error_message(validation_errors());
            return FALSE;
        }

        return TRUE;
    }

    public function check_date_balance($date_from)
    {
        $date_to = $this->input->post('main');

        if($date_to['date_to'] > $date_from)
        {
            return TRUE;
        }
        return FALSE;
    }

    public function is_float_order_sum($sum)
    {
        if($sum == '')
        {
            return TRUE;
        }
        else
        {
            if(is_float(floatval($sum)))
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function is_float_discount_sum($type)
    {
        if($type == 1)
        {
            return TRUE;
        }
        else
        {
            $disc_sum = $this->input->post('main[discount_sum]');
            if(is_float(floatval($disc_sum)))
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function check_edit_permission($id){
        $data = $this->db->select('COUNT(*) as count')
            ->from('`' . self::D_COUP . '` AS A')
            ->where('A.`' . self::ID_D_COUP . '`', $id)->limit(1)
            ->where('A.`admin_id`', $this->id_admin)
            ->where('A.`active`', 0)
            ->get()->row_array();

        if($data['count'] > 0) return TRUE;
        return FALSE;
    }

    public function check_coupon_discount($cart_products, $order_id) {
        $order_coupon_code = $this->db->select('discount_coupon_number')
            ->from('m_orders')
            ->where('id_m_orders', $order_id)->limit(1)->get()->row_array();
        if(count($order_coupon_code) > 0 && ($code = $order_coupon_code['discount_coupon_number']) != NULL) {
            $coupon_products = $this->db->select('*')
                ->from('`'.self::DC_PROD.'` as D_PROD')
                ->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_PROD.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
                ->where('D_CUST.`full_number`', $code)->where('D_CUST.`is_used`', 1)
                ->get()->result_array();

            $coupon_data = $this->db->select('*')
                ->from('`'.self::D_COUP.'` as D_COUP')
                ->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_COUP.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
                ->where('D_CUST.`full_number`', $code)->where('D_CUST.`is_used`', 1)
                ->limit(1)->get()->row_array();

            $total = 0;
            foreach($cart_products as $prod) {
                $total += $prod['total'];
            }

            if($total > $coupon_data['order_sum']) {
                if(count($coupon_products) > 0 && $coupon_data['discount_type'] == 1) {
                    // TODO перещет стоимости товаров
                    //$currency = $this->mcurrency->get_current_currency();
                    $total_discount = 0;
                    foreach($cart_products as $key => $ms) {
                        foreach($coupon_products as $prod) {
                            $discount = 0;
                            if($ms['PR_ID'] == $prod[self::ID_PROD]) {
                                $discount = $ms['total'] * $coupon_data['discount_percent'] / 100;
                                $cart_products[$key]['discount'] = $discount;
                                $cart_products[$key]['total_string'] = number_format($cart_products[$key]['total'], 2, ',', ' ')."(<span class='label'>".$discount."</span>)";
                                $cart_products[$key]['total'] = $ms['total'];
                            }
                            $total_discount += $discount;
                        }
                    }

                    return array('result' => 2, 'discount' => $total_discount, 'products' => $cart_products);
                } else {
                    if($coupon_data['discount_type'] == 0 ) {
                        return array('result' => 1, 'discount' => $coupon_data['discount_sum'], 'products' => $cart_products);
                    } else {
                        $discount_summ = ($total * $coupon_data['discount_percent']) / 100;
                        return array('result' => 1, 'discount' => $discount_summ, 'products' => $cart_products);
                    }
                }
            }
        } else {
            return array('result' => 0, 'discount' => 0, 'products' => FALSE);
        }

    }

}

