<?php
class Site_description extends AG_Controller
{
	public function index()
	{
		$this->load->model('site_settings/msite_settings');
		$view_array = $this->msite_settings->get_site_description();
		$this->template->add_view_to_template('site_description_block', 'site_settings/site_description', $view_array);
	}
}
?>