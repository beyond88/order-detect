<?php

namespace OrderBarrier;

/**
 * Installer class
 */
class Installer
{

    /**
     * Run the installer
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    public function run()
    {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    public function add_version()
    {
        $installed = get_option('orderbarrier_installed');

        if (!$installed) {
            update_option('orderbarrier_installed', time());
        }

        update_option('orderbarrier_version', ORDERBARRIER_VERSION);
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'otp_verification';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            phone_number varchar(15) NOT NULL,
            otp varchar(6) NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY phone_number (phone_number)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $is_error = empty($wpdb->last_error);
        return $is_error;
    }
}
