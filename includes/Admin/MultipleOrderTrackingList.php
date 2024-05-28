<?php

namespace OrderShield\Admin;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class MultipleOrderTrackingList extends \WP_List_Table
{

    private $phone_number;

    public function __construct($phone_number)
    {
        parent::__construct([
            'singular' => 'order',
            'plural' => 'orders',
            'ajax' => false,
        ]);
        $this->phone_number = $phone_number;
    }

    public function get_columns()
    {
        $columns = [
            'order_id' => 'Order ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
        return $columns;
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'order_id':
                return $item->get_id();
            case 'date':
                return $item->get_date_created()->date('Y-m-d H:i:s');
            case 'status':
                return wc_get_order_status_name($item->get_status());
            default:
                return print_r($item, true);
        }
    }

    public function prepare_items()
    {
        $orders = $this->get_orders_by_phone_number($this->phone_number);
        $this->items = $orders;
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    private function get_orders_by_phone_number($phone_number)
    {
        $args = [
            'status' => 'any',
            'meta_key' => '_billing_phone',
            'meta_value' => $phone_number,
            'meta_compare' => 'LIKE'
        ];
        return wc_get_orders($args);
    }
}
