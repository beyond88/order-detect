<?php

namespace OrderDetect\Admin;

use OrderDetect\Admin\MultipleOrderTrackingList;
use OrderDetect\Helper;

/**
 * Settings Handler class
 */
class MultipleOrderTracking {

    /**
     * Plugin main file
     *
     * @var string
     */
    public $main;

    /**
     * Constructor
     *
     * Initializes the class with the main plugin file and adds a filter for the admin menu.
     *
     * @param string $main The main plugin file.
     */
    public function __construct($main) {
        $this->main = $main;
        add_filter('orderdetect_admin_menu', array($this, 'orderdetect_multiple_order_tracking'), PHP_INT_MAX);
        add_filter('set-screen-option', array($this, 'set_screen_option'), 10, 3);
        add_action("load-order-detect_page_multiple-order-tracking", array($this, 'add_screen_options'));
    }

    /**
     * Plugin page handler
     *
     * This method sets up the multiple-order-tracking settings page in the admin menu.
     *
     * @since   1.0.0
     * @access  public
     * @return  array $settings The settings array for the multiple-order-tracking page.
     */
    public function orderdetect_multiple_order_tracking($settings) {
        if( ! Helper::check_license(wp_parse_args(get_option('orderdetect_license'))) ) {
            return;
        }
        $settings['multiple-order-tracking']['parent_slug'] = 'order-detect';
        $settings['multiple-order-tracking']['page_title'] = __('Multiple Order Tracking', 'order-detect');
        $settings['multiple-order-tracking']['menu_title'] = __('Multiple Order Tracking', 'order-detect');
        $settings['multiple-order-tracking']['capability'] = 'manage_options';
        // Instantiate the class
        $phone_number = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $orders_list_table = new MultipleOrderTrackingList($phone_number);
        $orders_list_table->prepare_items();

        $settings['multiple-order-tracking']['callback'] = function () use ($orders_list_table, $phone_number) {
            $template = __DIR__ . '/views/multiple-order-tracking.php';

            // Pass variables to the template
            if (file_exists($template)) {
                include $template;
            }
        };

        return $settings;
    }

    /**
     * Validate screen options on update.
     *
     * @param $status Screen option value. Default false to skip.
     * @param $option The option name.
     * @param $value The number of rows to use.
     * @return bool|int
     */
    public function set_screen_option($status, $option, $value) {
        if (in_array($option, array('multi_order_tracking_per_page'), true)) {
            return $value;
        }

        return $status;
    }

    /**
     * Load outbox page assets
     */
    public function add_screen_options() {
        /**
         * Add per page option.
         */
        add_screen_option('per_page', array(
            'label'   => esc_html__('Number of items per page', 'order-detect'),
            'default' => 20,
            'option'  => 'multi_order_tracking_per_page',
        ));
    }
}
