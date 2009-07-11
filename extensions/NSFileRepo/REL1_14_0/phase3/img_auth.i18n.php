<?php
/**
 * Internationalisation file for img_auth script (see see http://www.mediawiki.org/wiki/Manual:Image_Authorization).
*/

$messages = array();

/** English
 * @author Jack D. Pond
 */
$messages['en'] = array(
	'image_auth-desc' 		=> 'Image authorisation script',
	'image_auth-nopathinfo' => "Missing PATH_INFO.  Your server is not set up to pass this information - 
may be CGI-based and can't support img_auth.  See `Image Authorization` on MediaWiki.",
	'image_auth-notindir' 	=> "Requested path not in upload directory.",
	'image_auth-badtitle' 	=> "Unable to construct a valid Title from `$1`.",
	'image_auth-nologinnWL' => "Not logged in and `$1` not in whitelist.",
	'image_auth-nofile' 	=> "`$1` does not exist.",
	'image_auth-isdir' 		=> "`$1` is a directory.",
	'image_auth-streaming' 	=> "Streaming `$1`.",
	'image_auth-public'		=> "The function of img_auth.php is to output files from a private wiki. This wiki
is configured as a public wiki. For optimal security, img_auth.php is disabled for this case.",
	'image_auth-noread'		=> "User does not have access to read `$1`."
);

/** Message documentation (Message documentation)
 * @author Jack D. Pond
 */
$messages['qqq'] = array(
	'image_auth-desc' => 'Image authorisation script'
);

