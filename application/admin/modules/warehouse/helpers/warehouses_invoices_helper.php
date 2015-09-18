<?php
function helper_wh_invoices_grid_build(Grid $grid)
{
	$CI = & get_instance();
	$CI->load->model('warehouse/mwarehouses');
	$wh_array = $CI->mwarehouses->get_wh_to_select();

	$grid->add_column(
		 array(
			 'index'	=> 'wh_invoice_number',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '10%'
		 ), 'Номер');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_sale_number',
			 'type'		=> 'text',
			 'searchtable'=> 'B',
			 'tdwidth'	=> '10%',
			 'filter'	=> true
		 ), 'Номер продажи');
	$grid->add_column(
		 array(
			 'index'	=> 'total_qty',
			 'type'		=> 'text',
			 'tdwidth'	=> '7%'
		 ), 'К-во');
	$grid->add_column(
		 array(
			 'index'	=> 'total',
			 'type'		=> 'text'
		 ), 'Сумма');
	$grid->add_column(
		 array(
			 'index'		=> 'wh_alias',
			 'type'		 	=> 'select',
			 'searchtable'	=> 'LOG',
			 'searchname' 	=> 'id_wh',
			 'options'	 	=> array('' => '') + $wh_array,
			 'filter'		=> true,
			 'tdwidth'	=> '10%'
		 ), 'Склад');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_invoice_state',
			 'type'		=> 'text',
			 'tdwidth'	=> '10%',
		 ), 'Статус');
	$grid->add_column(
		 array(
			 'index'	=> 'create_date',
			 'searchtable'	=> 'A',
			 'type'		=> 'date',
			 'tdwidth'	=> '12%',
			 'filter'	=> true
		 ), 'Дата создания');
	$grid->add_column(
		 array(
			 'index'	=> 'update_date',
			 'searchtable'	=> 'A',
			 'type'		=> 'date',
			 'tdwidth'	=> '12%',
			 'filter'	=> true
		 ), 'Дата обновления');
	$grid->add_column(
		 array(
			 'index'	=> 'action',
			 'type'		=> 'action',
			 'tdwidth'	=> '10%',
			 'option_string' => 'align="center"',
			 'actions'	 => array(
				 /*array(
					 'type' 			=> 'link',
					 'html' 			=> '',
					 'href' 			=> set_url(array('*','*','view','wh_sh_id','$1')),
					 'href_values' 	=> array('ID'),
					 'options'		=> array('class'=>'icon_view', 'title'=>'Просмотр продукта')
				 )*/
			 )
		 ), 'Действия');
}
?>