<?php

function suaa_delete_user_tokens(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';

if (suaa_check_for_necessary_stuff() == true) {
$userID = sanitize_text_field($request['user_id']);
$refreshTokenScheme = get_option('suaa_refresh_token_scheme');
$accessTokenScheme = get_option('suaa_access_token_scheme');

    // destroy the old refresh token
    $oldRefreshToken = get_user_meta($userID, 'suaa_latest_refresh_token', true);
    if (!empty($oldRefreshToken)) {
    $oldRefreshTokenData = wp_parse_auth_cookie($oldRefreshToken, $refreshTokenScheme);
    $manager = WP_Session_Tokens::get_instance($userID);
    $manager->destroy($oldRefreshTokenData['token']);
    update_user_meta($userID, 'suaa_latest_refresh_token', ''); 
    }
    
    // destroy the old access token
    $oldAccessToken = get_user_meta($userID, 'suaa_latest_access_token', true);
    if (!empty($oldAccessToken)) {
    $oldAccessTokenData = wp_parse_auth_cookie($oldAccessToken, $accessTokenScheme);
    $manager = WP_Session_Tokens::get_instance($userID);
    $manager->destroy($oldAccessTokenData['token']);
    update_user_meta($userID, 'suaa_latest_access_token', ''); 
    }
    
    $showSuccessMessage = array('status' => 'success', 'tokens_deleted' => true);
    wp_send_json($showSuccessMessage, 200);
     
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'error', 'message' => "Some critical function isn't working");
	wp_send_json($errorMessage, 503);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'delete-user-tokens',array(
                'methods'  => 'POST',
                'callback' => 'suaa_delete_user_tokens',
	            'permission_callback' => '__return_true',
      ));
});