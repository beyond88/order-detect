<?php

/**
 * Plugin Name: Order Barrier
 * Description: Secure your store with phone verification, multi-order tracking, and parcel trust scores for smarter order management and reduced fraud.
 * Plugin URI: https://github.com/beyond88/order-barrier
 * Author: Mohiuddin Abdul Kader
 * Author URI: https://github.com/beyond88
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       order-barrier
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.5.2
 * @package Order Barrier
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
final class OrderBarrier
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
     * @return \OrderBarrier
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
        define('ORDERBARRIER_VERSION', self::version);
        define('ORDERBARRIER_FILE', __FILE__);
        define('ORDERBARRIER_PATH', __DIR__);
        define('ORDERBARRIER_URL', plugins_url('', ORDERBARRIER_FILE));
        define('ORDERBARRIER_ASSETS', ORDERBARRIER_URL . '/assets');
        define('ORDERBARRIER_BASENAME', plugin_basename(__FILE__));
        define('ORDERBARRIER_PLUGIN_NAME', 'Order Barrier');
        define('ORDERBARRIER_MINIMUM_PHP_VERSION', '5.6.0');
        define('ORDERBARRIER_MINIMUM_WP_VERSION', '4.4');
        define('ORDERBARRIER_MINIMUM_WC_VERSION', '3.1');
        // Licensing
        define('ORDERBARRIER_STORE_URL', 'https://thebitcraft.com');
        define('ORDERBARRIER_SL_ITEM_ID', 2357);
        define('ORDERBARRIER_SL_ITEM_SLUG', 'order-barrier');
        define('ORDERBARRIER_SL_ITEM_NAME', 'Order Barrier');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {

        new OrderBarrier\Assets();
        new OrderBarrier\OrderBarrieri18n();
        new OrderBarrier\API();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            new OrderBarrier\Ajax();
        }

        if (is_admin()) {
            new OrderBarrier\Admin();
        } else {
            new OrderBarrier\Frontend();
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installer = new OrderBarrier\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function order_barrier()
{
    return OrderBarrier::init();
}

// kick-off the plugin
order_barrier();
