<?php

namespace OrderDetect\Frontend;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use OrderDetect\API\OrderDetectAPI;
use OrderDetect\Helper;

/**
 * Ajax handler class
 */
class StoreFront
{

    private $api;
    private $settings;
    private $form;

    /**
     * Class constructor
     */
    public function __construct()
    {
        // $this->api = new OrderDetectAPI();
        $this->settings = get_option('orderdetect_settings');
        add_filter('woocommerce_locate_template', array($this, 'set_locate_template'), PHP_INT_MAX, 3);
        add_action('wp_footer', array($this, 'init_otp_modal_checkout'));
        // add_action('woocommerce_checkout_process', array($this, 'check_otp_status_before_submit'), PHP_INT_MAX);
    }

    /**
     * Check OTP status before form submission
     *
     * This method checks if OTP verification is enabled in the settings.
     * If enabled, it adds a notice indicating OTP verification failed.
     * 
     * @since	1.0.0
     * @access	public
     * @param	none
     * @return	void
     */
    public function check_otp_status_before_submit()
    {

        if (array_key_exists('enable_otp', $this->settings)) {

            $billing_phone = $_POST['billing_phone'];

            $is_verified = Helper::is_phone_number_verified($billing_phone);

            if ( ! isset( $_POST['billing_otp'] ) && ! $is_verified ) {
                wc_add_notice(__('OTP verification failed. Please try again.', 'order-detect'), 'error');
            }

            if ( isset( $_POST['billing_otp'] ) && empty( $_POST['billing_otp'] ) ) {
                wc_add_notice(__('OTP verification failed. Please try again.', 'order-detect'), 'error');
            }
    
            if ( isset( $_POST['billing_otp'] ) && ! empty( $_POST['billing_otp'] ) ) {
                
                $otp = sanitize_text_field($_POST['billing_otp']);
                $otp_verified = Helper::verify_otp($billing_phone, $otp);
                if ( ! $otp_verified ) {
                    wc_add_notice(__('OTP verification failed. Please try again.', 'order-detect'), 'error');
                }
    
                if ( $otp_verified ) {
                    Helper::set_phone_number_verified($billing_phone);
                }
            }

        }
        
    }

    /**
     * Return plugin directory
     *
     * @since	1.0.0
     * @access	public
     * @param	none
     * @return	string
     */
    public static function get_plugin_path()
    {
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
    public function set_locate_template($template, $template_name, $template_path)
    {

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
    public function init_otp_modal_checkout()
    {
        if (is_checkout() && array_key_exists('enable_otp', $this->settings)) {
            echo Form::otp_form();
        }
    }
}
