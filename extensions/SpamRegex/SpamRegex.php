<?php
/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if (!defined('MEDIAWIKI')) die();


/* help displayed on the special page  */
define ('SPAMREGEX_HELP', 'Use this form to effectively block expressions from saving into a page\'s text. If the text contains the given expression, change would not be saved and an explanation will be displayed to user that tried to save the page. Caution advised, expressions should not be too short or too common.') ;

define ('SPAMREGEX_PATH', '/') ;

/* for memcached - expiration time */
define ('SPAMREGEX_EXPIRE', 0) ;

/* return the name of the table  */
function wfSpamRegexGetTable() {
        global $wgSharedDB ;
        if ("" != $wgSharedDB) {
                return "{$wgSharedDB}.spam_regex" ;
        } else {
                return "spam_regex" ;
        }
}
	require_once ($IP.SPAMREGEX_PATH."extensions/SpamRegex/SpecialSpamRegex.php") ;
	//will need more, maybe Core?
	require_once ($IP.SPAMREGEX_PATH."extensions/SpamRegex/SpamRegexCore.php") ;
	require_once ($IP.SPAMREGEX_PATH."extensions/SimplifiedRegex.php") ;
?>
