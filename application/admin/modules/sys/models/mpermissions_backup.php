<?php

class Mpermissions extends AG_Model {

    public $permissions = array(
        0 => array(
            'discounts' => 1,
            'products'  => 1
        ),
        5 => array(
            'discount_coupons' => 4
        )
    );

    public $menu = array(
        0 => array(
            1   => array('Меню|Модули', '#', 4),
            2   => array('Каталог продукции', '#', 4),
            3   => array('Продажи', '#', 4),
            4   => array('Покупатели', '#', 4),
            5   => array('Настройка сайта', '#', 4),
            6   => array('Склад', '#', 4),
            56  => array('Система', '#', 4),
            7   => array('Выход', 'login/logout', 4)
        ),
        1 => array(
            8   => array('Настройки главной', 'home/home', 4),
            9   => array('Меню сайта', 'menu/menu', 4),
            10  => array('Модули сайта', 'site_modules/site_modules', 4)
        ),
        2 => array(
            11  => array('Категории каталога', '#', 4),
            12  => array('Продукты каталога', '#', 4),
            13  => array('Свойства продуктов', '#', 4),
            14  => array('Атрибуты продукции', '#', 4),
            15  => array('Скидки на покупку', '#', 4)
        ),
        3 => array(
            16  => array('Заказы', 'sales/orders', 4),
            17  => array('Инвойсы', 'sales/invoices', 4),
            18  => array('Отправки', 'sales/shippings', 4),
            19  => array('Возвраты', 'sales/credit_memo', 4),
            20  => array('Методы оплаты', 'sales/payment_methods', 4),
            21  => array('Методы доставки', 'sales/shipping_methods', 4),
            22  => array('Настройки', 'sales/sales_settings', 4)
        ),
        4 => array(
            23  => array('Покупатели', 'customers/customers', 4),
            24  => array('Группы покупателей', 'customers/customers_types', 4),
            25  => array('Настройки', 'customers/customers_settings', 4)
        ),
        5 => array(
            26  => array('Настройки сайта', 'site_settings/site_settings', 4),
            27  => array('Настройки языков', 'langs/langs', 4),
            28  => array('Дополнительные блоки', '#', 4)
        ),
        6 => array(
            29  => array('Склады', 'warehouse/warehouses', 4),
            30  => array('Продукты', 'warehouse/warehouses_products', 4),
            31  => array('Продажи', '#', 4),
            32  => array('Переносы', 'warehouse/warehouses_transfers', 4),
            33  => array('Логи, отчеты', 'warehouse/warehouses_logs', 4),
            34  => array('Настройки склада', 'warehouse/wh_settings', 4)
        ),
        11 => array(
            35  => array('Категории каталога', 'catalogue/categories', 4),
            36  => array('Продукты в категории', 'catalogue/categories_products', 4)
        ),
        12 => array(
            37  => array('Продукты каталога', 'catalogue/products', 4),
            38  => array('Продукты дополнительно', 'catalogue/products/additionally_grid', 4),
            39  => array('Сопутствующие продукты', 'catalogue/products_related', 4),
            40  => array('Похожие продукты', 'catalogue/products_similar', 4),
            41  => array('Отзывы к продуктам', 'catalogue/products_comments', 4),
            42  => array('Настройки продуктов', 'catalogue/products_settings', 4),
            43  => array('Настройки валют', 'catalogue/currency', 4),
            60  => array('Заявки о наличии продуктов', 'catalogue/products_waitlist', 4)
        ),
        13 => array(
            44  => array('Свойства продуктов', 'catalogue/products_properties', 4),
            45  => array('Группы свойств продуктов', 'catalogue/products_types', 4)
        ),
        14 => array(
            46  => array('Атрибуты', 'catalogue/products_attributes', 4),
            47  => array('Опции атрибутов','catalogue/products_attributes_options',4)
        ),
        15 => array(
            48  => array('Скидки на покупку', 'catalogue/discounts', 4),
            49  => array('Купоны на скидку', 'catalogue/discount_coupons', 4)
        ),
        28 => array(
            50  => array('Дополнительное header', 'block_additionally/block_additionally_header', 4),
            51  => array('Счетчики footer', 'block_additionally/block_additionally_footer', 4)
        ),
        31 => array(
            52  => array('Продажи', 'warehouse/warehouses_sales', 4),
            53  => array('Инвойсы', 'warehouse/warehouses_invoices', 4),
            54  => array('Отправки', 'warehouse/warehouses_shippings', 4),
            55  => array('Возвраты', 'warehouse/warehouses_credit_memo', 4)
        ),
        56 => array(
            57  => array('Модули системы', 'sys/permissions_modules', 4),
            59  => array('Пользовательские модули', 'sys/users_modules', 4),
            58  => array('Администраторы системы', 'sys/admins', 4)
        )
    );


    function build_menu($menu_arr, $user_type, $parrent_id = 0) {
        if(is_array($menu_arr) and count(@$menu_arr[$parrent_id]) > 0) {
            foreach($menu_arr[$parrent_id] as $key => $vall) {
                if ($user_type < $vall[2]) {
                    unset($menu_arr[$parrent_id][$key]);
                }
                $this->build_menu($menu_arr, $user_type, $key);
            }
        }
        return $menu_arr;
    }

    public function get_menu() {
        $rang = $this->session->get_data('rang');
        $final_menu = $this->build_menu($this->menu, $rang);

        return $final_menu;
    }

    /**
     * @param $module   //Module name for access
     * @param $type     //Access level
     * @return bool
     */
    public function check_access($module, $type) {
        if($this->session->get_data('primary') == 1 || $this->session->get_data('super') == 1) {
            return TRUE;
        }
        $this->db->select('AM.type')->from('m_administrator_permissions_modules as AM')
                 ->join('m_permissions_modules as PM', 'AM.id_m_permissions_modules=PM.id_m_permissions_modules', 'INNER')
                 ->where('PM.module', $module)->where('AM.id_m_administrators', $this->id_admin)->limit(1);
        $result = $this->db->get()->row_array();

        if(count($result) > 0) {
            if($result['type'] >= $type) {
                return TRUE;
            }
        } else {
            $this->db->select()->from('m_administrator_permissions_users_modules as AM')
                ->join('users_modules as UM', 'AM.id_users_modules=UM.id_users_modules')
                ->where('UM.alias', $module)->where('AM.id_m_administrators', $this->id_admin)->limit(1);
            $result = $this->db->get()->row_array();
            if(count($result) > 0)
                if($result['type'] >= $type) {
                    return TRUE;
                }
        }
        return FALSE;
    }

    public function get_access_lvl($module) {
        $lvl = 0;
        if($this->session->get_data('primary') == 1 || $this->session->get_data('super') == 1) {
            return 2;
        }
        $this->db->select('AM.`type`')->from('`m_administrator_permissions_modules` as AM')
            ->join('m_permissions_modules as PM', 'AM.`id_m_permissions_modules`=PM.`id_m_permissions_modules`', 'INNER')
            ->where('PM.`module`', $module)->where('AM.`id_m_administrators`', $this->id_admin)->limit(1);
        $result = $this->db->get()->row_array();

        if(count($result) > 0) {
            $lvl = $result['type'];
        } else {
            $this->db->select('AM.`type`')->from('`m_administrator_permissions_users_modules` as AM')
                ->join('`users_modules` as UM', 'AM.`id_users_modules`=UM.`id_users_modules`')
                ->where('UM.`alias`', $module)->where('AM.`id_m_administrators`', $this->id_admin)->limit(1);
            $result = $this->db->get()->row_array();
            if(count($result) > 0)
                $lvl = $result['type'];
        }

        return $lvl;
    }

    public function check_perm($alias, $type_module) {
        if($this->session->get_data('primary') == 1 || $this->session->get_data('super') == 1) {
            return TRUE;
        }
        if($alias) {
            $aliases = explode('/', $alias);

            if($type_module == 'system') {
                $perm = $this->session->get_data('system_perm');
                if(isset($perm[$aliases[0]][$aliases[1]])) return TRUE;
            } else {
                $perm = $this->session->get_data('user_perm');
                if(isset($perm[$aliases[0]][$aliases[1]])) return TRUE;
            }
        }

        return FALSE;
    }

}

/* End of file mpermissions.php */