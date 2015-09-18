<div class="last_reviews_block">
	<div class="last_reviews_title">
		<span>Отзывы</span> клиентов
		<a href="/reviews">Все отзывы</a>
	</div>
	
		<?php
		if(isset($reviews) && count($reviews)>0)
		{
		if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
			foreach($reviews as $ms)
			{
				?>
				<div class="block">
					<div class="last_review_name">
						<?=$ms['name']?>
					</div>
					<div class="last_review_content">
						<div class="last_review_description">
							<?=$ms['review']?>
						</div>
					</div>
				</div>
				<?
			}
		}
		?>
	
</div>
<?php
if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
?>