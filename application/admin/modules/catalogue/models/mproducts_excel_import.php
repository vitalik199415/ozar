<?php
class Mproducts_excel_import extends AG_Model {

	const XLS_FOLDER = '/xls_files/';

	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';

	private $xls_path = FALSE;

	function __construct()
	{
		parent::__construct();
		$this->xls_path = 'users/'.ID_USERS.self::XLS_FOLDER;
	}

	public function upload_file()
	{
		$data['files_list'] = scandir($this->xls_path);
		unset($data['files_list'][0]);
		unset($data['files_list'][1]);
		$this->load->helper('catalogue/products_excel_import_helper');
		upload_form($data);
		return true;
	}

	public function upload_xls()
	{
		$config['upload_path'] = BASE_PATH.$this->xls_path;
		$config['allowed_types'] = 'xls';
		$config['max_size']	= '2048';
		$config['encrypt_name'] = FALSE;
		$config['file_name'] = 'products.xls';
		$config['overwrite'] = TRUE;

		$this->load->library('upload', $config);
		if($this->upload->do_upload())
		{
			return TRUE;
		}
		else
		{
			$this->upload->display_errors();
			return FALSE;
		}
	}

	public function parse_file($file)
	{
		$uploadpath = $this->xls_path.$file;

		include_once  ("additional_libraries/Classes/Phpexcel.php" );
		//include_once  ("additional_libraries/Spreadsheet_excel_reader.php" );

		$xls_data = array();

		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(false);

		$objPHPExcel = $objReader->load($uploadpath);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$i = 0;

		$highestRow = $objWorksheet->getHighestRow(); // e.g. 10
		$highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
		$objReader->setReadDataOnly(false);

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
		for ($row = 4; $row <= $highestRow; ++$row) {
		//	echo '<tr>' . "\n";
			$xls_data[$row]['num']   = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
			$xls_data[$row]['short']    = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
			$xls_data[$row]['count']    = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();

			$xls_data[$row]['sku']    =	$objWorksheet->getCellByColumnAndRow(3, $row)->getFormattedValue();
			$xls_data[$row]['size']    = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();

			for ($col = 0; $col <= $highestColumnIndex; ++$col) {

				//echo '<td>' . $objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue() . '</td>' . "\n";
			}


	//echo '</tr>' . "\n";
		}
//echo '</table>' . "\n";

		$group_data = array(); // array('count' => '', 'sku' => '', 'size_info' => array('size' => '', 'size_count' => '') );
		for($i =4; $i <= count($xls_data); $i++)
		{
			if($xls_data[$i]['sku'] == null) continue;
			if(intval($xls_data[$i]['count']) < 0) $xls_data[$i]['count'] = 0;
			$group_data[$i]['count'] =  intval($xls_data[$i]['count']);
			$group_data[$i]['sku'] =  $xls_data[$i]['sku'];
			$group_data[$i]['name'] =  $xls_data[$i]['short'];
			$group_data[$i]['size_info'][] = array('size' => $xls_data[$i]['size'], 'size_count' => intval($xls_data[$i]['count']));

			for($j = $i+1; $j<count($xls_data); $j++)
			{
				if($xls_data[$i]['sku'] == $xls_data[$j]['sku'])
				{
					if(intval($xls_data[$j]['count']) < 0) $xls_data[$j]['count'] = 0;
					$group_data[$i]['count'] += intval($xls_data[$j]['count']);
					$group_data[$i]['size_info'][] = array('size' => $xls_data[$j]['size'], 'size_count' => intval($xls_data[$j]['count']));
					$xls_data[$j]['sku'] = null;
				}
			}
		}
		return $group_data;
	}

	public function import($file)
	{
		$products_info = $this->parse_file($file);

		foreach($products_info as $val)
		{
			$sku_array[] = $val['sku'];
		}

		$query = $this->db->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`in_stock`, A.`new`, A.`bestseller`, A.`sale`, B.`full_description` ")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
				"B.`".self::ID_PR."` = A.`".self::ID_PR."` ",
				"LEFT")
			->where_in("A.`sku`", $sku_array)
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$result = $query->get()->result_array();

		for($i = 0; $i<count($result); $i++)
		{
			if(strstr( $result[$i]['full_description'], "</div>") != FALSE) $result[$i]['full_description'] = strstr( $result[$i]['full_description'], "</div>");
			foreach($products_info as $key => $val)
			{
				
				if($result[$i]['sku'] == $val['sku'])
				{
					if($val['count'] > 0)
					{
						$result[$i]['in_stock'] = 1;

						$size_table = "";
						foreach($val['size_info'] as $sizes)
						{
							$size_table .=" <tr>
									<td>".$sizes['size']."</td><td>".$sizes['size_count']."</td>
								  </tr>";
						}
						$result[$i]['full_description'] = "<div id='size'><table id='size_table'>
								  						<tr><td>Размер</td><td>Количество</td></tr>".$size_table."
														</table> </div>".$result[$i]['full_description'];
					}
					else
					{
						$result[$i]['full_description'] = "<div id='size'>Нет в наличии. </div>".$result[$i]['full_description'];
						$result[$i]['in_stock'] = 0;
					}

					unset($products_info[$key]);
				}

			}
		}

		foreach($result as $val)
		{
			$pr_update_array[$val['ID']] = array('product' => array('in_stock' => $val['in_stock'], 'sku' => $val['sku'],
				'status' => 0, 'new' => $val['new'], 'bestseller' => $val['bestseller'], 'sale' => $val['sale']),
				'product_desc' => array($this->id_langs => array( 'full_description' => $val['full_description'] )),
				'new' => $val['new'],
				'product_prices' => array('new_price' => array('real_qty' => '1', 'min_qty' => '1', 'visible_rules' => '1',
				'show_attributes' => '1', 'special_price' => '',
				'desc' => array($this->id_langs => array('name' => '', 'description' => '')) ))
			);
		}

		foreach($products_info as $key => $val)
		{
			if($val['count'] > 0)
			{
				$val['in_stock'] = 1;

				$size_table = "";
				foreach($val['size_info'] as $sizes)
				{
					$size_table .=" <tr>
									<td>".$sizes['size']."</td><td>".$sizes['size_count']."</td>
								  </tr>";
				}
				$val['full_description'] = "<div id='size'><table id='size_table'>
								  						<tr><td>Размер</td><td>Количество</td></tr>".$size_table."
														</table> </div>";
			}
			else
			{
				$val['full_description'] = "<div id='size'>Нет в наличии. </div>";
				$val['in_stock'] = 0;
			}

			$pr_add_array[$key] = array('product' => array('in_stock' => $val['in_stock'], 'sku' => $val['sku'],
				'status' => 0, 'new' => '1', 'bestseller' => '0', 'sale' =>'0'),
				'product_desc' => array($this->id_langs => array( 'name' => $val['name'], 'full_description' => $val['full_description'] )),
				'new' => '1',
				'product_prices' => array('new_price' => array('real_qty' => '1', 'min_qty' => '1', 'visible_rules' => '1',
					'show_attributes' => '1', 'special_price' => '',
					'desc' => array($this->id_langs => array('name' => '', 'description' => '')) )));
		}

		$this->load->model('catalogue/mproducts_save');
		if(isset($pr_update_array))
		{
			foreach($pr_update_array as $id_pr => $pr_data)
			{
				$_POST = $pr_data;
				if(!$this->mproducts_save->save_pr($id_pr))
				{
					$this->massages->add_error_massage("Товар с артикулом ".$pr_data[$id_pr]['product']['sku']." не обновлен!");
				}
			}
		}
		if(isset($pr_add_array))
		{
			foreach($pr_add_array as $new_pr_data)
			{
				$_POST = $new_pr_data;
				if(!$this->mproducts_save->save_pr())
				{
					$this->massages->add_error_massage("Товар с артикулом ".$new_pr_data['product']['sku']." не добавлен!");
				}
			}
		}
	
		return true;
	}

	public function delete_file($file)
	{
		if(is_file($this->xls_path.$file))
		{
			return unlink($this->xls_path.$file);
		}
		else
		{
			return false;
		}
	}

	public function other_import()
	{
		$uploadpath = $this->xls_path.'products.xls';

		include_once  ("additional_libraries/Classes/PHPExcel.php" );

		$xls_data = array();

		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(false);

		$objPHPExcel = $objReader->load($uploadpath);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$i = 0;

		$highestRow = $objWorksheet->getHighestRow(); // e.g. 10
		$highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
		$objReader->setReadDataOnly(false);

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
		//echo '<table>' . "\n";
		for ($row = 2; $row <= $highestRow; ++$row)
		{
			$objWorksheet->getStyle('A'.$row)->getNumberFormat()->setFormatCode('000');
				//echo '<tr>' . "\n";
			$xls_data[$row]['sku']   = $objWorksheet->getCellByColumnAndRow(0, $row)->getFormattedValue();
			$xls_data[$row]['status']    = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
			$xls_data[$row]['in_stock']    = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
			$xls_data[$row]['bestseller']    =	$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
			$xls_data[$row]['sale']    = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
			$xls_data[$row]['new']    = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
			$xls_data[$row]['name']    = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
			$xls_data[$row]['short_description']    = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
			$xls_data[$row]['full_description']    = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
			$xls_data[$row]['price']    = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
			$xls_data[$row]['special_price']    = $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
			$xls_data[$row]['special_price_from']    = $objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
			$xls_data[$row]['special_price_to']    = $objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
			if(strlen($objWorksheet->getCellByColumnAndRow(13, $row)->getValue())>0) $xls_data[$row]['img_url']    = explode(',',$objWorksheet->getCellByColumnAndRow(13, $row)->getValue());
			if(isset($xls_data[$row]['img_url']) )
			{
				foreach($xls_data[$row]['img_url'] as $key => $img)
				{
					$xls_data[$row]['img_url'][$key] = $img;
				}
			}
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				//echo '<td>' . $objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue() . '</td>' . "\n";
			}
			//echo '</tr>' . "\n";
		}


		//echo '</table>' . "\n";

		foreach($xls_data as $key => $val)
		{
			if($val['full_description'] == '') $val['full_description'] = ' ';
			if($val['short_description'] == '') $val['short_description'] = ' ';
			$pr_add_array[$key] = array('product' => array('in_stock' => $val['in_stock'], 'sku' => $val['sku'],
				'status' => $val['status'], 'new' => $val['new'], 'bestseller' => $val['bestseller'], 'sale' =>$val['sale']),
				'product_desc' => array($this->id_langs => array( 'name' => $val['name'], 'short_description' => $val['short_description'], 'full_description' => $val['full_description'] )),
				'new' => '1',
				'product_prices' => array('new_price' => array('real_qty' => '1', 'min_qty' => '1', 'visible_rules' => '0',
					'show_attributes' => '1',
					'price' => $val['price'],
					'show_in_short' => 1, 'show_in_detail' => 1,
					'special_price' => $val['special_price'],
					'special_price_from' => $val['special_price_from'],
					'special_price_to' => $val['special_price_to'],
					'desc' => array($this->id_langs => array('name' => '', 'description' => '')) )),
				);
			if(isset($val['img_url'])) $pr_add_array[$key]['images'] = $val['img_url'];
		}

		$this->load->model('catalogue/mproducts_save');

		if(isset($pr_add_array))
		{
			foreach($pr_add_array as $new_pr_data)
			{
				$_POST = $new_pr_data;
				if($ID = $this->mproducts_save->save_pr())
				{
					if(isset($new_pr_data['images']) && is_array($new_pr_data['images']))
						foreach($new_pr_data['images'] as $image)
						{
							$filename = substr($image, strrpos($image, '/') + 1);

							$dir = './users/'.$this->id_users.'/media/catalogue/products/'.$ID.'/';
							$in = fopen($image, "rb");
							if(!file_exists($dir)) mkdir($dir);
							$out = fopen($dir.$filename, "wb");
							while ($chunk = fread($in, 8192))
							{
								fwrite($out, $chunk, 8192);
							}
							fclose($in);
							fclose($out);
							$thumb_image = substr_replace ( $image , 'thumb_'.$filename, strrpos($image, '/') + 1);
							$in = fopen($thumb_image, "rb");
							if(!file_exists($dir)) mkdir($dir);
							$out = fopen($dir.'thumb_'.$filename, "wb");
							while ($chunk = fread($in, 8192))
							{
								fwrite($out, $chunk, 8192);
							}
							fclose($in);
							fclose($out);
							$this->mproducts_save->save_pr_img($ID, array('file_name' => $filename));
						}
				}
				else
				{
					$this->massages->add_error_massage("Товар с артикулом ".$new_pr_data['product']['sku']." не добавлен!");
				}
			}
		}
		return true;
	}

}
/*
  * SELECT A.`sku`, A.`status`, A.`in_stock`, A.`bestseller`, A.`sale`, A.`new`, B.`name`, B.`short_description`, B.`full_description`, C.`price`, C.`special_price`, C.`special_price_from`, C.`special_price_to`, CONCAT('http://demodex-complex.com.ua/users/11055/media/catalogue/products/',
A.`id_m_c_products`, '/', IMG.`image`) AS `img_url`
FROM `m_c_products` AS A
LEFT JOIN `m_c_products_description` AS B ON B.`id_m_c_products` = A.`id_m_c_products` && B.`id_langs` = 1

INNER JOIN `m_c_products_price` AS C ON C.`id_m_c_products` = A.`id_m_c_products` && C.`id_m_c_products_price` = (SELECT `id_m_c_products_price` FROM `m_c_products_price` WHERE `id_m_c_products` = A.`id_m_c_products` ORDER BY `id_m_c_products_price` LIMIT 1)

INNER JOIN `m_c_products_images` AS IMG ON IMG.`id_m_c_products` = A.`id_m_c_products` && IMG.`id_m_c_products_images` = (SELECT `id_m_c_products_images` FROM `m_c_products_images` WHERE `id_m_c_products` = A.`id_m_c_products` ORDER BY `id_m_c_products_images` LIMIT 1)

WHERE 1
  */