<?php

function suaa_generate_refresh_token(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-anti-brute-force.php';

$usernameOrEmail = sanitize_user($request['username_or_email']);

  if (suaa_check_if_brute_force_is_not_reached($usernameOrEmail) == true) {
    if (suaa_check_for_necessary_stuff() == true) {
    $current_user_data = wp_authenticate($usernameOrEmail, sanitize_user($request['password']));

    if (is_wp_error($current_user_data)) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => suaa_add_new_brute_force_attempt($usernameOrEmail));
    echo json_encode($errorMessage);
    exit; 
    } else {
    $refreshTokenScheme = get_option('suaa_refresh_token_scheme');
    $userID = $current_user_data->ID;

    update_user_meta($userID, 'suaa_wrong_brute_force_attempts', 0);

    // destroy the old refresh token
    $oldRefreshToken = get_user_meta($userID, 'suaa_latest_refresh_token', true);
    if (!empty($oldRefreshToken)) {
    $oldRefreshTokenData = wp_parse_auth_cookie($oldRefreshToken, $refreshTokenScheme);
    $manager = WP_Session_Tokens::get_instance($userID);
    $manager->destroy($oldRefreshTokenData['token']);
    }
    
    
    // generate a new refresh token
    $refreshTokenValidTimeUserSpecific = get_user_meta($userID, 'suaa_refresh_token_valid_length_user', true);
   
    // 0, '', '0', false - are all considered empty
    if (!empty($refreshTokenValidTimeUserSpecific)) {
       $refreshTokenValidTime = $refreshTokenValidTimeUserSpecific;
    } else {
       $refreshTokenValidTime = get_option('suaa_refresh_token_valid_length');
   
    }
    $newRefreshToken = wp_generate_auth_cookie($userID, strtotime($refreshTokenValidTime), $refreshTokenScheme);
 
 
    // save the new refresh token to the user profile
    update_user_meta($userID, 'suaa_latest_refresh_token', $newRefreshToken); 
 
 
    // show the json data
    $newRefreshTokenData = array('status' => 'success', 'refresh_token' => $newRefreshToken, 'user_id' => $userID);
    echo json_encode($newRefreshTokenData);
     
     
    } 
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'failed', 'message' => "Some critical function isn't working");
	echo json_encode($errorMessage);
    exit;    
    }
  } else {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'You have made to much wrong login attempts... Wait ' . get_option('suaa_brute_force_block_time') . ' min before trying again!');
    echo json_encode($errorMessage);
    exit;    
  }
}
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'generate-refresh-token',array(
                'methods'  => 'POST',
                'callback' => 'suaa_generate_refresh_token',
	            'permission_callback' => '__return_true',
      ));
});
