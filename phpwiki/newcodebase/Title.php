<?
# See title.doc

/* private */ $wgValidInterwikis = array(
	"w"		=> "http://www.wikipedia.com/wiki/$1",
	"m"		=> "http://meta.wikipedia.com/wiki.phtml?title=$1",
	"ar"	=> "http://ar.wikipedia.com/wiki.cgi?$1",
	"ca"	=> "http://ca.wikipedia.com/wiki.cgi?$1",
	"zh"	=> "http://zh.wikipedia.com/wiki.cgi?$1",
	"dk"	=> "http://dk.wikipedia.com/wiki.cgi?$1",
	"nl"	=> "http://nl.wikipedia.com/wiki.cgi?$1",
	"de"	=> "http://de.wikipedia.com/wiki.cgi?$1",
	"eo"	=> "http://eo.wikipedia.com/wiki/$1",
	"fr"	=> "http://fr.wikipedia.com/wiki.cgi?$1",
	"he"	=> "http://he.wikipedia.com/wiki.cgi?$1",
	"hu"	=> "http://hu.wikipedia.com/wiki.cgi?$1",
	"it"	=> "http://it.wikipedia.com/wiki.cgi?$1",
	"ja"	=> "http://ja.wikipedia.com/wiki/$1",
	"pl"	=> "http://pl.wikipedia.com/wiki.cgi?$1",
	"pt"	=> "http://pt.wikipedia.com/wiki.cgi?$1",
	"ru"	=> "http://ru.wikipedia.com/wiki.cgi?$1",
	"simple"=> "http://simple.wikipedia.com/wiki.cgi?$1",
	"es"	=> "http://es.wikipedia.com/wiki.cgi?$1",
	"sv"	=> "http://sv.wikipedia.com/wiki.cgi?$1",
	"en"	=> "http://www.wikipedia.com/wiki/$1"
);

# This array is kept globally for existence-checking
# internal links and such
#
$wgArticleIDcache = array();

class Title {
	/* private */ var $mTextform, $mUrlform, $mDbkeyform;
	/* private */ var $mNamespace, $mInterwiki;
	/* private */ var $mArticleID, $mRestrictions, $mRestrictionsLoaded;
	/* private */ var $mOtherNamespaces, $mNamespacesLoaded;

	/* private */ function Title()
	{
		$this->mInterwiki = $this->mNamespace =
		$this->mUrlform = $this->mTextform = $this->mDbkeyform = "";
		$this->mArticleID = -1;
		$this->mNamespacesLoaded = $this->mRestrictionsLoaded = false;
		$this->mOtherNamespaces = array();
		$this->mRestrictions = array();
	}

	# Static factory methods
	#
	function newFromDBKey( $key )
	{
		$t = new Title();
		$t->mDbkeyform = $key;
		$t->secureAndSplit();
		return $t;
	}

	function newFromText( $text )
	{
		$t = new Title();
		$t->mDbkeyform = str_replace( " ", "_", $text );
		$t->secureAndSplit();
		return $t;
	}

	function newFromURL( $url )
	{
		$t = new Title();
		$s = urldecode( $url );
		$t->mDbkeyform = str_replace( " ", "_", $s );
		$t->secureAndSplit();
		return $t;
	}

	function getText() { return $this->mTextform; }
	function getURL() { return $this->mUrlform; }
	function getDBKey() { return $this->mDbkeyform; }
	function getNamespace() { return $this->mNamespace; }
	function getInterwiki() { return $this->mInterwiki; }

	function getPrefixedDBkey()
	{
		return $this->prefixes( false ) . $this->mDbkeyform;
	}

	function getPrefixedText()
	{
		return $this->prefixes( true ) . $this->mTextform;
	}

	function getPrefixedURL()
	{
		return $this->prefixes( false ) . $this->mUrlform;
	}

	function getFullURL()
	{
		global $wgArticlePath, $wgValidInterwikis;

		if ( "" == $this->mInterwiki ) {
			$p = $wgArticlePath;
		} else {
			$p = $wgValidInterwikis[$this->mInterwiki];
		}
		if ( "" != $this->mNamespace ) { $n = $this->mNamespace . ":"; }
		return str_replace( "$1", $n . $this->mUrlform, $p );
	}

	function getEditURL()
	{
		global $wgServer, $wgScript;

		if ( "" != $this->mInterwiki ) { return ""; }
		$s = "$wgServer$wgScript?title=" .
		  $this->getPrefixedURL() . "&action=edit";

		return $s;
	}

	function isExternal() { return ( "" != $this->mInterwiki ); }

	function userCanEdit()
	{
		global $wgUser;

		if ( 0 == strcmp( "Special", $this->mNamespace ) ) {
			return false;
		}
		$ur = $wgUser->getRights();
		foreach ( $this->getRestrictions() as $r ) {
			if ( "" != $r && ( ! in_array( $r, $ur ) ) ) {
				return false;
			}
		}
		return true;
	}

	function getOtherNamespaces()
	{
		if ( ! $this->mNamespacesLoaded ) {
			$conn = wfGetDB();
			$sql = "SELECT cur_namespace,cur_id FROM cur " .
			  "WHERE cur_title='{$this->mDbkeyform}'";
			wfDebug( "Title: 2: $sql\n" );

			$res = mysql_query( $sql, $conn );
			if ( ! $res || 0 == mysql_num_rows( $res ) ) {
				return $this->mOtherNamespaces;
			}

			while ( $row = mysql_fetch_object( $res ) ) {
				$ns = $row->cur_namespace;
				if ( 0 != strcmp( $this->mNamespace, $ns ) ) {
					array_push( $this->mOtherNamspaces, $ns );
				}
			}
			$this->mNamespacesLoaded = true;
			mysql_free_result( $res );
		}
		return $this->mOtherNamespaces;
	}

	function getRestrictions()
	{
		$id = $this->getArticleID();
		if ( ! $this->mRestrictionsLoaded ) {
			$res = wfGetSQL( "cur", "cur_restrictions", "cur_id=$id" );
			$this->mRestrictions = explode( ",", trim( $res ) );
			$this->mRestrictionsLoaded = true;
		}
		return $this->mRestrictions;
	}

	function getArticleID()
	{
		global $wgArticleIDcache;

		if ( -1 != $this->mArticleID ) { return $this->mArticleID; }

		$pt = $this->getPrefixedDBkey();
		if ( key_exists( $pt, $wgArticleIDcache ) ) {
			$this->mArticleID = $wgArticleIDcache[$pt];
		} else {
			$conn = wfGetDB();
			$sql = "SELECT cur_id FROM cur WHERE cur_namespace=" .
			  "'{$this->mNamespace}' AND cur_title='{$this->mDbkeyform}'";
			# wfDebug( "Title: 1: $sql\n" );
			$res = mysql_query( $sql, $conn );

			if ( ! $res || 0 == mysql_num_rows( $res ) ) {
				$this->mArticleID = 0;
			} else {
				$s = mysql_fetch_object( $res );
				$this->mArticleID = $s->cur_id;
				mysql_free_result( $res );
			}
		}
		$wgArticleIDcache[$pt] = $this->mArticleID;
		return $this->mArticleID;
	}

	/* private */ function prefixes( $textform )
	{
		if ( "" != $this->mInterwiki ) {
			$p = $this->mInterwiki . ":";
		}
		if ( "" != $this->mNamespace ) {
			if ( $textform ) {
				$p .= str_replace( "_", " ", $this->mNamespace ) . ":";
			} else {
				$p .= $this->mNamespace . ":";
			}
		}
		return $p;
	}

	# Assumes that mDbkeyform has been set, and is urldecoded
    # and uses undersocres, but not otherwise munged.  This function
    # removes illegal characters, splits off the winterwiki and
    # namespace prefixes, sets the other forms, and canonicalizes
    # everything.
	#
	/* private */ function secureAndSplit()
	{
		global $wgLang, $wgValidInterwikis, $wgLocalInterwiki;

		$validNamespaces = $wgLang->getNamespaces();
		$this->mInterwiki = $this->mNamespace = "";

		$done = false;
		$t = trim( $this->mDbkeyform );
		if ( ":" == $t{0} ) {
			$r = substr( $t, 2 );
		} else {
			if ( preg_match( "/^([A-Za-z][A-Za-z0-9 _]*):(.*)$/", $t, $m ) ) {
				$p = strtolower( $m[1] );
				if ( key_exists( $p, $wgValidInterwikis ) ) {
					$t = $m[2];
					$this->mInterwiki = $p;

					if ( preg_match( "/^([A-Za-z][A-Za-z0-9 _]*):(.*)$/",
					  $t, $m ) ) {
						$p = strtolower( $m[1] );
					} else {
						$done = true;
					}
				}
				if ( ! $done ) {
					$p = ucfirst( $p );
					if ( in_array( $p, $validNamespaces ) ) {
						$t = $m[2];
						$this->mNamespace = str_replace( " ", "_", $p );
					}
				}
			}
			$r = $t;
		}
		if ( 0 == strcmp( $this->mInterwiki, $wgLocalInterwiki ) ) {
			$this->mInterwiki = "";
		}
		# We already know that some pages won't be in the database!
		#
		if ( "" != $this->mInterwiki || "Special" == $this->mNamespace ) {
			$this->mArticleID = 0;
		}
		# Strip illegal characters. Note that since many troublesome
		# characters like quote marks are stripped here, we don't
		# can avoid having to escape them in other places.
		#
		$t = preg_replace( "/([^-,.()' _0-9A-Za-z\/:\x80-\xff])/", "", $r );

		$t = ucfirst( $t );
		$this->mDbkeyform = $t;
		$this->mUrlform = wfUrlencode( $t );
		$this->mTextform = str_replace( "_", " ", $t );
	}
}
?>
