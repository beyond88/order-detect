<span style="font-size: 16px;font-weight:600">
    <?php
    if ($value['name'] == 'sms_balance') {
        echo array_key_exists($value['name'], $setting_options) ? esc_attr($setting_options[$value['name']]) : '0.00';
    }
    ?>
</span>