<?php
if(isset($menu))
{
	?>
    <div class="footer_menu_block">
			<?php
            $c = count($menu);
            $i = 1;
            foreach($menu as $ms)
            {
                if ($ms['url'] == 'ozar-company' || $ms['url'] == 'discount' || $ms['url'] == 'paiment-and-delivery' || $ms['url'] == 'shipping-and-payments-in-Russia' || $ms['url'] == 'shipping-and-payment-to-Ukraine' || $ms['url'] == 'shipping-and-payments-in-Belarus' || $ms['url'] == 'dostavka-krum' || $ms['url'] == 'shipping-and-payment-to-the-CIS-countries' || $ms['url'] == 'help')
                {
                ?>
                    
                <?
                }
                else
                {
                ?>
                    <a <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?> ><?=$ms['name']?></a>
                <?
                }
                $i++;
            }
            ?>
    </div>
	<?
}
?>
