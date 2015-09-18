<?php
require_once "./application/frontend_2.0/modules/news/controllers/news.php";
class Users_news extends News
{	
	public function last_news()
	{
		$this->load->model('mnews');
		$this->mnews->_init(1422);
		$data = $this->mnews->get_last_news();
		$this->template->add_view_to_template('last_news_block', 'news/footer_news', $data);
	}
	public function last_statti()
	{
		$this->load->model('mnews');
		$this->mnews->_init(380);
		$data = $this->mnews->get_last_statti();
		$this->template->add_view_to_template('last_statti_block', 'news/footer_statti', $data);
	}
}
?>