<?php

use OrderShield\Helper;

class HelperTest extends WP_UnitTestCase
{

    /**
     * Test the check_license method
     */
    public function test_check_license()
    {
        // Test with empty settings
        $this->assertFalse(Helper::check_license([]));

        // Test with valid settings
        $settings = [
            'key' => 'valid_key',
            'expires' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ];
        $this->assertTrue(Helper::check_license($settings));

        // Test with expired license
        $settings = [
            'key' => 'valid_key',
            'expires' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ];
        $this->assertFalse(Helper::check_license($settings));
    }

    /**
     * Test the is_valid_Bangladeshi_phone_number method
     */
    public function test_is_valid_Bangladeshi_phone_number()
    {
        $valid_numbers = [
            '01712345678',
            '01812345678',
            '+8801712345678',
        ];

        $invalid_numbers = [
            '1234567890',
            '0171234567',
            '019123456789',
            '0171234abcd'
        ];

        foreach ($valid_numbers as $number) {
            $this->assertTrue(Helper::is_valid_Bangladeshi_phone_number($number));
        }

        foreach ($invalid_numbers as $number) {
            $this->assertFalse(Helper::is_valid_Bangladeshi_phone_number($number));
        }
    }

    /**
     * Test the generate_unique_otp_for_phone method
     */
    public function test_generate_unique_otp_for_phone()
    {
        global $wpdb;
        $wpdb->prefix = 'wp_';
        $wpdb->otp_verification = $wpdb->prefix . 'otp_verification';

        $phone_number = '01712345678';

        $otp = Helper::generate_unique_otp_for_phone($phone_number);
        $this->assertMatchesRegularExpression('/^\d{4}$/', $otp);

        // Check that the OTP is unique
        $wpdb->insert($wpdb->otp_verification, [
            'phone_number' => $phone_number,
            'otp' => $otp,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
        ]);

        $new_otp = Helper::generate_unique_otp_for_phone($phone_number);
        $this->assertNotEquals($otp, $new_otp);
    }

    /**
     * Test the generate_otp method
     */
    public function test_generate_otp()
    {
        global $wpdb;
        $wpdb->prefix = 'wp_';
        $wpdb->otp_verification = $wpdb->prefix . 'otp_verification';

        $phone_number = '01712345678';
        $otp = Helper::generate_otp($phone_number);

        // Check if the OTP was generated and stored correctly
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->otp_verification} WHERE phone_number = %s AND otp = %s",
            $phone_number,
            $otp
        ));
        $this->assertNotNull($result);
        $this->assertEquals($phone_number, $result->phone_number);
        $this->assertEquals($otp, $result->otp);
    }

    /**
     * Test the verify_otp method
     */
    public function test_verify_otp()
    {
        global $wpdb;
        $wpdb->prefix = 'wp_';
        $wpdb->otp_verification = $wpdb->prefix . 'otp_verification';

        $phone_number = '01712345678';
        $otp = Helper::generate_otp($phone_number);

        // Verify the generated OTP
        $this->assertTrue(Helper::verify_otp($phone_number, $otp));

        // Verify an invalid OTP
        $this->assertFalse(Helper::verify_otp($phone_number, '0000'));
    }

    /**
     * Test the skip_otp_form method
     */
    public function test_skip_otp_form()
    {
        // Simulate a logged-in user
        $user_id = $this->factory->user->create();
        wp_set_current_user($user_id);

        // Test with no next OTP check time
        $this->assertFalse(Helper::skip_otp_form());

        // Test with a valid next OTP check time
        $next_otp_check = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        update_user_meta($user_id, 'next_otp_check', $next_otp_check);
        $this->assertTrue(Helper::skip_otp_form());

        // Test with an expired next OTP check time
        $next_otp_check = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        update_user_meta($user_id, 'next_otp_check', $next_otp_check);
        $this->assertFalse(Helper::skip_otp_form());
    }

    /**
     * Test the getBalance method
     */
    public function test_getBalance()
    {
        $mock_url = 'https://api.example.com';
        $mock_api_key = 'mock_api_key';

        $mock_response = json_encode([
            'balance' => 100
        ]);

        $mock = $this->getMockBuilder('Helper')
            ->setMethods(['sendRequest'])
            ->getMock();

        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($mock_response);

        $balance = $mock->getBalance($mock_url, $mock_api_key);
        $this->assertEquals(100, $balance->balance);
    }

    /**
     * Test the send_sms_balance_notification method
     */
    public function test_send_sms_balance_notification()
    {
        // Set SMS balance to a low value
        update_option('ordershield_sms_balance', 40);

        // Capture the email sent
        add_filter('wp_mail', function ($args) {
            $this->assertEquals(get_option('admin_email'), $args['to']);
            $this->assertStringContainsString('Low SMS Balance Notification', $args['subject']);
            $this->assertStringContainsString('50 taka', $args['message']);
            return $args;
        });

        Helper::send_sms_balance_notification();
    }
}
