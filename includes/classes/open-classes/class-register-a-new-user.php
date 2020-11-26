<?php

function suaa_register_a_new_user(WP_REST_Request $request) {
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-check-for-necessary-stuff.php';
require_once ABSPATH . '/wp-content/plugins/simple-user-api-authentication-wordpress/includes/plugin-classes/class-anti-brute-force.php';

if (suaa_check_for_necessary_stuff() == true) {
$username = sanitize_user($request['username']);
$userEmail = sanitize_email($request['email']);

$newUser = register_new_user($username, $userEmail);

    if (is_wp_error($newUser)) {
    header("HTTP/1.1 401 Unauthorized");
    $errorMessage = array('status' => 'error', 'title' => 'Something went wrong :(', 'message' => $newUser->get_error_message(), 'created_new_user' => false);
    wp_send_json($errorMessage, 401);
    exit; 
    } else {
        
    $showSuccessMessage = array('status' => 'success', 'title' => 'You have been registered!', 'message' => 'Check your email to set your password!', 'created_new_user' => true);
    wp_send_json($showSuccessMessage, 200);
    }
    } else {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
	$errorMessage = array('status' => 'error', 'message' => "Some critical function isn't working");
	wp_send_json($errorMessage, 503);
    exit;    
    }
 }
 
 add_action('rest_api_init', function () {
  register_rest_route( 'simple-user-api-authentication', 'register-a-new-user',array(
                'methods'  => 'POST',
                'callback' => 'suaa_register_a_new_user',
	            'permission_callback' => '__return_true',
      ));
});