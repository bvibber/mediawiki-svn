<?

# ISBNs in wiki pages will create links to this page, with
# the ISBN passed in via the query string.

function wfSpecialBooksources()
{
	$isbn = $_REQUEST["isbn"];

	$bsl = new BookSourceList( $isbn );
	$bsl->show();
}

class BookSourceList {

	var $mIsbn;

	function BookSourceList( $isbn )
	{
		$this->mIsbn = $isbn;
	}

	function show()
	{
		global $wgOut, $wgUser, $wgLang;
		global $ip, $wpBlockAddress, $wpBlockReason;

		$wgOut->setPagetitle( wfMsg( "booksources" ) );
		$wgOut->addWikiText( wfMsg( "booksourcetext" ) );

		# If ISBN is blank, just show a list of links to the
		# home page of the various book sites.  Otherwise, show
		# a list of links directly to the book.

		$s = "<ul>\n";
		if ( ! $this->mIsbn ) {
			$s .= "<li><a href=\"http://www.addall.com/\">" .
			  "AddALL</a></li>\n";
			$s .= "<li><a href=\"http://www.pricescan.com/\">" .
			  "PriceSCAN</a></li>\n";
			$s .= "<li><a href=\"http://www.barnesandnoble.com/\">" .
			  "Barnes &amp; Noble</a></li>\n";
			$s .= "<li><a href=\"http://www.amazon.com/\">" .
			  "Amazon.com</a></li>\n";
		} else {
			$s .= "<li><a href=\"http://www.addall.com/New/Partner.cgi?" .
			  "query={$this->mIsbn}&amp;type=ISBN\">AddALL</a></li>\n";
			$s .= "<li><a href=\"http://www.pricescan.com/books/bookDetail" .
			  ".asp?isbn={$this->mIsbn}\">PriceSCAN</a></li>\n";
			$s .= "<li><a href=\"http://shop.barnesandnoble.com/bookSearch" .
			  "/isbnInquiry.asp?isbn={$this->mIsbn}\">Barnes &amp; Noble" .
			  "</a></li>\n";
			$s .= "<li><a href=\"http://www.amazon.com/exec/obidos/ISBN=" .
			  "{$this->mIsbn}\">Amazon.com</a></li>\n";
		}
		$s .= "</ul>\n";

		$wgOut->addHTML( $s );
	}
}

?>
