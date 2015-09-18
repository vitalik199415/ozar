<?php
class Mdiscounts extends AG_Model
{
    const DISC              = 'm_c_discounts';
    const ID_DISC           = 'id_m_c_discounts';

    function __construct()
    {
        parent::__construct();
    }

    public function render_discounts_grid()
    {
        $this->load->library("grid");
        $this->grid->_init_grid('discounts_grid', array());

        $this->grid->db
                ->select("A.`".self::ID_DISC."` AS ID, A.`type_discounts`, A.`sum_from`, A.`sum_to`, A.`discount_sum`, A.`discount_percent`,' %', A.`active`")
                ->from("`". self :: DISC ."` AS A")
                ->where("A.`". self :: ID_USERS ."`", $this->id_users);

        $this->load->helper("discounts");
        helper_discount_grid_build($this->grid);
        $this->grid->create_grid_data();
        $this->grid->update_grid_data("type_discounts", array('0' => 'Сумма', '1'=>'Процент'));
        $this->grid->update_grid_data("active", array('0' => 'Нет', '1'=>'Да'));
        $this->grid->render_grid();
    }

    public function add()
    {
        $this->load->model('catalogue/mcurrency');
        $data['data_default_currency'] = $this->mcurrency->get_default_currency_name();

        $this->load->helper("discounts");
        helper_discount_form_build($data);
    }

    public function edit($id)
    {
        $result = $this->db->select('*')
                ->from("`".self::DISC."` AS A")
                ->where("A.`".self::ID_DISC."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1)
                ->get()->row_array();
        $data = array();

        $this->load->model('catalogue/mcurrency');
        $data['data_default_currency'] = $this->mcurrency->get_default_currency_name();

        if (count($result) > 0)
        {
            $data['main'] = $result;
            $this->load->helper('discounts');
            helper_discount_form_build($data, '/id/'.$id);
            return TRUE;
        }
        return FALSE;
    }

    public function save($id = FALSE)
    {
        if($this->input->post('main'))
        {
            if($id)
            {
                $POST = $this->input->post('main');

                if($POST['type_discounts'] == 0)
                {
                    $POST['discount_percent'] = NULL;
                }
                else
                {
                    $POST['discount_sum'] = NULL;
                }
				if(floatval($POST['sum_to']) <= 0) $POST['sum_to'] = NULL;
                if(!$this->save_disc_validate()) return FALSE;

                $this->db->trans_start();
                $this->sql_add_data($POST)->sql_using_user()->sql_save(self::DISC, $id);
                $this->db->trans_complete();
                if($this->db->trans_status())
                {
                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
                return FALSE;
            }
            else
            {
                $POST = $this->input->post('main');

                if($POST['type_discounts'] == 0)
                {
                    unset($POST['discount_percent']);
                }
                else
                {
                    unset($POST['discount_sum']);
                }
				if(floatval($POST['sum_to']) <= 0) $POST['sum_to'] = NULL;
                if(!$this->save_disc_validate()) return FALSE;

                $this->db->trans_start();
                $ID = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::DISC);
                $this->db->trans_complete();
                if($this->db->trans_status())
                {
                    return $ID;
                }
                else
                {
                    return FALSE;
                }
                return FALSE;
            }
        }
        return FALSE;
    }

    public function delete($id)
    {
        if(is_array($id))
        {
            $this->db->where_in(self::ID_DISC, $id)->where("`".self::ID_USERS."`", $this->id_users);
            $this->db->delete(self::DISC);
            return TRUE;
        }

        $result = $this->db ->select('count(*) AS COUNT')
                            ->from('`'.self::DISC.'`')
                            ->where('`'.self::ID_DISC.'`', $id)->where('`'.self::ID_USERS.'`', $this->id_users)->get()->row_array();
        if($result['COUNT'] > 0)
        {
            $this->db->where(self::ID_DISC, $id)->where(self::ID_USERS, $this->id_users);
            if($this->db->delete(self::DISC))
            {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }

    public function activate($id, $active = 1)
    {
        if(is_array($id))
        {
            $data = array('active' => $active);
            foreach($id as $ms)
            {
                $this->sql_add_data($data)->sql_using_user()->sql_save(self::DISC, $ms);
            }
            return TRUE;
        }
        return FALSE;
    }

    public function save_disc_validate()
    {
        $this->load->library("form_validation");
        $this->form_validation->set_rules('main[sum_from]', 'Сумма от', 'required');
        //$this->form_validation->set_rules('main[sum_to]', 'Сумма до', 'required');

        if(!$this->form_validation->run())
        {
            $this->messages->add_error_message(validation_errors());
            return FALSE;
        }

        return TRUE;
    }
}

?>