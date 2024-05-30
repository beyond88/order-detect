<?php

// Initialize the current directory with the file's directory
$current_directory = dirname(__FILE__);

// Define the number of levels to traverse up the directory tree
$levels_to_traverse = 5; // Adjust this according to your directory structure

// Traverse up the directory tree by calling dirname() in a loop
for ($i = 0; $i < $levels_to_traverse; $i++) {
    $current_directory = dirname($current_directory);
}

// Define the path to the WordPress tests directory
$_tests_dir = $current_directory . '/wordpress-develop/tests/phpunit';


if (!$_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

// Manually load the plugin being tested.
function _manually_load_plugin() {
    require dirname(__DIR__) . '/order-shield.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Load the PHPUnit Polyfills library.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';