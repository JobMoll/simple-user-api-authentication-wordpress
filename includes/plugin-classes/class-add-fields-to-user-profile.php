<?php

add_action( 'show_user_profile', 'show_extra_profile_fields_achievements' );
add_action( 'edit_user_profile', 'show_extra_profile_fields_achievements' );


function show_extra_profile_fields_achievements($currentUser) { 
?>
  <h3>Simple API Authentication</h3>

    <table class="form-table">

		<tr>
			<th><label for="simple_api_authentication_latest_refresh_token">Latest refresh token</label></th>
			<td>
				<input disabled type="text" name="simple_api_authentication_latest_refresh_token" id="simple_api_authentication_latest_refresh_token" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('simple_api_authentication_latest_refresh_token', $currentUser->ID)); ?>"></input><br/>
				  <span class="description">The latest refresh token of this user.</span>
			</td>
		</tr>

		<tr>
			<th><label for="simple_api_authentication_latest_access_token">Latest access token</label></th>
			<td>
				<input disabled type="text" name="simple_api_authentication_latest_access_token" id="simple_api_authentication_latest_access_token" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('simple_api_authentication_latest_access_token', $currentUser->ID)); ?>"></input><br/>
				  <span class="description">The latest access token of this user.</span>
			</td>
		</tr>		

		<tr>
			<th><label for="simple_api_authentication_wrong_brute_force_attempts">Brute force wrong attempts</label></th>
			<td>
				<input disabled type="text" name="simple_api_authentication_wrong_brute_force_attempts" id="simple_api_authentication_wrong_brute_force_attempts" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('simple_api_authentication_wrong_brute_force_attempts', $currentUser->ID)); ?>"></input><br/>
				  <span class="description">Count of how many wrong attempts within the given timeframe.</span>
			</td>
		</tr>
		
		<tr>
			<th><label for="simple_api_authentication_wrong_brute_force_attempts_end_time">Brute force wrong attempts end time</label></th>
			<td>
				<input disabled type="text" name="simple_api_authentication_wrong_brute_force_attempts_end_time" id="simple_api_authentication_wrong_brute_force_attempts_end_time" class="regular-text" value="<?php echo gmdate("Y-m-d H:i:s", intval(esc_attr(get_the_author_meta('simple_api_authentication_wrong_brute_force_attempts_end_time', $currentUser->ID)))); ?>"></input><br/>
				  <span class="description">The till the user can try to login in again.</span>
			</td>
		</tr>
		
	</table>
<?php
}

// add_action( 'personal_options_update', 'save_extra_profile_fields_achievements' );
// add_action( 'edit_user_profile_update', 'save_extra_profile_fields_achievements' );

// function save_extra_profile_fields_achievements($userID) {
// if ( current_user_can('administrator') ) {
    
//     	update_user_meta($userID, 'simple_api_authentication_latest_refresh_token', sanitize_text_field($_POST['simple_api_authentication_latest_refresh_token']));
//     	update_user_meta($userID, 'simple_api_authentication_latest_access_token', sanitize_text_field($_POST['simple_api_authentication_latest_access_token']));
    	
// } else {
//     return false;
// }
// }