<?php

namespace OrderShield\Admin;

class MultipleOrderTrackingListTest extends WP_UnitTestCase
{
    protected $multipleOrderTrackingList;

    public function setUp(): void
    {
        parent::setUp();
        $this->multipleOrderTrackingList = new OrderShield\Admin\MultipleOrderTrackingList('1234567890');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->multipleOrderTrackingList);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(OrderShield\Admin\MultipleOrderTrackingList::class, $this->multipleOrderTrackingList);
    }

    public function testGetColumns()
    {
        $columns = $this->multipleOrderTrackingList->get_columns();
        $this->assertArrayHasKey('order_id', $columns);
        $this->assertArrayHasKey('date', $columns);
        $this->assertArrayHasKey('status', $columns);
        $this->assertArrayHasKey('billing', $columns);
        $this->assertArrayHasKey('ship_to', $columns);
        $this->assertArrayHasKey('phone_number', $columns);
        $this->assertArrayHasKey('total', $columns);
    }

    public function testGetSortableColumns()
    {
        $sortable_columns = $this->multipleOrderTrackingList->get_sortable_columns();
        $this->assertArrayHasKey('order_id', $sortable_columns);
        $this->assertArrayHasKey('date', $sortable_columns);
        $this->assertArrayHasKey('status', $sortable_columns);
        $this->assertArrayHasKey('billing', $sortable_columns);
        $this->assertArrayHasKey('ship_to', $sortable_columns);
        $this->assertArrayHasKey('phone_number', $sortable_columns);
        $this->assertArrayHasKey('total', $sortable_columns);
    }

    public function testGetItemsPerPage()
    {
        $option = 'multi_order_tracking_per_page';
        $default = 20;
        $result = $this->multipleOrderTrackingList->get_items_per_page($option, $default);
        $this->assertEquals($default, $result);
    }

    public function testPrepareItems()
    {
        $_REQUEST['s'] = '1234567890';
        $this->multipleOrderTrackingList->prepare_items();
        $this->assertNotEmpty($this->multipleOrderTrackingList->items);
    }

    public function testGetTotalItems()
    {
        $search = '1234567890';
        $result = $this->multipleOrderTrackingList->get_total_items($search);
        $this->assertIsInt($result);
    }

    public function testGetOrdersByPhoneNumber()
    {
        $phone_number = '1234567890';
        $per_page = 20;
        $current_page = 1;
        $result = $this->multipleOrderTrackingList->get_orders_by_phone_number($phone_number, $per_page, $current_page);
        $this->assertIsArray($result);
    }
}
