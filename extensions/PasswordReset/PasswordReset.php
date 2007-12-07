<?php
/** \file
* \brief Contains setup code for the Password Reset Extension.
*/

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
        echo "Password Reset extension";
        exit(1);
}
 
$wgExtensionCredits['specialpage'][] = array(
    'name'=>'Password Reset',
    'url'=>'http://www.mediawiki.org/wiki/Extension:Password_Reset',
    'author'=>'Tim Laqua',
    'description'=>"Resets Wiki user's passwords - requires 'passwordreset' privileges",
    'version'=>'1.3'
);
 
$wgAutoloadClasses['PasswordReset'] = dirname(__FILE__) . '/PasswordReset_body.php';
$wgSpecialPages['PasswordReset'] = 'PasswordReset';
 
if ( version_compare( $wgVersion, '1.10.0', '<' ) ) {
    //Extension designed for 1.10.0+, but will work on some older versions
    //LoadAllMessages hook throws errors before 1.10.0
} else {
    $wgHooks['LoadAllMessages'][] = 'PasswordReset::loadMessages';
}
