<?php
/**
 * Implements regular file uploads, except: 
 *	- stores in local temp area, adds record to session (SessionStash)
 * 	- returns lots of mime info and metadata
 *	- creates thumbnail
 *	- returns URLs for file and thumbnail accessible only to uploading user
 *
 * @file
 * @ingroup upload
 * @author Neil Kandalgaonkar
 */

class UploadFromFileToStash extends UploadFromFile {

	/**
	 * Overrides performUpload, which normally adds the file to the database and makes it publicly available.
	 * Instead, we store it in the SessionStash, and return metadata about the file
	 * We also create a thumbnail, which is visible only to the uploading user.
	 *
	 * @param {String} $comment: optional -- should not be used, here for parent class compatibility
	 * @param {String} $pageText: optional -- should not be used, here only for parent class compatibility
	 * @param {Boolean} $watch: optional -- whether to watchlist this item, should be unused, only here for parent class compatibility
	 * @param {User} $user: optional -- current user, should not be used, only here for parent class compatibility
	 * @return {Status} $status
	 */
	public function performUpload( $comment, $pageText, $watch, $user ) { 
		$status = new Status();
		try { 
			$stash = new SessionStash();
			$data = array( 
				'comment' => $comment,
				'pageText' => $pageText,
				'watch' => $watch,
				'mFileProps' => $this->mFileProps 
			);
			// we now change the value of the local file
			$this->mLocalFile = $stash->stashFile( $this->mTempPath, $data );
			$status->setResult( true, $this->mLocalFile );

		} catch ( Exception $e ) { 
			$status->setResult( false );
			$status->error( $e->getMessage );	
		}

		return $status;
	}

};
