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
    $(document).on('click', '#otp-verify-btn', function() {
        // document.getElementById('otp-verification-second-step').style.display = 'none';
        // document.getElementById('otp-verification-frist-step').style.display = 'block';
        $( 'form.checkout' ).submit();
    });

    

});
