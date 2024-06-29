<?php
namespace OrderDetect\API;

use OrderDetect\Helper;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Handles communication with the LicenseAPI API.
 */
class LicenseAPI {

    public function __construct() {
        // Register the custom REST API routes when the class is instantiated
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Register the custom REST API routes.
     */
    public function register_routes() {
        register_rest_route('orderdetect/v1', '/deactivate-license/', [
            'methods' => 'POST',
            'callback' => array($this, 'deactivate_license'),
            'permission_callback' => '__return_true', // You should implement proper permission checks here
        ]);
    }

    /**
     * Handle the deactivation of the license.
     * 
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The response object with status and message.
     */
    public function deactivate_license(WP_REST_Request $request) {

        $license_key = $request->get_param('license_key');
        $setting_options = wp_parse_args(get_option('orderdetect_license'));
        $stored_license_key = array_key_exists('key', $setting_options) ? $setting_options['key'] : '';
        $stored_license_key = Helper::decrypt_data($stored_license_key, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);

        if ($license_key === $stored_license_key) {
            delete_option('orderdetect_license');
            return new WP_REST_Response(['status' => 'success'], 200);
        } else {
            return new WP_REST_Response(['status' => 'failure', 'message' => 'License key does not match'], 400);
        }
    }
}