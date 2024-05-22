<?php

namespace OrderShield\Admin;

/**
 * Settings Handler class
 */
class License
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
        add_filter('ordershield_admin_menu', array($this, 'ordershield_license'), PHP_INT_MAX);
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
    public function ordershield_license()
    {
        $settings = array();
        $settings['license']['parent_slug'] = 'order-shield';
        $settings['license']['page_title'] = __('License', 'order-shield');
        $settings['license']['menu_title'] = __('License', 'order-shield');
        $settings['license']['capability'] = 'manage_options';
        $settings['license']['callback'] = function ($arg) {
            $template = __DIR__ . '/views/order-shield-license.php';

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
    public function check_license_expiration_frontend($date)
    {
        if (!empty($date)) {
            $current_date = current_time('mysql');
            if (strtotime($current_date) > strtotime($date)) {
                echo '<div class="license-expiration-message" style="color: red; text-align: left;font-size:20px;">' . sprintf(__('Your license has been expired.', 'order-shield')) . '</div>';
            } else {
                $valid_date_str = substr($date, 0, 19);
                $date = new \DateTime($valid_date_str);
                $timestamp = $date->getTimestamp();
                $readable_date = date_i18n('F j, Y, g:i A', $timestamp);
                echo '<h2>' . sprintf(__('<span style="color:#3c434a">License expire date:</span> %s', 'order-shield'), $readable_date) . '</h2>';
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
    public function license_button()
    {
        $option_name = $this->main->_optionName;
        $default_options = $this->main->_defaultOptions;
        $setting_options = wp_parse_args(get_option($option_name), $default_options);
        $license_key = $setting_options['license_key'];
        $license_expires = $setting_options['license_expires'];

        if (!empty($license_key) && !empty($license_expires)) {
            $current_date = current_time('mysql');
            if (strtotime($current_date) > strtotime($license_expires)) {
                return sprintf(
                    '<button type="button" name="license-submit" id="license-submit" class="btn-settings order-shield-settings-button">%s</button>',
                    __('Activate', 'order-shield')
                );
            }

            return sprintf(
                '<button type="button" name="license-deactivate" id="license-deactivate" class="btn-settings order-shield-settings-button">%s</button>',
                __('Deactivate', 'order-shield')
            );
        }

        if (empty($license_key) || empty($license_expires)) {
            return sprintf(
                '<button type="button" name="license-submit" id="license-submit" class="btn-settings order-shield-settings-button">%s</button>',
                __('Activate', 'order-shield')
            );
        }
    }
}
