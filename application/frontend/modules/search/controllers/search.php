<?php
class Search extends AG_Controller
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->mlangs->load_language_file('modules/search');
		$default_search_text = $this->lang->line('search_input_text');
		$search_text = '';
		if($search_string = $this->variables->get_url_vars('search_string'))
		{
			$search_text = rawurldecode($search_string);
		}
		$this->template->add_js('jquery.gbc_search', 'modules_js/search');
		$this->template->add_view_to_template('search_block', 'search/search_block', array('search_text' => $search_text));
		$this->template->add_view_to_template('search_init', 'search/search_js_init', array('default_search_text' => $default_search_text, 'search_text' => $search_text));
	}
	
	public function start_search()
	{
		if($search_string = $this->input->post('search_string'))
		{
			$hash = preg_replace('|[^\w\s№]|ui',' ',trim($search_string));
			$hash = preg_replace('|^(-+)|ui','',trim($hash));
			$hash = preg_replace('|(-+)$|ui','',trim($hash));
			redirect($this->router->build_url('search_data_lang', array('search_string' => $hash, 'lang' => $this->mlangs->lang_code)) ,301);
		}
	}
	
	public function search_data()
	{
		if($search_string = $this->variables->get_url_vars('search_string'))
		{
			echo modules::run('catalogue/catalogue_search/search');
		}
	}
}