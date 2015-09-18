<?php

function helper_excel_export_form($data) {
    $form_id = 'excel_export_form';
    $CI = & get_instance();
    $CI->load->library('form');
    $CI->form->_init('Действия с покупателями', $form_id, set_url('*/*/export'));

    $CI->form->add_button(
        array(
            'name' => 'Назад',
            'href' => set_url('customers')
        ));

    $CI->form->add_button(
        array(
            'name' => 'Експортировать в Excel',
            'href' => '#',
            'options' => array(
                'id' => 'submit'
            )
        ));

    $CI->form->add_tab('m_b', 'Настройки экспорта');

    $CI->form->add_group('m_b');

    $lid = $CI->form->group('m_b')->add_object(
        'fieldset',
        'sale_actions_data',
        'Опции'
    );

    $CI->form->group('m_b')->add_object_to($lid,
        'select',
        'excel_export_type',
        'Экспортируемая информация :',
        array(
            'options'	=> array('0' => 'Только E-mail адреса', '1' => 'Полная информация')
        )
    );

    $CI->form->group('m_b')->add_object_to($lid,
        'select',
        'excel_customers_type',
        'Выбор покупателей :',
        array(
            'options'	=> array('0' => 'Все покупатели', '1' => 'Выбранные групы'),
            'option' => array('id' => 'discount_coupons_add_edit_form_customers_type_select')
        )
    );

    $CI->form->add_group('group_block');

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
                'value'  => $group
            )
        );
    }
    $CI->form->group('group_block')->add_object('html', "</div>");

    $js =   '
        $("#discount_coupons_add_edit_form_customers_type_select").change(function(){
            var visible_type_customers = $("#discount_coupons_add_edit_form_customers_type_select").val();
            if (visible_type_customers == 0){
                $("#discount_coupons_group_customers_grid_data").hide();
            } else {
                $("#discount_coupons_group_customers_grid_data").show();
            }
        });
    ';
    $CI->form->group('group_block')->add_object(
        'js',
        $js
    );

    $CI->form->add_block_to_tab('m_b', 'm_b');
    $CI->form->add_block_to_tab('m_b', 'group_block');
    $CI->form->render_form();
}