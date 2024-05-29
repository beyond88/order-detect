<?php

namespace OrderShield;

class AdminTest extends WP_UnitTestCase
{
    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = new OrderShield\Admin();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->admin);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(OrderShield\Admin::class, $this->admin);
        $this->assertInstanceOf(OrderShield\Admin\Main::class, $this->admin->main);
        $this->assertInstanceOf(OrderShield\Admin\Menu::class, $this->admin->menu);
        $this->assertInstanceOf(OrderShield\Admin\MultipleOrderTracking::class, $this->admin->multipleOrderTracking);
        $this->assertInstanceOf(OrderShield\Admin\License::class, $this->admin->license);
        $this->assertInstanceOf(OrderShield\Admin\PluginMeta::class, $this->admin->pluginMeta);
    }
}
