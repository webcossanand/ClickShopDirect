<?php
/*
Plugin Name: PickNPrint OTP Registration
Description: Custom WooCommerce Registration with Email OTP
Version: 1.0
Author: Webcoss
*/

if (!defined('ABSPATH')) exit;

add_shortcode('webcoss_otp_register', 'webcoss_otp_register_form');

add_filter('woocommerce_login_redirect', function ($redirect, $user) {
    return site_url('/my-account/');
}, 10, 2);

add_action('template_redirect', function () {

    if (is_user_logged_in() && (is_page('login') || is_page('register'))) {
        wp_safe_redirect(site_url('/my-account/'));
        exit;
    }
});

add_action('template_redirect', function () {

    if (is_page('my-account') && ! is_user_logged_in()) {
        wp_safe_redirect(site_url('/login/'));
        exit;
    }
});


add_action('wp_enqueue_scripts', 'webcoss_otp_enqueue_styles');

function webcoss_otp_enqueue_styles()
{

    // Load only on register page
    if (is_page('register')) {

        wp_enqueue_style(
            'webcoss-otp-style',
            plugin_dir_url(__FILE__) . 'assets/css/otp.css',
            [],
            '1.0'
        );
    }
}

add_action('woocommerce_login_form_end', 'webcoss_add_register_button');

function webcoss_add_register_button()
{
    echo '<p style="margin-top:15px; text-align:center;">
            <a href="' . site_url('/register/') . '" class="woocommerce-button button__register">
                Create New Account
            </a>
          </p>';
}

function webcoss_otp_register_form()
{

    if (is_user_logged_in()) {
        return '<p>You are already logged in. <a href="' . site_url('/my-account/') . '">Go to Dashboard</a></p>';
    }

    ob_start();
?>

    <div class="webcoss-otp-wrapper">

        <h2>Create Account</h2>

        <form id="webcoss-register-form" method="post">

            <?php wp_nonce_field('webcoss_otp_nonce', 'webcoss_otp_nonce_field'); ?>

            <div class="webcoss-field">
                <label>Full Name</label>
                <input type="text" id="reg_name" name="name" placeholder="Enter name" required>
            </div>

            <div class="webcoss-field">
                <label>Email Address</label>
                <input type="email" id="reg_email" name="email" placeholder="Enter enmail" required>
            </div>

            <div class="webcoss-field">
                <label>Password</label>
                <input type="password" id="reg_password" name="password" placeholder="Enter password" required>
            </div>

            <div class="webcoss-field otp-field" style="display:none;">
                <label>Enter OTP</label>
                <input type="text" id="reg_otp" name="otp" placeholder="Enter 6-digit OTP">
            </div>

            <div class="webcoss-buttons">
                <button type="button" id="send_otp_btn" class="webcoss-btn">
                    Send OTP
                </button>

                <button type="submit" id="register_btn" class="webcoss-btn" style="display:none;">
                    Verify & Register
                </button>
            </div>

            <div id="reg_message" class="webcoss-message"></div>

        </form>

        <div class="webcoss-login-link">
            Already have an account?
            <a href="<?php echo site_url('/login/'); ?>">Login here</a>
        </div>

    </div>

<?php
    return ob_get_clean();
}

add_action('wp_ajax_send_otp', 'webcoss_send_otp');
add_action('wp_ajax_nopriv_send_otp', 'webcoss_send_otp');

function webcoss_send_otp()
{
    $name = sanitize_text_field($_POST['name']);

    if (!$name) {
        wp_send_json_error('Name is required.');
    }

    $email = sanitize_email($_POST['email']);

    if (!$email) {
        wp_send_json_error('Email is required.');
    }


    $password = sanitize_text_field($_POST['password']);

    if (!$password) {
        wp_send_json_error('Password is required.');
    }

    $otp_attempts_key = 'webcoss_otp_attempts_' . md5($email);
    $otp_attempts = get_transient($otp_attempts_key);

    if ($otp_attempts && $otp_attempts >= 3) {
        wp_send_json_error('Too many OTP requests. Please wait 5 minutes.');
    }

    // 🔒 NONCE CHECK (ADD THIS AT TOP)
    if (
        ! isset($_POST['webcoss_otp_nonce_field']) ||
        ! wp_verify_nonce($_POST['webcoss_otp_nonce_field'], 'webcoss_otp_nonce')
    ) {
        wp_send_json_error('Security verification failed.');
    }


    // ❌ Check if email already registered
    if (email_exists($email)) {
        wp_send_json_error('This email is already registered. Please login.');
    }

    $password = sanitize_text_field($_POST['password']);

    if (!$password) {
        wp_send_json_error('Password is required.');
    }

    if (strlen($password) < 6) {
        wp_send_json_error('Password must be at least 6 characters.');
    }

    $otp = wp_rand(100000, 999999);

    set_transient('webcoss_otp_' . $email, $otp, 300);

    // Count attempts
    if (!$otp_attempts) {
        set_transient($otp_attempts_key, 1, 5 * MINUTE_IN_SECONDS);
    } else {
        set_transient($otp_attempts_key, $otp_attempts + 1, 5 * MINUTE_IN_SECONDS);
    }

    wp_mail($email, 'Your OTP Code', 'Your OTP is: ' . $otp);

    wp_send_json_success('OTP sent successfully.');
}

add_action('wp_ajax_verify_register', 'webcoss_verify_register');
add_action('wp_ajax_nopriv_verify_register', 'webcoss_verify_register');

function webcoss_verify_register()
{
    // 🔒 NONCE CHECK (ADD THIS AT TOP)
    if (
        ! isset($_POST['webcoss_otp_nonce_field']) ||
        ! wp_verify_nonce($_POST['webcoss_otp_nonce_field'], 'webcoss_otp_nonce')
    ) {
        wp_send_json_error('Security verification failed.');
    }

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);

    if (strlen($password) < 6) {
        wp_send_json_error('Password must be at least 6 characters.');
    }
    $otp = $_POST['otp'];

    $saved_otp = get_transient('webcoss_otp_' . $email);

    if (!$saved_otp || $otp != $saved_otp) {
        wp_send_json_error('Invalid or expired OTP');
    }

    if (email_exists($email)) {
        wp_send_json_error('Email already registered. Please login.');
    }

    $user_id = wc_create_new_customer($email, $email, $password);


    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    }

    $name_parts = explode(' ', $name, 2);

    $first_name = $name_parts[0];
    $last_name  = isset($name_parts[1]) ? $name_parts[1] : '';

    wp_update_user([
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'display_name' => $name
    ]);

    update_user_meta($user_id, 'billing_first_name', $first_name);
    update_user_meta($user_id, 'billing_last_name', $last_name);

    // 🔥 AUTO LOGIN STARTS HERE
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $email, get_user_by('id', $user_id));

    delete_transient('webcoss_otp_' . $email);

    wp_send_json_success(array(
        'message'  => 'Registration successful',
        'redirect' => site_url('/my-account/')
    ));
}

add_action('wp_enqueue_scripts', function () {

    if (is_page('register')) {

        wp_enqueue_script(
            'webcoss-otp',
            plugin_dir_url(__FILE__) . 'assets/js/otp.js',
            ['jquery'],
            '1.0',
            true
        );

        wp_localize_script('webcoss-otp', 'webcoss_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
});
