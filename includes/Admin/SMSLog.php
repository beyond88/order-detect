<?php

namespace OrderDetect\Admin;

use OrderDetect\Admin\OTPLogList;
use OrderDetect\Helper;

/**
 * Settings Handler class
 */
class SMSLog
{

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
    public function __construct($main)
    {
        $this->main = $main;
        add_filter('orderdetect_admin_menu', array($this, 'orderdetect_sms_log'), PHP_INT_MAX);
        add_filter('set-screen-option', array($this, 'set_screen_option'), 10, 3);
        add_action("load-order-detect_page_sms-log", array($this, 'add_screen_options'));
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
    public function orderdetect_sms_log($settings)
    {
        if( ! Helper::check_license(wp_parse_args(get_option('orderdetect_license'))) ) {
            return;
        }
        $settings['sms-log']['parent_slug'] = 'order-detect';
        $settings['sms-log']['page_title'] = __('SMS Log', 'order-detect');
        $settings['sms-log']['menu_title'] = __('SMS Log', 'order-detect');
        $settings['sms-log']['capability'] = 'manage_options';
        // Instantiate the class
        $phone_number = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $sms_list_table = new SMSLogList($phone_number);
        $sms_list_table->prepare_items();

        $settings['sms-log']['callback'] = function () use ($sms_list_table, $phone_number) {
            $template = __DIR__ . '/views/sms-log.php';

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
    public function set_screen_option($status, $option, $value)
    {
        if (in_array($option, array('sms_log_per_page'), true)) {
            return $value;
        }

        return $status;
    }

    /**
     * Load outbox page assets
     */
    public function add_screen_options()
    {
        /**
         * Add per page option.
         */
        add_screen_option('per_page', array(
            'label'   => esc_html__('Number of items per page:', 'order-detect'),
            'default' => 20,
            'option'  => 'sms_log_per_page',
        ));
    }
}
