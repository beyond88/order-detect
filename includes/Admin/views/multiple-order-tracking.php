<?php $phone_number = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('Multiple Order Tracking', 'order-shield'); ?></h1>
    <form method="post" action="">
        <input type="text" name="customer_phone" placeholder="Enter customer phone number" value="<?php echo esc_attr($phone_number); ?>" required>
        <input type="submit" name="search_orders" value="Search Orders" class="button-primary">
    </form>
    <?php
    if ($orders_list_table) {
        $orders_list_table->display();
    }
    ?>
</div>