<?
# The wikiUser class handles all user information

class WikiUser {
    var $id , $name , $password , $retypePassword ;
    var $options , $email ;
    var $rights ;
    var $isLoggedIn ; # Is this user currently logged in?

#### Skin functions

    # Creates the options for the currently selected skin by calling the appropriate function
    function skin () {
        if ( $this->options["skin"] == "" ) $this->skinBlank () ;
        else if ( $this->options["skin"] == "None" ) $this->skinBlank () ;
        else if ( $this->options["skin"] == "Star Trek" ) $this->skinStarTrek () ;
        else if ( $this->options["skin"] == "Nostalgy" ) $this->skinNostalgy () ;
        else if ( $this->options["skin"] == "Cologne Blue" ) $this->skinCologneBlue () ;
        }

    # This sets the options for the standard skin
    function skinBlank () {
        #$this->options["background"] = " BGCOLOR=\"#FFFFFF\"" ;
        $this->options["background"] = "#FFFFFF" ;
        $this->options["text"] = "" ;
        $this->options["forceQuickBar"] = "" ;
        $this->options["quickBarBackground"] = "" ;
        $this->options["textTableBackground"] = "" ;
        $this->options["forceLinks"] = "" ;
        $this->options["leftImage"] = "" ;
        $this->options["borderColor"] = "black" ;
        $this->options["tabLine0"] = " bgcolor=\"#BBBBBB\" " ;
        $this->options["tabLine1"] = "" ;
        $this->options["tabLine2"] = " bgcolor=\"#FFFFCC\"" ;
        }

    # This sets the options for the Cologne Blue skin
    function skinCologneBlue () {
        $this->options["background"] = "#FFFFFF" ;
        $this->options["text"] = "" ;
        $this->options["forceQuickBar"] = "anywhere" ;
        $this->options["quickBarBackground"] = " bgcolor=blue" ;
        $this->options["textTableBackground"] = "" ;
        $this->options["forceLinks"] = "" ;
        $this->options["leftImage"] = "" ;
        $this->options["borderColor"] = "white" ;
        $this->options["tabLine0"] = " bgcolor=\"#BBBBBB\" " ;
        $this->options["tabLine1"] = "" ;
        $this->options["tabLine2"] = " bgcolor=\"#FFFFCC\"" ;
        }

    # This sets the options for the StarTrek skin
    function skinStarTrek () {
        global $wikiStarTrekImage ;
        #$this->options["background"] = " BGCOLOR=\"#000000\" " ;
        #$this->options["text"] = " TEXT=\"#00BB00\" " ;
        $this->options["background"] = "#000000" ;
        $this->options["text"] = "#00BB00" ;
        $this->options["forceQuickBar"] = "left" ;
        $this->options["quickBarBackground"] = " bgcolor=yellow " ;
        $this->options["textTableBackground"] = " bgcolor=\"#444444\"" ;
        $this->options["forceLinks"] = " style=\"color:#0000FF;text-decoration:none\" " ;
        $this->options["leftImage"] = $wikiStarTrekImage ;
        $this->options["borderColor"] = "yellow" ;
        $this->options["tabLine0"] = " bgcolor=\"#550055\" " ;
        $this->options["tabLine1"] = "" ;
        $this->options["tabLine2"] = " bgcolor=\"#333333\"" ;
        }
    
    # This sets the options for the Classic skin
    function skinNostalgy () {
	$this->skinBlank() ;
        $this->options["background"] = "#FFFFFF" ;
        $this->options["text"] = "#000000" ;
        $this->options["forceQuickBar"] = "none" ;
#        $this->options["quickBarBackground"] = " bgcolor=yellow " ;
#        $this->options["textTableBackground"] = " bgcolor=\"#444444\"" ;
#        $this->options["forceLinks"] = " style=\"color:#0000FF;text-decoration:none\" " ;
#        $this->options["leftImage"] = $wikiStarTrekImage ;
#        $this->options["borderColor"] = "yellow" ;
#        $this->options["tabLine0"] = " bgcolor=\"#550055\" " ;
#        $this->options["tabLine1"] = "" ;
#        $this->options["tabLine2"] = " bgcolor=\"#333333\"" ;
        }
    
    # Creates a style sheet for the page from the skin & link style options
    function styleSheet( $action = "view" ) {
        global $namespaceBackground ;
	$cb = "Cologne Blue" ;
	$skin = $this->options[skin] ;
        $ret = "<style type=\"text/css\"><!--\n";
        $ret .= "body { ";
	if ( $skin == $cb ) {
		$ret .= "	background-color: #FFFFFF;\n" ;
		$ret .= "	margin-top: 0px;\n" ;
		$ret .= "	margin-left: 0px;\n" ;
		$ret .= "	margin-right: 0px;\n" ;
		}
        $textcolor = $this->options[text];
        $bgcolor = $this->options[background];
	if ( $this->options["skin"] == "Nostalgy" OR $this->options[skin] == "Cologne Blue" ) $namespaceBackground = "" ;
        if ( $namespaceBackground != "" ) $bgcolor = $namespaceBackground ;
        if ( $textcolor == "" )
            $textcolor = "black"; # For un-coloring links. Should be "inherit" but Netscape 4.x messes it up
        else
            $ret .= "color: $textcolor; ";
        if ( $bgcolor == "" )
            $bgcolor = "white";
        else
            $ret .= "background: $bgcolor; ";
	if ( $this->options[skin] == "Cologne Blue" ) $ret .= "margin: 0; " ;
        $ret .= "}\n";

	if ( $skin == $cb ) {
		$ret .= "
		.header {
			background-color: #7089AA;
		}

		.footnote {
			font-size: 8pt;
			color: #666666;
		}

		p.topmenu {
			margin-bottom: 4px;
			text-transform: uppercase;
			color: #FFFFFF;
			font-family: Verdana, sans-serif;
			font-size: 8pt;
		}

		a.topmenu {
			color: #FFFFFF;
			text-decoration: none;
			font-size: 10pt;
		}

		.tagline {
			color: #000000;
			text-transform: uppercase;
			font-family: Verdana, sans-serif;
			font-size: 11px;
		}

		p, form, dl {
			font-family: Verdana, sans-serif;
			font-size: 10pt;
		}

		h1 {
			font-family: Helvetica, Arial, sans-serif;
			color: #858585;
			font-size: 18pt;
			font-weight: bold;
		}
		span.spacer {
			font-family: sans-serif;
			font-size: 20px;
		}

		input {
			font-family: Verdana, sans-serif;
			font-size: 8pt;
		}

		p.menu {
			font-family: Verdana, sans-serif;
			font-size: 8pt;
			line-height: 13pt;
		}

		span.menuhead {
			font-family: Verdana, sans-serif;
			font-size: 8pt;
			font-weight: bold;
			color: #999999;
		}

		a.menulink {
			font-weight: bold;
			text-decoration: none;
			color: #4B6587;
		}

		a:hover {
			text-decoration: underline;
		}

		.bodytext {
			font-family: Verdana, sans-serif;
			font-size: 10pt;
			text-align: justify;
		}
		a, a.external {
			color: #4B6587
		}

		a.external {
			text-decoration: none;
		}
		a.interwiki { color: #3333BB; text-decoration: none; }
		" ;
		}

        $ret .= "a { text-decoration: " . (($this->options[underlineLinks] == "no") ? "none" : "underline") . "; }\n";
        
        $qbside = ( $this->options["quickBar"] == "left" ) ? "right" : "left";
	if ( $this->options[skin] == "Cologne Blue" ) {
		$qbside = "nope" ; # nope is a dummy, will be ignored
		}
        $ret .= "a.interwiki, a.external { color: #3333BB; text-decoration: none; }\n" .
            "a.red { color: red; text-decoration: none; }\n" .
            "a.green { color: blue; text-decoration: none; }\n" .
	    "a.syslink { color:white; text-decoration:none; }\n" .
	    "a.CBlink { color:#4B6587; text-decoration:none; font-size:11pt; }\n" . # Was:#0000AA
            ".topbar { border-bottom-width: 2; border-bottom-style: ridge; }\n" .
            ".middle { background:white }\n" .
            ".quickbar { background:$bgcolor; border-$qbside-width: 2; border-$qbside-style: ridge; }\n" .
            ".footer { border-top-color: black; border-top-width: 2; border-top-style: groove; }\n";

        if ( $action == "print" ) {
            $ret .= "a { color: inherit; text-decoration: none; font-style: italic; }\n ";
            $ret .= "a.newlink { color: inherit; font-style: inherit; }\n.newlinkedge { display: none; }\n";
        } elseif ( $this->options[markupNewTopics] == "red") {
            $ret .= "a.newlink { color: red; }\n.newlinkedge { display: none; }\n";
        } elseif ( $this->options[markupNewTopics] == "inverse") {
            $ret .= "a.newlink { color: white; background: blue; }\n.newlinkedge { display: inline; }\n";
        } else if ( $skin != $cb ) {
            $ret .= "a.newlink { color: $textcolor; text-decoration: none; }\n.newlinkedge { display: inline; }\n";
            }
        $ret .= "//--></style>";
        return $ret;
        }

#### Management functions

    # This checks the cookies for prior log-ins
    function scanCookies () {
        global $WikiUserID , $WikiUserPassword , $WikiLoggedIn ;
        $this->id = 0 ;
        $this->name = "" ;
        $this->isLoggedIn = false ;
        if ( $WikiUserID > 0 and $WikiLoggedIn == "yes" ) {
            $connection = getDBconnection () ;
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
                }
            }
        $this->loadSettings () ;
        $this->ensureDefaultOptions () ;
        $this->skin () ;
        }

    # This sets the default options for new and no-log-in users
    function ensureDefaultOptions () {
        if ( $this->options["quickBar"] == "" ) $this->options["quickBar"] = "right" ; # For demonstration
        if ( $this->options["markupNewTopics"] == "" ) $this->options["markupNewTopics"] = "normal" ;
        if ( $this->options["underlineLinks"] == "" ) $this->options["underlineLinks"] = "yes" ;
        if ( $this->options["showHover"] == "" ) $this->options["showHover"] = "yes" ;
        if ( $this->options["cols"] == "" ) $this->options["cols"] = "60" ;
        if ( $this->options["rows"] == "" ) $this->options["rows"] = "20" ;
        if ( $this->options["changesLayout"] == "" ) $this->options["changesLayout"] = "classic" ;
        if ( $this->options["hideMinor"] == "" ) $this->options["hideMinor"] = "no" ;
        if ( $this->options["justify"] == "" ) $this->options["justify"] = "no" ;
        if ( $this->options["resultsPerPage"] == "" ) $this->options["resultsPerPage"] = "20" ;
        if ( $this->options["skin"] == "" ) $this->options["skin"] = "None" ;
        if ( $this->options["hourDiff"] == "" ) $this->options["hourDiff"] = "0" ;
        if ( $this->options["encoding"] == "") $this->options["encoding"] = 0;
        if ( $this->options["numberHeadings"] == "" ) $this->options["numberHeadings"] = "no" ;
        if ( $this->options["viewFrames"] == "" ) $this->options["viewFrames"] = "no" ;
        if ( $this->options["viewRecentChanges"] == "" ) $this->options["viewRecentChanges"] = "50" ;

#       if ( $this->options["showStructure"] == "" ) # NO SUBPAGES ANYMORE
        $this->options["showStructure"] = "no" ;
        }

    # Loads the user settings from the database
    function loadSettings () {
        $this->rights = array () ;
        
        # if the user is not logged in, there are no settings        
        if ( !$this->isLoggedIn ) return ;
        
        # get the settings from the database
        $connection = getDBconnection() ;
        $sql = "SELECT user_options, user_rights, user_password, user_email
                FROM user
                WHERE user_id = $this->id" ;
        $result = mysql_query ( $sql , $connection ) ;
        $t = mysql_fetch_object ( $result ) ;
        mysql_free_result ( $result ) ;
        
        # filling the settings variables
        $this->options = array () ;        
        $a = explode ( "\n" , urldecode ( $t->user_options ) ) ;
        foreach ( $a as $x ) {
            $b = explode ( "=" , $x ) ;
            $this->options[$b[0]] = $b[1] ;
        }
        $this->rights = explode ( "," , strtolower ( $t->user_rights ) ) ;
        $this->password = $t->user_password ; 
        $this->email = $t->user_email ;
        $this->skin () ;
    }

    # Saves/updates the user settings in the database
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
        setMySQL ( "user" , "user_options" , nurlencode ( $t ) , "user_id=".$this->id ) ;
        setMySQL ( "user" , "user_password" , $this->password , "user_id=".$this->id ) ;
        setMySQL ( "user" , "user_email" , $this->email , "user_id=".$this->id ) ;
        if ( $this->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $this->password , $expiration ) ;
        }

    # Creates a link to the user home page, or returns the IP
    function getLink () {
        global $wikiUser ;
        if ( $this->isLoggedIn ) {
            $s = new WikiPage ;
            $s = $s->parseContents ( "[[$wikiUser:$this->name|$this->name]]" ) ;
            $s = substr ( strstr ( $s , ">" ) , 1 ) ;
            $s = str_replace ( "</p>" , "" , $s ) ;
            return $s ;
            }

        # These headers can be exploited to falsify an IP. Probably not worth bothering with them,
        # let proxies be proxies.
        #if ( getenv ( HTTP_X_FORWARDED_FOR ) ) $s = getenv ( HTTP_X_FORWARDED_FOR ) ;
        #elseif ( getenv ( HTTP_CLIENT_IP ) ) $s = getenv ( HTTP_CLIENT_IP ) ;
        #else $s = getenv ( REMOTE_ADDR ) ;
        $s = getenv ( REMOTE_ADDR ) ;


#       THIS USED TO CHANGE THE URL OF NON LOGGED-IN USERS TO ".xxx" ;
#       $s = explode ( "." , $s ) ;
#       $s = $s[0].".".$s[1].".".$s[2].".xxx" ;
        return $s ;
        }

    # Checks if a user with that name exists
    function doesUserExist () {
        $s = trim ( $this->name ) ;
        if ( $s == "" ) return false ;
        $connection = getDBconnection () ;
        $sql = "SELECT user_id FROM user WHERE user_name=\"$s\"" ;
        $result = mysql_query ( $sql , $connection ) ;
        if ( $result == "" ) {
            return false ;
            }
        $s = mysql_fetch_object ( $result ) ;
        mysql_free_result ( $result ) ;
        if ( $s == "" ) return false ;
        return true ;
        }

    # Adds a new user to the database
    function addToDatabase () {
        $connection = getDBconnection () ;
        $sql = "INSERT INTO user (user_name,user_password) VALUES (\"$this->name\",\"$this->password\")" ;
        $result = mysql_query ( $sql , $connection ) ;
        }

    # Checks the login
    function verify () {
        global $wikiNoSuchUser , $wikiWrongPassword , $wikiYouAreLoggedIn , $wikiUserError ;
        $this->isLoggedIn = false ;
        if ( !$this->doesUserExist() ) return str_replace ( "$1" , $this->name , $wikiNoSuchUser ) ;
        $connection = getDBconnection () ;
        $sql = "SELECT * FROM user WHERE user_name=\"$this->name\"" ;
        $result = mysql_query ( $sql , $connection ) ;
        if ( $result == "" ) return str_replace ( "$1" , $this->name , $wikiNoSuchUser ) ;
        if ( $s = mysql_fetch_object ( $result ) ) {
            mysql_free_result ( $result ) ;
            if ( $s->user_password == $this->password ) {
                $ret = str_replace ( "$1" , $this->name , $wikiYouAreLoggedIn ) ;
                $this->id = $s->user_id ;
                $this->isLoggedIn = true ;
                $this->loadSettings() ;
            } else {
                $ret = str_replace ( "$1" , $this->name , $wikiWrongPassword ) ;
                }
            }
        else {
            mysql_free_result ( $result ) ;
            $this->contents = str_replace ( "$1" , $this->name , $wikiUserError ) ;
            }
        
        return $ret ;
        }

    # Toggles the watch on an article for this user
    function doWatch ( $t ) {
        $a = getMySQL ( "user" , "user_watch" , "user_id=$this->id" ) ;
        $b = explode ( "\n" , $a ) ;
        return in_array ( $t , $b ) ;
        }
    }
?>
