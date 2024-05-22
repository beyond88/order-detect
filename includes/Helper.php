<?php

namespace OrderShield;

/**
 * Installer class
 */
class Helper
{

    /**
     * Check the validity of the license
     *
     * This method verifies the license key from the provided settings array.
     * It ensures that the license is valid and returns the appropriate status.
     *
     * @since	1.0.0
     * @access	public
     * @param	array $settings The settings array containing the license key.
     * @return	void
     */
    public static function check_license($settings)
    {
        if (!isset($settings)) {
            return false;
        }

        $license_key = array_key_exists('license_key', $settings) ? $settings['license_key'] : '';
        $license_expires = array_key_exists('license_expires', $settings) ? $settings['license_expires'] : '';
        if (!empty($license_key) && !empty($license_expires)) {
            $current_date = current_time('mysql');
            if (strtotime($current_date) > strtotime($license_expires)) {
                return false;
            }
        }
        return true;
    }
}
