<?php

class Permissions extends AG_Controller {

    public function check_perm($alias = FALSE, $type_module) {
        if($alias) {
            $aliases = explode('/', $alias);

            if($type_module == 'system') {
                $this->session->get_data('system_perm');
            }

        }

        return FALSE;
    }

}