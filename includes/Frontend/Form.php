<?php

namespace OrderBarrier\Frontend;

class Form
{

    public static function otp_form()
    {
?>
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner" id="otp-verification-frist-step">
                <div class="otp-verification-header">
                    <h2><?php echo __('Mobile Verification', 'order-barrier'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="otp-status-error" id="otp-sending-status"></p>
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="tel" name="otp-mobile-number" class="otp-mobile-number" id="otp-mobile-number" maxlength="13" placeholder="<?php echo __('Enter your mobile number', 'order-barrier'); ?>">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verification-btn">
                                <?php echo __('Get OTP', 'order-barrier'); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="otp-verification-footer"></div>
            </div>

            <div class="otp-verification-inner" id="otp-verification-second-step">
                <div class="otp-verification-header">
                    <h2><?php echo __('Verification Code', 'order-barrier'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="otp-status-success" id="otp-status-notice"></p>
                    <p class="otp-status-error" id="otp-verify-failed"></p>
                    <form class="otp-verification-form">
                        <div class="otp-form-group">
                            <input type="text" name="otp-code" class="otp-code" id="otp-code" maxlength="4" placeholder="<?php echo __('Enter OTP code', 'order-barrier'); ?>">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verify-btn">
                                <?php echo __('Verify', 'order-barrier'); ?>
                            </button>
                        </div>
                        <p class="otp-resend-section">
                            <?php echo __('Didn\'t receive code?', 'order-barrier'); ?>
                            <a href="javascript:void(0)" class="otp-resend-btn" id="otp-resend-btn"><?php echo __('Resend', 'order-barrier'); ?></a>
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
                    <h2><?php echo __('Mobile Verification', 'order-barrier'); ?></h2>
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
