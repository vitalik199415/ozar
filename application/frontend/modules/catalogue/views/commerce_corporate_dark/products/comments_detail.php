<?php
foreach($PRD_array['comments_array']['comments'] as $ms)
{
?>
<div class="base_block">		
	<div class="base_top">
		<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
	</div>
	<div class="base_center">
		<div class="base_center_left"></div><div class="base_center_right"></div>
		<div class="base_center_repeat">
			<div class="block">
			
<div class="comment_block">
	<div class="comment_name"><span><?=$ms['name']?></span></div>
	<div class="comment_date"><span><?=$ms['create_date']?></span></div>
	<div class="comment_message"><?=$ms['message']?></div>
	<?php
	if($ms['is_answer'] == 1)
	{
		?>
		<div class="answer_block">
			<div class="comment_name"><span><?=$ms['admin_name']?></span></div>
			<div class="comment_message"><?=$ms['answer']?></div>
		</div>
		<?php
	}
	?>
</div>

			</div>
		</div>
	</div>	
	<div class="base_bot">
		<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
	</div>
</div>
<?
}
?><div id="product_comments_pagination"><?=$this->load->view('catalogue/products/comments_detail_pagination', $PRD_array['comments_array']['pages'], TRUE);?></div>