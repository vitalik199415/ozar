<?php
function helper_discount_grid_build($grid)
{
    $grid->add_button("Добавить скидку на покупку", set_url("*/*/add"),
        array(
            'class' => 'addButton'
        )
    );

    $grid->set_checkbox_actions('ID', 'discounts_grid_checkbox',
        array(
            'options' => array(
                'on' => 'Активность: Да',
				'off' => 'Активность: Нет',
				'delete' => 'Удалить выбраные'
            ),
            'name' => 'discounts_grid_select',
        )
    );

    $grid->add_column(
        array
            (
                'index'         => 'type_discounts',
                'tdwidth'       => '10%',
                'option_string' => 'align="center"'
            ),
            'Тип скидки'
    );

    $grid->add_column
    (
        array(
            'index'         => 'sum_from',
            'type'          => 'text',
            'option_string' => 'align="center"',
            'sortable'      => TRUE
        ), 'Сумма заказа от:'
    );

    $grid->add_column
    (
        array(
            'index'         => 'sum_to',
            'type'          => 'text',
            'option_string' => 'align="center"',
            'sortable'      => TRUE
        ), 'Сумма заказа до:'
    );

    $grid->add_column
    (
        array(
            'index'         => 'discount_sum',
            'type'          => 'text',
            'option_string' => 'align="center"'
        ), 'Сумма скидки'
    );

    $grid->add_column
    (
        array(
            'index'         => 'discount_percent',
            'type'          => 'text',
            'option_string' => 'align="center"'
        ), 'Процент скидки'
    );

    $grid->add_column
    (
        array(
            'index'         => 'active',
            'type'          => 'select',
            'option_string' => 'align="center"',
            'filter'        => TRUE,
            'options'       => array('' => '', '0' => 'Нет', '1'=>'Да')
        ), 'Активность'
    );

    $grid->add_column
    (
        array(
            'index'         => 'action',
            'type'          => 'action',
            'tdwidth'       => '12%',
            'option_string' => 'align="center"',
            'actions'       => array(
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/*/edit/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_edit', 'title'=>'Редактировать скидку')
                ),
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/*/delete/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_delete delete_question' , 'title'=>'Удалить скидку')
                )
            )
        ), 'Действия'
    );
}

function helper_discount_form_build($data = array(), $save_param = '')
{

    $form_id = "discounts_add_edit_form";
    $CI = &get_instance();
    $CI->load->library("form");
    $CI->form->_init("Скидки на покупку", $form_id, set_url('*/*/save'.$save_param));

    $CI->form->add_button(
        array(
            'name'  => 'Назад',
            'href'  => set_url('*/*')
        )
    );

    if($save_param != '')
    {
        $CI->form->add_button(
            array(
                'name'  => 'Добавить скидку',
                'href'  => set_url('*/*/add')
            )
        );

        $CI->form->add_button(
            array(
                'name'      => 'Удалить скидку',
                'href'      => set_url('*/*/delete'.$save_param),
                'options'   => array(
                    'class' => 'delete_question'
                )
            )
        );
    }

    $CI->form->add_button(
        array(
            'name'      => 'Сохранить и продолжить редактирование',
            'href'      => '#',
            'options'   => array(
                'id'    => 'submit_back',
                'class' => 'addButton'
            )
        )
    );

    $CI->form->add_button(
        array(
            'name'      => 'Сохранить',
            'href'      => '#',
            'options'   => array(
                'id'    => 'submit',
                'class' => 'addButton'
            )
        )
    );

    $CI->form->add_validation('main[sum_from]', array('required' => 'true'));
    //$CI->form->add_validation('main[sum_to]', array('required' => 'true'));

    $CI->form->add_inputmask('main[sum_from]', 'Regex', 'regex: "^([0-9]{1,10})([\\.][0-9]{2})$"');
    //$CI->form->add_inputmask('main[sum_to]', 'Regex', 'regex: "^([0-9]{1,10})([\\.][0-9]{2})$"');

    $CI->form->add_tab('main_block', 'Основные данные');

    $PMdata['main'] = FALSE;
    if(isset($data['main']))  $PMdata['main'] = $data['main'];

    $CI->form->add_group('main_block', $PMdata);

    $lid = $CI->form->group('main_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Основные данные'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'select',
        'main[type_discounts]',
        'Тип скидки (*):',
        array('options' => array('0' => 'Сумма', '1' => 'Процент'),
              'option'  => array('id' => 'discounts_add_edit_form_type'))

    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[sum_from]',
        'Сумма заказа в '.$data['data_default_currency'].' от (*):'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[sum_to]',
        'Сумма заказа в '.$data['data_default_currency'].' до (*):'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '<div style="padding: 0;" id="discounts_add_edit_form_sum">'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[discount_sum]',
        'Сумма скидки в '.$data['data_default_currency'].':'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '</div>'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '<div style="padding: 0;" id="discounts_add_edit_form_percent">'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'text',
        'main[discount_percent]',
        'Процент скидки:'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'html',
        '</div>'
    );

    $CI->form->group('main_block')->add_object_to($lid,
        'select',
        'main[active]',
        'Активность (*):',
        array('options' => array('0' => 'Нет', '1' => 'Да'))
    );

    $js =   '
    var active_type = $("#discounts_add_edit_form_type").val();
    if (active_type == 0){
        $("#discounts_add_edit_form_percent").hide();
        $("#discounts_add_edit_form_sum").show();
    }
    else {
        $("#discounts_add_edit_form_percent").show();
        $("#discounts_add_edit_form_sum").hide();
    }

    $("#discounts_add_edit_form_type").change(function(){
        active_type = $(this).val();
        if (active_type == 0){
            $("#discounts_add_edit_form_percent").hide();
            $("#discounts_add_edit_form_sum").show();
        }
        else {
            $("#discounts_add_edit_form_percent").show();
            $("#discounts_add_edit_form_sum").hide();
        }
    });';

    $CI->form->group('main_block')->add_object(
        'js',
        $js
    );

    $CI->form->add_block_to_tab('main_block', 'main_block');

    $CI->form->render_form();
}
?>