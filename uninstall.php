<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * OrderDetect Uninstall
 *
 * Uninstalling OrderDetect deletes user roles, tables, pages, meta data and options.
 *
 * @since   3.2.15
 *
 * @package OrderDetect\Uninstaller
 */
class OrderDetect_Uninstaller {
    /**
     * Constructor for the class OrderDetect_Uninstaller
     *
     * @since 3.2.15
     */
    public function __construct() {
        global $wpdb;

        $this->drop_tables();

        $this->delete_options();

        // Clear any cached data that has been removed.
        wp_cache_flush();

    }

    /**
     * Return a list of tables. Used to make sure all OrderDetect tables are dropped
     * when uninstalling the plugin
     *
     * @since 3.2.15
     *
     * @return array OrderDetect tables.
     */
    private function get_tables() {
        return [
            'otp_log'
        ];
    }

    /**
     * Drop all tables created by OrderDetect Lite and Pro
     *
     * @since 3.2.15
     *
     * @return void
     */
    private function drop_tables() {
        global $wpdb;

        $tables = $this->get_tables();

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" ); // phpcs:ignore
        }
    }

    /**
     * Delete OrderDetect and OrderDetect Pro related user metas
     *
     * @since 3.7.12
     *
     * @return void
     */
    private function delete_options() {
        global $wpdb;

        $options = [
            'orderdetect_settings',
            'orderdetect_license',
            'orderdetect_sms_balance',
            'orderdetect_installed'
        ];

        foreach ( $options as $option ) {
            get_option($option); // phpcs:ignore
        }
    }
}

new Dokan_Uninstaller();
