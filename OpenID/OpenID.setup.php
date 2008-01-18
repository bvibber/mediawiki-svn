<?php
/**
 * OpenID.setup.php -- Make MediaWiki an OpenID consumer and server
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * Copyright 2007,2008 Evan Prodromou <evan@prodromou.name>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@prodromou.name>
 * @addtogroup Extensions
 */

if (!defined('MEDIAWIKI')) {
	exit( 1 );
}

define('MEDIAWIKI_OPENID_VERSION', '0.8.0');

# CONFIGURATION VARIABLES

# Whether to hide the "Login with OpenID link" link; set to true if you already have this link in your skin.
	
$wgHideOpenIDLoginLink = false;

# Location of the OpenID login logo. You can copy this to your server if you want.
	
$wgOpenIDLoginLogoUrl = 'http://www.openid.net/login-bg.gif';
	
# Whether to show the OpenID identity URL on a user's home page. Possible values are 'always', 'never', or 'user' 
# 'user' lets the user decide.
	
$wgOpenIDShowUrlOnUserPage = 'user';

# These are trust roots that we don't bother asking users
# whether the trust root is allowed to trust; typically
# for closely-linked partner sites.

$wgOpenIDServerForceAllowTrust = array();

# Where to store transitory data. Can be 'memc' for the $wgMemc
# global caching object, or 'file' if caching is turned off
# completely and you need a fallback.

$wgOpenIDServerStoreType = ($wgMainCacheType == CACHE_NONE) ? 'file' : 'memc';

# If the store type is set to 'file', this is is the name of a
# directory to store the data in.

$wgOpenIDServerStorePath = ($wgMainCacheType == CACHE_NONE) ? "/tmp/$wgDBname/openidserver/" : NULL;

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

# END CONFIGURATION VARIABLES

$wgExtensionFunctions[] = 'setupOpenID';

function setupOpenID() {
	global $wgHooks, $wgAutoloadClasses, $wgSpecialPages, $wgExtensionCredits;

	# Autoload for special pages

	foreach (array('Login', 'Finish', 'Convert', 'Server', 'XRDS') as $sub) {
		$wgAutoloadClasses['SpecialOpenID' . $sub] = dirname(__FILE__) . '/SpecialOpenID' . $sub . '.body.php';
		$wgSpecialPages['OpenID'.$sub] = array('SpecialOpenID'.$sub);
	}

	# Autoload common parent with utility methods

	$wgAutoloadClasses['SpecialOpenID'] = dirname(__FILE__) . '/SpecialOpenID.body.php';

	# Autoload storage classes

	$wgAutoloadClasses['OpenID_MemcStore'] = dirname(__FILE__) . '/MemcStore.php';

	$wgHooks['PersonalUrls'][] = 'OpenIDPersonalUrls';
	$wgHooks['UserToggles'][] = 'OpenIDUserToggles';
	$wgHooks['ArticleViewHeader'][] = 'OpenIDArticleViewHeader';
	# Load the i18n messages
	$wgHooks['LoadAllMessages'][] = 'SpecialOpenID::loadMessages';
	# Add any aliases for the special page.
	$wgHooks['LanguageGetSpecialPageAliases'][] = 'SpecialOpenID::LocalizedPageName'; 
	# Typo in versions of MW earlier than 1.11.x (?)
	$wgHooks['LangugeGetSpecialPageAliases'][] = 'SpecialOpenID::LocalizedPageName'; # Add any aliases for the special page.

	$wgExtensionCredits['other'][] = array('name' => 'OpenID',
										   'version' => MEDIAWIKI_OPENID_VERSION,
										   'author' => 'Evan Prodromou',
										   'url' => 'http://www.mediawiki.org/wiki/Extension:OpenID',
										   'description' => 'lets users login to the wiki with an ' .
										   ' [http://openid.net/ OpenID] ' .
										   'and login to other OpenID-aware Web sites ' . 
										   'with their wiki user account');
}

# Hook is called whenever an article is being viewed

function OpenIDArticleViewHeader(&$article, &$outputDone, &$pcache ) {
	global $wgOut;
	
	$nt = $article->getTitle();
		
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
			$openid = SpecialOpenID::getUserUrl($user);
			if (isset($openid) && strlen($openid) != 0) {
				global $wgOpenIDShowUrlOnUserPage;
				
				if ($wgOpenIDShowUrlOnUserPage == 'always' ||
					($wgOpenIDShowUrlOnUserPage == 'user' && !$user->getOption('hideopenid')))
				{
					global $wgOpenIDLoginLogoUrl;
					
					$url = SpecialOpenID::OpenIDToUrl($openid);
					$disp = htmlspecialchars($openid);
					$wgOut->setSubtitle("<span class='subpages'>" .
										"<img src='$wgOpenIDLoginLogoUrl' alt='OpenID' />" .
										"<a href='$url'>$disp</a>" .
										"</span>");
				}
			} else {
				$st = Title::makeTitleSafe(NS_SPECIAL, 'OpenIDServer');
				$wgOut->addLink(array('rel' => 'openid.server',
									  'href' => $st->getFullURL()));
				$wgOut->addLink(array('rel' => 'openid2.provider',
									  'href' => $st->getFullURL()));
				$rt = Title::makeTitle(NS_SPECIAL, 'OpenIDXRDS/'.$user->getName());
				$wgOut->addMeta('http:X-XRDS-Location', $rt->getFullURL());
				header('X-XRDS-Location', $rt->getFullURL());
			}
		}
	}
	
	return TRUE;
}

function OpenIDPersonalUrls(&$personal_urls, &$title) {
	global $wgHideOpenIDLoginLink, $wgUser, $wgLang, $wgOut;
	
	if (!$wgHideOpenIDLoginLink && $wgUser->getID() == 0) {
		$wgOut->addHeadItem('openidloginstyle', OpenIDLoginStyle());
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