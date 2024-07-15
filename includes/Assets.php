<?php

namespace OrderDetect;

/**
 * Assets handlers class
 */
class Assets {

    /**
     * Class constructor
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
    }

    /**
     * All available scripts
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_scripts() {
        return array(
            'order-detect-script' => array(
                'src'     => ORDERDETECT_ASSETS . '/js/frontend.js',
                'version' => filemtime(ORDERDETECT_PATH . '/assets/js/frontend.js'),
                'deps'    => array('jquery'),
            ),
        );
    }

    /**
     * All available styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_styles() {
        return array(
            'order-detect-style' => array(
                'src'     => ORDERDETECT_ASSETS . '/css/frontend.css',
                'version' => filemtime(ORDERDETECT_PATH . '/assets/css/frontend.css'),
            ),

        );
    }

    /**
     * Register scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function register_assets() {

        if( ! Helper::check_license( wp_parse_args( get_option('orderdetect_license') ) ) ) {
            return '';
        }
        
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('order-detect-script', 'order_detect', array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('order-detect-nonce'),
            'loader' => '<div class="order-detect-loader"></div>',
            'resend_otp' => __('Resend OTP', 'order-detect'),
            'place_order' => __('Place Order', 'order-detect'),
            'verify' => __('Verify', 'order-detect'),
            'try_again' => __('Try again', 'order-detect'),
            'something_wrong' => __('Something went wrong!', 'order-detect'),
        ));
    }

    /**
     * All available scripts
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_scripts() {
        return array(
            'order-detect-admin-script' => array(
                'src'     => ORDERDETECT_ASSETS . '/js/admin.js',
                'version' => filemtime(ORDERDETECT_PATH . '/assets/js/admin.js'),
                'deps'    => array('jquery'),
            ),
        );
    }

    /**
     * All available styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_styles() {
        return array(
            'orderdetect-admin-style' => array(
                'src'     => ORDERDETECT_ASSETS . '/css/admin.css',
                'version' => filemtime(ORDERDETECT_PATH . '/assets/css/admin.css'),
            ),
        );
    }

    /**
     * Register scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function register_admin_assets($hook) {

        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        $section = isset($_GET['section']) ? $_GET['section'] : '';

        $scripts = $this->get_admin_scripts();
        $styles  = $this->get_admin_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';
            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('order-detect-admin-script', 'order_detect', array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('order-detect-admin-nonce'),
            'activate' => __('Activate', 'order-detect'),
            'deactivate' => __('Deactivate', 'order-detect'),
            'loader' => '<div class="order-detect-loader"></div>',
        ));
    }
}
