<?
# See title.doc

/* private */ $wgValidInterwikis = array(
	# Special cases
	"w"		=> "http://www.wikipedia.org/wiki/$1",
	"m"		=> "http://meta.wikipedia.org/wiki/$1",
	"simple"=> "http://simple.wikipedia.com/wiki.cgi?$1",

	# ISO 639 2-letter language codes
	"aa"    => "http://aa.wikipedia.com/wiki.cgi?$1",
	"ab"    => "http://ab.wikipedia.com/wiki.cgi?$1",
	"af"    => "http://af.wikipedia.com/wiki.cgi?$1",
	"am"    => "http://am.wikipedia.com/wiki.cgi?$1",
	"ar"	=> "http://ar.wikipedia.com/wiki.cgi?$1",
	"as"    => "http://as.wikipedia.com/wiki.cgi?$1",
	"ay"    => "http://ay.wikipedia.com/wiki.cgi?$1",
	"az"    => "http://az.wikipedia.com/wiki.cgi?$1",
	"ba"    => "http://ba.wikipedia.com/wiki.cgi?$1",
	"be"    => "http://be.wikipedia.com/wiki.cgi?$1",
	"bh"    => "http://bh.wikipedia.com/wiki.cgi?$1",
	"bi"    => "http://bi.wikipedia.com/wiki.cgi?$1",
	"bn"    => "http://bn.wikipedia.com/wiki.cgi?$1",
	"bn"    => "http://bn.wikipedia.com/wiki.cgi?$1",
	"bo"    => "http://bo.wikipedia.com/wiki.cgi?$1",
	"ca"	=> "http://ca.wikipedia.com/wiki.cgi?$1",
	"co"    => "http://co.wikipedia.com/wiki.cgi?$1",
	"cs"    => "http://cs.wikipedia.com/wiki.cgi?$1",
	"cy"    => "http://cy.wikipedia.com/wiki.cgi?$1",
	"da"    => "http://da.wikipedia.org/wiki/$1",
	"de"	=> "http://de.wikipedia.org/wiki/$1",
	"dk"    => "http://da.wikipedia.org/wiki/$1",
	"dz"    => "http://dz.wikipedia.com/wiki.cgi?$1",
	"el"    => "http://el.wikipedia.com/wiki.cgi?$1",
	"en"	=> "http://www.wikipedia.org/wiki/$1",    # May in future be renamed to en.wikipedia.org; should work as alternate
	"eo"	=> "http://eo.wikipedia.com/wiki/$1",
	"es"	=> "http://es.wikipedia.com/wiki.cgi?$1",
	"et"    => "http://et.wikipedia.com/wiki.cgi?$1",
	"eu"    => "http://eu.wikipedia.com/wiki.cgi?$1",
	"fa"    => "http://fa.wikipedia.com/wiki.cgi?$1",
	"fi"    => "http://fi.wikipedia.com/wiki.cgi?$1",
	"fj"    => "http://fj.wikipedia.com/wiki.cgi?$1",
	"fo"    => "http://fo.wikipedia.com/wiki.cgi?$1",
	"fr"	=> "http://fr.wikipedia.com/wiki.cgi?$1",
	"fy"    => "http://fy.wikipedia.com/wiki.cgi?$1",
	"ga"    => "http://ga.wikipedia.com/wiki.cgi?$1",
	"gl"    => "http://gl.wikipedia.com/wiki.cgi?$1",
	"gn"    => "http://gn.wikipedia.com/wiki.cgi?$1",
	"gu"    => "http://gu.wikipedia.com/wiki.cgi?$1",
	"ha"    => "http://ha.wikipedia.com/wiki.cgi?$1",
	"he"	=> "http://he.wikipedia.com/wiki.cgi?$1",
	"hi"    => "http://hi.wikipedia.com/wiki.cgi?$1",
	"hr"    => "http://hr.wikipedia.com/wiki.cgi?$1",
	"hu"	=> "http://hu.wikipedia.com/wiki.cgi?$1",
	"hy"    => "http://hy.wikipedia.com/wiki.cgi?$1",
	"ia"    => "http://ia.wikipedia.com/wiki.cgi?$1",
	"id"    => "http://id.wikipedia.com/wiki.cgi?$1",
	"ik"    => "http://ik.wikipedia.com/wiki.cgi?$1",
	"is"    => "http://is.wikipedia.com/wiki.cgi?$1",
	"it"	=> "http://it.wikipedia.com/wiki.cgi?$1",
	"iu"    => "http://iu.wikipedia.com/wiki.cgi?$1",
	"ja"	=> "http://ja.wikipedia.org/wiki/$1",
	"jv"    => "http://jv.wikipedia.com/wiki.cgi?$1",
	"ka"    => "http://ka.wikipedia.com/wiki.cgi?$1",
	"kk"    => "http://kk.wikipedia.com/wiki.cgi?$1",
	"kl"    => "http://kl.wikipedia.com/wiki.cgi?$1",
	"km"    => "http://km.wikipedia.com/wiki.cgi?$1",
	"kn"    => "http://kn.wikipedia.com/wiki.cgi?$1",
	"ko"    => "http://ko.wikipedia.com/wiki.cgi?$1",
	"ks"    => "http://ks.wikipedia.com/wiki.cgi?$1",
	"ku"    => "http://ku.wikipedia.com/wiki.cgi?$1",
	"ky"    => "http://ky.wikipedia.com/wiki.cgi?$1",
	"la"    => "http://la.wikipedia.com/wiki.cgi?$1",
	"lo"    => "http://lo.wikipedia.com/wiki.cgi?$1",
	"lv"    => "http://lv.wikipedia.com/wiki.cgi?$1",
	"mg"    => "http://mg.wikipedia.com/wiki.cgi?$1",
	"mi"    => "http://mi.wikipedia.com/wiki.cgi?$1",
	"mk"    => "http://mk.wikipedia.com/wiki.cgi?$1",
	"ml"    => "http://ml.wikipedia.com/wiki.cgi?$1",
	"mn"    => "http://mn.wikipedia.com/wiki.cgi?$1",
	"mo"    => "http://mo.wikipedia.com/wiki.cgi?$1",
	"mr"    => "http://mr.wikipedia.com/wiki.cgi?$1",
	"ms"    => "http://ms.wikipedia.com/wiki.cgi?$1",
	"my"    => "http://my.wikipedia.com/wiki.cgi?$1",
	"na"    => "http://na.wikipedia.com/wiki.cgi?$1",
	"ne"    => "http://ne.wikipedia.com/wiki.cgi?$1",
	"nl"	=> "http://nl.wikipedia.org/wiki/$1",
	"no"    => "http://no.wikipedia.com/wiki.cgi?$1",
	"oc"    => "http://oc.wikipedia.com/wiki.cgi?$1",
	"om"    => "http://om.wikipedia.com/wiki.cgi?$1",
	"or"    => "http://or.wikipedia.com/wiki.cgi?$1",
	"pa"    => "http://pa.wikipedia.com/wiki.cgi?$1",
	"pl"	=> "http://pl.wikipedia.com/wiki.cgi?$1",
	"ps"    => "http://ps.wikipedia.com/wiki.cgi?$1",
	"pt"	=> "http://pt.wikipedia.com/wiki.cgi?$1",
	"qu"    => "http://qu.wikipedia.com/wiki.cgi?$1",
	"rm"    => "http://rm.wikipedia.com/wiki.cgi?$1",
	"rn"    => "http://rn.wikipedia.com/wiki.cgi?$1",
	"ro"    => "http://ro.wikipedia.com/wiki.cgi?$1",
	"ru"	=> "http://ru.wikipedia.com/wiki.cgi?$1",
	"rw"    => "http://rw.wikipedia.com/wiki.cgi?$1",
	"sa"    => "http://sa.wikipedia.com/wiki.cgi?$1",
	"sd"    => "http://sd.wikipedia.com/wiki.cgi?$1",
	"sg"    => "http://sg.wikipedia.com/wiki.cgi?$1",
	"sh"    => "http://sh.wikipedia.com/wiki.cgi?$1",
	"si"    => "http://si.wikipedia.com/wiki.cgi?$1",
	"sk"    => "http://sk.wikipedia.com/wiki.cgi?$1",
	"sl"    => "http://sl.wikipedia.com/wiki.cgi?$1",
	"sm"    => "http://sm.wikipedia.com/wiki.cgi?$1",
	"sn"    => "http://sn.wikipedia.com/wiki.cgi?$1",
	"so"    => "http://so.wikipedia.com/wiki.cgi?$1",
	"sq"    => "http://sq.wikipedia.com/wiki.cgi?$1",
	"sr"    => "http://sr.wikipedia.com/wiki.cgi?$1",
	"ss"    => "http://ss.wikipedia.com/wiki.cgi?$1",
	"st"    => "http://st.wikipedia.com/wiki.cgi?$1",
	"su"    => "http://su.wikipedia.com/wiki.cgi?$1",
	"sv"	=> "http://sv.wikipedia.com/wiki.cgi?$1",
	"sw"    => "http://sw.wikipedia.com/wiki.cgi?$1",
	"ta"    => "http://ta.wikipedia.com/wiki.cgi?$1",
	"te"    => "http://te.wikipedia.com/wiki.cgi?$1",
	"tg"    => "http://tg.wikipedia.com/wiki.cgi?$1",
	"th"    => "http://th.wikipedia.com/wiki.cgi?$1",
	"ti"    => "http://ti.wikipedia.com/wiki.cgi?$1",
	"tk"    => "http://tk.wikipedia.com/wiki.cgi?$1",
	"tl"    => "http://tl.wikipedia.com/wiki.cgi?$1",
	"tn"    => "http://tn.wikipedia.com/wiki.cgi?$1",
	"to"    => "http://to.wikipedia.com/wiki.cgi?$1",
	"tr"    => "http://tr.wikipedia.com/wiki.cgi?$1",
	"ts"    => "http://ts.wikipedia.com/wiki.cgi?$1",
	"tt"    => "http://tt.wikipedia.com/wiki.cgi?$1",
	"tw"    => "http://tw.wikipedia.com/wiki.cgi?$1",
	"ug"    => "http://ug.wikipedia.com/wiki.cgi?$1",
	"uk"    => "http://uk.wikipedia.com/wiki.cgi?$1",
	"ur"    => "http://ur.wikipedia.com/wiki.cgi?$1",
	"uz"    => "http://uz.wikipedia.com/wiki.cgi?$1",
	"vi"    => "http://vi.wikipedia.com/wiki.cgi?$1",
	"vo"    => "http://vo.wikipedia.com/wiki.cgi?$1",
	"wo"    => "http://wo.wikipedia.com/wiki.cgi?$1",
	"xh"    => "http://xh.wikipedia.com/wiki.cgi?$1",
	"yi"    => "http://yi.wikipedia.com/wiki.cgi?$1",
	"yo"    => "http://yo.wikipedia.com/wiki.cgi?$1",
	"za"    => "http://za.wikipedia.com/wiki.cgi?$1",
	"zh"	=> "http://zh.wikipedia.com/wiki.cgi?$1",
	"zu"    => "http://zu.wikipedia.com/wiki.cgi?$1"
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
		global $wgLang, $wgServer, $HTTP_SERVER_VARS;
		
		$t = new Title();
		$s = urldecode( $url ); # This is technically wrong, as anything
								# we've gotten is already decoded by PHP.
								# Kept for backwards compatibility with
								# buggy URLs we had for a while...
		
		# For links that came from outside, check for alternate/legacy
		# character encoding.
		if( strncmp($wgServer, $HTTP_SERVER_VARS["HTTP_REFERER"], strlen( $wgServer ) ) )
			$s = $wgLang->checkTitleEncoding( $s );
		
		$t->mDbkeyform = str_replace( " ", "_", $s );
		$t->secureAndSplit();
		return $t;
	}

	function legalChars()
	{
		return "-,.()' &;%!?_0-9A-Za-z\\/:\\x80-\\xFF";
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

	/* static */ function indexTitle( $ns, $title )
	{
		global $wgDBminWordLen, $wgLang;

		$lc = SearchEngine::legalSearchChars() . "&#;";
		$t = preg_replace( "/[^{$lc}]+/", " ", $title );
		$t = strtolower( $t );

		# Handle 's, s'
		$t = preg_replace( "/([{$lc}]+)'s( |$)/", "\\1 \\1's ", $t );
		$t = preg_replace( "/([{$lc}]+)s'( |$)/", "\\1s ", $t );

		$t = preg_replace( "/\\s+/", " ", $t );

		if ( $ns == Namespace::getImage() ) {
			$t = preg_replace( "/ (png|gif|jpg|jpeg|ogg)$/", "", $t );
		}
		return trim( $t );
	}

	function getIndexTitle()
	{
		return Title::indexTitle( $this->mNamespace, $this->mTextform );
	}

	/* static */ function makeName( $ns, $title )
	{
		global $wgLang;

		$n = $wgLang->getNsText( $ns );
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

		$s = urlencode ( $s ) ;
		# Cleaning up URL to make it look nice -- is this safe?
		$s = preg_replace( "/%3[Aa]/", ":", $s );
		$s = preg_replace( "/%2[Ff]/", "/", $s );
		$s = str_replace( "%28", "(", $s );
		$s = str_replace( "%29", ")", $s );
		return $s;
	}

	function getFullURL()
	{
		global $wgLang, $wgArticlePath, $wgValidInterwikis;

		if ( "" == $this->mInterwiki ) {
			$p = $wgArticlePath;
		} else {
			$p = $wgValidInterwikis[$this->mInterwiki];
		}
		$n = $wgLang->getNsText( $this->mNamespace );
		if ( "" != $n ) { $n .= ":"; }
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
		$s = wfLocalUrl( $this->getPrefixedURL(), "action=edit" );

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

	function isLog()
	{
		if ( $this->mNamespace != Namespace::getWikipedia() ) {
			return false;
		}
		if ( ( 0 == strcmp( wfMsg( "uploadlogpage" ), $this->mDbkeyform ) ) ||
		  ( 0 == strcmp( wfMsg( "dellogpage" ), $this->mDbkeyform ) ) ) {
			return true;
		}
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
		global $wgLang;

		$p = "";
		if ( "" != $this->mInterwiki ) {
			$p = $this->mInterwiki . ":";
		}
		if ( 0 != $this->mNamespace ) {
			$p .= $wgLang->getNsText( $this->mNamespace ) . ":";
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
		unset( $validNamespaces[0] );

		$this->mInterwiki = $this->mFragment = "";
		$this->mNamespace = 0;

		$t = preg_replace( "/[\\s_]+/", "_", $this->mDbkeyform );
		if ( "_" == $t{0} ) { $t = substr( $t, 1 ); }
		$l = strlen( $t );
		if ( $l && ( "_" == $t{$l-1} ) ) { $t = substr( $t, 0, $l-1 ); }
		if ( "" == $t ) { $t = "_"; }

		$this->mDbkeyform = $t;
		$done = false;

		$imgpre = ":" . $wgLang->getNsText( Namespace::getImage() ) . ":";
		if ( 0 == strncasecmp( $imgpre, $t, strlen( $imgpre ) ) ) {
			$t = substr( $t, 1 );
		}
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
					foreach ( $validNamespaces as $ns ) {
						if ( 0 == strcasecmp( $p, $ns ) ) {
							$t = $m[2];
							$this->mNamespace = $wgLang->getNsIndex(
							  str_replace( " ", "_", $p ) );
							break;
						}
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

		$t = $wgLang->ucfirst( $t );
		$this->mDbkeyform = $t;
		$this->mUrlform = wfUrlencode( $t );
		$this->mTextform = str_replace( "_", " ", $t );
	}
}
?>
