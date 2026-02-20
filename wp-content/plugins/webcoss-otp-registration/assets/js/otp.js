jQuery(document).ready(function ($) {

    $('#reg_otp').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#reg_otp').attr('maxlength', 6);

    $('#send_otp_btn').click(function (e) {

        e.preventDefault();

        let name = $('#reg_name').val().trim();
        let email = $('#reg_email').val().trim();
        let password = $('#reg_password').val().trim();
        let otp = $('#reg_otp').val().trim();

        let errorBox = $('#form_error');
        errorBox.text('');

        // 🔹 Name validation
        if (name.length < 3) {
            errorBox.text('Name must be at least 3 characters.');
            return;
        }

        // 🔹 Email validation
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            errorBox.text('Please enter valid email address.');
            return;
        }

        // 🔹 Password validation
        if (password.length < 6) {
            errorBox.text('Password must be at least 6 characters.');
            return;
        }

        // 🔹 Strong password check (optional)
        let strongPass = /^(?=.*[A-Za-z])(?=.*\d).{6,}$/;
        if (!strongPass.test(password)) {
            errorBox.text('Password must contain letters and numbers.');
            return;
        }

        // 🔹 OTP validation (exactly 6 digits)
        if (!/^\d{6}$/.test(otp)) {
            errorBox.text('Please enter valid 6-digit OTP.');
            return;
        }

        $.post(webcoss_ajax.ajaxurl, {
            action: 'send_otp',
            name: $('#reg_name').val(),
            email: $('#reg_email').val(),
            password: $('#reg_password').val(),
            webcoss_otp_nonce_field: $('input[name="webcoss_otp_nonce_field"]').val()
        }, function (response) {

            if (response.success) {
                $('#reg_message').html(response.data);
                $('.otp-field').show();
                $('#register_btn').show();
                $('#send_otp_btn').hide();
            } else {
                $('#reg_message').html(response.data);
            }

        });

    });

    $('#webcoss-register-form').submit(function (e) {
        e.preventDefault();

        $.post(webcoss_ajax.ajaxurl, {
            action: 'verify_register',
            name: $('#reg_name').val(),
            email: $('#reg_email').val(),
            password: $('#reg_password').val(),
            otp: $('#reg_otp').val(),
            webcoss_otp_nonce_field: $('input[name="webcoss_otp_nonce_field"]').val()
        }, function (response) {

            if (response.success) {
                window.location.href = response.data.redirect;
            } else {
                $('#reg_message').html(response.data);
            }

        });

    });

});