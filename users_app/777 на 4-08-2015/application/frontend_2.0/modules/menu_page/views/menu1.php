<?php
if(isset($menu))
{
	?>
    <div class="menu_block">
    	<div class="block">
			<?php
            $c = count($menu);
            $i = 1;
            foreach($menu as $ms)
            {
                if($i < $c)
                {
                ?>
                    <a <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?> ><span><?=$ms['name']?></span></a>
                <?
                }
                else
                {
                ?>
                    <a <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?> ><span><?=$ms['name']?></span></a>
                <?
                }
                $i++;
            }
            ?>
        </div>
    </div>
	<?
}
?>
