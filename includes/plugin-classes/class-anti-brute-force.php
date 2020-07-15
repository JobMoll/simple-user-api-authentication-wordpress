<?php

function check_if_brute_force_is_not_reached($username) {
$bruteForceWrongAttemptsMax = 5;
$bruteForceWrongAttemptsBlockForTimeInMinutes = '1';

    $user_id = check_for_email_or_username($username);
    
     if ($user_id != false && !empty($user_id)) {
        
        $bruteForceWrongAttempts = get_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts', true);
        $bruteForceWrongAttemptsBlockEndTime = get_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts_end_time', true);
        
           $ts1 = strtotime('now');
           $seconds_diff =  $ts1 - $bruteForceWrongAttemptsBlockEndTime;                            
        
        // when everything is ok return true
        if ($bruteForceWrongAttemptsMax > intval($bruteForceWrongAttempts)) {
            
        return true;

        // reset to 0 when the block time has expired so they can try again
        } else if ($seconds_diff >= ($bruteForceWrongAttemptsBlockForTimeInMinutes * 60)) {
            update_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts_end_time', strtotime('now'));     
            update_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts', 0);
            return true;
            
            
        // when they have a wrong password set the block time
        } else if ($bruteForceWrongAttemptsMax <= intval($bruteForceWrongAttempts) && $seconds_diff >= 60) {
            update_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts_end_time', strtotime('+ ' . $bruteForceWrongAttemptsBlockForTimeInMinutes . ' min')); 
            return false;
        }
        
    } else {
        // username or email is not connected to an account
        return 'Wrong username / email & password combination';
    }
    return false;
}

function add_new_brute_force_attempt($username) {
$bruteForceWrongAttemptsMax = 5;

    $user_id = check_for_email_or_username($username);
    if ($user_id != false && !empty($user_id)) {
        
        $oldBruteForceWrongAttempts = get_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts', true);
        
        if ($bruteForceWrongAttemptsMax <= intval($oldBruteForceWrongAttempts)) {
        return 'You have made to many wrong login attempts... Try again in about 5 min'; 
        } else{
        update_user_meta($user_id, 'simple_api_authentication_wrong_brute_force_attempts', $oldBruteForceWrongAttempts + 1);
        
        return 'Wrong username / email & password combination. You have ' . (5 - ($oldBruteForceWrongAttempts + 1)) . ' attempts to go.';    
        }
    } else {
        // username or email is not connected to an account
        return 'Wrong username / email & password combination';
    }
}

function check_for_email_or_username($username) {
    if (is_email($username)) {
        $user = get_user_by('email', $username);
    } else {
        $user = get_user_by('login', $username);
    }
    
    if ($user) {
        return $user->ID;
    } else {
        return false;
    }
}


// 1. check i they don't already have 5 attempts
// check if not then return true

// if they already have had 5 attempts:
// 2. check if 