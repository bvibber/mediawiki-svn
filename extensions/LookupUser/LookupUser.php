<?php

if ( !defined( 'MEDIAWIKI' ) ):
?>
<html><head><title>LookupUser</title></head>
<body>
<h1>LookupUser</h1>
<p>LookupUser is a special page to retrieve information about a user such as email address and ID.</p>
</body>
</html>
<?php
exit(1);
endif;

$wgExtensionFunctions[] = 'wfSetupLookupUser';
$wgAvailableRights[] = 'lookupuser';

function wfSetupLookupUser() {
	global $wgMessageCache, $IP;

	$wgMessageCache->addMessages( array(
		'lookupuser' => 'Look up user info',
		'lookupuser_username' => 'Username:',
		'lookupuser_nonexistent' => 'User does not exist',
		'lookupuser_authenticated' => 'authenticated on $1',
		'lookupuser_not_authenticated' => 'not authenticated',
		'lookupuser_info' => 
'* Name: $1
* User ID: $2
* Email address: $3
* Real name: $4
* Registration date: $5
* User record last touched: $6
* Email authentication: $7

User options:

$8
',
	));

	require_once( "$IP/includes/SpecialPage.php" );
	
	class LookupUserPage extends SpecialPage {
		function __construct() {
			parent::__construct( 'LookupUser', 'lookupuser' );
		}

		function execute( $subpage ) {
			global $wgRequest, $wgUser;

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
			global $wgOut;
			$title = $this->getTitle();
			$action = htmlspecialchars( $title->getLocalURL() );
			$target = htmlspecialchars( $target );
			$username = htmlspecialchars( wfMsg( 'lookupuser_username' ) );

			$wgOut->addHTML( <<<EOT
<form method="get" action="$action">
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

	SpecialPage::addPage( new LookupUserPage );
}
?>
