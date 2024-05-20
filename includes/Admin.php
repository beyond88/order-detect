<?php

namespace OrderShield;

/**
 * The admin class
 */
class Admin
{

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct()
    {
        $main = new Admin\Main();
        new Admin\Menu($main);
        new Admin\License($main);
        $this->dispatch_actions($main);
        new Admin\PluginMeta();
    }

    /**
     * Dispatch and bind actions
     *
     * @since   1.0.0
     * @access  public
     * @param   string
     * @return  void
     */
    public function dispatch_actions($main)
    {
    }
}
