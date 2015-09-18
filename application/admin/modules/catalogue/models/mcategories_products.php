<?php
class Mcategories_products extends AG_Model
{
	const CAT 				= 'm_c_categories';
	const ID_CAT 			= 'id_m_c_categories';
	const CAT_DESC 			= 'm_c_categories_description';
	const ID_CAT_DESC 		= 'id_m_c_categories_description';
	const CAT_LINK			= 'm_c_categories_link';

	const PR 	= 'm_c_products';
	const ID_PR = 'id_m_c_products';
	const PR_DESC = 'm_c_products_description';
	const PR_PRICE 	= 'm_c_products_price';
	const ID_PR_PRICE = 'id_m_c_products_price';

	const PR_CAT = 'm_c_productsNcategories';
	const ID_PR_CAT = 'id_m_c_productsNcategories';

	private $tree_array = array();

	public $id_categorie = FALSE;

	function __construct()
	{
		parent::__construct();
	}

	public function render_categories_products_grid()
	{
		$this->load->helper('aggrid_tree_helper');
		$Grid = new Aggrid_tree_Helper('catalogue_categories_products_grid');

		$Grid->db	->select("A.`".self::ID_CAT."` AS ID, A.`id_parent`, A.`level`, A.`sort` AS sort, A.`active`, A.`create_date`, A.`update_date`, B.`name`,
							(SELECT COUNT(*) FROM `".self::CAT."` WHERE `id_parent` = A.`".self::ID_CAT."`) AS PARENT_COUNT")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"left")
					->where("A.`".self::ID_USERS."`",$this->id_users)->order_by('sort');

		$this->load->helper('catalogue/categories_products_helper');

		$Grid = categories_products_grid_build($Grid);
		$Grid->createDataArray();
		$Grid	->updateGridValues('active',array('0'=>'Нет', '1'=>'Да'))
				->setGridValues("sort", "<a class='arrow_down' href='$1' title='Смена позиции: Опустить'></a><a class='arrow_up' href='$1' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$Grid->renderGrid();
	}

	public function render_actions($cat_id)
	{
		if(($cat_id = intval($cat_id))>0)
		{
			if($this->check_isset_categorie($cat_id))
			{
				$query = $this->db->select("B.`name`")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
							"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
							"LEFT")
					->where("A.`".self::ID_CAT."`", $cat_id)->limit(1);
				$result = $query->get()->row_array();

				$this->template->add_title(' - '.$result['name']);
				$this->template->add_navigation($result['name']);

				$this->load->model('catalogue/mproducts_settings');
				$data['settings'] = $this->mproducts_settings->get_products_settings();

				$data['products'] = $this->get_categories_products_grid($cat_id, $data['settings']);
				$data['products'] .= $this->load->view('catalogue/products/show_product_js', array('show_products_block_id' => 'categorie'.$cat_id.'_products_grid'), TRUE);

				helper_catalogue_mass_sale_categories_action_form_build($cat_id, $data);
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function get_categories_products_grid($cat_id, $settings)
	{
		$this->load->library('grid');
		$this->grid->_init_grid('categorie'.$cat_id.'_products_grid', array('limit' => 1000, 'url' => set_url('*/*/ajax_categories_products_grid/cat_id/'.$cat_id)), TRUE);

		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`new`, C.`sort`, C.`id_m_c_productsNcategories`")
			->from("`".self::PR_CAT."` AS C USE INDEX (`".self::ID_CAT."`)")
			->join("`".self::PR."` AS A",
					"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("C.`".self::ID_CAT."`", $cat_id)->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("C.`new`+0", "DESC");
		if($settings['products_sort_type'] == 1) $this->grid->db->order_by("C.`sort`+0", "DESC"); else $this->grid->db->order_by("C.`sort`+0");

		$this->load->helper('catalogue/categories_products_helper');
		categories_products_incat_grid_build($this->grid, $cat_id);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('status',array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('new',array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data_using_string("sort", "<input type = 'text' style = 'width:90%' name = 'product_sort[$1]' value = '$2'>", array('$1' => 'id_m_c_productsNcategories', '$2' => 'sort'));
		return $this->grid->render_grid(TRUE);
	}

	public function save($cat_id)
	{
		$POST = $this->input->post('product_sort');
		if($POST && count($POST) > 0)
		{
			$this->db->trans_start();
			foreach($POST as $key => $ms)
			{
				$this->sql_add_data(array('sort' => intval($ms)))->sql_using_user()->sql_save(self::PR_CAT, $key);
			}
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function check_isset_categorie($cat_id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")
				->from("`".self::CAT."`")
				->where("`".self::ID_USERS."`",$this->id_users)
				->where("`".self::ID_CAT."`", $cat_id);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function render_export($cat_id)
	{
		if(($cat_id = intval($cat_id))>0)
		{
			if($this->check_isset_categorie($cat_id))
			{
				$query = $this->db->select("B.`name`")
					->from("`".self::CAT."` AS A")
					->join(	"`".self::CAT_DESC."` AS B",
						"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"LEFT")
					->where("A.`".self::ID_CAT."`", $cat_id)->limit(1);
				$result = $query->get()->row_array();

				$this->template->add_title(' - '.$result['name'].' Експорт');
				$this->template->add_navigation($result['name'], set_url('*/*/action/cat_id/'.$cat_id));
				$this->template->add_navigation('Експорт');

				$this->load->model('catalogue/mproducts_settings');
				$data['settings'] = $this->mproducts_settings->get_products_settings();

				$data['products'] = $this->get_categories_products_grid($cat_id, $data['settings']);

				helper_catalogue_mass_sale_categories_action_export_form_build($cat_id, $data);
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function get_category_name($cat_id)
	{
		if($this->check_isset_categorie($cat_id))
		{
			$query = $this->db->select("B.`name`")
				->from("`".self::CAT."` AS A")
				->join(	"`".self::CAT_DESC."` AS B",
					"B.`".self::ID_CAT."` = A.`".self::ID_CAT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
				->where("A.`".self::ID_CAT."`", $cat_id)->limit(1);
			$result = $query->get()->row_array();
			return $result['name'];
		}
		else
		{
			return false;
		}
	}

	public function export_cat($cat_id)
	{
		$add_images = $this->input->post('excel_images');
		$add_description = $this->input->post('excel_short_description');
		$select_description = '';
		if($add_description == 1)
		{
			$select_description = ", B.`short_description`";
		}
		//echo var_dump($this->input->post());//$add_images;
		$cat_name = $this->get_category_name($cat_id);
		$query = $this->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`new`, C.`sort`, C.`id_m_c_productsNcategories`".$select_description)
			->from("`".self::PR_CAT."` AS C USE INDEX (`".self::ID_CAT."`)")
			->join("`".self::PR."` AS A",
				"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
				"INNER")
			->join(	"`".self::PR_DESC."` AS B",
				"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
				"LEFT")
			->where("C.`".self::ID_CAT."`", $cat_id)
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->order_by("C.`new`", "DESC");
		$products_array = $query->get()->result_array();

		foreach($products_array as $val)
		{
			$products_id[] = intval($val['ID']);
		}

		$this->load->model('catalogue/mproducts_excel_export');

		if($add_images == 1)
		{
			$products_array = $this->mproducts_excel_export->add_pr_images($products_array, $products_id);
		}

		$products_array = $this->mproducts_excel_export->add_pr_prices($products_array, $products_id);

        ob_end_clean();
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
		header('Content-Disposition: attachment;filename="'.$cat_name.'_category_export_'.date("Y-m-d H:i:s").'.xls"');
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
						$objPHPExcel->getActiveSheet()->getRowDimension($row_count)->setRowHeight($img_size[1]/(96/72) );
					}
				}
			}
			$row_count++;
		}
		if($add_images == 1) $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(($img_max_width*9.140625)/64);

		$title = $cat_name;
		if(strlen($title) > 30) $title = "Продукты категории";

		$objPHPExcel->getActiveSheet()->setTitle($title);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true;
	}

}
?>