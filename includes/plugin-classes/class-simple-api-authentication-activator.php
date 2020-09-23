<?php

class suaa_activator {

	public static function activate() {
    // secret refresh token scheme
    add_option('suaa_refresh_token_scheme', bin2hex(random_bytes(8)), '', 'no');
    // refresh token valid length
    add_option('suaa_refresh_token_valid_length', '+ 3 weeks', '', 'no');
    
    // secret access token scheme
    add_option('suaa_access_token_scheme', bin2hex(random_bytes(8)), '', 'no');
    // access token valid length
    add_option('suaa_access_token_valid_length', '+ 25 mins', '', 'no');
    
    // brute force
    add_option('suaa_brute_force_block_after_attempts', 4, '', 'no');
    add_option('suaa_brute_force_block_time', 3, '', 'no');
	}

}