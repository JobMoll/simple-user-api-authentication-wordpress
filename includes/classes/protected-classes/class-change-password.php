<?php

function suaa_change_password(WP_REST_Request $request) {
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
    $newPassword = $request['new_password'];
    
    $newUserData = reset_password($currentUserData, $newPassword);
    
    if (is_wp_error($newUserData)) {
    $errorMessage = array('status' => 'error', 'title' => 'Something went wrong :(', 'message' => $newUserData->get_error_message(), 'changed_password' => false);
    echo json_encode($errorMessage);
    
    } else {
    $successMessage = array('status' => 'success', 'title' => 'Your password has been changed', 'message' => 'We have successfully changed your password!', 'changed_password' => true);
    echo json_encode($successMessage);
     
    } 
    // } else {
    // $successMessage = array('status' => 'error', 'title' => "Password has to be unique", 'message' => 'Your new password must be different from your old password!' . suaa_wp_check_password($newPassword, $hasedPassword, $validateAccessToken), 'changed_password' => false);
    // echo json_encode($successMessage);
    
    // }
    }
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'failed', 'message' => "Some critical function isn't working");
	echo json_encode($errorMessage);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'change-password',array(
                'methods'  => 'POST',
                'callback' => 'suaa_change_password',
	            'permission_callback' => '__return_true',
      ));
});