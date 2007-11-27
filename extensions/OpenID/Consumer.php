<?php
/**
 * Consumer.php -- Consumer side of OpenID site
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * By Evan Prodromou <evan@wikitravel.org>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@wikitravel.org>
 * @addtogroup Extensions
 */

if (defined('MEDIAWIKI')) {

	require_once("Auth/OpenID/Consumer.php");

	# Defines the trust root for this server
	# If null, we make a guess

	$wgTrustRoot = null;

	# When using deny and allow arrays, defines how the security works.
	# If true, works like "Order Allow,Deny" in Apache; deny by default,
	# allow items that match allow that don't match deny to pass.
	# If false, works like "Order Deny,Allow" in Apache; allow by default,
	# deny items in deny that aren't in allow.

	$wgOpenIDConsumerDenyByDefault = false;

	# Which partners to allow; regexps here. See above.

	$wgOpenIDConsumerAllow = array();

	# Which partners to deny; regexps here. See above.

	$wgOpenIDConsumerDeny = array();

	# Where to store transitory data. Can be 'memc' for the $wgMemc
	# global caching object, or 'file' if caching is turned off
	# completely and you need a fallback.
	
	# Default is memc unless the global cache is disabled.

	$wgOpenIDConsumerStoreType = ($wgMainCacheType == CACHE_NONE) ? 'file' : 'memc';

	# If the store type is set to 'file', this is is the name of a
	# directory to store the data in.

	$wgOpenIDConsumerStorePath = ($wgMainCacheType == CACHE_NONE) ? "/tmp/$wgDBname/openidconsumer/" : NULL;

	# Expiration time for the OpenID cookie. Lets the user re-authenticate
	# automatically if their session is expired. Only really useful if
	# it's much greater than $wgCookieExpiration. Default: about one year.

	$wgOpenIDCookieExpiration = 365 * 24 * 60 * 60;

	function wfSpecialOpenIDLogin($par) {
		global $wgRequest, $wgUser, $wgOut;

		if ($wgUser->getID() != 0) {
			OpenIDAlreadyLoggedIn();
			return;
		}

		if ($wgRequest->getText('returnto')) {
			OpenIDConsumerSetReturnTo($wgRequest->getText('returnto'));
		}
		
		$openid_url = $wgRequest->getText('openid_url');
		if (isset($openid_url) && strlen($openid_url) > 0) {
			OpenIDLogin($openid_url);
		} else {
			OpenIDLoginForm();
		}
	}

	function wfSpecialOpenIDFinish($par) {

		global $wgUser, $wgOut, $wgRequest;

		# Shouldn't work if you're already logged in.

		if ($wgUser->getID() != 0) {
			OpenIDAlreadyLoggedIn();
			return;
		}

		$consumer = OpenIDConsumer();

		switch ($par) {
		 case 'ChooseName':
			list($response, $sreg) = OpenIDConsumerFetchValues();
			if (!isset($response) ||
				$response->status != Auth_OpenID_SUCCESS ||
				!isset($response->identity_url)) {
				OpenIDConsumerClearValues();
				# No messing around, here
				$wgOut->errorpage('openiderror', 'openiderrortext');
				return;
			}

			if ($wgRequest->getCheck('wpCancel')) {
				OpenIDConsumerClearValues();
				$wgOut->errorpage('openidcancel', 'openidcanceltext');
				return;
			}

			$choice = $wgRequest->getText('wpNameChoice');
			$nameValue = $wgRequest->getText('wpNameValue');
			wfDebug("OpenID: Got form values '$choice' and '$nameValue'\n");

			$name = OpenIDGetName($response, $sreg, $choice, $nameValue);

			if (!$name || !OpenIDUserNameOK($name)) {
				OpenIDChooseNameForm($response, $sreg);
				return;
			}

			$user = OpenIDCreateUser($response->identity_url, $sreg, $name);

			if (!isset($user)) {
				OpenIDConsumerClearValues();
				$wgOut->errorpage('openiderror', 'openiderrortext');
				return;
			}

			$wgUser = $user;
			OpenIDConsumerClearValues();

			OpenIDFinishLogin($response->identity_url);
			break;

		 default: # No parameter, returning from a server

			$response = $consumer->complete($_GET);

			if (!isset($response)) {
				$wgOut->errorpage('openiderror', 'openiderrortext');
				return;
			}

			switch ($response->status) {
			 case Auth_OpenID_CANCEL:
				// This means the authentication was cancelled.
				$wgOut->errorpage('openidcancel', 'openidcanceltext');
				break;
			 case Auth_OpenID_FAILURE:
				$wgOut->errorpage('openidfailure', 'openidfailuretext');
				break;
			 case Auth_OpenID_SUCCESS:
				// This means the authentication succeeded.
				$openid = $response->identity_url;
				$sreg = $response->extensionResponse('sreg');

				if (!isset($openid)) {
					$wgOut->errorpage('openiderror', 'openiderrortext');
					return;
				}

				$user = OpenIDGetUser($openid);

				if (isset($user)) {
					OpenIDUpdateUser($user, $sreg); # update from server
				} else {
					# For easy names
					$name = OpenIDCreateName($openid, $sreg);
					if ($name) {
						$user = OpenIDCreateUser($openid, $sreg, $name);
					} else {
					# For hard names
						OpenIDConsumerSaveValues($response, $sreg);
						OpenIDChooseNameForm($response, $sreg);
						return;
					}
				}

				if (!isset($user)) {
					$wgOut->errorpage('openiderror', 'openiderrortext');
				} else {
					$wgUser = $user;
					OpenIDFinishLogin($openid);
				}
			}
		}
	}

	function OpenIDFinishLogin($openid) {

		global $wgUser, $wgOut;

		$wgUser->SetupSession();
		$wgUser->SetCookies();

		# Run any hooks; ignore results

		wfRunHooks('UserLoginComplete', array(&$wgUser));

		# Set a cookie for later check-immediate use

		OpenIDLoginSetCookie($openid);

		$wgOut->setPageTitle( wfMsg( 'openidsuccess' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addWikiText( wfMsg( 'openidsuccess', $wgUser->getName(), $openid ) );
		$wgOut->returnToMain(false, OpenIDConsumerReturnTo());
	}

	function OpenIDLoginSetCookie($openid) {
		global $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgCookiePrefix;
		global $wgOpenIDCookieExpiration;

		$exp = time() + $wgOpenIDCookieExpiration;

		setcookie($wgCookiePrefix.'OpenID', $openid, $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure);
	}

	function OpenIDLoginForm() {
		global $wgOut, $wgUser, $wgOpenIDLoginLogoUrl;
		$sk = $wgUser->getSkin();
		$instructions = wfMsg('openidlogininstructions');
		$ok = wfMsg('login');
		$wgOut->addHTML("<p>{$instructions}</p>" .
						'<form action="' . $sk->makeSpecialUrl('OpenIDLogin') . '" method="POST">' .
						'<input type="text" name="openid_url" size=30 ' .
						' style="background: url(' . $wgOpenIDLoginLogoUrl . ') ' .
						'        no-repeat; background-color: #fff; background-position: 0 50%; ' .
						'        color: #000; padding-left: 18px;" value="" />' .
						'<input type="submit" value="' . $ok . '" />' .
						'</form>');
	}

	function OpenIDChooseNameForm($response, $sreg) {

		global $wgOut, $wgUser;
		$sk = $wgUser->getSkin();
		if (array_key_exists('nickname', $sreg)) {
			$message = wfMsg('openidnotavailable', $sreg['nickname']);
		} else {
			$message = wfMsg('openidnotprovided');
		}
		$instructions = wfMsg('openidchooseinstructions');
		$wgOut->addHTML("<p>{$message}</p>" .
						"<p>{$instructions}</p>" .
						'<form action="' . $sk->makeSpecialUrl('OpenIDFinish/ChooseName') . '" method="POST">');
		$def = false;

		# These options won't exist if we can't get them.

		if (array_key_exists('fullname', $sreg) && OpenIDUserNameOK($sreg['fullname'])) {
			$wgOut->addHTML("<input type='radio' name='wpNameChoice' id='wpNameChoiceFull' value='full' " .
							((!$def) ? "checked = 'checked'" : "") . " />" .
							"<label for='wpNameChoiceFull'>" . wfMsg("openidchoosefull", $sreg['fullname']) . "</label><br />");
			$def = true;
		}

		$idname = OpenIDToUserName($response->identity_url);

		if ($idname && OpenIDUserNameOK($idname)) {
			$wgOut->addHTML("<input type='radio' name='wpNameChoice' id='wpNameChoiceUrl' value='url' " .
							((!$def) ? "checked = 'checked'" : "") . " />" .
							"<label for='wpNameChoiceUrl'>" . wfMsg("openidchooseurl", $idname) . "</label><br />");
			$def = true;
		}

		# These are always available

		$wgOut->addHTML("<input type='radio' name='wpNameChoice' id='wpNameChoiceAuto' value='auto' " .
							((!$def) ? "checked = 'checked'" : "") . " />" .
							"<label for='wpNameChoiceAuto'>" . wfMsg("openidchooseauto", OpenIDAutomaticName($sreg)) . "</label><br />");

		$def = true;

		$wgOut->addHTML("<input type='radio' name='wpNameChoice' id='wpNameChoiceManual' value='manual' " .
						" checked='off' />" .
						"<label for='wpNameChoiceManual'>" . wfMsg("openidchoosemanual") . "</label> " .
						"<input type='text' name='wpNameValue' id='wpNameChoice' size='30' /><br />");

		$ok = wfMsg('login');
		$cancel = wfMsg('cancel');

		$wgOut->addHTML("<input type='submit' name='wpOK' value='{$ok}' /> <input type='submit' name='wpCancel' value='{$cancel}' />");
		$wgOut->addHTML("</form>");
	}

	function OpenIDLogin($openid_url, $finish_page = 'OpenIDFinish') {

		global $wgUser, $wgTrustRoot, $wgOut;

		# If it's an interwiki link, expand it

		$openid_url = OpenIDInterwikiExpand($openid_url);

		wfDebug("New URL is '$openid_url'\n");

		# Check if the URL is allowed

		if (!OpenIDCanLogin($openid_url)) {
			$wgOut->errorpage('openidpermission', 'openidpermissiontext');
			return;
		}

		$sk = $wgUser->getSkin();

		if (isset($wgTrustRoot)) {
			$trust_root = $wgTrustRoot;
		} else {
			global $wgArticlePath, $wgServer;
			$root_article = str_replace('$1', '', $wgArticlePath);
			$trust_root = $wgServer . $root_article;
		}

		$consumer = OpenIDConsumer();

		if (!$consumer) {
			$wgOut->errorpage('openiderror', 'openiderrortext');
			return;
		}

		# Make sure the user has a session!

		global $wgSessionStarted;

		if (!$wgSessionStarted) {
			$wgUser->SetupSession();
		}

		$auth_request = $consumer->begin($openid_url);

		// Handle failure status return values.
		if (!$auth_request) {
			$wgOut->errorpage('openiderror', 'openiderrortext');
			return;
		}

		# Check the processed URLs, too

		$endpoint = $auth_request->endpoint;

		if (isset($endpoint)) {
			# Check if the URL is allowed

			if (isset($endpoint->identity_url) && !OpenIDCanLogin($endpoint->identity_url)) {
				$wgOut->errorpage('openidpermission', 'openidpermissiontext');
				return;
			}

			if (isset($endpoint->delegate) && !OpenIDCanLogin($endpoint->delegate)) {
				$wgOut->errorpage('openidpermission', 'openidpermissiontext');
				return;
			}
		}

		$auth_request->addExtensionArg('sreg', 'optional', 'nickname,email,fullname,language,timezone');

		$process_url = OpenIDFullUrl($finish_page);

		$redirect_url = $auth_request->redirectURL($trust_root,
												   $process_url);

		# OK, now go
		$wgOut->redirect($redirect_url);
	}

	function OpenIDCanLogin($openid_url) {

		global $wgOpenIDConsumerDenyByDefault, $wgOpenIDConsumerAllow, $wgOpenIDConsumerDeny;

		if (OpenIDIsLocalUrl($openid_url)) {
			return false;
		}

		if ($wgOpenIDConsumerDenyByDefault) {
			$canLogin = false;
			foreach ($wgOpenIDConsumerAllow as $allow) {
				if (preg_match($allow, $openid_url)) {
					wfDebug("OpenID: $openid_url matched allow pattern $allow.\n");
					$canLogin = true;
					foreach ($wgOpenIDConsumerDeny as $deny) {
						if (preg_match($deny, $openid_url)) {
							wfDebug("OpenID: $openid_url matched deny pattern $deny.\n");
							$canLogin = false;
							break;
						}
					}
					break;
				}
			}
		} else {
			$canLogin = true;
			foreach ($wgOpenIDConsumerDeny as $deny) {
				if (preg_match($deny, $openid_url)) {
					wfDebug("OpenID: $openid_url matched deny pattern $deny.\n");
					$canLogin = false;
					foreach ($wgOpenIDConsumerAllow as $allow) {
						if (preg_match($allow, $openid_url)) {
							wfDebug("OpenID: $openid_url matched allow pattern $allow.\n");
							$canLogin = true;
							break;
						}
					}
					break;
				}
			}
		}
		return $canLogin;
	}

	function OpenIDIsLocalUrl($url) {

		global $wgServer, $wgArticlePath;

		$pattern = $wgServer . $wgArticlePath;
		$pattern = str_replace('$1', '(.*)', $pattern);
		$pattern = str_replace('?', '\?', $pattern);

		return preg_match('|^' . $pattern . '$|', $url);
	}

	function OpenIDFullUrl($title) {
		$nt = Title::makeTitleSafe(NS_SPECIAL, $title);
		if (isset($nt)) {
			return $nt->getFullURL();
		} else {
			return NULL;
		}
	}

	function OpenIDConsumer() {
		global $wgOpenIDConsumerStoreType, $wgOpenIDConsumerStorePath;

		$store = getOpenIDStore($wgOpenIDConsumerStoreType,
					'consumer',
					array('path' => $wgOpenIDConsumerStorePath));

		return new Auth_OpenID_Consumer($store);
	}

	# Find the user with the given openid, if any

	function OpenIDGetUser($openid) {
		global $wgSharedDB, $wgDBprefix;

		if (isset($wgSharedDB)) {
			$tableName = "`$wgSharedDB`.${wgDBprefix}user_openid";
		} else {
			$tableName = 'user_openid';
		}

		$dbr =& wfGetDB( DB_SLAVE );
		$id = $dbr->selectField($tableName, 'uoi_user',
								array('uoi_openid' => $openid));
		if ($id) {
			$name = User::whoIs($id);
			return User::newFromName($name);
		} else {
			return NULL;
		}
	}

	function OpenIDUpdateUser($user, $sreg) {
		global $wgAllowRealName;

		# FIXME: only update if there's been a change

		if (array_key_exists('nickname', $sreg)) {
			$user->setOption('nickname', $sreg['nickname']);
		} else {
			$user->setOption('nickname', '');
		}

		if (array_key_exists('email', $sreg)) {
			$user->setEmail( $sreg['email'] );
		} else {
			$user->setEmail(NULL);
		}

		if (array_key_exists('fullname', $sreg) && $wgAllowRealName) {
			$user->setRealName($sreg['fullname']);
		} else {
			$user->setRealName(NULL);
		}

		if (array_key_exists('language', $sreg)) {
			# FIXME: check and make sure the language exists
			$user->setOption('language', $sreg['language']);
		} else {
			$user->setOption('language', NULL);
		}

		if (array_key_exists('timezone', $sreg)) {
			# FIXME: do something with it.
			# $offset = OpenIDTimezoneToTzoffset($sreg['timezone']);
			# $user->setOption('timecorrection', $offset);
		} else {
			# $user->setOption('timecorrection', NULL);
		}

		$user->saveSettings();
	}

	function OpenIDCreateUser($openid, $sreg, $name) {

		global $wgAuth, $wgAllowRealName;

		$user = User::newFromName($name);

		$user->addToDatabase();

		if (!$user->getId()) {
			wfDebug("OpenID: Error adding new user.\n");
		} else {

			OpenIDInsertUserUrl($user, $openid);

			if (array_key_exists('nickname', $sreg)) {
				$user->setOption('nickname', $sreg['nickname']);
			}
			if (array_key_exists('email', $sreg)) {
				$user->setEmail( $sreg['email'] );
			}
			if ($wgAllowRealName && array_key_exists('fullname', $sreg)) {
				$user->setRealName($sreg['fullname']);
			}
			if (array_key_exists('language', $sreg)) {
				# FIXME: check and make sure the language exists
				$user->setOption('language', $sreg['language']);
			}
			if (array_key_exists('timezone', $sreg)) {
				# FIXME: do something with it.
				# $offset = OpenIDTimezoneToTzoffset($sreg['timezone']);
				# $user->setOption('timecorrection', $offset);
			}
			$user->saveSettings();
			return $user;
		}
	}

	function OpenIDCreateName($openid, $sreg) {

		if (array_key_exists('nickname', $sreg) && # try nickname
			OpenIDUserNameOK($sreg['nickname']))
		{
			return $sreg['nickname'];
		}
	}

	function OpenIDToUserName($openid) {
        if (Services_Yadis_identifierScheme($openid) == 'XRI') {
			wfDebug("OpenID: Handling an XRI: $openid\n");
			return OpenIDToUserNameXri($openid);
		} else {
			wfDebug("OpenID: Handling an URL: $openid\n");
			return OpenIDToUserNameUrl($openid);
		}
	}

	# We try to use an OpenID URL as a legal MediaWiki user name in this order
	# 1. Plain hostname, like http://evanp.myopenid.com/
	# 2. One element in path, like http://profile.typekey.com/EvanProdromou/
	#    or http://getopenid.com/evanprodromou

    function OpenIDToUserNameUrl($openid) {
		static $bad = array('query', 'user', 'password', 'port', 'fragment');

	    $parts = parse_url($openid);

		# If any of these parts exist, this won't work

		foreach ($bad as $badpart) {
			if (array_key_exists($badpart, $parts)) {
				return NULL;
			}
		}

		# We just have host and/or path

		# If it's just a host...
		if (array_key_exists('host', $parts) &&
			(!array_key_exists('path', $parts) || strcmp($parts['path'], '/') == 0))
		{
			$hostparts = explode('.', $parts['host']);

			# Try to catch common idiom of nickname.service.tld

			if ((count($hostparts) > 2) &&
				(strlen($hostparts[count($hostparts) - 2]) > 3) && # try to skip .co.uk, .com.au
				(strcmp($hostparts[0], 'www') != 0))
			{
				return $hostparts[0];
			} else {
				# Do the whole hostname
				return $parts['host'];
			}
		} else {
			if (array_key_exists('path', $parts)) {
				# Strip starting, ending slashes
				$path = preg_replace('@/$@', '', $parts['path']);
				$path = preg_replace('@^/@', '', $path);
				if (strpos($path, '/') === false) {
					return $path;
				}
			}
		}

		return NULL;
	}

	function OpenIDToUserNameXri($xri) {
		$base = OpenIDXriBase($xri);

		if (!$base) {
			return NULL;
		} else {
			# =evan.prodromou
			# or @gratis*evan.prodromou
			$parts = explode('*', substr($base, 1));
			return array_pop($parts);
		}
	}

	# Is this name OK to use as a user name?

	function OpenIDUserNameOK($name) {
		global $wgReservedUsernames;
		return (0 == User::idFromName($name) &&
				!in_array( $name, $wgReservedUsernames ));
	}

	# Get an auto-incremented name

	function OpenIDFirstAvailable($prefix) {
		for ($i = 2; ; $i++) { # FIXME: this is the DUMB WAY to do this
			$name = "$prefix$i";
			if (OpenIDUserNameOK($name)) {
				return $name;
			}
		}
	}

	function OpenIDAlreadyLoggedIn() {

		global $wgUser, $wgOut;

		$wgOut->setPageTitle( wfMsg( 'openiderror' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addWikiText( wfMsg( 'openidalreadyloggedin', $wgUser->getName() ) );
		$wgOut->returnToMain(false, OpenIDConsumerReturnTo() );
	}

	function OpenIDGetUserUrl($user) {
		$openid_url = null;

		if (isset($user) && $user->getId() != 0) {
			global $wgSharedDB, $wgDBprefix;
			if (isset($wgSharedDB)) {
				$tableName = "`${wgSharedDB}`.${wgDBprefix}user_openid";
			} else {
				$tableName = 'user_openid';
			}

			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(array($tableName),
								array('uoi_openid'),
								array('uoi_user' => $user->getId()),
								'OpenIDGetUserUrl');

			# This should return 0 or 1 result, since user is unique
			# in the table.

			while ($res && $row = $dbr->fetchObject($res)) {
				$openid_url = $row->uoi_openid;
			}
			$dbr->freeResult($res);
		}
		return $openid_url;
	}

	function OpenIDSetUserUrl($user, $url) {
		$other = OpenIDGetUserUrl($user);
		if (isset($other)) {
			OpenIDUpdateUserUrl($user, $url);
		} else {
			OpenIDInsertUserUrl($user, $url);
		}
	}

	function OpenIDInsertUserUrl($user, $url) {
		global $wgSharedDB, $wgDBname;
		$dbw =& wfGetDB( DB_MASTER );

		if (isset($wgSharedDB)) {
			# It would be nicer to get the existing dbname
			# and save it, but it's not possible
			$dbw->selectDB($wgSharedDB);
		}

		$dbw->insert('user_openid', array('uoi_user' => $user->getId(),
										  'uoi_openid' => $url));

		if (isset($wgSharedDB)) {
			$dbw->selectDB($wgDBname);
		}
	}

	function OpenIDUpdateUserUrl($user, $url) {
		global $wgSharedDB, $wgDBname;
		$dbw =& wfGetDB( DB_MASTER );

		if (isset($wgSharedDB)) {
			# It would be nicer to get the existing dbname
			# and save it, but it's not possible
			$dbw->selectDB($wgSharedDB);
		}

		$dbw->set('user_openid', 'uoi_openid', $url,
				  'uoi_user = ' . $user->getID());

		if (isset($wgSharedDB)) {
			$dbw->selectDB($wgDBname);
		}
	}

	function OpenIDInterwikiExpand($openid_url) {
		# try to make it into a title object
		$nt = Title::newFromText($openid_url);
		# If it's got an iw, return that
		if (!is_null($nt) && !is_null($nt->getInterwiki())
			&& strlen($nt->getInterwiki()) > 0) {
			return $nt->getFullUrl();
		} else {
			return $openid_url;
		}
	}

	function OpenIDConsumerSaveValues($response, $sreg) {
		global $wgSessionStarted, $wgUser;

		if (!$wgSessionStarted) {
			$wgUser->SetupSession();
		}

		$_SESSION['openid_consumer_response'] = $response;
		$_SESSION['openid_consumer_sreg'] = $sreg;

		return true;
	}

	function OpenIDConsumerClearValues() {
		unset($_SESSION['openid_consumer_response']);
		unset($_SESSION['openid_consumer_sreg']);
		return true;
	}

	function OpenIDConsumerFetchValues() {
		return array($_SESSION['openid_consumer_response'], $_SESSION['openid_consumer_sreg']);
	}

	function OpenIDConsumerReturnTo() {
		return $_SESSION['openid_consumer_returnto'];
	}
	
	function OpenIDConsumerSetReturnTo($returnto) {
		$_SESSION['openid_consumer_returnto'] = $returnto;
	}

	function OpenIDGetName($response, $sreg, $choice, $nameValue) {
		switch ($choice) {
		 case 'full':
			return ((array_key_exists('fullname', $sreg)) ? $sreg['fullname'] : null);
			break;
		 case 'url':
			return OpenIDToUserName($response->identity_url);
			break;
		 case 'auto':
			return OpenIDAutomaticName($sreg);
			break;
		 case 'manual':
			return $nameValue;
		 default:
			return null;
		}
	}

	function OpenIDAutomaticName($sreg) {
		if (array_key_exists('nickname', $sreg) && # try auto-generated from nickname
			strlen($sreg['nickname']) > 0) {
			return OpenIDFirstAvailable($sreg['nickname']);
		} else { # try auto-generated
			return OpenIDFirstAvailable(wfMsg('openidusernameprefix'));
		}
	}
}

?>
