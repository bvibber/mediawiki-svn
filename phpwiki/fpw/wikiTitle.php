<?
# The wikiTitle class manages the titles of articles. Useful for converting titles into different needed formats.
# Gives its functions and variables to the wikiPage class

class WikiTitle {
	var $title , $secureTitle , $url , $isSpecialPage , $thisVersion ;
	var $namespace , $mainTitle , $subpageTitle , $hasNamespace ;

##### Functions
####### # User rights
	# Can the current user delete this page?
	function canEdit () {
		global $action ;
#		global $oldID ; if ( isset ( $oldID ) ) return false ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage and $action != "edit" ) return false ;
		if ( $this->namespace == "special" ) return false ;

		# Allowing only a handful of namespaces
		$allowed = array ( "wikipedia" , "talk" , "user" , "" ) ;
		if ( !in_array ( strtolower ( $this->namespace ) , $allowed ) ) return false ;

		return true ;
		}

	# Can the current user delete this page?
	function canDelete () {
		global $action , $user ;
		global $oldID ; if ( isset ( $oldID ) ) return false ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		if ( in_array ( "is_sysop" , $user->rights ) ) return true ;
		return false ;
		}

	# Can the current user protect this page? (NOT USED YET)
	function canProtect () {
		global $action , $user ;
		global $oldID ; if ( isset ( $oldID ) ) return false ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		if ( in_array ( "is_sysop" , $user->rights ) ) return true ;
		return false ;
		}

	# Can the current user advance this page? (NOT USED YET)
	function canAdvance () {
		global $action , $user ;
		global $oldID ; if ( isset ( $oldID ) ) return false ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		if ( in_array ( "is_sysop" , $user->rights ) ) return true ;
		return false ;
		}

#### Title functions

	# Generates a "secure" title
	function makeSecureTitle () {
		$this->splitTitle () ;
		$s = ucfirst ( trim ( $this->namespace ) ) ;
		if ( $s != "" ) $s .= ":" ;
		$s .= ucfirst ( trim ( $this->mainTitle ) ) ;
		if ( trim ( $this->subpageTitle ) != "" ) $s .= "/".trim($this->subpageTitle) ;
		$s = str_replace ( "\\\"" , "" , $s ) ;
		$s = str_replace ( "\"" , "" , $s ) ;
		$s = str_replace ( "\\'" , "'" , $s ) ;

		# Make it compatible with old wiki
		$s = str_replace ( " " , "_" , $s ) ;

		$this->secureTitle = $s ;
		}

	# Converts the secure title to an even more secure one (URL-style)
	function makeURL () {		
		$this->url = urlencode ( $this->secureTitle ) ;
		}

	# Converts a secure title back to a nice-looking one
	function getNiceTitle ( $s = "" ) {
		if ( $s == "" ) $s = $this->secureTitle ;
		$s = str_replace ( "_" , " " , $s ) ;
		$s = str_replace ( "\\'" , "'" , $s ) ;
		$s = str_replace ( "\\\\" , "\\" , $s ) ;
		return ucfirst ( $s ) ;
		}

	# Takes apart a title by namespace, subpage...
	function splitTitle () {
		$a = explode ( ":" , $this->title , 2 ) ;
		if ( count ( $a ) == 1 ) {
			$this->namespace = "" ;
			$this->hasNamespace = false ;
			$rest = $a[0] ;
		} else {
			$this->namespace = strtolower ( $a[0] ) ;
			$this->hasNamespace = true ;
			$rest = $a[1] ;
			}
		$a = explode ( "/" , $rest , 2 ) ;
		$this->mainTitle = $a[0] ;
		if ( count ( $a ) == 1 ) $this->$subpageTitle = "" ;
		else $this->subpageTitle = $a[1] ;
		$this->namespace = strtolower ( $this->namespace ) ;
		}

	# This converts an internal link to stay in the same namespace, if desired. Used in wikiPage-getInternalLinks()
	function getLinkTo ( $target ) {
		$keepNamespace = array ( "stable" ) ; # For future use
		$n = "" ;
		if ( in_array ( strtolower ( $this->namespace ) , $keepNamespace ) ) $n = $this->namespace ;
		if ( $target->hasNamespace ) $n = $target->namespace ;
		if ( $n != "" ) $n .= ":" ;
		$p = $target->mainTitle ;
#		if ( $p == "" ) $p = $this->mainTitle ;  # SUBPAGES TURNED OFF
		$ret = $n.$p ;
		if ( $target->subpageTitle != "" ) $ret .= "/".$target->subpageTitle ;
		return $ret ;
		}

	# These are pretty straight-forward
	function makeAll () { $this->makeSecureTitle(); $this->makeURL(); }
	function setTitle ( $t ) { $this->title = $t ; $this->makeAll() ; }

	# OUTDATED!!! BUT LEAVE IT!!
	function getMainTitle () {
		$r = $this->title ;
#		if ( strstr (  $r , ":" ) == false and $this->hasNamespace and $this->namespace != "" ) $r = $this->namespace.":$r" ;
#		if ( $this->subpageTitle != "" ) $r .= "/".$this->subpageTitle ;
		return $r ;
		}

	# Checks the database if this topic already exists
	function doesTopicExist ( $conn = "" ) {
		$this->makeSecureTitle () ;
		if ( $this->namespace == "special" ) return true ;
		if ( $conn == "" ) $connection = getDBconnection () ;
		else $connection = $conn ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT cur_id FROM cur WHERE cur_title=\"$this->secureTitle\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result == "" ) return false ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			mysql_free_result ( $result ) ;
			if ( $conn == "" ) mysql_close ( $connection ) ; # Closing local connection
			return true ;
			}
		return false ;
		}

	# Checks for one namespace and one subpage level max.
	function validateTitle () {
		$this->makeSecureTitle () ;
		if ( $this->mainTitle == "" ) return false ;
		if ( substr_count ( $this->title , "/" ) > 1 ) return false ;
		if ( substr_count ( $this->title , ":" ) > 1 ) return false ;
		return true ;
		}
	}
?>