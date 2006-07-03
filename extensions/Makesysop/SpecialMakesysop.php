<?php
/**
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/**
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "MakeSysop extension\n";
	exit( 1 ) ;
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Makesysop',
	'description' => 'gives bureaucrats/stewards the ability to modify user levels'
);

# Internationlization
require_once( 'SpecialMakesysop.i18n.php' );

// Set groups to the appropriate sysop/bureaucrat structure:
// * Steward can do 'full' work (makesysop && userrights)
// * Bureaucrat can only do limited work (makesysop)
$wgGroupPermissions['steward'   ]['makesysop' ] = true;
$wgGroupPermissions['steward'   ]['userrights'] = true;
$wgGroupPermissions['bureaucrat']['makesysop' ] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = false;

$wgAvailableRights[] = 'makesysop';

/**
 * Quick hack for clusters with multiple master servers; if an alternate
 * is listed for the requested database, a connection to it will be opened
 * instead of to the current wiki's regular master server.
 *
 * Requires that the other server be accessible by network, with the same
 * username/password as the primary.
 *
 * eg $wgAlternateMaster['enwiki'] = 'ariel';
 */
$wgAlternateMaster = array();

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialMakesysop_body.php', 'Makesysop', 'MakeSysopPage' );

?>
