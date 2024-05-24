<?php

namespace OrderShield\Admin;

use OrderShield\API\OrderShieldAPI;
use OrderShield\Helper;

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
	public $_optionName  = 'ordershield_settings';

	/**
	 * Settings otpions group field
	 * 
	 * @var string
	 */
	public $_optionGroup = 'ordershield_options_group';

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

		OrderShieldSettings::init();

		$this->api = new OrderShieldAPI();
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
		$settings = OrderShieldSettings::setting_fields();
		$template = __DIR__ . '/views/order-shield-settings.php';

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
		return apply_filters('ordershield_default_options', $this->_defaultOptions);
	}

	public function check_and_save_sms_balance()
	{

		if (isset($_POST['ordershield_nonce']) && check_admin_referer('ordershield_options_verify', 'ordershield_nonce')) {
			if (
				!empty($_POST['ordershield_settings']['enable_otp']) &&
				!empty($_POST['ordershield_settings']['sms_api_endpoint']) &&
				!empty($_POST['ordershield_settings']['sms_api_key'])
			) {

				$balance_response = Helper::getBalance(esc_url($_POST['ordershield_settings']['sms_api_endpoint']), sanitize_text_field($_POST['ordershield_settings']['sms_api_key']));
				if ($balance_response && $balance_response->error === 0) {
					$balance = $balance_response->data->balance;
					update_option('ordershield_sms_balance', $balance);
				} elseif ($balance_response && $balance_response->error === 405) {
					error_log('Please configure SMS API first.');
				} else {
					error_log('Unknown Error, failed to fetch balance');
				}
			}
		}
	}
}
