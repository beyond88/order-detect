<?php

use OrderShield\Installer;

class InstallerTest extends WP_UnitTestCase {

    /**
     * Test the run method
     */
    public function test_run() {
        // Create an instance of the Installer class
        $installer = new Installer();

        // Run the installer
        $installer->run();

        // Check if the version and install time were added
        $this->assertNotEmpty(get_option('ordershield_installed'));
        $this->assertEquals(ORDERSHIELD_VERSION, get_option('ordershield_version'));

        // Check if the database table was created
        global $wpdb;
        $table_name = $wpdb->prefix . 'otp_verification';
        $this->assertEquals($table_name, $wpdb->get_var("SHOW TABLES LIKE '$table_name'"));
    }

    /**
     * Test the add_version method
     */
    public function test_add_version() {
        // Create an instance of the Installer class
        $installer = new Installer();

        // Run the add_version method
        $installer->add_version();

        // Check if the version and install time were added
        $this->assertNotEmpty(get_option('ordershield_installed'));
        $this->assertEquals(ORDERSHIELD_VERSION, get_option('ordershield_version'));

        // Check if the install time remains the same after re-running the method
        $installed_time = get_option('ordershield_installed');
        $installer->add_version();
        $this->assertEquals($installed_time, get_option('ordershield_installed'));
    }

    /**
     * Test the create_tables method
     */
    public function test_create_tables() {
        global $wpdb;

        // Drop the table if it exists to ensure a clean state
        $table_name = $wpdb->prefix . 'otp_verification';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Create an instance of the Installer class
        $installer = new Installer();

        // Run the create_tables method
        $is_error = $installer->create_tables();

        // Check if the table was created successfully
        $this->assertTrue($is_error);
        $this->assertEquals($table_name, $wpdb->get_var("SHOW TABLES LIKE '$table_name'"));

        // Check if the table has the correct structure
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
        $expected_columns = ['id', 'phone_number', 'otp', 'expires_at'];
        foreach ($columns as $column) {
            $this->assertContains($column->Field, $expected_columns);
        }
    }
}
