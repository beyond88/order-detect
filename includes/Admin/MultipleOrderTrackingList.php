<?php

namespace OrderShield\Admin;

// Check if the WP_List_Table class exists, and if not, include the necessary files
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
    require_once ABSPATH . 'wp-admin/includes/screen.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * MultipleOrderTrackingList class
 * This class extends the WP_List_Table class and provides a custom list table
 * to display multiple orders associated with a customer's phone number.
 */
class MultipleOrderTrackingList extends \WP_List_Table
{

    /**
     * Constructor
     * @param string $phone_number The customer's phone number
     */
    public function __construct($phone_number)
    {
        parent::__construct([
            'singular' => 'order',
            'plural' => 'orders',
            'ajax' => false,
        ]);
        $this->phone_number = $phone_number;
    }

    /**
     * Get the list of columns for the table
     * @return array The list of columns
     */
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

    /**
     * Render the default column content
     * @param WC_Order $item The order object
     * @param string $column_name The column name
     * @return string The column content
     */
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
            case 'phone_number':
                return $item->get_billing_phone();
            case 'total':
                return $item->get_total();
            default:
                return print_r($item, true);
        }
    }

    /**
     * Get the list of sortable columns
     * @return array The list of sortable columns
     */
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

    /**
     * Get the number of items per page
     * @param string $option The option name
     * @param int $default The default number of items per page
     * @return int The number of items per page
     */
    public function get_items_per_page($option, $default = 20)
    {
        $user_per_page = (int) get_user_meta(get_current_user_id(), $option, true);
        if (empty($user_per_page) || $user_per_page < 1) {
            $user_per_page = $default;
        }
        return $user_per_page;
    }

    /**
     * Prepare the items for the list table
     * @return void
     */
    public function prepare_items()
    {
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

    /**
     * Get the total number of items
     * @param string $search The search query
     * @return int The total number of items
     */
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

    /**
     * Get the orders by phone number
     * @param string $phone_number The customer's phone number
     * @param int $per_page The number of items per page
     * @param int $current_page The current page number
     * @return array The list of orders
     */
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