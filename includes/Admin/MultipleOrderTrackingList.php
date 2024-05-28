<?php

namespace OrderShield\Admin;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
    require_once ABSPATH . 'wp-admin/includes/screen.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class MultipleOrderTrackingList extends \WP_List_Table
{

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
            'order_id' => __('Order ID', 'order-shield'),
            'date' => __('Date', 'order-shield'),
            'status' => __('Status', 'order-shield'),
            'billing' => __('Billing', 'order-shield'),
            'ship_to' => __('Ship To', 'order-shield'),
            'phone_number' => __('Phone Number', 'order-shield'),
            'total' => __('Total', 'order-shield'),
        ];
        return $columns;
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'order_id':
                $order_id = $item->get_id();
                $order_link = admin_url('post.php?post=' . $order_id . '&action=edit');
                return '<a href="' . esc_url($order_link) . '">' . $order_id . '</a>';
            case 'date':
                return $item->get_date_created()->date('Y-m-d H:i:s');
            case 'status':
                return wc_get_order_status_name($item->get_status());
            case 'billing':
                return $item->get_billing_first_name() . ' ' . $item->get_billing_last_name();
            case 'ship_to':
                return $item->get_shipping_first_name() . ' ' . $item->get_shipping_last_name();
            case 'phone_number': // New case
                return $item->get_billing_phone();
            case 'total':
                return $item->get_total();
            default:
                return print_r($item, true);
        }
    }

    public function get_sortable_columns()
    {
        $sortable_columns = [
            'order_id' => ['order_id', true],
            'date' => ['date', true],
            'status' => ['status', false],
            'billing' => ['billing', false],
            'ship_to' => ['ship_to', false],
            'phone_number' => ['phone_number', false],
            'total' => ['total', false],
        ];
        return $sortable_columns;
    }

    public function get_items_per_page($option, $default = 20)
    {
        $user_per_page = (int) get_user_meta(get_current_user_id(), $option, true);
        if (empty($user_per_page) || $user_per_page < 1) {
            $user_per_page = $default;
        }
        return $user_per_page;
    }

    public function prepare_items()
    {
        // $per_page = $this->get_items_per_page('multi_order_tracking_per_page', $this->per_page);
        // $current_page = $this->get_pagenum();
        // $total_items = $this->get_total_items();

        // $this->set_pagination_args([
        //     'total_items' => $total_items,
        //     'per_page' => $per_page,
        //     'total_pages' => ceil($total_items / $per_page),
        // ]);

        // $orders = $this->get_orders_by_phone_number($this->phone_number, $per_page, $current_page);
        // $this->items = $orders;

        // $columns = $this->get_columns();
        // $hidden = [];
        // $sortable = $this->get_sortable_columns();
        // $this->_column_headers = [$columns, $hidden, $sortable];


        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        $per_page = $this->get_items_per_page('multi_order_tracking_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = $this->get_total_items($search);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

        $orders = $this->get_orders_by_phone_number($search, $per_page, $current_page);
        $this->items = $orders;

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    private function get_total_items($search = '')
    {
        $args = [
            'status' => 'any',
        ];

        if (!empty($search)) {
            $args['meta_key'] = '_billing_phone';
            $args['meta_value'] = $search;
            $args['meta_compare'] = 'LIKE';
        }

        $orders = wc_get_orders($args);
        return count($orders);
    }

    private function get_orders_by_phone_number($phone_number, $per_page, $current_page)
    {
        $args = [
            'status' => 'any',
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page,
        ];

        if (!empty($phone_number)) {
            $args['meta_key'] = '_billing_phone';
            $args['meta_value'] = $phone_number;
            $args['meta_compare'] = 'LIKE';
        }

        return wc_get_orders($args);
    }
}
