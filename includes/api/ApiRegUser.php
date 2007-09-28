<?php

/*
 * Created on Aug 1, 2007
 *
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2007 Jesus Velez
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
 * A module to register new user accounts.
 *
 * @addtogroup API
 */
class ApiRegUser extends ApiBase {

	const GET_CAPTCHA = -1;
	const MISSING_CAPTCHA = -2;

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName, 'ru');
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

	public function process($value,$results = null) { 
		switch ($value) {
			case ApiRegUser::GET_CAPTCHA :
										$myCaptcha = new FancyCaptcha();
										$myCaptcha->storage->clearAll();
										$result['result'] = 'CaptchaIdGenerated';
										$this->captchaSupport($myCaptcha, $result);
										break;
			case ApiRegUser::MISSING_CAPTCHA :
										$myCaptcha = new FancyCaptcha();
										$myCaptcha->storage->clearAll();
										$result['result'] = 'MissingCaptcha';
										$this->captchaSupport($myCaptcha, $result);
										break;
			case LoginForm::SUCCESS :
										$result['result'] = 'Success';
										break;
			case LoginForm::COOKIE :
										$result['result'] = 'Logged';
										$result['userid'] = $_SESSION['wsUserID'];
										$result['username'] = $_SESSION['wsUserName'];
										$result['token'] = $_SESSION['wsToken'];
										break;
			case LoginForm::NOCOOKIE :
										$result['result'] = 'NoCookie';
										$result['userid'] = $_SESSION['wsUserID'];
										$result['username'] = $_SESSION['wsUserName'];
										$result['token'] = $_SESSION['wsToken'];
										break;
			case LoginForm::WRONG_PASS :
										$result['result'] = 'WrongPassword';
										break;
			case LoginForm::READ_ONLY :
										$result['result'] = 'ReadOnly';
										break;
			case LoginForm::NOT_ALLOWED :
										$result['result'] = 'NotAllowed';
										break;
			case LoginForm::USER_BLOCKED :
										$result['result'] = 'UserBlocked';
										break;
			case LoginForm::SORBS :
										$result['result'] = 'Sorbs';
										$result['blockedIp'] = $results['ip'];
										break;
			case LoginForm::NO_NAME :
										$result['result'] = 'NoName';
										break;
			case LoginForm::USER_EXISTS :
										$result['result'] = 'UserExists';
										break;
			case LoginForm::BAD_RETYPE :
										$result['result'] = 'BadRetype';
										break;
			case LoginForm::TOO_SHORT :
										$result['result'] = 'TooShort';
										break;
			case LoginForm::ABORT_ERROR :
										$result['result'] = 'AbortError';
										break;
			case LoginForm::DB_ERROR :
										$result['result'] = 'DbError';
										break;
			case LoginForm::NO_EMAIL :
										$result['result'] = 'NoEmail';
										break;
			case LoginForm::MAIL_ERROR :
										$result['result'] = 'MailError';
										break;
			case LoginForm::ACCMAILTEXT :
										$result['result'] = 'AccMailText';
										$result['userid'] = $_SESSION['wsUserID'];
										$result['username'] = $_SESSION['wsUserName'];
										$result['token'] = $_SESSION['wsToken'];
										break;
			default :
										$result['result'] = 'Invalid';

		}
		if ($results['mailMsg'] == 1) {
			$result['confirmEmail'] = 'MailSent';
		} else if ($results['mailMsg'] == 2) {
			$result['confirmEmail'] = $results['error']->getMessage();
		}
		$this->getResult()->addValue(null, 'reguser', $result);
	}

	public function checkCaptcha() {
		global $wgHooks;
		$i = 0;
		$value = false;
		while ($i < sizeof($wgHooks['UserCreateForm'])) {
			if ($wgHooks['UserCreateForm'][$i][0] instanceof FancyCaptcha) $value = true;
			$i++;
		}
		return $value;
	}

	public function execute() {
		global $wgRequest;

		$resultDetails = null;
		$value = null;
		if( session_id() == '' ) {
			wfSetupSession();
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				extract($this->extractRequestParams());
				if ( (strlen($user) == 0) && (strlen($password) == 0) && ($this->checkCaptcha()) ) {
					$value = ApiRegUser::GET_CAPTCHA; 
				} elseif ($this->checkCaptcha() && ($captchaid == 0)) {
					$value = ApiRegUser::MISSING_CAPTCHA;
				} else {
					$data = array('wpName' => $user, 
									'wpPassword' => $password,
									'wpRetype' => $password,
									'wpEmail' => $email, 
									'wpRealName' => $name,
									'wpCaptchaWord' => $captchaword,
									'wpCaptchaId' => $captchaid,
									'wpDomain' => $domain);
					$request = new FauxRequest($data);
					$wgRequest = $request;
					$form = new LoginForm( $request );
					$value = $form->addNewAccount($resultDetails);
				}
		}
		$this->process($value,$resultDetails);
	}

	protected function getDescription() {
		return 'Create new user account';
	}

	protected function getExamples() {
		return array (
				"Create new user account using MediaWiki API",
				" You must register using POST method."
			);
	}

	protected function getAllowedParams() {
		return array (
			'user' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'password' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'email' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'name' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'captchaword' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'captchaid' => array(
			ApiBase :: PARAM_TYPE => 'string'
			),
			'domain' => array(
			ApiBase :: PARAM_TYPE => 'string'
			)

		);
	}

	protected function getParamDescription() {
		return array (
			'user' => 'user login name',
			'password' => 'user password',
			'email' => 'user email',
			'name' => 'user name',
			'domain' => 'domain',
			'captchaid' => 'question',
			'captchaword' => 'answer'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiRegUser.php 22289 2007-05-20 23:31:44Z jvelezv $';
	}
}
?>
