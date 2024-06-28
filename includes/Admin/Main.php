<?php

namespace OrderDetect\Admin;

use OrderDetect\API\OrderDetectAPI;
use OrderDetect\Helper;

/**
 * Settings Handler class
 */
class Main
{

	/**
	 * Settings otpions field
	 * 
	 * @var string
	 */
	public $_optionName  = 'orderdetect_settings';

	/**
	 * Settings otpions group field
	 * 
	 * @var string
	 */
	public $_optionGroup = 'orderdetect_options_group';

	/**
	 * Settings otpions field default values
	 * 
	 * @var array
	 */
	public $_defaultOptions = array(
		'pathao_api_endpoint' => '',
		'pathao_api_key' => '',
		'pathao_secret_key' => '',
		'steadfast_api_endpoint' => '',
		'steadfast_api_key' => '',
		'steadfast_secret_key' => '',
		'redx_api_endpoint' => '',
		'redx_api_key' => '',
		'redx_secret_key' => '',
		'enable_otp' => '',
		'sms_api_endpoint' => '',
		'sms_api_key' => '',
	);

	private $api;

	/**
	 * Initial the class and its all methods
	 *
	 * @since 1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function __construct()
	{
		add_action('plugins_loaded', array($this, 'set_default_options'));
		add_action('admin_init', array($this, 'menu_register_settings'));
		add_action('admin_init', array($this, 'check_and_save_sms_balance'));
		
		OrderDetectSettings::init();
		$this->api = new OrderDetectAPI();

		add_filter( 'edd_sl_api_request_verify_ssl', '__return_false' );
		add_action( 'admin_init', array( $this, 'plugin_update') );

	}

	public function plugin_update() {

		$orderdetect_license = get_option('orderdetect_license');
		$license_key = isset($orderdetect_license['key']) ? $orderdetect_license['key'] : '';
		if( ! empty( $license_key ) ) {
			$updater = new PluginUpdate( ORDERDETECT_STORE_URL, ORDERDETECT_FILE, array(
				'version'      => ORDERDETECT_VERSION,
				'license'      => Helper::decrypt_data($license_key, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV),
				'item_name'    => ORDERDETECT_SL_ITEM_NAME,
				'item_id' 	   => 23,
				'author'       => 'Imran Ahmad',
				)
			);

			// print_r($updater);
			echo Helper::decrypt_data($license_key, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);
		}
	}

	/**
	 * Plugin page handler
	 *
	 * @since 1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function plugin_page()
	{
		$settings = OrderDetectSettings::setting_fields();
		$template = __DIR__ . '/views/order-detect-settings.php';

		if (file_exists($template)) {
			include $template;
		}
	}

	/**
	 * Save the setting options		
	 * 
	 * @since	1.0.0
	 * @access 	public
	 * @param	array
	 * @return	void
	 */
	public function menu_register_settings()
	{
		add_option($this->_optionName, $this->_defaultOptions);
		register_setting($this->_optionGroup, $this->_optionName);
	}

	/**
	 * Apply filter with default options
	 * 
	 * @since	1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function set_default_options()
	{
		return apply_filters('orderdetect_default_options', $this->_defaultOptions);
	}

	public function check_and_save_sms_balance()
	{

		if (isset($_POST['orderdetect_nonce']) && check_admin_referer('orderdetect_options_verify', 'orderdetect_nonce')) {
			if (
				!empty($_POST['orderdetect_settings']['enable_otp']) &&
				!empty($_POST['orderdetect_settings']['sms_api_endpoint']) &&
				!empty($_POST['orderdetect_settings']['sms_api_key'])
			) {

				$balance_response = Helper::get_balance(esc_url($_POST['orderdetect_settings']['sms_api_endpoint']), sanitize_text_field($_POST['orderdetect_settings']['sms_api_key']));
				if ($balance_response && $balance_response->error === 0) {
					$balance = $balance_response->data->balance;
					update_option('orderdetect_sms_balance', $balance);
					Helper::send_sms_balance_notification();
				} elseif ($balance_response && $balance_response->error === 405) {
					error_log('Please configure SMS API first.');
				} else {
					error_log('Unknown Error, failed to fetch balance');
				}
			}
		}
	}
}
