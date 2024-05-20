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

    public function __construct($main)
    {
        $this->main = $main;
        add_filter('ordershield_admin_menu', array($this, 'ordershield_license'), PHP_INT_MAX);
        add_action('admin_init', array($this, 'ordershield_save_license'));
    }

    /**
     * Plugin page handler
     *
     * @return void
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

    public function ordershield_save_license()
    {

        if (isset($_POST['order_shield_nonce_field']) && wp_verify_nonce($_POST['order_shield_nonce_field'], 'order_shield_nonce')) {
            if (isset($_POST['ordershield_license_key'])) {
                $ordershield_license_key = sanitize_text_field($_POST['ordershield_license_key']);
                $option_name = $this->main->_optionName;
                $default_options = $this->main->_defaultOptions;
                $setting_options = wp_parse_args(get_option($option_name), $default_options);
                $setting_options['license_key'] = $ordershield_license_key;

                update_option($option_name, $setting_options);

                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>License is updated!</p></div>';
                });
            }
        }
    }
}
