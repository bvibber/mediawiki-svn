<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * @copyright Copyright © 2010 Mark A. Hershberger <mah@everybody.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

class ApiFirefoggChunkedUpload extends ApiUpload {
	public function execute() {
		global $wgUser;

		// Check whether upload is enabled
		if ( !UploadBase::isEnabled() ) {
			$this->dieUsageMsg( array( 'uploaddisabled' ) );
		}

		$this->mParams = $this->extractRequestParams();

		$this->validateParams( $this->mParams );

		$request = $this->getMain()->getRequest();
		$this->mUpload = new FirefoggChunkedUploadHandler;

		$status = $this->mUpload->initialize(
			$request->getVal( 'done', null ),
			$request->getVal( 'filename', null ),
			$request->getVal( 'chunksession', null ),
			$request->getFileTempName( 'chunk' ),
			$request->getFileSize( 'chunk' ),
			$request->getSessionData( UploadBase::getSessionKeyname() )
		);

		if ( $status !== true ) {
			$this->dieUsage(  $status, 'chunk-init-error' );
		}

		$ret = $this->performUpload( );

		if(is_array($ret)) {
			foreach($ret as $key => $val) {
				$this->getResult()->addValue(null, $key, $val);
			}
		} else {
			$this->dieUsage($ret, 'error');
		}
	}

	public function getUpload() { return $this->mUpload; }

	public function performUploadInit($comment, $pageText, $watchlist, $user) {
		$check = $this->mUpload->validateNameAndOverwrite();
		if( $check !== true ) {
			$this->getVerificationError( $check );
		}

		$session = $this->mUpload->setupChunkSession( $comment, $pageText, $watchlist );
		return array('uploadUrl' =>
			wfExpandUrl( wfScript( 'api' ) ) . "?" .
			wfArrayToCGI( array(
				'action' => 'firefoggupload',
				'token' => $user->editToken(),
				'format' => 'json',
				'chunksession' => $session,
				'filename' => $this->mUpload->getDesiredName(),
			) ) );
	}

	public function performUploadChunk() {
		$this->mUpload->setupChunkSession();
		$status = $this->mUpload->appendChunk();
		if ( !$status->isOK() ) {
			$this->dieUsage($status->getWikiText(), 'error');
		}
		return array('result' => 1, 'filesize' => $this->mUpload->getFileSize() );
	}

	public function performUploadDone($user) {
		$this->mUpload->finalizeFile();
		$status = parent::performUpload( $this->comment, $this->pageText, $this->watchlist, $user );

		if ( $status['result'] !== 'Success' ) {
			return $status;
		}
		$file = $this->mUpload->getLocalFile();
		return array('result' => 1, 'done' => 1, 'resultUrl' =>  wfExpandUrl( $file->getDescriptionUrl() ) );
	}

	/**
	 * Handle a chunk of the upload.  Overrides the parent method
	 * because Chunked Uploading clients (i.e. Firefogg) require
	 * specific API responses.
	 * @see UploadBase::performUpload
	 */
	public function performUpload( ) {
		wfDebug( "\n\n\performUpload(chunked): comment: " . $this->comment .
				 ' pageText: ' . $this->pageText . ' watch: ' . $this->watchlist );
		$ret = "unknown error";

		global $wgUser;
		if ( $this->mUpload->getChunkMode() == FirefoggChunkedUploadHandler::INIT ) {
			$ret = $this->performUploadInit($this->comment, $this->pageText, $this->watchlist, $wgUser);
		} else if ( $this->mUpload->getChunkMode() == FirefoggChunkedUploadHandler::CHUNK ) {
			$ret = $this->performUploadChunk();
		} else if ( $this->mUpload->getChunkMode() == FirefoggChunkedUploadHandler::DONE ) {
			$ret = $this->performUploadDone($user);
		}

		return $ret;
	}

	public function mustBePosted() {
		return true;
	}

	public function isWriteMode() {
		return true;
	}

	protected function validateParams( $params ) {
		if( $params['done'] ) {
			$required[] = 'chunksession';
		}
		if( $params['chunksession'] === null ) {
			$required[] = 'filename';
			$required[] = 'comment';
			$required[] = 'watchlist';
			$required[] = 'ignorewarnings';
		}

		foreach( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
	}

	public function getAllowedParams() {
		return array(
			'filename' => null,
			'token' => null,
			'comment' => null,
			'ignorewarnings' => false,
			'chunksession' => null,
			'chunk' => null,
			'done' => false,
			'watchlist' => array(
				ApiBase::PARAM_DFLT => 'preferences',
				ApiBase::PARAM_TYPE => array(
					'watch',
					'unwatch',
					'preferences',
					'nochange'
				),
			),
		);
	}

	public function getParamDescription() {
		return array(
			'filename' => 'Target filename',
			'token' => 'Edit token. You can get one of these through prop=info',
			'comment' => 'Upload comment',
			'watchlist' => 'Unconditionally add or remove the page from your watchlist, use preferences or do not change watch',
			'ignorewarnings' => 'Ignore any warnings',
			'chunksession' => 'The session key, established on the first contact during the chunked upload',
			'chunk' => 'The data in this chunk of a chunked upload',
			'done' => 'Set to 1 on the last chunk of a chunked upload',
		);
	}

	public function getDescription() {
		return array(
			'Upload a file in chunks using the protocol documented at http://firefogg.org/dev/chunk_post.html'
		);
	}

	public function getPossibleErrors() {
		return array_merge(
			parent::getPossibleErrors(),
			array(
				array( 'missingparam' ),
				array( 'chunk-init-error' ),
				array( 'code' => 'chunk-init-error', 'info' => 'Insufficient information for initialization.' ),
				array( 'code' => 'chunked-error', 'info' => 'There was a problem initializing the chunked upload.' ),
			)
		);
	}

	public function getExamples() {
		return array(
			'api.php?action=firefoggupload&filename=Wiki.png',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
