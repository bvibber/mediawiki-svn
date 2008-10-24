<?php
/**
 * LogEntry extension
 *
 * @file
 * @ingroup Extensions
 */

// Check environment
if( !defined( 'MEDIAWIKI' ) ) {
    echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
    die( -1 );
}

/* Configuration */

// Credits
$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'LogEntry',
	'author'         => 'Trevor Parscal', 
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:LogEntry', 
	'description'    => 'This tag extension provides a form for appending to log pages',
	'descriptionmsg' => 'logentry-parserhook-desc',
);

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// Internationalization
$wgExtensionMessagesFiles['LogEntry'] = $dir . 'LogEntry.i18n.php';
$wgExtensionAliasesFiles['LogEntry'] = $dir . 'LogEntry.alias.php';

// Register auto load for the special page class
$wgAutoloadClasses['LogEntryHooks'] = $dir . 'LogEntry.hooks.php';
$wgAutoloadClasses['LogEntry'] = $dir . 'LogEntry.page.php';

// Register parser hook
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	// Modern
    $wgHooks['ParserFirstCallInit'][] = 'LogEntryHooks::register';
} else {
	// Legacy
    $wgExtensionFunctions[] = 'LogEntryHooks::register';
}

// Register the LogEntry special page
$wgSpecialPages['LogEntry'] = 'LogEntry';
