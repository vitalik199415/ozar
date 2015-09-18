<?php

class Musers_modules extends AG_Model {
    
    const   P_MODULES           = 'modules';
    const   ID_P_MODULES        = 'id_modules';
    const   P_MODULES_DESC      = 'modules_description';
    const   ID_P_MODULES_DESC   = 'id_modules_description';
    const   PERM_TYPES          = 'modules_types';
    const   ID_PERM_TYPES       = 'id_modules_types';
    const   PERM_TYPES_DESC     = 'modules_types_description';
    const   ID_PERM_TYPES_DESC  = 'id_modules_types_description';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function render_users_modules_grid() {
        $this->load->library('grid');
        $this->grid->_init_grid('users_modules_grid', array());

        $this->grid->db
                ->select("PM.`".self::ID_P_MODULES."` AS ID, PM.`alias` AS aliace, PMD.`name`, PMD.`description`")
                ->from("`".self::P_MODULES."` as PM")
                ->join("`".self::P_MODULES_DESC."` as PMD", "PM.`".self::ID_P_MODULES."`=PMD.`".self::ID_P_MODULES."`", "INNER")
                ->where("PMD.`id_langs`", 1)->order_by('PM.`sort`', 'asc');

        $this->load->helper('users_modules');
        helper_users_modules_grid_build($this->grid);
        $this->grid->create_grid_data();
//        $this->grid->update_grid_data('discount_type', array('0' => 'Сумма', '1' => 'Процент'));
//        $this->grid->update_grid_data('consider_promotional_items', array('0' => 'Нет', '1' => 'Да'));
//        $this->grid->update_grid_data('is_start', array('0' => 'Нет', '1' => 'Да'));
        $this->grid->render_grid();
    }
    
    public function add() {
        $this->db
             ->select("`id_langs`, `name`")
             ->from("`langs`")->where('`active`', 1);
        $langs = $this->db->get()->result_array();
        
        $data['langs'] = array();
        foreach($langs as $lang) {
            $data['langs'][$lang['id_langs']] = $lang['name'];
        }
        
        $this->load->helper('users_modules');
        helper_users_modules_form_build($data);
    }
    
    public function edit($id){
        $main = $this->db->select('*')
                           ->from('`'.self::P_MODULES.'` AS A')
                           ->where('A.`'.self::ID_P_MODULES.'`',$id)->limit(1)
                           ->get()->row_array();

        if(count($main)>0)
        {
            $data = array();

            $data['main'] = $main;                                                          // основная информация о купоне

            $users = $this->db->select(self::ID_PERM_TYPES.' as ID, alias')
                             ->from(self::PERM_TYPES)
                             ->where(self::ID_P_MODULES, $id)
                             ->get()->result_array();
            foreach($users as $perm) {
                $data['perm_blocks'][$perm['ID']] = $perm['alias'];
                $data['perm'][$perm['ID']]['alias'] = $perm['alias'];
                $perm_desc = $this->db->select('name, description, id_langs')
                    ->from(self::PERM_TYPES_DESC)
                    ->where(self::ID_PERM_TYPES, $perm['ID'])
                    ->get()->result_array();
                foreach($perm_desc as $desc) {
                    $data['perm'][$perm['ID']]['desc'][$desc['id_langs']]['name'] = $desc['name'];
                    $data['perm'][$perm['ID']]['desc'][$desc['id_langs']]['description'] = $desc['description'];
                }
            }

            
            $this->db
                 ->select("`id_langs`, `name`")
                 ->from("`langs`")->where('`active`', 1);
            $langs = $this->db->get()->result_array();

            $data['langs'] = array();
            foreach($langs as $lang) {
                $data['langs'][$lang['id_langs']] = $lang['name'];
            }
            
            $description = $this->db->select('*')
                              ->from('`'.self::P_MODULES_DESC.'`')
                              ->where('`'.self::ID_P_MODULES.'`', $id)->get()->result_array();

            foreach($description as $desc)
            {
                $data['desc'][$desc['id_langs']]['name'] = $desc['name'];
                $data['desc'][$desc['id_langs']]['description'] = $desc['description'];
            }

            $this->load->helper('users_modules');
            helper_users_modules_form_build($data, '/id/'.$id);
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
                $main = $this->input->post('main');
                $desc = $this->input->post('desc');
                $perm = $this->input->post('perm');

                if (!$this->save_validate()) return FALSE;

                $this->db->trans_start();

                $this->db->where('`'.self::ID_P_MODULES.'`', $id)->update('`'.self::P_MODULES.'`', $main);

                $this->db
                     ->select("`id_langs`, `name`")
                     ->from("`langs`")->where('`active`', 1);
                $langs = $this->db->get()->result_array();

                foreach ($langs as $lang) {
                    if (isset($desc[$lang['id_langs']])) {
                        $description = array(
                            'name' => $desc[$lang['id_langs']]['name'],
                            'description' => $desc[$lang['id_langs']]['description']
                        );
                        $this->db->where('`'.self::ID_P_MODULES.'`', $id)->where('`id_langs`', $lang['id_langs'])
                                 ->update('`'.self::P_MODULES_DESC.'`', $description);
                    }
                }

                $this->save_type_desc($perm, $langs, $id);

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return TRUE;
            }
            else
            {
                $main = $this->input->post('main');
                $desc = $this->input->post('desc');
                $perm = $this->input->post('perm');

                if (!$this->save_validate()) return FALSE;

                $this->db->trans_start();
                $last_id = $this->sql_add_data($main)->sql_save(self::P_MODULES);
                $sort['sort'] = $last_id;
                $this->db->where('`'.self::ID_P_MODULES.'`', $last_id)->update('`'.self::P_MODULES.'`', $sort);

                $this->db
                     ->select("`id_langs`, `name`")
                     ->from("`langs`")->where('`active`', 1);
                $langs = $this->db->get()->result_array();

                foreach ($langs as $lang) {
                    if (isset($desc[$lang['id_langs']])) {
                        $description = array(
                            self::ID_P_MODULES => $last_id,
                            'name' => $desc[$lang['id_langs']]['name'],
                            'description' => $desc[$lang['id_langs']]['description'],
                            'id_langs' => $lang['id_langs']
                        );
                        $this->sql_add_data($description)->sql_save(self::P_MODULES_DESC);
                    }
                }

                foreach($perm as $val) {
                    $perm = array(
                        self::ID_P_MODULES => $last_id,
                        'alias' => $val['alias'],
                        'sort'  => $last_id
                    );
                    $type_id = $this->sql_add_data($perm)->sql_save(self::PERM_TYPES);

                    foreach ($langs as $lang) {
                        if (isset($val['desc'][$lang['id_langs']])) {
                            $perm_desc = array(
                                self::ID_PERM_TYPES => $type_id,
                                'name' => $val['desc'][$lang['id_langs']]['name'],
                                'description' => $val['desc'][$lang['id_langs']]['description'],
                                'id_langs' => $lang['id_langs']
                            );
                            $this->sql_add_data($perm_desc)->sql_save(self::PERM_TYPES.'_description');
                        }
                    }
                }

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return $last_id;
            }
        }
    }
    
    public function delete($id)
    {
        if(is_array($id))
        {
            $this->db->where_in(self::ID_P_MODULES, $id)->delete(self::P_MODULES);
            //$this->db->where_in(self::ID_P_MODULES, $id)->delete(self::P_MODULES_DESC);
            return TRUE;
        }

        $result = $this->db ->select('count(*) AS COUNT')
                            ->from('`'.self::P_MODULES.'`')
                            ->where('`'.self::ID_P_MODULES.'`', $id)->get()->row_array();
        if($result['COUNT'] > 0)
        {
            $this->db->where(self::ID_P_MODULES, $id);
            if($this->db->delete(self::P_MODULES))
            {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }

    public function save_type_desc($perm = array(), $langs, $ID)
    {
        $result = $this->db->where('`'.self::ID_P_MODULES.'`', $ID)->get(self::PERM_TYPES)->result_array();

        foreach($result as $perm_id)
        {
            $data[$perm_id['alias']] = $perm_id[self::ID_PERM_TYPES];
        }

        if(count($perm)) {
            foreach ($perm as $val) {
                if (isset($data[$val['alias']])) {
                    $perms = array(
                        'alias' => $val['alias'],
                        'sort'  => $data[$val['alias']]
                    );
                    $this->db->where('`'.self::ID_P_MODULES.'`', $data[$val['alias']])->update(self::PERM_TYPES, $perms);

                    foreach ($langs as $lang) {
                        if (isset($val['desc'][$lang['id_langs']])) {
                            $perm_desc = array(
                                'name' => $val['desc'][$lang['id_langs']]['name'],
                                'description' => $val['desc'][$lang['id_langs']]['description'],
                                'id_langs' => $lang['id_langs']
                            );
                            $this->db->where(self::ID_PERM_TYPES, $data[$val['alias']])->where('`id_langs`', $lang['id_langs'])->update(self::PERM_TYPES_DESC, $perm_desc);
                        }
                    }
                    unset($data[$val['alias']]);
                } else {
                    $perm = array(
                        self::ID_P_MODULES => $ID,
                        'alias' => $val['alias'],
                        'sort'  => $ID
                    );
                    $type_id = $this->sql_add_data($perm)->sql_save(self::PERM_TYPES);

                    foreach ($langs as $lang) {
                        if (isset($val['desc'][$lang['id_langs']])) {
                            $perm_desc = array(
                                self::ID_PERM_TYPES => $type_id,
                                'name' => $val['desc'][$lang['id_langs']]['name'],
                                'description' => $val['desc'][$lang['id_langs']]['description'],
                                'id_langs' => $lang['id_langs']
                            );
                            $this->sql_add_data($perm_desc)->sql_save(self::PERM_TYPES.'_description');
                        }
                    }
                    unset($data[$val['alias']]);
                }
            }
        }

        foreach($perm as $val) {

        }

        $del_data = FALSE;
        if(count($data))
        {
            foreach($data as $id => $val)
            {
                $del_data[] = $val;
            }
        }

        if($del_data)
        {
            $this->db->where_in(self::ID_PERM_TYPES, $del_data);
            $this->db->delete(self::PERM_TYPES);
        }
    }
    
    public function save_validate()
    {
        $this->load->library("form_validation");

        $this->form_validation->set_rules('main[alias]', 'Алиас модуля', 'required');

        if(!$this->form_validation->run())
        {
            $this->messages->add_error_message(validation_errors());
            return FALSE;
        }

        return TRUE;
    }
    
}

/*  End of file musers_modules.php  */