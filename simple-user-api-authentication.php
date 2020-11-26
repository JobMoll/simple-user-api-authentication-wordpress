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
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-classes/class-simple-user-api-authentication-activator.php';
	suaa_activator::activate();
}

function uninstall_suaa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-classes/class-simple-user-api-authentication-uninstaller.php';
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
// Endpoint: wp-json/simple-user-api-authentication/generate-refresh-token
// Example body: {"username_or_email": "admin", "password": "pass"}
include 'includes/classes/open-classes/class-generate-refresh-token.php';

// Used get the acccess token using the refresh token
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/generate-access-token
// Example body: {"refresh_token": "refresh_token_here"}
include 'includes/classes/protected-classes/class-generate-access-token.php';

// Used to get the user data safely
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/get-user-data
// Example body: {"access_token": "access_token_here"}
include 'includes/classes/protected-classes/class-get-user-data.php';

// Used when for example logging out an user. It will destroy all the valid keys connected to their account
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/delete-user-tokens
// Example body: {"access_token": "access_token_here"}
include 'includes/classes/open-classes/class-delete-user-tokens.php';

// Used when the user forgot their password and wants to receive a link to get a new one
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/forgot-password
// Example body: {"username_or_email": "username_or_email_here"}
include 'includes/classes/open-classes/class-forgot-password.php';

// Used when a new users want to create an account
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/register-a-new-user
// Example body: {"username": "username_here", "email": "email_here"}
include 'includes/classes/open-classes/class-register-a-new-user.php';

// Used when a loggedin user wants to change their password
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/change-password
// Example body: {"access_token": "access_token_here", "new_password": "new_password_here"}
include 'includes/classes/protected-classes/class-change-password.php';

// Used to get the app passcode
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/get-app-passcode
// Example body: {"access_token": "access_token_here"}
include 'includes/classes/protected-classes/class-get-app-passcode.php';

// Used when changing the app passcode
// Request: POST
// Endpoint: wp-json/simple-user-api-authentication/change-app-passcode
// Example body: {"access_token": "access_token_here", "new_passcode": "new_passcode_here"}
include 'includes/classes/protected-classes/class-change-app-passcode.php';


// Adds some elements to the user profile for the admin to see
include 'includes/plugin-classes/class-add-fields-to-user-profile.php';


// add the plugin settings page to the settings menu
include 'includes/plugin-pages/simple-user-api-authentication-settings-page.php';
add_action( 'admin_menu', 'suaa_admin_add_admin_menu' );
function suaa_admin_add_admin_menu(  ) {
    add_options_page(
    'Simple User API Authentication',
    'Simple User API Authentication',
    'manage_options',
    'simple-user-api-authentication',
    'suaa_settings'
    );
}