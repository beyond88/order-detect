<?php

namespace OrderShield;

/**
 * Helper class
 */
class Helper
{
    /**
     * Check the validity of the license
     *
     * This method verifies the license key from the provided settings array.
     * It ensures that the license is valid and returns the appropriate status.
     *
     * @since   1.0.0
     * @access  public
     * @param   array $settings The settings array containing the license key.
     * @return  bool True if the license is valid, false otherwise.
     */
    public static function check_license($settings)
    {
        if (!isset($settings)) {
            return false;
        }

        $key = array_key_exists('key', $settings) ? $settings['key'] : '';
        $expires = array_key_exists('expires', $settings) ? $settings['expires'] : '';
        if (!empty($key) && !empty($expires)) {
            $current_date = current_time('mysql');
            if (strtotime($current_date) > strtotime($expires)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate a Bangladeshi phone number
     *
     * This method uses a regular expression to validate a Bangladeshi phone number.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $phoneNumber The phone number to validate.
     * @return  bool True if the phone number is valid, false otherwise.
     */
    public static function is_valid_Bangladeshi_phone_number($phoneNumber)
    {
        $regex = '/^(?:\+?88)?01[1-9]\d{8}$/';
        return preg_match($regex, $phoneNumber);
    }

    /**
     * Generate a unique OTP for a phone number
     *
     * This method generates a 4-digit unique OTP for the given phone number.
     * It ensures that the generated OTP does not already exist for the phone number.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $phone_number The phone number for which the OTP is generated.
     * @param   int    $length The length of the OTP, default is 4.
     * @return  string The generated OTP.
     */
    public static function generate_unique_otp_for_phone($phone_number, $length = 4)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'otp_verification';

        do {
            $random_number = rand(0, 9999);
            $otp = str_pad($random_number, $length, '0', STR_PAD_LEFT);

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE phone_number = %s AND otp = %s",
                $phone_number,
                $otp
            ));
        } while ($exists);
        return $otp;
    }

    /**
     * Generate an OTP for a phone number
     *
     * This method generates a unique OTP for the given phone number and stores it
     * in the database with an expiration time of 5 minutes.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $phone_number The phone number for which the OTP is generated.
     * @return  string The generated OTP.
     */
    public static function generate_otp($phone_number)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'otp_verification';
        date_default_timezone_set('Asia/Dhaka'); // Set timezone to Dhaka

        $otp = Helper::generate_unique_otp_for_phone($phone_number);
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Set expiration time to 5 minutes from now

        $wpdb->replace(
            $table_name,
            array(
                'phone_number' => $phone_number,
                'otp' => $otp,
                'expires_at' => $expires_at,
            ),
            array(
                '%s',
                '%s',
                '%s'
            )
        );

        return $otp;
    }

    /**
     * Verify an OTP for a phone number
     *
     * This method verifies if the provided OTP is valid for the given phone number
     * and has not expired.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $phone_number The phone number associated with the OTP.
     * @param   string $otp The OTP to verify.
     * @return  bool True if the OTP is valid, false otherwise.
     */
    public static function verify_otp($phone_number, $otp)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'otp_verification';

        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE phone_number = %s AND otp = %s AND expires_at > NOW()",
            $phone_number,
            $otp
        ));

        return (bool) $result;
    }

    /**
     * Allow user to use OTP
     *
     * This method checks if the logged-in user is allowed to use OTP based on
     * the stored `next_otp_check` time. It returns true if the OTP is still valid,
     * false otherwise.
     *
     * @since   1.0.0
     * @access  public
     * @return  bool True if the user is allowed to use OTP, false otherwise.
     */
    public static function skip_otp_form()
    {
        if (is_user_logged_in()) {
            date_default_timezone_set('Asia/Dhaka'); // Set timezone to Dhaka
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $next_otp_check = get_user_meta($user_id, 'next_otp_check', true); // Retrieve the next OTP check time

            if ($next_otp_check) {
                $current_time = time(); // Get the current Unix timestamp
                if ($current_time > strtotime($next_otp_check)) {
                    return false; // Allow OTP has expired
                } else {
                    return true; // Allow OTP is still valid
                }
            } else {
                return false; // No next OTP check time found
            }
        }
        return false; // User is not logged in
    }

    /**
     * @param $url
     * @param string $method
     * @param array $postfields
     * @return bool|string
     */
    private static function sendRequest($url, $method = 'GET', $postfields = [])
    {

        $args = [
            'method'    => $method,
            'timeout'   => 45,
            'sslverify' => false,
            'body'      => $postfields
        ];

        $request = wp_remote_post($url, $args);

        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200) {
            return false;
        }

        return wp_remote_retrieve_body($request);
    }

    /**
     * @return mixed
     */
    public static function getBalance($url, $api_key)
    {
        $response = Helper::sendRequest($url . '/user/balance/?api_key=' . $api_key);

        return json_decode($response);
    }
}
