<?php 
/**
 * Plugin Name: Simple User API Authentication (For Applications & Mobile Apps)
 * Plugin URI: https://mollup.nl/
 * Description: With this plugin you can easily and securely link your mobile app or any other application to your Wordpress website to retrieve information from the user.
 * Version: 1.0
 * Author: Job Moll - Mollup
 * Author URI: https://mollup.nl
 */
 
if (!defined('WPINC')) {
	die;
}

function activate_suaa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-classes/class-simple-api-authentication-activator.php';
	suaa_activator::activate();
}

function uninstall_suaa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-classes/class-simple-api-authentication-uninstaller.php';
	suaa_uninstaller::uninstall();
}

register_activation_hook(__FILE__, 'activate_suaa');
register_uninstall_hook(__FILE__, 'uninstall_suaa');


// No other third party plugin needed 
// Developed for the newest Wordpress version
// Brute force protection
// Refresh tokens and access tokens
// Random refresh / access token schemes
// Super easy to understand and well documented
// Simple endpoint for custom ACF fields from a user account
// Manage plugin options from the plugin settings page in the admin panel
// Destroy all the current refresh and access tokens
// Automatically destroy the old refresh / access token when requesting a new one

// 1. Generate a refresh token with the user credentials (the refresh token is valid for a long time and we use the refresh token so we don't have to send the user credentials in every call a.k.a. more secure)
// 2. Use the refresh token to get a access token (the access token is only valid for a short time. So when someone gets their hands on the access token they only have a limited time to do something)
// 3. Use the access token to get the user data that you'd like


// Used for getting a refresh token using the user login credentials
// Request: POST
// Endpoint: wp-json/simple-api-authentication/generate-refresh-token
// Example body: {"username": "admin", "password": "pass"}
include 'includes/classes/class-generate-refresh-token.php';

// Used get the acccess token using the refresh token
// Request: POST
// Endpoint: wp-json/simple-api-authentication/generate-access-token
// Example body: {"refresh_token": "refresh_token_here"}
include 'includes/classes/class-generate-access-token.php';

// Used to get the user data safely
// Request: GET
// Endpoint: wp-json/simple-api-authentication/get-user-data
// Example body: {"access_token": "access_token_here"}
include 'includes/classes/class-get-user-data.php';

// Used when for example logging out an user. It will destroy all the valid keys connected to their account
// Request: POST
// Endpoint: wp-json/simple-api-authentication/delete-user-tokens
// Example body: {"access_token": "access_token_here"}
include 'includes/classes/class-delete-user-tokens.php';



// Adds some elements to the user profile for the admin to see
include 'includes/plugin-classes/class-add-fields-to-user-profile.php';


// add the plugin settings page to the settings menu
include 'includes/plugin-pages/simple-user-api-authentication-settings-page.php';
add_action( 'admin_menu', 'tidywp_admin_add_admin_menu' );
function tidywp_admin_add_admin_menu(  ) {
    add_options_page(
    'Simple User API Authentication',
    'Simple User API Authentication',
    'manage_options',
    'simple-user-api-authentication',
    'suaa_settings'
    );
}



// TO-DO 3
// Brute force check for the wp_authenticate 
// if wrong 5 times in 5 min block the whole user login for 3 min
// check with the unix timestamp

// TO-DO 4
// add an api endpoint for custom ACF fields
// ability to ask for multiple at the same time
