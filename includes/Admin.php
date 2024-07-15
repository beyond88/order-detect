<?php

namespace OrderDetect;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct() {
        $main = new Admin\Main();
        new Admin\Menu($main);
        new Admin\MultipleOrderTracking($main);
        new Admin\SMSLog($main);
        new Admin\License($main);
        new Admin\PluginMeta();
    }
}
