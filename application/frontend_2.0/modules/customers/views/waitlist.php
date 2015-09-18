<div style="width: 400px; margin: 0 auto;" >
    <form enctype="multipart/form-data" action="<?=$this->router->build_url('ajax_lang', array('ajax' => 'customers/waitlist/ajax_add_item','lang' => $this->mlangs->lang_code));?>" method="post" id="waitlist_form">
        <div align="center" class="form_block waitlist_form">
            <div class="form_label customer_login_label"><span><?=$this->lang->line('waitlist_message')?></span></div>
                <div class="form_item_block customer_login_block">
                <div class="form_item_inside_block">
                    <div class="form_field login_field">
                        <label for="waitlist_email" >E-mail</label>
                        <input type="text" placeholder="Email" id="waitlist_email" name="email" required="required">
                        <div class="clear_both"></div>
                    </div>
                    <input type="hidden" id="waitlist_product" name="product_id" value="<?=$product_id?>" required="required">
                    <div class="form_message_block" id="waitlist_message_block">
                        <div class="error_message">
                            <div></div>
                        </div>
                        <div class="success_message">
                            <div></div>
                        </div>
                    </div>
                    <div class="clear_both"></div>
                    <div class="form_button waitlist_button">
                        <a href="#" id="submit">
                            <span><?=$this->lang->line('save')?></span>
                        </a>
                    </div>
                    <div class="clear_both"></div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $('#waitlist_form').gbc_products_detail('init_waitlist_send_email', '<?=$this->lang->line('error_submit')?>');
    </script>
</div>