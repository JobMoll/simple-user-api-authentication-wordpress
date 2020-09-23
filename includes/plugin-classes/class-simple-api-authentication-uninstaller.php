<?php

class suaa_uninstaller {

	public static function uninstall() {
    // secret refresh token scheme
    delete_option('suaa_refresh_token_scheme');
    // refresh token valid length
    delete_option('suaa_refresh_token_valid_length');
    
    // secret access token scheme
    delete_option('suaa_access_token_scheme');
    // access token valid length
    delete_option('suaa_access_token_valid_length');
    
    // brute force
    delete_option('suaa_brute_force_block_after_attempts');
    delete_option('suaa_brute_force_block_time');
	}

}
