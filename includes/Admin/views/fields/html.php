<span style="font-size: 16px;font-weight:600">
    <?php
    if ($value['name'] == 'sms_balance') {
        $sms_balance = get_option('ordershield_sms_balance');
        echo isset($sms_balance) ? esc_attr($sms_balance) : '0.00';
    }
    ?>
</span>