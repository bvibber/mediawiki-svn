<?PHP

/* 
	fetch phrases to block, and fill $wgSpamRegex with them, rather than scribble that into the variable at startup
*/

global $wgHooks;
/* initialize hook, FilterEdit is too far in code */
$wgHooks['AlternateEdit'][] = 'wfGetSpamRegex';

function wfGetSpamRegex () {
	global $wgMemc, $wgUser, $wgSpamRegex, $wgSharedDB ;
	if (!wfSimplifiedRegexCheckSharedDB())
		return true ;

	$phrases = "" ;
	$first = true ;

	/* first, check if regex string is already stored in memcache */
	$key = "$wgSharedDB:spamRegexCore:spamRegex" ;
	$cached = $wgMemc->get ($key) ;
	if ( !$cached ) {
		/* fetch data from db, concatenate into one string, then fill cache */
		$dbr =& wfGetDB( DB_SLAVE ) ;
		$query = "SELECT spam_text FROM ".wfSpamRegexGetTable() ;
		$res = $dbr->query ($query) ;
		while ( $row = $dbr->fetchObject( $res ) ) {
			$concat = $row->spam_text ;
			if (!$first) {
				$phrases .= "|".$concat ;	
			} else {
				$phrases .= $concat ;
				$first = false ;
			}
		}
		$wgMemc->set ($key, $phrases, 0) ; 
		$dbr->freeResult ($res) ;
	} else {
		/* take from cache */
		$phrases = $cached ;
	}	
	("" != $phrases) ? $wgSpamRegex =  "/".$phrases."/i" : $wgSpamRegex = false ;
	return true ;
}

?>
