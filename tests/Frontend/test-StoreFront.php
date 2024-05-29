<?php

namespace OrderShield\Frontend;

class StoreFrontTest extends WP_UnitTestCase
{
    protected $storeFront;

    public function setUp(): void
    {
        parent::setUp();
        $this->storeFront = new OrderShield\Frontend\StoreFront();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->storeFront);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(OrderShield\Frontend\StoreFront::class, $this->storeFront);
    }

    public function testSetUserVerifiedForOtp()
    {
        $order_id = 1;
        $old_status = 'pending';
        $new_status = 'processing';

        $this->storeFront->set_user_verified_for_otp($order_id, $old_status, $new_status);

        $this->assertNotFalse(get_user_meta(get_current_user_id(), 'next_otp_check', true));
    }

    public function testCheckOtpStatusBeforeSubmit()
    {
        $this->expectOutputString('OTP verification failed. Please try again.');
        $this->storeFront->check_otp_status_before_submit();
    }

    public function testSetLocateTemplate()
    {
        $template = '';
        $template_name = 'checkout/form-checkout.php';
        $template_path = '';

        $result = $this->storeFront->set_locate_template($template, $template_name, $template_path);

        $this->assertNotEmpty($result);
    }

    public function testInitOtpModalCheckout()
    {
        update_option('ordershield_settings', ['enable_otp' => true]);
        update_option('ordershield_license', 'valid_license');

        $this->expectOutputString(OrderShield\Frontend\Form::otp_form());
        $this->storeFront->init_otp_modal_checkout();
    }
}