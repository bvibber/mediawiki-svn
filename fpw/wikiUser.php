<?
class WikiUser {
	var $id , $name , $password , $retypePassword ;
	var $isLoggedIn ;
	var $options , $email ;
	var $rights ;

	function skin () {
		if ( $this->options["skin"] == "" ) $this->skinBlank () ;
		else if ( $this->options["skin"] == "None" ) $this->skinBlank () ;
		else if ( $this->options["skin"] == "Star Trek" ) $this->skinStarTrek () ;
		}
	function skinBlank () {
		$this->options["background"] = "" ;
		$this->options["text"] = "" ;
		$this->options["forceQuickBar"] = "" ;
		$this->options["quickBarBackground"] = "" ;
		$this->options["textTableBackground"] = "" ;
		$this->options["forceLinks"] = "" ;
		$this->options["leftImage"] = "" ;
		$this->options["borderColor"] = "black" ;
		$this->options["tabLine0"] = " bgcolor=#BBBBBB " ;
		$this->options["tabLine1"] = "" ;
		$this->options["tabLine2"] = " bgcolor=#FFFFCC" ;
		}
	function skinStarTrek () {
		$this->options["background"] = " BGCOLOR=#000000 " ;
		$this->options["text"] = " TEXT=#00BB00 " ;
		$this->options["forceQuickBar"] = "left" ;
		$this->options["quickBarBackground"] = " bgcolor=yellow " ;
		$this->options["textTableBackground"] = " bgcolor=#444444" ;
		$this->options["forceLinks"] = " style=\"color:#0000FF;text-decoration:none\" " ;
		$this->options["leftImage"] = "startrek.png" ;
		$this->options["borderColor"] = "yellow" ;
		$this->options["tabLine0"] = " bgcolor=#550055 " ;
		$this->options["tabLine1"] = "" ;
		$this->options["tabLine2"] = " bgcolor=#333333" ;
		}
	function scanCookies () {
		global $WikiUserID , $WikiUserPassword , $WikiLoggedIn ;
		$this->id = 0 ;
		$this->name = "" ;
		$this->isLoggedIn = false ;
		if ( $WikiUserID > 0 and $WikiLoggedIn == "yes" ) {
			$connection = getDBconnection () ;
			mysql_select_db ( "wikipedia" , $connection ) ;
			$sql = "SELECT * FROM user WHERE user_id=$WikiUserID" ;
			$result = mysql_query ( $sql , $connection ) ;
			if ( $result ) {
				$s = mysql_fetch_object ( $result ) ;
				$this->name = $s->user_name ;
				if ( $WikiUserPassword == $s->user_password ) {
					$this->password = $s->user_password ;
					$this->id = $s->user_id ;
					$this->isLoggedIn = true ;
					}
				mysql_free_result ( $result ) ;
				mysql_close ( $connection ) ;
				}
			}
		$this->loadSettings () ;
		$this->ensureDefaultOptions () ;
		$this->skin () ;
		}
	function ensureDefaultOptions () {
		if ( $this->options["quickBar"] == "" ) $this->options["quickBar"] = "none" ;
		if ( $this->options["markupNewTopics"] == "" ) $this->options["markupNewTopics"] = "normal" ;
		if ( $this->options["underlineLinks"] == "" ) $this->options["underlineLinks"] = "yes" ;
		if ( $this->options["showHover"] == "" ) $this->options["showHover"] = "yes" ;
		if ( $this->options["autoTalk"] == "" ) $this->options["autoTalk"] = "no" ;
		if ( $this->options["cols"] == "" ) $this->options["cols"] = "60" ;
		if ( $this->options["rows"] == "" ) $this->options["rows"] = "20" ;
		if ( $this->options["changesLayout"] == "" ) $this->options["changesLayout"] = "classic" ;
		if ( $this->options["justify"] == "" ) $this->options["justify"] = "no" ;
		if ( $this->options["resultsPerPage"] == "" ) $this->options["resultsPerPage"] = "20" ;
		if ( $this->options["skin"] == "" ) $this->options["skin"] = "None" ;
		}
	function loadSettings () {
		if ( !$this->isLoggedIn ) return ;
		$t = getMySQL ( "user" , "user_options" , "user_id=".$this->id ) ;
		$t = urldecode ( $t ) ;
		$a = explode ( "\n" , $t ) ;
		$this->options = array () ;
		foreach ( $a as $x ) {
			$b = explode ( "=" , $x ) ;
			$this->options[$b[0]] = $b[1] ;
			}
		$t = getMySQL ( "user" , "user_rights" , "user_id=".$this->id ) ;
		$this->rights = explode ( "," , strtolower ( $t ) ) ;
		$this->password = getMySQL ( "user" , "user_password" , "user_id=".$this->id ) ;
		$this->email = getMySQL ( "user" , "user_email" , "user_id=".$this->id ) ;
		$this->skin () ;
		}
	function saveSettings () {
		global $expiration ;
		if ( !$this->isLoggedIn ) return ;
		$t = "" ;
		$a = array_keys ( $this->options ) ;
		foreach ( $a as $x ) {
			if ( $x != "" ) {
				if ( $t != "" ) $t .= "\n" ;
				$t .= $x."=".$this->options[$x] ;
				}
			}
		setMySQL ( "user" , "user_options" , urlencode ( $t ) , "user_id=".$this->id ) ;
		setMySQL ( "user" , "user_password" , $this->password , "user_id=".$this->id ) ;
		setMySQL ( "user" , "user_email" , $this->email , "user_id=".$this->id ) ;
		if ( $this->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $this->password , $expiration ) ;
		}
	function getLink () {
		global $REMOTE_ADDR ;
		if ( $this->isLoggedIn ) {
			$s = new WikiPage ;
#			$s->setTitle ( "user:$this->name" ) ;
			$s = $s->parseContents ( "[[user:$this->name|$this->name]]" ) ;
			$s = substr ( strstr ( $s , ">" ) , 1 ) ;
			$s = str_replace ( "</p>" , "" , $s ) ;
			return $s ;
			}
		$s = $REMOTE_ADDR ;
		$s = explode ( "." , $s ) ;
		$s = $s[0].".".$s[1].".".$s[2].".xxx" ;
		return $s ;
		}
	function doesUserExist () {
		$s = trim ( $this->name ) ;
		if ( $s == "" ) return false ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT user_id FROM user WHERE user_name=\"$s\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result == "" ) {
			mysql_close ( $connection ) ;
			return false ;
			}
		$s = mysql_fetch_object ( $result ) ;
		mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
		if ( $s == "" ) return false ;
		return true ;
		}
	function addToDatabase () {
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "INSERT INTO user (user_name,user_password) VALUES (\"$this->name\",\"$this->password\")" ;
		$result = mysql_query ( $sql , $connection ) ;
		mysql_close ( $connection ) ;		
		}
	function verify () {
		$this->isLoggedIn = false ;
		if ( !$this->doesUserExist() ) return "<font color=red>Unknown user \"$this->name\"!</font>" ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT * FROM user WHERE user_name=\"$this->name\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result == "" ) return "<font color=red>No such user \"$this->name\".</font>" ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			mysql_free_result ( $result ) ;
			mysql_close ( $connection ) ;
			if ( $s->user_password == $this->password ) {
				$ret = "$this->name, you are logged in!" ; 
				$this->id = $s->user_id ;
				$this->isLoggedIn = true ;
				$this->loadSettings() ;
			} else {
				$ret = "<font color=red>Wrong password for user $this->name!</font>" ;
				}
			}
		else {
			mysql_free_result ( $result ) ;
			mysql_close ( $connection ) ;
			$this->contents = "Error with \"".$this->name."\"" ;
			}
		
		return $ret ;
		}
	function doWatch ( $t ) {
		$a = getMySQL ( "user" , "user_watch" , "user_id=$this->id" ) ;
		$b = explode ( "'" , $a ) ;
		return in_array ( $t , $b ) ;
		}
	}
?>
