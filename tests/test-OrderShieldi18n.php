<?php

use OrderShield\OrderShieldi18n;

class OrderShieldi18nTest extends WP_UnitTestCase
{

    /**
     * Test the constructor method
     */
    public function test_constructor()
    {
        $i18n = new OrderShieldi18n();

        // Check if the action 'plugins_loaded' has been added with the correct callback
        $this->assertNotFalse(has_action('plugins_loaded', array($i18n, 'load_plugin_textdomain')));
    }

    /**
     * Test the load_plugin_textdomain method
     */
    public function test_load_plugin_textdomain()
    {
        $i18n = new OrderShieldi18n();

        // Call the load_plugin_textdomain method
        $i18n->load_plugin_textdomain();

        // Check if the text domain is loaded
        $is_textdomain_loaded = load_plugin_textdomain('order-shield', false, dirname(dirname(ORDERSHIELD_BASENAME)) . '/languages/');

        // Ensure the text domain is loaded correctly
        $this->assertTrue($is_textdomain_loaded);
    }
}
