<?php
/**
 * OpenID.php -- Make MediaWiki and OpenID consumer and server
 * Copyright 2006 Internet Brands (http://www.internetbrands.com/)
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
 * @package MediaWiki
 * @subpackage Extensions
 */

if (defined('MEDIAWIKI')) {

	require_once("$IP/extensions/OpenID/Consumer.php");
	require_once("$IP/extensions/OpenID/Convert.php");
	require_once("$IP/extensions/OpenID/Server.php");

	require_once("SpecialPage.php");

	define('MEDIAWIKI_OPENID_VERSION', '0.3');

	$wgExtensionFunctions[] = 'setupOpenID';
	$wgOpenIDPassphrase = null;

	function setupOpenID() {
		global $wgOpenIDPassphrase, $wgMessageCache, $wgOut;

		$wgMessageCache->addMessages(array('openidlogin' => 'Login with OpenID',
										   'openidfinish' => 'Finish OpenID login',
										   'openidserver' => 'OpenID server',
										   'openidconvert' => 'OpenID converter',
										   'openidlogininstructions' => 'Add your OpenID login URL below',
										   'openiderror' => 'Verification error',
										   'openiderrortext' => 'An error occured during verification of the OpenID URL.',
										   'openidpermission' => 'OpenID permissions error',
										   'openidpermissiontext' => 'The OpenID you provided is not allowed to login to this server.',
										   'openidcancel' => 'Verification cancelled',
										   'openidcanceltext' => 'Verification of the OpenID URL was cancelled.',
										   'openidfailure' => 'Verification failed',
										   'openidfailuretext' => 'Verification of the OpenID URL failed.',
										   'openidsuccess' => 'Verification succeeded',
										   'openidsuccesstext' => 'Verification of the OpenID URL succeeded.',
										   'openidusernameprefix' => 'OpenIDUser',
										   'openidserverlogininstructions' => 'Enter your password below to log in to $3 as user $2 (user page $1).',
										   'openidtrustinstructions' => 'Check if you want to share data with $1.',
										   'openidallowtrust' => 'Allow $1 to trust this user account.',
										   'openidnopolicy' => 'Site has not specified a privacy policy.',
										   'openidpolicy' => 'Check the <a target="_new" href="$1">privacy policy</a> for more information.',
										   'openidoptional' => 'Optional',
										   'openidrequired' => 'Required',
										   'openidnickname' => 'Nickname',
										   'openidfullname' => 'Fullname',
										   'openidemail' => 'Email address',
										   'openidlanguage' => 'Language',
										   'openidnotavailable' => 'Your preferred nickname ($1) is already in use by another user.',
										   'openidnotprovided' => 'Your OpenID server did not provide a nickname (either because it can\'t, or because you told it not to).',
										   'openidchooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
										   'openidchoosefull' => 'Your full name ($1)',
										   'openidchooseurl' => 'A name picked from your OpenID ($1)',
										   'openidchooseauto' => 'An auto-generated name ($1)',
										   'openidchoosemanual' => 'A name of your choice: ',
										   'openidconvertinstructions' => 'This form lets you change your user account to use an OpenID URL.',
										   'openidconvertsuccess' => 'Successfully converted to OpenID',
										   'openidconvertsuccesstext' => 'You have successfully converted your OpenID to $1.',
										   'openidconvertyourstext' => 'That is already your OpenID.',
										   'openidconvertothertext' => 'That is someone else\'s OpenID.',
										   ));

		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDLogin'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDFinish'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDServer'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDConvert'));

		# FIXME: make this only output for user pages

		$wgOut->addLink(array('rel' => 'openid.server',
							  'href' => OpenIDServerUrl()));

		# FIXME: People should set their own dang passphrase

		if (is_null($wgOpenIDPassphrase)) {
			global $wgDBname, $wgDBuser, $wgDBpassword;
			$wgOpenIDPassphrase = "$wgDBname|$wgDBuser|$wgDBpassword";
		}
	}
}

?>