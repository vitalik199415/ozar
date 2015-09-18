<div class="menu">
    <?php
        function build_menu($menu_arr, $parrent_id = 0) {
            if(is_array($menu_arr) and count(@$menu_arr[$parrent_id]) > 0) {
                $tree = '<ul>';
                foreach($menu_arr[$parrent_id] as $key => $vall) {
                    if ($vall[1] == '#') $tree .= '<li><a href="#" style="width:145px;"><p>'.$vall[0].'</p></a>';
                    else $tree .= '<li><a href="'.set_url($vall[1]).'" style="width:145px;"><p>'.$vall[0].'</p></a>';

                    $tree .= build_menu($menu_arr, $key);
                    $tree .= '</li>';
                }
                $tree .= '</ul>';
            }
            else return null;
            return $tree;
        }
        echo build_menu($menu);
    ?>
</div>

<script>
    $('div.menu > ul').attr('id', 'gbc_dropdown_menu');
    $('div.menu > ul > li > ul > li').each(function(){
        var list = $(this).find('ul');

        if(list.length > 0){
            $($(this).find('a')[0]).addClass('str');
        }
    });
    $('#gbc_dropdown_menu').gbc_dropdown_menu();
</script>