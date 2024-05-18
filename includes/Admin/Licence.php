<?php

namespace OrderShield\Admin;

/**
 * Settings Handler class
 */
class Licence
{
    /**
     * Plugin page handler
     *
     * @return void
     */
    public function licence_page()
    {
        $template = __DIR__ . '/views/order-shield-licence.php';

        if (file_exists($template)) {
            include $template;
        }
    }
}
