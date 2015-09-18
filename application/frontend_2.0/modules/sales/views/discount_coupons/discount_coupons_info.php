<div>
    <script type="text/javascript">
        $("div#PRS_bloc a.btn_detail").each().attr({target: "blank"});
    </script>
    <table>
        <tr>
            <td><?=$this->lang->line('d_c_info_name')?></td>
            <td><?=$C_info['name']?></td>
        </tr>
        <tr>
            <td><?=$this->lang->line('d_c_info_desc')?></td>
            <td><?=$C_info['description']?></td>
        </tr>
        <tr>
            <td><?=$this->lang->line('d_c_info_date_from')?></td>
            <td><?=$C_info['date_from']?></td>
        </tr>
        <tr>
            <td><?=$this->lang->line('d_c_info_date_to')?></td>
            <td><?=$C_info['date_to']?></td>
        </tr>
        <tr>
            <td><?=$this->lang->line('d_c_info_order_sum')?></td>
            <td><? echo $C_info['order_sum']*$C_info['currency']['rate'].' '.$C_info['currency']['name'];?></td>
        </tr>
        <tr>
            <td><?=$this->lang->line('d_c_info_discount_type')?></td>
            <td><? if($C_info['discount_type'] == 1) echo $this->lang->line("d_c_info_percent"); else echo $this->lang->line("d_c_info_sum"); ?></td>
        </tr>
        <?if($C_info['discount_type'] == 1){ ?>
            <tr>
                <td><?=$this->lang->line('d_c_info_discount_percent')?></td>
                <td><?=$C_info['discount_percent']?> %</td>
            </tr>
        <? } else { ?>
            <tr>
                <td><?=$this->lang->line('d_c_info_discount_sum')?></td>
                <td><? echo $C_info['discount_sum']*$C_info['currency']['rate'].' '.$C_info['currency']['name'];?></td>
            </tr>
        <? } ?>
        <tr>
            <td><?=$this->lang->line('d_c_info_is_used')?></td>
            <td><?if($C_info['is_used'] == 1){ echo $this->lang->line('yes'); } else { echo $this->lang->line('no'); } ?></td>
        </tr>
    </table>
</div>
