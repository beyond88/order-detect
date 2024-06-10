<?php

namespace OrderDetect\Frontend;

class Form
{

    public static function otp_form()
    {
?>
        <div class="otp-verification-container" id="otp-verification-popup">
            <div class="otp-verification-inner" id="otp-verification-second-step">
                <div class="otp-verification-header">
                    <h2><?php echo __('Mobile Verification', 'order-detect'); ?></h2>
                    <label class="modal__close" for="modal-1"></label>
                </div>
                <div class="otp-verification-body">
                    <p class="otp-sedning-msg" id="otp-sedning-msg"></p>
                    <p class="otp-status-success" id="otp-status-notice"></p>
                    <p class="otp-status-error" id="otp-verify-failed"></p>
                    <form class="otp-verification-form">
                        <div class="otp-form-group otp-form-group-25">
                            <input type="text" name="otp-code" class="otp-code" id="otp-code" maxlength="4" placeholder="<?php echo __('Enter OTP Code', 'order-detect'); ?>">
                        </div>
                        <div class="otp-form-group">
                            <button type="button" class="otp-verification-btn" id="otp-verify-btn">
                                <?php echo __('Verify', 'order-detect'); ?>
                            </button>
                        </div>
                        <div class="otp-form-group" id="otp-resend-section">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }
}
