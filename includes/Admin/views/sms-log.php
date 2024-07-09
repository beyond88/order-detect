<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('SMS Log', 'order-detect'); ?></h1>
    <form method="get">
        <input type="hidden" name="page" value="sms-log" />
        <?php
            $sms_list_table->search_box(__('Search', 'order-detect'), 'phone');
            $sms_list_table->views();
            $sms_list_table->display();
        ?>
    </form>
</div>