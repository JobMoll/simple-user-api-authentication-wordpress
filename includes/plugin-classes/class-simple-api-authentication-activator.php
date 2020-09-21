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
	}

}