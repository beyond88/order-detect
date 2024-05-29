<?php

use OrderShield\Admin\Menu;
use OrderShield\Admin\Main;

class MenuTest extends WP_UnitTestCase {

    protected $menu;
    protected $main;

    public function setUp(): void {
        parent::setUp();
        $this->main = $this->getMockBuilder(Main::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->menu = new Menu($this->main);
    }

    public function test_constructor() {
        $this->assertTrue(has_action('admin_menu', [$this->menu, 'admin_menu']) !== false);
    }

    public function test_admin_menu() {
        // Call the method
        $this->menu->admin_menu();

        // Check that the menu and submenu pages are registered
        global $menu, $submenu;

        // Check main menu item
        $this->assertNotEmpty($menu['50']); // Position 50 in the menu
        
        // Check submenus are registered if settings were filtered
        $settings = apply_filters('ordershield_admin_menu', []);
        foreach ($settings as $slug => $setting) {
            $this->assertNotEmpty($submenu[$setting['parent_slug']]);
        }
    }

    public function test_enqueue_assets() {
        // Call the method
        $this->menu->enqueue_assets();

        // Check that styles and scripts are enqueued
        $this->assertTrue(wp_style_is('ordershield-admin-boostrap', 'enqueued'));
        $this->assertTrue(wp_style_is('ordershield-admin-style', 'enqueued'));
        $this->assertTrue(wp_script_is('ordershield-admin-script', 'enqueued'));
    }
}
