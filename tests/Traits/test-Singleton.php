<?php

namespace OrderShield\Traits;

class SingletonTest extends WP_UnitTestCase
{
    use OrderShield\Traits\Singleton;

    public function testInstance()
    {
        $instance1 = SingletonTest::instance();
        $instance2 = SingletonTest::instance();

        $this->assertInstanceOf(SingletonTest::class, $instance1);
        $this->assertInstanceOf(SingletonTest::class, $instance2);
        $this->assertSame($instance1, $instance2);
    }
}
