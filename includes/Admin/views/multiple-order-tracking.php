<?php //$phone_number = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : ''; 
?>

<div class="wrap">
    <h1>Multiple Order Tracking</h1>
    <form method="post" action="">
        <input type="text" name="customer_phone" placeholder="Enter customer phone number" value="<?php //echo esc_attr($phone_number); 
                                                                                                    ?>" required>
        <input type="submit" name="search_orders" value="Search Orders" class="button-primary">
    </form>
    <?php
    // if (!empty($phone_number)) {
    //     $orders_list_table = new MultipleOrderTrackingList($phone_number);
    //     $orders_list_table->prepare_items();
    //     $orders_list_table->display();
    // }
    ?>
</div>