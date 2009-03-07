<?php

/*
 * Created on Aug 21, 2008
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2008 - 2009 Bryan Tong Minh <Bryan.TongMinh@Gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('MEDIAWIKI')) {
	// Eclipse helper - will be ignored in production
	require_once ("ApiBase.php");
}


/**
 * @ingroup API
 */
class ApiUpload extends ApiBase {

	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}

	public function execute() {
		global $wgUser;
		$this->getMain()->requestWriteMode();
		$this->mParams = $this->extractRequestParams();
		$request = $this->getMain()->getRequest();
		// Add the uploaded file to the params array
		$this->mParams['file'] = $request->getFileName( 'file' );
		
		// Check whether upload is enabled
		if( !UploadBase::isEnabled() )
			$this->dieUsageMsg( array( 'uploaddisabled' ) );
		
		// One and only one of the following parameters is needed
		$this->requireOnlyOneParameter( $this->mParams,
			'sessionkey', 'file', 'url', 'enablechunks' );
		
		if( $this->mParams['sessionkey'] ) {
			// Stashed upload			
			$this->mUpload = new UploadFromStash();
			$this->mUpload->initialize( $this->mParams['sessionkey'] );
		} else {
			// Upload from url or file or start a chunks request
			
			// Parameter filename is required
			if( !isset( $this->mParams['filename'] ) )
				$this->dieUsageMsg( array( 'missingparam', 'filename' ) );
			
			// Initialize $this->mUpload
			if( isset( $this->mParams['file'] ) ) {				
				$this->mUpload = new UploadFromUpload();
				$this->mUpload->initialize(
					$request->getFileTempName( 'file' ),
					$request->getFileSize( 'file' ),
					$request->getFileName( 'file' )
				);				
			} elseif( isset( $this->mParams['url'] ) ) {											
				$this->mUpload = new UploadFromUrl();
				$this->mUpload->initialize(  $this->mParams['filename'], $this->mParams['url'] );											
			}elseif (isset( $this->mParams['enablechunks'])) {								
				$this->mUpload = new UploadFromChunks();
				$this->mUpload->initializeFromParams( $this->mParams );				
			}
		}
		// Check whether the user has the appropriate permissions to upload anyway
		$permission = $this->mUpload->isAllowed( $wgUser );
		
		/*global $wgGroupPermissions;
		
		print "perm: $permission";
		print_r($wgGroupPermissions['user'], $wgUser->isAllowed( 'upload' ));
		die();*/
		
		if( $permission !== true ) {
			if( !$wgUser->isLoggedIn() )
				$this->dieUsageMsg( array( 'mustbeloggedin', 'upload' ) );
			else
				$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}
		
		// Perform the upload
		$result = $this->performUpload();
		
		// Cleanup any temporary mess
		$this->mUpload->cleanupTempFile();
		
		$this->getResult()->addValue( null, $this->getModuleName(), $result );
	}
	
	private function performUpload() {
		global $wgUser;
		$result = array();
		$resultDetails = null;
		
		$permErrors = $this->mUpload->verifyPermissions( $wgUser );
		if( $permErrors !== true ) {
			$result['result'] = 'Failure';
			$result['error'] = 'permission-denied';
			return $result;
		}
		
		$verification = $this->mUpload->verifyUpload( $resultDetails );
		if( $verification != UploadFromBase::OK ) {
			$result['result'] = 'Failure';
			switch( $verification ) {
				case UploadFromBase::EMPTY_FILE:
					$result['error'] = 'empty-file';
					break;
				case UploadFromBase::FILETYPE_MISSING:
					$result['error'] = 'filetype-missing';
					break;
				case UploadFromBase::FILETYPE_BADTYPE:
					global $wgFileExtensions;
					$result['error'] = 'filetype-banned';
					$result['filetype'] = $resultDetails['finalExt'];
					$result['allowed-filetypes'] = $wgFileExtensions;
					break;
				case UploadFromBase::MIN_LENGHT_PARTNAME:
					$result['error'] = 'filename-tooshort';
					break;
				case UploadFromBase::ILLEGAL_FILENAME:
					$result['error'] = 'illegal-filename';
					$result['filename'] = $resultDetails['filtered'];
					break;
				case UploadFromBase::OVERWRITE_EXISTING_FILE:
					$result['error'] = 'overwrite';
					break;
				case UploadFromBase::VERIFICATION_ERROR:
					$result['error'] = 'verification-error';
					$args = $resultDetails['veri'];
					$code = array_shift( $args );
					$result['verification-error'] = $code;
					$result['args'] = $args;
					$this->getResult()->setIndexedTagName( $result['args'], 'arg' );
					break;
				case UploadFromBase::UPLOAD_VERIFICATION_ERROR:
					$result['error'] = 'upload-verification-error';
					$result['upload-verification-error'] = $resultDetails['error'];
					break;
				default:
					$result['error'] = 'unknown-error';
					$result['code'] = $verification;
					break;
			}
			return $result;
		}
		
		if( !$this->mParams['ignorewarnings'] ) {
			$warnings = $this->mUpload->checkWarnings();
			if( $warnings ) {
				$this->getResult()->setIndexedTagName( $warnings, 'warning' );
				
				$result['result'] = 'Warning';
				$result['warnings'] = $warnings;
				if( isset( $result['filewasdeleted'] ) )
					$result['filewasdeleted'] = $result['filewasdeleted']->getDBkey();
				
				$sessionKey = $this->mUpload->stashSession();
				if( $sessionKey )
					$result['sessionkey'] = $sessionKey;
				return $result;
			}
		}
		
		$status = $this->mUpload->performUpload( $this->mParams['comment'],
			$this->mParams['comment'], $this->mParams['watch'], $wgUser );
		
		if( !$status->isGood() ) {
			$result['result'] = 'Failure';
			$result['error'] = 'internal-error';
			$result['details'] = $status->getErrorsArray();
			$this->getResult()->setIndexedTagName( $result['details'], 'error' );
			return $result;
		}
		
		$file = $this->mUpload->getLocalFile();
		$result['result'] = 'Success';
		$result['filename'] = $file->getName();
		
		// Append imageinfo to the result
		$result['imageinfo'] = ApiQueryImageInfo::getInfo( $file,
			array_flip( ApiQueryImageInfo::allProps() ),
			$this->getResult() );
		
		return $result; 
	}

	public function mustBePosted() { 
		return false; 
	}

	public function getAllowedParams() {
		return array (
			'filename' => null,
			'file' => null,
			'url' => null,
			'comment' => array(
				ApiBase :: PARAM_DFLT => ''
			),
			'watch' => false,
			'ignorewarnings' => false,
			'enablechunks' => false,
			'done'	=> false,
			'sessionkey' => null,
		);
	}

	public function getParamDescription() {
		return array (
			'filename' => 'Target filename',
			'file' => 'File contents',
			'url' => 'Url to upload from',
			'comment' => 'Upload comment or initial page text',
			'watch' => 'Watch the page',
			'ignorewarnings' => 'Ignore any warnings',
			'enablechunks' => 'Boolean If we are in chunk mode; accepts many small file POSTs',
			'chunk_inx'=> 'The index of the chunk being uploaded. Used to order the build of a single file',
			'done'	=> 'When used with "chunks", Is sent to notify the api The last chunk is being uploaded.',
			'sessionkey' => 'Session key in case there were any warnings, or uploading chunks'
		);
	}

	public function getDescription() {
		return array(
			'Upload an File'
		);
	}

	protected function getExamples() {
		return array (
			'api.php?action=upload&filename=Wiki.png&url=http%3A//upload.wikimedia.org/wikipedia/en/b/bc/Wiki.png&ignorewarnings'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}

