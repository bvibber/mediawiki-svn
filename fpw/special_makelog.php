<?
function makeLog ( $logPage , $logText , $logMessage , $doAppend = true ) {
	global $user ;
	$np = new wikiPage ;
	$np->setTitle ( $logPage ) ;
	$np->ensureExistence () ;
	$log = getMySQL ( "cur" , "cur_text" , "cur_title=\"".$np->secureTitle."\"" ) ;
	if ( $doAppend ) {
		$log = $logText.$log ;
	} else { # Not implemented
		}
	$np->setEntry ( $log , $logMessage , $user->id , $user->name , 1 ) ;
	}
?>