<?php

namespace OrderShield\Admin;


class PluginMetaTest extends WP_UnitTestCase
{
    protected $pluginMeta;

    public function setUp(): void
    {
        parent::setUp();
        $this->pluginMeta = new OrderShield\Admin\PluginMeta();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->pluginMeta);
    }

    public function testPluginActionLinks()
    {
        $links = [];
        $result = $this->pluginMeta->plugin_action_links($links);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('Settings', $result[0]);
    }

    public function testPluginMetaLinks()
    {
        $links = [];
        $file = 'order-shield/order-shield.php';
        $result = $this->pluginMeta->plugin_meta_links($links, $file);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('Support', $result[0]);
        $this->assertStringContainsString('Plugin Homepage', $result[1]);
    }
}
