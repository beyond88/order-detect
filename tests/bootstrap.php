<?php
// Load WordPress test environment
require_once '/Users/mohiuddinabdulkader/Sites/wordpress-develop/tests/phpunit/includes/functions.php';

// Load the plugin
function _manually_load_plugin()
{
    require dirname(__DIR__) . '/order-shield.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment
require '/Users/mohiuddinabdulkader/Sites/wordpress-develop/tests/phpunit/includes/bootstrap.php';
