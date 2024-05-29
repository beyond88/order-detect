<?php

use OrderShield\Admin\Main;
use OrderShield\API\OrderShieldAPI;
use OrderShield\Helper;

class MainTest extends WP_UnitTestCase {

    protected $main;

    public function setUp(): void {
        parent::setUp();
        $this->main = new Main();
    }

    public function test_constructor() {
        $this->assertTrue(has_action('plugins_loaded', [$this->main, 'set_default_options']) !== false);
        $this->assertTrue(has_action('admin_init', [$this->main, 'menu_register_settings']) !== false);
        $this->assertTrue(has_action('admin_init', [$this->main, 'check_and_save_sms_balance']) !== false);
    }

    public function test_set_default_options() {
        $default_options = $this->main->set_default_options();
        $this->assertIsArray($default_options);
        $this->assertArrayHasKey('pathao_api_endpoint', $default_options);
        $this->assertArrayHasKey('enable_otp', $default_options);
    }

    public function test_menu_register_settings() {
        // Call the method
        $this->main->menu_register_settings();

        // Check that the option is added
        $option = get_option($this->main->_optionName);
        $this->assertNotFalse($option);

        // Check that the settings group is registered
        global $new_whitelist_options;
        $this->assertArrayHasKey($this->main->_optionGroup, $new_whitelist_options);
        $this->assertContains($this->main->_optionName, $new_whitelist_options[$this->main->_optionGroup]);
    }

    public function test_plugin_page() {
        $this->expectOutputRegex('/<html>/'); // Change this regex to match your template output
        $this->main->plugin_page();
    }

    public function test_check_and_save_sms_balance() {
        // Mock POST data and nonce
        $_POST['ordershield_nonce'] = wp_create_nonce('ordershield_options_verify');
        $_POST['ordershield_settings'] = [
            'enable_otp' => '1',
            'sms_api_endpoint' => 'https://api.example.com',
            'sms_api_key' => 'fake_api_key'
        ];

        // Mocking Helper::getBalance
        $mock_balance_response = (object)[
            'error' => 0,
            'data' => (object)['balance' => 100]
        ];
        
        // Mock Helper::getBalance method
        Helper::shouldReceive('getBalance')
            ->with('https://api.example.com', 'fake_api_key')
            ->andReturn($mock_balance_response);

        // Mock Helper::send_sms_balance_notification method
        Helper::shouldReceive('send_sms_balance_notification')
            ->andReturn(true);

        // Call the method
        $this->main->check_and_save_sms_balance();

        // Check the balance is saved
        $this->assertEquals(100, get_option('ordershield_sms_balance'));
    }
}
