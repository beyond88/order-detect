<?php

namespace OrderShield\Admin;

use OrderShield\Helper;

/**
 * The Menu handler class
 */
class Menu
{

    /**
     * Plugin main file
     *
     * @var string
     */
    public $main;

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     * @access  public
     * @param   object
     * @return  void
     */
    function __construct($main)
    {
        $this->main = $main;
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Register admin menu
     *
     * @since   1.0.0
     * @access  public
     * @param   none   
     * @return  void
     */
    public function admin_menu()
    {
        global $submenu;

        $parent_slug = 'order-shield';
        $capability = 'manage_options';
        $icon_url = '';

        $hook = add_menu_page(
            __('Order Shield', 'order-shield'),
            __('Order Shield', 'order-shield'),
            $capability,
            $parent_slug,
            [$this->main, 'plugin_page'],
            $icon_url,
            50
        );
        add_action('admin_head-' . $hook, array($this, 'enqueue_assets'));

        if (current_user_can($capability)) {
            $submenu[$parent_slug][] = [__('Kickstart', 'order-shield'), $capability, 'admin.php?page=' . $parent_slug . '#/'];
            $submenu[$parent_slug][] = [__('License', 'order-shield'), $capability, 'admin.php?page=' . $parent_slug . '#/license'];
        }
    }

    /**
     * Enqueue scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none   
     * @return  void
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('ordershield-admin-boostrap');
        wp_enqueue_style('ordershield-admin-style');
        wp_enqueue_script('ordershield-admin-script');
    }
}
