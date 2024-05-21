<?php

namespace OrderShield\Frontend;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use OrderShield\API\OrderShieldAPI;
use OrderShield\API\Resources\Order;

/**
 * Ajax handler class
 */
class StoreFront
{

    private $api;
    private $settings;

    /**
     * Class constructor
     */
    public function __construct()
    {
        // $this->api = new OrderShieldAPI();
        $this->settings = get_option('ordershield_settings');
        add_filter('woocommerce_locate_template', array($this, 'set_locate_template'), PHP_INT_MAX, 3);
        add_action('wp_footer', array($this, 'init_otp_modal_checkout'));
        add_action('woocommerce_checkout_process', array($this, 'check_otp_status_before_submit'), PHP_INT_MAX);
    }

    public function check_otp_status_before_submit()
    {
        if (array_key_exists('enable_otp', $this->settings)) {
            wc_add_notice(__('OTP verification failed. Please try again.', 'order-shield'), 'error');
        }
    }

    /**
     * Return plugin directory
     *
     * @since	1.0.0
     * @access	public
     * @param	none
     * @return	string
     */
    public static function get_plugin_path()
    {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Return WooCommerce template path
     * 
     * @since	1.0.0
     * @access	public
     * @param	string, string, string
     * @return	string
     */
    public function set_locate_template($template, $template_name, $template_path)
    {

        global $woocommerce;
        $_template = $template;
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }

        $plugin_path  = self::get_plugin_path() . '/views/woocommerce/';
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if (!$template && file_exists($plugin_path . $template_name))
            $template = $plugin_path . $template_name;

        if (!$template)
            $template = $_template;

        return $template;
    }

    public function init_otp_modal_checkout()
    {
        if (is_checkout() && array_key_exists('enable_otp', $this->settings)) { ?>
            <div class="otp-verification-container" id="otp-verification-popup">
                <div class="otp-verification-inner" id="otp-verification-frist-step">
                    <div class="otp-verification-header">
                        <h2><?php echo __('Mobile Verification', 'order-shield'); ?></h2>
                        <label class="modal__close" for="modal-1"></label>
                    </div>
                    <div class="otp-verification-body">
                        <form class="otp-verification-form">
                            <div class="otp-form-group">
                                <input type="tel" name="otp-mobile-number" class="otp-mobile-number" id="otp-mobile-number" maxlength="50" placeholder="<?php echo __('Enter your mobile number', 'order-shield'); ?>">
                            </div>
                            <div class="otp-form-group">
                                <button type="button" class="otp-verification-btn" id="otp-verification-btn">
                                    <?php echo __('Get OTP', 'order-shield'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="otp-verification-footer"></div>
                </div>

                <div class="otp-verification-inner" id="otp-verification-second-step">
                    <div class="otp-verification-header">
                        <h2><?php echo __('Verification Code', 'order-shield'); ?></h2>
                        <label class="modal__close" for="modal-1"></label>
                    </div>
                    <div class="otp-verification-body">
                        <form class="otp-verification-form">
                            <div class="otp-form-group">
                                <input type="text" name="otp-code" class="otp-code" id="otp-code" maxlength="200" placeholder="<?php echo __('Enter OTP code', 'order-shield'); ?>">
                            </div>
                            <div class="otp-form-group">
                                <button type="button" class="otp-verification-btn" id="otp-verify-btn">
                                    <?php echo __('Verify', 'order-shield'); ?>
                                </button>
                            </div>
                            <p class="otp-resend-section">
                                <?php echo __('Didn\'t receive code?', 'order-shield'); ?>
                                <a href="javascript:void(0)" class="otp-resend-btn" id="otp-resend-btn"><?php echo __('Resend', 'order-shield'); ?></a>
                            </p>
                        </form>
                    </div>
                    <div class="otp-verification-footer"></div>
                </div>

            </div>
<?php   }
    }
}
