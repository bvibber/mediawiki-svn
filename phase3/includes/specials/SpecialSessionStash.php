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
	public function __construct( $request = null, $subpage = null ) {
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

		try { 
			$file = $this->getStashFile( $subPage );
			if ( $file->getSize() > $this->maxServeFileSize ) {
				throw new MWException( 'file size too large' );
			}
			$this->outputFile( $file );
			return true;

		} catch( SessionStashFileNotFoundException $e ) {
			$code = 404;
		} catch( SessionStashBadPathException $e ) {
			$code = 403;
		} catch( Exception $e ) {
			$code = 500;
		}
			
		wfHttpError( $code, self::$HttpErrors[$code], $e->getCode(), $e->getMessage() );
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
		
		try {
			$file = $this->stash->getFile( $key );
		} catch ( SessionStashFileNotFoundException $e ) { 
			// if we couldn't find it, and it looks like a thumbnail,
			// and it looks like we have the original, go ahead and generate it
			$matches = array();
			if ( ! preg_match( '/^(\d+)px-(\S+)$/', $key, $matches ) ) {
				// that doesn't look like a thumbnail. re-raise exception 
				throw $e;
			}

			$width = $matches[1];
			$origKey = $matches[2];

			// do not trap exceptions, if not found let exceptions propagate to caller.
			$origFile = $this->stash->getFile( $origKey );

			// ok we're here so the original must exist. Generate the thumbnail. 
			// because the file is a SessionStashFile, this thumbnail will also be stashed,
			// and a thumbnailFile will be created in the thumbnailImage composite object
			$thumbnailImage = null;
			if ( ! $thumbnailImage = $origFile->getThumbnail( $width ) ) { 
				throw new MWException( 'Could not obtain thumbnail' );
			}
			$file = $thumbnailImage->thumbnailFile;
		}
 
		return $file;
	}

	/**
	 * Output HTTP response for file
	 * Side effects, obviously, of echoing lots of stuff to stdout.
	 * @param {File} file
	 */		
	private function outputFile( $file ) { 
		header( 'Content-Type: ' . $file->getMimeType(), true );
		header( 'Content-Transfer-Encoding: binary', true );
		header( 'Expires: Sun, 17-Jan-2038 19:14:07 GMT', true );
		header( 'Pragma: public', true );
		header( 'Content-Length: ' . $file->getSize(), true );
		readfile( $file->getPath() );
	}
}

