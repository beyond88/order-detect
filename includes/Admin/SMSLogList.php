<?php

namespace OrderDetect\Admin;

// Check if the WP_List_Table class exists, and if not, include the necessary files
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
    require_once ABSPATH . 'wp-admin/includes/screen.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * SMSLogList class
 * This class extends the WP_List_Table class and provides a custom list table
 * to display multiple orders associated with a customer's phone number.
 */
class SMSLogList extends \WP_List_Table {

    /**
     * The customer's phone number.
     * @var string
     */
    public $phone_number;

    /**
     * Constructor
     * @param string $phone_number The customer's phone number
     */
    public function __construct($phone_number) {
        parent::__construct([
            'singular' => 'sms_log',
            'plural' => 'sms_logs',
            'ajax' => false,
        ]);
        $this->phone_number = $phone_number;
    }

    /**
     * Get the list of columns for the table
     * @return array The list of columns
     */
    public function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', 'order-detect'),
            'phone_number' => __('Phone Number', 'order-detect'),
            'code' => __('Code', 'order-detect'),
            'expires_at' => __('Expires At', 'order-detect'),
            'is_verified' => __('Verified?', 'order-detect'),
            'created_at' => __('Created At', 'order-detect'),
        ];
        return $columns;
    }

    /**
     * Render the default column content
     * @param object $item The row data
     * @param string $column_name The column name
     * @return string The column content
     */
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
                return $item->id;
            case 'phone_number':
                return $item->phone_number;
            case 'code':
                return $item->code;
            case 'expires_at':
                return $item->expires_at;
            case 'is_verified':
                return $item->is_verified ? __('Yes', 'order-detect') : __('No', 'order-detect');
            case 'created_at':
                return $item->created_at;
            default:
                return print_r($item, true);
        }
    }

    /**
     * Render the checkbox column content
     * @param object $item The row data
     * @return string The column content
     */
    protected function column_cb($item){
        return sprintf('<input type="checkbox" name="sms_log[]" value="%s" />', $item->id);
    }

    /**
     * Get the list of sortable columns
     * @return array The list of sortable columns
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'id' => ['id', true],
            'phone_number' => ['phone_number', true],
            'code' => ['code', false],
            'expires_at' => ['expires_at', false],
            'is_verified' => ['is_verified', false],
            'created_at' => ['created_at', false]
        ];
        return $sortable_columns;
    }

    /**
     * Get the list of bulk actions
     * @return array The list of bulk actions
     */
    public function get_bulk_actions() {
        $actions = [
            'delete' => __('Delete', 'order-detect')
        ];
        return $actions;
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['sms_log']) ? $_REQUEST['sms_log'] : [];
            if (is_array($ids)) {
                $ids = array_map('intval', $ids);
            }
            if (!empty($ids)) {
                $this->delete_items($ids);
            }
        }
    }

    /**
     * Delete items from the database
     * @param array $ids The IDs of the items to delete
     */
    public function delete_items($ids) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'od_otp_log';
        $ids_format = implode(',', array_fill(0, count($ids), '%d'));

        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_format)", $ids));
    }

    /**
     * Get the number of items per page
     * @param string $option The option name
     * @param int $default The default number of items per page
     * @return int The number of items per page
     */
    public function get_items_per_page($option, $default = 20) {
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
    public function prepare_items() {
        $this->process_bulk_action();

        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        $per_page = $this->get_items_per_page('sms_log_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = $this->get_total_items($search);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

        $sms_logs = $this->get_sms_log_by_phone_number($search, $per_page, $current_page);
        $this->items = $sms_logs;

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
    public function get_total_items($search = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'od_otp_log';

        $query = "SELECT COUNT(*) FROM $table_name WHERE 1=1";

        if (!empty($search)) {
            $query .= $wpdb->prepare(" AND phone_number LIKE %s", '%' . $wpdb->esc_like($search) . '%');
        }

        return (int) $wpdb->get_var($query);
    }

    /**
     * Get the SMS logs by phone number
     * @param string $phone_number The customer's phone number
     * @param int $per_page The number of items per page
     * @param int $current_page The current page number
     * @return array The list of SMS logs
     */
    public function get_sms_log_by_phone_number($phone_number, $per_page, $current_page) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'od_otp_log';

        $offset = ($current_page - 1) * $per_page;

        $query = "SELECT * FROM $table_name WHERE phone_number LIKE %s LIMIT %d OFFSET %d";
        $prepared_query = $wpdb->prepare($query, '%' . $wpdb->esc_like($phone_number) . '%', $per_page, $offset);

        return $wpdb->get_results($prepared_query);
    }
}
