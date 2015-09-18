<?php
require_once "./application/frontend_2.0/modules/reviews/controllers/reviews.php";
class Users_reviews extends Reviews
{	
	public function last_reviews()
	{
		$this->load->model('reviews/mreviews');
		$this->mreviews->_init(1420);
		$data = $this->mreviews->get_last_reviews();
		$this->template->add_view_to_template('last_reviews_block', 'reviews/last_reviews', $data + array('settings' => $this->settings));
	}
}
?>