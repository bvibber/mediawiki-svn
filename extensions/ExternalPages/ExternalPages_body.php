<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "ExternalPages extension\n";
	exit( 1 );
}

/**
 * Special page allows retrieval and display of pages from remote WMF sites
 * with year, lang and project specifable
 */
class ExternalPages extends SpecialPage {

	private $mYear = '';
	private $mLang = '';
	private $mProject = '';
	private $mPage = false;
	private $mPageURL = '';
	private $mPageText = false;
	private $mFromCache = false;

	// adjust these as needed to change cache expiry 
	const EP_SMAXAGE = 600;
	const EP_MAXAGE = 600;
	const EP_MEMCACHE_EXP = 600;

	public function __construct() {
		parent::__construct( 'ExternalPages' );
		wfLoadExtensionMessages( 'ExternalPages' );
	}

	/**
	 * Entry point (retrieve parsed page, convert rel links to full
	 * URLs that direct to the remote site
	 * $par would be the subpage. we don't need it 
	 */
	public function execute( $par ) {
		global $wgUser, $wgRequest;

		wfLoadExtensionMessages( 'ExternalPages' );
		$this->setHeaders();

		if ( !$this->parseParams() ) {
			return( false );
		}
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return( false );
		}

		$this->constructURL();
		$this->retrieveExternalPage();
	}

	/**
	 * Process parameters of the request
	 */
	private function parseParams() {
		global $wgRequest, $wgServer;

		if ( !$wgRequest->getVal( 'EPyear' ) ) {
			$this->mYear = false;
		} else {
			$this->mYear = $wgRequest->getInt( 'EPyear' );
			// if this code is still being used 50 years from now, replace it :-P
			if (! ( ( $this->mYear > 2000 ) && ( $this->mYear < 2050 ) ) ) {
				ExternalPagesErrors::showError( 'externalpages-bad-year' );
				return( false );
			}
		}

		if ( !$wgRequest->getVal( 'EPlanguage' ) ) {
			$this->mLang = false;
		} else {
			$this->mLang = $wgRequest->getVal( 'EPlanguage' );
			$knownLanguages = Language::getLanguageNames( false );
			if ( !array_key_exists( $this->mLang, $knownLanguages ) ) {
				ExternalPagesErrors::showError( 'externalpages-bad-language' );
				return( false );
			}
		}

		if ( !$wgRequest->getVal( 'EPproject' ) ) {
			ExternalPagesErrors::showError( 'externalpages-no-project' );
			return( false );
		} else {
			$this->mProject = $wgRequest->getVal( 'EPproject' );
			// for initial fundraiser rollout, just allow pages from one project.
			// This can be generalized later
			if ( 'wikimediafoundation.org' != $this->mProject ) {
				ExternalPagesErrors::showError( 'externalpages-bad-project' );
				return( false );
			}
		}

		if ( !$wgRequest->getVal( 'EPpage' ) ) {
			ExternalPagesErrors::showError( 'externalpages-no-page' );
			return( false );
		}
		$this->mPage = $wgRequest->getVal( 'EPpage' );
		// strictly speaking this may behave differently on the local wiki, oh well
		if ( !Title::newFromText( $this->mPage ) ) {
			ExternalPagesErrors::showError( 'externalpages-bad-page' );
			return( false );
		}
		return( true );
	}

	private function constructURL() {
		$url = 'http://' . $this->mProject . '/w/api.php?action=parse&page=';
		$title = ( $this->mYear ? $this->mYear . '/' : '' ) . $this->mPage;
		$title .=  $this->mLang ? '/' . $this->mLang : '';
		$title = urlencode( $title );
		$url = $url . $title . '&format=php';
		$this->mPageURL = $url;
	}

	public function cacheHeaders() {
		global $wgRequest;

		$smaxage = self::EP_SMAXAGE;
		$maxage = self::EP_MAXAGE;

		$public = ( session_id() == '' );

		if ( $public ) {
			$wgRequest->response()->header( "Cache-Control: public, s-maxage=$smaxage, max-age=$maxage" );
		} else {
			$wgRequest->response()->header( "Cache-Control: private, s-maxage=0, max-age=$maxage" );
		}
		$time = time() + self::EP_MAXAGE;
		$wgRequest->response()->header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', $time ) . ' GMT' );
		return( true );
	}

	private function getCacheKey( $string ) {
		return( wfMemcKey( 'externalpages', $string ) );
	}

	private function getPageFromCache() {
		global $wgMemc;

		wfProfileIn( __METHOD__ );

		if ( !$this->mPageURL ) {
			$this->constructURL();
		}

		$this->mPageText = $wgMemc->get( $this->getCacheKey( $this->mPageURL ) );
		if ( !$this->mPageText ) {
			wfDebugLog( 'ExternalPages', "Remote Page Text: cache miss for {$this->mPageURL} " );
			wfProfileOut( __METHOD__ );
			return( false );
		}
		wfProfileOut( __METHOD__ );
		return( true );
	}

	private function savePageToCache() {
		global $wgMemc;

		wfDebugLog( 'ExternalPages', "Saving text {$this->mPageURL} to cache." );
		$wgMemc->set( $this->getCacheKey( $this->mPageURL ), $this->mPageText, self::EP_MEMCACHE_EXP );
	}

	private function retrieveExternalPage() {
		global $wgOut, $wgRequest, $wgHooks;

		if ( !$this->mPageURL ) {
			$this->constructURL();
		}

		// try from cache first
		$this->getPageFromCache();

		if ( !$this->mPageText ) {
			$serializedText = Http::get( $this->mPageURL );

			if ( empty( $serializedText ) )  {
				ExternalPagesErrors::showError( 'externalpages-bad-url' );
				return( false );
			} else {
				$text = unserialize( $serializedText );
			}

			if ( isset( $text['parse'] ) && ( isset( $text['parse']['text'] ) ) ) {
				$this->mPageText = $text['parse']['text']['*'];
				$absurl = '<a href="http://' . $this->mProject . '/';
				$this->mPageText = str_replace( '<a href="/', $absurl, $this->mPageText );
			}
			$this->savePageToCache();
		} else {
			wfDebugLog( 'ExternalPages', "Retrieved {$this->mPageURL} from cache." );
		}

		if ( $this->mPageText ) {
			$wgHooks['CacheHeadersAfterSet'][] = array( $this, 'cacheHeaders' );
			$wgOut->addHTML( $this->mPageText );
		} else {
			ExternalPagesErrors::showError( 'externalpages-bad-url-data' );
			return( false );
		}
		return;
	}
}

/**
 * Error handler for some formatting of error messages 
 */
class ExternalPagesErrors {

	static function showError( $errorText = 'externalpages-error-generic', $phpErrorText = false ) {
		global $wgOut;

		$args = func_get_args();

		array_shift( $args );
		$msg = wfMsg( $errorText, $args );

		$wgOut->addWikiText(
			'<div class="errorbox" style="float:none;">' .
			$msg .
			'</div>'
		);
	}

}