<span style="font-size: 16px;font-weight:600">
    <?php
    if ($value['name'] == 'sms_balance') {
        $sms_balance = get_option('orderbarrier_sms_balance');
        echo !empty($sms_balance) ? $sms_balance : '0.00';
    }
    ?>
</span>