<?php
/**
 * Extension adds "next" and "previous" alphabetic paging links to
 * the top of articles
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionFunctions[] = 'efBackAndForth';
$wgExtensionCredits['other'][] = array(
	'name' => 'Back and Forth',
	'svn-date' => '$LastChangedDate$',
	'svn-revision' => '$LastChangedRevision$',
	'author' => 'Rob Church',
	'description' => 'Adds "Next" and "Previous" alphabetic paging links to the top of articles',
	'descriptionmsg' => 'backforth-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Back-and-Forth',
);

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['BackAndForth'] = $dir . 'BackAndForth.class.php';
$wgExtensionMessagesFiles['BackAndForth'] = $dir . 'BackAndForth.i18n.php';

/**
 * Extension setup function
 */
function efBackAndForth() {
	global $wgHooks;

	wfLoadExtensionMessages( 'BackAndForth' );

	$wgHooks['ArticleViewHeader'][] = 'BackAndForth::viewHook';
}
