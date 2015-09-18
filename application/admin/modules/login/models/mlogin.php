 <?php
class Mlogin extends AG_Model
{
	const USERS = 'users';
    const M_ADMIN = 'm_administrators';
	
	function __construct()
		{
			parent::__construct();
		}
		
	public function isAutorize()
		{
			if($this->session->get_data('id_users') && $this->session->get_data('id_users')>0)
			{
				return true;
			}
			return false;		
		}

	public function autorize($data)
		{
			$state = 2;
			$query = $this->db->select("MA.*, U.`rang`")->from(self::M_ADMIN." as MA")
                          ->join(self::USERS.' as U', 'U.`id_users`=MA.`id_users`', 'INNER')
						  ->where('MA.`login`',$data['login'])->where('MA.`password`',$data['password']);
			$result = $this->db->get()->result_array();
			if (count($result) == 1) {
				if($result[0]['active'] == 1) {
					$sys_perm = $this->db->select('PM.`module` as pm_alias')
						->from('`m_administrator_permissions_modules` as APM')
						->join('`m_permissions_modules` as PM', 'APM.`id_m_permissions_modules`=PM.`id_m_permissions_modules`', 'INNER')
						->where('APM.`id_m_administrators`', $result[0]['id_m_administrators'])->get()->result_array();
					$sys = array();
					foreach($sys_perm as $perm) {
						$sys[$perm['pm_alias']] = $perm['pm_alias'];
					}
					$this->session->set_data('system_perm', $sys);

					$sys = array();
					$sys_perm = $this->db->select('PM.`module` as pm_alias, PT.`alias` as pt_alias')
								->from('`m_administrator_permissions_modules` as APM')
								->join('`m_permissions_modules` as PM', 'APM.`id_m_permissions_modules`=PM.`id_m_permissions_modules`', 'INNER')
								->join('`m_permissions_types` as PT', 'APM.`id_m_permissions_types`=PT.`id_m_permissions_types`', 'INNER')
								->where('APM.`id_m_administrators`', $result[0]['id_m_administrators'])->get()->result_array();

					foreach($sys_perm as $perm) {
						$sys[$perm['pm_alias']][$perm['pt_alias']] =$perm['pt_alias'];
					}
					$this->session->set_data('system_actions', $sys);

					$user_perm = $this->db->select('PM.`alias` as pm_alias, PT.`alias` as pt_alias')
						->from('`m_administrator_permissions_users_modules` as APM')
						->join('`modules` as PM', 'APM.`id_modules`=PM.`id_modules`', 'INNER')
						->join('`modules_types` as PT', 'APM.`id_modules_types`=PT.`id_modules_types`', 'INNER')
						->where('APM.`id_m_administrators`', $result[0]['id_m_administrators'])->get()->result_array();
					$user = array();
					foreach($user_perm as $perm) {
						$user[$perm['pm_alias']][$perm['pt_alias']] =$perm['pt_alias'];
					}
					$this->session->set_data('user_perm', $user);

					$this->session->set_data('id_users', $result[0]['id_users']);
					$this->session->set_data('id_admin', $result[0]['id_m_administrators']);
					$this->session->set_data('rang', $result[0]['rang']);

					if ($result[0]['primary_administrator'] == 1) {
						$this->session->set_data('primary', $result[0]['primary_administrator']);
					}
					if ($result[0]['superadmin'] == 1) {
						$this->session->set_data('super', $result[0]['superadmin']);
					}
				} else $state = 1;
			} else $state = 0;
			return $state;
		}		
}
?>