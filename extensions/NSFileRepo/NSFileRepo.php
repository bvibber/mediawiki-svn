<?php
/**
 * The purpose of this extension is to provide NameSpace-based features to uploaded files in the local file repositories (FileRepo)

 * The optimal solution would be a clean extension that is easily maintainable as the trunk of MW moves foward.
 *
 * @file
 * @author Jack D. Pond <jack.pond@psitex.com>
 * @ingroup Extensions
 * @copyright  2009 Jack D. pond
 * @url http://www.mediawiki.org/wiki/Manual:Extension:NSFileRepo
 * @licence GNU General Public Licence 2.0 or later
 *
 * Version 1.4 - Several thumbnail fixes and updates for FileRepo enhancements
 *
 * Version 1.3 - Allows namespace protected files to be whitelisted
 *
 * Version 1.2 - Fixes reupload error and adds lockdown security to archives, deleted, thumbs
 *
 * This extension extends and is dependent on extension Lockdown - see http://www.mediawiki.org/wiki/Extension:Lockdown
 * It must be included(required) after Lockdown!  Also, $wgHashedUploadDirectory must be true and cannot be changed once repository has files in it
 */

if (!defined('MEDIAWIKI')) die('Not an entry point.');
if (!function_exists('lockdownUserCan')) die('You MUST load Extension Lockdown before NSFileRepo (http://www.mediawiki.org/wiki/Extension:Lockdown).');

$wgImgAuthPublicTest = false;		// Must be set to false if you want to use more restrictive than general ['*']['read']
$wgIllegalFileChars = isset($wgIllegalFileChars) ? $wgIllegalFileChars : "";  // For MW Versions <1.16
$wgIllegalFileChars = str_replace(":","",$wgIllegalFileChars);			      // Remove the default illegal char ':' - need it to determine NS

# Internationalisation file
$wgExtensionMessagesFiles['NSFileRepo'] =  dirname(__FILE__) .'/NSFileRepo.i18n.php';
$wgExtensionMessagesFiles['img_auth'] =  dirname(__FILE__) .'/img_auth.i18n.php';

$wgExtensionFunctions[] = 'NSFileRepoSetup';
$wgExtensionCredits['media'][] = array(
	'path' => __FILE__,
	'name' => 'NSFileRepo',
	'author' => 'Jack D. Pond',
	'version' => '1.4',
	'url' => 'http://www.mediawiki.org/wiki/Extension:NSFileRepo',
	'descriptionmsg' => 'nsfilerepo-desc'
);

/**
 * Set up hooks for NSFileRepo
 */

$wgHooks['UploadForm:BeforeProcessing'][] =  'NSFileRepoNSCheck';
/**
Note, this must be AFTER lockdown has been included - thus assuming that the user has access to files in general + files at this particular namespace.
*/
$wgHooks['userCan'][] = 'NSFileRepolockdownUserCan';
$wgHooks['ImgAuthBeforeStream'][] = 'NSFileRepoImgAuthCheck';

class NSLocalRepo extends LocalRepo {
	var $fileFactory = array( 'NSLocalFile', 'newFromTitle' );
	var $oldFileFactory = array( 'NSOldLocalFile', 'newFromTitle' );
	var $fileFromRowFactory = array( 'NSLocalFile', 'newFromRow' );
	var $oldFileFromRowFactory = array( 'NSOldLocalFile', 'newFromRow' );

	static function getHashPathForLevel( $name, $levels ) {
		global $wgContLang;
		$bits=explode(':',$name);
		$filename = $bits[count($bits)-1];
		$path = parent::getHashPathForLevel( $filename, $levels );
		return ((count($bits) > 1) ? $wgContLang->getNsIndex($bits[0]).'/'.$path : $path);
	}
	/**
	 * Get a relative path including trailing slash, e.g. f/fa/
	 * If the repo is not hashed, returns an empty string
	 * This is needed because self:: will call parent if not included - exact same as in FSRepo
	 */
	function getHashPath( $name ) {
		return self::getHashPathForLevel( $name, $this->hashLevels );
	}
	/**
	 * Pick a random name in the temp zone and store a file to it.
	 * @param string $originalName The base name of the file as specified
	 *     by the user. The file extension will be maintained.
	 * @param string $srcPath The current location of the file.
	 * @return FileRepoStatus object with the URL in the value.
	 */
	function storeTemp( $originalName, $srcPath ) {
		$date = gmdate( "YmdHis" );
		$hashPath = $this->getHashPath( $originalName );
		$filename = $this->getFileNameStripped($originalName);
		$dstRel = "$hashPath$date!$filename";
		$dstUrlRel = $hashPath . $date . '!' . rawurlencode( $filename );
		$result = $this->store( $srcPath, 'temp', $dstRel );
		$result->value = $this->getVirtualUrl( 'temp' ) . '/' . $dstUrlRel;
		return $result;
	}
	function getFileNameStripped($suffix) {
		return(NSLocalFile::getFileNameStripped($suffix));
	}
}

class NSLocalFile extends LocalFile
{
	/**
	 * Get the path of the file relative to the public zone root
	 */
	function getRel() {
		return $this->getHashPath() . $this->getFileNameStripped($this->getName());
	}

	/**
	 * Get urlencoded relative path of the file
	 */
	function getUrlRel() {
		return $this->getHashPath() . rawurlencode( $this->getFileNameStripped($this->getName()));
	}

	/** Get the URL of the thumbnail directory, or a particular file if $suffix is specified */
	function getThumbUrl( $suffix = false ) {
		$path = $this->repo->getZoneUrl('thumb') . '/' . $this->getUrlRel();
		if ( $suffix !== false ) {
			$path .= '/' . rawurlencode( $this->getFileNameStripped($suffix) );
		}
		return $path;
	}


	/** Return the file name of a thumbnail with the specified parameters */
	function thumbName( $params ) {
		if ( !$this->getHandler() ) {
			return null;
		}
		$extension = $this->getExtension();
		list( $thumbExt, $thumbMime ) = $this->handler->getThumbType( $extension, $this->getMimeType() );
/* This is the part that changed from LocalFile */
		$thumbName = $this->handler->makeParamString( $params ) . '-' . $this->getFileNameStripped($this->getName());
/* End of changes */
		if ( $thumbExt != $extension ) {
			$thumbName .= ".$thumbExt";
		}
		$bits=explode(':',$this->getName());
		if (count($bits) > 1) $thumbName = $bits[0].":".$thumbName;
		return $thumbName;
	}


	/** Get the path of the thumbnail directory, or a particular file if $suffix is specified */
	function getThumbPath( $suffix = false ) {
		$path = $this->repo->getZonePath('thumb') . '/' . $this->getRel();
		if ( $suffix !== false ) {
			$path .= '/' . $this->getFileNameStripped($suffix);
		}
		return $path;
	}

	/** Get the relative path for an archive file */
	function getArchiveRel( $suffix = false ) {
		$path = 'archive/' . $this->getHashPath();
		if ( $suffix === false ) {
			$path = substr( $path, 0, -1 );
		} else {
			$path .= $this->getFileNameStripped($suffix);
		}
		return $path;
	}

	/** Get the URL of the archive directory, or a particular file if $suffix is specified */
	function getArchiveUrl( $suffix = false ) {
		$path = $this->repo->getZoneUrl('public') . '/archive/' . $this->getHashPath();
		if ( $suffix === false ) {
			$path = substr( $path, 0, -1 );
		} else {
			$path .= rawurlencode( $this->getFileNameStripped($suffix) );
		}
		return $path;
	}

	/** Get the virtual URL for an archive file or directory */
	function getArchiveVirtualUrl( $suffix = false ) {
		$path = $this->repo->getVirtualUrl() . '/public/archive/' . $this->getHashPath();
		if ( $suffix === false ) {
			$path = substr( $path, 0, -1 );
		} else {
			$path .= rawurlencode( $this->getFileNameStripped($suffix) );
		}
		return $path;
	}

	/** Get the virtual URL for a thumbnail file or directory */
	function getThumbVirtualUrl( $suffix = false ) {
		$path = $this->repo->getVirtualUrl() . '/thumb/' . $this->getUrlRel();
		if ( $suffix !== false ) {
			$path .= '/' . rawurlencode( $this->getFileNameStripped($suffix) );
		}
		return $path;
	}

	/** Get the virtual URL for the file itself */
	function getVirtualUrl( $suffix = false ) {
		$path = $this->repo->getVirtualUrl() . '/public/' . $this->getUrlRel();
		if ( $suffix !== false ) {
			$path .= '/' . rawurlencode( $this->getFileNameStripped($suffix) );
		}
		return $path;
	}

	/** Strip namespace (if any) from file name */
	function getFileNameStripped($suffix) {
		$bits=explode(':',$suffix);
		return $bits[count($bits)-1];
	}

	/**
	 * This function overrides the LocalFile because the archive name should not contain the namespace in the
	 * filename.  Otherwise the function would have worked.  This only affects reuploads
	 *
	 * Move or copy a file to its public location. If a file exists at the
	 * destination, move it to an archive. Returns the archive name on success
	 * or an empty string if it was a new file, and a wikitext-formatted
	 * WikiError object on failure.
	 *
	 * The archive name should be passed through to recordUpload for database
	 * registration.
	 *
	 * @param string $sourcePath Local filesystem path to the source image
	 * @param integer $flags A bitwise combination of:
	 *     File::DELETE_SOURCE    Delete the source file, i.e. move
	 *         rather than copy
	 * @return FileRepoStatus object. On success, the value member contains the
	 *     archive name, or an empty string if it was a new file.
	 */
	function publish( $srcPath, $flags = 0 ) {
		$this->lock();
		$dstRel = $this->getRel();
/* This is the part that changed from LocalFile */
		$archiveName = gmdate( 'YmdHis' ) . '!'.$this->getFileNameStripped($this->getName());
/* End of changes */
		$archiveRel = 'archive/' . $this->getHashPath() . $archiveName;
		$flags = $flags & File::DELETE_SOURCE ? LocalRepo::DELETE_SOURCE : 0;
		$status = $this->repo->publish( $srcPath, $dstRel, $archiveRel, $flags );
		if ( $status->value == 'new' ) {
			$status->value = '';
		} else {
			$status->value = $archiveName;
		}
		$this->unlock();
		return $status;
	}

	/**
	 * The only thing changed here is that the array needs to strip the NS from the file name for the has (oldname is already fixed)
	 * Add the old versions of the image to the batch
	 */
	function addOlds() {
		$archiveBase = 'archive';
		$this->olds = array();
		$this->oldCount = 0;

		$result = $this->db->select( 'oldimage',
			array( 'oi_archive_name', 'oi_deleted' ),
			array( 'oi_name' => $this->oldName ),
			__METHOD__
		);
		while( $row = $this->db->fetchObject( $result ) ) {
			$oldName = $row->oi_archive_name;
			$bits = explode( '!', $oldName, 2 );
			if( count( $bits ) != 2 ) {
				wfDebug( "Invalid old file name: $oldName \n" );
				continue;
			}
			list( $timestamp, $filename ) = $bits;
			if( $this->oldName != $filename ) {
				wfDebug( "Invalid old file name: $oldName \n" );
				continue;
			}
			$this->oldCount++;
			// Do we want to add those to oldCount?
			if( $row->oi_deleted & File::DELETED_FILE ) {
				continue;
			}
			$this->olds[] = array(
				"{$archiveBase}/{$this->oldHash}{$oldName}",
/* This is the part that changed from LocalFile */
				"{$archiveBase}/{$this->newHash}{$timestamp}!".$this->getFileNameStripped($this->newName)
/* End of changes */
			);
		}
		$this->db->freeResult( $result );
	}

	/**
	 * The only thing changed here is to strip NS from the file name
	 * Delete cached transformed files
	*/

	function purgeThumbnails() {
		global $wgUseSquid;
		// Delete thumbnails
		$files = $this->getThumbnails();
		$dir = $this->getThumbPath();
		$urls = array();
		foreach ( $files as $file ) {
			# Check that the base file name is part of the thumb name
			# This is a basic sanity check to avoid erasing unrelated directories

/* This is the part that changed from LocalFile */
			if ( strpos( $file, $this->getFileNameStripped($this->getName()) ) !== false ) {
/* End of changes */
				$url = $this->getThumbUrl( $file );
				$urls[] = $url;
				@unlink( "$dir/$file" );
			}
		}

		// Purge the squid
		if ( $wgUseSquid ) {
			SquidUpdate::purge( $urls );
		}
	}

	/**
	 * Replaces hard coded OldLocalFile::newFromRow to use $this->repo->oldFileFromRowFactory configuration
	 * This may not be necessary in the future if LocalFile is patched to allow configuration
	*/

	function getHistory( $limit = null, $start = null, $end = null, $inc = true ) {
		$dbr = $this->repo->getSlaveDB();
		$tables = array( 'oldimage' );
		$fields = OldLocalFile::selectFields();
		$conds = $opts = $join_conds = array();
		$eq = $inc ? '=' : '';
		$conds[] = "oi_name = " . $dbr->addQuotes( $this->title->getDBkey() );
		if( $start ) {
			$conds[] = "oi_timestamp <$eq " . $dbr->addQuotes( $dbr->timestamp( $start ) );
		}
		if( $end ) {
			$conds[] = "oi_timestamp >$eq " . $dbr->addQuotes( $dbr->timestamp( $end ) );
		}
		if( $limit ) {
			$opts['LIMIT'] = $limit;
		}
		// Search backwards for time > x queries
		$order = ( !$start && $end !== null ) ? 'ASC' : 'DESC';
		$opts['ORDER BY'] = "oi_timestamp $order";
		$opts['USE INDEX'] = array( 'oldimage' => 'oi_name_timestamp' );

		wfRunHooks( 'LocalFile::getHistory', array( &$this, &$tables, &$fields, 
			&$conds, &$opts, &$join_conds ) );

		$res = $dbr->select( $tables, $fields, $conds, __METHOD__, $opts, $join_conds );
		$r = array();
		while( $row = $dbr->fetchObject( $res ) ) {
/* This is the part that changed from LocalFile */
			if ( $this->repo->oldFileFromRowFactory ) {
				$r[] = call_user_func( $this->repo->oldFileFromRowFactory, $row, $this->repo );
			} else {
				$r[] = OldLocalFile::newFromRow( $row, $this->repo );
			}
/* End of changes */
		}
		if( $order == 'ASC' ) {
			$r = array_reverse( $r ); // make sure it ends up descending
		}
		return $r;
	}



	/** Instantiating this class using "self"
	 * If you're reading this, you're problably wondering why on earth are the following static functions, which are copied
	 * verbatim from the original extended class "LocalFIle" included here?
	 * The answer is that "self", will instantiate the class the code is physically in, not the class extended from it.
	 * Without the inclusion of these methods in "NSLocalFile, "self" would instantiate a "LocalFile" class, not the
	 * "NSLocalFile" class we want it to.  Since there are only two methods within the "LocalFile" class that use "self",
	 * I just copied that code into the new "NSLocalFile" extended class, and the copied code will instantiate the "NSLocalFIle"
	 * class instead of the "LocalFile" class (at least in PHP 5.2.4)
	 */

	/**
	 * Create a NSLocalFile from a title
	 * Do not call this except from inside a repo class.
	 *
	 * Note: $unused param is only here to avoid an E_STRICT
	 */
	static function newFromTitle( $title, $repo, $unused = null ) {
		return new self( $title, $repo );
	}
	/**
	 * Create a NSLocalFile from a title
	 * Do not call this except from inside a repo class.
	 */

	static function newFromRow( $row, $repo ) {
		$title = Title::makeTitle( NS_IMAGE, $row->img_name );
		$file = new self( $title, $repo );
		$file->loadFromRow( $row );
		return $file;
	}
}
class NSOldLocalFile extends OldLocalFile
{

	function getRel() {
		return 'archive/' . $this->getHashPath() . $this->getFileNameStripped($this->getArchiveName());
	}
	function getUrlRel() {
		return 'archive/' . $this->getHashPath() . urlencode( $this->getFileNameStripped($this->getArchiveName()) );
	}
	function publish( $srcPath, $flags = 0 ) {
		return NSLocalFile::publish( $srcPath, $flags );
	}
	function getThumbUrl( $suffix = false ) {
		return(NSLocalFile::getThumbUrl( $suffix ) );
	}
	function thumbName( $params ) {
		return(NSLocalFile::thumbName( $params ));
	}
	function getThumbPath( $suffix = false ) {
		return(NSLocalFile::getThumbPath( $suffix ));
	}
	function getArchiveRel( $suffix = false ) {
		return(NSLocalFile::getArchiveRel( $suffix ));
	}
	function getArchiveUrl( $suffix = false ) {
		return(NSLocalFile::getArchiveUrl( $suffix ));
	}
	function getArchiveVirtualUrl( $suffix = false ) {
		return(NSLocalFile::getArchiveVirtualUrl( $suffix ));
	}
	function getThumbVirtualUrl( $suffix = false ) {
		return(NSLocalFile::getArchiveVirtualUrl( $suffix ));
	}
	function getVirtualUrl( $suffix = false ) {
		return(NSLocalFile::getVirtualUrl( $suffix ));
	}
	function getFileNameStripped($suffix) {
		return(NSLocalFile::getFileNameStripped($suffix));
	}
	function addOlds() {
		return(NSLocalFile::addOlds());
	}
	function purgeThumbnails() {
		return(NSLocalFile::purgeThumbnails());
	}
	/**
	 * Replaces hard coded OldLocalFile::newFromRow to use $this->repo->oldFileFromRowFactory configuration
	 * This may not be necessary in the future if LocalFile is patched to allow configuration
	*/
	function getHistory( $limit = null, $start = null, $end = null, $inc = true ) {
		return(NSLocalFile::getHistory( $limit, $start , $end, $inc) );
	}

	/** See comment above about Instantiating this class using "self" */

	static function newFromTitle( $title, $repo, $time = null ) {
		# The null default value is only here to avoid an E_STRICT
		if( $time === null )
			throw new MWException( __METHOD__.' got null for $time parameter' );
		return new self( $title, $repo, $time, null );
	}

	static function newFromArchiveName( $title, $repo, $archiveName ) {
		return new self( $title, $repo, null, $archiveName );
	}

	static function newFromRow( $row, $repo ) {
		$title = Title::makeTitle( NS_IMAGE, $row->oi_name );
		$file = new self( $title, $repo, null, $row->oi_archive_name );
		$file->loadFromRow( $row, 'oi_' );
		return $file;
	}
}

/**
 * Initial setup, add .i18n. messages from $IP/extensions/DiscussionThreading/DiscussionThreading.i18n.php
*/
function NSFileRepoSetup() {
	global $wgLocalFileRepo,$wgVersion;
	$xversion = explode(".",$wgVersion);
	if ($xversion[0] <= "1" && $xversion[1] < "16") wfLoadExtensionMessages( 'img_auth' );  // loads img_auth messages for versions <1.16
	wfLoadExtensionMessages( 'NSFileRepo' );
	$wgLocalFileRepo['class'] = "NSLocalRepo";
	RepoGroup::destroySingleton();
}
/*
 * Check for Namespace in Title Line
*/
function NSFileRepoNSCheck($UploadForm) {
	$title = Title::newFromText($UploadForm->mDesiredDestName);
	if ($title->mNamespace < 100) {
		$UploadForm->mDesiredDestName = preg_replace ( "/:/", '-', $UploadForm->mDesiredDestName);
	} else {
		$bits=explode(':',$UploadForm->mDesiredDestName);
		$ns = array_shift($bits);
		$UploadForm->mDesiredDestName = $ns.":".implode("-",$bits);
	}
	return (true);
}

// If Extension:Lockdown has been activated (recommend), check individual namespace protection

function NSFileRepolockdownUserCan($title, $user, $action, &$result) {
	global $wgWhitelistRead;
	if (in_array($title->getPrefixedText(), $wgWhitelistRead)) return true;
	if (function_exists('lockdownUserCan')){
		if($title->getNamespace() == NS_IMAGE) {
			$ntitle = Title::newFromText($title->mDbkeyform);
			return ($ntitle->mNamespace < 100) ? true : lockdownUserCan($ntitle, $user, $action, $result);
		}
	}
	return true;
}

function NSFileRepoImgAuthCheck($title, $path, $name, $result) {
	global $wgContLang;

# See if stored in a NS path

	$subdirs = explode('/',$path);
	$x = (!is_numeric($subdirs[1]) && ($subdirs[1] == "archive" || $subdirs[1] == "deleted" || $subdirs[1] == "thumb")) ? 2 : 1;
	$x = ($x == 2 && $subdirs[1] == "thumb" && $subdirs[2] == "archive") ? 3 : $x;
	if (strlen($subdirs[$x]) >= 3 && is_numeric($subdirs[$x]) && $subdirs[$x] >= 100)  {
		$title = Title::makeTitleSafe( NS_IMAGE, $wgContLang->getNsText($subdirs[$x]).":".$name );
		if( !$title instanceof Title ) {
			$result = array('img-auth-accessdenied','img-auth-badtitle',$name);
			return false;
		}
	}
	return true;
}