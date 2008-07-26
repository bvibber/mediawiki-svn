<?php
/**
 * SpamRegex - A special page with the interface for blocking, viewing and unblocking of unwanted phrases
 *
 * @ingroup Extensions
 * @author Bartek Łapiński
 * @version 1.0
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if (!defined('MEDIAWIKI'))
	die();

/* for memcached - expiration time */
define('SPAMREGEX_EXPIRE', 0);

/* two modes for two kinds of blocks */
define('SPAMREGEX_TEXTBOX', 0);
define('SPAMREGEX_SUMMARY', 1);

/* return the name of the table  */
function wfSpamRegexGetTable() {
	global $wgSharedDB;
	if ("" != $wgSharedDB) {
		return "{$wgSharedDB}.spam_regex";
	} else {
		return "spam_regex";
	}
}

/* return the proper db key for Memc */
function wfSpamRegexGetMemcDB() {
	global $wgSharedDB, $wgDBname;
	if (!empty( $wgSharedDB ) ) {
		return $wgSharedDB;
	} else {
		return $wgDBname;
	}
}

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['SpamRegex'] = $dir . 'SpamRegex.i18n.php';
$wgExtensionAliasesFiles['SpamRegex'] = $dir . 'SpamRegex.alias.php';
$wgAutoloadClasses['SpamRegex'] = $dir . 'SpecialSpamRegex.php';
$wgSpecialPages['SpamRegex'] = 'SpamRegex';

//New user right
$wgAvailableRights[] = 'spamregex';
$wgGroupPermissions['staff']['spamregex'] = true;

//Extension credits
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Regular Expression Spam Block',
	'version' => '1.0',
	'author' => 'Bartek Łapiński',
	'url' => 'http://www.mediawiki.org/wiki/Extension:SpamRegex',
	'description' => 'Filters out unwanted phrases in edited pages, based on regular expressions',
	'descriptionmsg' => 'spamregex-desc',
);

require_once("$IP/extensions/SpamRegex/SpamRegexCore.php");
require_once("$IP/extensions/SimplifiedRegex/SimplifiedRegex.php");