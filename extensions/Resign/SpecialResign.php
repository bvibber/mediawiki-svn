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

$wgExtensionCredits['specialpage'][] = array(
	'author' => 'Rotem Liss',
	'version' => '2008-01-11',
	'name' => 'Resign',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Resign',
	'description' => 'Gives users the ability to remove their permissions',
	'descriptionmsg' =>  'resign-desc',
);

# Add resign permission for every group set in the database
foreach( $wgGroupPermissions as $key => $value ) {
	if ( $key != '*' && $key != 'user' && $key != 'autoconfirmed' && $key != 'emailconfirmed' ) {
		$wgGroupPermissions[$key]['resign'] = true;
	}
}

# Add log action
$wgLogActions['rights/resign'] = 'resign-logentry';

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['ResignPage'] = $dir . 'SpecialResign.i18n.php';
$wgAutoloadClasses['ResignPage'] = $dir . 'SpecialResign_body.php';
$wgSpecialPages['Resign'] = 'ResignPage';
