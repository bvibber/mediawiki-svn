<?
# See search.doc

class SearchEngine {
	/* private */ var $mUsertext, $mSearchterms;
	/* private */ var $mTitlecond, $mTextcond;

	function SearchEngine( $text )
	{
		# We display the query, so let's strip it for safety
		#
		$lc = SearchEngine::legalSearchChars() . "()";
		$this->mUsertext = preg_replace( "/[^{$lc}]/", " ", $text );
		$this->mSearchterms = array();
	}

	function showResults()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;
		global $offset, $limit;
		$fname = "SearchEngine::showResults";

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

		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur " .
		  "WHERE {$this->mTitlecond} AND (cur_namespace=0) " .
		  "LIMIT {$offset}, {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur " .
		  "WHERE {$this->mTextcond} AND (cur_namespace=0) " .
		  "LIMIT {$offset}, {$limit}";
		$res2 = wfQuery( $sql, $fname );

		$top = SearchEngine::showingResults( $offset, $limit );
		$wgOut->addHTML( "<p>{$top}\n" );

		$sl = SearchEngine::viewPrevNext( $offset, $limit, "",
		  "search={$this->mUsertext}" );
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
	}

	function legalSearchChars()
	{
		$lc = "A-Za-z_'0-9\\x90-\\xFF\\-";
		return $lc;
	}

	function showingResults( $offset, $limit )
	{
		$top = str_replace( "$1", $limit, wfMsg( "showingresults" ) );
		$top = str_replace( "$2", $offset+1, $top );
		return $top;
	}

	function viewPrevNext( $offset, $limit, $link, $query = "" )
	{
		global $wgUser;
		$prev = str_replace( "$1", $limit, wfMsg( "prevn" ) );
		$next = str_replace( "$1", $limit, wfMsg( "nextn" ) );

		$sk = $wgUser->getSkin();
		if ( 0 != $offset ) {
			$po = $offset - $limit;
			if ( $po < 0 ) { $po = 0; }
			$q = "limit={$limit}&offset={$po}";
			if ( "" != $query ) { $q .= "&{$query}"; }
			$plink = "<a href=\"" . wfLocalUrlE( $link, $q ) . "\">{$prev}</a>";
		} else { $plink = $prev; }

		$no = $offset + $limit;
		$q = "limit={$limit}&offset={$no}";
		if ( "" != $query ) { $q .= "&{$query}"; }

		$nlink = "<a href=\"" . wfLocalUrlE( $link, $q ) . "\">{$next}</a>";
		$nums = SearchEngine::numLink( $offset, 20, $link ) . " | " .
		  SearchEngine::numLink( $offset, 50, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 100, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 250, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 500, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 1000, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 2500, $link, $query ) . " | " .
		  SearchEngine::numLink( $offset, 5000, $link, $query );

		$sl = str_replace( "$1", $plink, wfMsg( "viewprevnext" ) );
		$sl = str_replace( "$2", $nlink, $sl );
		$sl = str_replace( "$3", $nums, $sl );
		return $sl;
	}

	function numLink( $offset, $limit, $link, $query = "" )
	{
		global $wgUser;
		if ( "" == $query ) { $q = ""; }
		else { $q = "{$query}&"; }
		$q .= "limit={$limit}&offset={$offset}";

		$s = "<a href=\"" . wfLocalUrlE( $link, $q ) . "\">{$limit}</a>";
		return $s;
	}

	function parseQuery()
	{
		global $wgDBminWordLen;

		$lc = SearchEngine::legalSearchChars() . "()";
		$q = preg_replace( "/([()])/", " \\1 ", $this->mUsertext );
		$q = preg_replace( "/\\s+/", " ", $q );
		$w = explode( " ", strtolower( trim( $q ) ) );

		$last = $cond = "";
		foreach ( $w as $word ) {
			if ( "and" == $word || "or" == $word || "not" == $word
			  || "(" == $word || ")" == $word ) {
				$cond .= " " . strtoupper( $word );
				$last = "";
			} else if ( strlen( $word ) < $wgDBminWordLen ) {
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
		  "cur_ind_title", $cond ) . " )";

		$this->mTextcond = "(" . str_replace( "##field##",
		  "cur_ind_text", $cond ) . " AND (cur_is_redirect=0) )";
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
		$wgOut->addHTML( "<li>{$link}" );

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
}

