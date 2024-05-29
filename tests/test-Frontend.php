<?php

use OrderShield\Frontend;

class FrontendTest extends WP_UnitTestCase {

    public function test_constructor() {
        $frontend = new Frontend();
        $this->assertInstanceOf(OrderShield\Frontend\StoreFront::class, $frontend);
    }
}
