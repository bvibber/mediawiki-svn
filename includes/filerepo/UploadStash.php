<?php
/**
 * Store for uncompleted uploads.
 * 
 * 
 */

class UploadStash {
	protected $repo;
	protected static $selectFields = array( 
		'us_id', 'us_user', 'us_timestamp', 
		'us_expiry', 'us_path', 'us_data' 
	);
	
	public function __construct( $repo ) {
		$this->repo = $repo;
	}
	
	/**
	 * Stash a file in a temporary directory for later processing
	 * and record it into the database
	 *
	 * @param $user User
	 * @param $expiry Timestamp
	 * @param string $saveName - the destination filename
	 * @param string $tempSrc - the source temporary file to save
	 * @param $data array Additional data that needs to be stored
	 * @return TemporaryUpload 
	 */
	public function saveFile( $user, $expiry, $saveName, $tempSrc, $data ) {
		$status = $this->repo->storeTemp( $saveName, $tempSrc );
		if ( !$status->isOk() )
			return false;
		
		$upload = $this->recordUpload( $user, $expiry, $status->value, $data );
		return $upload;
	}
	
	/**
	 * Record a temporary upload into the database
	 * 
	 * @param $user User
	 * @param $expiry Timestamp
	 * @param $path string The mwrepo:// virtual url
	 * @param $data array Additional data that needs to be stored
	 * @return TemporaryUpload
	 */
	public function recordUpload( $user, $expiry, $path, $data ) {
		$dbw = $this->repo->getMasterDB();
		$insert = array(
			'us_id' => $dbw->nextSequenceValue( 'upload_stash_us_id_seq' ),
			'us_user' => $user->getId(),
			'us_timestamp' => $dbw->timestamp(),
			'us_expiry' => $dbw->timestamp( $expiry ),
			'us_path' => $path,
			'us_data' => serialize( $data ),
		);
		$dbw->insert( 'upload_stash', $insert, __METHOD__ );
		
		$insert['us_id'] = $dbw->insertId();
		return TemporaryUpload::newFromArray( $this->repo, $insert );
	}
	
	/**
	 * Get the information for a specific upload
	 * 
	 * @param $id int 
	 * @return TemporaryUpload
	 */
	public function getUpload( $id ) {
		$dbr = $this->repo->getSlaveDB();
		$row = $dbr->selectRow( 'upload_stash', 
			self::$selectFields, 
			array( 'us_id' => $id ),
			__METHOD__
		);
		if ( !$row )
			return null;
		
		return TemporaryUpload::newFromRow( $this->repo, $row );

	}
	/**
	 * List the uploads from a certain user
	 * 
	 * @param $user User
	 * @return array Array of items
	 */
	public function listUploads( $user ) {
		$dbr = $this->repo->getSlaveDB();
		$result = $dbr->select( 'upload_stash', 
			self::$selectFields, 
			array( 'us_user' => $user->getId() ),
			__METHOD__ 
		);
		
		$uploads = array();
		foreach ( $result as $row ) {
			$uploads[] = TemporaryUpload::newFromRow( $this->repo, $row );
		}
		return $uploads;
	}
	
	/**
	 * Delete the temporary file and remove the record from the database
	 * 
	 * FIXME
	 */
	public function freeUpload( $upload ) {
		$dbw = $this->repo->getMasterDB();
		$dbw->delete( 'upload_stash', array( 'us_id' => $upload->getId() ), __METHOD__ );
		
		return $this->repo->freeTemp( $upload->getVirtualUrl() );
	}
	
}


