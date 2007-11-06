<?php

/*
 * Created on Aug 29, 2007
 *
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2007 Alberto Bernal
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
 * A module to upload given files
 *
 * @addtogroup API
 */
class ApiUploadFile extends ApiBase {
	const UPLOAD_INVALID = -1;
	const UPLOAD_ENABLED_UPLOADS = -2;
	const UPLOAD_NOT_LOGGED = -3;
	const UPLOAD_NOT_ALLOWED = -4;
	const UPLOAD_BLOCKED_PAGE = -5;
	const UPLOAD_READ_ONLY = -6;
	const UPLOAD_BAD_TOKEN = -7;
	const UPLOAD_WITHOUT_POSTPARAMETERS = -8;

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName, 'up');
	}

 	public function execute() {
		global $wgRequest, $wgOut, $wgEnableUploads, $wgUser;

        if( session_id() == '' ) {
            wfSetupSession();
        }

        if (sizeof($_POST) > 0) {
            extract($this->extractRequestParams());

            $data = array('wpSourceType' => "file",
                          'wpDestFile' => $destfile,
                          'wpUploadDescription' => $summary,
                          'wpWatchthis' => $watch,
                          'wpIgnoreWarning' => $ignore,
                          'wpLicense' => $license);
            $request = new FauxRequest($data);

			$form = new UploadForm( $request );

			$form->mTempPath       = $wgRequest->getFileTempName( 'upfile' );
			$form->mSrcName        = $wgRequest->getFileName( 'upfile' );
			$form->mCurlError      = $wgRequest->getUploadError( 'upfile' );
			$form->mFileSize       = $wgRequest->getFileSize( 'upfile' );

			# If the application client is not web mode, the user must be given.
			if( $userid!="" && $usertoken!="" ){
				$MyUser = new User();
				$MyUser->setID( $userid );

				if( $MyUser->loadFromId() ){
					if( $usertoken == $MyUser->mToken ){
						$MyUser->setCookies();
						$wgUser = $MyUser;
					}else{
						$this->process( self::UPLOAD_BAD_TOKEN );
						return;
					}
				}
			}

			# Check uploading enabled
			if( !$wgEnableUploads ) {
				$this->process( self::UPLOAD_ENABLED_UPLOADS );
				return;
			}

			# Check permissions
			if( !$wgUser->isAllowed( 'upload' ) ) {
				if( !$wgUser->isLoggedIn() ) {
					$this->process( self::UPLOAD_NOT_LOGGED );
				} else {
					$this->process( self::UPLOAD_NOT_ALLOWED );
				}
				return;
			}

			# Check blocks
			if( $wgUser->isBlocked() ) {
				$this->process( self::UPLOAD_BLOCKED_PAGE );
				return;
			}

			if( wfReadOnly() ) {
				$this->process( self::UPLOAD_READ_ONLY );
				return;
			}

		 	$details = null;
			$value = $form->internalProcessUpload( $details );
			$form->cleanupTempFile();
			$this->process($value);

        } else {
        	$this->process( self::UPLOAD_WITHOUT_POSTPARAMETERS );
        }
	}

    public function process($value) {
    	global $wgRequest;

        switch ($value) {
			case ApiUploadFile::UPLOAD_INVALID :
	            $result['result'] = 'UPLOAD_INVALID';
			    break;

			case ApiUploadFile::UPLOAD_ENABLED_UPLOADS :
	            $result['result'] = 'UPLOAD_ENABLED_UPLOADS';
			    break;

			case ApiUploadFile::UPLOAD_NOT_LOGGED :
	            $result['result'] = 'UPLOAD_NOT_LOGGED';
			    break;

			case ApiUploadFile::UPLOAD_NOT_ALLOWED :
	            $result['result'] = 'UPLOAD_NOT_ALLOWED';
			    break;

			case ApiUploadFile::UPLOAD_BLOCKED_PAGE :
	            $result['result'] = 'UPLOAD_BLOCKED_PAGE';
			    break;

			case ApiUploadFile::UPLOAD_READ_ONLY :
	            $result['result'] = 'UPLOAD_READ_ONLY';
			    break;

			case ApiUploadFile::UPLOAD_BAD_TOKEN :
	            $result['result'] = 'UPLOAD_BAD_TOKEN';
			    break;

			case ApiUploadFile::UPLOAD_WITHOUT_POSTPARAMETERS :
	            $result['result'] = 'UPLOAD_WITHOUT_POSTPARAMETERS';
			    break;

            case UploadForm::SUCCESS :
	            $result['result'] = 'SUCCESS';
				$result['title'] = $wgRequest->getText( 'updestfile' );
				$result['ns'] = "6";
	            break;

			case UploadForm::BEFORE_PROCESSING:
	            $result['result'] = 'UPLOAD_BEFORE_PROCESSING ';
			    break;

			case UploadForm::LARGE_FILE_SERVER:
	            $result['result'] = 'UPLOAD_LARGE_FILE_SERVER';
			    break;

			case UploadForm::EMPTY_FILE:
	            $result['result'] = 'UPLOAD_EMPTY_FILE';
			    break;

			case UploadForm::MIN_LENGHT_PARTNAME:
	            $result['result'] = 'UPLOAD_MIN_LENGHT_PARTNAME';
			    break;

			case UploadForm::ILLEGAL_FILENAME:
	            $result['result'] = 'UPLOAD_ILLEGAL_FILENAME';
			    break;

			case UploadForm::PROTECTED_PAGE:
	            $result['result'] = 'UPLOAD_PROTECTED_PAGE';
			    break;

			case UploadForm::OVERWRITE_EXISTING_FILE:
	            $result['result'] = 'UPLOAD_OVERWRITE_EXISTING_FILE';
			    break;

			case UploadForm::FILETYPE_MISSING:
	            $result['result'] = 'UPLOAD_FILETYPE_MISSING';
			    break;

			case UploadForm::FILETYPE_BADTYPE:
	            $result['result'] = 'UPLOAD_FILETYPE_BADTYPE';
			    break;

			case UploadForm::VERIFICATION_ERROR:
	            $result['result'] = 'UPLOAD_VERIFICATION_ERROR';
			    break;

			case UploadForm::UPLOAD_VERIFICATION_ERROR:
	            $result['result'] = 'UPLOAD_UPLOADVERIFICATION_ERROR';
			    break;

			case UploadForm::UPLOAD_WARNING:
	            $result['result'] = 'UPLOAD_WARNING';
			    break;

            default :
            	$result['result'] = 'UPLOAD_INVALID';
        }

        $this->getResult()->addValue(null, 'upload', $result);
    }

    protected function getAllowedParams() {
        return array (
            'file' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'destfile' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'summary' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'watch' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'ignore' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'license' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'userid' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'usertoken' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),


        );
    }

    protected function getParamDescription() {
        return array (
            'file' => '<file>',
            'destfile' => '<file_name.jpg>',
            'summary' => 'Description or summary',
            'watch' => 'Boolean',
            'ignore' => 'Boolean',
            'license' => 'License',
            'userid' => 'User Id',
            'usertoken' => 'User token'
        );
    }

	protected function getDescription() {
		return 'Upload image selected to path created and insert image to database.';
	}

	protected function getExamples() {
		return array (
				"Multipart post request:  api.php ? action=upload ",
				"Post Parameters:",
				"  upfile=<path_to_file>",
				"  updestfile=<file_name.jpg>",
				"  upsummary=<summary>",
				"  [upwatch=yes/no]",
				"  [upignore=yes/no]",
				"  [uplicense=<License>]",
				"  [upuserid=<userId>]",
				"  [upusertoken=lgToken]",
			);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiUploadFile.php 23819 2007-11-06 17:45:10Z abernala $';
	}
}