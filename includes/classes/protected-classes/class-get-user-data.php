<?php

function suaa_get_user_data(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';

    if (suaa_check_for_necessary_stuff() == true) {
    $accessTokenScheme = get_option('suaa_access_token_scheme');
    $access_token = sanitize_user($request['access_token']);
    $validateAccessToken = wp_validate_auth_cookie($access_token, $accessTokenScheme);

    if ($validateAccessToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'This access token is invalid or revoked');
    echo json_encode($errorMessage);
    exit; 
    } else {

    $currentUserData = get_user_by('ID', $validateAccessToken);
    $currentUserDataArray = array('user_id' => $validateAccessToken, 'user_nicename' => $currentUserData->user_nicename, 'user_registered' => $currentUserData->user_registered, 'user_email' => $currentUserData->user_email);

    $currentUserDataDisplay = array('status' => 'success', 'access_token_is_valid' => true, 'user_data' => $currentUserDataArray);
    echo json_encode($currentUserDataDisplay);
     
    } 
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'failed', 'message' => "Some critical function isn't working");
	echo json_encode($errorMessage);
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