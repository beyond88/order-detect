<?php

namespace OrderShield\Admin;

use OrderShield\API\OrderShieldAPI;

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
		'license_key' => '',
		'license_expires' => ''
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

}
