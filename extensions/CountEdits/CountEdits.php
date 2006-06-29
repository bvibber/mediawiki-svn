<?php

/**
 * Simple edit counter special page for small wikis
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 * @copyright Copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['other'][] = array( 'name' => 'Count Edits', 'author' => 'Rob Church' );
$wgCountEditsMostActive = true;

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/CountEdits_body.php', 'CountEdits', 'CountEdits' );

?>
