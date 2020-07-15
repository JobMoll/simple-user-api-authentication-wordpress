<?php

function delete_user_tokens(WP_REST_Request $request) {
include str_replace('classes', 'plugin-classes', plugin_dir_path(__FILE__)) . 'class-check-for-necessary-stuff.php';

$refreshTokenScheme = get_option('simple_api_authentication_refresh_token_scheme');

$accessTokenScheme = get_option('simple_api_authentication_access_token_scheme');

    if (check_for_necessary_stuff() == true) {
    $accessToken = sanitize_user($request['access_token']);
    $validateAccessToken = wp_validate_auth_cookie($accessToken, $accessTokenScheme);
    // $validateRefreshToken returns user id if not false

    if ($validateAccessToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'This refresh token is invalid or revoked');
    echo json_encode($errorMessage);
    exit; 
    } else {

    // destroy the old refresh token
    $oldRefreshToken = get_user_meta($validateAccessToken, 'simple_api_authentication_latest_refresh_token', true);
    if (!empty($oldRefreshToken)) {
    $oldRefreshTokenData = wp_parse_auth_cookie($oldRefreshToken, $refreshTokenScheme);
    $manager = WP_Session_Tokens::get_instance($validateAccessToken);
    $manager->destroy($oldRefreshTokenData['token']);
    update_user_meta($validateAccessToken, 'simple_api_authentication_latest_refresh_token', ''); 
    }
    
    // destroy the old access token
    $oldAccessToken = get_user_meta($validateAccessToken, 'simple_api_authentication_latest_access_token', true);
    if (!empty($oldAccessToken)) {
    $oldAccessTokenData = wp_parse_auth_cookie($oldAccessToken, $accessTokenScheme);
    $manager = WP_Session_Tokens::get_instance($validateAccessToken);
    $manager->destroy($oldAccessTokenData['token']);
    update_user_meta($validateAccessToken, 'simple_api_authentication_latest_access_token', ''); 
    }
    
    $showSuccessMessage = array('status' => 'succes', 'access_token_is_valid' => true,  'tokens_deleted' => true);
    echo json_encode($showSuccessMessage);
     
    } 
    } else {
    header("HTTP/1.1 401 Unauthorized");
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-api-authentication', 'delete-user-tokens',array(
                'methods'  => 'POST',
                'callback' => 'delete_user_tokens'
      ));
});