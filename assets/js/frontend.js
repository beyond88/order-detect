jQuery(document).ready(function($) {

    $(document).on('click', '.show-otp-popup', function() {
        let billingPhone = $("#billing_phone").val();
        $("#otp-mobile-number").val(billingPhone);
        document.getElementById('otp-verification-popup').style.display = 'flex';
    });

    $(document).on('click', '.modal__close', function() {
        document.getElementById('otp-verification-popup').style.display = 'none';
    });

    function isValidBangladeshiPhoneNumber(phoneNumber) {
        const regex = /^(?:\+?88)?01[1-9]\d{8}$/;
        return regex.test(phoneNumber);
    }

    function normalizeBangladeshiPhoneNumber(phoneNumber) {
        // Remove any leading +88 or 88
        phoneNumber = phoneNumber.replace(/^(\+88|88)/, '');
        
        // Ensure the phone number starts with '0' and has 11 digits
        if (phoneNumber.length === 11 && phoneNumber.startsWith('0')) {
            return phoneNumber;
        } else {
            return null; // Return null if the phone number is not valid
        }
    }

    $(document).on('click', '#otp-verification-btn', function() {
        let phoneNumber = $.trim($("#otp-mobile-number").val());
        phoneNumber = normalizeBangladeshiPhoneNumber(phoneNumber);
        
        if (phoneNumber != '' && isValidBangladeshiPhoneNumber(phoneNumber)) {
            let that = $(this);
            that.html(order_shield.loader);
            that.prop("disabled", true);
    
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: order_shield.ajax_url,
                data: {
                    action: 'send_otp',
                    security: order_shield.nonce,
                    phone_number: phoneNumber
                },
                success: function(response, textStatus, jqXHR) {
                    console.log('response==>', response);
                    var statusCode = jqXHR.status;
    
                    if (statusCode === 200 && response.success) {
                        // Successful OTP sending
                        $('#otp-status-notice').addClass('order-shield-show').text(response.message);
                        document.getElementById('otp-verification-frist-step').style.display = 'none';
                        document.getElementById('otp-verification-second-step').style.display = 'block';
                    } else {
                        // Error in OTP sending
                        $('#otp-sending-status').addClass('order-shield-show').text(response.message);
                    }
                },
                error: function(jqXHR) {
                    // AJAX request error
                    $('#otp-sending-status').addClass('order-shield-show').text('Failed to send OTP: ' + jqXHR.statusText);
                },
                complete: function() {
                    // Restore button state
                    that.html(order_shield.get_otp);
                    that.prop("disabled", false);
                }
            });
        }
    });
    
    $(document).on('click', '#otp-verify-btn', function() {
       
        let that = $(this);
        let phoneNumber = $.trim($("#otp-mobile-number").val());
        phoneNumber = normalizeBangladeshiPhoneNumber(phoneNumber);
        let otpCode = $.trim($("#otp-code").val());

        if(otpCode !='' && otpCode.length == 4 && (phoneNumber !='' && isValidBangladeshiPhoneNumber(phoneNumber)) ){

            that.html(order_shield.loader);
            that.prop("disabled",true);
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: order_shield.ajax_url,
                data: {
                    action: 'verify_otp',
                    security: order_shield.nonce,
                    otp: otpCode,
                    phone_number:phoneNumber
                },
                success: function(response, textStatus, jqXHR) {
                    console.log('response==>', response);
                    var statusCode = jqXHR.status;
                    that.html('');

                    if (statusCode === 200 && response.success) {
                        $('#otp-verify-failed').addClass('order-shield-hide')
                        $('#otp-status-notice').addClass('order-shield-show').text(response.message);
                        that.html(order_shield.verify);
                        $('form.checkout').submit();
                    } else {
                        $('#otp-verify-failed').addClass('order-shield-show').text(response.message);
                        that.html(order_shield.verify);
                        that.prop("disabled", false);
                    }
                },
                error: function(jqXHR) {
                    console.log('error==>', jqXHR);
                    $('#otp-verify-failed').addClass('order-shield-show').text(order_shield.something_wrong);
                    that.html('');
                    that.html(order_shield.try_again);
                    that.prop("disabled",false);
                }
            });

        }
    });

    $(document).on('click', '#otp-resend-btn', function(e) {
        e.preventDefault();
        document.getElementById('otp-verification-second-step').style.display = 'none';
        document.getElementById('otp-verification-frist-step').style.display = 'block';
    });
    

});
