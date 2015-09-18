<div class="form_block discount_coupons_form">
    <form enctype="multipart/form-data" action="<?=$this->router->build_url('order_methods_lang', array('method' => 'save', 'lang' => $this->mlangs->lang_code));?>" method="post" id="coupon_info">
        <div class="form_label order_label"><span><?=$this->lang->line('d_c_info')?></span></div>
        <div class="form_item_inside_block">
            <div class="form_field order_field note_field"><label for="promocode"><?=$this->lang->line('c_o_activate_note')?> *:</label><input type="text" name="promocode" id="promocode" value="<?=@$order_data['promocode']?>"><div class="clear_both"></div></div>
        </div>
        <div class="form_message_block activate_message_block">
            <div class="error_message">
                <div></div>
            </div>
            <div class="success_message">
                <div></div>
            </div>
        </div>
        <div class="form_button order_button submit_order"><a href="#" id="activate"><span><?=$this->lang->line('d_c_info_view')?></span></a></div>
    </form>
</div>