<?php
/**
 * MicroID.php -- Generate MicroID meta info for user pages
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
 * @addtogroup Extensions
 */

if (defined('MEDIAWIKI')) {

	define('MEDIAWIKI_MICROID_VERSION', '0.1');

	$wgExtensionFunctions[] = 'setupMicroID';
	$wgExtensionCredits['other'][] = array(
		'name' => 'MicroID',
		'version' => MEDIAWIKI_MICROID_VERSION,
		'author' => 'Evan Prodromou',
		'url' => 'http://www.mediawiki.org/wiki/MicroID_extension',
		'description' => 'adds a [http://www.microid.org/ MicroID] to user pages to confirm account with external services',
	);

	function setupMicroID() {

		global $wgOut, $wgRequest, $wgMessageCache, $wgHooks;

		$wgMessageCache->addMessages(array(
			'tog-microid' => 'Publish a <a href="http://microid.org/">MicroID</a> to confirm account with external services',
		));

		$wgHooks['UserToggles'][] = 'MicroIDUserToggle';

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

			// If the page being viewed is a user page...

			if ($nt &&
				($nt->getNamespace() == NS_USER) &&
				strpos($nt->getText(), '/') === false)
			{
				// If the user qualifies...
				wfDebug("MicroID: on User page " . $nt->getText() . "\n");

				$user = User::newFromName($nt->getText());

				if ($user &&                             // got a user
					$user->getID() != 0 &&               // they're real
					$user->getEmail() &&                 // they've added an email address
					$user->isEmailConfirmed() &&         // it's been confirmed
					$user->getOption('microid'))         // they authorize microid
				{
					wfDebug("MicroID: on User page " . $nt->getText() . "\n");
					$wgOut->addMeta('microid', MakeMicroID($user->getEmail(), $nt->getFullURL()));
				}
		    }
		}
	}

	function MakeMicroID($email, $url) {
		return sha1( sha1( 'mailto:' . $email ) . sha1( $url ) );
	}

	function MicroIDUserToggle(&$arr) {
		$arr[] = 'microid';
		return true;
	}
}
