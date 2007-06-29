<?php
/**
 * Convert.php -- Convert existing account to OpenID account
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

	# We use some of the consumer code

	require_once("$IP/extensions/OpenID/Consumer.php");

	function wfSpecialOpenIDConvert($par) {

		global $wgRequest, $wgUser, $wgOut;

		if ($wgUser->getID() == 0) {
			$wgOut->errorpage('openiderror', 'notloggedin');
			return;
		}

		switch ($par) {
		 case 'Finish':
			OpenIDConvertFinish();
			break;
		 default:
			$openid_url = $wgRequest->getText('openid_url');
			if (isset($openid_url) && strlen($openid_url) > 0) {
				OpenIDConvert($openid_url);
			} else {
				OpenIDConvertForm();
			}
		}
	}

	function OpenIDConvert($openid_url) {
		global $wgUser, $wgOut;

		# Expand Interwiki

		$openid_url = OpenIDInterwikiExpand($openid_url);

		if (!OpenIDCanLogin($openid_url)) {
			$wgOut->errorpage('openidpermission', 'openidpermissiontext');
			return;
		}

		$other = OpenIDGetUser($openid_url);

		if (isset($other)) {
			if ($other->getId() == $wgUser->getID()) {
				$wgOut->errorpage('openiderror', 'openidconvertyourstext');
			} else {
				$wgOut->errorpage('openiderror', 'openidconvertothertext');
			}
			return;
		}

		# If we're OK to here, let the user go log in

		OpenIDLogin($openid_url, 'OpenIDConvert/Finish');
	}

	function OpenIDConvertForm() {
		global $wgOut, $wgUser;
		$sk = $wgUser->getSkin();
		$url = OpenIDGetUserUrl($wgUser);
		if (is_null($url)) {
			$url = '';
		}

		$ok = wfMsg('ok');
		$instructions = wfMsg('openidconvertinstructions');
		$wgOut->addHTML("<p>{$instructions}</p>" .
						'<form action="' . $sk->makeSpecialUrl('OpenIDConvert') . '" method="POST">' .
						'<input type="text" name="openid_url" size=30 ' .
						' style="background: url(http://www.openid.net/login-bg.gif) ' .
						'        no-repeat; background-color: #fff; background-position: 0 50%; ' .
						'        color: #000; padding-left: 18px;" value="' . $url . '" />' .
						'<input type="submit" value="' . $ok . '" />' .
						'</form>');
	}

	function OpenIDConvertFinish() {

		global $wgUser, $wgOut;

		$consumer = OpenIDConsumer();
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
			$openid_url = $response->identity_url;

			if (!isset($openid_url)) {
				$wgOut->errorpage('openiderror', 'openiderrortext');
				return;
			}

			# We check again for dupes; this may be normalized or
			# reformatted by the server.

			$other = OpenIDGetUser($openid_url);

			if (isset($other)) {
				if ($other->getId() == $wgUser->getID()) {
					$wgOut->errorpage('openiderror', 'openidconvertyourstext');
				} else {
					$wgOut->errorpage('openiderror', 'openidconvertothertext');
				}
				return;
			}

			OpenIDSetUserUrl($wgUser, $openid_url);

			$wgOut->setPageTitle( wfMsg( 'openidconvertsuccess' ) );
			$wgOut->setRobotpolicy( 'noindex,nofollow' );
			$wgOut->setArticleRelated( false );
			$wgOut->addWikiText( wfMsg( 'openidconvertsuccesstext', $openid_url ) );
			$wgOut->returnToMain( );
		}
	}
}
