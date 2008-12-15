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
	'name' => 'Ask SQL',
	'version' => '2008-01-31',
	'description' => 'Do SQL queries through a [[Special:Asksql|special page]]',
	'descriptionmsg' => 'asksql-desc',
	'author' => 'Brion Vibber',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Asksql',
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

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Asksql'] = $dir . 'Asksql.i18n.php';
$wgAutoloadClasses['SpecialAsksql'] = $dir . 'Asksql_body.php';
$wgSpecialPages['Asksql'] = 'SpecialAsksql';
