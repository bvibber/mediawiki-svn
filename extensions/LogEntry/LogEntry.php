<?php

# Check environment
if( !defined( 'MEDIAWIKI' ) ) {
    echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
    die( -1 );
}

# Shrtcut to this dirctory
$dir = dirname( __FILE__ ) . '/';

# Credits
$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'LogEntry',
	'author'         => 'Trevor Parscal', 
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:LogEntry', 
	'description'    => 'This tag extension provides a form for appending to log pages',
	'descriptionmsg' => 'logentry-parserhook-desc',
);
$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'LogEntry',
	'author'         => 'Trevor Parscal', 
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:LogEntry', 
	'description'    => 'This tag extension provides processing for appending to log pages',
	'descriptionmsg' => 'logentry-specialpage-desc',
);

# Internationalization
$wgExtensionMessagesFiles['LogEntry'] = $dir . 'LogEntry.i18n.php';
$wgExtensionAliasesFiles['LogEntry'] = $dir . 'LogEntry.alias.php';

# Body
require_once $dir . 'LogEntry.body.php';
$wgAutoloadClasses['LogEntry'] = $dir . 'LogEntry.body.php';

# Let MediaWiki know about your new special page.
$wgSpecialPages['LogEntry'] = 'LogEntry';