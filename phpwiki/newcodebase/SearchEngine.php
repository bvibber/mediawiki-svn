<?
# See search.doc

class SearchEngine {
	/* private */ var $mUsertext;

	function SearchEngine( $text )
	{
		$this->mUsertext = $text;
	}

	function showResults()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;

		$wgOut->setPageTitle( wfMsg( "searchresults" ) );
		$q = str_replace( "$1", $this->mUsertext,
		  wfMsg( "searchquery" ) );
		$wgOut->setSubtitle( $q );
		$wgOut->setArticleFlag( false );

		$sql = $this->parseQuery();
		if ( "" == $sql ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "badquery" ) . "</h2>\n" .
			  "<p>" . wfMsg( "badquerytext" ) );
			return;
		}

		$wgOut->addHTML( "<h2>TODO: Search</h2>\n" );
	}
}

