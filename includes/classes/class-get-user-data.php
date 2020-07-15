<?php

function get_user_data(WP_REST_Request $request) {
include str_replace('classes', 'plugin-classes', plugin_dir_path(__FILE__)) . 'class-check-for-necessary-stuff.php';

$accessTokenScheme = get_option('simple_api_authentication_access_token_scheme');

    if (check_for_necessary_stuff() == true) {
    $access_token = sanitize_user($request['access_token']);
    $validateAccessToken = wp_validate_auth_cookie($access_token, $accessTokenScheme);

    if ($validateAccessToken == false) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'This access token is invalid or revoked');
    echo json_encode($errorMessage);
    exit; 
    } else {

    $currentUserData = get_user_by('ID', $validateAccessToken);
    $currentUserDataArray = array('user_id' => $currentUserData->id, 'user_nicename' => $currentUserData->user_nicename, 'user_registered' => $currentUserData->user_registered, 'user_email' => $currentUserData->user_email);

    $currentUserDataDisplay = array('status' => 'succes', 'access_token_is_valid' => true, 'user_data' => $currentUserDataArray);
    echo json_encode($currentUserDataDisplay);
     
    } 
    } else {
    header("HTTP/1.1 401 Unauthorized");
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-api-authentication', 'get-user-data',array(
                'methods'  => 'GET',
                'callback' => 'get_user_data'
      ));
});