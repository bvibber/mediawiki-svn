<?php
/* Copyright (c) 2007 River Tarnell <river@wikimedia.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */
/*
 * AuthPlugin that authenticates users against Atlassian Crowd.
 *
 * To use it, add something like this to LocalSettings.php:
 *
 *    require_once("$IP/extensions/CrowdAuthentication/CrowdAuthentication.php");
 *    $caApplicationName = 'mediawiki';
 *    $caApplicationPassword = 'whatever';
 *    $caCrowdServerUrl = 'http://localhost:8095/crowd/services';
 *    $wgAuth = new CrowdAuthenticator();
 *
 */


require_once("AuthPlugin.php");

class caPasswordCredential {
	public /*string*/ $credential;
};

class caAuthenticatedToken {
};

class caPrincipal {
};

class caSOAPAttribute {
	public /*string*/ $name;
	public /*string[]*/ $values;

	public function __construct(/*string*/ $name, /*string*/ $value) {
		$this->name = $name;
		$this->values = array($value);
	}
};

class caApplicationAuthenticationContext {
	public /*PasswordCredential*/ $credential;
	public /*string*/ $name;
	public /*ValidationFactor[]*/ $validationFactors = null;
};

class caPrincipalAuthenticationContext {
	public /*string*/ $application;
	public /*PasswordCredential*/ $credential;
	public /*string*/ $name;
	public /*ValidationFactor[]*/ $validationFactors = null;
};

class CrowdAuthenticator extends AuthPlugin {
	private $crowd = null;
	private $token = null;

	private function /*SoapClient*/ getCrowd() {
	global	$caCrowdServerUrl, $caApplicationName, $caApplicationPassword;

		if (is_null($this->crowd)) {
			$this->crowd = new SoapClient($caCrowdServerUrl . '/SecurityServer?wsdl',
					array('classmap' =>
						array(
							'ApplicationAuthenticationContext' => 'caApplicationAuthenticationContext',
							'PrincipalAuthenticationContext' => 'caPrincipalAuthenticationContext',
							'PasswordCredential' => 'caPasswordCredential',
							'AuthenticatedToken' => 'caAuthenticatedToken',
							'SOAPPrincipal' => 'caPrincipal',
							'SOAPAttribute' => 'caSOAPAttribute',
						),
					)
				);
			$cred = new caPasswordCredential();
			$cred->credential = $caApplicationPassword;
			$authctx = new caApplicationAuthenticationContext();
			$authctx->credential = $cred;
			$authctx->name = $caApplicationName;
			$t = $this->crowd->authenticateApplication(array("in0" => $authctx));
			$this->token = $t->out;
		}

		return $this->crowd;
	}

	private function /*bool*/ findUsername(/*string*/ $name) {
		/*
		 * Need to check several variations, e.g. lowercase initial letter,
		 * _ instead of ' ', etc.
		 */
		$variations = array(
			$name,
			strtolower($name[0]) . substr($name, 1),
			str_replace(" ", "_", $name),
		);

		$crowd = $this->getCrowd();
		foreach ($variations as $v) {
			try {
				$crowd->findPrincipalByName(array("in0" => $this->token, "in1" => $v));
				return $v;
			} catch (Exception $e) {
				continue;
			}
		}

		return null;
	}
				
	public function /*bool*/ userExists(/*string*/ $name) {
		return !is_null($this->findUsername($name));
	}

	public function /*bool*/ authenticate(/*string*/ $username, /*string*/ $password) {
	global	$caApplicationName;

		$crowd = $this->getCrowd();
		$cred = new caPasswordCredential();
		$cred->credential = $password;
		$authctx = new caPrincipalAuthenticationContext();
		$authctx->name = $this->findUsername($username);
		$authctx->credential = $cred;
		$authctx->application = $caApplicationName;

		try {
			$crowd->authenticatePrincipal(array("in0" => $this->token, "in1" => $authctx));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function /*bool*/ autoCreate(/*void*/) {
		return true;
	}

	public function /*bool*/ strict(/*void*/) {
		return true;
	}

	public function /*bool*/ allowPasswordChange(/*void*/) {
		return true;
	}

	public function /*bool*/ setPassword(/*User*/ $user, /*string*/ $password) {
		$newcred = new caPasswordCredential;
		$newcred->credential = $password;
		$username = $this->findUsername($user->getName());
		$crowd = $this->getCrowd();
		try {
			$crowd->updatePrincipalCredential(array(
						"in0" => $this->token,
						"in1" => $username,
						"in2" => $newcred));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function /*bool*/ canCreateAccounts(/*void*/) {
		return true;
	}

	public function /*bool*/ addUser(/*User*/ $user, /*string*/ $password,
					 /*string*/ $email = '', /*string*/ $realname = '') {
		$crowd = $this->getCrowd();
		$nameparts = split(" ", $realname, 2);
		$firstname = $lastname = "";
		if (count($nameparts) > 0)
			$firstname = $nameparts[0];
		if (count($nameparts) > 1)
			$lastname = $nameparts[1];
		$cred = new caPasswordCredential();
		$cred->credential = $password;
		$principal = new caPrincipal();
		$principal->name = $user->getName();
		$principal->attributes = array(
			new caSOAPAttribute("mail", $email),
			new caSOAPAttribute("givenName", $firstname),
			new caSOAPAttribute("sn", $lastname),
			new caSOAPAttribute("invalidPasswordAttempts", 0),
			new caSOAPAttribute("lastAuthenticated", 0),
			new caSOAPAttribute("passwordLastChanged", 0),
			new caSOAPAttribute("requiresPasswordChange", 0),
		);
		$principal->active = true;
		$principal->conception = 0;
		$principal->lastModified = 0;

		try {
			$crowd->addPrincipal(array("in0" => $this->token,
						   "in1" => $principal, 
						   "in2" => $cred));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
};
