<?
####################################################################### ARTICLE DATABASE INTERFACE

function acquireTopic ( $s ) {
	global $title ;
	$s=getSecureTitle($s);
#	$s=strtolower($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM cur WHERE cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $s = mysql_fetch_object ( $result ) ) {
		$title=$s->cur_title ;
		$s = $s->cur_text ;
		}
	else {
		$s = "" ;
		}
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $s ;
	}

function acquireOldTopic ( $s , $id ) {
	global $title ;
	$s=getSecureTitle($s);
#	$s=strtolower($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "select * from old where old_title=\"$title\" and old_id=$id" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $s = mysql_fetch_object ( $result ) ) {
		$title=$s->old_title ;
		$s = $s->old_text ;
		}
	else {
		$s = "nothing available" ;
		}
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $s ;
	}

function saveTopic ( $txt , $com , $min ) {
	global $title ;
	global $USERLOGGEDIN , $USERID ;
	$s=getSecureTitle($title);
#	$s=strtolower($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$txt = str_replace ( "\r" , "" , $txt ) ;

	$sql = "update cur set cur_text=\"$txt\" where cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;

	$sql = "update cur set cur_comment='$com' where cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;

	$sql = "update cur set cur_minor_edit=1 where cur_title=\"$s\"" ;
	if ( $min == "on" ) $result = mysql_query ( $sql , $connection ) ;

	$id = $USERID ;
	if ( $id == "" or $USERLOGGEDIN != "YES" ) $id = "0" ;
	$sql = "update cur set cur_user='$id' where cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;

	$un = getCurrentUserName () ;
	$sql = "update cur set cur_user_text='$un' where cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;

	mysql_close ( $connection ) ;
	}

function addPlainTopic ( $t ) {
	global $title ;
	$s=getSecureTitle($title);
#	$s=strtolower($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;

	$sql = "insert into cur ( cur_title, cur_text ) VALUES ( \"$s\" , \"\" )" ;
	$result = mysql_query ( $sql , $connection ) ;

	mysql_close ( $connection ) ;
	}

function backupTopic ( $t ) {
	global $title ;
	$s=getSecureTitle($title);
#	$s=strtolower($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;

	# Reading current version
	$sql = "select * from cur where cur_title=\"$t\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;

	$o_title = $s->cur_title ;
	$o_text = $s->cur_text ;
	$o_comment = $s->cur_comment ;
	$o_user = $s->cur_user ;
	$o_user_text = $s->cur_user_text ;
	$o_old_version = $s->cur_old_version ;
	$o_timestamp = $s->cur_timestamp ;
	$o_minor_edit = $s->cur_minor_edit ;

	$o_text = str_replace ( '"' , '\"' , $o_text ) ;

	mysql_free_result ( $result ) ;

	# Adding data to "old" table
	$sql = "insert into old ( old_title, old_text , old_comment , old_user, old_user_text , old_old_version , old_timestamp , old_minor_edit ) VALUES ( \"$o_title\" , \"$o_text\" , \"$o_comment\" , \"$o_user\" , \"$o_user_text\" , \"$o_old_version\" , \"$o_timestamp\" , \"$o_minor_edit\" )" ;
	$result = mysql_query ( $sql , $connection ) ;

	# Get old id
	$sql = "select * from old where old_title=\"$o_title\" and old_old_version=\"$o_old_version\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$n_old_version = $s->old_id ;
	mysql_free_result ( $result ) ;

	# Update current version
	$sql = "update cur set cur_old_version='$n_old_version' where cur_title=\"$title\"" ;
	$result = mysql_query ( $sql , $connection ) ;	

	mysql_close ( $connection ) ;
	}

function doesTopicExist ( $s ) {
	if ( $s == "" ) return false ;
	$s=getSecureTitle($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur WHERE cur_title=\"$s\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $result ) {
		$s = mysql_fetch_object ( $result ) ;
		if ( $s->number > 0 ) $ret = true ;
		else $ret = false ;
		mysql_free_result ( $result ) ;
	} else $ret = false ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function doesNamespaceExist ( $ns ) {
	$ns = getSecureTitle ( $ns ) ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur WHERE cur_title LIKE \"$ns:%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	if ( $s->number > 0 ) $ret = true ;
	else $ret = false ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function getTopicSetting ( $tt , $s ) {
	$tt = getSecureTitle ( $tt ) ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM cur WHERE cur_title=\"$tt\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $t = mysql_fetch_object ( $result ) ) $ret = $t->$s ;
	else $ret = "NOSUCHTHING" ; # This topic or property doesn't exist
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function changeTopicSetting ( $tt , $s , $v ) {
	$secureTitle = getSecureTitle ( $tt ) ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "UPDATE cur SET $s = \"$v\" WHERE cur_title = \"$secureTitle\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	mysql_close ( $connection ) ;
	}

function getAllNamespaces ( $tt ) {
	$ret = "" ;
	$cnt = 0 ;
	if ( doesTopicExist($tt) ) {
		$ret = "<a href=\"$PHP_SELF?action=view&title=$tt\">[".getNiceTitle($tt)."]</a>" ;
		$cnt++ ;
		}
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT cur_title FROM cur WHERE cur_title LIKE \"%:$tt\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		if ( $ret != "" ) $ret .= "  " ;
		$ret .= "<a href=\"$PHP_SELF?action=view&title=$s->cur_title\">[".getNiceTitle($s->cur_title)."]</a>" ;
		$cnt++ ;
		}
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	if ( $cnt < 2 ) $ret = "" ;
	return $ret ;	
	}
?>