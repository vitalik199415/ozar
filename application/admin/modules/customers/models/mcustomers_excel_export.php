<?php
class Mcustomers_excel_export extends AG_Model {

    const CT 				= 'm_u_customers';
    const ID_CT 			= 'id_m_u_customers';

    const CT_ADDR 			= 'm_u_customers_address';
    const ID_CT_ADDR 		= 'id_m_u_customers_address';

    const CT_TYPE 		= 'm_u_types';
    const ID_CT_TYPE 	= 'id_m_u_types';
    const CT_TYPE_DESC 		= 'm_u_types_description';
    const ID_CT_TYPE_DESC 	= 'id_m_u_types_description';

    const CT_N_TYPE		= 'm_u_customers_types';

    function __construct()
    {
        parent::__construct();
    }

    public function render_customers_form()
    {
        $this->load->model('customers/mcustomers_types');
        $data['customers_groups'] = $this->mcustomers_types->get_customers_types();

        $this->load->helper('customers/customers_excel_export_helper');
        helper_excel_export_form($data);

    }

    public function export()
    {

        $type_info = $this->input->post('excel_export_type');           // full or short(email) information export
        $type_customers = $this->input->post('excel_customers_type');   // all customers or selected group
        if(!$customers = $this->select_customers($type_info, $type_customers))
        {
            redirect(set_url('*'));
            return false;
        }

        if(!$this->generate_xls($customers, $type_info, $type_customers))
        {
            redirect(set_url('*'));
            return false;
        }
    }

    public function select_customers($type_info, $type_customers)
    {
        $all_info_select_query = '';
        if($type_info == 1) {
            $all_info_select_query = ', B.`name`, B.`country`, B.`city`, B.`address`, B.`telephone`';
        }

        if($type_customers == 1) {
            $cust_group = $this->input->post('customers_group');
            $group_arr = array();

            foreach($cust_group as $id => $ms) {
                $group_arr[] = $id;
            }

            $this->db->select('A.`email`'.$all_info_select_query)
                ->from("`".self::CT."` as A")
                ->join("`".self::CT_N_TYPE."` as C", "A.`".self::ID_CT."` = C.`".self::ID_CT."`", "INNER")
                ->join("`".self::CT_ADDR."` as B", "A.`".self::ID_CT."` = B.`".self::ID_CT."`", "INNER")
                ->where('B.`type`', 'B')
                ->where_in('C.`'.self::ID_CT_TYPE.'`', $group_arr)
                ->where('`id_users`', $this->id_users);
        } else {
            $this->db->select('A.`email`'.$all_info_select_query)
                ->from("`".self::CT."` as A")
                ->join("`".self::CT_ADDR."` as B", "A.`".self::ID_CT."` = B.`".self::ID_CT."`", "INNER")
                ->where('B.`type`', 'B')
                ->where('`id_users`', $this->id_users);
        }

        $customers_array = $this->db->get()->result_array();

        return $customers_array;
    }

    public function generate_xls($customers_array, $type_info)
    {
        ob_end_clean();
        require_once ("additional_libraries/Classes/PHPExcel.php" );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setTitle('Покупатели магазина');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);

        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow('A', 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

        if($type_info == 1) {
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A1', '№')
            ->setCellValue('B1', 'Email');

        if($type_info == 1) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('C1', 'Фамилия, Имя')
                ->setCellValue('D1', 'Страна')
                ->setCellValue('E1', 'Город')
                ->setCellValue('F1', 'Адресс')
                ->setCellValue('G1', 'Телефон');
        }

        $row_count=2;

        foreach($customers_array as $data){

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row_count, $row_count-1);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, $row_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row_count, $data['email']);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1, $row_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            if($type_info == 1) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row_count, $data['name']);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row_count, $data['country']);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row_count, $data['city']);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row_count, $data['address']);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row_count, $data['telephone']);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6, $row_count)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            }
            $row_count++;
        }



        $objPHPExcel->getProperties()->setCreator("user")
            ->setLastModifiedBy("username")
            ->setTitle("Title")
            ->setSubject("Название.")
            ->setDescription("Описание")
            ->setKeywords("php, all results")
            ->setCategory("some category");

        /*// Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_'.date("Y-m-d H:i:s").'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');*/

        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( 'Content-Disposition: attachment; filename="export_'.date("Y-m-d H:i:s").'.xls' );

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        //return true;
    }

}