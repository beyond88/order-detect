<?php

use OrderShield\Admin\License;

class LicenseTest extends WP_UnitTestCase {

    protected $license;

    public function setUp(): void {
        parent::setUp();
        $this->license = new License('order-shield/order-shield.php');
    }

    public function test_constructor() {
        $this->assertEquals('order-shield/order-shield.php', $this->license->main);
    }

    public function test_ordershield_license() {
        $settings = [];
        $result = $this->license->ordershield_license($settings);
        
        $this->assertArrayHasKey('license', $result);
        $this->assertArrayHasKey('parent_slug', $result['license']);
        $this->assertArrayHasKey('page_title', $result['license']);
        $this->assertArrayHasKey('menu_title', $result['license']);
        $this->assertArrayHasKey('capability', $result['license']);
        $this->assertArrayHasKey('callback', $result['license']);

        $this->assertEquals('order-shield', $result['license']['parent_slug']);
        $this->assertEquals('License', $result['license']['page_title']);
        $this->assertEquals('License', $result['license']['menu_title']);
        $this->assertEquals('manage_options', $result['license']['capability']);
    }

    public function test_check_license_expiration_frontend() {
        $expired_date = date('Y-m-d H:i:s', strtotime('-1 day'));
        $valid_date = date('Y-m-d H:i:s', strtotime('+1 day'));

        // Test expired date
        ob_start();
        $this->license->check_license_expiration_frontend($expired_date);
        $output_expired = ob_get_clean();
        $this->assertStringContainsString('Your license has been expired.', $output_expired);

        // Test valid date
        ob_start();
        $this->license->check_license_expiration_frontend($valid_date);
        $output_valid = ob_get_clean();
        $this->assertStringContainsString('License expire date:', $output_valid);
    }

    public function test_license_button() {
        // Mock get_option function
        add_filter('pre_option_ordershield_license', function() {
            return [
                'key' => 'valid_key',
                'expires' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ];
        });

        // Test active license
        $button_html = $this->license->license_button();
        $this->assertStringContainsString('Deactivate', $button_html);

        // Mock expired license
        add_filter('pre_option_ordershield_license', function() {
            return [
                'key' => 'valid_key',
                'expires' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ];
        });

        // Test expired license
        $button_html = $this->license->license_button();
        $this->assertStringContainsString('Activate', $button_html);

        // Mock missing key or expires
        add_filter('pre_option_ordershield_license', function() {
            return [];
        });

        // Test missing license data
        $button_html = $this->license->license_button();
        $this->assertStringContainsString('Activate', $button_html);
    }
}
