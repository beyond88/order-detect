<?php

namespace OrderDetect;


/**
 * Frontend handler class
 * 
 * @since    1.0.0
 * @param    none
 * @return   object
 */
class Frontend
{
    
    /**
     * Initialize the class
     *
     * @since    1.0.0
     * @param    none
     * @return   object
     */
    function __construct()
    {

        if( ! Helper::check_license(wp_parse_args(get_option('orderdetect_license'))) ) {
            return;
        }

        new Frontend\StoreFront();
    }
}
