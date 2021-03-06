<?php

function suaa_get_app_passcode(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';

    if (suaa_check_for_necessary_stuff() == true) {
    $accessTokenScheme = get_option('suaa_access_token_scheme');
    $access_token = sanitize_text_field($request['access_token']);
    $validateAccessToken = wp_validate_auth_cookie($access_token, $accessTokenScheme);

    if ($validateAccessToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'error', 'message' => 'This access token is invalid or revoked');
    wp_send_json($errorMessage, 401);
    exit; 
    } else {
    $userAppPasscode = get_user_meta($validateAccessToken, 'suaa_app_passcode', true);
    
    if (empty($userAppPasscode)) {
    $errorMessage = array('status' => 'error', 'title' => 'User has not set a passcode', 'message' => 'The user has not set a passcode...', 'get_passcode' => false);
    wp_send_json($errorMessage, 200);
    
    } else {
    $successMessage = array('status' => 'success', 'access_token_is_valid' => true, 'app_passcode' => $userAppPasscode);
    wp_send_json($successMessage, 200);

    } 

    $errorMessage = array('status' => 'error', 'title' => 'Something went wrong :(', 'message' => 'Something went wrong with getting your passcode...', 'get_passcode' => false);
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
  register_rest_route( 'simple-user-api-authentication', 'get-app-passcode',array(
                'methods'  => 'POST',
                'callback' => 'suaa_get_app_passcode',
	            'permission_callback' => '__return_true',
      ));
});