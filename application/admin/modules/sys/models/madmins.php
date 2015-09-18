<?php

class Madmins extends AG_Model {
    const M_ADMIN = 'm_administrators';
    const ID_M_ADMIN = 'id_m_administrators';
    const MODULES = 'modules';
    const ID_MODULES = 'id_modules';
    const MODULES_DESC = 'modules_description';
    const ID_MODULES_DESC = 'id_modules_description';
    const S_MODULES = 'm_permissions_modules';
    const ID_S_MODULES = 'id_m_permissions_modules';
    const PERM_S_TYPES = 'm_permissions_types';
    const ID_PERM_S_TYPES = 'id_m_permissions_types';
    const PERM_U_TYPES = 'modules_types';
    const ID_PERM_U_TYPES = 'id_modules_types';
    const A_S_MODULES = 'm_administrator_permissions_modules';
    const ID_A_S_MODULES = 'id_m_administrator_permissions_modules';
    const A_U_MODULES = 'm_administrator_permissions_users_modules';
    const ID_A_U_MODULES = 'id_m_administrator_permissions_users_modules';
    const A_CAT_PERM = 'm_administrator_categories_permission';
    const ID_CAT = 'id_m_c_categories';

    function __construct() {
        parent::__construct();
    }

    public function render_admins_grid() {
        $this->load->library('grid');
        $this->grid->_init_grid('permissions_modules_grid', array());

        $this->grid->db->select(self::ID_M_ADMIN." as ID, name, login, email, note, active")->from(self::M_ADMIN);

        $this->load->helper('admins');
        helper_admins_grid_build($this->grid);
        $this->grid->create_grid_data();
        $this->grid->render_grid();
    }

    public function add() {
        if($this->session->get_data('primary') == 1) {
            $data = array();

            $this->load->model('catalogue/mcategories');
            $data['categories'] = $this->mcategories->get_categories_tree();
            $data = array_merge($data, $this->get_module());
            $data = array_merge($data, $this->get_module_perm());

            $this->load->helper('admins');
            helper_admins_form_build($data);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit($id){
        if($this->session->get_data('primary') == 1) {
            $main = $this->db->select(self::ID_M_ADMIN." as ID, superadmin, name, login, password, email, note, active")
                ->from(self::M_ADMIN)->where(self::ID_M_ADMIN, $id)->where('id_users', $this->id_users)->limit(1)
                ->get()->row_array();

            if(count($main)>0)
            {
                $data = array();
                $data['main'] = $main;

                $this->load->model('catalogue/mcategories');
                $data['categories'] = $this->mcategories->get_categories_tree();
                $data = array_merge($data, $this->get_module());
                $data = array_merge($data, $this->get_module_perm());

                if($main['superadmin'] != 1) {
                    $system_modules = $this->db->select(self::ID_S_MODULES." as ID, ".self::ID_PERM_S_TYPES." as ID_P")
                        ->from(self::A_S_MODULES)->where(self::ID_M_ADMIN, $id)
                        ->get()->result_array();

                    foreach ($system_modules as $sys_module) {
                        $data['system_modules'][$sys_module['ID']] =intval($sys_module['ID']);
                        $data['system_perm'][$sys_module['ID']][$sys_module['ID_P']] = $sys_module['ID_P'];
                    }

                    $user_modules = $this->db->select(self::ID_MODULES." as ID,".self::ID_PERM_U_TYPES)
                        ->from(self::A_U_MODULES)->where(self::ID_M_ADMIN, $id)
                        ->get()->result_array();

                    foreach ($user_modules as $user_module) {
                        $data['user_modules'][$user_module['ID']] = $user_module['ID'];
                        $data['user_perm'][$user_module['ID']][$user_module[self::ID_PERM_U_TYPES]] = $user_module[self::ID_PERM_U_TYPES];
                    }

                    $cat_perm = $this->db->select(self::ID_CAT." as ID")
                        ->from(self::A_CAT_PERM)->where(self::ID_M_ADMIN, $id)
                        ->get()->result_array();

                    foreach ($cat_perm as $cat) {
                        $data['cat_perm'][$cat['ID']] = $cat['ID'];
                    }
                }
                $this->load->helper('admins');
                helper_admins_form_build($data, '/id/'.$id);
                return TRUE;
            }
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
                $main['id_users'] = $this->id_users;
                $user_m['modules'] = $this->input->post('user_modules');
                $user_m['perm'] = $this->input->post('user_perm');
                $system['modules'] = $this->input->post('system_modules');
                $system['perm'] = $this->input->post('system_perm');
                $categories = $this->input->post('cat_perm');

                if (!$this->save_validate()) return FALSE;

                $this->db->trans_start();

                $this->db->where('`'.self::ID_M_ADMIN.'`', $id)->update('`'.self::M_ADMIN.'`', $main);

                if($main['superadmin'] != 1) {
                    $this->save_system_modules($system, $id, TRUE);
                    $this->save_user_modules($user_m, $id, TRUE);
                    $this->save_cat_perm($categories, $id, TRUE);
                }

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return TRUE;
            }
            else
            {
                $main = $this->input->post('main');
                $main['id_users'] = $this->id_users;
                $user_m['modules'] = $this->input->post('user_modules');
                $user_m['perm'] = $this->input->post('user_perm');
                $system['modules'] = $this->input->post('system_modules');
                $system['perm'] = $this->input->post('system_perm');
                $categories = $this->input->post('cat_perm');

                if (!$this->save_validate()) return FALSE;

                $last_id = $this->sql_add_data($main)->sql_save(self::M_ADMIN);
                $this->db->trans_start();

                if($main['superadmin'] != 1) {
                    $this->save_system_modules($system, $last_id);
                    $this->save_user_modules($user_m, $last_id);
                    $this->save_cat_perm($categories, $last_id);
                }

                $this->db->trans_complete();
                if (!$this->db->trans_status()) return FALSE;

                return $last_id;
            }
        }
    }

    public function save_system_modules($system, $id_admin, $edit = FALSE)
    {
        if($edit) {
            if (is_array($system['modules']) AND count($system['modules'])) {
                $result = $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->get(self::A_S_MODULES)->result_array();

                $temp_perm = array();
                $temp_modules = array();
                foreach ($result as $module_id) {
                    $temp_modules[$module_id[self::ID_S_MODULES]] = $module_id[self::ID_S_MODULES];
                    if($module_id[self::ID_PERM_S_TYPES] != NULL)$temp_perm[$module_id[self::ID_PERM_S_TYPES]] = $module_id[self::ID_PERM_S_TYPES];
                }

                foreach ($system['modules'] as $key => $vall) {
                    if(!isset($temp_modules[$key])) {
                        $group = array(self::ID_S_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_S_TYPES => NULL);
                        $this->db->insert(self::A_S_MODULES, $group);
                    }
                    if (isset($system['perm'][$key]) AND count($system['perm'][$key]) > 0) {
                        foreach ($system['perm'][$key] as $k => $v) {
                            if(!isset($temp_perm[$k])) {
                                $group = array(self::ID_S_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_S_TYPES => $k);
                                $this->db->insert(self::A_S_MODULES, $group);
                            }
                            if (isset($temp_perm[$k])) unset($temp_perm[$k]);
                        }
                    }
                    if (isset($temp_modules[$key])) unset($temp_modules[$key]);
                }

                $del_perm_arr = array();
                $del_modules_arr = array();
                foreach ($temp_perm as $val) {
                    $del_perm_arr[] = $val;
                }

                foreach ($temp_modules as $val) {
                    $del_modules_arr[] = $val;
                }

                if (count($del_perm_arr) > 0) {
                    $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->where_in(self::ID_PERM_S_TYPES, $del_perm_arr);
                    $this->db->delete(self::A_S_MODULES);
                }

                if (count($del_modules_arr) > 0) {
                    $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->where_in(self::ID_S_MODULES, $del_modules_arr);
                    $this->db->delete(self::A_S_MODULES);
                }

            } else {
                $this->db->where(self::ID_M_ADMIN, $id_admin)->delete(self::A_S_MODULES);
            }
        } else {
            foreach ($system['modules'] as $key => $vall) {
                $group = array(self::ID_S_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_S_TYPES => NULL);
                $this->db->insert(self::A_S_MODULES, $group);

                if (isset($system['perm'][$key]) AND count($system['perm'][$key]) > 0) {
                    foreach ($system['perm'][$key] as $k => $v) {
                        $group = array(self::ID_S_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_S_TYPES => $k);
                        $this->db->insert(self::A_S_MODULES, $group);
                    }
                }
            }
        }

    }

    public function save_user_modules($users, $id_admin, $edit = FALSE)
    {
        if($edit) {
            if (is_array($users['modules']) AND count($users['modules'])) {
                $result = $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->get(self::A_U_MODULES)->result_array();

                $temp_perm = array();
                $temp_modules = array();
                foreach ($result as $module_id) {
                    $temp_modules[$module_id[self::ID_MODULES]] = $module_id[self::ID_MODULES];
                    if($module_id[self::ID_PERM_U_TYPES] != NULL)$temp_perm[$module_id[self::ID_PERM_U_TYPES]] = $module_id[self::ID_PERM_U_TYPES];
                }

                foreach ($users['modules'] as $key => $vall) {
                    if(!isset($temp_modules[$key])) {
                        $group = array(self::ID_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_U_TYPES => NULL);
                        $this->db->insert(self::A_U_MODULES, $group);
                    }
                    if (isset($users['perm'][$key]) AND count($users['perm'][$key]) > 0) {
                        foreach ($users['perm'][$key] as $k => $v) {
                            if(!isset($temp_perm[$k])) {
                                $group = array(self::ID_MODULES => $vall['id'], self::ID_M_ADMIN => $id_admin, self::ID_PERM_U_TYPES => $k);
                                $this->db->insert(self::A_U_MODULES, $group);
                            }
                            if (isset($temp_perm[$k])) unset($temp_perm[$k]);
                        }
                    }
                    if (isset($temp_modules[$key])) unset($temp_modules[$key]);
                }

                $del_perm_arr = array();
                $del_modules_arr = array();
                foreach ($temp_perm as $val) {
                    $del_perm_arr[] = $val;
                }

                foreach ($temp_modules as $val) {
                    $del_modules_arr[] = $val;
                }

                if (count($del_perm_arr) > 0) {
                    $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->where_in(self::ID_PERM_U_TYPES, $del_perm_arr);
                    $this->db->delete(self::A_U_MODULES);
                }

                if (count($del_modules_arr) > 0) {
                    $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->where_in(self::ID_MODULES, $del_modules_arr);
                    $this->db->delete(self::A_U_MODULES);
                }

            } else {
                $this->db->where(self::ID_M_ADMIN, $id_admin)->delete(self::A_U_MODULES);
            }
        } else {
            foreach ($users['modules'] as $key => $vall) {
                $group = array(self::ID_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_U_TYPES => NULL);
                $this->db->insert(self::A_U_MODULES, $group);

                if (isset($system['perm'][$key]) AND count($system['perm'][$key]) > 0) {
                    foreach ($system['perm'][$key] as $k => $v) {
                        $group = array(self::ID_MODULES => $key, self::ID_M_ADMIN => $id_admin, self::ID_PERM_U_TYPES => $k);
                        $this->db->insert(self::A_U_MODULES, $group);
                    }
                }
            }
        }
    }

    public function save_cat_perm($cat_arr, $id_admin, $edit = FALSE)
    {
        if($edit) {
            if (is_array($cat_arr) AND count($cat_arr)) {
                $result = $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->get(self::A_CAT_PERM)->result_array();

                $temp_perm = array();
                foreach ($result as $ms) {
                    $temp_perm[$ms[self::ID_CAT]] = $ms[self::ID_CAT];
                }

                foreach ($cat_arr as $key => $vall) {
                    if(!isset($temp_perm[$key])) {
                        $data = array(self::ID_CAT => $key, self::ID_M_ADMIN => $id_admin);
                        $this->db->insert(self::A_CAT_PERM, $data);
                    } else {
                        unset($temp_perm[$key]);
                    }
                }

                $del_perm_arr = array();
                foreach ($temp_perm as $val) {
                    $del_perm_arr[] = $val;
                }

                if (count($del_perm_arr) > 0) {
                    $this->db->where('`' . self::ID_M_ADMIN . '`', $id_admin)->where_in(self::ID_CAT, $del_perm_arr);
                    $this->db->delete(self::A_CAT_PERM);
                }

            } else {
                $this->db->where(self::ID_M_ADMIN, $id_admin)->delete(self::A_CAT_PERM);
            }
        } else {
            foreach ($cat_arr as $key => $vall) {
                $group = array(self::ID_MODULES => $key, self::ID_M_ADMIN => $id_admin);
                $this->db->insert(self::A_CAT_PERM, $group);
            }
        }
    }

    public function delete($id)
    {
        if(is_array($id))
        {
            $this->db->where_in(self::ID_M_ADMIN, $id)->delete(self::M_ADMIN);
            //$this->db->where_in(self::ID_P_MODULES, $id)->delete(self::P_MODULES_DESC);
            return TRUE;
        }

        $result = $this->db ->select('count(*) AS COUNT')
            ->from('`'.self::M_ADMIN.'`')
            ->where('`'.self::ID_M_ADMIN.'`', $id)->get()->row_array();
        if($result['COUNT'] > 0)
        {
            $this->db->where(self::ID_M_ADMIN, $id);
            if($this->db->delete(self::M_ADMIN))
            {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }

    public function get_module() {
        $rang = $this->session->get_data('rang');
        $data = array();

        $a_modules = $this->db->select('PM.`id_m_permissions_modules` as ID,PM.`module`, PM.`rang`, PMD.`name`, PMD.`description`')
            ->from('m_permissions_modules as PM')
            ->join('m_permissions_modules_description as PMD', 'PM.`id_m_permissions_modules`=PMD.`id_m_permissions_modules`', 'INNER')
            ->where('PMD.`id_langs`', 1)->get()->result_array();

        $data['a_modules'] = array();
        foreach ($a_modules as $key => $vall) {
            if ($vall['rang'] <= $rang) {
                $data['a_modules'][$vall['ID']]['name'] = $vall['module'].'     ['.$vall['name'].']';
                $data['a_modules'][$vall['ID']]['desc'] = $vall['description'];
            }
        }

        $u_modules = $this->db->select('UM.`id_modules` as ID, UM.`alias`, MD.`name`, MD.`description`')
            ->from('modules as UM')
            ->join('modules_description as MD', 'UM.`id_modules`=MD.`id_modules`', 'INNER')
            ->where('MD.`id_langs`', 1)->get()->result_array();

        foreach ($u_modules as $key => $vall) {
            $data['u_modules'][$vall['ID']]['name'] = $vall['alias'].'     ['.$vall['name'].']';
            $data['u_modules'][$vall['ID']]['desc'] = $vall['description'];
        }
        return $data;
    }

    public function get_module_perm() {
        $data = array();

        $a_types = $this->db->select('PT.`id_m_permissions_types` as ID, PT.`id_m_permissions_modules` as ID_M, PT.`alias`, PTD.`name`, PTD.`description`')
            ->from('m_permissions_types as PT')
            ->join('m_permissions_types_description as PTD','PT.`id_m_permissions_types`=PTD.`id_m_permissions_types`', 'INNER')
            ->where('PTD.`id_langs`', 1)->get()->result_array();

        foreach ($a_types as $key => $vall) {
                $data['a_types'][$vall['ID_M']][$vall['ID']]['name'] = $vall['alias'].'     ['.$vall['name'].']';
                $data['a_types'][$vall['ID_M']][$vall['ID']]['desc'] = $vall['description'];
        }

        $u_types = $this->db->select('MT.`id_modules_types` as ID, MT.`id_modules` as ID_M, MT.`alias`, MTD.`name`, MTD.`description`')
            ->from('modules_types as MT')
            ->join('modules_types_description as MTD', 'MT.`id_modules_types`=MTD.`id_modules_types`', 'INNER')
            ->where('MTD.`id_langs`', 1)->get()->result_array();

        foreach ($u_types as $key => $vall) {
                $data['u_types'][$vall['ID_M']][$vall['ID']]['name'] = $vall['alias'].'     ['.$vall['name'].']';
                $data['u_types'][$vall['ID_M']][$vall['ID']]['desc'] = $vall['description'];
        }
        return $data;
    }

    public function get_cat_perm() {
        $res = $this->db->select(self::ID_CAT)
                        ->from(self::A_CAT_PERM)
                        ->where(self::ID_M_ADMIN, $this->id_admin)
                        ->get()->result_array();
        if(count($res) > 0) {
            return FALSE;
        }

        $cat = array();
        foreach($res as $ms) {
            $cat[] = $ms[self::ID_CAT];
        }
        return $cat;
    }

    public function save_validate()
    {
        $this->load->library("form_validation");

        $this->form_validation->set_rules('main[login]', 'Логин', 'required');
        $this->form_validation->set_rules('main[password]', 'Пароль', 'required');
        $this->form_validation->set_rules('main[email]', 'Email', 'required');
        $this->form_validation->set_rules('main[name]', 'Имя', 'required');

        if(!$this->form_validation->run())
        {
            $this->messages->add_error_message(validation_errors());
            return FALSE;
        }

        return TRUE;
    }
}