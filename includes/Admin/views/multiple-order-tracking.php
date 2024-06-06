<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('Multiple Order Tracking', 'order-detect'); ?></h1>
    <form method="get">
        <input type="hidden" name="page" value="multiple-order-tracking" />
        <?php
        $orders_list_table->search_box(__('Search', 'order-detect'), 'phone');
        $orders_list_table->views();
        $orders_list_table->display();
        ?>
    </form>
</div>