<?
# See search.doc

class SearchEngine {
	/* private */ var $mUsertext, $mSearchterms;
	/* private */ var $mTitlecond, $mTextcond;

        var $doSearchRedirects = true ;
        var $na = array ( 0 ) ;
        var $add2links = array () ;
        var $alternateTitle ;
        function queryNamespaces ()
                {
                return "cur_namespace=".implode ( " OR cur_namespace=" , $this->na ) ;
                }
        function searchRedirects ()
                {
                if ( $this->doSearchRedirects ) return "" ;
                return "AND cur_is_redirect=0 " ;
                }
        function powersearch ()
                {
                global $wgUser, $wgOut, $wgLang, $wgTitle;
                global $search, $wgNs, $wgLR , $wpSearch ;

                $r = wfMsg("powersearchtext") ;

                # Namespaces
                $ns = $wgLang->getNamespaces() ;
                $ns2 = $ns ;
                array_shift ( $ns2 ) ;
                $a = 0 ;
                $na = array () ;
                if ( !isset ( $wgNs ) ) $wgNs = array () ;
				$this->add2links["wpSearch"] = 1;
                if ( !isset ( $wpSearch ) )
                        {
                        $wgNs[0] = 1 ;
                        $wgLR = 1 ;
                        }
                foreach ( $ns2 AS $x )
                        {
                        if ( $a > 0 ) $r1 .= " " ;
                        $v = $wgNs[$a] ;
                        if ( $v )
                                {
                                $this->add2links["wgNs[$a]"] = 1 ;
                                $v = " checked" ;
                                $na[] = $a ;
                                }
                        $n = "wgNs[$a]" ;
                        $x = str_replace ( "_" , " " , $x ) ;
                        if ( $x == "" ) $x = ":" ;
                        $r1 .= "<input type=checkbox value=1 name='{$n}'{$v}>$x\n" ;
                        $a++ ;
                        }

                # List Redirects
                $v = "" ;
                if ( $wgLR )
                        {
                        $this->add2links["wgLR"] = 1 ;
                        $v = " checked" ;
                        }
                $r2 = "<input type=checkbox value=1 name='wgLR'{$v}>\n" ;

                # Search field
                $r3 = "<input type=text name=search value=\"" .
			htmlspecialchars( $search ) ."\" width=80>\n" ;

                # The search button
                $r9 = "<input type=submit name='wpSearch' value='".wfMsg("powersearch")."'>\n" ;

                $r = str_replace ( "$1" , $r1 , $r ) ;
                $r = str_replace ( "$2" , $r2 , $r ) ;
                $r = str_replace ( "$3" , $r3 , $r ) ;
                $r = str_replace ( "$9" , $r9 , $r ) ;

                $r = "<div style=\"background: #DDEEFF; border-style: solid; border-width: 1; padding: 2\">".
                        "\n{$r}\n</div>\n" ;
                $r = "<FORM method=post>\n{$r}</FORM>\n" ;

                if ( isset ( $wpSearch ) )
                        {
                        if ( count ( $na ) == 0 ) $na[] = 0 ;
                        $this->na = $na ;
                        if ( !$wgLR ) $this->doSearchRedirects = false ;
                        }

                return "\n<br><br>\n{$r}\n" ;
                }


	function SearchEngine( $text )
	{
		# We display the query, so let's strip it for safety
		#
		$lc = SearchEngine::legalSearchChars() . "()";
		$this->mUsertext = trim( preg_replace( "/[^{$lc}]/", " ", $text ) );
		$this->mSearchterms = array();
	}

	function showResults()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;
		global $offset, $limit;
		$fname = "SearchEngine::showResults";

                $powersearch = $this->powersearch() ;

		$wgOut->setPageTitle( wfMsg( "searchresults" ) );
		$q = str_replace( "$1", $this->mUsertext,
		  wfMsg( "searchquery" ) );
		$wgOut->setSubtitle( $q );
		$wgOut->setArticleFlag( false );
		$wgOut->setRobotpolicy( "noindex,nofollow" );

		$sk = $wgUser->getSkin();
		$text = str_replace( "$1", $sk->makeKnownLink(
		  wfMsg( "searchhelppage" ), wfMsg( "searchingwikipedia" ) ),
		  wfMsg( "searchresulttext" ) );
		$wgOut->addHTML( $text );

		$this->parseQuery();
		if ( "" == $this->mTitlecond || "" == $this->mTextcond ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "badquery" ) . "</h2>\n" .
			  "<p>" . wfMsg( "badquerytext" ) );
			return;
		}
		if ( ! isset( $limit ) ) {
			$limit = $wgUser->getOption( "searchlimit" );
			if ( ! $limit ) { $limit = 20; }
		}
		if ( ! $offset ) { $offset = 0; }

                $searchnamespaces = $this->queryNamespaces () ;
		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur,searchindex " .
		  "WHERE cur_id=si_page AND {$this->mTitlecond} AND ({$searchnamespaces}) " .
		  "LIMIT {$offset}, {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur,searchindex " .
		  "WHERE cur_id=si_page AND {$this->mTextcond} AND ({$searchnamespaces}) " .
		  "LIMIT {$offset}, {$limit}";
		$res2 = wfQuery( $sql, $fname );

		$top = wfShowingResults( $offset, $limit );
		$wgOut->addHTML( "<p>{$top}\n" );

		# For powersearch
                $a2l = "" ;
                $akk = array_keys ( $this->add2links ) ;
                foreach ( $akk AS $ak )
                        $a2l .= "&{$ak}={$this->add2links[$ak]}" ;

		$sl = wfViewPrevNext( $offset, $limit, "",
		  "search=" . wfUrlencode( $this->mUsertext ) . $a2l );
		$wgOut->addHTML( "<br>{$sl}\n" );

		$foundsome = false;
		if ( 0 == wfNumRows( $res1 ) ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "notitlematches" ) . "</h2>\n" );
		} else {
			$foundsome = true;
			$off = $offset + 1;
			$wgOut->addHTML( "<h2>" . wfMsg( "titlematches" ) . "</h2>\n" .
			  "<ol start='{$off}'>" );
			while ( $row = wfFetchObject( $res1 ) ) {
				$this->showHit( $row );
			}
			wfFreeResult( $res1 );
			$wgOut->addHTML( "</ol>\n" );
		}
		if ( 0 == wfNumRows( $res2 ) ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "notextmatches" ) . "</h2>\n" );
		} else {
			$foundsome = true;
			$off = $offset + 1;
			$wgOut->addHTML( "<h2>" . wfMsg( "textmatches" ) . "</h2>\n" .
			  "<ol start='{$off}'>" );
			while ( $row = wfFetchObject( $res2 ) ) {
				$this->showHit( $row );
			}
			wfFreeResult( $res2 );
			$wgOut->addHTML( "</ol>\n" );
		}
		if ( ! $foundsome ) {
			$wgOut->addHTML( "<p>" . wfMsg( "nonefound" ) . "\n" );
		}
		$wgOut->addHTML( "<p>{$sl}\n" );
                $wgOut->addHTML( $powersearch );
	}

	function legalSearchChars()
	{
		$lc = "A-Za-z_'0-9\\x80-\\xFF\\-";
		return $lc;
	}

	function parseQuery()
	{
		global $wgDBminWordLen, $wgLang;

		$lc = SearchEngine::legalSearchChars() . "()";
		$q = preg_replace( "/([()])/", " \\1 ", $this->mUsertext );
		$q = preg_replace( "/\\s+/", " ", $q );
		$w = explode( " ", strtolower( trim( $q ) ) );

		$last = $cond = "";
		foreach ( $w as $word ) {
			$word = $wgLang->stripForSearch( $word );
			if ( "and" == $word || "or" == $word || "not" == $word
			  || "(" == $word || ")" == $word ) {
				$cond .= " " . strtoupper( $word );
				$last = "";
			} else if ( strlen( $word ) < $wgDBminWordLen ) {
				continue;
			} else if ( FulltextStoplist::inList( $word ) ) {
				continue;
			} else {
				if ( "" != $last ) { $cond .= " AND"; }
				$cond .= " (MATCH (##field##) AGAINST ('" .
				  wfStrencode( $word ). "'))";
				$last = $word;
				array_push( $this->mSearchterms, "\\b" . $word . "\\b" );
			}
		}
		if ( 0 == count( $this->mSearchterms ) ) { return; }

		$this->mTitlecond = "(" . str_replace( "##field##",
		  "si_title", $cond ) . " )";

		$this->mTextcond = "(" . str_replace( "##field##",
		  "si_text", $cond ) . " AND (cur_is_redirect=0) )";
	}

	function showHit( $row )
	{
		global $wgUser, $wgOut;

		$t = Title::makeName( $row->cur_namespace, $row->cur_title );
		$sk = $wgUser->getSkin();

		$contextlines = $wgUser->getOption( "contextlines" );
		if ( "" == $contextlines ) { $contextlines = 5; }
		$contextchars = $wgUser->getOption( "contextchars" );
		if ( "" == $contextchars ) { $contextchars = 50; }

		$link = $sk->makeKnownLink( $t, "" );
		$size = str_replace( "$1", strlen( $row->cur_text ), WfMsg( "nbytes" ) );
		$wgOut->addHTML( "<li>{$link} ({$size})" );

		$lines = explode( "\n", $row->cur_text );
		$pat1 = "/(.*)(" . implode( "|", $this->mSearchterms ) . ")(.*)/i";
		$lineno = 0;

		foreach ( $lines as $line ) {
			if ( 0 == $contextlines ) { break; }
			--$contextlines;
			++$lineno;
			if ( ! preg_match( $pat1, $line, $m ) ) { continue; }

			$pre = $m[1];
			if ( 0 == $contextchars ) { $pre = "..."; }
			else {
				if ( strlen( $pre ) > $contextchars ) {
					$pre = "..." . substr( $pre, -$contextchars );
				}
			}
			$pre = wfEscapeHTML( $pre );

			if ( count( $m ) < 3 ) { $post = ""; }
			else { $post = $m[3]; }

			if ( 0 == $contextchars ) { $post = "..."; }
			else {
				if ( strlen( $post ) > $contextchars ) {
					$post = substr( $post, 0, $contextchars ) . "...";
				}
			}
			$post = wfEscapeHTML( $post );
			$found = wfEscapeHTML( $m[2] );

			$line = "{$pre}{$found}{$post}";
			$pat2 = "/(" . implode( "|", $this->mSearchterms ) . ")/i";
			$line = preg_replace( $pat2,
			  "<font color='red'>\\1</font>", $line );

			$wgOut->addHTML( "<br><small>{$lineno}: {$line}</small>\n" );
		}
		$wgOut->addHTML( "</li>\n" );
	}
	function goResult() {
	
	
		global $wgOut,$wgArticle,$wgTitle,$search;
		$fname = "SearchEngine::goResult";
		
		#first try to go to page as entered		
		$wgArticle=new Article();
		$wgTitle=Title::newFromText($search);
		if($wgArticle->getID()) {
			$wgArticle->view();
		} else {
					
			# now try all lower case (i.e. first letter capitalized)
			$wgTitle=Title::newFromText(strtolower($search));
			if($wgArticle->getID()) {
				$wgArticle->view();
			} else {
		
				# now try capitalized string
				$wgTitle=Title::newFromText(ucwords(strtolower($search)));
				if($wgArticle->getID()) {
					$wgArticle->view();
				} else {					
					
					
					# try a near match
					$this->parseQuery();										
					$sql = "SELECT cur_id,cur_title,cur_namespace,si_page FROM cur,searchindex " .
					  "WHERE cur_id=si_page AND {$this->mTitlecond} " .
					  "LIMIT 1";
					if($this->mTitlecond) {
						$res = wfQuery( $sql, $fname );
					} 				
					if (isset($res) && wfNumRows( $res )) {
					
						$s=wfFetchObject($res);
						$wgTitle=Title::newFromDBkey($s->cur_title);
						$wgTitle->setNamespace($s->cur_namespace);
						$wgArticle->view();
					
					# run a normal search
					} else {
					
						$wgOut->addHTML(wfMsg("nogomatch"));
						$this->showResults();
					}
					
				}
			}
		}
	}

}

