<?php

function helper_discount_coupons_grid_build($grid)
{
    $grid->add_button(
        'Добавить купон на скидку',
        set_url('*/*/add'),
        array(
            'class' => 'addButton'
        )
    );

    $grid->set_checkbox_actions(
        'ID',
        'discount_coupons_grid_checkbox',
        array(
            'option' => array(
                'on'     => 'Активность: Да',
                'off'    => 'Активность: Нет',
                'delete' => 'Удалить выбранные'
            ),
            'name'  => 'discount_coupons_grid_select'
        )
    );

    $grid->add_column(
        array(
            'index'         => 'name',
            'option_string' => 'align="center"',
        ), 'Название купона:'
    );

    $grid->add_column(
        array(
            'index'         => 'description',
            'option_string' => 'align="center"',
        ), 'Описание купона:'
    );

    $grid->add_column(
        array(
            'index'         => 'order_sum',
            'option_string' => 'align="center"',
            'filter'        => 'true'
        ), 'Сумма заказа ОТ:'
    );

    $grid->add_column(
        array(
            'index'         => 'discount_type',
            'option_string' => 'align="center"'
        ), 'Тип скидки:'
    );

    $grid->add_column(
        array(
            'index'         => 'discount_sum',
            'option_string' => 'align="center"'
        ), 'Сумма скидки:'
    );

    $grid->add_column(
        array(
            'index'         => 'discount_percent',
            'option_string' => 'align="center"'
        ), 'Процент скидки:'
    );

    $grid->add_column(
        array(
            'index'         => 'consider_promotional_items',
            'option_string' => 'align="center"'
        ), 'Учитывать акционные товары:'
    );

    $grid->add_column(
        array(
            'index'         => 'is_start',
            'option_string' => 'align="center"',
            'tdwidth'       => '12%',
            'sortable'        => TRUE
        ), 'Задействован:'
    );

    $grid->add_column(
        array(
            'index'         => 'action',
            'type'          => 'action',
            'tdwidth'       => '8%',
            'option_string' => 'align="center"',
            'actions'       => array(
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/*/edit/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_view' , 'title'=>'Изменить купон')
                )
            )
        ), 'Действия:'
    );
}

function helper_discount_coupons_form_build($data = array(), $save_param = '')
{
    $input_disabled = array();
    $select_disabled = array();
    $active_disabled = array();
    $hide_btn = array();

    $form_id = 'discount_coupons_add_edit_form';
    $CI = &get_instance();
    $CI->load->library('form');
    $CI->form->_init('Купоны на скидку', $form_id, set_url("*/*/save".$save_param));
    $CI->form->enable_CKE();

    if(!$data['perm_lvl']) $active_disabled = array('readonly' => NULL, 'disabled' => NULL);

    $CI->form->add_button(
        array(
            'name'  => 'Назад',
            'href'  => set_url('*/*/')
        )
    );

    if(isset($data['main'])) {

        $CI->form->add_button(
            array(
                'name' => 'Добавить купон',
                'href' => set_url('*/*/add')
            )
        );

        if(isset($data['main']['is_start'])) {
            if ($data['main']['is_start'] != 1) {
                $CI->form->add_button(
                    array(
                        'name' => 'Задействовать купон',
                        'href' => set_url('*/*/activate' . $save_param)
                    )
                );
            }

            if ($data['main']['is_start'] == 1) {
                $input_disabled = array('readonly' => NULL);
                $select_disabled = array('readonly' => NULL, 'disabled' => NULL);
            }
        }
    }

    if($save_param == '' || $data['main']['is_start'] != 1)
    {
        $CI->form->add_button(
            array(
                'name' => 'Сохранить',
                'href' => '#',
                'options' => array(
                    'class' => 'addButton',
                    'id' => 'submit_back',
                    'display' => NULL
                )
            )
        );
    }

    $CI->form->add_validation('main[order_sum]', array('required' => 'true'));
    $CI->form->add_validation('main[date_to]', array('required' => 'true'));
    $CI->form->add_validation('email[][title]', array('required' => 'true'));
    $CI->form->add_validation('email[][text]', array('required' => 'true'));

    $CI->form->add_tab('main_block', 'Основные данные');
    $CI->form->add_tab('email_block', 'Данные письма');
    $CI->form->add_tab('product_block', 'Акционные товары');

    $PMdata = FALSE;
    if(isset($data['main'])) $PMdata['main'] = $data['main'];

    $CI->form->add_group('main_block', $PMdata);

    $CI->form->group('main_block')->add_object('html',
        '<div style="text-align: center; color: #fff;"><h2>После внесения изменений не забывайте их сохранить!</h2></div>');

    $lid = $CI->form->group('main_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Основные данные'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[name]',
        'Название купона:',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[description]',
        'Описание купона:',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[order_sum]',
        'Сумма заказа в '.$data['data_default_currency'].' ОТ:',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'select',
        'main[discount_type]',
        'Тип скидки (*):',
        array(
            'options'  => array('0' => 'Сумма', '1' => 'Процент'),
            'option'   => array('id' => 'discount_coupons_add_edit_form_type') + $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '<div style="padding: 0;" id="discount_coupons_add_edit_form_sum">'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[discount_sum]',
        'Сумма скидки в '.$data['data_default_currency'].' (*):',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '</div>'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '<div style="padding: 0;" id="discount_coupons_add_edit_form_percent">'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[discount_percent]',
        'Процент скидки (*):',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '</div>'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[date_from]',
        'Дата начала:',
        array(
            'option' => array('class' => 'datepicker') + $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[date_to]',
        'Дата окончания:',
        array(
            'option' => array('class' => 'datepicker') + $input_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'select',
        'main[active]',
        'Активность (*):',
        array(
            'options' => array('0' => 'Нет', '1' => 'Да'),
            'option'  => $select_disabled+$active_disabled
        )
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'select',
        'main[consider_promotional_items]',
        'Учитывать акционные товары (*):',
        array(
              'options' => array('0' => 'Нет', '1' => 'Да'),
              'option'  => $select_disabled
        )
    );

    $PMdata = FALSE;
    if(isset($data['customers'])) $PMdata['customers'] = $data['customers'];
    $CI->form->add_group('type_block', $PMdata);

    $cust = $CI->form->group('type_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Выбор получателей'
    );

    $CI->form->group('type_block')->add_object_to($cust,
        'select',
        'customers[type]',
        'Выбор получателей',
        array(
            'options' => array(
                '0' => 'Отправить всем зарегистрированым покупателям',
                '1' => 'Отправить выбраным группам покупателей',
                '2' => 'Отправить выбраным покупателя'
            ),
            'option' => array('id' => 'discount_coupons_add_edit_form_customers_type_select') + $select_disabled
        )
    );

    $PMdata = FALSE;
    if(isset($data['customers_group'])) $PMdata['customers_group'] = $data['customers_group'];
    $CI->form->add_group('group_block', $PMdata);

    $CI->form->group('group_block')->add_object('html', "<div id='discount_coupons_group_customers_grid_data' style='display: none; padding-left: 50px; '>");
    $grup = $CI->form->group('group_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Групы покупателей'
    );


    foreach ($data['customers_groups'] as $group => $ms)
    {
        $CI->form->group('group_block')->add_object_to($grup,
            'checkbox',
            'customers_group['.$group.']',
            $ms.' :',
            array(
                'value' => $group,
                'option' => $input_disabled
            )
        );
    }
    $CI->form->group('group_block')->add_object('html', "</div>");

    $CI->form->add_group('cust_block');

    $CI->form->group('cust_block')->add_html("<div id='discount_coupons_customers_grid_data' style='padding:5px; display:none; padding-left: 50px;'>");

    if(isset($data['selected_customers'])) {
        $selected = $CI->form->group('cust_block')->add_object(
            'fieldset',
            'base_fieldset',
            'Выбранные покупатели'
        );
        if(isset($data['main']) && $data['main']['is_start'] == 0 ) {
            $CI->form->group('cust_block')->add_html_to($selected, "<div style='color: #FFF; text-align: center;'> <h3>Для удаления выбраных пользователей отметьте их в таблице!</h3> </div>");
        }
        $CI->form->group('cust_block')->add_html_to($selected, $data['selected_customers']);
    }
    if(isset($data['data_customers'])) {
        $all_cust = $CI->form->group('cust_block')->add_object(
            'fieldset',
            'base_fieldset',
            'Доступные покупатели'
        );

        $CI->form->group('cust_block')->add_html_to($all_cust, $data['data_customers']);
    }
    $CI->form->group('cust_block')->add_html("</div>");

    $PMdata = FALSE;
    if(isset($data['email'])) $PMdata['email'] = $data['email'];
    $CI->form->add_group('email_block', $PMdata, $data['langs']);

    $email = $CI->form->group('email_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Данные письма:'
    );

    $CI->form->group('email_block')->add_object_to($email,
        'text',
        'email[$][title]',
        'Тема письма:',
        array(
            'option' => $input_disabled
        )
    );

    $CI->form->group('email_block')->add_object_to($email,
        'textarea',
        'email[$][text]', 'Текст письма:',
        array(
            'option'  => array('class' => 'ckeditor') + $input_disabled
        )
    );

    $CI->form->add_group('product_block');

    if(isset($data['products_temp']))
    {
        $selected_prod = $CI->form->group('product_block')->add_object(
            'fieldset',
            'base_fieldset',
            'Выбраные товары:'
        );
        $CI->form->group('product_block')->add_html_to($selected_prod, $data['products_temp']);
        $CI->form->add_js_code("$('#discount_coupons_selected_products_grid').gbc_show_product();");
    }

    if(isset($data['products']))
    {
        $product = $CI->form->group('product_block')->add_object(
            'fieldset',
            'base_fieldset',
            'Все товары:'
        );
        $CI->form->group('product_block')->add_html_to($product, $data['products']);
        $CI->form->add_js_code("$('#discount_coupons_products_grid').gbc_show_product();");
    }

    $js =   '
            var active_type = $("#discount_coupons_add_edit_form_type").val();
            if (active_type == 0){
                $("#discount_coupons_add_edit_form_percent").hide();
                $("#discount_coupons_add_edit_form_sum").show();
            }
            else {
                $("#discount_coupons_add_edit_form_percent").show();
                $("#discount_coupons_add_edit_form_sum").hide();
            }

            $("#discount_coupons_add_edit_form_type").change(function(){
                active_type = $(this).val();
                if (active_type == 0){
                    $("#discount_coupons_add_edit_form_percent").hide();
                    $("#discount_coupons_add_edit_form_sum").show();
                }
                else {
                    $("#discount_coupons_add_edit_form_percent").show();
                    $("#discount_coupons_add_edit_form_sum").hide();
                }
            });

            var visible_type_customers = $("#discount_coupons_add_edit_form_customers_type_select").val();
            if (visible_type_customers == 0){
                $("#discount_coupons_customers_grid_data").hide();
                $("#discount_coupons_group_customers_grid_data").hide();
            }
            else
            if (visible_type_customers == 1){
                $("#discount_coupons_customers_grid_data").hide();
                $("#discount_coupons_group_customers_grid_data").show();
            }
            else
            {
                $("#discount_coupons_customers_grid_data").show();
                $("#discount_coupons_group_customers_grid_data").hide();
            }

            $("#discount_coupons_add_edit_form_customers_type_select").change(function(){
                var visible_type_customers = $("#discount_coupons_add_edit_form_customers_type_select").val();
                if (visible_type_customers == 0){
                    $("#discount_coupons_customers_grid_data").hide();
                    $("#discount_coupons_selected_customers_grid_data").hide();
                    $("#discount_coupons_group_customers_grid_data").hide();
                }
                else
                if (visible_type_customers == 1){
                    $("#discount_coupons_customers_grid_data").hide();
                    $("#discount_coupons_selected_customers_grid_data").hide();
                    $("#discount_coupons_group_customers_grid_data").show();
                }
                else
                {
                    $("#discount_coupons_customers_grid_data").show();
                    $("#discount_coupons_group_customers_grid_data").hide();
                    $("#discount_coupons_selected_customers_grid_data").show();
                }
            });
    ';

    $CI->form->group('cust_block')->add_object(
        'js',
        $js
    );

    $CI->form->add_block_to_tab('main_block', 'main_block');
    $CI->form->add_block_to_tab('main_block', 'type_block');
    $CI->form->add_block_to_tab('main_block', 'group_block');
    $CI->form->add_block_to_tab('main_block', 'cust_block');
    $CI->form->add_block_to_tab('email_block', 'email_block');
    $CI->form->add_block_to_tab('product_block', 'product_block');

    $CI->form->render_form();
}

function select_customers_grid_build($grid)
{
    $grid->set_checkbox_actions('ID', 'customer[]',
        array(
            'options'   => NULL,
            'name'      => NULL,
        )
    );

    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'Фамилия, Имя'
    );

    $grid->add_column(
        array(
            'index' => 'email',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'Email'
    );
}

function selected_customers_temp_grid_build($grid)
{
    $grid->set_checkbox_actions('ID', 'customer_temp[]',
        array(
            'options'   => NULL,
            'name'      => NULL
        )
    );

    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text'
        ), 'Фамилия, Имя'
    );

    $grid->add_column(
        array(
            'index' => 'email',
            'type'  => 'text'
        ), 'Email'
    );
}

function selected_customers_grid_build($grid)
{
    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text'
        ), 'Фамилия, Имя'
    );

    $grid->add_column(
        array(
            'index' => 'email',
            'type'  => 'text'
        ), 'Email'
    );

    $grid->add_column
    (
        array(
            'index'         => 'is_used',
            'type'          => 'text',
            'option_string' => 'align="center"',
            'options'       => array('' => '', '0' => 'Нет', '1'=>'Да')
        ), 'Использовано'
    );
}

function products_grid_build($grid)
{
    $grid->set_checkbox_actions('ID', 'products[]',
        array(
            'options'   => NULL,
            'name'      => NULL
        )
    );

    $grid->add_column(
        array(
            'index' => 'sku',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'SKU'
    );

    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'Название товара'
    );

    $grid->add_column(
        array(
            'index'		 => 'status',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%',
        ), 'В поиске');

    $grid->add_column(
        array(
            'index'		 => 'in_stock',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%'

        ),'В наличии');

    $grid->add_column(
        array(
            'index'         => 'action',
            'type'          => 'action',
            'tdwidth'       => '8%',
            'option_string' => 'align="center"',
            'actions'       => array(
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/products/view/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_view show_product_link' , 'title'=>'Изменить купон')
                )
            )
        ), 'Действия:'
    );
}

function selected_products_grid_build($grid)
{
    $grid->add_column(
        array(
            'index' => 'sku',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'SKU'
    );

    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'Название товара'
    );

    $grid->add_column(
        array(
            'index'		 => 'status',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%',
        ), 'В поиске');

    $grid->add_column(
        array(
            'index'		 => 'in_stock',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%'

        ),'В наличии');

    $grid->add_column(
        array(
            'index'         => 'action',
            'type'          => 'action',
            'tdwidth'       => '8%',
            'option_string' => 'align="center"',
            'actions'       => array(
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/products/view/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_view show_product_link' , 'title'=>'Изменить купон')
                )
            )
        ), 'Действия:'
    );
}

function selected_products_temp_grid_build($grid)
{
    $grid->set_checkbox_actions('ID', 'products_temp[]',
        array(
            'options'   => NULL,
            'name'      => NULL
        )
    );

    $grid->add_column(
        array(
            'index' => 'sku',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'SKU'
    );

    $grid->add_column(
        array(
            'index' => 'name',
            'type'  => 'text',
            'filter'=> TRUE
        ), 'Название товара'
    );

    $grid->add_column(
        array(
            'index'		 => 'status',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%',
        ), 'В поиске');

    $grid->add_column(
        array(
            'index'		 => 'in_stock',
            'type'		 => 'select',
            'filter'     => TRUE,
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%'

        ),'В наличии');

    $grid->add_column(
        array(
            'index'         => 'action',
            'type'          => 'action',
            'tdwidth'       => '8%',
            'option_string' => 'align="center"',
            'actions'       => array(
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/products/view/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_view show_product_link' , 'title'=>'Изменить купон')
                )
            )
        ), 'Действия:'
    );
}

?>