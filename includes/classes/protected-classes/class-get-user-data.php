<?php

function suaa_get_user_data(WP_REST_Request $request) {
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

    $currentUserData = get_user_by('ID', $validateAccessToken);
    $currentUserDataArray = array('user_id' => $validateAccessToken, 'user_nicename' => $currentUserData->user_nicename, 'user_first_name' => $currentUserData->first_name, 'user_last_name' => $currentUserData->last_name, 'user_registered' => $currentUserData->user_registered, 'user_email' => $currentUserData->user_email);

    $currentUserDataDisplay = array('status' => 'success', 'access_token_is_valid' => true, 'user_data' => $currentUserDataArray);
    wp_send_json($currentUserDataDisplay, 200);
     
    } 
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'error', 'message' => "Some critical function isn't working");
	wp_send_json($errorMessage, 503);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'get-user-data',array(
                'methods'  => 'POST',
                'callback' => 'suaa_get_user_data',
	            'permission_callback' => '__return_true',
      ));
});