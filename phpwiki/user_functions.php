<?
####################################################################### USER FUNCTIONS

function getCurrentUserName () {
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN ;
	global $REMOTE_ADDR ;
	if ( $USERLOGGEDIN == "YES" ) return $USERNAME ;
	else return $REMOTE_ADDR ;
	}

function doesUserExist ( $un ) {
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM user WHERE user_name=\"$un\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $s = mysql_fetch_object ( $result ) ) $ret = true ;
	else $ret = false ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function getUserSetting ( $un , $s ) {
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM user WHERE user_name=\"$un\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$t = mysql_fetch_object ( $result ) ;
	$ret = $t->$s ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function changeUserSetting ( $un , $s , $v ) {
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "UPDATE user SET $s = \"$v\" WHERE user_name = \"$un\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	mysql_close ( $connection ) ;
	}

function checkUserPassword ( $un , $up ) {
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM user WHERE user_name=\"$un\" AND user_password=\"$up\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $s = mysql_fetch_object ( $result ) ) {
		setcookie ( "USERID" , "$s->user_id" ) ;
		$ret = true ;
		}
	else $ret = false ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function addNewUser ( $un , $up , $ur ) {
	if ( doesUserExist ( $un ) ) return ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "INSERT INTO user (user_name, user_password, user_rights) VALUES (\"$un\", \"$up\", \"$ur\")" ;
	$result = mysql_query ( $sql , $connection ) ;

	$sql = "SELECT * FROM user WHERE user_name=\"$un\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	setcookie ( "USERNAME" , "$s->user_name" ) ;
	setcookie ( "USERPASSWORD" , "$s->user_password" ) ;
	setcookie ( "USERID" , "$s->user_id" ) ;
	setcookie ( "USERLOGGEDIN" , "YES" ) ;
	mysql_free_result ( $result ) ;

	mysql_close ( $connection ) ;
	}

?>