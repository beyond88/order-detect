<?php

// Display OTP and button under billing phone
add_action( 'woocommerce_after_checkout_billing_form', 'display_custom_field_and_button' );
function display_custom_field_and_button( $checkout ) {
    // woocommerce_form_field( 'billing_otp', array(
    //     'type'        => 'text',
    //     'class'       => array('form-row-wide'),
    //     'label'       => __('OTP', 'order-detect'),
    //     'placeholder' => __('Enter your OTP code', 'order-detect'),
    //     'required'    => true,
    //     ), $checkout->get_value( 'billing_otp' ));
    //     echo '<button type="button" class="button alt" id="resend_otp">'.__('Resend', 'order-detect').'</button>';
    //     echo "<br>";
    //     echo '<span class="order-detect-otp-status" id="order-detect-otp-status"></span>';
        echo '<div id="phone-verification-wrapper"></div>';
}

function orderguard_validate_phone_number() {
    if (isset($_POST['billing_phone']) && !empty($_POST['billing_phone'])) {
        $billing_phone = preg_replace('/\D/', '', $_POST['billing_phone']); 
        if (strlen($billing_phone) != 11) {
            wc_add_notice(__('Please provide your correct 11-digit mobile number.'), 'error');
        }
    }
}
add_action('woocommerce_checkout_process', 'orderguard_validate_phone_number');
