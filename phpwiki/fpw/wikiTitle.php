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
		global $action , $user , $wikiAllowedNamespaces ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage and $action != "edit" ) return false ;
		if ( $this->namespace == "special" ) return false ;

		$r = explode ( "," , trim ( getMySQL ( "cur" , "cur_restrictions" , "cur_title=\"$this->secureTitle\"" ) ) ) ;
		if ( $r[0] == "" ) array_shift ( $r ) ;
		$x = array_intersect ( $r , $user->rights ) ;
		if ( count ( $r ) > 0 and count ( $x ) == 0 ) return false ;

		# Allowing only a handful of namespaces
		if ( !in_array ( str_replace ( "_" , " " , strtolower ( $this->namespace ) ) , $wikiAllowedNamespaces ) ) return false ;

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

	# Can the current user protect this page?
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
		
		# Unescape apostrophes (does this always work?)
		$s = str_replace ( "\\'" , "'" , $s ) ;

		# Strip forbidden characters
		$s = str_replace ( "\\\"" , "" , $s ) ;
		#$s = str_replace ( "\"" , "" , $s ) ;
		# All non-alpha ASCII chars: !"#$%&'()*+,-./:;<=>?@[\]^_`{|}~\127
		# FIXME: Currently following Usemod rules for forbidding chars, except for /, :, and 0x80-0xc0 range for UTF-8
		# Do we want that?
		$s = preg_replace ( "/([^-,.()' _0-9A-Za-z\/:\x80-\xff])/", "", $s);
		
		# Make it compatible with old wiki
		$s = str_replace ( " " , "_" , $s ) ;
		
		# If you use $this->secureTitle in a URL, Satan will eat your soul with a blunt spoon.
		# I'm not kidding. Use $this->url instead or nurlencode() it if you're writing out a URL!
		# secureTitle ONLY belongs in SQL queries and comparisons therewith.
		$this->secureTitle = $s ;

		# IF YOU'RE WRITING A URL, USE $this->url ALWAYS ALWAYS ALWAYS ALWAYS!
		# Why? BECAUSE IT'S URL-ENCODED!!!!!
		$this->url = nurlencode ( $this->secureTitle ) ;
		}

	# Converts the secure title to an even more secure one (URL-style)
	# Dummy function, should not be used anymore.
	function makeURL () {
		makeSecureTitle();
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
		if ( count ( $a ) == 1 ) $this->subpageTitle = "" ;
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
	function makeAll () { $this->makeSecureTitle(); } #$this->makeURL(); }
	function setTitle ( $t ) { $this->title = $t ; $this->makeAll() ; }

	# OUTDATED!!! BUT LEAVE IT!!
	function getMainTitle () {
		return $this->title ;
#		$r = $this->title ;
#		if ( strstr (  $r , ":" ) == false and $this->hasNamespace and $this->namespace != "" ) $r = $this->namespace.":$r" ;
#		if ( $this->subpageTitle != "" ) $r .= "/".$this->subpageTitle ;
#		return $r ;
		}

	# Checks the database if this topic already exists
	function doesTopicExist ( $conn = "" ) {
		$this->makeSecureTitle () ;
		if ( $this->namespace == "special" ) return true ;
		if ( $conn == "" ) $connection = getDBconnection () ;
		else $connection = $conn ;
		$sql = "SELECT cur_id FROM cur WHERE cur_title=\"$this->secureTitle\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result == "" ) return false ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			mysql_free_result ( $result ) ;
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
