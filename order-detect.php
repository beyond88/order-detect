<?php

/**
 * Plugin Name: Order Detect
 * Description: Secure your store with phone verification, multi-order tracking, and parcel trust scores for smarter order management and reduced fraud.
 * Plugin URI: https://github.com/beyond88/order-detect
 * Author: Mohiuddin Abdul Kader
 * Author URI: https://github.com/beyond88
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       order-detect
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.5.2
 * @package Order Detect
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html 
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class OrderDetect
{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes a singleton instance
     *
     * @return \OrderDetect
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('ORDERDETECT_VERSION', self::version);
        define('ORDERDETECT_FILE', __FILE__);
        define('ORDERDETECT_PATH', __DIR__);
        define('ORDERDETECT_URL', plugins_url('', ORDERDETECT_FILE));
        define('ORDERDETECT_ASSETS', ORDERDETECT_URL . '/assets');
        define('ORDERDETECT_BASENAME', plugin_basename(__FILE__));
        define('ORDERDETECT_PLUGIN_NAME', 'Order Detect');
        define('ORDERDETECT_MINIMUM_PHP_VERSION', '5.6.0');
        define('ORDERDETECT_MINIMUM_WP_VERSION', '4.4');
        define('ORDERDETECT_MINIMUM_WC_VERSION', '3.1');
        // Licensing
        define('ORDERDETECT_STORE_URL', 'https://admin.orderdetect.com/');
        define('ORDERDETECT_SL_ITEM_NAME', 'Order Detect');

        define('ORDERDETECT_ENCRYPTION_KEY', 'yE7VLwfyweOTwWyxQgjNcxgArStNUARmkHVvsF3j4eU=');
        define('ORDERDETECT_IV', 'sq/gQejtmYczi99rYa61hA==');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {

        new OrderDetect\Assets();
        new OrderDetect\OrderDetecti18n();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            new OrderDetect\Ajax();
        }

        if (is_admin()) {
            new OrderDetect\Admin();
        } else {
            new OrderDetect\Frontend();
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installer = new OrderDetect\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function order_detect()
{
    return OrderDetect::init();
}

// kick-off the plugin
order_detect();
