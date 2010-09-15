<?php
/**
 * Lightweight variable page redirection
 */

//Alert the user that this is not a valid entry point to MediaWiki if they try to access the setup file directly.
if ( !defined( 'MEDIAWIKI' ) ) { 
	echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/VariablePage/VariablePage.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits[ 'VariablePage' ][] = array(
	'path' => __FILE__,
	'name' => 'VariablePage',
	'version' => '0.1',
	'author' => 'Arthur Richards',
	'descriptionmsg' => 'variablepage-desc',
);

/**
 * An array of pages and the probability of a user being redirected to each page.
 * 
 * The key in the array is the full URL path, the value is an integer representing
 * a percentage (0-100) probability of a user being redirected to that page.
 *
 * The percentages here MUST add up to 100 -or- a value must be set for
 * $wgVariablePageDefault
 *
 * The following will redirect a user to http://foo.com/bar 90% of the time:
 *		$wgVariablePagePossibilities = array(
 *			'http://foo.com/bar' => 90,
 *		);
 */
$wgVariablePagePossibilities = array(
	'http://wikimediafoundation.org/wiki/Support_Wikipedia' => 100
);

/**
 * You may set a custom utm_medium to be used for pages reached via VariablePage
 *
 * This can be set to whatever string you wish to use for utm_medium
 */
$wgVariablePageUtmMedium = '';

/**
 * The default URL to send a user to in the event that one of the URLs in 
 * $wgVariablePagePossibilities not selected.
 *
 * Either this must be set or the probabilities in $wgVariablePagePossibiliites
 * must add up to 100.
 */
$wgVariablePageDefault = '';

$dir = dirname( __FILE__ ) . '/';

$wgAutoloadClasses[ 'SpecialVariablePage' ] = $dir . 'VariablePage.body.php';
$wgExtensionMessagesFiles[ 'VariablePage' ] = $dir . 'VariablePage.i18n.php';
$wgSpecialPages[ 'VariablePage' ] = 'SpecialVariablePage';
$wgSpecialPageGroups[ 'VariablePage' ] = 'contribution';
