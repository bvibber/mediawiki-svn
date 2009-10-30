<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "ExternalPages extension\n";
	exit( 1 );
}

$base = dirname( __FILE__ );

/**
 * Special page allows retrieval and display of pages from remote WMF sites
 * with year, lang and project specifable
 */
class ExternalPages extends SpecialPage {

    private $mYear = '';
    private $mLang = '';
    private $mProject = '';
    private $mPage = false;
	
    function __construct() {
		SpecialPage::SpecialPage( 'ExternalPages' );
		wfLoadExtensionMessages( 'ExternalPages' );
    }
	
	/*
	 * entry point (retrieve parsed page, convert rel links to full
	 * urls that direct to the remote site
	 * $par would be the subpage. we don't need it 
	 */
	function execute( $par ) {
		global $wgUser, $wgRequest;
		
		wfLoadExtensionMessages( 'ExternalPages' );
		$this->setHeaders();
		if ( ! $this->parseParams() ) {
			return(false);
		}
		if ( ! $this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return(false);
		}

		$this->retrieveExternalPage( $this->constructURL() );
		
	}

	/* 
	 * process parameters of the request
	 */
	private function parseParams() {
		global $wgRequest, $wgServer;

		if (!$wgRequest->getVal( 'EPyear') ) {
			$this->mYear=false;
		}
		else {
			$this->mYear = $wgRequest->getInt( 'EPyear' );
			// if this code is still being used 50 years from now, replace it :-P
			if (! (( $this->mYear > 2000 ) && ( $this->mYear < 2050 ))) { 
				ExternalPagesErrors::showError( 'externalpages-bad-year' );
				return(false);
			}
		}
		
		if ( !$wgRequest->getVal( 'EPlanguage' ) ) {
			$this->mLang=false;
		}
		else {
			$this->mLang = $wgRequest->getVal( 'EPlanguage' );
			$knownLanguages = Language::getLanguageNames( false );
			if ( !array_key_exists( $code, $knownLanguages ) ) {
				ExternalPagesErrors::showError( 'externalpages-bad-language' );
				return(false);
			}
		}

		if ( !$wgRequest->getVal( 'EPproject' ) ) {
			ExternalPagesErrors::showError( 'externalpages-no-project' );
			return(false);
		}
		else {
			$this->mProject = $wgRequest->getVal( 'EPproject' );
			// for initial fundraiser rollout, just allow pages from one project. this
			// can be generalized later
			if ( 'wikimediafoundation.org' != $this->mProject ) {
				ExternalPagesErrors::showError( 'externalpages-bad-project' );
				return(false);
			}
		}

		if ( !$wgRequest->getVal( 'EPpage' ) ) {
			ExternalPagesErrors::showError( 'externalpages-no-page' );
			return(false);
		}
		$this->mPage = $wgRequest->getVal( 'EPpage' );
		return(true);
	}

	private function constructURL() {
		$url = "http://" . $this->mProject . "/w/api.php?action=parse&page=";
		$title = ( $this->mYear ? $this->mYear."/" : "" ) . $this->mPage;
		$title .=  $this->mLang ? "/".$this->mLang : "" ;
		$title = urlencode( $title );
		$url = $url . $title . '&format=xml';
		return( $url );
	}

	private function retrieveExternalPage( $url ) {
		global $wgOut, $wgRequest;

		$url_text = @file_get_contents( $url );
		if ( empty( $url_text ) )  {
			ExternalPagesErrors::showError( 'externalpages-bad-url' );
			return(false);
		}
		else {
			if ( preg_match('/<text[^>]*>([^<]*)<\/text>/',$url_text,$matches) ) {
				$text = $matches[1];
				$text = html_entity_decode( $text );
				$absurl = '<a href="http://'.$this->mProject."/";
				$text = str_replace( '<a href="/', $absurl, $text );
				$wgOut->addHTML( $text );
			}
			else {
				ExternalPagesErrors::showError( 'externalpages-bad-url-data' );
				return(false);
			}
		}
		return;
	}
}

/*
 * error handler for some formatting of error messages 
 */
class ExternalPagesErrors {

	static function showError( $errorText='externalpages-error-generic', $phpErrorText=false ) {
		global $wgOut;
		
		$args = func_get_args();
		
		array_shift( $args );
		$msg =  wfMsg( $errorText, $args );
		
		$wgOut->addWikiText( "<div class=\"errorbox\" style=\"float:none;\">" .
							 $msg .
							 "</div>" );
	}

}

?>
