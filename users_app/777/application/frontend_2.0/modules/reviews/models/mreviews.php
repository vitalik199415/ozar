<?php
require_once "./application/frontend_2.0/modules/reviews/models/mreviews.php";
class Users_mreviews extends Mreviews
{	
	 public function get_last_reviews($limit = 4)
	 {
	  $query = $this->get_reviews_collection_query();
	  $query = $query->limit($limit);
	  return $this->get_reviews_collection_array($query->get()->result_array());
	 }
}
?>