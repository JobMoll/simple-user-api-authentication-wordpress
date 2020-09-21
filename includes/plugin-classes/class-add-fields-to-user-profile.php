<?php

add_action('show_user_profile', 'suaa_show_extra_profile_fields');
add_action('edit_user_profile', 'suaa_show_extra_profile_fields');


function suaa_show_extra_profile_fields($currentUser) { 
?>
  <h3>Simple API Authentication</h3>

    <table class="form-table">

		<tr>
			<th><label for="suaa_latest_refresh_token">Latest refresh token</label></th>
			<td>
				<input disabled type="text" name="suaa_latest_refresh_token" id="suaa_latest_refresh_token" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('suaa_latest_refresh_token', $currentUser->ID)); ?>"><br/>
				  <span class="description">The latest refresh token of this user.</span>
			</td>
		</tr>

		<tr>
			<th><label for="suaa_latest_access_token">Latest access token</label></th>
			<td>
				<input disabled type="text" name="suaa_latest_access_token" id="suaa_latest_access_token" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('suaa_latest_access_token', $currentUser->ID)); ?>"><br/>
				  <span class="description">The latest access token of this user.</span>
			</td>
		</tr>		

		<tr>
			<th><label for="suaa_wrong_brute_force_attempts">Brute force wrong attempts</label></th>
			<td>
				<input disabled type="text" name="suaa_wrong_brute_force_attempts" id="suaa_wrong_brute_force_attempts" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('suaa_wrong_brute_force_attempts', $currentUser->ID)); ?>"><br/>
				  <span class="description">Count of how many wrong attempts within the given timeframe.</span>
			</td>
		</tr>
		
		<tr>
			<th><label for="suaa_wrong_brute_force_attempts_end_time">Brute force wrong attempts end time</label></th>
			<td>
				<input disabled type="text" name="suaa_wrong_brute_force_attempts_end_time" id="suaa_wrong_brute_force_attempts_end_time" class="regular-text" value="<?php echo gmdate("Y-m-d H:i:s", intval(esc_attr(get_the_author_meta('suaa_wrong_brute_force_attempts_end_time', $currentUser->ID)))); ?>"><br/>
				  <span class="description">The till the user can try to login in again.</span>
			</td>
		</tr>
		
	</table>
<?php
}