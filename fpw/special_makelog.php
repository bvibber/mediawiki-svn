<?
function makeLog ( $logPage , $logText , $logMessage , $showOnRecentChanges = true ) {
	global $user ;
	$np = new wikiPage ;
	$np->setTitle ( $logPage ) ;
	$np->ensureExistence () ;
	$log = getMySQL ( "cur" , "cur_text" , "cur_title=\"".$np->secureTitle."\"" ) ;
	$log = $logText.$log ;
	if ( $showOnRecentChanges ) {
		$np->setEntry ( $log , $logMessage , $user->id , $user->name , 1 ) ;
	} else {
		$np->setEntry ( $log , $logMessage , $user->id , $user->name , 1 , ",cur_timestamp=cur_timestamp " ) ;
		}
	}
?>