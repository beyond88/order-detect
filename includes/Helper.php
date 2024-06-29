<?php
namespace OrderDetect;

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
        if (empty($settings)) {
            return false;
        }

        $key = array_key_exists('key', $settings) ? $settings['key'] : '';
        $expires = array_key_exists('expires', $settings) ? $settings['expires'] : '';
        if (!empty($key) && !empty($expires)) {
            $current_date = current_time('mysql');
            if( Helper::decrypt_data($expires, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV) === "lifetime" ){
                return true;
            }
            if (strtotime($current_date) > strtotime(Helper::decrypt_data($expires, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV))) {
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
        $table_name = $wpdb->prefix . 'od_otp_log';

        do {
            $random_number = rand(0, 9999);
            $otp = str_pad($random_number, $length, '0', STR_PAD_LEFT);

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE phone_number = %s AND code = %s",
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
        $table_name = $wpdb->prefix . 'od_otp_log';
        date_default_timezone_set('Asia/Dhaka'); // Set timezone to Dhaka

        $otp = Helper::generate_unique_otp_for_phone($phone_number);
        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Set expiration time to 5 minutes from now

        $wpdb->replace(
            $table_name,
            array(
                'phone_number' => $phone_number,
                'code' => $otp,
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
        $table_name = $wpdb->prefix . 'od_otp_log';

        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE phone_number = %s AND code = %s AND expires_at > NOW()",
            $phone_number,
            $otp
        ));

        return (bool) $result;
    }

    public static function set_phone_number_verified($phone_number) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'od_otp_log';
        $wpdb->update(
            $table_name,
            array('is_verified' => true),
            array('phone_number' => $phone_number)
        );
    }

    public static function is_phone_number_verified($phone_number) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'od_otp_log'; // Assuming $table_name is the correct name of your table
    
        // Prepare the SQL query
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE phone_number = %s AND is_verified = %d",
            $phone_number,
            1 // Assuming 'is_verified' is stored as boolean or integer value where 1 means true
        );
    
        // Execute the query
        $count = $wpdb->get_var($sql);
    
        // Return true if count is greater than 0, indicating the phone number is verified
        return $count > 0;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $postfields
     * @return bool|string
     */
    public static function send_request($url, $method = 'GET', $postfields = [])
    {

        $args = [
            'method'    => $method,
            'timeout'   => 9999,
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
    public static function get_balance($url, $api_key)
    {
        $response = Helper::send_request($url . '/user/balance/?api_key=' . $api_key);

        return json_decode($response);
    }

    /**
     * Check SMS balance and send email notification to shop owner.
     */
    public static function send_sms_balance_notification( $sms_balance = null ) {

        $sms_balance = get_option('orderdetect_sms_balance', 0);

        if ($sms_balance <= 50 && ( $sms_balance == 50 || $sms_balance == 40 || $sms_balance == 30 || $sms_balance == 20 || $sms_balance == 10 || $sms_balance == 2 ) ) {
            $to = get_option('admin_email');
            $subject = 'Low SMS Balance Notification';
            $message = "Dear shop owner,\n\n";
            $message .= "This is to inform you that your SMS balance has reached ".$sms_balance." taka.\n";
            $message .= "Please refill your SMS balance to ensure uninterrupted service for your customers.\n\n";
            $message .= "Current SMS balance: " . $sms_balance . " taka\n\n";
            $message .= "Best regards,\n";
            $message .= "OrderDetect";

            wp_mail($to, $subject, $message);
        }
    }

    /**
     * Encrypt data
     *
     * This method encrypts the given target data using the AES-256-CBC cipher method.
     * The provided key and initialization vector (IV) are used for encryption.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $target_data The data to be encrypted.
     * @param   string $key The encryption key (default is ORDERDETECT_ENCRYPTION_KEY).
     * @param   string $iv The initialization vector (default is ORDERDETECT_IV).
     * @return  string The encrypted data, encoded in base64.
     */
    public static function encrypt_data($target_data, $key = ORDERDETECT_ENCRYPTION_KEY, $iv = ORDERDETECT_IV) {
        $cipher_method = 'aes-256-cbc';
        $encrypted = openssl_encrypt($target_data, $cipher_method, base64_decode($key), 0, base64_decode($iv));
        return base64_encode($encrypted);
    }

    /**
     * Decrypt data
     *
     * This method decrypts the given encrypted data using the AES-256-CBC cipher method.
     * The provided key and initialization vector (IV) are used for decryption.
     *
     * @since   1.0.0
     * @access  public
     * @param   string $encrypted_data The data to be decrypted, encoded in base64.
     * @param   string $key The decryption key (default is ORDERDETECT_ENCRYPTION_KEY).
     * @param   string $iv The initialization vector (default is ORDERDETECT_IV).
     * @return  string The decrypted data.
     */
    public static function decrypt_data($encrypted_data, $key = ORDERDETECT_ENCRYPTION_KEY, $iv = ORDERDETECT_IV) {
        $cipher_method = 'aes-256-cbc';
        $decrypted = openssl_decrypt(base64_decode($encrypted_data), $cipher_method, base64_decode($key), 0, base64_decode($iv));
        return $decrypted;
    }

    /**
     * Mask a string
     *
     * This method masks the given input string by replacing all characters with asterisks (*).
     *
     * @since   1.0.0
     * @access  public
     * @param   string $input The input string to be masked.
     * @return  string The masked string with all characters replaced by asterisks.
     */
    public static function mask_string($input) {
        $length = strlen($input);
        return str_repeat('*', $length);
    }

    
}