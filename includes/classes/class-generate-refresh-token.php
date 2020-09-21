<?php

function suaa_generate_refresh_token(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-anti-brute-force.php';

$refreshTokenScheme = get_option('suaa_refresh_token_scheme');
$refreshTokenValidTime = get_option('suaa_refresh_token_valid_length');

$username = sanitize_user($request['username']);

  if (suaa_check_if_brute_force_is_not_reached($username) == true) {
    if (suaa_check_for_necessary_stuff() == true) {
    $current_user_data = wp_authenticate($username, sanitize_user($request['password']));

    if (is_wp_error($current_user_data)) {
    header("HTTP/1.1 401 Unauthorized");
   // add_wrong_brute_force_attempt($username);
    $errorMessage = array('status' => 'failed', 'message' => suaa_add_new_brute_force_attempt($username));
    echo json_encode($errorMessage);
    exit; 
    } else {

    // destroy the old refresh token
    $oldRefreshToken = get_user_meta($current_user_data->ID, 'suaa_latest_refresh_token', true);
    if (!empty($oldRefreshToken)) {
    $oldRefreshTokenData = wp_parse_auth_cookie($oldRefreshToken, $refreshTokenScheme);
    $manager = WP_Session_Tokens::get_instance($current_user_data->ID);
    $manager->destroy($oldRefreshTokenData['token']);
    }
    
    // generate a new refresh token
    $newRefreshToken = wp_generate_auth_cookie($current_user_data->ID, strtotime($refreshTokenValidTime), $refreshTokenScheme);
 
    // save the new refresh token to the user profile
    update_user_meta($current_user_data->ID, 'suaa_latest_refresh_token', $newRefreshToken); 
 
    // show the json data
    $newRefreshTokenData = array('status' => 'succes', 'refresh_token' => $newRefreshToken);
    echo json_encode($newRefreshTokenData);
     
    } 
    } else {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'Some of the function needed for this call don\'t seem to work');
    echo json_encode($errorMessage);
    exit;    
    }
  } else {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'failed', 'message' => 'You have made to much wrong login attempts for this username... Try again in a few minutes.');
    echo json_encode($errorMessage);
    exit;    
  }
}
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-api-authentication', 'generate-refresh-token',array(
                'methods'  => 'POST',
                'callback' => 'suaa_generate_refresh_token',
	            'permission_callback' => '__return_true',
      ));
});
