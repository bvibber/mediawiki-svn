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

class Title {
	/* private */ var $mTextform, $mUrlform, $mDbkeyform;
	/* private */ var $mNamespace, $mInterwiki, $mFragment;
	/* private */ var $mArticleID, $mRestrictions, $mRestrictionsLoaded;

	/* private */ function Title()
	{
		$this->mInterwiki = $this->mUrlform =
		$this->mTextform = $this->mDbkeyform = "";
		$this->mArticleID = -1;
		$this->mNamespace = 0;
		$this->mRestrictionsLoaded = false;
		$this->mRestrictions = array();
	}

	# Static factory methods
	#
	function newFromDBkey( $key )
	{
		$t = new Title();
		$t->mDbkeyform = $key;
		$t->secureAndSplit();
		return $t;
	}

	function newFromText( $text )
	{
		$trans = get_html_translation_table( HTML_ENTITIES );
		$trans = array_flip( $trans );
		$text = strtr( $text, $trans );
		$text = urldecode( $text );

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

	function legalChars()
	{
		return "-,.()' &;%!?_0-9A-Za-z\\/:\\xA0-\\xFF";
	}

	function getInterwikiLink( $key )
	{
		global $wgValidInterwikis;

		if ( array_key_exists( $key, $wgValidInterwikis ) ) {
			return $wgValidInterwikis[$key];
		} else return "";
	}

	function getText() { return $this->mTextform; }
	function getURL() { return $this->mUrlform; }
	function getDBkey() { return $this->mDbkeyform; }
	function getNamespace() { return $this->mNamespace; }
	function setNamespace( $n ) { $this->mNamespace = $n; }
	function getInterwiki() { return $this->mInterwiki; }
	function getFragment() { return $this->mFragment; }

	function getIndexTitle()
	{
		$lc = SearchEngine::legalSearchChars() . "&#;";
		$t = preg_replace( "/[^{$lc}]+/", " ", $this->mTextform );
		$t = preg_replace( "/\\b[{$lc}][{$lc}]\\b/", " ", $t );
		$t = preg_replace( "/\\b[{$lc}]\\b/", " ", $t );
		$t = preg_replace( "/\\s+/", " ", $t );

		$t = strtolower( $t );
		if ( Namespace::getIndex( "Image" ) == $this->mNamespace ) {
			$t = preg_replace( "/ (png|gif|jpg|jpeg)$/", "", $t );
		}
		return trim( $t );
	}

	/* static */ function makeName( $ns, $title )
	{
		$n = Namespace::getName( $ns );
		if ( "" == $n ) { return $title; }
		else { return "{$n}:{$title}"; }
	}

	function getPrefixedDBkey()
	{
		$s = $this->prefix( $this->mDbkeyform );
		$s = str_replace( " ", "_", $s );
		return $s;
	}

	function getPrefixedText()
	{
		$s = $this->prefix( $this->mTextform );
		$s = str_replace( "_", " ", $s );
		return $s;
	}

	function getPrefixedURL()
	{
		$s = $this->prefix( $this->mDbkeyform );
		$s = str_replace( " ", "_", $s );
		return urlencode( $s );
	}

	function getFullURL()
	{
		global $wgArticlePath, $wgValidInterwikis;

		if ( "" == $this->mInterwiki ) {
			$p = $wgArticlePath;
		} else {
			$p = $wgValidInterwikis[$this->mInterwiki];
		}
		$n = Namespace::getName( $this->mNamespace );
		if ( "" != $n ) { $n .= "%3A"; }
		$u = str_replace( "$1", $n . $this->mUrlform, $p );
		if ( "" != $this->mFragment ) {
			$u .= "#" . $this->mFragment;
		}
		return $u;
	}

	function getEditURL()
	{
		global $wgServer, $wgScript;

		if ( "" != $this->mInterwiki ) { return ""; }
		$s = "$wgServer$wgScript?title=" .
		  $this->getPrefixedURL() . "&amp;action=edit";

		return $s;
	}

	function isExternal() { return ( "" != $this->mInterwiki ); }

	function isProtected()
	{
		if ( -1 == $this->mNamespace ) { return true; }
		$a = $this->getRestrictions();
		if ( in_array( "sysop", $a ) ) { return true; }
		return false;
	}

	function userIsWatching()
	{
		global $wgUser;

		if ( -1 == $this->mNamespace ) { return false; }
		if ( 0 == $this->getArticleID() ) { return false; }
		if ( 0 == $wgUser->getID() ) { return false; }

		return $wgUser->isWatched( $this->getPrefixedDBkey() );
	}

	function userCanEdit()
	{
		global $wgUser;

		if ( -1 == $this->mNamespace ) { return false; }
		# if ( 0 == $this->getArticleID() ) { return false; }

		$ur = $wgUser->getRights();
		foreach ( $this->getRestrictions() as $r ) {
			if ( "" != $r && ( ! in_array( $r, $ur ) ) ) {
				return false;
			}
		}
		return true;
	}

	function getRestrictions()
	{
		$id = $this->getArticleID();
		if ( 0 == $id ) { return array(); }

		if ( ! $this->mRestrictionsLoaded ) {
			$res = wfGetSQL( "cur", "cur_restrictions", "cur_id=$id" );
			$this->mRestrictions = explode( ",", trim( $res ) );
			$this->mRestrictionsLoaded = true;
		}
		return $this->mRestrictions;
	}

	function getArticleID()
	{
		global $wgLinkCache;

		if ( -1 != $this->mArticleID ) { return $this->mArticleID; }
		$this->mArticleID = $wgLinkCache->addLink(
		  $this->getPrefixedDBkey() );
		return $this->mArticleID;
	}

	function resetArticleID( $newid )
	{
		global $wgLinkCache;
		$wgLinkCache->clearBadLink( $this->getPrefixedDBkey() );

		if ( 0 == $newid ) { $this->mArticleID = -1; }
		else { $this->mArticleID = $newid; }
		$this->mRestrictionsLoaded = false;
		$this->mRestrictions = array();
	}

	/* private */ function prefix( $name )
	{
		$p = "";
		if ( "" != $this->mInterwiki ) {
			$p = $this->mInterwiki . ":";
		}
		if ( -1 == $this->mNamespace ) {
			$p .= "Special:";
		} else if ( 0 != $this->mNamespace ) {
			$p .= Namespace::getName( $this->mNamespace ) . ":";
		}
		return $p . $name;
	}

	# Assumes that mDbkeyform has been set, and is urldecoded
    # and uses undersocres, but not otherwise munged.  This function
    # removes illegal characters, splits off the winterwiki and
    # namespace prefixes, sets the other forms, and canonicalizes
    # everything.  This one function is really at the core of
	# Wiki--don't mess with it unless you're really sure you know
	# what you're doing.
	#
	/* private */ function secureAndSplit()
	{
		global $wgLang, $wgValidInterwikis, $wgLocalInterwiki;

		$validNamespaces = $wgLang->getNamespaces();
		$this->mInterwiki = $this->mFragment = "";
		$this->mNamespace = 0;

		$done = false;
		$t = trim( $this->mDbkeyform );
		if ( ":" == $t{0} ) {
			$r = substr( $t, 1 );
		} else {
	 		if ( preg_match( "/^([A-Za-z][A-Za-z0-9_]*):(.*)$/", $t, $m ) ) {
				$p = strtolower( $m[1] );
				if ( array_key_exists( $p, $wgValidInterwikis ) ) {
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
						$this->mNamespace = Namespace::getIndex(
						  str_replace( " ", "_", $p ) );
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
		if ( "" != $this->mInterwiki || -1 == $this->mNamespace ) {
			$this->mArticleID = 0;
		}
		$f = strstr( $r, "#" );
		if ( false !== $f ) {
			$this->mFragment = substr( $f, 1 );
			$r = substr( $r, 0, strlen( $r ) - strlen( $f ) );
		}
		# Strip illegal characters.
		#
		$tc = Title::legalChars();
		$t = preg_replace( "/[^{$tc}]/", "", $r );

		$t = ucfirst( $t );
		$this->mDbkeyform = $t;
		$this->mUrlform = wfUrlencode( $t );
		$this->mTextform = str_replace( "_", " ", $t );
	}
}
?>
