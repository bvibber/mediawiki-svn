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
	'author' => 'Rotem Liss',
	'name' => 'Resign',
	'description' => 'Gives users the ability to remove their permissions'
);

# Internationalisation file
require_once( dirname(__FILE__) . '/SpecialResign.i18n.php' );

# Add resign permission for every group set in the database
foreach( $wgGroupPermissions as $key => $value ) {
	if ( $key != '*' && $key != 'user' && $key != 'autoconfirmed' && $key != 'emailconfirmed' ) {
		$wgGroupPermissions[$key]['resign'] = true;
	}
}

# Add log action
$wgLogActions['rights/resign'] = 'resign-logentry';

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialResign_body.php', 'Resign', 'ResignPage' );

function wfSpecialResign() {
	# Add messages
	global $wgMessageCache, $wgResignMessages;
	$wgMessageCache->addMessagesByLang( $wgResignMessages );
}

?>
