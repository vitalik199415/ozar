<?php
function helper_products_grid_build($grid)
{
    $grid->set_checkbox_actions('ID', 'products_checkbox',
        array(
            'options' => array(
                'status_on' => 'В поиске: Да',
                'status_off' => 'В поиске: Нет',
                'in_stock_on' => 'В наличии: Да',
                'in_stock_off' => 'В наличии: Нет',
                'delete' => 'Удалить выбраные'
            ),
            'name' => 'products_select_action'
        )
    );

    $grid->add_column(
        array
        (
            'index'		 => 'sku',
            'type'		 => 'text',
            'tdwidth'	 => '10%',
            'filter'	 => true
        ), 'Артикул');
    $grid->add_column(
        array
        (
            'index'		 => 'name',
            'type'		 => 'text',
            'filter'	 => true
        ),'Название');
    $grid->add_column(
        array
        (
            'index'		 => 'create_date',
            'type'		 => 'date',
            'tdwidth'	 => '11%',
            'sortable' 	 => true,
            'filter'	 => true
        ), 'Создан');
   /* $grid->add_column(
        array
        (
            'index'		 => 'update_date',
            'type'		 => 'date',
            'tdwidth'	 => '11%',
            'sortable' 	 => true,
            'filter'	 => true
        ), 'Обновлен');*/
    $grid->add_column(
        array
        (
            'index'		 => 'status',
            'type'		 => 'select',
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%',
            'filter'	 => true
        ), 'В поиске');
    /*$grid->add_column(
        array
        (
            'index'		 => 'in_stock',
            'type'		 => 'select',
            'options'	 => array(''=>'','0'=>'Нет','1'=>'Да'),
            'tdwidth'	 => '8%',
            'filter'	 => true
        ),'В наличии');*/
    $grid->add_column(
        array
        (
            'index'		 => 'new_comment',
            'tdwidth'	 => '8%',
            'type'		 => 'select',
            'options'	 => array(''=>'', '1' => 'Новые отзывы'),
            'filter'	 => true
        ),'Новые отзывы');

    $grid->add_column(
        array
        (
            'index'		 => 'action',
            'type'		 => 'action',
            'tdwidth'	 => '12%',
            'option_string' => 'align="center"',
            'sortable' 	 => false,
            'filter'	 => false,
            'actions'	 => array(
                array(
                    'type' 			=> 'link',
                    'html' 			=> '',
                    'href' 			=> set_url(array('*','products','view','id','$1')),
                    'href_values' 	=> array('ID'),
                    'options'		=> array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
                ),

                array(
                    'type' 			=> 'link',
                    'html' 			=> '',
                    'href' 			=> set_url('*/*/view_product_comments/id/$1'),
                    'href_values' 	=> array('ID'),
                    'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Отзывы к товару')
                )
            )
        ), 'Actions');
}

function comments_grid_build($Grid, $id)
{
    $Grid->add_button('Назад', set_url('*/*'),
        array(
            'rel' => 'add',
            'class' => 'addButton'
        ));

    $Grid->add_button('Добавить отзыв', set_url('*/*/add/id/'.$id),
        array(
            'rel' => 'add',
            'class' => 'addButton'
        ));

    /*$Grid->set_checkbox_actions('ID', 'comment_grid_checkbox',
        array(
            'options' => array(
                'on' => 'Активность: Да',
                'off' => 'Активность: Нет',
                'delete' => 'Удалить выбраные'

            ),
            'name' => 'comment_grid_select'
        )
    );*/

    $Grid->add_column(
        array
        (
            'index'		 => 'name',
            'type'		 => 'text',
            'tdwidth'	 => '10%',
			'filter'	 => true
        ), 'Имя');

    $Grid->add_column(
        array
            (
                'index'		 => 'email',
				'type'		 => 'text',
                'tdwidth'	 => '14%',
				'filter'	 => true
            ),'Email');

    $Grid->add_column(
        array
            (
                'type'		 => 'text',
				'index'		 => 'message',
				'type'		 => 'text'
            ),'Сообщение');

    $Grid->add_column(
        array
        (
            'type'		 => 'text',
			'index'		 => 'answer',
			'type'		 => 'text'
        ),'Ответ');

    $Grid->add_column(
        array
            (
                'index'		 => 'create_date',
				'type'		 => 'date',
                'tdwidth'	 => '11%',
				'filter'	 => true
            ),'Создан' );
			
    $Grid->add_column(
        array
            (
                'type'		 => 'select',
				'index'		 => 'active',
                'tdwidth'	 => '7%',
				'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
				'filter'	 => true
            ),'Активность');

    $Grid->add_column(
        array
            (
                'type'		 => 'select',
				'index'		 => 'is_answer',
                'tdwidth'	 => '6%',
				'options'	 => array('' => '', '0' => 'Нет', '1' => 'Да'),
				'filter'	 => true
            ),'Ответ');

    $Grid->add_column(
        array
            (
                'index'		 => 'action',
                'type'		 => 'action',
                'tdwidth'	 => '10%',
                'option_string' => 'align="center"',
                'actions'	 => array(
                    array(
                        'type' 			=> 'link',
                        'html' 			=> '',
                        'href' 			=> set_url('*/*/edit/id/'.$id.'/id_c/$1'),
                        'href_values' 	=> array('ID'),
                        'options'		=> array('class'=>'icon_edit', 'title'=>'Редактировать')
                    ),
                    array(
                        'type' 			=> 'link',
                        'html' 			=> '',
                        'href' 			=> set_url('*/*/delete/id/'.$id.'/id_c/$1'),
                        'href_values' 	=> array('ID'),
                        'options'		=> array('class'=>'icon_detele delete_question', 'title'=>'Удалить')
                    )
                )
            ),'Действия'
    );
    return $Grid;
}

function helper_add_edit_comment_form_build($data = array(), $id, $save_param = '')
{
    $form_id = 'add_edit_comment_form';
    $CI = & get_instance();
    $CI->load->library('form');
    $CI->form->_init('Отзывы к товару', $form_id, set_url('*/*/save/id/'.$id.$save_param));
    $CI->form->enable_CKE();
    $CI->form->add_button(
        array(
            'name' 		=> 'Назад',
            'href' 		=> set_url('*/*/view_product_comments/id/'.$id),
            'options' 	=> array()
        ));

    $CI->form->add_button(
        array(
            'name' 		=> 'Сохранить и продолжить редактирование',
            'href' 		=> '#',
            'options' 	=> array(
                'id' 	=> 'submit_back'
            )
        ));

    $CI->form->add_button(
        array(
            'name' => 'Сохранить',
            'href' => '#',
            'options' => array(
                'id' => 'submit',
                'class' => 'addButton'
            )
        ));

    $CI->form->add_tab('comment', 'Коментарий');
	
	$CI->form->add_validation('main[name]', array('required' => 'true'));
	
    if(!isset($data['main'])) $data['main'] = FALSE;

    $CI->form->add_group('comment', $data);

    $lid = $CI->form->group('comment')->add_object(
        'fieldset',
        'base_fieldset',
        'Комментарий к продукту'
    );

    if($save_param != '')
    {
        $CI->form->group('comment')->add_object_to($lid,
            'select',
            'main[id_langs]',
            'Выберите язык :',
            array(
                'option' => array('disabled' => NULL, 'readonly' => NULL),
                'options' => $data['on_langs']
            )
        );
    }
    else
    {
        $CI->form->group('comment')->add_object_to($lid,
            'select',
            'main[id_langs]',
            'Выберите язык :',
            array(
                'options' => $data['on_langs']
            )
        );
    }

    $CI->form->group('comment')->add_object_to($lid,
        'text',
        'main[name]',
        'Имя :',
        array(
            'option' => array('maxlength' => '40')
        )
    );

    $CI->form->group('comment')->add_object_to($lid,
        'text',
        'main[email]',
        'Email :',
        array(
            'option' => array('maxlength' => '40')
        )
    );

    $CI->form->group('comment')->add_object_to($lid,
        'select',
        'main[active]',
        'Активность :',
        array(
            'options'	=> array('1' => 'Да', '0' => 'Нет')
        )
    );

    $CI->form->group('comment')->add_object_to($lid,
        'textarea',
        'main[message]',
        'Введите сообщение :',
        array(
            'option'	=> array('class' => 'ckeditor')
        )
    );

    $CI->form->add_tab('answer','Ответ');

    $CI->form->add_group('answer', $data);

    $lid = $CI->form->group('answer')->add_object(
        'fieldset',
        'base_fieldset',
        'Ответ'
    );
    $CI->form->group('answer')->add_object_to($lid,
        'textarea',
        'answer[message]',
        'Введите сообщение :',
        array(
            'option'	=> array('class' => 'ckeditor')
        )
    );

    $CI->form->add_block_to_tab('comment', 'comment');
    $CI->form->add_block_to_tab('answer', 'answer');

    $CI->form->render_form();
}

function helper_answer_form_build($data = array(), $id, $save_param = '')
{

    $form_id = 'answer_form';
    $CI = & get_instance();
    $CI->load->library('form');
    $CI->form->_init('Отзывы', $form_id, set_url('*/*/save/id/'.$id.$save_param));
    $CI->form->enable_CKE();
    $CI->form->add_button(
        array(
            'name' 		=> 'Назад',
            'href' 		=> set_url('*/*/*'),
            'options' 	=> array()
        ));

    $CI->form->add_button(
        array(
            'name' 		=> 'Сохранить и продолжить редактирование',
            'href' 		=> '#',
            'options' 	=> array(
                'id' 	=> 'submit_back',
                'class' => 'addButton'
            )
        ));

    $CI->form->add_button(
        array(
            'name' => 'Сохранить',
            'href' => '#',
            'options' => array(
                'id' => 'submit',
                'class' => 'addButton'
            )
        ));

    $CI->form->add_tab('comment','Отзыв');
    if(!isset($data['main'])) $data['main'] = FALSE;

    $CI->form->add_group('comment', $data);
//echo var_dump($data);
    $lid = $CI->form->group('comment')->add_object(
        'fieldset',
        'base_fieldset',
        'Детали сообщения'
    );

    $CI->form->group('comment')->add_object_to($lid,
        'text',
        'main[name]',
        'Имя :',
        array(
            'option' => array('maxlength' => '80', 'readonly' => 'TRUE')
        )
    );

    $CI->form->group('comment')->add_object_to($lid,
        'text',
        'main[email]',
        'Email :',
        array(
            'option' => array('maxlength' => '80', 'readonly' => 'TRUE')
        )
    );

    /*$CI->form->group('comment')->add_object_to($lid,
        'select',
        'main[active]',
        'Активность :',
        array(
            'options'	=> array('1'=>'Да', '0'=>'Нет')
        )
    );*/

    /*$CI->form->group('comment')->add_object_to($lid,
                'checkbox',
                'main[mail_notification]',
                'Получить уведомление об ответе на email? :',
                array(
                    'value'	=> '1',
                )
            );*/

    $CI->form->group('comment')->add_object_to($lid,
        'textarea',
        'main[message]',
        'Введите вопрос :',
        array(
            'option'	=> array('maxlenght' => '100', 'readonly' => 'TRUE')//'class' => 'ckeditor', 'disabled'=>'TRUE')
        )
    );



    $lid = $CI->form->group('comment')->add_object(
        'hidden',
        'main[id_langs]'
    );

    $CI->form->add_tab('answer','Ответ');

    $CI->form->add_group('answer', $data);

    $lid = $CI->form->group('answer')->add_object(
        'fieldset',
        'base_fieldset',
        'Ответ'
    );

    /*$CI->form->group('answer')->add_object_to($lid,
                'text',
                'answer[langs][$][name]',
                'Имя :',
                array(
                    'option' => array('maxlength' => '80')
                )
            );

    $CI->form->group('answer')->add_object_to($lid,
                'text',
                'answer[email]',
                'Email :',
                array(
                    'option' => array('maxlength' => '80')
                )
            );*/

    $CI->form->group('answer')->add_object_to($lid,
        'select',
        'answer[active]',
        'Активность :',
        array(
            'options'	=> array('1'=>'Да', '0'=>'Нет')
        )
    );

    /*$CI->form->group('answer')->add_object_to($lid,
                'checkbox',
                'answer[mail_notification]',
                'Получить уведомление об ответе на email? :',
                array(
                    'value'	=> '1'
                )
            );*/

    $CI->form->group('answer')->add_object_to($lid,
        'textarea',
        'answer[message]',
        'Введите сообщение :',
        array(
            'option'	=> array('maxlenght' => '100', 'class' => 'ckeditor')
        )
    );

    $CI->form->add_block_to_tab('comment', 'comment');
    $CI->form->add_block_to_tab('answer', 'answer');
    $CI->form->render_form();


}

?>