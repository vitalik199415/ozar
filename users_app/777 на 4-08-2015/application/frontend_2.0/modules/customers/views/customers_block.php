<div id="open_customers">
	<a href="#" class="icon">
		Мой аккаунт
	</a>
</div>
<div class="slide_block_customers">
	    <?php
		if($this->session->userdata('customer_id'))
			{
			?>
		    <div class="block">
				<a href="#" id="customer_office"><?=$this->session->userdata(array('CUSTOMER','name'));?>!</a>
				<a href="<?=$this->router->build_url('customers_methods_lang', array('method' => 'logout', 'lang' => $this->mlangs->lang_code));?>" ><?=$this->lang->line('login_logout')?></a>
		    </div>
			<?
			}
			else
			{
			?>
		    	<div class="block">
					<a href="#" id="customer_login"><i class="icon-lock"></i> <?=$this->lang->line('login_enter')?></a><br>
					<a href="#" id="customer_registration"><i class="icon-group"></i> <?=$this->lang->line('login_registration')?></a>
		        </div>
			<?php
			}
		?>
</div>






<?=$this->template->get_temlate_view('customers_init');?>
<script type="text/javascript">
		$('.slide_block_customers').hide();	
		$('#open_customers a').toggle(
			function(){
				$('.slide_block_customers').show(300);
				$(this).toggleClass('active'); 
			},
			function(){
				$('.slide_block_customers').hide(300);
				$(this).toggleClass('active'); 
			}
		);

		$(document).click(function(event){
			if($(event.target).closest(".slide_block_customers").length) return;
			$('.slide_block_customers').hide(300);
			$('#open_customers a').removeClass('active'); 
			event.stopPropagation();
		});
	</script>