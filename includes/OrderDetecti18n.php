<?php

namespace OrderDetect;

/**
 * Support language
 * 
 * @since    1.0.0
 */
class OrderDetecti18n {

	/**
	 * Call language method 
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function __construct() {
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
	}

	/**
	 * Load language file from directory
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'order-detect',
			false,
			dirname(dirname(ORDERDETECT_BASENAME)) . '/languages/'
		);
	}
}
