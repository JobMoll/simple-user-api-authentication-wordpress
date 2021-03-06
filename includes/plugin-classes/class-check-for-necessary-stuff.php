<?php

function suaa_check_for_necessary_stuff() {
	
    // check if all the functions exist
    if (function_exists('wp_generate_auth_cookie') && function_exists('wp_authenticate') && function_exists('wp_validate_auth_cookie') && function_exists('wp_parse_auth_cookie')) {
    // check if accessed trough SSL
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
    header("HTTP/1.1 417 Expectation error");
    $errorMessage = array('status' => 'error', 'message' => 'No https connection...');
    wp_send_json($errorMessage, 417);
    exit; 
    } else {
    return true;
    }
    } else {
    header("HTTP/1.1 417 Expectation error");
    $errorMessage = array('status' => 'error', 'message' => 'The functions wp_generate_auth_cookie, wp_validate_auth_cookie, wp_parse_auth_cookie or wp_authenticate doesn\'t exist');
    wp_send_json($errorMessage, 417);
    exit; 
		
    }
}