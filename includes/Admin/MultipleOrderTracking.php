<?php

namespace OrderShield\Admin;

use OrderShield\Admin\MultipleOrderTrackingList;

/**
 * Settings Handler class
 */
class MultipleOrderTracking
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
        add_filter('ordershield_admin_menu', array($this, 'ordershield_multiple_order_tracking'), PHP_INT_MAX);
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
    public function ordershield_multiple_order_tracking($settings)
    {
        $settings['multiple']['parent_slug'] = 'order-shield';
        $settings['multiple']['page_title'] = __('Multiple Order Tracking', 'order-shield');
        $settings['multiple']['menu_title'] = __('Multiple Order Tracking', 'order-shield');
        $settings['multiple']['capability'] = 'manage_options';
        $settings['multiple']['callback'] = function ($arg) {
            $template = __DIR__ . '/views/multiple-order-tracking.php';

            if (file_exists($template)) {
                include $template;
            }
        };

        return $settings;
    }
}
