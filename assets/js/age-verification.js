jQuery(function($) {
    $('.age-verification-btn-yes').on('click', function(e) {
        e.preventDefault();
        if ($('.age-gate-remember-me-checkbox').is(":checked")) {
            Cookies.set('remember_me', 1);
        }
        $.ajax({
            type: 'POST',
            url: my_ajax_obj.ajax_url,
            data: {
                _ajax_nonce: my_ajax_obj.nonce,
                action: 'set_age_verification_cookie'
            },
            success: function(response) {
                $('.age-verification-overlay').remove();
            }, error: function () {
                Cookies.remove('remember_me', { path: '' });
            }
        });
    });
    $footerClass = $('footer').attr('class');
    if (!$footerClass) {
        $footerClass = $('footer').attr('id');
    }
    if (Cookies.get('age_verification') || Cookies.get('age_verification_mode') == 0) {
        if (typeof Cookies.get('age_verification_mode') !== 'undefined') {
            $('.age-verification-overlay').css("display", "none");
        } else {
            $('.age-verification-overlay').css("display", "block");
        }
    } else {
        $('.age-verification-overlay').css("display", "block");
    }
});