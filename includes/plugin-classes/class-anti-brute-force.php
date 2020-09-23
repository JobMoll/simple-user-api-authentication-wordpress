<?php

function suaa_check_if_brute_force_is_not_reached($username) {
$bruteForceWrongAttemptsMax = get_option('suaa_brute_force_block_after_attempts');

    $user_id = suaa_check_for_email_or_username($username);
    
     if ($user_id != false && !empty($user_id)) {
        
        $bruteForceWrongAttempts = get_user_meta($user_id, 'suaa_wrong_brute_force_attempts', true);
        $bruteForceWrongAttemptsBlockEndTime = get_user_meta($user_id, 'suaa_wrong_brute_force_attempts_end_time', true);
        
           $timestampNow = strtotime('now');
           $secondsDiff =  $timestampNow - $bruteForceWrongAttemptsBlockEndTime;                            
   
   // if attempts if lower then then the max attempts and the time since the last wrong login has expired reset the brute force attempts
   if ($secondsDiff >= 0 && $bruteForceWrongAttemptsMax > intval($bruteForceWrongAttempts)) {
            update_user_meta($user_id, 'suaa_wrong_brute_force_attempts_end_time', strtotime('now'));     
            update_user_meta($user_id, 'suaa_wrong_brute_force_attempts', 0);
            return true;
   
   // if time is expired reset brute force         
   } else if ($secondsDiff >= 0) {
            update_user_meta($user_id, 'suaa_wrong_brute_force_attempts_end_time', strtotime('now'));     
            update_user_meta($user_id, 'suaa_wrong_brute_force_attempts', 0);
            return true;
    
    // if attempts are lower then the max attempts it's cool        
    } else if ($bruteForceWrongAttemptsMax > intval($bruteForceWrongAttempts)) {
        return true;

    // if everything is incorrect then return false since they don't have permission to try again
    } else {
        return false;
    }
    
  } else {
    // username or email is not connected to an account
    return 'Wrong username / email & password combination';
  }
  return false;
}

function suaa_add_new_brute_force_attempt($username) {
$bruteForceWrongAttemptsMax = get_option('suaa_brute_force_block_after_attempts');
$bruteForceWrongAttemptsBlockForTimeInMinutes = get_option('suaa_brute_force_block_time');

    $user_id = suaa_check_for_email_or_username($username);
    if ($user_id != false && !empty($user_id)) {
        
        $oldBruteForceWrongAttempts = get_user_meta($user_id, 'suaa_wrong_brute_force_attempts', true);
        
        if ($bruteForceWrongAttemptsMax <= intval($oldBruteForceWrongAttempts)) {
         return 'You have made to many wrong login attempts... Try again in ' . $bruteForceWrongAttemptsBlockForTimeInMinutes . ' min'; 
        } else{
         update_user_meta($user_id, 'suaa_wrong_brute_force_attempts', $oldBruteForceWrongAttempts + 1);
         update_user_meta($user_id, 'suaa_wrong_brute_force_attempts_end_time', strtotime('+ ' . $bruteForceWrongAttemptsBlockForTimeInMinutes . ' min')); 
         $attemptsToGo = ($bruteForceWrongAttemptsMax - ($oldBruteForceWrongAttempts + 1));
       
       if ($attemptsToGo != 0) {
        $errorMessage = 'Wrong username / email & password combination. You have ' . $attemptsToGo . ' attempts to go';  
       } else {
        $errorMessage = 'This was your last login attempt... You can try again in ' . $bruteForceWrongAttemptsBlockForTimeInMinutes . ' min';  
       }
        return $errorMessage;
       }
    } else {
        // username or email is not connected to an account
        return 'Wrong username / email & password combination';
    }
}

function suaa_check_for_email_or_username($username) {
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