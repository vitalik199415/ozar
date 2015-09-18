<?php
class Langs extends AG_Controller
{
	public function index()
	{
		$this->load->model('langs/mlangs');
		$active_langs = $this->mlangs->get_site_languages();
		$this->template->add_view_to_template('langs_block', 'langs/langs', array('langs' => $active_langs));
	}
}
?>