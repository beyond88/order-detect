<?php
// Override the order button HTML
add_filter('woocommerce_order_button_html', 'custom_order_button_html');
function custom_order_button_html($button_html)
{
    $button_html = '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order_custom" data-value="Place order" onclick="return false;">Custom Button</button>';
    return $button_html;
}
