<?php

/**
 * Extension to to retrieve information about a user such as email address and ID.
 *
 * @addtogroup Extensions
 * @author Tim Starling
 * @copyright 2006 Tim Starling
 * @licence GNU General Public Licence
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionFunctions[] = 'wfSetupLookupUser';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Lookup User',
	'author' => 'Tim Starling',
	'description' => 'Retrieve information about a user such as email address and ID',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['LookupUser'] = $dir . 'LookupUser.i18n.php';

$wgSpecialPages['LookupUser'] = 'LookupUserPage';
$wgAvailableRights[] = 'lookupuser';

function wfSetupLookupUser() {
	global $IP;

	class LookupUserPage extends SpecialPage {
		function __construct() {
		SpecialPage::SpecialPage( 'LookupUser' );
		}

		function getDescription() {
			return wfMsg( 'lookupuser' );
		}

		function execute( $subpage ) {
			global $wgRequest, $wgUser;
			wfLoadExtensionMessages( 'LookupUser' );

			$this->setHeaders();

			if ( !$wgUser->isAllowed( 'lookupuser' ) ) {
				$this->displayRestrictionError();
				return;
			}

			if ( $subpage ) {
				$target = $subpage;
			} else {
				$target = $wgRequest->getText( 'target' );
			}
			$this->showForm( $target );
			if ( $target ) {
				$this->showInfo( $target );
			}
		}

		function showForm( $target ) {
			global $wgScript, $wgOut;
			$title = htmlspecialchars( $this->getTitle()->getPrefixedText() );
			$action = htmlspecialchars( $wgScript );
			$target = htmlspecialchars( $target );
			$username = htmlspecialchars( wfMsg( 'lookupuser_username' ) );

			$wgOut->addHTML( <<<EOT
<form method="get" action="$action">
<input type="hidden" name="title" value="{$title}" />
<table border="0">
<tr>
<td align="right">$username</td>
<td align="left"><input type="text" size="50" name="target" value="$target" />
</tr>
<tr>
<td colspan="2" align="center"><input type="submit" name="submit" value="OK" /></td>
</tr>
</table>
</form>
EOT
			);
		}

		function showInfo( $target ) {
			global $wgOut, $wgLang;
			$user = User::newFromName( $target );
			if ( $user->getId() == 0 ) {
				$wgOut->addWikiText( wfMsg( 'lookupuser_nonexistent', $target ) );
			} else {
				$authTs = $user->getEmailAuthenticationTimestamp();
				if ( $authTs ) {
					$authenticated = wfMsg( 'lookupuser_authenticated', $wgLang->timeanddate( $authTs ) );
				} else {
					$authenticated = wfMsg( 'lookupuser_not_authenticated' );
				}
				$optionsString = '';
				foreach ( $user->mOptions as $name => $value ) {
					$optionsString .= "$name = $value <br />";
				}
				$wgOut->addWikiText( wfMsg( 'lookupuser_info',
					$user->getName(),
					$user->getId(),
					$user->getEmail(),
					$user->getRealName(),
					$wgLang->timeanddate( $user->mRegistration ),
					$wgLang->timeanddate( $user->mTouched ),
					$authenticated,
					$optionsString
				));
			}
		}
	}
}
