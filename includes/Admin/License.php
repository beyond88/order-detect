<?php

namespace OrderDetect\Admin;
use OrderDetect\Helper;

/**
 * Settings Handler class
 */
class License {

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
        add_filter('orderdetect_admin_menu', array($this, 'orderdetect_license'), PHP_INT_MAX);
    }

    /**
     * Plugin page handler
     *
     * This method sets up the license settings page in the admin menu.
     *
     * @since   1.0.0
     * @access  public
     * @return  array $settings The settings array for the license page.
     */
    public function orderdetect_license($settings) {

        $helper = new Helper(); 
        $settings['license']['parent_slug'] = 'order-detect';
        $settings['license']['page_title'] = __('License', 'order-detect');
        $settings['license']['menu_title'] = __('License', 'order-detect');
        $settings['license']['capability'] = 'manage_options';
        $settings['license']['callback'] = function ($arg) use($helper) {
            $template = __DIR__ . '/views/order-detect-license.php';

            if (file_exists($template)) {
                include $template;
            }
        };

        return $settings;
    }

    /**
     * Check license expiration on frontend
     *
     * This method checks if the license has expired based on the given date
     * and displays an appropriate message on the frontend.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $date The expiration date of the license.
     * @return  void
     */
    public function license_expire_notice($date) {
        if (!empty($date)) {
            $current_date = current_time('mysql');
            if( $date === "lifetime" ) {
                echo '<h2>' . sprintf(__('<span style="color:#3c434a">License expire date:</span> %s', 'order-detect'), $date) . '</h2>';
            } else if (strtotime($current_date) > strtotime($date)) {
                echo '<div class="license-expiration-message" style="color: red; text-align: left;font-size:20px;">' . sprintf(__('Your license has been expired.', 'order-detect')) . '</div>';
            } else {
                $valid_date_str = substr($date, 0, 19);
                $date = new \DateTime($valid_date_str);
                $timestamp = $date->getTimestamp();
                $readable_date = date_i18n('F j, Y, g:i A', $timestamp);
                echo '<h2>' . sprintf(__('<span style="color:#3c434a">License expire date:</span> %s', 'order-detect'), $readable_date) . '</h2>';
            }
        }
    }

    /**
     * Generate license button
     *
     * This method generates and returns the appropriate license button (Activate/Deactivate)
     * based on the current license status.
     *
     * @since   1.0.0
     * @access  public
     * @return  string The HTML for the license button.
     */
    public function license_button() {

        $setting_options = wp_parse_args(get_option('orderdetect_license'));
        $license_key = array_key_exists('key', $setting_options) ? $setting_options['key'] : '';
        $license_expires = array_key_exists('expires', $setting_options) ? $setting_options['expires'] : '';

        if (!empty($license_key) && !empty($license_expires)) {
            $current_date = current_time('mysql');

            if( Helper::decrypt_data($license_expires, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV) === "lifetime" ) {
                return sprintf(
                    '<button type="button" name="license-deactivate" id="license-deactivate" class="btn-settings order-detect-settings-button">%s</button>',
                    __('Deactivate', 'order-detect')
                );
            }
            
            if (strtotime($current_date) > strtotime(Helper::decrypt_data($license_expires, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV))) {
                return sprintf(
                    '<button type="button" name="license-submit" id="license-submit" class="btn-settings order-detect-settings-button">%s</button>',
                    __('Activate New License', 'order-detect')
                );
            }

            return sprintf(
                '<button type="button" name="license-deactivate" id="license-deactivate" class="btn-settings order-detect-settings-button">%s</button>',
                __('Deactivate', 'order-detect')
            );
        }

        if (empty($license_key) || empty($license_expires)) {
            return sprintf(
                '<button type="button" name="license-submit" id="license-submit" class="btn-settings order-detect-settings-button">%s</button>',
                __('Activate', 'order-detect')
            );
        }
    }
}
