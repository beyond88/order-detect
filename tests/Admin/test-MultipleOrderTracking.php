<?php

namespace OrderShield\Admin;

class MultipleOrderTrackingTest extends WP_UnitTestCase
{
    protected $multipleOrderTracking;

    public function setUp(): void
    {
        parent::setUp();
        $this->multipleOrderTracking = new OrderShield\Admin\MultipleOrderTracking('path/to/main/plugin/file.php');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->multipleOrderTracking);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(OrderShield\Admin\MultipleOrderTracking::class, $this->multipleOrderTracking);
    }

    public function testOrderShieldMultipleOrderTracking()
    {
        $settings = array();
        $result = $this->multipleOrderTracking->ordershield_multiple_order_tracking($settings);

        $this->assertArrayHasKey('multiple-order-tracking', $result);
        $this->assertArrayHasKey('parent_slug', $result['multiple-order-tracking']);
        $this->assertArrayHasKey('page_title', $result['multiple-order-tracking']);
        $this->assertArrayHasKey('menu_title', $result['multiple-order-tracking']);
        $this->assertArrayHasKey('capability', $result['multiple-order-tracking']);
        $this->assertArrayHasKey('callback', $result['multiple-order-tracking']);
    }

    public function testSetScreenOption()
    {
        $status = false;
        $option = 'multi_order_tracking_per_page';
        $value = 10;

        $result = $this->multipleOrderTracking->set_screen_option($status, $option, $value);

        $this->assertEquals($value, $result);
    }

    public function testAddScreenOptions()
    {
        // Mock the add_screen_option function and test its invocation
        $this->expectOutputString('Number of items per page');
        $this->multipleOrderTracking->add_screen_options();
    }
}