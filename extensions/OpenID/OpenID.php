<?php
/**
 * OpenID.php -- Make MediaWiki and OpenID consumer and server
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

	require_once("$IP/extensions/OpenID/Consumer.php");
	require_once("$IP/extensions/OpenID/Convert.php");
	require_once("$IP/extensions/OpenID/Server.php");
	require_once("$IP/extensions/OpenID/MemcStore.php");

	require_once("Auth/OpenID/FileStore.php");

	require_once("SpecialPage.php");

	define('MEDIAWIKI_OPENID_VERSION', '0.7.0');

	$wgExtensionFunctions[] = 'setupOpenID';
	$wgExtensionCredits['other'][] = array('name' => 'OpenID',
										   'version' => MEDIAWIKI_OPENID_VERSION,
										   'author' => 'Evan Prodromou',
										   'url' => 'http://www.mediawiki.org/wiki/Extension:OpenID',
										   'description' => 'lets users login to the wiki with an [http://openid.net/ OpenID] ' .
										   'and login to other OpenID-aware Web sites with their wiki user account');
	
	# Whether to hide the "Login with OpenID link" link; set to true if you already have this link in your skin.
	
	$wgHideOpenIDLoginLink = false;
	
	# Location of the OpenID login logo. You can copy this to your server if you want.
	
	$wgOpenIDLoginLogoUrl = 'http://www.openid.net/login-bg.gif';
	
	# Whether to show the OpenID identity URL on a user's home page. Possible values are 'always', 'never', or 'user' 
	# 'user' lets the user decide.
	
	$wgOpenIDShowUrlOnUserPage = 'user';
	
	function setupOpenID() {
		global $wgMessageCache, $wgOut, $wgRequest, $wgHooks;

		$wgMessageCache->addMessages(array('openidlogin' => 'Login with OpenID',
										   'openidfinish' => 'Finish OpenID login',
										   'openidserver' => 'OpenID server',
										   'openidconvert' => 'OpenID converter',
										   'openidlogininstructions' => 'Enter your OpenID identifier to log in:',
										   'openiderror' => 'Verification error',
										   'openiderrortext' => 'An error occured during verification of the OpenID URL.',
										   'openidconfigerror' => 'OpenID Configuration Error',
										   'openidconfigerrortext' => 'The OpenID storage configuration for this wiki is invalid.  Please consult this site\'s administrator.',
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
										   'openidalreadyloggedin' => '<strong>User $1, you are already logged in!</strong>',
										   'tog-hideopenid' => 'Hide your <a href="http://openid.net/">OpenID</a> on your user page, if you log in with OpenID.',
										   ));

		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDLogin'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDFinish'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDServer'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDConvert'));
		SpecialPage::AddPage(new UnlistedSpecialPage('OpenIDXRDS'));

		$wgHooks['PersonalUrls'][] = 'OpenIDPersonalUrls';
		$wgHooks['UserToggles'][] = 'OpenIDUserToggles';

		$wgOut->addHeadItem('openidloginstyle', OpenIDLoginStyle());
		
		$action = $wgRequest->getText('action', 'view');

		if ($action == 'view') {

			$title = $wgRequest->getText('title');

			if (!isset($title) || strlen($title) == 0) {
				# If there's no title, and Cache404 is in use, check using its stuff
				if (defined('CACHE404_VERSION')) {
					if ($_SERVER['REDIRECT_STATUS'] == 404) {
						$url = getRedirectUrl($_SERVER);
						if (isset($url)) {
							$title = cacheUrlToTitle($url);
						}
					}
				} else {
					$title = wfMsg('mainpage');
				}
			}

  		    $nt = Title::newFromText($title);

		    // If the page being viewed is a user page,
		    // generate the openid.server META tag and output
		    // the X-XRDS-Location.  See the OpenIDXRDS
		    // special page for the XRDS output / generation
		    // logic.
		    if ($nt &&
				($nt->getNamespace() == NS_USER) &&
				strpos($nt->getText(), '/') === false)
			{
				$user = User::newFromName($nt->getText());
				if ($user && $user->getID() != 0) {
					$openid = OpenIdGetUserUrl($user);
					if (isset($openid) && strlen($openid) != 0) {
						global $wgOpenIDShowUrlOnUserPage;
						
						if ($wgOpenIDShowUrlOnUserPage == 'always' ||
							($wgOpenIDShowUrlOnUserPage == 'user' && !$user->getOption('hideopenid')))
						  {
								global $wgOpenIDLoginLogoUrl;
						
								$url = OpenIDToUrl($openid);
								$disp = htmlspecialchars($openid);
								$wgOut->setSubtitle("<span class='subpages'>" .
													"<img src='$wgOpenIDLoginLogoUrl' alt='OpenID' />" .
													"<a href='$url'>$disp</a>" .
													"</span>");
						  }
					} else {
						$wgOut->addLink(array('rel' => 'openid.server',
											  'href' => OpenIDServerUrl()));
						$rt = Title::makeTitle(NS_SPECIAL, 'OpenIDXRDS/'.$user->getName());
						$wgOut->addMeta('http:X-XRDS-Location', $rt->getFullURL());
						header('X-XRDS-Location', $rt->getFullURL());
					}
				}
		    }
		}

		// Verify the config file settings.  FIXME: How to
		// report error?
		global $wgOpenIDServerStorePath, $wgOpenIDServerStoreType,
		  $wgOpenIDConsumerStorePath, $wgOpenIDConsumerStoreType;

		if ($wgOpenIDConsumerStoreType == 'file') {
		    assert($wgOpenIDConsumerStorePath != false);
		}

		if ($wgOpenIDServerStoreType == 'file') {
		    assert($wgOpenIDServerStorePath != false);
		}
	}

	function getOpenIDStore($storeType, $prefix, $options) {
	    global $wgOut;

	    switch ($storeType) {
		 case 'memcached':
		 case 'memc':
			return new OpenID_MemcStore($prefix);

		 case 'file':
			# Auto-create path if it doesn't exist
			if (!is_dir($options['path'])) {
				if (!mkdir($options['path'], 0770, true)) {
					$wgOut->errorPage('openidconfigerror', 'openidconfigerrortext');
					return NULL;
				}
			}
			return new Auth_OpenID_FileStore($options['path']);

		 default:
			$wgOut->errorPage('openidconfigerror', 'openidconfigerrortext');
	    }
	}

	function OpenIDXriBase($xri) {
		if (substr($xri, 0, 6) == 'xri://') {
			return substr($xri, 6);
		} else {
			return $xri;
		}
	}

	function OpenIDXriToUrl($xri) {
		return 'http://xri.net/' . OpenIDXriBase($xri);
	}

	function OpenIDToUrl($openid) {
		/* ID is either an URL already or an i-name */
        if (Services_Yadis_identifierScheme($openid) == 'XRI') {
			return OpenIDXriToUrl($openid);
		} else {
			return $openid;
		}
	}
	
	function OpenIDPersonalUrls(&$personal_urls, &$title) {
		global $wgHideOpenIDLoginLink, $wgUser, $wgLang;
		
		if (!$wgHideOpenIDLoginLink && $wgUser->getID() == 0) {
			$sk = $wgUser->getSkin();
			$returnto = ($title->getPrefixedUrl() == $wgLang->specialPage( 'Userlogout' )) ?
			  '' : ('returnto=' . $title->getPrefixedURL());
			  
			$personal_urls['openidlogin'] = array(
					'text' => wfMsg('openidlogin'),
					'href' => $sk->makeSpecialUrl( 'OpenIDLogin', $returnto ),
					'active' => $title->isSpecial( 'OpenIDLogin' )
				);
		}
		
		return true;
	}
	
	function OpenIDUserToggles(&$extraToggles) {
		global $wgOpenIDShowUrlOnUserPage;
		
		if ($wgOpenIDShowUrlOnUserPage == 'user') {
			$extraToggles[] = 'hideopenid';
		}
		
		return true;
	}
	
	function OpenIDLoginStyle() {
		global $wgOpenIDLoginLogoUrl;
		return <<<EOS
<style type='text/css'>
li#pt-openidlogin {
  background: url($wgOpenIDLoginLogoUrl) top left no-repeat;
  padding-left: 20px;
  text-transform: none;
}
</style>
EOS;
	}
}

