<?php

use OrderShield\Assets;

class AssetsTest extends WP_UnitTestCase {

    protected $assets;

    public function setUp(): void {
        parent::setUp();
        $this->assets = new Assets();
        wp_enqueue_script('jquery');  // Ensure jQuery is enqueued for dependency resolution
    }

    public function test_constructor() {
        $this->assertEquals(10, has_action('wp_enqueue_scripts', array($this, 'register_assets')));
        $this->assertEquals(10, has_action('admin_enqueue_scripts', array($this, 'register_admin_assets')));
    }

    public function test_register_assets() {
        $this->assets->register_assets();

        // Check if scripts are enqueued
        $this->assertTrue(wp_script_is('order-shield-script', 'enqueued'));
        $this->assertTrue(wp_style_is('order-shield-style', 'enqueued'));

        // Check if localized script data is correct
        $localized_data = wp_scripts()->get_data('order-shield-script', 'data');
        $this->assertStringContainsString('ajax_url', $localized_data);
        $this->assertStringContainsString('nonce', $localized_data);
    }

    public function test_register_admin_assets() {
        $this->assets->register_admin_assets('admin_page_hook');

        // Check if admin scripts are enqueued
        $this->assertTrue(wp_script_is('order-shield-admin-script', 'enqueued'));
        $this->assertTrue(wp_style_is('ordershield-admin-style', 'enqueued'));

        // Check if localized script data is correct
        $localized_data = wp_scripts()->get_data('order-shield-admin-script', 'data');
        $this->assertStringContainsString('ajax_url', $localized_data);
        $this->assertStringContainsString('nonce', $localized_data);
    }

    public function test_get_scripts() {
        $scripts = $this->assets->get_scripts();

        $this->assertArrayHasKey('order-shield-script', $scripts);
        $this->assertEquals(ORDERSHIELD_ASSETS . '/js/frontend.js', $scripts['order-shield-script']['src']);
    }

    public function test_get_styles() {
        $styles = $this->assets->get_styles();

        $this->assertArrayHasKey('order-shield-style', $styles);
        $this->assertEquals(ORDERSHIELD_ASSETS . '/css/frontend.css', $styles['order-shield-style']['src']);
    }

    public function test_get_admin_scripts() {
        $admin_scripts = $this->assets->get_admin_scripts();

        $this->assertArrayHasKey('order-shield-admin-script', $admin_scripts);
        $this->assertEquals(ORDERSHIELD_ASSETS . '/js/admin.js', $admin_scripts['order-shield-admin-script']['src']);
    }

    public function test_get_admin_styles() {
        $admin_styles = $this->assets->get_admin_styles();

        $this->assertArrayHasKey('ordershield-admin-style', $admin_styles);
        $this->assertEquals(ORDERSHIELD_ASSETS . '/css/admin.css', $admin_styles['ordershield-admin-style']['src']);
    }
}
