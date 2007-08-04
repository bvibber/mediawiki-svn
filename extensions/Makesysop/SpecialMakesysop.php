<?php
/**
 * @addtogroup SpecialPage
 */

/**
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "MakeSysop extension\n";
	exit( 1 ) ;
}

$wgExtensionCredits['specialpage'][] = array(
	'author' => 'Tim Starling',
	'name' => 'Makesysop',
	'description' => 'Gives bureaucrats/stewards the ability to modify user levels',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Makesysop'
);

# Internationalisation file
require_once( dirname(__FILE__) . '/SpecialMakesysop.i18n.php' );

// Set groups to the appropriate sysop/bureaucrat structure:
// * Steward can do 'full' work (makesysop && userrights)
// * Bureaucrat can only do limited work (makesysop)
$wgGroupPermissions['steward'   ]['makesysop' ] = true;
$wgGroupPermissions['steward'   ]['userrights'] = true;
$wgGroupPermissions['bureaucrat']['makesysop' ] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = false;

$wgAvailableRights[] = 'makesysop';

# Register special page
if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialMakesysop_body.php', 'Makesysop', 'MakeSysopPage' );


