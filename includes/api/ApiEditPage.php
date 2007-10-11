<?php

/*
 * Created on August 16, 2007
 *
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2007 Iker Labarga <Firstname><Lastname>@gmail.com
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
 * A query module to list all external URLs found on a given set of pages.
 *
 * @addtogroup API
 */
class ApiEditPage extends ApiBase {
	//----------------------------------------
	//**** APIEDITPAGE CONSTANTS (2xx) value ****
	const BAD_LGTOKEN 						= 001;
	const BAD_EDITTOKEN						= 002;
	const NO_POST_REQUEST					= 003;
	const GET_CAPTCHA						= 004;
	const MISSING_CAPTCHA					= 005;
	//----------------------------------------

    public function __construct($query, $moduleName) {
        parent :: __construct($query, $moduleName, 'ep');
    }

	/**
	* Return the link to the captcha generated
	*/
	function captchaSupport($myCaptcha, &$result) {
		$info = $myCaptcha->pickImage();
		if( !$info ) {
			return -1;
		} else {
			$index = $myCaptcha->storeCaptcha( $info );
			$title = Title::makeTitle( NS_SPECIAL, 'Captcha/image' );
			$result['captchaId']  = $index;
			$result['captchaURL'] = $title->getLocalUrl( 'wpCaptchaId=' . urlencode( $index ) );
		}
	}

	public function checkCaptcha() {
		global $wgHooks, $wgCaptchaTriggers;
		$i = 0;
		$value = false;
		while ($i < sizeof($wgHooks['EditFilter'])) {
			if (($wgHooks['EditFilter'][$i][0] instanceof FancyCaptcha) && ($wgCaptchaTriggers['edit'] == true)) $value = true;
			$i++;
		}
		return $value;
	}

    public function execute() {
        global $wgUser, $wgRequest;
				$title = $text = $summary = $edittime = $lgtoken = $userid = $tokenid = $value = null;

		if( session_id() == '' ) {
			wfSetupSession();
    	}

		extract($this->extractRequestParams());
		$params = new FauxRequest(array (
	       	'wpTitle' 		=> $title,
	       	'wpTextbox1' 	=> $text,
	       	'wpSummary'		=> $summary,
	       	'wpEdittime'	=> $edittime,
	       	'wplgToken' 	=> $lgtoken,
	       	'wpUserID'		=> $userid,
	       	'wpEditToken'	=> $tokenid,
	       	'wpCaptchaWord' => $captchaword,
			'wpCaptchaId' 	=> $captchaid
	    ));
	  	$wgRequest = $params;

	  	if ((strlen($title) == 0) && ($this->checkCaptcha()) ) {
			$value = 'GET_CAPTCHA';
		} elseif ($this->checkCaptcha() && ($captchaid == 0)) {
			$value = 'MISSING_CAPTCHA';
		}
		else{
			// Ensure the correct timestamp format
			$edittime =eregi_replace("[-,a-z,:]","",$edittime);
    		$object_title = Title::newFromDBkey($title);
			$myArticle = new Article($object_title);

        	// User creation since UserID number
			if ($userid != 0){
        		$myUser = new User();
				$myUser->setID($userid);
				$myUser->loadFromId();
				$myUser->setCookies();
		 		$wgUser = $myUser;

		 		if ($lgtoken != $_SESSION['wsToken']){
					$value = BAD_LGTOKEN;
				}
			}

			if ($value != 'BAD_LGTOKEN'){
    			$md5 = $wgUser->editToken();
      			// This is only to fast testing. So must be cleanned before a Release
      			$tokenid = $md5;

      			// APiEditPage only accepts POST requests
				if (!$_SERVER['REQUEST_METHOD']){
      				$value = 'NO_POST_REQUEST';
      			}

      			else{
      				$params->wasPosted = true;
     				if ($md5 != $tokenid){
						$value = BAD_EDITTOKEN;
      				}
			      	else {
						$editForm = new EditPage($myArticle);
						$editForm->mTitle = $object_title;
						$editForm->importFormData($params);

						$resultDetails = false;
						$value=$editForm->internalAttemptSave( &$resultDetails );
					}
    			}
			}
		}
		switch ($value){
			case EditPage::AS_END:
				$result['result'] = 'Conflict detected';
				break;

			case EditPage::AS_SUCCESS_UPDATE:
				$result['result'] 		= 'Success';
		       	$result['title']		= $editForm->mTitle;
		       	$result['id']			= $myArticle->getID();
		       	$result['revid']		= $myArticle->getRevIdFetched();
		       	$rtext['content']		= $editForm->textbox1;
		       	break;

			case EditPage::AS_MAX_ARTICLE_SIZE_EXCEDED:
				$result['result'] = 'Article too long';
		          break;

			case EditPage::AS_TEXTBOX_EMPTY:
				$result['result'] = 'Blank edition';
				break;

			case EditPage::AS_SUMMARY_NEEDED:
				$result['result'] = 'Summary is mandatory';
				break;

			case EditPage::AS_CONFLICT_DETECTED:
				$result['result'] = 'Conflict detected';
				break;

			case EditPage::AS_SUCCESS_NEW_ARTICLE:
				$result['result'] 		= 'Success';
		       	$result['title']		= $editForm->mTitle;
		       	$result['id']			= $myArticle->getID();
		       	$result['revid']		= $myArticle->getRevIdFetched();
		       	$rtext['content']		= $editForm->textbox1;
		       	break;

	 		case EditPage::AS_BLANK_ARTICLE:
			 	$result['result'] = 'Blank article';
			 	break;

		 	case EditPage::AS_NO_CREATE_PERMISSION;
				$result['result'] = 'No create permission';
				break;

		 	case EditPage::AS_ARTICLE_WAS_DELETED:
			 	$result['result'] = 'Article was deleted before';
			 	break;

			case EditPage::AS_RATE_LIMITED:
			 	$result['result'] = 'Rate limit excedeed';
			 	break;

		 	case EditPage::AS_READ_ONLY_PAGE:
			 	$result['result'] = 'Read only page';
			 	break;

		 	case EditPage::AS_READ_ONLY_PAGE_LOGGED:
				$result['result'] = 'Read only allowed';
				break;

			case EditPage::AS_READ_ONLY_PAGE_ANON:
				$result['result'] = 'Read only allowed';
				break;

			case EditPage::AS_CONTENT_TOO_BIG:
				$result['result'] = 'Article too long';
				break;

			case EditPage::AS_BLOCKED_PAGE_FOR_USER:
				$result['result'] = 'Blocked page for the user';
				break;

			case EditPage::AS_HOOK_ERROR:
				$result['result'] = 'Hook error detected';
				break;

			case EditPage::AS_SPAM_ERROR:
				$result['result'] = 'Spam error detected';
				break;

			case EditPage::AS_FILTERING:
				$result['result'] = 'Filtering not passed';
				break;

			case EditPage::AS_HOOK_ERROR_EXPECTED:
				$result['result'] = 'Hook error detected';
				break;

			case self::NO_POST_REQUEST:
				$result['result'] = 'Error.Only POST requests are allowed';
				break;

			case 'BAD_LGTOKEN':
				$result['result'] = "Error.Login token is wrong";
				break;

			case 'BAD_EDITTOKEN':
				$result['result'] = "Error.Edit token is wrong";
				break;

			case GET_CAPTCHA :
				$myCaptcha = new FancyCaptcha();
				$myCaptcha->storage->clearAll();
				$result['result'] = 'CaptchaIdGenerated';
				$this->captchaSupport($myCaptcha, $result);
				break;

			case MISSING_CAPTCHA :
				$myCaptcha = new FancyCaptcha();
				$myCaptcha->storage->clearAll();
				$result['result'] = 'MissingCaptcha';
				$this->captchaSupport($myCaptcha, $result);
				$result['result'] = 'Error-EditFilter';
				break;

			default :
                $result['result'] = 'Invalid';
                break;
		}

		$this->getResult()->addValue(null, 'editpage', $result);
    	if (isset ($rtext['content'])) $this->getResult()->addValue('text', 'content', $rtext);

    }

    protected function getAllowedParams() {
        return array (
			'title' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
		    'text' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'summary' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'userid' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'edittime' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'lgtoken' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
            'tokenid' => array(
                ApiBase :: PARAM_TYPE => 'string'
            ),
			'captchaword' => array(
				ApiBase :: PARAM_TYPE => 'string'
			),
			'captchaid' => array(
				ApiBase :: PARAM_TYPE => 'string'
			),
       );
    }

    protected function getDescription() {
   		return array (
            'title'			=> 'Title of article',
            'text' 			=> 'text of article',
            'summary'		=> 'Summary of article',
			'userid'		=> 'ID of the user',
			'edittime'		=> 'Timestamp of base revision edited',
			'lgtoken'		=> 'Login token of the user',
			'captchaid' 	=> 'question',
			'captchaword' 	=> 'answer'

        );
    }

    protected function getExamples() {
        return array (
                "Edit a page (anonimous user):",
                "    api.php?action=edit&eptitle=Test&epsummary=test%20summary&eptext=article%20content&epedittime=20070824123454&eptokenid=+%5C"
            );
    }

    public function getVersion() {
        return __CLASS__ . ': $Id: ApiEditPage.php 22289 2007-08-16 13:27:44Z ilabarg1 $';
    }
}
?>