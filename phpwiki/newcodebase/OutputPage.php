<?
# See design.doc

class OutputPage {
	var $mHeaders, $mCookies, $mMetatags, $mKeywords;
	var $mLinktags, $mPagetitle, $mBodytext, $mDebugtext;
	var $mHTMLtitle, $mRobotpolicy, $mIsarticle, $mPrintable;
	var $mSubtitle;

	var $mDTopen, $mLastSection; # Used for processing DL, PRE
	var $mLanguageLinks, $mSupressQuickbar;

	function OutputPage()
	{
		$this->mHeaders = $this->mCookies = $this->mMetatags =
		$this->mKeywords = $this->mLinktags = array();
		$this->mHTMLtitle = $this->mPagetitle = $this->mBodytext =
		$this->mLastSection = 
		$this->mSubtitle = $this->mDebugtext = $this->mRobotpolicy = "";
		$this->mIsarticle = $this->mPrintable = true;
		$this->mSupressQuickbar = $this->mDTopen = false;
		$this->mLanguageLinks = array();
	}

	function addHeader( $name, $val ) { array_push( $this->mHeaders, "$name: $val" ) ; }
	function addCookie( $name, $val ) { array_push( $this->mCookies, array( $name, $val ) ); }

	# To add an http-equiv meta tag, precede the name with "http:"
	function addMeta( $name, $val ) { array_push( $this->mMetatags, array( $name, $val ) ); }
	function addKeyword( $text ) { array_push( $this->mKeywords, $text ); }
	function addLink( $rel, $rev, $target ) { array_push( $this->mLinktags, array( $rel, $rev, $target ) ); }

	function setRobotpolicy( $str ) { $this->mRobotpolicy = $str; }
	function setHTMLtitle( $name ) { $this->mHTMLtitle = $name; }
	function setPageTitle( $name ) { $this->mPagetitle = $name; }
	function getPageTitle() { return $this->mPagetitle; }
	function setSubtitle( $str ) { $this->mSubtitle = $str; }
	function getSubtitle() { return $this->mSubtitle; }
	function setArticleFlag( $v ) { $this->mIsarticle = $v; }
	function isArticle() { return $this->mIsarticle; }
	function setPrintable() { $this->mPrintable = true; }
	function isPrintable() { return $this->mPrintable; }
	function getLanguageLinks() { return $this->mLanguageLinks; }

	function supressQuickbar() { $this->mSupressQuickbar = true; }
	function isQuickbarSupressed() { return $this->mSupressQuickbar; }

	function addHTML( $text ) { $this->mBodytext .= $text; }
	function debug( $text ) { $this->mDebugtext .= $text; }

	# First pass--just handle <nowiki> sections, pass the rest off
	# to doWikiPass2() which does all the real work.
	#

	function addWikiText( $text, $linestart = true )
	{
		$unique = "3iyZiyA7iMwg5rhxP0Dcc9oTnj8qD1jm1Sfv4";
		$nwlist = array();
		$nwsecs = 0;
		$stripped = "";

		while ( "" != $text ) {
			$p = preg_split( "/<\\s*nowiki\\s*>/i", $text, 2 );
			$stripped .= $p[0];

			if ( "" == $p[1] ) { $text = ""; }
			else {
				$q = preg_split( "/<\\/\\s*nowiki\\s*>/i", $p[1], 2 );
				++$nwsecs;
				$nwlist[$nwsecs] = $q[0];
				$stripped .= $unique;
				$text = $q[1];
			}
		}
		$text = $this->doWikiPass2( $stripped, $linestart );
		for ( $i = 1; $i <= $nwsecs; ++$i ) {
			$text = preg_replace( "/{$unique}/", $nwsecs[1],
			  $text, 1 );
		}
		$this->addHTML( $text );
	}

	# Finally, all the text has been munged and accumulated into
	# the object, let's actually output it:
	#
	function output()
	{
		global $wgUser, $wgDebugComments, $wgCookieExpiration;
		$sk = $wgUser->getSkin();

		# TODO: Internationalization
		$this->addHeader( "Content-type",
		  "text/html; charset=iso-8859-1" );

		foreach( $this->mHeaders as $t ) {
			header( $t );
		}
		$exp = time() + $wgCookieExpiration;
		foreach( $this->mCookies as $name => $val ) {
			setcookie( $name, $val, $exp, "/" );
		}
		$sk->initPage();
		print $this->headElement();

		print "<body";
		$ops = $sk->getBodyOptions();
		foreach ( $ops as $name => $val ) {
			print " $name='$val'";
		}
		print ">\n";
		if ( $wgDebugComments ) {
			print "<!-- Wiki debugging output:\n" .
			  $this->mDebugtext . "-->\n";
		}
		print $sk->beforeContent();
		print $this->mBodytext;
		print $sk->afterContent();
		print "</body></html>";
		flush();
	}

	# Note: these arguments are keys into wfMsg(), not text!
	#
	function errorpage( $title, $msg )
	{
		global $wgTitle;

		$this->mDebugtext .= "Original title: " .
		  $wgTitle->getText() . "\n";
		$this->setHTMLTitle( wfMsg( "errorpagetitle" ) );
		$this->setPageTitle( wfMsg( $title ) );
		$this->setRobotpolicy( "noindex,nofollow" );
		$this->setArticleFlag( false );

		$this->mBodytext = "";
		$this->addHTML( "<p>" . wfMsg( $msg ) . "\n" );
		$this->returnToMain();
	}

	function databaseError( $op )
	{
		wfDebug( "MySQL: " . mysql_errno() . ": " . mysql_error() . "\n" );
		$this->setPageTitle( wfMsg( "databaseerror" ) );
		$this->setRobotpolicy( "noindex,nofollow" );
		$this->setArticleFlag( false );

		$this->mBodytext = str_replace( "$1", $op, wfMsg( "dberrortext" ) );
		$this->returnToMain();
	}

	function returnToMain()
	{
		global $wgOut, $returnto;

		if ( "" == $returnto ) {
			$r = wfMsg( "returntomain" );
		} else {
			$r = str_replace( "$1", $returnto, wfMsg( "returnto" ) );
			$wgOut->addMeta( "http:Refresh", "10;url=" .
			  wfLocalLink( $returnto ) );
		}
		$wgOut->addWikiText( "\n<p>$r\n" );
	}

	# Well, OK, it's actually about 14 passes.  But since all the
	# hard lifting is done inside PHP's regex code, it probably
	# wouldn't speed things up much to add a real parser.
	#
	function doWikiPass2( $text, $linestart )
	{
		global $wgUser;

		$text = $this->removeHTMLtags( $text );
		$text = $this->replaceVariables( $text );

		$text = preg_replace( "/(^|\n)-----*/", "\\1<hr>", $text );
		$text = str_replace ( "<HR>", "<hr>", $text );

		$text = $this->doQuotes( $text );
		$text = $this->doHeadings( $text );
		$text = $this->doBlockLevels( $text, $linestart );

		$text = $this->parseImages( $text );
		$text = $this->replaceExternalLinks( $text );
		$text = $this->replaceInternalLinks ( $text );

		$text = $this->magicISBN( $text );
		$text = $this->magicRFC( $text );
		$text = $this->autoNumberHeadings( $text );

		$sk = $wgUser->getSkin();
		$text = $sk->transformContent( $text );

		return $text;
	}

	# TODO: The algorithm here is from Usemod, but it can generate
	# improperly nested HTML in the case of '''''xx''yy'''. That's
	# not trivial to fix.
	#
	/* private */ function doQuotes( $text )
	{
		$text = preg_replace( "/('*)'''(.*?)'''/",
		  "\\1<strong>\\2</strong>", $text );
		$text = preg_replace( "/''(.*?)''/", "<em>\\1</em>", $text );
		return $text;
	}

	/* private */ function doHeadings( $text )
	{
		for ( $i = 6; $i >= 1; --$i ) {
			$h = substr( "======", 0, $i );
			$text = preg_replace( "/(^|\\n)\\s*{$h}\\s+([^\\n]+)\\s+{$h}/",
			  "\\1<h{$i}>\\2</h{$i}>", $text );
		}
		return $text;
	}

	/* private */ function parseImages( $text )
	{
		global $wgUser;
		$sk = $wgUser->getSkin();

		$text = preg_replace(
		  "/(^|[^[])http:\/\/([a-zA-Z0-9_\/:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
		  "\\1" . $sk->makeImageLink( "http://\\2.\\3", "[Image]" ), $text );
		return $text;
	}

	/* private */ function replaceExternalLinks( $text )
	{
		$text = $this->subReplaceExternalLinks( $text, "http:", true );
		$text = $this->subReplaceExternalLinks( $text, "ftp:", false );
		$text = $this->subReplaceExternalLinks( $text, "gopher:", false );
		$text = $this->subReplaceExternalLinks( $text, "news:", false );
		$text = $this->subReplaceExternalLinks( $text, "mailto:", false );
		return $text;
	}

	/* private */ function subReplaceExternalLinks( $s, $protocol, $autonumber )
	{
		global $wgUser;

		if ( strstr( $s, "[$protocol" ) === false ) { return $s; }
		$cnt = 1;
		$a = explode( "[$protocol", " " . $s );
		$s = array_shift( $a );
		$s = substr( $s, 1 );
		$image = "";
		$sk = $wgUser->getSkin();

		foreach ( $a as $t ) {
			$b = spliti( "]", $t, 2 );
			if ( count($b) < 2 ) { $s .= "[Broken link: $b[0]]"; }
			else {
				$c = explode( " ", $b[0], 2 );
				if ( count ( $c ) == 1 ) { array_push( $c, "" ); }
				$link = $c[0];
				$text = trim( $c[1] );

				if ( "" == $text ) {
					if ( $autonumber ) { $text = "[" . $cnt++ . "]"; }
					else { $text = "[$protocol$link]"; }
				} else {
					if ( ! ( strstr( $text, " " ) === false ) &&
					  1 == $wgUser->getOption( "underline" ) ) {
						$text = "[$text]";
					}
				}
				if ( ! strstr( $b[1], "<hr>" ) === false ) { $cnt = 1; }
				$link = "~$protocol" . $link;

				$linkStyle = $sk->getExternalLinkAttributes( $link, $text );
				$theLink = "<a href=\"$link\"$linkStyle>$image$text</a>";
				$s .= $theLink . $b[1];
			}
		}
        $o_no_dot = "A-Za-z0-9_~/=?\:\%\+\&\#\-";
        $o = "\.$o_no_dot";
        $s = eregi_replace( "([^~\"])http://([$o]+[$o_no_dot])([^$o_no_dot])",
		  "\\1<a href=\"http://\\2\">".$image."http://\\2</a>\\3", $s );
        $s = str_replace( "~$protocol", "$protocol", $s );
		return $s;
	}

	/* private */ function replaceInternalLinks( $s )
	{
		global $wgTitle, $wgUser, $wgServer, $wgUploadPath, $wgLang;

		$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\x80-\\xff]";
		$sk = $wgUser->getSkin();

		$a = explode( "[[", " " . $s );
		$s = array_shift( $a );
		$s = substr( $s, 1 );

		foreach ( $a as $line ) {
			$e1 = "/^({$tc}+)\\|([^]]+)]](.*)$$/sD";
			$e2 = "/^({$tc}+)]](.*)$$/sD";

			if ( preg_match( $e1, $line, $m ) ) {
				$link = $m[1];
				$text = $m[2];
				$trail = $m[3];
			} else if ( preg_match( $e2, $line, $m ) ) {
				$link = $m[1];
				$text = "";
				$trail = $m[2];
			} else { # Invalid form; output directly
				$s .= "[[" . $line;
				continue;
			}
			if ( "" == $text ) { $text = $link; }

			if ( preg_match( "/^([a-z]+):(.*)$$/", $link,  $m ) ) {
				$pre = strtolower( $m[1] );
				$suf = $m[2];
				if ( "image" == $pre ) {
					$s .= $sk->makeImageLink( "$wgServer$wgUploadPath/"	.
					  $suf, $text );
					$s .= $trail;
				} else {
					$l = $wgLang->getLanguageName( $pre );
					if ( "" == $l ) {
						$s .= $sk->makeInternalLink( $link, $text,
						  "", $trail );
					} else {
						array_push( $this->mLanguageLinks, "$pre:$suf" );
						$s .= $trail;
					}
				}
			} else {
				$s .= $sk->makeInternalLink( $link, $text, "", $trail );
			}
		}
		return $s;
	}

	# Some functions here used by doBlockLevels()
	#
	/* private */ function closeParagraph( )
	{
		$result = "";
		if ( 0 != strcmp( "p", $this->mLastSection ) &&
		  0 != strcmp( "", $this->mLastSection ) ) {
			$result = "</" . $this->mLastSection  . ">";
		}
		$this->mLastSection = "";
		return $result;
	}
	# getCommon() returns the length of the longest common substring
	# of both arguments, starting at the beginning of both.
	#
	/* private */ function getCommon( $st1, $st2 )
	{
		$fl = strlen( $st1 );
		$shorter = strlen( $st2 );
		if ( $fl < $shorter ) { $shorter = $fl; }

		for ( $i = 0; $i < $shorter; ++$i ) {
			if ( $st1{$i} != $st2{$i} ) { break; }
		}
		return $i;
	}
	# These next three functions open, continue, and close the list
	# element appropriate to the prefix character passed into them.
	#
	/* private */ function openList( $char )
    {
		$result = $this->closeParagraph();

		if ( "*" == $char ) { $result .= "<ul><li>"; }
		else if ( "#" == $char ) { $result .= "<ol><li>"; }
		else if ( ":" == $char ) { $result .= "<dl><dd>"; }
		else if ( ";" == $char ) {
			$result .= "<dl><dt>";
			$this->mDTopen = true;
		}
		else { $result = "<!-- ERR 1 -->"; }

		return $result;
	}

	/* private */ function nextItem( $char )
	{
		if ( "*" == $char || "#" == $char ) { return "</li><li>"; }
		else if ( ":" == $char || ";" == $char ) {
			$close = "</dd>";
			if ( $this->mDTopen ) { $close = "</dt>"; }
			if ( ";" == $char ) {
				$this->mDTopen = true;
				return $close . "<dt>";
			} else {
				$this->mDTopen = false;
				return $close . "<dd>";
			}
		}
		return "<!-- ERR 2 -->";
	}

	/* private */function closeList( $char )
	{
		if ( "*" == $char ) { return "</li></ul>"; }
		else if ( "#" == $char ) { return "</li></ol>"; }
		else if ( ":" == $char ) {
			if ( $this->mDTopen ) {
				$this->mDTopen = false;
				return "</dt></dl>";
			} else {
				return "</dd></dl>";
			}
		}
		return "<!-- ERR 3 -->";
	}

	/* private */ function doBlockLevels( $text, $linestart )
	{
		# Parsing through the text line by line.  The main thing
		# happening here is handling of block-level elements p, pre,
		# and making lists from lines starting with * # : etc.
		#
		$a = explode( "\n", $text );
		$text = $lastPref = "";
		$this->mDTopen = $inBlockElem = false;

		if ( ! $linestart ) { $text .= array_shift( $a ); }
		foreach ( $a as $t ) {
			if ( "" != $text ) { $text .= "\n"; }

			$oLine = $t;
			$opl = strlen( $lastPref );
			$npl = strspn( $t, "*#:;" );
			$pref = substr( $t, 0, $npl );
			$pref2 = str_replace( ";", ":", $pref );
			$t = substr( $t, $npl );

			if ( 0 != $npl && 0 == strcmp( $lastPref, $pref2 ) ) {
				$text .= $this->nextItem( substr( $pref, -1 ) );

				if ( ";" == substr( $pref, -1 ) ) {
					$cpos = strpos( $t, ":" );
					if ( ! ( false === $cpos ) ) {
						$term = substr( $t, 0, $cpos );
						$text .= $term . $this->nextItem( ":" );
						$t = substr( $t, $cpos + 1 );
					}
				}
			} else if (0 != $npl || 0 != $opl) {
				$cpl = $this->getCommon( $pref, $lastPref );

				while ( $cpl < $opl ) {
					$text .= $this->closeList( $lastPref{$opl-1} );
					--$opl;
				}
				if ( $npl <= $cpl && $cpl > 0 ) {
					$text .= $this->nextItem( $pref{$cpl-1} );
				}
				while ( $npl > $cpl ) {
					$char = substr( $pref, $cpl, 1 );
					$text .= $this->openList( $char );

					if ( ";" == $char ) {
						$cpos = strpos( $t, ":" );
						if ( ! ( false === $cpos ) ) {
							$term = substr( $t, 0, $cpos );
							$text .= $term . $this->nextItem( ":" );
							$t = substr( $t, $cpos + 1 );
						}
					}
					++$cpl;
				}
				$lastPref = $pref2;
			}
			if ( 0 == $npl ) { # No prefix--go to paragraph mode
				if ( preg_match(
				  "/(<table|<blockquote|<h1|<h2|<h3|<h4|<h5|<h6)/i", $t ) ) {
					$text .= $this->closeParagraph();
					$inBlockElem = true;
				}
				if ( ! $inBlockElem ) {
					if ( " " == $t{0} ) { $newSection = "pre"; }
					else { $newSection = "p"; }

					if ( 0 == strcmp( "", trim( $oLine ) ) ) {
						$text .= $this->closeParagraph();
						$text .= "<" . $newSection . ">";
					} else if ( 0 != strcmp( $this->mLastSection,
					  $newSection ) ) {
						$text .= $this->closeParagraph();
						if ( 0 != strcmp( "p", $newSection ) ) {
							$text .= "<" . $newSection . ">";
						}
					}
					$this->mLastSection = $newSection;
				}
				if ( $inBlockElem &&
				  preg_match( "/(<\\/table|<\\/blockquote|<\\/h1|<\\/h2|<\\/h3|<\\/h4|<\\/h5|<\\/h6)/i", $t ) ) {
					$inBlockElem = false;
				}
			}
			$text .= $t;
		}
		return $text;
	}

	/* private */ function replaceVariables( $text )
	{
		global $wgLang;

		$v = date( "m" );
		$text = str_replace( "{{CURRENTMONTH}}", $v, $text );
		$v = $wgLang->getMonthName( date( "F" ) );
		$text = str_replace( "{{CURRENTMONTHNAME}}", $v, $text );
		$v = date( "j" );
		$text = str_replace( "{{CURRENTDAY}}", $v, $text );
		$v = date( "l" );
		$text = str_replace( "{{CURRENTDAYNAME}}", $v, $text );
		$v = date( "Y" );
		$text = str_replace( "{{CURRENTYEAR}}", $v, $text );

		if ( ! ( strstr( $text, "{{NUMBEROFARTICLES}}" ) === false ) ) {
			$v = wfNumberOfArticles();
			$text = str_replace( "{{NUMBEROFARTICLES}}", $v, $text );
		}
		return $text;
	}

	/* private */ function removeHTMLtags( $text )
	{
		$htmlpairs = array( # Tags that must be closed
			"b", "i", "u", "font", "big", "small", "sub", "sup", "h1",
			"h2", "h3", "h4", "h5", "h6", "cite", "code", "em", "s",
			"strike", "strong", "tt", "var", "div", "center",
			"blockquote", "ol", "ul", "dl", "table", "caption", "pre",
			"ruby", "rt" , "rb" , "rp"
		);
        $htmlsingle = array(
			"br", "p", "hr", "li", "dt", "dd"
		);
		$htmlnest = array( # Tags that can be nested--??
			"table", "tr", "td", "th", "div", "blockquote", "ol", "ul",
			"dl", "font", "big", "small", "sub", "sup"
		);
		$tabletags = array( # Can only appear inside table
			"td", "th", "tr"
		);

		$htmlsingle = array_merge( $tabletags, $htmlsingle );
		$htmlelements = array_merge( $htmlsingle, $htmlpairs );

        $htmlattrs = array( # Allowed attributes--no scripting, etc.
			"title", "align", "lang", "dir", "width", "height",
			"bgcolor", "clear", /* BR */ "noshade", /* HR */
			"cite", /* BLOCKQUOTE, Q */ "size", "face", "color",
			/* FONT */ "type", "start", "value", "compact",
			/* For various lists, mostly deprecated but safe */
			"summary", "width", "border", "frame", "rules",
			"cellspacing", "cellpadding", "valign", "char",
			"charoff", "colgroup", "col", "span", "abbr", "axis",
			"headers", "scope", "rowspan", "colspan", /* Tables */
			"id", "class", "name", "style" /* For CSS */
		);

		# Remove HTML comments
		$text = preg_replace( "/<!--.*-->/", "", $text );

		$bits = explode( "<", $text );
		$text = array_shift( $bits );
		$tagstack = array(); $tablestack = array();

		foreach ( $bits as $x ) {
			preg_match( "/^(\\/?)(\\w+)([^>]*)(\\/{0,1}>)([^<]*)$/",
			  $x, $regs );
			list( $qbar, $slash, $t, $params, $brace, $rest ) = $regs;

			$badtag = 0 ;
			if ( in_array( $t = strtolower( $t ), $htmlelements ) ) {
				# Check our stack
				if ( $slash ) {
					# Closing a tag...
					if ( ! in_array( $t, $htmlsingle ) &&
					  ( $ot = array_pop( $tagstack ) ) != $t ) {
						array_push( $tagstack, $ot );
						$badtag = 1;
					} else {
						if ( $t == "table" ) {
							$tagstack = array_pop( $tablestack );
						}
						$newparams = "";
					}
				} else {
					# Keep track for later
					if ( in_array( $t, $tabletags ) &&
					  ! in_array( "table", $tagstack ) ) {
						$badtag = 1;
					} else if ( in_array( $t, $tagstack ) &&
					  ! in_array ( $t , $htmlnest ) ) {
						$badtag = 1 ;
					} else if ( ! in_array( $t, $htmlsingle ) ) {
						if ( $t == "table" ) {
							array_push( $tablestack, $tagstack );
							$tagstack = array();
						}
						array_push( $tagstack, $t );
					}
					# Strip non-approved attributes from the tag
					$newparams = preg_replace(
					  "/(\\w+)(\\s*=\\s*([^\\s\">]+|\"[^\">]*\"))?/e",
					  "(in_array(strtolower(\"\$1\"),\$htmlattrs)?(\"\$1\".((\"x\$3\" != \"x\")?\"=\$3\":'')):'')",
					  $params);
				}
				if ( ! $badtag ) {
					$rest = str_replace( ">", "&gt;", $rest );
					$text .= "<$slash$t$newparams$brace$rest";
					continue;
				}
			}
			$text .= "&lt;" . str_replace( ">", "&gt;", $x);
        }
		# Close off any remaining tags
		while ( $t = array_pop( $tagstack ) ) {
			$text .= "</$t>\n";
			if ( $t == "table" ) { $tagstack = array_pop( $tablestack ); }
		}
		return $text;
	}

	/* private */ function autoNumberHeadings( $text )
	{
		global $wgUser;
		if ( 1 != $wgUser->getOption( "numberheadings" ) ) {
			return $text;
		}
		$j = 0;
		$n = -1;
		for ( $i = 0; $i < 9; ++$i ) {
			if ( stristr( $text, "<h$i>" ) != false ) {
				++$j;
				if ( $n == -1 ) $n = $i;
			}
		}
		if ( $j < 2 ) return $text;
		$i = $n;
		$v = array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
		$t = "";
		while ( count( spliti( "<h", $text, 2 ) ) == 2 ) {
			$a = spliti( "<h", $text, 2 );
			$j = substr( $a[1], 0, 1 );
			if ( strtolower( $j ) != "r" ) {
				$t .= $a[0] . "<h" . $j . ">";
				++$v[$j];
				$b = array();
				for ( $k = $i; $k <= $j; $k++ ) array_push( $b, $v[$k] );
				for ( $k = $j+1; $k < 9; $k++ ) $v[$k] = 0;
				$t .= implode( ".", $b ) . " ";
                $text = substr( $a[1] , 2 ) ;
			} else { # <HR> tag, not a heading!
				$t .= $a[0] . "<hr>";
				$text = substr( $a[1], 2 );
			}
		}
        return $t . $text;
	}

	/* private */ function magicISBN( $text )
	{
		$a = split( "ISBN ", " $text" );
		if ( count ( $a ) < 2 ) return $text;
		$text = substr( array_shift( $a ), 1);
        $valid = "0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ";

		foreach ( $a as $x ) {
			$isbn = $blank = "" ;
			while ( " " == $x{0} ) {
                $blank .= " ";
                $x = substr( $x, 1 );
			}
            while ( strstr( $valid, $x{0} ) != false ) {
				$isbn .= $x{0};
				$x = substr( $x, 1 );
			}
            $num = str_replace( "-", "", $isbn );
            $num = str_replace( " ", "", $num );

            if ( "" == $num ) {
				$text .= "ISBN $blank$x";
            } else {
				$text .= "<a href=\"http://www.pricescan.com/books/bookDetail.asp?isbn=$num\">ISBN $isbn</a> ";
				#$text .= "<a href=\"http://shop.barnesandnoble.com/bookSearch/isbnInquiry.asp?isbn=$num\">Barnes&amp;Noble</a> ";
				#$text .= "(<a href=\"http://www.amazon.com/exec/obidos/ISBN=$num\">Amazon</a>, ";
				#$text .= "<a href=\"http://www.pricescan.com/books/bookDetail.asp?isbn=$num\">Pricescan</a>)";
				$text .= $x ;
			}
		}
        return $text;
	}

	/* private */ function magicRFC( $text )
	{
		return $text;
	}

	/* private */ function headElement()
	{
		global $wgDocType, $wgUser;

		$ret = "<!DOCTYPE HTML PUBLIC \"$wgDocType\">\n";

		if ( "" == $this->mHTMLtitle ) {
			$this->mHTMLtitle = $this->mPagetitle;
		}
		$ret .= "<html><head><title>{$this->mHTMLtitle}</title>\n";
		foreach ( $this->mMetatags as $tag ) {
			if ( 0 == strcasecmp( "http:", substr( $tag[0], 0, 5 ) ) ) {
				$a = "http-equiv";
				$tag[0] = substr( $tag[0], 5 );
			} else {
				$a = "name";
			}
			$ret .= "<meta $a=\"{$tag[0]}\" content=\"{$tag[1]}\">\n";
		}
		$p = $this->mRobotpolicy;
		if ( "" == $p ) { $p = "index,follow"; }
		$ret .= "<meta name=\"robots\" content=\"$p\">\n";

		if ( count( $this->keywords ) > 0 ) {
			$ret .= "<meta name=\"keywords\" content=\"" .
			  implode( ",", $this->mKeywords ) . "\">\n";
		}
		foreach ( $this->mLinktags as $tag ) {
			$ret .= "<link ";
			if ( "" != $tag[0] ) { $ret .= "rel=\"{$tag[0]}\" "; }
			if ( "" != $tag[1] ) { $ret .= "rev=\"{$tag[1]}\" "; }
			$ret .= "href=\"{$tag[2]}\">\n";
		}
		$sk = $wgUser->getSkin();
		$ret .= $sk->getUserStyles();

		$ret .= "</head>\n";
		return $ret;
	}
}

?>
