<?
class WikiTitle {
	var $title , $secureTitle , $url , $isSpecialPage ;
	var $namespace , $mainTitle , $subpageTitle , $hasNamespace ;

	# Functions
	# User rights
	function canEdit () {
		global $action ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage and $action != "edit" ) return false ;
		if ( $this->namespace == "special" ) return false ;
		return true ;
		}
	function canDelete () {
		global $action ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		return true ;
		}
	function canProtect () {
		global $action ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		return true ;
		}
	function canAdvance () {
		global $action ;
		if ( !$this->validateTitle() ) return false ;
		if ( $this->isSpecialPage ) return false ;
		if ( $this->namespace == "special" ) return false ;
		return true ;
		}

	# Title functions
	function makeSecureTitle () {
		$this->splitTitle () ;
		$s = ucfirst ( trim ( $this->namespace ) ) ;
		if ( $s != "" ) $s .= ":" ;
		$s .= ucfirst ( trim ( $this->mainTitle ) ) ;
		if ( trim ( $this->subpageTitle ) != "" ) $s .= "/".trim($this->subpageTitle) ;
		$s = str_replace ( "\"" , "_" , $s ) ;

		# Make it compatible with old wiki
		$s = str_replace ( "'" , "" , $s ) ;
		$s = str_replace ( " " , "_" , $s ) ;

		$this->secureTitle = $s ;
		}
	function makeURL () {		
		$this->url = urlencode ( $this->secureTitle ) ;
		}
	function getNiceTitle ( $s  ) {
		if ( !isset ( $s ) ) $s = $this->secureTitle ;
		$s = str_replace ( "_" , " " , $s ) ;
		return ucfirst ( $s ) ;
		}
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
	function getLinkTo ( $target ) {
		$n = $this->namespace ;
		if ( $target->hasNamespace ) $n = $target->namespace ;
		if ( $n != "" ) $n .= ":" ;
		$p = $target->mainTitle ;
		if ( $p == "" ) $p = $this->mainTitle ;
		$ret = $n.$p ;
		if ( $target->subpageTitle != "" ) $ret .= "/".$target->subpageTitle ;
		return $ret ;
		}
	function makeAll () { $this->makeSecureTitle(); $this->makeURL(); }
	function setTitle ( $t ) { $this->title = $t ; $this->makeAll() ; }
	function doesTopicExist () {
		$this->makeSecureTitle () ;
		if ( $this->namespace == "special" ) return true ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT cur_id FROM cur WHERE cur_title=\"$this->secureTitle\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result == "" ) return false ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			mysql_free_result ( $result ) ;
			mysql_close ( $connection ) ;
			return true ;
			}
		return false ;
		}
	function validateTitle () {
		$this->makeSecureTitle () ;
		if ( $this->mainTitle == "" ) return false ;
		if ( substr_count ( $this->title , "/" ) > 1 ) return false ;
		if ( substr_count ( $this->title , ":" ) > 1 ) return false ;
		return true ;
		}
	}
?>