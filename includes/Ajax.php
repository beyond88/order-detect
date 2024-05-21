<?php

namespace OrderShield;

/**
 * Ajax handler class
 */
class Ajax
{

    private $settings;

    /**
     * Class constructor
     */
    function __construct()
    {
        $this->settings = get_option('ordershield_settings');
        //License activate
        add_action('wp_ajax_license_activate', array($this, 'license_activate'));
        add_action('wp_ajax_nopriv_license_activate', array($this, 'license_activate'));

        add_action('wp_ajax_license_deactivate', array($this, 'license_deactivate'));
        add_action('wp_ajax_nopriv_license_deactivate', array($this, 'license_deactivate'));
    }

    public function license_activate()
    {
        check_ajax_referer('order-shield-admin-nonce', 'security');
        $license_key = sanitize_text_field($_POST['license_key']);
        if (isset($license_key)) {
            $license_key = sanitize_text_field($_POST['license_key']);
            $api_params = array(
                'edd_action' => 'activate_license',
                'sslverify' => false,
                'timeout'   => 40,
                'license'    => $license_key,
                'item_name'  => urlencode(ORDERSHIELD_SL_ITEM_NAME),
                'item_id'    => urlencode(ORDERSHIELD_SL_ITEM_ID),
                'url'        => home_url()
            );

            $response = wp_remote_post(esc_url(ORDERSHIELD_STORE_URL), array('body' => $api_params));

            error_log('license response:' . print_r($response, true));

            if (is_wp_error($response)) {
                wp_send_json(array('message' => 'HTTP request failed.', 'class' => 'order-shield-license-status-error'), 500);
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->success) {
                $this->settings['license_key'] = $license_key;
                $this->settings['license_expires'] = $license_data->expires;
                update_option('ordershield_settings', $this->settings);
                wp_send_json(array('message' => 'License activated successfully.', 'class' => 'order-shield-license-status-success'), 200);
            } else {
                wp_send_json(array('message' => 'License activation failed: ' . $license_data->error, 'class' => 'order-shield-license-status-error'), 400);
            }
        } else {
            wp_send_json(array('message' => 'License key invalid!.', 'class' => 'order-shield-license-status-error'), 400);
        }

        wp_die();
    }

    public function license_deactivate()
    {
        // Check for nonce security
        check_ajax_referer('order-shield-admin-nonce', 'security');

        $license_key = $this->settings['license_key'];
        if ($license_key) {
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license'    => $license_key,
                'item_name'  => urlencode(ORDERSHIELD_SL_ITEM_NAME),
                'url'        => home_url()
            );

            $response = wp_remote_post(esc_url(ORDERSHIELD_STORE_URL), array('body' => $api_params));

            if (is_wp_error($response)) {
                wp_send_json(array('message' => 'HTTP request failed.', 'class' => 'order-shield-license-status-error'), 500);
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->success) {
                $this->settings['license_key'] = '';
                $this->settings['license_expires'] = '';
                update_option('ordershield_settings', $this->settings);
                wp_send_json(array('message' => 'License deactivated successfully.', 'class' => 'order-shield-license-status-success'), 200);
            } else {
                wp_send_json(array('message' => 'License deactivation failed: ' . $license_data->error, 'class' => 'order-shield-license-status-error'), 400);
            }
        } else {
            wp_send_json(array('message' => 'License key not found.', 'class' => 'order-shield-license-status-error'), 400);
        }

        wp_die();
    }
}
