<?php
	require_once "./application/frontend/modules/news/models/mnews.php";
		class Users_mnews extends Mnews
		{ 
			 public function get_last_news($limit = 2)
				 {
				  $query = $this->get_news_collection_query();
				  $query = $query->limit($limit);
				  return $this->get_news_collection_array($query->get()->result_array());
				 }
			public function get_last_statti($limit = 2)
				 {
				  $query = $this->get_news_collection_query();
				  $query = $query->limit($limit);
				  return $this->get_news_collection_array($query->get()->result_array());
				 }

		}
?>