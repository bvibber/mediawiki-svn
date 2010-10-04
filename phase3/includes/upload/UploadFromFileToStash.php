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
	 * @return {Status} result
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
			$this->mLocalFile = $stash->stashFile( false, $this->mTempPath, $data );
			$status->setResult( true, $this->mLocalFile );

		} catch ( Exception $e ) { 
			$status->setResult( false );
			$status->error( $e->getMessage );	
		}

		return $status;
	}

	/**
	 * Get image info, but also add a thumbnail.  Relies a lot on SessionStashFile...
	 * This method also has to pass in an API result, which is a rather bad idea and breaks
 	 * separation of concerns, but that's how the rest of the code works :(
	 *
	 * @param {ApiResult} result
	 * @return {Array} key-val pair of image info, including thumbnail
	 */
	public function getImageInfo( $result ) {

		$imageInfo = parent::getImageInfo( $result );

		// XXX get default thumbnail width. 
		// perhaps when initializing... Isn't this a global? Can't find it anywhere in docs.
		$thumbWidth = 120;

		if ( isset( $this->mParams['thumbWidth'] ) ) {
			$thumbWidthParam = ( int )( $this->mParams['thumbWidth'] );
			if ( $thumbWidthParam > 0 and $thumbWidthParam <= $file->getWidth() ) {
				$thumbWidth = $thumbWidthParam;
			}
		}
		return $imageInfo;
	}
};
