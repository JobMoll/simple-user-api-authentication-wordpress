<?php

function suaa_forgot_password(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-anti-brute-force.php';

if (suaa_check_for_necessary_stuff() == true) {
$usernameOrEmail = sanitize_user($request['username_or_email']);
$userID = suaa_check_for_email_or_username($usernameOrEmail);

if ($userID != false) {
$userData = get_userdata($userID); 

$userFirstname = $userData->first_name;
$websiteName = get_bloginfo('name');

$userLogin = $userData->user_login;
$userEmail = $userData->user_email;
$key = get_password_reset_key($userData);

  $body .= 'Hey ' . $userFirstname . ',' . "</br></br>";
  $body .= 'Someone requested a password reset for the following account: <b>' . $userLogin . '</b> on ' . network_site_url('/') . '.' . "</br></br>";
  $body .= 'If you did not request the password reset or if you did it by accident, just ignore this email and nothing will happen :)' . "</br></br>";
  $body .= 'To reset your password, visit the following address:' . "</br>";
  $body .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($userLogin), 'login') . "</br></br></br>";
  $body .= 'Cheers, the ' . $websiteName . ' team!';
  
  $subject = 'Password reset request for ' . $userFirstname . ' ' . $userData->last_name . ' on - ' . $websiteName;

  $headers = array('Content-Type: text/html; charset=UTF-8');
 
  wp_mail($userEmail, $subject, $body, $headers);

  $showSuccessMessage = array('status' => 'success', 'title' => 'Email has been send!', 'message' => 'Check your email and click on the link to reset your password', 'forgot_password_send' => true);
    wp_send_json($showSuccessMessage, 200);
} else {
  $errorMessage = array('status' => 'error', 'title' => 'No existing user', 'message' => 'This username or email does not exist on this app...', 'forgot_password_send' => false);
    wp_send_json($errorMessage, 200); 
}
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'error', 'message' => "Some critical function isn't working");
	wp_send_json($errorMessage, 503);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'forgot-password',array(
                'methods'  => 'POST',
                'callback' => 'suaa_forgot_password',
	            'permission_callback' => '__return_true',
      ));
});