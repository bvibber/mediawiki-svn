<?php

/**
 * Special page to allow local bureaucrats to grant/revoke the bot flag
 * for a particular user
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'SpecialPage.php' );
	$wgExtensionFunctions[] = 'efMakeBot';
	$wgAvailableRights[] = 'makebot';
	$wgExtensionCredits['specialpage'][] = array( 'name' => 'MakeBot', 'author' => 'Rob Church' );
	
	/**
	 * Determines who can use the extension; as a default, bureaucrats are permitted
	 */
	$wgGroupPermissions['bureaucrat']['makebot'] = true;
	
	/**
	 * Toggles whether or not a bot flag can be given to a user who is also a sysop or bureaucrat
	 */
	$wgMakeBotPrivileged = false;
	
	/**
	 * Populate the message cache and register the special page
	 */
	function efMakeBot() {
		global $wgMessageCache;
		# TODO: Messages
		$wgMessageCache->addMessage( 'makebot', 'Grant/revoke bot flag' );
		$wgMessageCache->addMessage( 'makebot-header', "'''A local bureaucrat can use this page to grant or revoke a [[Help:Bot|bot flag]] to another user account.'''<br />This should be done in accordance with applicable policies." );
		$wgMessageCache->addMessage( 'makebot-username', 'Username:' );
		$wgMessageCache->addMessage( 'makebot-search', 'Go' );
		$wgMessageCache->addMessage( 'makebot-isbot', '[[User:$1|$1]] has a bot flag.' );
		$wgMessageCache->addMessage( 'makebot-notbot', '[[User:$1|$1]] does not have a bot flag.' );
		$wgMessageCache->addMessage( 'makebot-privileged', '[[User:$1|$1]] has [[Special:Listadmins|administrator or bureaucrat privileges]], and cannot be granted a bot flag.' );
		$wgMessageCache->addMessage( 'makebot-grant', 'Grant flag' );
		$wgMessageCache->addMessage( 'makebot-revoke', 'Revoke flag' );
		$wgMessageCache->addMessage( 'makebot-granted', '[[User:$1|$1]] now has a bot flag.' );
		$wgMessageCache->addMessage( 'makebot-revoked', '[[User:$1|$1]] no longer has a bot flag.' );
		SpecialPage::addPage( new MakeBot() );
	}
	
	class MakeBot extends SpecialPage {
	
		var $target = '';
	
		/**
		 * Constructor
		 */
		function MakeBot() {
			SpecialPage::SpecialPage( 'Makebot', 'makebot' );
		}
		
		/**
		 * Main execution function
		 * @param $par Parameters passed to the page (will be ignored for now)
		 */
		function execute( $par ) {
			global $wgRequest, $wgOut, $wgMakeBotPrivileged, $wgUser;
			$this->setHeaders();
			$this->target = $wgRequest->getText( 'username', '' );
			
			$wgOut->addWikiText( wfMsg( 'makebot-header' ) );
			$wgOut->addHtml( $this->makeSearchForm() );
			
			if( $this->target != '' ) {
				$wgOut->addHtml( wfElement( 'p', NULL, NULL ) );
				$user = User::newFromName( $this->target );
				if( is_object( $user ) && !is_null( $user ) ) {
					$user->loadFromDatabase();
					# Valid username, check existence
					if( $user->getID() ) {
						if( $wgRequest->getVal( 'dosearch' ) ) {
							# Exists, check botness
							if( in_array( 'bot', $user->mGroups ) ) {
								# Has a bot flag
								$wgOut->addWikiText( wfMsg( 'makebot-isbot', $user->getName() ) );
								$wgOut->addHtml( $this->makeGrantForm( true, false ) );
							} else {
								# Not a bot; check other privs
								if( !$wgMakeBotPrivileged && ( in_array( 'sysop', $user->mGroups ) || in_array( 'bureaucrat', $user->mGroups ) ) ) {
									# Account is privileged and can't be given a bot flag
									$wgOut->addWikiText( wfMsg( 'makebot-privileged', $user->getName() ) );
								} else {
									# Can proceed to promotion
									$wgOut->addWikiText( wfMsg( 'makebot-notbot', $user->getName() ) );
									$wgOut->addHtml( $this->makeGrantForm( false, true ) );
								}
							}
						} elseif( $wgRequest->getVal( 'grant' ) && $wgUser->matchEditToken( $wgRequest->getText( 'token' ), 'makebot' ) ) {
							# Grant the flag
							$user->addGroup( 'bot' );
							$wgOut->addWikiText( wfMsg( 'makebot-granted', $user->getName() ) );
						} elseif( $wgRequest->getVal( 'revoke' ) && $wgUser->matchEditToken( $wgRequest->getText( 'token' ), 'makebot' ) ) {
							# Revoke the flag
							$user->removeGroup( 'bot' );
							$wgOut->addWikiText( wfMsg( 'makebot-revoked', $user->getName() ) );
						}
					} else {
						# Doesn't exist
						$wgOut->addHtml( wfMsgHtml( 'nosuchusershort', htmlspecialchars( $this->target ) ) );
					}
				} else {
					# Invalid username
				}
			}
			
		}
		
		/**
		 * Produce a form to allow for entering a username
		 * @return string
		 */
		function makeSearchForm() {
			$thisTitle = Title::makeTitle( NS_SPECIAL, $this->getName() );
			$form  = wfElement( 'form', array( 'method' => 'post', 'action' => $thisTitle->escapeLocalUrl ), NULL );
			$form .= wfElement( 'label', array( 'for' => 'username' ), wfMsgHtml( 'makebot-username' ) ) . ' ';
			$form .= wfElement( 'input', array( 'type' => 'text', 'name' => 'username', 'id' => 'username', 'value' => htmlspecialchars( $this->target ) ), '' ) . ' ';
			$form .= wfElement( 'input', array( 'type' => 'submit', 'name' => 'dosearch', 'value' => wfMsgHtml( 'makebot-search' ) ), '' );
			$form .= wfCloseElement( 'form' );
			return( $form );
		}
		
		/**
		 * Produce a form to allow granting or revocation of the flag
		 * @param $grant Enable grant button
		 * @param $revoke Enable revoke button
		 * @return string
		 */
		function makeGrantForm( $grant, $revoke ) {
			global $wgUser;
			$thisTitle = Title::makeTitle( NS_SPECIAL, $this->getName() );
			$form  = wfElement( 'form', array( 'method' => 'post', 'action' => $thisTitle->escapeLocalUrl ), NULL );
			# Add the buttons
			foreach( explode( ' ', 'grant revoke' ) as $button ) {
				$attribs = array( 'type' => 'submit', 'name' => $button, 'value' => wfMsgHtml( 'makebot-' . $button ) );
				if( $$button )
					$attribs['disabled'] = 'disabled';
				$form .= wfElement( 'input', $attribs, '' );
			}
			# Username
			$form .= wfElement( 'input', array( 'type' => 'hidden', 'name' => 'username', 'value' => $this->target ), '' );
			# Edit token
			$form .= wfElement( 'input', array( 'type' => 'hidden', 'name' => 'token', 'value' => $wgUser->editToken( 'makebot' ) ), '' );
			$form .= wfCloseElement( 'form' );
			return( $form );
		}
	
	}
	
} else {

	echo( "This file is an extension to the MediaWiki software and cannot be executed standalone.\n" );
	die( -1 );

}