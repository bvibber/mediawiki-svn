<?php
/**
 * @file
 * @ingroup upload
 *
 * Implements uploading from previously stored file.
 *
 * @author Bryan Tong Minh
 */

class UploadFromStash extends UploadBase {
	public static function isValidRequest( $request ) {
		$stash = RepoGroup::singleton()->getLocalRepo()->getStash();
		return (bool)$stash->getUpload( $request->getInt( 'wpSessionKey' ) );
	}
	/**
	 * some $na vars for uploadBase method compatibility.
	 */
	public function initialize( $name, $id, $na=false, $na2=false ) {
		$this->mStash = RepoGroup::singleton()->getLocalRepo()->getStash();
		$this->mTemporaryUpload = $this->mStash->getUpload( $id );
		$data = $this->mTemporaryUpload->getData();

		parent::initialize( $name,
			$this->mTemporaryUpload->getRealPath(),
			$data['size'],
			/* $removeTempFile */ false
		);

		$this->mFileProps = $data['props'];
	}

	public function initializeFromRequest( &$request ) {
		$this->mSessionKey = $request->getInt( 'wpSessionKey' );
		$sessionData = $request->getSessionData('wsUploadData');

		$desiredDestName = $request->getText( 'wpDestFile' );
		if( !$desiredDestName )
			$desiredDestName = $request->getText( 'wpUploadFile' );
		return $this->initialize( $desiredDestName, $this->mSessionKey, false );
	}

	/**
	 * File has been previously verified so no need to do so again.
	 */
	protected function verifyFile() {
		return true;
	}

	
	/**
	 * There is no need to stash the image twice
	 */
	public function stashSession() {
		if ( !empty( $this->mSessionKey ) )
			return $this->mSessionKey;
		return parent::stashSession();
	}

	/**
	 * Remove a temporarily kept file stashed by saveTempUploadedFile().
	 * @return success
	 */
	public function unsaveUploadedFile() {
		$success = $this->mStash->freeUpload( $this->mTemporaryUpload );
		return $success;
	}

}