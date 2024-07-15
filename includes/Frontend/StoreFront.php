<?php
namespace OrderDetect\Frontend;

use OrderDetect\Helper;

/**
 * Ajax handler class
 */
class StoreFront {

    private $api;
    private $settings;
    private $form;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->settings = get_option('orderdetect_settings');
        add_filter('woocommerce_locate_template', array($this, 'set_locate_template'), PHP_INT_MAX, 3);
        add_action('wp_footer', array($this, 'init_otp_modal_checkout'));
    }



    /**
     * Return plugin directory
     *
     * @since	1.0.0
     * @access	public
     * @param	none
     * @return	string
     */
    public static function get_plugin_path() {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Return WooCommerce template path
     * 
     * @since	1.0.0
     * @access	public
     * @param	string, string, string
     * @return	string
     */
    public function set_locate_template($template, $template_name, $template_path) {

        global $woocommerce;
        $_template = $template;
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }

        $plugin_path  = self::get_plugin_path() . '/views/woocommerce/';
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if (!$template && file_exists($plugin_path . $template_name))
            $template = $plugin_path . $template_name;

        if (!$template)
            $template = $_template;

        return $template;
    }

    /**
     * Initialize OTP modal during checkout
     *
     * This method adds the OTP verification modal to the checkout page
     * if OTP verification is enabled in the settings. It includes the
     * HTML structure for the modal and its various elements.
     *
     * @since	1.0.0
     * @access	public
     * @param	none
     * @return	void
     */
    public function init_otp_modal_checkout() {
        if (is_checkout() && array_key_exists('enable_otp', $this->settings)) {
            echo Form::otp_form();
        }
    }
}
