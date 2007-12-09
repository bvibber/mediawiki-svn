<?php
/* $Id$ */

/**
 * If enabled through $wgAllowSysopQueries = true, this class
 * let users with sysop right the possibility to make sql queries
 * against the cur table.
 * Heavy queries could slow down the database specially for the
 * biggest wikis.
 *
 * @addtogroup SpecialPage
 */

if (!defined('MEDIAWIKI'))
	exit;

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Asksql',
	'version' => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Asksql',
	'author' => 'Brion Vibber',
	'description' => 'Allow users with sysop right the possibility to make sql queries',
);

# Sysop SQL queries
#   The sql user shouldn't have too many rights other the database, restrict
#   it to SELECT only on 'page', 'revision' and 'text' tables for example
#
/** Dangerous if not configured properly. */
$wgAllowSysopQueries = true;
#$wgDBsqluser = 'sqluser';
#$wgDBsqlpassword = 'sqlpass';
$wgSqlLogFile = "{$wgUploadDirectory}/sqllog_mFhyRe6";

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/Asksql_body.php', 'Asksql', 'SpecialAsksql' );


