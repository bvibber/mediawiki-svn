<?
# See deferred.doc

class SearchUpdate {

	/* private */ var $mId, $mTitle, $mText;
	/* private */ var $mTitleWords, $mTextWords;

	function SearchUpdate( $id, $title, $text )
	{
		$this->mId = $id;
		$this->mText = $text;

		$nt = Title::newFromText( $title );
		$this->mTitle = $nt->getText(); # Discard namespace

		$this->mTitleWords = $this->mTextWords = array();
	}

	function doUpdate()
	{
		$lc = SearchEngine::legalSearchChars() . "&#;";
		$t = preg_replace( "/[^{$lc}]+/", " ", $this->mTitle );
		$t = preg_replace( "/\\b[{$lc}][{$lc}]\\b/", " ", $t );
		$t = preg_replace( "/\\b[{$lc}]\\b/", " ", $t );
		$t = preg_replace( "/\\s+/", " ", $t );

		$words = explode( " ", strtolower( trim( $t ) ) );
		foreach ( $words as $w ) { $this->mTitleWords[$w] = 1; }

		$text = preg_replace( "/<\\/?\\s*[A-Za-z][A-Za-z0-9]*\\s*([^>]*?)>/",
		  " ", strtolower( $this->mText ) ); # Strip HTML markup
		$text = preg_replace( "/([^{$lc}])([{$lc}]+)]]([a-z]+)/",
		  "\\1\\2 \\2\\3", $text ); # Handle [[game]]s

		$text = preg_replace( "/[^{$lc}]+/", " ", $text );
		$text = preg_replace( "/([{$lc}]+)'s /", "\\1 \\1's ", $text );
		$text = preg_replace( "/([{$lc}]+)s' /", "\\1s ", $text );

		# Need to strip external links?

		$text = preg_replace( "/(^|[^{$lc}])[{$lc}][{$lc}]([^{$lc}]|$)/",
		  "\\1 \\2", $text );
		$text = preg_replace( "/(^|[^{$lc}])[{$lc}]([^{$lc}]|$)/",
		  "\\1 \\2", $text );
		$text = preg_replace( "/''[']*/", " ", $text );

		foreach ( $this->mTitleWords as $w => $val ) {
			$text = str_replace( $w, " ", $text );
		}
		$text = preg_replace( "/\\s+/", " ", $text );
		$words = explode( " ", trim( $text ) );

		foreach ( $words as $w ) {
			$this->mTextWords[$w] = 1;
		}
		$text = implode( " ", array_keys( $this->mTextWords ) );

		$conn = wfGetDB();
		$sql = "UPDATE cur SET cur_timestamp=cur_timestamp,cur_ind_text='" .
		  wfStrencode( $text ) . "' WHERE cur_id={$this->mId}";
		wfQuery( $sql, $conn, "SearchUpdate::doUpdate" );
	}
}

?>
