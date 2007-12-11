<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to allow users to remove their permissions.
 * Should be included as the latest extension that sets user groups.
 *
 * @addtogroup Extensions
 *
 * @author Rotem Liss
 */

$wgExtensionFunctions[] = 'wfSpecialResign';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Resign',
	'version' => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Resign',
	'author' => 'Rotem Liss',
	'description' => 'Gives users the ability to remove their permissions',
);

# Add resign permission for every group set in the database
foreach( $wgGroupPermissions as $key => $value ) {
	if ( $key != '*' && $key != 'user' && $key != 'autoconfirmed' && $key != 'emailconfirmed' ) {
		$wgGroupPermissions[$key]['resign'] = true;
	}
}

# Add log action
$wgLogActions['rights/resign'] = 'resign-logentry';

$wgAutoloadClasses['ResignPage'] = dirname( __FILE__ ) . '/SpecialResign_body.php';
$wgSpecialPages['Resign'] = 'ResignPage';

function wfSpecialResign() {
	global $wgMessageCache;
	require_once( dirname(__FILE__) . '/SpecialResign.i18n.php' );
	$wgMessageCache->addMessagesByLang( efResignMessages() );
}
