jQuery(document).ready(function($) {

    $(document).on('click', '.show-otp-popup', function() {
        document.getElementById('otp-verification-popup').style.display = 'flex';
    });

    $(document).on('click', '.modal__close', function() {
        document.getElementById('otp-verification-popup').style.display = 'none';
    });

    $(document).on('click', '#otp-verification-btn', function() {
        document.getElementById('otp-verification-frist-step').style.display = 'none';
        document.getElementById('otp-verification-second-step').style.display = 'block';
    });

    $(document).on('click', '#otp-resend-btn', function(e) {
        e.preventDefault();
        document.getElementById('otp-verification-second-step').style.display = 'none';
        document.getElementById('otp-verification-frist-step').style.display = 'block';
    });
    
    $('form.checkout').on('submit', function(e) {
        e.preventDefault();     

        // $('#verify-otp-button').on('click', function() {
        //     var otpCode = $('#otp-code').val();

        //     if (otpCode === "") {
        //         alert("Please enter the OTP");
        //         return;
        //     }

        //     // Simulate OTP verification
        //     // You should replace this with an actual AJAX request to your server to verify the OTP
        //     var isValidOtp = verifyOtp(otpCode);

        //     if (isValidOtp) {
        //         // Hide the OTP popup
        //         $('#otp-verification-popup').hide();
        //         $('.place_order').prop('disabled', false);

        //         // Submit the form
        //         $('form.checkout').unbind('submit').submit();
        //     } else {
        //         alert("Invalid OTP. Please try again.");
        //     }
        // });
    });

    function verifyOtp(otpCode) {
        // Replace this with an actual OTP verification logic
        // Here we assume any OTP with '1234' is valid for demonstration purposes
        return otpCode === "1234";
    }
});
