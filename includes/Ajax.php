<?php

namespace OrderShield;

use OrderShield\API\OrderShieldAPI;

/**
 * Ajax handler class
 */
class Ajax
{

    private $api;
    private $settings;

    /**
     * Class constructor
     *
     * Initializes the class by retrieving the plugin settings and adding AJAX actions for license activation, deactivation, sending OTP, and verifying OTP.
     */
    function __construct()
    {
        $this->api = new OrderShieldAPI();
        $this->settings = get_option('ordershield_settings');

        // License activate
        add_action('wp_ajax_license_activate', array($this, 'license_activate'));
        add_action('wp_ajax_nopriv_license_activate', array($this, 'license_activate'));

        // License deactivate
        add_action('wp_ajax_license_deactivate', array($this, 'license_deactivate'));
        add_action('wp_ajax_nopriv_license_deactivate', array($this, 'license_deactivate'));

        // Send OTP
        add_action('wp_ajax_send_otp', array($this, 'send_otp'));
        add_action('wp_ajax_nopriv_send_otp', array($this, 'send_otp'));

        // Verify OTP
        add_action('wp_ajax_verify_otp', array($this, 'verify_otp'));
        add_action('wp_ajax_nopriv_verify_otp', array($this, 'verify_otp'));
    }

    /**
     * License activation handler
     *
     * Handles the AJAX request for activating the license. Verifies the license key by sending a request to the store URL and updates the settings accordingly.
     *
     * @return void
     */
    public function license_activate()
    {
        // Check for nonce security
        check_ajax_referer('order-shield-admin-nonce', 'security');

        $license_key = sanitize_text_field($_POST['license_key']);
        if (isset($license_key)) {
            $license_key = sanitize_text_field($_POST['license_key']);
            $api_params = array(
                'edd_action' => 'activate_license',
                'sslverify' => false,
                'timeout'   => 60,
                'license'    => $license_key,
                'item_name'  => urlencode(ORDERSHIELD_SL_ITEM_NAME),
                'item_id'    => urlencode(ORDERSHIELD_SL_ITEM_ID),
                'url'        => home_url()
            );

            $response = wp_remote_post(esc_url(ORDERSHIELD_STORE_URL), array('body' => $api_params));

            if (is_wp_error($response)) {
                wp_send_json(array('message' => $response->get_error_message(), 'class' => 'order-shield-license-status-error'), 500);
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->success) {
                $settings = [];
                $settings['key'] = $license_key;
                $settings['expires'] = $license_data->expires;
                update_option('ordershield_license', $settings);
                wp_send_json(array('message' => 'License activated successfully.', 'class' => 'order-shield-license-status-success'), 200);
            } else {
                wp_send_json(array('message' => 'License activation failed: ' . $license_data->error, 'class' => 'order-shield-license-status-error'), 400);
            }
        } else {
            wp_send_json(array('message' => 'License key invalid!', 'class' => 'order-shield-license-status-error'), 400);
        }

        wp_die();
    }

    /**
     * License deactivation handler
     *
     * Handles the AJAX request for deactivating the license. Sends a request to the store URL and updates the settings accordingly.
     *
     * @return void
     */
    public function license_deactivate()
    {
        // Check for nonce security
        check_ajax_referer('order-shield-admin-nonce', 'security');

        $ordershield_license = get_option('ordershield_license');
        $license_key = $ordershield_license['key'];
        if ($license_key) {
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'sslverify' => false,
                'timeout'   => 60,
                'license'    => $license_key,
                'item_name'  => urlencode(ORDERSHIELD_SL_ITEM_NAME),
                'url'        => home_url()
            );

            $response = wp_remote_post(esc_url(ORDERSHIELD_STORE_URL), array('body' => $api_params));

            if (is_wp_error($response)) {
                wp_send_json(array('message' => $response->get_error_message(), 'class' => 'order-shield-license-status-error'), 500);
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->success) {
                $settings = [];
                $settings['key'] = '';
                $settings['expires'] = '';
                update_option('ordershield_license', $settings);
                wp_send_json(array('message' => 'License deactivated successfully.', 'class' => 'order-shield-license-status-success'), 200);
            } else {
                wp_send_json(array('message' => 'License deactivation failed: ' . $license_data->error, 'class' => 'order-shield-license-status-error'), 400);
            }
        } else {
            wp_send_json(array('message' => 'License key not found.', 'class' => 'order-shield-license-status-error'), 400);
        }

        wp_die();
    }

    /**
     * Send OTP handler
     *
     * Handles the AJAX request for sending an OTP to the provided phone number. Validates the phone number and generates an OTP if valid.
     *
     * @return void
     */
    public function send_otp()
    {
        check_ajax_referer('order-shield-nonce', 'security');

        $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
        $response = array();

        if (!Helper::is_valid_Bangladeshi_phone_number($phone_number)) {
            $response['success'] = false;
            $response['message'] = 'Invalid phone number format. Please enter a valid Bangladeshi phone number.';
            wp_send_json($response, 400);
        }

        $otp = Helper::generate_otp($phone_number);

        if (!$otp) {
            $response['success'] = false;
            $response['message'] = 'Failed to generate OTP. Please try again later.';
            wp_send_json($response, 500);
        }

        $enable = array_key_exists('enable_otp', $this->settings) ? $this->settings['enable_otp'] : '';
        $endpoint = array_key_exists('sms_api_endpoint', $this->settings) ? $this->settings['sms_api_endpoint'] : '';
        $api_key = array_key_exists('sms_api_key', $this->settings) ? $this->settings['sms_api_key'] : '';

        if (isset($enable) && isset($endpoint) && isset($api_key)) {
            $message = 'Your OTP code: ' . $otp;
            $params = [
                'api_key' => $api_key,
                'msg' => $message,
                'to' => $phone_number,
            ];

            $sms_response = $this->api->post(esc_url($endpoint . 'sendsms'), $params);

            $balance_response = Helper::getBalance($endpoint, $api_key);
            Helper::send_sms_balance_notification();

            if ($balance_response && $balance_response->error === 0) {
                $balance = $balance_response->data->balance;
                update_option('ordershield_sms_balance', $balance);
            } elseif ($balance_response && $balance_response->error === 405) {
                error_log('Please configure SMS API first.');
            } else {
                error_log('Unknown Error, failed to fetch balance');
            }
        }

        $response['success'] = true;
        $response['message'] = 'OTP has been sent to your phone number.';
        wp_send_json($response, 200);
        wp_die();
    }

    /**
     * Verify OTP handler
     *
     * Handles the AJAX request for verifying the provided OTP. Checks if the OTP is valid and returns the appropriate response.
     *
     * @return void
     */
    public function verify_otp()
    {
        check_ajax_referer('order-shield-nonce', 'security');

        $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
        $otp = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']) : '';

        $response = array();

        $otp_verified = Helper::verify_otp($phone_number, $otp);

        if ($otp_verified) {
            $response['success'] = true;
            $response['message'] = 'OTP verification successful.';
        } else {
            $response['success'] = false;
            $response['message'] = 'OTP verification failed. Invalid OTP.';
        }

        wp_send_json($response, 200);
        wp_die();
    }
}
