<?php 

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "IPAuth.\n" );
	die( 1 );
}

$wgExtensionCredits['other'][] = array( 
	'path' => __FILE__,
	'name' => 'IPAuth', 
	'author' => 'Daniel Kinzler', 
	'url' => 'http://mediawiki.org/wiki/Extension:IPAuth',
	'description' => 'Automatic login from fixed IPs',
);

$wgIPAuthUsers = array(  );
# $wgIPAuthUsers = array( "127.0.0.1" => "LocalUser" );

$wgHooks['UserLoadAfterLoadFromSession'][] = 'ipAuthUserLoadAfterLoadFromSession';

function ipAuthUserLoadAfterLoadFromSession( $user ) {
	global $wgIPAuthUsers;

	if ( $user->isLoggedIn() ) {
		return true;
	}

	$ip = wfGetIP();
	if ( isset( $wgIPAuthUsers[ $ip ] ) ) {
		$name = $wgIPAuthUsers[ $ip ];

		$xuser = User::newFromName( $name );

		if($xuser->getID() == 0) {
			wfDebug( "User $name assigned to IP $ip does not exist!\n" );
		} else {
			#HACK: force user data reload by assigning members directly
			$user->mId = $xuser->mId;
			$user->mName = $xuser->mName;
			$user->loadFromId();

			wfDebug( "User $name assigned to IP $ip logged in.\n" );

			if ( !isset( $_SESSION['wsUserID'] ) ) {
				wfDebug( "Setting up a session for $name assigned to IP $ip logged in.\n" );
				wfSetupSession();
				$_SESSION['wsToken'] = "IP:$ip";
				$_SESSION['wsUserName'] = $name;
				$user->setCookies();
			}
		}
	}
	
	return true;
}

