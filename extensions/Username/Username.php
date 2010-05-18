<?php
 
 
$wgHooks['ParserFirstCallInit'][] = 'efUsernameParserInit';
 
function efUsernameParserInit( &$parser ) {
	$parser->setHook( 'username', 'efUsernameRender' );
	return true;
}
 
function efUsernameRender( $input, $args, $parser, $frame ) {
	// Nothing exciting here, just escape the user-provided
	// input and throw it back out again
	//return htmlspecialchars( $input );
	global $wgUser;
	if (!$wgUser->isLoggedIn())
	{
		return ($input==null)?"너":$input;
	}
	else return $wgUser->getName();
}
