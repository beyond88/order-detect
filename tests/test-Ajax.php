<?php

use OrderShield\Ajax;

class AjaxTest extends WP_UnitTestCase {

    protected $ajax;

    public function setUp(): void {
        parent::setUp();
        $this->ajax = new Ajax();
    }

    public function test_constructor() {
        $this->assertInstanceOf(OrderShield\API\OrderShieldAPI::class, $this->ajax->api);
        $this->assertIsArray($this->ajax->settings);
    }

    public function test_license_activate() {
        // Mock necessary data and functions
        $_POST['license_key'] = 'valid_license_key';
        $_POST['security'] = wp_create_nonce('order-shield-admin-nonce');
        add_filter('pre_http_request', [$this, 'mock_license_activate_response'], 10, 3);

        // Capture the output of the AJAX action
        ob_start();
        $this->ajax->license_activate();
        $output = ob_get_clean();
        $response = json_decode($output, true);

        // Assertions
        $this->assertEquals('License activated successfully.', $response['message']);
        $this->assertEquals('order-shield-license-status-success', $response['class']);
    }

    public function test_license_deactivate() {
        // Mock necessary data and functions
        update_option('ordershield_license', ['key' => 'valid_license_key']);
        $_POST['security'] = wp_create_nonce('order-shield-admin-nonce');
        add_filter('pre_http_request', [$this, 'mock_license_deactivate_response'], 10, 3);

        // Capture the output of the AJAX action
        ob_start();
        $this->ajax->license_deactivate();
        $output = ob_get_clean();
        $response = json_decode($output, true);

        // Assertions
        $this->assertEquals('License deactivated successfully.', $response['message']);
        $this->assertEquals('order-shield-license-status-success', $response['class']);
    }

    public function test_send_otp() {
        // Mock necessary data and functions
        $_POST['phone_number'] = '01712345678';
        $_POST['security'] = wp_create_nonce('order-shield-nonce');
        add_filter('pre_http_request', [$this, 'mock_send_otp_response'], 10, 3);

        // Capture the output of the AJAX action
        ob_start();
        $this->ajax->send_otp();
        $output = ob_get_clean();
        $response = json_decode($output, true);

        // Assertions
        $this->assertTrue($response['success']);
        $this->assertEquals('OTP has been sent to your phone number.', $response['message']);
    }

    public function test_verify_otp() {
        // Mock necessary data and functions
        $_POST['phone_number'] = '01712345678';
        $_POST['otp'] = '123456';
        $_POST['security'] = wp_create_nonce('order-shield-nonce');
        add_filter('pre_http_request', [$this, 'mock_verify_otp_response'], 10, 3);

        // Capture the output of the AJAX action
        ob_start();
        $this->ajax->verify_otp();
        $output = ob_get_clean();
        $response = json_decode($output, true);

        // Assertions
        $this->assertTrue($response['success']);
        $this->assertEquals('OTP verification successful.', $response['message']);
    }

    // Mock responses for HTTP requests
    public function mock_license_activate_response($preempt, $args, $url) {
        return [
            'response' => ['code' => 200],
            'body' => json_encode(['success' => true, 'expires' => '2025-12-31']),
        ];
    }

    public function mock_license_deactivate_response($preempt, $args, $url) {
        return [
            'response' => ['code' => 200],
            'body' => json_encode(['success' => true]),
        ];
    }

    public function mock_send_otp_response($preempt, $args, $url) {
        return [
            'response' => ['code' => 200],
            'body' => json_encode(['success' => true]),
        ];
    }

    public function mock_verify_otp_response($preempt, $args, $url) {
        return [
            'response' => ['code' => 200],
            'body' => json_encode(['success' => true]),
        ];
    }
}
