<?php

namespace OrderShield\Frontend;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use OrderShield\API\OrderShieldAPI;
use OrderShield\API\Resources\Order;

/**
 * Ajax handler class
 */
class StoreFront
{

    private $api;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->api = new OrderShieldAPI();
    }

    /**
     * Sends order data to Hub when the order status is changed.
     *
     * @param int $order_id The ID of the order.
     * @param string $old_status The old order status.
     * @param string $new_status The new order status.
     */
    public function send_order_data_to_hub($order_id, $old_status, $new_status)
    {

    }
}
