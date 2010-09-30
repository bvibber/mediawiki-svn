<?php
/** 
 * SessionStash is intended to accomplish a few things:
 *   - enable applications to temporarily stash files without publishing them to the wiki.
 *      - Several parts of MediaWiki do this in similar ways: UploadBase, UploadWizard, and FirefoggChunkedExtension
 *        And there are several that reimplement stashing from scratch, in idiosyncratic ways. The idea is to unify them all here.
 *	  Mostly all of them are the same except for storing some custom fields, which we subsume into the data array.
 *   - enable applications to find said files later, as long as the session or temp files haven't been purged. 
 *   - enable the uploading user (and *ONLY* the uploading user) to access said files, and thumbnails of said files, via a URL.
 *     We accomplish this by making the session serve as a URL->file mapping, on the assumption that nobody else can access 
 *     the session, even the uploading user. See SpecialSessionStash, which implements a web interface to some files stored this way.
 *
 */

class SessionStash {
	// repository that this uses to store temp files
	protected $repo; 
	
	// array of initialized objects obtained from session (lazily initialized upon getFile())
	private $files = array();  

	// the base URL for files in the stash
	private $baseUrl;
	
	// TODO: Once UploadBase starts using this, switch to use these constants rather than UploadBase::SESSION*
	// const SESSION_VERSION = 2;
	// const SESSION_KEYNAME = 'wsUploadData';

	/**
	 * Represents the session which contain temporarily stored files.
	 * Designed to be compatible with the session stashing code in UploadBase (should replace eventually)
	 * @param {FileRepo} optional -- repo in which to store files. Will choose LocalRepo if not supplied.
	 */
	public function __construct( $repo=null ) { 

		if ( is_null( $repo ) ) {
			$repo = RepoGroup::singleton()->getLocalRepo();
		}

		// sanity check repo. If we want to mock the repo later this should be modified.
		if ( ! is_dir( $repo->getZonePath( 'temp' ) ) ) {
			throw new MWException( 'invalid repo or cannot read repo temp dir' );
		}
		$this->repo = $repo;

		if ( ! isset( $_SESSION ) ) {
			throw new MWException( 'session not available' );
		}

		if ( ! array_key_exists( UploadBase::SESSION_KEYNAME, $_SESSION ) ) {
			$_SESSION[UploadBase::SESSION_KEYNAME] = array();
		}
		
		$this->baseUrl = SpecialPage::getTitleFor( 'SessionStash' )->getLocalURL(); 
	}

	/**
	 * Get the base of URLs by which one can access the files 
	 * @return {String} url
	 */
	public function getBaseUrl() { 
		return $this->baseUrl;
	}

	/** 
	 * Get a file from the stash.
	 * May throw exception if session data cannot be parsed due to schema change.
	 * @param {Integer} key
	 * @return {null|SessionStashItem} null if no such item or item out of date, or the item
	 */
	public function getFile( $key ) { 
		if ( !isset( $this->files[$key] ) ) {
			wfDebug( "checking key = <$key> is in session\n" );
			if ( !isset( $_SESSION[UploadBase::SESSION_KEYNAME][$key] ) ) {
				wfDebug( "checking key = <$key> is in session - it isn't\n" );
				wfDebug( print_r( $_SESSION[UploadBase::SESSION_KEYNAME], 1 ) );
				throw new SessionStashFileNotFoundException();
			}

			$stashData = $_SESSION[UploadBase::SESSION_KEYNAME][$key];
	
			// guards against PHP class changing while session data doesn't
			if ($stashData['version'] !== UploadBase::SESSION_VERSION ) {
				return self::$error['outdated session version'];
			}
			
			// The path is flattened in with the other random props so we have to dig it out.
			$data = array();
			foreach( $stashData as $stashKey => $stashVal ) {
				if ( $stashKey === 'mTempPath' ) {
					$path = $stashVal;
				} else {
					$data[ $stashKey ] = $stashVal;
				}
			} 

			$file = new SessionStashFile( $this, $this->repo, $path, $key, $data );
			$this->files[$key] = $file;

		}
		return $this->files[$key];
	}

	/**
	 * Stash a file in a temp directory and record that we did this in the session, along with other parameters.
	 * @param {String} name - this is used for directory hashing when storing. Otherwise not important
	 * @param {String} path - path to file you want stashed
	 * @param {Array} data - other data you want added to the session. Do not use 'mTempPath', 'mFileProps', 'mFileSize', or version as keys here
	 * @return {SessionStashFile} file
	 */
	public function stashFile( $key, $path, $data=array() ) {
		if ( !$key ) {
			$key = mt_rand( 0, 0x7fffffff );
		}

		// if not already in a temporary area, put it there 
		$status = $this->repo->storeTemp( basename($path), $path );
		if( !$status->isOK() ) {
			return false;
		}
		$stashPath = $status->value;

                
                // get props
                $fileProps = File::getPropsFromPath( $path );
		$fileSize = $fileProps['size'];
		 		
		// standard info we always store.
		// 'mTempPath', 'mFileSize', and 'mFileProps' are arbitrary names
		// chosen for compatibility with UploadBase's way of doing this.
		$stashData = array( 
			'mTempPath' => $stashPath,
			'mFileSize' => $fileSize,
			'mFileProps' =>	$fileProps,
			'version' => UploadBase::SESSION_VERSION
		);

		// put extended info into the session (this changes from application to application).
		// UploadWizard wants different things than say FirefoggChunkedUpload.
		foreach ($data as $stashKey => $stashValue) {
			if ( !array_key_exists( $stashKey, $data ) ) {
				$stashData[$stashKey] = $stashValue;
			}
		}

		$_SESSION[UploadBase::SESSION_KEYNAME][$key] = $stashData;
		
		//wfDebug( "SESSION\n=====\n " . print_r( $_SESSION, 1 ) . "\n" );
		
		return $this->getFile( $key );
	}
}

class SessionStashFile extends UnregisteredLocalFile {
	public $sessionStash;
	public $sessionKey;
	public $sessionData;
	private $urlName;

	/**
	 * A LocalFile wrapper around a file that has been temporarily stashed, so we can do things like create thumbnails for it
	 * Arguably UnregisteredLocalFile should be handling its own file repo but that class is a bit retarded currently
	 * @param {FileRepo} repository where we should find the path
	 * @param {String} path to file
	 */
	public function __construct( $stash, $repo, $path, $key, $data ) {
		$this->sessionStash = $stash;
		$this->sessionKey = $key;
		$this->sessionData = $data;
		
		// resolve mwrepo:// urls
		if ( $repo->isVirtualUrl( $path ) ) {
			$path = $repo->resolveVirtualUrl( $path );	
		}

		// check if path appears to be sane, no parent traverals, and is in this repo's temp zone.
		if ( ( ! $repo->validateFilename( $path ) ) || 
			( strpos( $path, $repo->getZonePath( 'temp' ) ) !== 0 ) ) {
			throw new SessionStashBadPathException();
		}

		wfDebug( "checking if path exists and is good: $path " );
		// check if path exists! and is a plain file.
		if ( ! $repo->fileExists( $path, $repo::FILES_ONLY ) ) {
			wfDebug( "checking if path exists and is good: $path  -- no!! " );
			throw new SessionStashFileNotFoundException();
		}

		parent::__construct( false, $repo, $path, false );

		// we will be initializing from some tmpnam files that don't have extensions.
		// most of MediaWiki assumes all uploaded files have good extensions. So, we fix this.
		$this->name = basename( $this->path );
		$this->setExtension();

	}

	/**
	 * Test if a path looks like it's in the right place
	 *
	 * @param {String} $path 
	 * @return {Boolean}
	 */
	public function isPathValid( $path ) {

                if ( strval( $filename ) == '' ) { 
                        return false; 
                } 

                /** 
	 	 * Lifted this bit from extensions/WebStore::validateFilename.
                 * Use the same traversal protection as Title::secureAndSplit() 
                 */ 
                if ( strpos( $filename, '.' ) !== false && 
                     ( $filename === '.' || $filename === '..' || 
                       strpos( $filename, './' ) === 0  || 
                       strpos( $filename, '../' ) === 0 || 
                       strpos( $filename, '/./' ) !== false || 
                       strpos( $filename, '/../' ) !== false ) ) { 
                        return false; 
                } 

		
		return true;

	}

	/**
	 * A method needed by the file transforming and scaling routines in File.php
	 * We do not necessarily care about doing the description at this point
	 * @return {String} the empty string
	 */
	public function getDescriptionUrl() {
		return '';
	}

	/**
	 * Find or guess extension -- ensuring that our extension matches our mime type.
	 * Since these files are constructed from php tempnames they may not start off 
	 * with an extension
	 * This does not override getExtension because things like getMimeType already call getExtension,
	 * and that results in infinite recursion. So, we preemptively *set* the extension so getExtension can find it.
	 * For obvious reasons this should be called as early as possible, as part of initialization
	 */
	public function setExtension() { 	
		// Does this have an extension?
		$n = strrpos( $this->path, '.' );
		if ( $n !== false ) {
			$extension = $n ? substr( $this->path, $n + 1 ) : '';
		} else {
			// If not, assume that it should be related to the mime type of the original file.
			//
			// This entire thing is backwards -- we *should* just create an extension based on 
			// the mime type of the transformed file, *after* transformation.  But File.php demands 
			// to know the name of the transformed file before creating it. 
			$mimeType = $this->getMimeType();
			$extensions = explode( ' ', MimeMagic::singleton()->getExtensionsForType( $mimeType ) );
			if ( count( $extensions ) ) { 
				$extension = $extensions[0];	
			}
		}

		if ( is_null( $extension ) ) {
			throw new MWException( 'cannot determine extension' );
		}

		$this->extension = parent::normalizeExtension( $extension );
	}

	/**
	 * Get the path for the thumbnail (actually any transformation of this file)
	 * The actual argument is the result of thumbName although we seem to have 
	 * buggy code elsewhere that expects a boolean 'suffix'
	 *
	 * @param {String|false} name of thumbnail (e.g. "120px-123456.jpg" ), or false to just get the path
	 * @return {String} path thumbnail should take on filesystem, or containing directory if thumbname is false
	 */
	public function getThumbPath( $thumbName=false ) { 
		$path = dirname( $this->path );
		if ( $thumbName !== false ) {
			$path .= "/$thumbName";
		}
		return $path;
	}

	/**
	 * Return the file/url base name of a thumbnail with the specified parameters
	 *
	 * @param {Array} $params: handler-specific parameters
	 * @return {String} base name for URL, like '120px-12345.jpg'
	 */
	function thumbName( $params ) {
		if ( !$this->getHandler() ) {
			return null;
		}
		$extension = $this->getExtension();
		list( $thumbExt, $thumbMime ) = $this->handler->getThumbType( $extension, $this->getMimeType(), $params );
		$thumbName = $this->handler->makeParamString( $params ) . '-' . $this->getUrlName();
		if ( $thumbExt != $extension ) {
			$thumbName .= ".$thumbExt";
		}
		return $thumbName;
	}

	/** 
	 * Get a URL to access the thumbnail 
	 * This is required because the model of how files work requires that 
	 * the thumbnail urls be predictable. However, in our model the URL is not based on the filename
	 * (that's hidden in the session)
	 *
	 * @param {String} basename of thumbnail file -- however, we don't want to use the file exactly
	 * @return {String} URL to access thumbnail, or URL with partial path
	 */
	public function getThumbUrl( $thumbName=false ) { 
		$path = $this->sessionStash->getBaseUrl();
		$extension = $this->getExtension();
		if ( $thumbName !== false ) {
			$path .= '/' . rawurlencode( $thumbName );
		}
		return $path;
	}

	/** 
	 * The basename for the URL, which we want to not be related to the filename.
	 * Will also be used as the lookup key for a thumbnail file.
	 * @param {Array} optional transformation parameters
	 * @return {String} base url name, like '120px-123456.jpg'
	 */
	public function getUrlName() {
		if ( ! $this->urlName ) {
			$this->urlName = $this->sessionKey . '.' . $this->getExtension();
		}
		return $this->urlName;
	}

	/**
	 * Return the URL of the file, if for some reason we wanted to download it
 	 * We tend not to do this for the original file, but we do want thumb icons
	 * @return {String} url
	 */
	public function getUrl() {
		if ( !isset( $this->url ) ) {
			$this->url = $this->sessionStash->getBaseUrl() . '/' . $this->getUrlName();
		}
		return $this->url;
	}

	/**
	 * Parent classes use this method, for no obvious reason, to return the path (relative to wiki root, I assume). 
	 * But with this class, the URL is unrelated to the path.
	 *
	 * @return {String} url
	 */
	public function getFullUrl() { 
		return $this->getUrl();
	}


	/**
	 * Typically, transform() returns a ThumbnailImage, which you can think of as being the exact
	 * equivalent of an HTML thumbnail on Wikipedia. So its URL is the full-size file, not the thumbnail's URL.
	 *
	 * Here we override transform() to stash the thumbnail file, and then 
	 * provide a way to get at the stashed thumbnail file to extract properties such as its URL
	 *
	 * @param {Array} parameters suitable for File::transform()
	 * @param {Bitmask} flags suitable for File::transform()
	 * @return {ThumbnailImage} with additional File thumbnailFile property
	 */
	public function transform( $params, $flags=0 ) { 

		// force it to get a thumbnail right away
		$flags |= self::RENDER_NOW;

		// returns a ThumbnailImage object containing the url and path. Note. NOT A FILE OBJECT.
		$thumb = parent::transform( $params, $flags );

		$key = $this->thumbName($params);

		// remove extension, so it's stored in the session under '120px-123456'
		// this makes it uniform with the other session key for the original, '123456'
		$n = strrpos( $key, '.' );	
		if ( $n !== false ) {
			$key = substr( $key, 0, $n );
		}

		// stash the thumbnail File, and provide our caller with a way to get at its properties
		$stashedThumbFile = $this->sessionStash->stashFile( $key, $thumb->path );
		$thumb->thumbnailFile = $stashedThumbFile;

		return $thumb;	

	}

}

class SessionStashFileNotFoundException extends MWException {};
class SessionStashBadPathException extends MWException {};

