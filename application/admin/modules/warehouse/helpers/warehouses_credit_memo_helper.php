<?php
function helper_wh_credit_memo_grid_build(Grid $grid)
{
	$CI = & get_instance();
	$CI->load->model('warehouse/mwarehouses');
	$wh_array = $CI->mwarehouses->get_wh_to_select();

	$grid->add_column(
		 array(
			 'index'	=> 'wh_credit_memo_number',
			 'searchtable'=> 'CM',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '10%'
		 ), 'Номер');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_sale_number',
			 'searchtable'=> 'SALE',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '10%'
		 ), 'Номер продажи');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_invoice_number',
			 'searchtable'=> 'INV',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '10%'
		 ), 'Номер инвойса');
	$grid->add_column(
		 array(
			 'index'	=> 'wh_shipping_number',
			 'searchtable'=> 'SHP',
			 'type'		=> 'text',
			 'filter'	=> true,
			 'tdwidth'	=> '10%'
		 ), 'Номер отправки');
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
			 'searchtable'	=> 'WH',
			 'searchname' 	=> 'id_wh',
			 'options'	 	=> array('' => '') + $wh_array,
			 'filter'		=> true,
			 'tdwidth'	=> '10%'
		 ), 'Склад');
	$grid->add_column(
		 array(
			 'index'	=> 'create_date',
			 'searchtable'	=> 'CM',
			 'type'		=> 'date',
			 'tdwidth'	=> '12%',
			 'filter'	=> true
		 ), 'Дата создания');
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