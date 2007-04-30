<?php

if(!defined('MEDIAWIKI'))
   die();

/* is regex valid? */
function wfValidRegex ($text) {
	try {
		$test = @preg_match("/{$text}/", 'Whatever') ;
		if (!is_int($test)) {
			throw new Exception ("error!") ;
		}
	} catch (Exception $e) {
		return false ;
	}

        return true ;
}

/* temporary */
function wfSimplifiedRegex ($text) {
	return preg_quote ($text) ;
}

function wfSimplifiedRegexCheckSharedDB() {
	global $wgSharedDB ;
	if (isset($wgSharedDB)) { /* if no shared database, don't load extension at all */
		return true ;
	}
	return false ;
}

?>
