jQuery(document).ready(function($) {

    $(document).on('click', '.show-otp-popup', function(e) {
    
        if (validateCheckoutFields()) {
        let billingPhone = $("#billing_phone").val();
            $("#otp-mobile-number").val(billingPhone);
            document.getElementById('otp-verification-popup').style.display = 'flex';
        } else {
            $('form.checkout').submit();
        }
    });
    
    /*==========================
    * 
    * This function will check 
    * WC legacy checkout template fields
    * 
     ===========================*/
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

    $(document).on('change', 'select.select2', function() {
        var $field = $(this);
        if ($field.hasClass('validate-required') && $field.val() === '') {
        $field.addClass('woocommerce-invalid');
        } else {
        $field.removeClass('woocommerce-invalid');
        }
    });
            
    
    $(document).on('click', '.modal__close', function() {
        document.getElementById('otp-verification-popup').style.display = 'none';
    });

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

    // function checkRequiredFields(sectionId) {
    //     const section = document.getElementById(sectionId);
    //     const requiredFields = section.querySelectorAll('input[required], select[required], textarea[required], input[type="checkbox"][required], input[type="radio"][required]');
    
    //     return Array.from(requiredFields).every(field => field.value);
    // }
    
    //$(document).on('click', '.wc-block-components-checkout-place-order-button', function(event) {
        // console.log("submitted")
        // event.preventDefault();
        // const sections = ['contact-fields', 'shipping-fields', 'payment-method'];
        // const allFieldsFilled = sections.every(sectionId => checkRequiredFields(sectionId));
    
        // if (!allFieldsFilled) {
        //     event.preventDefault();
        //     alert('Please fill in all required fields before proceeding.');
        // } else{
        //     event.preventDefault();
        // }
    //});
    
});
