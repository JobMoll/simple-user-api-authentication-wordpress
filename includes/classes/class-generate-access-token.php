<?php

function generate_access_token(WP_REST_Request $request) {
include str_replace('classes', 'plugin-classes', plugin_dir_path(__FILE__)) . 'class-check-for-necessary-stuff.php';

$refreshTokenScheme = get_option('simple_api_authentication_refresh_token_scheme');

$accessTokenScheme = get_option('simple_api_authentication_access_token_scheme');
$accessTokenValidTime = get_option('simple_api_authentication_access_token_valid_length'); // needs to be a valid strtotime English textual datetime

    if (check_for_necessary_stuff() == true) {
    $refreshToken = sanitize_user($request['refresh_token']);
    $validateRefreshToken = wp_validate_auth_cookie($refreshToken, $refreshTokenScheme);
    // $validateRefreshToken returns user id if not false

    if ($validateRefreshToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'This refresh token is invalid or revoked');
    echo json_encode($errorMessage);
    exit; 
    } else {

    // destroy the old access token
    $oldAccessToken = get_user_meta($validateRefreshToken, 'simple_api_authentication_latest_access_token', true);
    if (!empty($oldAccessToken)) {
    $oldAccessTokenData = wp_parse_auth_cookie($oldAccessToken, $accessTokenScheme);
    $manager = WP_Session_Tokens::get_instance($validateRefreshToken);
    $manager->destroy($oldAccessTokenData['token']);
    }
    
    // generate a new access token
    $newAccessToken = wp_generate_auth_cookie($validateRefreshToken, strtotime($accessTokenValidTime), $accessTokenScheme);
     
    // save the new refresh token to the user profile
    update_user_meta($validateRefreshToken, 'simple_api_authentication_latest_access_token', $newAccessToken); 
    
    // show the json data
    $newAccessTokenData = array('status' => 'succes', 'refresh_token_is_valid' => true,  'access_token' => $newAccessToken);
    echo json_encode($newAccessTokenData);
     
    } 
    } else {
    header("HTTP/1.1 401 Unauthorized");
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-api-authentication', 'generate-access-token',array(
                'methods'  => 'POST',
                'callback' => 'generate_access_token'
      ));
});