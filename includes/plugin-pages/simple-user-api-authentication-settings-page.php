<?php
// save the values basic values
if (strpos($_SERVER["REQUEST_URI"], '/wp-admin/options-general.php?page=simple-user-api-authentication') !== false && isset($_POST['simple_api_authentication_access_token_valid_length_value']) && isset($_POST['simple_api_authentication_refresh_token_valid_length_value'])) {
    
    if (isset($_POST['simple_api_authentication_refresh_token_scheme_value']) && sanitize_text_field($_POST['simple_api_authentication_refresh_token_scheme_value']) != get_option('simple_api_authentication_refresh_token_scheme')) {
    update_option('simple_api_authentication_refresh_token_scheme', sanitize_text_field($_POST['simple_api_authentication_refresh_token_scheme_value']));
    }

    if (isset($_POST['simple_api_authentication_refresh_token_valid_length_value'])) {
    update_option('simple_api_authentication_refresh_token_valid_length', sanitize_text_field($_POST['simple_api_authentication_refresh_token_valid_length_value']));
    }

    if (isset($_POST['simple_api_authentication_access_token_scheme_value']) && sanitize_text_field($_POST['simple_api_authentication_access_token_scheme_value']) != get_option('simple_api_authentication_access_token_scheme')) {
    update_option('simple_api_authentication_access_token_scheme', sanitize_text_field($_POST['simple_api_authentication_access_token_scheme_value']));
    }
    
    if (isset($_POST['simple_api_authentication_access_token_valid_length_value'])) {
    update_option('simple_api_authentication_access_token_valid_length', sanitize_text_field($_POST['simple_api_authentication_access_token_valid_length_value']));
    }

    // activate the success message basic values
    add_action( 'admin_notices', 'simple_api_authentication_settings_page_saved_success' );

}

// success message basic values
function simple_api_authentication_settings_page_saved_success() {
    ?>
<div class="notice notice-success is-dismissible"> 
	<p><strong>Plugin settings are saved!</strong></p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
    <?php
}


if (strpos($_SERVER["REQUEST_URI"], '/wp-admin/options-general.php?page=simple-user-api-authentication') !== false && isset($_POST['simple_api_authentication_reset_tokens'])) {
    // regenerate the refresh token scheme
    update_option('simple_api_authentication_refresh_token_scheme', bin2hex(random_bytes(8)), 'no');
    // regenerate the access token scheme
    update_option('simple_api_authentication_access_token_scheme', bin2hex(random_bytes(8)), 'no');
    
    WP_Session_Tokens::destroy_all_for_all_users();

    // activate the success message reset tokens
    add_action( 'admin_notices', 'simple_api_authentication_settings_page_token_reset_success' );
}


// success message reset token reset tokens
function simple_api_authentication_settings_page_token_reset_success() {
    ?>
<div class="notice notice-success is-dismissible"> 
	<p><strong>Token have been resetted succesfully!</strong></p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
    <?php
}

// content of the custom settings page for this plugin
function simple_user_api_authentication_settings() {
    ?>
<style>
h2 {
    font-size: 1.6em;
    margin: 2em 1px 1px 1px;
}
h3 {
    font-size: 1.2em;
    margin: 1em 1px 1px 1px;
    font-weight: 400;
}
p {
    font-size: 15px;
}
.tipURL {
    font-size: 13px;
    margin-left: 10px;
}
.inputSimpleUserAPIAuthentication {
    margin-left: 7px;
    font-size: 1.0em;
    width: 20%;
    max-width: 320px;
    min-width: 100px;
}
</style>
    
    <div class="wrap">
<h1>Simple User API Authentication Settings</h1>

<hr>
<p style="margin-bottom: 15px;">A simple interface to manage the Simple User API Authentication Settings.</p>

<form id="basicDataForm" method="post">
    
  <h2>Refresh token:</h2>
  <h3>Secret refresh token scheme:
  <input disabled class="inputSimpleUserAPIAuthentication" type="text" name="simple_api_authentication_refresh_token_scheme_value" value="<?php echo get_option('simple_api_authentication_refresh_token_scheme'); ?>" placeholder="98f71e6b28443bb9">
  </h3>
  
  <h3>Refresh token valid length:
  <input class="inputSimpleUserAPIAuthentication" type="text" name="simple_api_authentication_refresh_token_valid_length_value" value="<?php echo get_option('simple_api_authentication_refresh_token_valid_length'); ?>" placeholder="+ 3 weeks">
  <a class="tipURL" href="https://www.w3schools.com/php/func_date_strtotime.asp" target="_blank">Check the list of valid strtotime's</a>
  </h3>
  
  
  <h2>Access token:</h2>
  <h3>Secret access token scheme:
  <input disabled class="inputSimpleUserAPIAuthentication" type="text" name="simple_api_authentication_access_token_scheme_value" value="<?php echo get_option('simple_api_authentication_access_token_scheme'); ?>" placeholder="c52d2d81a6cf2f7a">
  </h3>
  
  <h3>Access token valid length:
  <input class="inputSimpleUserAPIAuthentication" type="text" name="simple_api_authentication_access_token_valid_length_value" value="<?php echo get_option('simple_api_authentication_access_token_valid_length'); ?>" placeholder="+ 15 mins">
  <a class="tipURL" href="https://www.w3schools.com/php/func_date_strtotime.asp" target="_blank">Check the list of valid strtotime's</a>
  </h3>
  
</form>
  <button type="submit" form="basicDataForm" style="margin-top: 20px;" class="save-button">Save version</button>


  <h2>Reset all refresh / access tokens:</h2>
  <h3>This action also means that every user has to log in again (including the admin) </br> This action will also regenerate the refresh / access token schemes for extra security:
<form method="post">
    <input type="submit" name="simple_api_authentication_reset_tokens" value="Reset" onclick="return confirm('Are you sure? All users are logged out immediately and the tokens are reset.')"/>
</form>
  </h3>

    </div>
    <?php
}
