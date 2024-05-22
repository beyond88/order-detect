<?php

namespace OrderShield\Frontend;

class Form
{

    public static function otp_form()
    {
?>
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner" id="otp-verification-frist-step">
                <div class="otp-verification-header">
                    <h2><?php echo __('Mobile Verification', 'order-shield'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="tel" name="otp-mobile-number" class="otp-mobile-number" id="otp-mobile-number" maxlength="50" placeholder="<?php echo __('Enter your mobile number', 'order-shield'); ?>">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verification-btn">
                                <?php echo __('Get OTP', 'order-shield'); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="otp-verification-footer"></div>
            </div>

            <div class="otp-verification-inner" id="otp-verification-second-step">
                <div class="otp-verification-header">
                    <h2><?php echo __('Verification Code', 'order-shield'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="text" name="otp-code" class="otp-code" id="otp-code" maxlength="200" placeholder="<?php echo __('Enter OTP code', 'order-shield'); ?>">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verify-btn">
                                <?php echo __('Verify', 'order-shield'); ?>
                            </button>
                        </div>
                        <p class="otp-resend-section">
                            <?php echo __('Didn\'t receive code?', 'order-shield'); ?>
                            <a href="javascript:void(0)" class="otp-resend-btn" id="otp-resend-btn"><?php echo __('Resend', 'order-shield'); ?></a>
                        </p>
                    </form>
                </div>
                <div class="otp-verification-footer"></div>
            </div>
        </div>
    <?php
    }

    public static function license_form()
    { ?>
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner">
                <div class="otp-verification-header">
                    <h2><?php echo __('Mobile Verification', 'order-shield'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="license-expired-notice"><?php echo __('The license has expired or has not been activated.'); ?></p>
                </div>
                <div class="otp-verification-footer"></div>
            </div>
        </div>
<?php
    }
}
