<?
# See search.doc

class SearchEngine {
	/* private */ var $mUsertext, $mSearchterms;
	/* private */ var $mTitlecond, $mTextcond;

	function SearchEngine( $text )
	{
		$this->mUsertext = $text;
		$this->mSearchterms = array();
	}

	function showResults()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;
		global $wgServer, $wgScript;
		global $offset, $limit;

		$wgOut->setPageTitle( wfMsg( "searchresults" ) );
		$q = str_replace( "$1", $this->mUsertext,
		  wfMsg( "searchquery" ) );
		$wgOut->setSubtitle( $q );
		$wgOut->setArticleFlag( false );

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

		$conn = wfGetDB();
		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur " .
		  "WHERE {$this->mTitlecond} AND (cur_namespace=0) AND " .
		  "(cur_is_redirect = 0) LIMIT {$offset}, {$limit}";
		$res1 = wfQuery( $sql, $conn );

		$conn = wfGetDB();
		$sql = "SELECT cur_id,cur_namespace,cur_title," .
		  "cur_text FROM cur " .
		  "WHERE {$this->mTextcond} AND (cur_namespace=0) AND " .
		  "(cur_is_redirect = 0) LIMIT {$offset}, {$limit}";
		$res2 = wfQuery( $sql, $conn );

		$top = str_replace( "$1", $limit, wfMsg( "showingmatches" ) );
		$top = str_replace( "$2", $offset+1, $top );
		$wgOut->addHTML( "<p>{$top}\n" );

		$prev = str_replace( "$1", $limit, wfMsg( "searchprev" ) );
		$next = str_replace( "$1", $limit, wfMsg( "searchnext" ) );

		$sk = $wgUser->getSkin();
		if ( 0 != $offset ) {
			$po = $offset - $limit;
			$plink = "<a href=\"$wgServer$wgScript?search={$this->mUsertext}" .
			  "&amp;limit={$limit}&amp;offset={$po}\">{$prev}</a>";
		} else { $plink = $prev; }
		$no = $offset + $limit;
		$nlink = "<a href=\"$wgServer$wgScript?search={$this->mUsertext}" .
		  "&amp;limit={$limit}&amp;offset={$no}\">{$next}</a>";

		$sl = str_replace( "$1", $plink, wfMsg( "searchlinks" ) );
		$sl = str_replace( "$2", $nlink, $sl );
		$wgOut->addHTML( "<br>{$sl}\n" );

		if ( 0 == mysql_num_rows( $res1 ) ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "notitlematches" ) . "</h2>\n" );
		} else {
			$off = $offset + 1;
			$wgOut->addHTML( "<h2>" . wfMsg( "titlematches" ) . "</h2>\n" .
			  "<ol start='{$off}'>" );
			while ( $row = mysql_fetch_object( $res1 ) ) {
				$this->showHit( $row );
			}
			mysql_free_result( $res1 );
			$wgOut->addHTML( "</ol>\n" );
		}
		if ( 0 == mysql_num_rows( $res2 ) ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "notextmatches" ) . "</h2>\n" );
		} else {
			$off = $offset + 1;
			$wgOut->addHTML( "<h2>" . wfMsg( "textmatches" ) . "</h2>\n" .
			  "<ol start='{$off}'>" );
			while ( $row = mysql_fetch_object( $res2 ) ) {
				$this->showHit( $row );
			}
			mysql_free_result( $res2 );
			$wgOut->addHTML( "</ol>\n" );
		}
		$wgOut->addHTML( "<p>{$sl}\n" );
	}

	function parseQuery()
	{
		$q = preg_replace( "/[^-A-Za-z_0-9\\x90-\\xFF()]/", " ",
		  $this->mUsertext );
		$q = preg_replace( "/([()])/", " \\1 ", $q );
		$q = preg_replace( "/\\s+/", " ", $q );
		$w = explode( " ", strtolower( trim( $q ) ) );

		$last = $cond = "";
		foreach ( $w as $word ) {
			if ( "and" == $word || "or" == $word || "not" == $word
			  || "(" == $word || ")" == $word ) {
				$cond .= " {$word}";
				$last = "";
			} else {
				if ( "" != $last ) { $cond .= " and"; }
				$cond .= " (match (##field##) against ('{$word}'))";
				$last = $word;
				array_push( $this->mSearchterms, $word );
			}
		}
		$this->mTitlecond = "(" . str_replace( "##field##",
		  "cur_ind_title", $cond ) . " )";
		$this->mTextcond = "(" . str_replace( "##field##",
		  "cur_text", $cond ) . " )";
	}

	function showHit( $row )
	{
		global $wgUser, $wgOut;

		$t = Title::makeName( $row->cur_namespace, $row->cur_title );
		$sk = $wgUser->getSkin();

		$link = $sk->makeKnownLink( $t, "" );
		$wgOut->addHTML( "<li>{$link}" );

		$lines = explode( "\n", $row->cur_text );
		$words = "/(.*)(" . implode( "|", $this->mSearchterms ) . ")(.*)/i";
		$lineno = 0;

		foreach ( $lines as $line ) {
			++$lineno;
			if ( ! preg_match( $words, $line, $m ) ) { continue; }

			$pre = $m[1];
			if ( strlen( $pre ) > 60 ) {
				$pre = "..." . substr( $pre, -60 );
			}
			if ( count( $m ) < 3 ) { $post = ""; }
			else { $post = $m[3]; }
			if ( strlen( $post ) > 60 ) {
				$post = substr( $post, 0, 60 ) . "...";
			}
			$line = "{$pre}<font color='red'>{$m[2]}</font>{$post}";
			$line = wfEscapeHTML( $line );
			$wgOut->addHTML( "<br><small>{$lineno}: {$line}</small>\n" );
		}
		$wgOut->addHTML( "</li>\n" );
	}
}

