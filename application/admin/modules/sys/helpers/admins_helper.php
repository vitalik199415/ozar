<?php

function helper_admins_grid_build($grid) {
    $grid->add_button(
        'Добавить нового администратора',
        set_url('*/*/add'),
        array(
            'class' => 'addButton'
        )
    );

    $grid->set_checkbox_actions(
        'ID',
        'permissions_modules_grid_checkbox',
        array(
            'option' => array(
                'on'     => 'Активность: Да',
                'off'    => 'Активность: Нет',
                'delete' => 'Удалить выбранные'
            ),
            'name'  => 'permissions_modules_grid_select'
        )
    );

    $grid->add_column(
        array(
            'index'         => 'name',
            'option_string' => 'align="center"',
        ), 'Им`я администратора:'
    );

    $grid->add_column(
        array(
            'index'         => 'login',
            'option_string' => 'align="center"',
        ), 'Логин:'
    );

    $grid->add_column(
        array(
            'index'         => 'email',
            'option_string' => 'align="center"'
        ), 'Електронная пошта:'
    );

    $grid->add_column(
        array(
            'index'         => 'active',
            'option_string' => 'align="center"'
        ), 'Активность:'
    );

    $grid->add_column(
        array(
            'index'         => 'note',
            'option_string' => 'align="center"'
        ), 'Заметки:'
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
                    'options'       => array('class'=>'icon_edit', 'title'=>'Редактировать')
                ),
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/*/delete/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_delete delete_question' , 'title'=>'Удалить')
                )
            )
        ), 'Действия:'
    );
}

function helper_admins_form_build($data = array(), $save_param = '') {
    $form_id = 'admins_add_edit_form';
    $CI = &get_instance();
    $CI->load->library('form');
    $CI->form->_init('Администраторы системы', $form_id, set_url("*/*/save".$save_param));

    $CI->form->add_button(
        array(
            'name'  => 'Назад',
            'href'  => set_url('*/*/')
        )
    );

    $CI->form->add_button(
        array(
            'name' => 'Добавить администратора',
            'href' => set_url('*/*/add')
        )
    );

    $CI->form->add_button(
        array(
            'name' => 'Сохранить',
            'href' => '#',
            'options' => array(
                'class' => 'addButton',
                'id' => 'submit',
                'display' => NULL
            )
        )
    );

    $CI->form->add_button(
        array(
            'name' => 'Сохранить и продолжить',
            'href' => '#',
            'options' => array(
                'class' => 'addButton',
                'id' => 'submit_back',
                'display' => NULL
            )
        )
    );

    $CI->form->add_tab('main_block', 'Основные данные');
    $CI->form->add_tab('a_modules', 'Системные модули');
    $CI->form->add_tab('u_modules', 'Пользовательские модули');
    $CI->form->add_tab('categories_perm', 'Доступ к категориям');

    //Блок основных данных
    $PMdata = FALSE;
    if(isset($data['main'])) $PMdata['main'] = $data['main'];

    $CI->form->add_group('main_block', $PMdata);

    $main = $CI->form->group('main_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Основные данные'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[login]',
        'Логин:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[password]',
        'Пароль:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[email]',
        'Електронная пошта:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[name]',
        'Имя:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[note]',
        'Заметки:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'select',
        'main[active]',
        'Активность:',
        array(
            'options' => array('0' => 'Нет', '1' => 'Да')
        )
    );

    $CI->form->group('main_block')->add_object_to($main,
        'select',
        'main[superadmin]',
        'Суперадминистратор:',
        array(
            'options' => array('0' => 'Нет', '1' => 'Да')
        )
    );

    $PMdata = FALSE;
    if(isset($data['system_modules'])) {
        $PMdata['system_modules'] = $data['system_modules'];
        $PMdata['system_perm'] = $data['system_perm'];
    }

    $CI->form->add_group('a_modules', $PMdata);

    $system_modules = $CI->form->group('a_modules')->add_object(
        'fieldset',
        'base_fieldset',
        'Доступ к системным модулям'
    );

    if(count($data['a_modules']) > 0) {
        foreach ($data['a_modules'] as $key => $vall) {
            $CI->form->group('a_modules')->add_object_to($system_modules,
                'checkbox',
                'system_modules['.$key.']',
                $vall['name'],
                array(
                    'value' => $key,
                    'option' => array('class' => 'attributes')
                )
            );

            $CI->form->group('a_modules')->add_object_to($system_modules,
                'html',
                '<div style="padding:5px 30px; margin: 0 0 10px 50px;color: #fff">'
            );

            $CI->form->group('a_modules')->add_object_to($system_modules,
                'html',
                '<p>'.$vall['desc'].'</p><br/>'
            );

            if(isset($data['a_types'][$key])) {
                foreach($data['a_types'][$key] as $key_p => $val_p) {
                    $CI->form->group('a_modules')->add_object_to($system_modules,
                        'checkbox',
                        'system_perm['.$key.']['.$key_p.']',
                        $val_p['name'],
                        array(
                            'value'  => $key_p,
                            'option' => array('class' => 'attributes')
                        )
                    );
                }
            }

            $CI->form->group('a_modules')->add_object_to($system_modules,
                'html',
                '</div>'
            );
        }
    }

    $PMdata = FALSE;
    if(isset($data['user_modules'])) {
        $PMdata['user_modules'] = $data['user_modules'];
        $PMdata['user_perm'] = $data['user_perm'];
    }

    $CI->form->add_group('u_modules', $PMdata);

    $user_modules = $CI->form->group('u_modules')->add_object(
        'fieldset',
        'base_fieldset',
        'Доступ к пользовательским модулям'
    );

    if(count($data['u_modules']) > 0) {
        foreach ($data['u_modules'] as $key => $vall) {
            $CI->form->group('u_modules')->add_object_to($user_modules,
                'checkbox',
                'user_modules['.$key.']',
                $vall['name'],
                array(
                    'value' => $key,
                    'option' => array('class' => 'attributes')
                )
            );

            $CI->form->group('u_modules')->add_object_to($user_modules,
                'html',
                '<div style="padding:5px 30px; margin: 0 0 10px 50px; color: #fff">'
            );

            if(isset($data['u_types'][$key])) {
                foreach($data['u_types'][$key] as $key_p => $val_p) {
                    $CI->form->group('u_modules')->add_object_to($user_modules,
                        'checkbox',
                        'user_perm['.$key.']['.$key_p.']',
                        $val_p['name'],
                        array(
                            'value'  => $key_p,
                            'option' => array('class' => 'attributes')
                        )
                    );
                }
            }

            $CI->form->group('u_modules')->add_object_to($user_modules,
                'html',
                '</div>'
            );
        }
    }

    $PMdata = FALSE;
    if(isset($data['cat_perm'])) {
        $PMdata['cat_perm'] = $data['cat_perm'];
    }

    $CI->form->add_group('categories_perm', $PMdata);

    $categories_perm = $CI->form->group('categories_perm')->add_object(
        'fieldset',
        'base_fieldset',
        'Доступ к категориям каталога'
    );

    if(count($data['categories']) > 0) {
        foreach ($data['categories'] as $key => $ms) {

            $CI->form->group('categories_perm')->add_object_to($categories_perm,
                'html',
                '<div style="padding:0 0 0 ' . ($ms['level'] * 40) . 'px;">'
            );
            $CI->form->group('categories_perm')->add_object_to($categories_perm,
                'checkbox',
                'cat_perm[' . $ms['ID'] . ']',
                $ms['name'],
                array(
                    'value' => $ms['ID']
                )
            );
            $CI->form->group('categories_perm')->add_object_to($categories_perm,
                'html',
                '</div>'
            );
        }
    }

    $CI->form->add_block_to_tab('main_block', 'main_block');
    $CI->form->add_block_to_tab('a_modules', 'a_modules');
    $CI->form->add_block_to_tab('u_modules', 'u_modules');
    $CI->form->add_block_to_tab('categories_perm', 'categories_perm');

    $CI->form->render_form();

}