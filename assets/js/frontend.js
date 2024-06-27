jQuery(document).ready(function($) {
    var countdownInterval; // Global variable to hold the countdown interval
    var countdownDuration = 60; // Countdown duration in seconds

    function isValidBangladeshiPhoneNumber(phoneNumber) {
        const regex = /^(?:\+?88)?01[1-9]\d{8}$/;
        return regex.test(phoneNumber);
    }

    function normalizeBangladeshiPhoneNumber(phoneNumber) {
        phoneNumber = phoneNumber.replace(/^(\+88|88)/, '');
        
        if (phoneNumber.length === 11 && phoneNumber.startsWith('0')) {
            return phoneNumber;
        } else {
            return null;
        }
    }

    function validateCheckoutFields() {
        var isValid = true;
        
        $('.woocommerce-invalid').removeClass('woocommerce-invalid');
        $('.woocommerce-error').remove();
        
        $('form.checkout').find('.woocommerce-billing-fields .validate-required input, .woocommerce-billing-fields .validate-required select').each(function() {
            var $field = $(this);
            var value = $field.is('select') ? $field.val() : $field.val().trim();
            if (value === '' || (value === null && $field.is('select'))) {
                isValid = false;
                $field.addClass('woocommerce-invalid');
            } else {
                $(this).removeClass('woocommerce-invalid');
            } 
        });

        if ($('#ship-to-different-address-checkbox').prop('checked') === true) {
            $('form.checkout').find('.shipping_address .validate-required input, .shipping_address .validate-required select').each(function() {
                var $field = $(this);
                var value = $field.is('select') ? $field.val() : $field.val().trim();
                if (value === '' || (value === null && $field.is('select'))) {
                    isValid = false;
                    $(this).addClass('woocommerce-invalid');
                } else {
                    $(this).removeClass('woocommerce-invalid');
                }
            });
        }
        
        return isValid;
    }

    function startCountdown(duration, elementId) {
        clearInterval(countdownInterval); // Clear any existing interval
        var timer = duration, minutes, seconds;
        var $element = $(elementId);
        countdownInterval = setInterval(function() {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            seconds = seconds < 10 ? "0" + seconds : seconds;

            $element.html('<p class="otp-resend-msg" id="otp-resend-msg">Didn\'t receive code? Resend in ' + minutes + ':' + seconds + '</p>');

            if (--timer < 0) {
                clearInterval(countdownInterval);
                $element.html('');
                $element.append('<button type="button" class="otp-verification-btn" id="otp-resend-btn">' + order_detect.resend_otp + '</button>');
            }
        }, 1000);
    }

    async function checkPhoneIsVerified(billingPhone) {
        try {
            const response = await $.ajax({
                type: 'POST',
                dataType: 'json',
                url: order_detect.ajax_url,
                data: {
                    action: 'check_phone_is_verified',
                    security: order_detect.nonce,
                    phone_number: billingPhone
                }
            });

            return response.success === true;
        } catch (error) {
            console.error('Error fetching data:', error);
            return false;
        }
    }

    $(document).on('click', '.show-otp-popup', async function(e) {
        e.preventDefault();

        if (validateCheckoutFields()) {
            document.getElementById('otp-verification-popup').style.display = 'flex';
            let billingPhone = $("#billing_phone").val();
            billingPhone = normalizeBangladeshiPhoneNumber(billingPhone);
            const isVerified = await checkPhoneIsVerified(billingPhone);
            
            if (isVerified) {
                $('form.checkout').submit();
            } else {
                sendOTP('#otp-verify-btn', billingPhone);
                $('.otp-processing-area').hide();
                $('.otp-form-area').show();
                startCountdown(countdownDuration, '#otp-resend-section'); // Start the countdown
            }
        } else {
            $('form.checkout').submit();
        }
    });

    $(document).on('click', '#otp-verify-btn', function() {
        let that = $(this);
        let phoneNumber = $.trim($("#billing_phone").val());
        phoneNumber = normalizeBangladeshiPhoneNumber(phoneNumber);
        let otpCode = $.trim($("#otp-code").val());

        if (otpCode != '' && otpCode.length == 4 && (phoneNumber != '' && isValidBangladeshiPhoneNumber(phoneNumber))) {

            that.html(order_detect.loader);
            that.prop("disabled", true);
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: order_detect.ajax_url,
                data: {
                    action: 'verify_otp',
                    security: order_detect.nonce,
                    otp: otpCode,
                    phone_number: phoneNumber
                },
                success: function(response, textStatus, jqXHR) {
                    var statusCode = jqXHR.status;
                    that.html('');

                    if (statusCode === 200 && response.success) {
                        $('#otp-verify-failed').addClass('order-detect-hide');
                        $('#otp-status-notice').addClass('order-detect-show').text(response.message);
                        that.html(order_detect.verify);
                        $('form.checkout').submit();
                    } else {
                        $('#otp-verify-failed').addClass('order-detect-show').text(response.message);
                        that.html(order_detect.verify);
                        that.prop("disabled", false);
                    }
                },
                error: function(jqXHR) {
                    $('#otp-verify-failed').addClass('order-detect-show').text(order_detect.something_wrong);
                    that.html('');
                    that.html(order_detect.try_again);
                    that.prop("disabled", false);
                }
            });

        }
    });

    $(document).on('click', '#otp-resend-btn', function(e) {
        e.preventDefault();
        $("#otp-code").val('');
        let billingPhone = $("#billing_phone").val();
        sendOTP('#otp-resend-btn', billingPhone);
    });

    function sendOTP(elementId, billingPhone) {
        billingPhone = normalizeBangladeshiPhoneNumber(billingPhone);
        if (billingPhone != '' && isValidBangladeshiPhoneNumber(billingPhone)) {

            let that = $(elementId);
            that.html(order_detect.loader);
            that.prop("disabled", true);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: order_detect.ajax_url,
                data: {
                    action: 'send_otp',
                    security: order_detect.nonce,
                    phone_number: billingPhone
                },
                success: function(response, textStatus, jqXHR) {
                    $("#otp-sedning-msg").text(response.message);
                },
                error: function(jqXHR) {
                    $("#otp-sedning-msg").text('Failed to send OTP: ' + jqXHR.statusText);
                    that.html(order_detect.verify);
                    that.prop("disabled", false);
                },
                complete: function(response) {
                    $("#otp-sedning-msg").text(response.message);
                    that.html(order_detect.verify);
                    that.prop("disabled", false);
                    startCountdown(countdownDuration, '#otp-resend-section'); 
                }
            });

        }
    }

    $(document).on('click', '.modal__close', function() {
        document.getElementById('otp-verification-popup').style.display = 'none';
        $('.otp-processing-area').show();
        $('.otp-form-area').hide();
        $("#otp-resend-msg, #otp-resend-btn").remove();
        clearInterval(countdownInterval);
        countdownDuration = 60;
    });
});
