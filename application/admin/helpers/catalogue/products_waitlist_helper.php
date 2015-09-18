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
            'index'		 => 'count',
            'tdwidth'	 => '8%',
            'type'		 => 'text',
            'filter'	 => true
        ),'Запросы');

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
                    'type'          => 'link',
                    'html'          => '',
                    'href'          => set_url(array('*','products','view','id','$1')),
                    'href_values'   => array('ID'),
                    'options'       => array('class'=>'icon_view products_view', 'title'=>'Просмотр продукта')
                ),

                array(
                    'type' 			=> 'link',
                    'html' 			=> '',
                    'href' 			=> set_url('*/*/view_waitlist_customers/id/$1'),
                    'href_values' 	=> array('ID'),
                    'options'		=> array('class'=>'icon_arrow_r', 'title' => 'Список покупателей')
                )
            )
        ), 'Actions');
}

function waitlist_grid_build($Grid, $id)
{
    $Grid->add_button('Назад', set_url('*/*'),
        array(
            'rel' => 'add',
            'class' => 'addButton'
        ));

    $Grid->add_column(
        array
        (
            'index'		 => 'name',
            'type'		 => 'text',
//            'tdwidth'	 => '20%',
			'filter'	 => true
        ), 'Имя');

    $Grid->add_column(
        array
            (
                'index'		 => 'email',
				'type'		 => 'text',
//                'tdwidth'	 => '24%',
				'filter'	 => true
            ),'Email');

    $Grid->add_column(
        array
            (
                'index'		 => 'action',
                'type'		 => 'action',
                'tdwidth'	 => '10%',
                'option_string' => 'align="center"',
                'actions'	 => array()
            ),'Действия'
    );
    return $Grid;
}
?>