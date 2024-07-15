<?php

namespace OrderDetect\Admin;

/**
* Initiate plugin action links
*
* @since    1.0.0
*/
class PluginMeta {

    /**
     * Load plugin meta actions
     * 
     * @since 1.0.0
     * @access public
     * @param none
     * @return void
     */
    public function __construct() {
        add_filter( 'plugin_action_links_' . ORDERDETECT_BASENAME, [ $this, 'plugin_action_links' ] );
        add_filter( 'plugin_row_meta', [ $this, 'plugin_meta_links' ], 10, 2 );
    }

    /**
     * Create plugin action links
     *
     * @since   1.0.0
     * @access  public
     * @param   array
     * @return  array
     */
    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=order-detect#general_settings' ) . '">' . __( 'Settings', 'order-detect' ) . '</a>';
        return $links;
    }

    /**
     * Create plugin meta links
     *
     * @since   1.0.0
     * @access  public
     * @param   array string
     * @return  array
     */
    public function plugin_meta_links( $links, $file ) {
        
        if ($file !== plugin_basename( ORDERDETECT_FILE )) {
			return $links;
		}

		$support_link = '<a target="_blank" href="https://github.com/beyond88/order-detect/issues" title="' . __('Get help', 'order-detect') . '">' . __('Support', 'order-detect') . '</a>';
		$home_link = '<a target="_blank" href="https://github.com/beyond88/order-detect" title="' . __('Plugin Homepage', 'order-detect') . '">' . __('Plugin Homepage', 'order-detect') . '</a>';

		$links[] = $support_link;
		$links[] = $home_link;

		return $links;

    }
}