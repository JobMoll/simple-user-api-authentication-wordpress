<?php

function suaa_generate_access_token(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';

    if (suaa_check_for_necessary_stuff() == true) {
    $refreshTokenScheme = get_option('suaa_refresh_token_scheme');
    $refreshToken = sanitize_text_field($request['refresh_token']);
    $validateRefreshToken = wp_validate_auth_cookie($refreshToken, $refreshTokenScheme);
    // $validateRefreshToken returns user id if not false

    if ($validateRefreshToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'error', 'message' => 'This refresh token is invalid or revoked');
    echo json_encode($errorMessage);
    exit; 
    } else {
    $accessTokenScheme = get_option('suaa_access_token_scheme');
    $accessTokenValidTime = get_option('suaa_access_token_valid_length');

    // destroy the old access token
    $oldAccessToken = get_user_meta($validateRefreshToken, 'suaa_latest_access_token', true);
    if (!empty($oldAccessToken)) {
    $oldAccessTokenData = wp_parse_auth_cookie($oldAccessToken, $accessTokenScheme);
    $manager = WP_Session_Tokens::get_instance($validateRefreshToken);
    $manager->destroy($oldAccessTokenData['token']);
    }
    
    // generate a new access token
    $newAccessToken = wp_generate_auth_cookie($validateRefreshToken, strtotime($accessTokenValidTime), $accessTokenScheme);
     
    // save the new refresh token to the user profile
    update_user_meta($validateRefreshToken, 'suaa_latest_access_token', $newAccessToken); 
    
    // show the json data
    $newAccessTokenData = array('status' => 'success', 'refresh_token_is_valid' => true,  'new_access_token' => $newAccessToken);
    echo json_encode($newAccessTokenData);
     
    } 
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'error', 'message' => "Some critical function isn't working");
	echo json_encode($errorMessage);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'generate-access-token',array(
                'methods'  => 'POST',
                'callback' => 'suaa_generate_access_token',
	            'permission_callback' => '__return_true',
      ));
});