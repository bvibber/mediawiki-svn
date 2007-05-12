<?php

/**
 * A file object referring to either a standalone local file, or a file in a 
 * local repository with no database, for example an FSRepo repository.
 *
 * Read-only.
 *
 * TODO: Currently it doesn't really work in the repository role, there are 
 * lots of functions missing. It is used by the WebStore extension in the 
 * standalone role.
 */
class UnregisteredLocalFile extends File {
	var $title, $path, $mime, $handler, $dims;

	function newFromPath( $path, $mime ) {
		return new UnregisteredLocalFile( false, false, $path, $mime );
	}

	function newFromTitle( $title, $repo ) {
		return new UnregisteredLocalFile( $title, $repo, false, false );
	}

	function __construct( $title = false, $repo = false, $path = false, $mime = false ) {
		if ( !( $title && $repo ) && !$path ) {
			throw new MWException( __METHOD__.': not enough parameters, must specify title and repo, or a full path' );
		}
		if ( $title ) {
			$this->title = $title;
			$this->name = $title->getDBkey();
		} else {
			$this->name = basename( $path );
			$this->title = Title::makeTitleSafe( NS_IMAGE, $this->name );
		}
		$this->repo = $repo;
		if ( $path ) {
			$this->path = $path;
		} else {
			$this->path = $repo->getRootDirectory() . '/' . $repo->getHashPath( $this->name ) . $this->name;
		}
		if ( $mime ) {
			$this->mime = $mime;
		}
		$this->dims = array();
	}

	function getPageDimensions( $page = 1 ) {
		if ( !isset( $this->dims[$page] ) ) {
			$this->dims[$page] = $this->getHandler()->getPageDimensions( $this, $page );
		}
		return $this->dims[$page];
	}

	function getWidth( $page = 1 ) {
		$dim = $this->getPageDimensions( $page );
		return $dim['width'];
	}

	function getHeight( $page = 1 ) {
		$dim = $this->getPageDimensions( $page );
		return $dim['height'];
	}

	function getMimeType() {
		if ( !isset( $this->mime ) ) {
			$magic = MimeMagic::singleton();
			$this->mime = $magic->guessMimeType( $this->path );
		}
		return $this->mime;
	}

	function getPath() {
		return $this->path;
	}

	function getFullPath() {
		return $this->path;
	}

	function getHandler() {
		if ( !isset( $this->handler ) ) {
			$this->handler = MediaHandler::getHandler( $this->getMimeType() );
		}
		return $this->handler;
	}

	function getImageSize() {
		return $this->getHandler()->getImageSize( $this, $this->getImagePath() );
	}

	function getMetadata() {
		if ( !isset( $this->metadata ) ) {
			$this->metadata = $this->getHandler()->getMetadata( $this, $this->getImagePath() );
		}
		return $this->metadata;
	}

	function getURL() {
		if ( $this->repo ) {
			return $this->repo->getZoneUrl( 'public' ) . $this->repo->getHashPath( $this->name ) . urlencode( $this->name );
		} else {
			return false;
		}
	}

	function transform( $params, $flags = 0 ) {
		# TODO
		return $this->iconThumb();
	}

}
?>
