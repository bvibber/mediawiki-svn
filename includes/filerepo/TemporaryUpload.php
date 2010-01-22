<?php

class TemporaryUpload extends UnregisteredLocalFile {
	public static function newFromRow( $repo, $row ) {
		return TemporaryUpload::newFromArray( $repo, array(
			'us_id' => $row->us_id,
			'us_user' => $row->us_user,
			'us_timestamp' => $row->us_timestamp,
			'us_expiry' => $row->us_expiry,
			'us_path' => $row->us_path,
			'us_data' => $row->us_data,
		) );
	}
	public static function newFromArray( $repo, $row ) {
		$path = $repo->resolveVirtualUrl( $row['us_path'] );
		
		$file = new TemporaryUpload( $repo, $path );
		$file->loadFromArray( $row );
		
		return $file;				
	}
	
	public function __construct( $repo, $path ) {
		parent::__construct( false, $repo, $path );
	}
	
	public function loadFromArray( $row ) {
		$this->id = $row['us_id'];
		$this->userId = $row['us_user'];
		$this->user = null;
		$this->timestamp = wfTimestamp( TS_MW, $row['us_timestamp'] );
		$this->expiry = wfTimestamp( TS_MW, $row['us_expiry'] );
		$this->virtualPath = $row['us_path'];
		$this->data = unserialize( $row['us_data'] );
	}
	
	/**
	 * Returns the id of the upload
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	/**
	 * Returns a the owner of this file. Overrides parent
	 * 
	 * @return mixed
	 */
	public function getUser( $type = 'text' ) {
		if ( is_null( $this->user ) )
			$this->user = User::newFromId( $this->getUserId() );		
			
		if ( $type == 'id' )
			return $this->getUserId();
		elseif ( $type == 'text' )
			return $this->user->getName();
			
		return null;
	}
	/**
	 * Returns the id of the owner of this file
	 * 
	 * @return int
	 */
	public function getUserId() {
		return $this->userId;
	} 
	/**
	 * Returns the time of upload in TS_MW format
	 * 
	 * @return string
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	/**
	 * Returns the expiry time of the file in TS_MW format
	 * 
	 * @return string
	 */
	public function getExpiry() {
		return $this->expiry;
	}
	/**
	 * Returns whether the file is expired
	 * 
	 * @return bool
	 */
	public function isExpired() {
		return (wfTimestamp( TS_MW ) > $this->getExpiry());
	}
	/**
	 * Returns the virtual path of the file
	 * 
	 * @param $suffix string
	 * @return string
	 */
	public function getVirtualUrl( $suffix = false ) {
		$path = $this->virtualPath;
		if ( $suffix !== false ) {
			$path .= '/' . rawurlencode( $suffix );
		}
		return $path;
	}
	/**
	 * Returns the path on the file system
	 * 
	 * @return string
	 */
	public function getRealPath() {
		# Set by parent constructor
		return $this->path;
	}
	/**
	 * Returns any associated data
	 * 
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * Returns the public URL. Overrides parent.
	 * 
	 * @return string
	 */
	public function getURL() {
		# Not really necessary yet, but once we have thumbnailing for temp 
		# files this is needed.
		return false;
	}
}
