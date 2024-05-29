<?php

namespace OrderShield\Frontend;

class FormTest extends WP_UnitTestCase
{
    public function testOtpForm()
    {
        $expected_output = <<<HTML
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner" id="otp-verification-frist-step">
                <div class="otp-verification-header">
                    <h2>Mobile Verification</h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="otp-status-error" id="otp-sending-status"></p>
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="tel" name="otp-mobile-number" class="otp-mobile-number" id="otp-mobile-number" maxlength="13" placeholder="Enter your mobile number">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verification-btn">
                                Get OTP
                            </button>
                        </div>
                    </form>
                </div>
                <div class="otp-verification-footer"></div>
            </div>

            <div class="otp-verification-inner" id="otp-verification-second-step">
                <div class="otp-verification-header">
                    <h2>Verification Code</h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="otp-status-success" id="otp-status-notice"></p>
                    <p class="otp-status-error" id="otp-verify-failed"></p>
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="text" name="otp-code" class="otp-code" id="otp-code" maxlength="4" placeholder="Enter OTP code">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verify-btn">
                                Verify
                            </button>
                        </div>
                        <p class="otp-resend-section">
                            Didn't receive code?
                            <a href="javascript:void(0)" class="otp-resend-btn" id="otp-resend-btn">Resend</a>
                        </p>
                    </form>
                </div>
                <div class="otp-verification-footer"></div>
            </div>
        </div>
        HTML;

        $this->expectOutputString($expected_output);
        OrderShield\Frontend\Form::otp_form();
    }

    public function testLicenseForm()
    {
        $expected_output = <<<HTML
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner">
                <div class="otp-verification-header">
                    <h2>Mobile Verification</h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="license-expired-notice">The license has expired or has not been activated.</p>
                </div>
                <div class="otp-verification-footer"></div>
            </div>
        </div>
        HTML;

        $this->expectOutputString($expected_output);
        OrderShield\Frontend\Form::license_form();
    }
}
