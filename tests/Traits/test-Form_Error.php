<?php

namespace OrderShield\Traits;

class FormErrorTest extends WP_UnitTestCase
{
    use OrderShield\Traits\Form_Error;

    public function testHasError()
    {
        $this->assertFalse($this->has_error('test_key'));

        $this->errors['test_key'] = 'Test error message';
        $this->assertTrue($this->has_error('test_key'));
    }

    public function testGetError()
    {
        $this->assertFalse($this->get_error('test_key'));

        $this->errors['test_key'] = 'Test error message';
        $this->assertEquals('Test error message', $this->get_error('test_key'));
    }
}