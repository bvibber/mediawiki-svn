<?php

/**#@+
*      Extension used for blocking users names and IP addresses with regular expressions. Contains both the blocking mechanism and a special page to add/manage blocks
*
* @addtogroup SpecialPage
*
* @author Bartek
* @copyright Copyright © 2007, Wikia Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
*/


/* generic reasons */

    global $wgContactLink;
if($wgContactLink == ''){
  $wgContactLink = '[[Special:Contact|contact Wikia]]';
 }

/* these users may be innocent - we do want them to contact Wikia if they are */
define ('REGEXBLOCK_REASON_IP', "This IP address is prevented from editing due to vandalism or other disruption by you or by someone who shares your IP address. If you believe this is in error, please $wgContactLink") ;

/* we do not really want these users to contact Wikia about the problem - those are vandals */
define ('REGEXBLOCK_REASON_NAME', "This username is prevented from editing due to vandalism or other disruption. If you believe this is in error, please $wgContactLink") ;
define ('REGEXBLOCK_REASON_REGEX', "This username is prevented from editing due to vandalism or other disruption by a user with a similar name. Please create an alternate user name or $wgContactLink about the problem.") ;

define ('REGEXBLOCK_PATH', '/') ;


/* help displayed on the special page  */
define ('REGEXBLOCK_HELP', "Use the form below to block write access from a specific IP address or username. This should be done only only to prevent vandalism, and in accordance with policy. <i>This page will allow you to block even non-existing users, and will also block users with names similar to given, i.e. 'Test' will be blocked along with 'Test 2' etc. You can also block full IP addresses, meaning that no one logging from them will be able to edit pages. Note: partial IP addresses will be treated by usernames in determining blocking.  If no reason is specified, a default generic reason will be used.</i>") ;

/* get name of the table  */
function wfRegexBlockGetTable () {
	global $wgSharedDB ;
	if ("" != $wgSharedDB) {
		return "{$wgSharedDB}.blockedby" ;
	} else {
		return 'blockedby' ;
	}
}

/* get the name of the stats table */
function wfRegexBlockGetStatsTable () {
	global $wgSharedDB ;
	if ("" != $wgSharedDB) {
		return "{$wgSharedDB}.stats_blockedby" ;
	} else {
		return 'stats_blockedby' ;
	}
}

/* memcached expiration time (0 - infinite) */
define ('REGEXBLOCK_EXPIRE', 0) ;
/* modes for fetching data during blocking */
define ('REGEXBLOCK_MODE_NAMES',0) ;
define ('REGEXBLOCK_MODE_IPS',1) ;
/* for future use */
define ('REGEXBLOCK_USE_STATS', 1) ;

/* core includes */
require_once ($IP.REGEXBLOCK_PATH."extensions/regexBlock/regexBlockCore.php") ;
require_once ($IP.REGEXBLOCK_PATH."extensions/regexBlock/SpecialRegexBlock.php") ;
require_once ($IP.REGEXBLOCK_PATH."extensions/regexBlock/SpecialRegexBlockStats.php") ;

/* simplified regexes, this is shared with SpamRegex */
require_once ($IP.REGEXBLOCK_PATH."extensions/SimplifiedRegex/SimplifiedRegex.php") ;


