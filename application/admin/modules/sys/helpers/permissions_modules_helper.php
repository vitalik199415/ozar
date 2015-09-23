<?php

function helper_permissions_modules_grid_build($grid) {
    $grid->add_button(
        'Добавить новый модуль',
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
            'index'         => 'aliace',
            'option_string' => 'align="center"',
        ), 'Алиас модуля:'
    );

    $grid->add_column(
        array(
            'index'         => 'name',
            'option_string' => 'align="center"',
        ), 'Название модуля:'
    );

    $grid->add_column(
        array(
            'index'         => 'description',
            'option_string' => 'align="center"'
        ), 'Описание модуля:'
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
                    'options'       => array('class'=>'icon_edit', 'title'=>'Редактировать модуль')
                ),
                array(
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url('*/*/delete/id/$1'),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_delete delete_question' , 'title'=>'Удалить модуль')
                )
            )
        ), 'Действия:'
    );
}

function helper_permissions_modules_form_build($data = array(), $save_param = '') {
    $form_id = 'permissions_modules_add_edit_form';
    $CI = &get_instance();
    $CI->load->library('form');
    $CI->form->_init('Модули системы', $form_id, set_url("*/*/save".$save_param));
    
    $CI->form->add_button(
        array(
            'name'  => 'Назад',
            'href'  => set_url('*/*/')
        )
    );
    
    $CI->form->add_button(
        array(
            'name' => 'Добавить модуль',
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
    $CI->form->add_tab('desc_block', 'Описание модуля');
    $CI->form->add_tab('perm_block', 'Права доступа');
    
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
        'main[module]',
        'Алиас модуля:'
    );

    $CI->form->group('main_block')->add_object_to($main,
        'text',
        'main[rang]',
        'Ранг доступа к модулю:'
    );

    //Блок описания
    $PMdata = FALSE;
    if(isset($data['desc'])) $PMdata['desc'] = $data['desc'];
    $CI->form->add_group('desc_block', $PMdata, $data['langs']);

    $desc = $CI->form->group('desc_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Описание:'
    );

    $CI->form->group('desc_block')->add_object_to($desc,
        'text',
        'desc[$][name]',
        'Название модуля:'
    );

    $CI->form->group('desc_block')->add_object_to($desc,
        'textarea',
        'desc[$][description]', 
        'Описание модуля:'
    );

    //Product Permissions Block
    $edit_data = FALSE;
    $perm_blocks = array('new_price' => '#1');
    if(isset($data['perm'])) $edit_data['perm'] = $data['perm'];
    if(isset($data['perm_blocks'])) $perm_blocks = $data['perm_blocks'];

    /*if($session_temp = $CI->session->flashdata($form_id))
    {
        $edit_data['product_prices'] = @$session_temp['product_prices'];
        if(isset($session_temp['product_prices']) && is_array($session_temp['product_prices']))
        {
            foreach($session_temp['product_prices'] as $key => $ms)
            {
                $perm_blocks[$key] = $ms['alias'];
            }
        }
    }*/

    $CI->form->add_group('perm_block_top');
    $CI->form->group('perm_block_top')->add_object(
        'html',
        '<div class="def_buttons" align="center"><a href="#" onclick="addTab();return false;">Добавить новое правило</a></div><br />'
    );
    $CI->form->add_group('perm_block', $edit_data, $perm_blocks);
    $CI->form->group('perm_block')->add_object(
        'html',
        '<div id="perm">

		<div style="padding:5px 0 20px 0;" class="def_buttons" align="center">
			<a href="#" class="delete_price">Удалить правило</a>
		</div>'
    );
    $CI->form->group('perm_block')->add_object(
        'text',
        'perm[$][alias]',
        'Идентификатор правила:',
        array(
            'option'	=> array('class' => $form_id.'_perm_alias', 'maxlength' => '40')
        )
    );

    $lid = $CI->form->group('perm_block')->add_object(
        'fieldset',
        'base_fieldset',
        'Описание правила'
    );
    foreach($data['langs'] as $key => $ms)
    {
        $CI->form->group('perm_block')->add_object_to($lid,
            'text',
            'perm[$][desc]['.$key.'][name]',
            'Название правила ('.$ms.') :',
            array(
                'option' => array('maxlength' => '50')
            )
        );
        $CI->form->group('perm_block')->add_object_to($lid,
            'textarea',
            'perm[$][desc]['.$key.'][description]',
            'Описание к правилу ('.$ms.') :',
            array(
                'option' => array('rows' => '3')
            )
        );
    }
    $CI->form->group('perm_block')->add_object_to($lid,
        'html',
        '
		</div>
		'
    );

    $js_perm_tabs =
        '
		var Tabi=0;
		function addTab()
		{
			if($("#'.$form_id.' #perm_block div.langs_tabs_block").length<=15)
			{
				$("#'.$form_id.' #perm_block .langs_tabs ul").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('perm_block')->create_tab("#"))).'");
				$("#'.$form_id.' #perm_block").append("'.addslashes(str_replace( array( "\n", "\r" ), "", $CI->form->group('perm_block')->create_tabs_block_NL())).'");
				fields = $("#'.$form_id.' #perm_block div.langs_tabs_block:last").find("input,select,textarea");
				jQuery(fields).each(function()
				{
					str = $(this).attr("name");
					$(this).attr("name", str.replace(/\$/g, "new_"+Tabi));
				});
				$("#'.$form_id.' #perm_block .langs_tabs ul").tabs("#'.$form_id.' #perm_block div.langs_tabs_block");
				var api = $("#'.$form_id.' #perm_block .langs_tabs ul").data("tabs");
				api.click(api.getTabs().length-1);
				Tabi++;
				datepicker_load();

				$("#'.$form_id.'").find(".'.$form_id.'_perm_alias").inputmask("Regex", {regex : "[-a-zA-Z0-9№_\/#]+"});
			}
		}
			$("#'.$form_id.'").on("keyup", ".'.$form_id.'_perm_alias", function()
			{
				var api = $("#'.$form_id.' #perm_block .langs_tabs ul").data("tabs");
				LI = api.getCurrentTab();
				$(LI).html($(this).val());
			});
			$("#'.$form_id.'").on("click", ".delete_price", function()
			{
				var api = $("#'.$form_id.' #perm_block .langs_tabs ul").data("tabs");
				if(api.getPanes().length > 1)
				{
					var t = api.getCurrentPane();
					var z = api.getCurrentTab();

					z.remove();
					t.remove();

					api.destroy();
					$("#'.$form_id.' #perm_block .langs_tabs ul").tabs("#'.$form_id.' #perm_block div.langs_tabs_block");
				}
				else
				{
					alert("Вы не можете удалить все правила доступа!");
				}
				return false;
			});
		';
    $CI->form->group('perm_block_top')->add_object(
        'js',
        $js_perm_tabs
    );
    
    $CI->form->add_block_to_tab('main_block', 'main_block');
    $CI->form->add_block_to_tab('desc_block', 'desc_block');
    $CI->form->add_block_to_tab('perm_block', 'perm_block_top');
    $CI->form->add_block_to_tab('perm_block', 'perm_block');

    
    $CI->form->render_form();
    
}


/*  End of file permissions_modules_helper.php  */