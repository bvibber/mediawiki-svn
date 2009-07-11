<?php

/**
 * Image authorisation script
 *
 * To use this, see http://www.mediawiki.org/wiki/Manual:Image_Authorization
 *
 * - Set $wgUploadDirectory to a non-public directory (not web accessible)
 * - Set $wgUploadPath to point to this file
 *
 * Your server needs to support PATH_INFO; CGI-based configurations usually don't.
 *
 * @file
 */


/** 
	For security reasons, you usually don't want your user to know access was denied, just that it was.
	If you want to change this, you can set $wgImgAuthDetails to 'true' in localsettings.php and it will give the user the reason
	why access was denied.
**/

global $wgImgAuthDetails;
$wgImgAuthDetails = false;

define( 'MW_NO_OUTPUT_COMPRESSION', 1 );
require_once( dirname( __FILE__ ) . '/includes/WebStart.php' );
wfProfileIn( 'img_auth.php' );
require_once( dirname( __FILE__ ) . '/includes/StreamFile.php' );

global $wgMessageCache, $messages;
require_once( dirname( __FILE__ ) . '/img_auth.i18n.php' );
foreach( $messages as $lang => $LangMsg )
	$wgMessageCache->addMessages( $LangMsg, $lang );

$perms = User::getGroupPermissions( array( '*' ) );

// See if this is a public Wiki (no protections)
if ( in_array( 'read', $perms, true ) )
	wfPublicError(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-public'));

// Extract path and image information
if( !isset( $_SERVER['PATH_INFO'] ) )
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-nopathinfo'));

$path = $_SERVER['PATH_INFO'];
$filename = realpath( $wgUploadDirectory . $_SERVER['PATH_INFO'] );
$realUpload = realpath( $wgUploadDirectory );

// Basic directory traversal check
if( substr( $filename, 0, strlen( $realUpload ) ) != $realUpload )
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-notindir'));

// Extract the file name and chop off the size specifier
// (e.g. 120px-Foo.png => Foo.png)
$name = wfBaseName( $path );
if( preg_match( '!\d+px-(.*)!i', $name, $m ) )
	$name = $m[1];

// Check to see if the file exists
if( !file_exists( $filename ) )
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-nofile',$filename));

// Check to see if tried to access a directory
if( is_dir( $filename ) )
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-isdir',$filename));


$title = Title::makeTitleSafe( NS_FILE, $name );

// See if could create the title object
if( !$title instanceof Title ) 
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-badtitle',$name));

// Run hook
if (!wfRunHooks( 'ImgAuthBeforeStream', array( &$title, &$path, &$name, &$result ) ) )
	wfForbidden($result[0],$result[1]);
	
// Check the whitelist if needed, deprecated since usercan added
// $pTitle = $title->getPrefixedText();
// if( !$wgUser->getId() && ( !is_array( $wgWhitelistRead ) || !in_array( $pTitle, $wgWhitelistRead ) ) ) 
//	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-nologinnWL',$pTitle));


//  Check user authorization for this title
if( !$title->userCanRead() )
	wfForbidden(wfMsgHTML('image_auth-accessdenied'),wfMsgHTML('image_auth-noread',$name));


// Stream the requested file
wfDebugLog( 'img_auth', "Streaming `{$filename}`" );
wfStreamFile( $filename, array( 'Cache-Control: private', 'Vary: Cookie' ) );
wfLogProfilingData();

/**
 * Issue a standard HTTP 403 Forbidden header ($msg1) and an
 * error message ($msg2), then end the script
 */
function wfForbidden($msg1,$msg2) {
	global $wgImgAuthDetails;
	$detailMsg = $wgImgAuthDetails ? $msg2 : wfMsgHTML('badaccess-group0');
	wfDebugLog( 'img_auth', "wfForbidden Msg: ".$msg2 );
	header( 'HTTP/1.0 403 Forbidden' );
	header( 'Vary: Cookie' );
	header( 'Content-Type: text/html; charset=utf-8' );
	echo <<<ENDS
<html>
<body>
<h1>$msg1</h1>
<p>$detailMsg</p>
</body>
</html>
ENDS;
	wfLogProfilingData();
	exit();
}

/**
 * Show a 403 error for use when the wiki is public
 */
function wfPublicError($msg1,$msg2) {
	header( 'HTTP/1.0 403 Forbidden' );
	header( 'Content-Type: text/html; charset=utf-8' );
	wfDebugLog( 'img_auth', "wfPublicError Msg: ".$msg2 );
	echo <<<ENDS
<html>
<body>
<h1>$msg1</h1>
<p>$msg2</p>
</body>
</html>
ENDS;
	wfLogProfilingData();
	exit;
}

