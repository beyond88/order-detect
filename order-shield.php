<?php

/**
 * Plugin Name: OrderShield
 * Description: Secure your store with phone verification, multi-order tracking, and parcel trust scores for smarter order management and reduced fraud.
 * Plugin URI: https://github.com/beyond88/order-shield
 * Author: Mohiuddin Abdul Kader
 * Author URI: https://github.com/beyond88
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       order-shield
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.5.2
 * @package OrderShield
 *
 * WC requires at least: 3.1
 * WC tested up to:   8.8.2
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
final class OrderShield
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
     * @return \OrderShield
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
        define('ORDERSHIELD_VERSION', self::version);
        define('ORDERSHIELD_FILE', __FILE__);
        define('ORDERSHIELD_PATH', __DIR__);
        define('ORDERSHIELD_URL', plugins_url('', ORDERSHIELD_FILE));
        define('ORDERSHIELD_ASSETS', ORDERSHIELD_URL . '/assets');
        define('ORDERSHIELD_BASENAME', plugin_basename(__FILE__));
        define('ORDERSHIELD_PLUGIN_NAME', 'OrderShield');
        define('ORDERSHIELD_MINIMUM_PHP_VERSION', '5.6.0');
        define('ORDERSHIELD_MINIMUM_WP_VERSION', '4.4');
        define('ORDERSHIELD_MINIMUM_WC_VERSION', '3.1');
        // Licensing
        define('ORDERSHIELD_STORE_URL', 'https://thebitcraft.com');
        define('ORDERSHIELD_SL_ITEM_ID', 2357);
        define('ORDERSHIELD_SL_ITEM_SLUG', 'order-shield');
        define('ORDERSHIELD_SL_ITEM_NAME', 'OrderShield');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {

        new OrderShield\Assets();
        new OrderShield\OrderShieldi18n();
        new OrderShield\API();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            new OrderShield\Ajax();
        }

        if (is_admin()) {
            new OrderShield\Admin();
        } else {
            new OrderShield\Frontend();
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installer = new OrderShield\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function order_shield()
{
    return OrderShield::init();
}

// kick-off the plugin
order_shield();
