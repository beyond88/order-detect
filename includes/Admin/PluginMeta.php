<?php

namespace OrderBarrier\Admin;

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
        add_filter( 'plugin_action_links_' . ORDERBARRIER_BASENAME, [ $this, 'plugin_action_links' ] );
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

        $links[] = '<a href="' . admin_url( 'admin.php?page=order-barrier#general_settings' ) . '">' . __( 'Settings', 'order-barrier' ) . '</a>';
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
        
        if ($file !== plugin_basename( ORDERBARRIER_FILE )) {
			return $links;
		}

		$support_link = '<a target="_blank" href="https://github.com/beyond88/order-barrier/issues" title="' . __('Get help', 'order-barrier') . '">' . __('Support', 'order-barrier') . '</a>';
		$home_link = '<a target="_blank" href="https://github.com/beyond88/order-barrier" title="' . __('Plugin Homepage', 'order-barrier') . '">' . __('Plugin Homepage', 'order-barrier') . '</a>';

		$links[] = $support_link;
		$links[] = $home_link;

		return $links;

    }
}