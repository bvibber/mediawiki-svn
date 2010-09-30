<?php
/**
 * Special:SessionStash
 *
 * Web access for files temporarily stored by SessionStash.
 *
 * For example -- files that were uploaded with the UploadWizard extension are stored temporarily 
 * before committing them to the db. But we want to see their thumbnails and get other information
 * about them.
 *
 * Since this is based on the user's session, in effect this creates a private temporary file area.
 * However, the URLs for the files cannot be shared.
 *
 * @file
 * @ingroup SpecialPage
 * @ingroup Upload
 */

class SpecialSessionStash extends SpecialPage {

	static $HttpErrors = array(
		400 => 'Bad Request',
		403 => 'Access Denied',
		404 => 'File not found',
		500 => 'Internal Server Error',
	);

	// SessionStash
	private $stash;

	// we should not be reading in really big files and serving them out
	private $maxServeFileSize = 262144; // 256K

	// $request is the request (usually wgRequest)
	// $subpage is everything in the URL after Special:SessionStash
	public function __construct( $request=null, $subpage=null ) {
		global $wgRequest;

                parent::__construct( 'SessionStash', 'upload' );

		$this->stash = new SessionStash();

	}

	/**
	 * If file available in stash, cats it out to the client as a simple HTTP response.
	 * n.b. Most sanity checking done in SessionStashLocalFile, so this is straightforward.
	 * 
	 * @param {String} subpage, e.g. in http://sample.com/wiki/Special:SessionStash/foo.jpg, the "foo".
  	 * @return {Boolean} success 
	 */
	public function execute( $subPage ) {
		global $wgOut;

		// prevent callers from doing standard HTML output -- we'll take it from here
		$wgOut->disable();

		// global $wgScriptPath, $wgLang, $wgUser, $wgOut;
		wfDebug( __METHOD__ . " in subpage for $subPage \n" );

		try { 
			$file = $this->getStashFile( $subPage );
			if ( $file->getSize() > $this->maxServeFileSize ) {
				throw new MWException( 'file size too large' );
			}
			$this->outputFile( $file );
			return true;

		} catch( SessionStashFileNotFoundException $e ) {
			wfHttpError( 404, self::$HttpErrors[404], $e->getCode(), $e->getMessage() );

		} catch( SessionStashBadPathException $e ) {
			wfHttpError( 403, self::$HttpErrors[403], $e->getCode(), $e->getMessage() );

		} catch( Exception $e ) {
			wfHttpError( $code, self::$HttpErrors[$code], $e->getCode(), $e->getMessage() );

		}
			
		return false;
	}


	/** 
	 * Convert the incoming url portion (subpage of Special page) into a stashed file, if available.
	 * @param {String} $subPage 
	 * @return {File} file object
	 * @throws MWException, SessionStashFileNotFoundException, SessionStashBadPathException
	 */
	private function getStashFile( $subPage ) {
		// due to an implementation quirk (and trying to be compatible with older method) 
		// the stash key doesn't have an extension 
		$key = $subPage;
		$n = strrpos( $subPage, '.' );
                if ( $n !== false ) {
                        $key = $n ? substr( $subPage, 0, $n ) : $subPage;
		}
		
		$file = $this->stash->getFile( $key );
		return $file;
	}

	/**
	 * Output HTTP response for file
	 * Side effects, obviously, of echoing lots of stuff to stdout.
	 * @param {File} file
	 */		
	private function outputFile( $file ) { 
		header( 'Content-Type: ' . $file->getMimeType() );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: Sun, 17-Jan-2038 19:14:07 GMT' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $file->getSize() );
		readfile( $file->getPath() );
	}
}

