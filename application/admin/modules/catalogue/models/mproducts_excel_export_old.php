<?php
class Mproducts_excel_export extends AG_Model {

	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';

	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';
	const ID_PR_PRICE_DESC 	= 'id_m_c_products_price_description';

	const PR_IMG 			= 'm_c_products_images';
	const ID_PR_IMG 		= 'id_m_c_products_images';
	const PR_IMG_DESC 		= 'm_c_products_images_description';
	const ID_PR_IMG_DESC 	= 'id_m_c_products_images_description';

	const PR_ALB 			= 'm_c_products_albums';
	const ID_PR_ALB 		= 'id_m_c_products_albums';
	const PR_ALB_DESC 		= 'm_c_products_albums_description';
	const ID_PR_ALB_DESC 	= 'id_m_c_products_albums_description';

	const IMG_FOLDER = '/media/catalogue/products/';
	private $img_path = FALSE;

	function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}

	public function render_product_form()
	{
		$data['products'] = $this->render_product_grid();
		//$this->load->helper('excel_export/excel_export_helper');
		helper_excel_export_form($data);

	}

	public function render_product_grid()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('products_grid_excel', array('url' => setUrl('*/*/get_ajax_products_grid')));
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`" )
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
				"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		//->where("A.`".self::ID_PR."` IN (SELECT DISTINCT `".self::ID_PR."` FROM `".self::PR_COMM."` WHERE `".self::ID_USERS."` = '".$this->id_users."' && `new_comment` = 1)", NULL, FALSE);
		$this->load->helper('catalogue/products_excel_export_helper');
		helper_products_grid_build($this->grid);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));

		return $this->grid->render_grid(TRUE);
	}

	public function export()
	{
		$grid_data = $this->session->get_data('GRID_products_grid_excel');
		$search_params = $grid_data['search'];
		$sort = array();
		if(isset($grid_data['sort'])&& strlen($grid_data['sort'])>0)
		{
			$sort['sort'] = $grid_data['sort'];
			$sort['desc'] = $grid_data['desc'];
		}

		$add_images = $this->input->post('excel_images');
		$add_description = $this->input->post('excel_short_description');
		if(!$products = $this->select_products($search_params, $sort, $add_images, $add_description))
		{
			redirect(set_url('*'));
			return false;
		}
		
		if(!$this->generate_xls($products, $add_images, $add_description))
		{
			redirect(set_url('*'));
			return false;
		}
	}

	public function select_products($search_params, $sort, $add_images, $add_description)
	{
		$select_description = '';
			if($add_description == 1)
			{
				$select_description = ", B.`short_description`";
			}
		
		if(count($search_params) > 0)
		{
			$match = array();
			if(isset($search_params['sku']))
			{
				$match['sku'] = $search_params['sku'];
			}
			if(isset($search_params['name']))
			{
				$match['name'] = $search_params['name'];
			}
			
			$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`".$select_description )
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
			if (isset($match)&& count($match)>0)
			{
				$query->like($match);
			}
			if (isset($search_params['status']))
			{
				$query->where("A.`status`", $search_params['status']);
			}
			if (isset($search_params['in_stock']))
			{
				$query->where("A.`in_stock`", $search_params['in_stock']);
			}
			if(isset($search_params['create_date-date_from'])&& isset($search_params['create_date-date_to']))
			{
				$search_params['create_date-date_from'].=" 00:00:00";
				$search_params['create_date-date_to'].=" 23:59:59";
				$create_interval_q ="A.`create_date` BETWEEN '".date("Y-m-d H:i:s",strtotime($search_params['create_date-date_from']))."' AND '".date("Y-m-d H:i:s",strtotime($search_params['create_date-date_to']))."'";//$search_params['create_date-date_to']." 23:59:59";
				$query->where($create_interval_q);
			}
			else
			{
				if(isset($search_params['create_date-date_from']))
				{
					$search_params['create_date-date_from'].=" 00:00:00";
					$create_interval_q ="A.`create_date` >= '".date("Y-m-d H:i:s",strtotime($search_params['create_date-date_from']))."'";
					$query->where($create_interval_q);
				}
				if(isset($search_params['create_date-date_to']))
				{
					$search_params['create_date-date_to'].=" 23:59:59";
					$create_interval_q ="A.`create_date` <='".date("Y-m-d H:i:s",strtotime($search_params['create_date-date_to']))."'";
					$query->where($create_interval_q);
				}
			}

			if(isset($search_params['update_date-date_from'])&& isset($search_params['update_date-date_to']))
			{
				$search_params['update_date-date_from'].=" 00:00:00";
				$search_params['update_date-date_to'].=" 23:59:59";
				$update_interval_q ="A.`update_date` BETWEEN '".date("Y-m-d H:i:s",strtotime($search_params['update_date-date_from']))."' AND '".date("Y-m-d H:i:s",strtotime($search_params['update_date-date_to']))."'";//$search_params['create_date-date_to']." 23:59:59";
				$query->where($update_interval_q);
			}
			else
			{
				if(isset($search_params['update_date-date_from']))
				{
					$search_params['update_date-date_from'].=" 00:00:00";
					$update_interval_q ="A.`update_date` >= '".date("Y-m-d H:i:s",strtotime($search_params['update_date-date_from']))."'";
					$query->where($update_interval_q);
				}
				if(isset($search_params['update_date-date_to']))
				{
					$search_params['update_date-date_to'].=" 23:59:59";
					$update_interval_q ="A.`update_date` <='".date("Y-m-d H:i:s",strtotime($search_params['update_date-date_to']))."'";
					$query->where($update_interval_q);
				}
			}
		}
		else
		{
			$query = $this->db
				->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`".$select_description )
				->from("`".self::PR."` AS A")
				->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		}
		if(isset($sort) && count($sort)>0)
		{
			$query->order_by("A.`".$sort['sort']."`", $sort['desc']);
		}
		$products_array = $query->get()->result_array();

		if(count($products_array) == 0)
		{
			return false;
		}

		foreach($products_array as $val)
		{
			$products_id[] = intval($val['ID']);
		}

		//add product prices

		$products_array = $this->add_pr_prices($products_array, $products_id);// $prices_query->get()->result_array();

		if($add_images == 1)
		{
			$products_array = $this->add_pr_images($products_array, $products_id); //$img_query->get()->result_array();
		}
		return $products_array;
	}

	public function generate_xls($products_array, $add_images, $add_description)
	{
		require_once ("additional_libraries/Classes/PHPExcel.php" );
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("user")
			->setLastModifiedBy("username")
			->setTitle("Title")
			->setSubject("Название.")
			->setDescription("Описание")
			->setKeywords("php, all results")
			->setCategory("some category");

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="export_'.date("Y-m-d H:i:s").'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		//header('Cache-Control: max-age=1');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('A', 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('0000');

		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);// setAutoSize(true);
		if($add_description == 1)$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
		
		if($add_images == 1) $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', '№')
			->setCellValue('B1', 'Артикул')
			->setCellValue('C1', 'Название')
			->setCellValue('D1', 'Цена');
		if($add_description == 1)$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Описание');

		if($add_images == 1)$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Изображение');

		$row_count=2;
		$img_max_width = 0;
		foreach($products_array as $data){

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row_count, $row_count-1);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $row_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
																								 ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row_count, $data['sku']);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1, $row_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
																								 ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row_count, $data['name']);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row_count, ltrim(rtrim($data['prices']), "\n"));
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

			if($add_description == 1){
				$description =  strip_tags($data['short_description']);
				$description = trim($description);
				$description = html_entity_decode($description);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row_count, $description);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			}
			
			if($add_images == 1){
				if(isset($data['img']))
				{
					foreach($data['img'] as $img)
					{
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						$objDrawing->setName('pr_img');
						$objDrawing->setDescription('img');
						$objDrawing->setPath('.'.$img);
						$objDrawing->setCoordinates('F'.$row_count);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
						// 12 пунктов * (96/72) = 16 пикселей.
						$img_size = getimagesize('.'.$img);
						if($img_size[0] > $img_max_width)
						{
							$img_max_width = $img_size[0];
						}
						if($objPHPExcel->getActiveSheet()->getRowDimension($row_count)->getRowHeight() < ($img_size[1]/(96/72)))
						{
							$objPHPExcel->getActiveSheet()->getRowDimension($row_count)->setRowHeight($img_size[1]/(96/72) );
						}
						
					}
				}
			}
			$row_count++;
		}
		if($add_images == 1) $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(($img_max_width*9.140625)/64);

		$objPHPExcel->getActiveSheet()->setTitle('Результат поиска');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true;
	}

	public function add_pr_prices($products_array, $products_id)
	{
		$prices_query = $this->db
			->select("PRICE.`".self::ID_PR_PRICE."` AS ID_PRICE, PRICE.`".self::ID_PR."` AS ID, PRICE.`price`, PRICE_DESC.`name` AS price_name, PRICE_DESC.`description` AS price_description")
			->from("`".self::PR_PRICE."` AS PRICE")
			->join(	"`".self::PR_PRICE_DESC."` AS PRICE_DESC",
				"PRICE_DESC.`".self::ID_PR_PRICE."` = PRICE.`".self::ID_PR_PRICE."` && PRICE_DESC.`".self::ID_LANGS."` = '".$this->id_langs."'",
				"LEFT")
			->where_in("PRICE.`id_m_c_products`", $products_id)
			->order_by("PRICE.`".self::ID_PR_PRICE."`");

		$prices_array = $prices_query->get()->result_array();

		$this->load->model('catalogue/mcurrency');
		$currency_name =  $this->mcurrency->get_default_currency_name();

		foreach($products_array as $key => $prod)
		{ $products_array[$key]['prices'] = '';
			foreach($prices_array as $price)
			{
				if(intval($prod['ID']) == intval($price['ID']))
				{
					$products_array[$key]['prices'] .= $price['price_name'].' '.$price['price'].' '.$currency_name.' '.$price['price_description']."\n";
				}
			}
		}

		return $products_array;
	}

	public function add_pr_images($products_array, $products_id)
	{
		$img_query = $this->db->select("A.`image`, A.`".self::ID_PR."` AS ID, A.sort AS SORT, B.`".self::ID_PR_ALB."` AS ID_ALB")
			->from("`".self::PR_IMG."` AS A")
			->join(	"`".self::PR_ALB."` AS B",
				"B.`".self::ID_PR_ALB."` = A.`".self::ID_PR_ALB."`",
				"LEFT")
			->where_in("A.`".self::ID_PR."`", $products_id)
			->order_by("SORT");
		
		$img_array = $img_query->get()->result_array();

		foreach($products_array as $key => $prod)
		{
			$products_array[$key]['img'] = array();
			foreach($img_array as $img)
			{
				if(intval($prod['ID']) == intval($img['ID']))
				{
					if(!is_null($img['ID_ALB']))
					{
						if(file_exists('.'.$this->img_path.$img['ID'].'/'.$img['ID_ALB'].'/thumb_'.$img['image']))
						{
							$products_array[$key]['img'][] = $this->img_path.$img['ID'].'/'.$img['ID_ALB'].'/thumb_'.$img['image'];
							break;
						}
					}
					else
					{
						if(file_exists('.'.$this->img_path.$img['ID'].'/thumb_'.$img['image']))
						{
							$products_array[$key]['img'][] = $this->img_path.$img['ID'].'/thumb_'.$img['image'];
							break;
						}
					}
				}
			}
		}
		return $products_array;
	}

} 