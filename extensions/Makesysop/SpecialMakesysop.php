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

$dir = dirname( __FILE__ );

# Internationalisation file
$wgExtensionMessagesFiles['Makesysop'] = "$dir/SpecialMakesysop.i18n.php";

// Set groups to the appropriate sysop/bureaucrat structure:
// * Steward can do 'full' work (makesysop && userrights)
// * Bureaucrat can only do limited work (makesysop)
$wgGroupPermissions['steward'   ]['makesysop' ] = true;
$wgGroupPermissions['steward'   ]['userrights'] = true;
$wgGroupPermissions['bureaucrat']['makesysop' ] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = false;

# Register special page
$wgSpecialPages['Makesysop'] = 'MakesysopPage';
$wgAutoloadClasses['MakesysopPage'] = "$dir/SpecialMakesysop_body.php";
$wgAutoloadClasses['MakesysopForm'] = "$dir/SpecialMakesysop_body.php";
