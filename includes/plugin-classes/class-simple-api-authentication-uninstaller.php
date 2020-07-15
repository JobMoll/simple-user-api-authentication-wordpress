<?php

class simple_api_authentication_uninstaller {

	public static function uninstall() {
    // secret refresh token scheme
    delete_option('simple_api_authentication_refresh_token_scheme');
    // refresh token valid length
    delete_option('simple_api_authentication_refresh_token_valid_length');
    
    // secret access token scheme
    delete_option('simple_api_authentication_access_token_scheme');
    // access token valid length
    delete_option('simple_api_authentication_access_token_valid_length');
	}

}
