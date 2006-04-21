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
	require_once( 'LogPage.php' );
	require_once( 'SpecialLog.php' );
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
		global $wgHooks, $wgMessageCache;
		# Hooks for auditing
		$wgHooks['LogPageValidTypes'][] = 'efMakeBotAddLogType';
		$wgHooks['LogPageLogName'][] = 'efMakeBotAddLogName';
		$wgHooks['LogPageLogHeader'][] = 'efMakeBotAddLogHeader';
		$wgHooks['LogPageActionText'][] = 'efMakeBotAddActionText';
		# Basic messages
		$wgMessageCache->addMessage( 'makebot', 'Grant or revoke bot flag' );
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
		# Audit trail messages
		$wgMessageCache->addMessage( 'makebot-logpage', 'Bot status log' );
		$wgMessageCache->addMessage( 'makebot-logpagetext', 'This is a log of changes to users\' [[Help:Bot|bot]] status.' );
		$wgMessageCache->addMessage( 'makebot-logentrygrant', 'granted bot flag to [[$1]]' );
		$wgMessageCache->addMessage( 'makebot-logentryrevoke', 'removed bot flag from [[$1]]' );
		# Register page		
		SpecialPage::addPage( new MakeBot() );
	}
	
	/**
	 * Audit trail functions
	 */
	
	function efMakeBotAddLogType( &$types ) {
		if ( !in_array( 'makebot', $types ) )
			$types[] = 'makebot';
		return( true );
	}
	
	function efMakeBotAddLogName( &$names ) {
		$names['makebot'] = 'makebot-logpage';
		return( true );
	}
	
	function efMakeBotAddLogHeader( &$headers ) {
		$headers['makebot'] = 'makebot-logpagetext';
		return( true );
	}
	
	function efMakeBotAddActionText( &$actions ) {
		$actions['makebot/grant'] = 'makebot-logentrygrant';
		$actions['makebot/revoke'] = 'makebot-logentryrevoke';
		return( true );
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
						if( $wgRequest->getVal( 'dosearch' ) || !$wgRequest->wasPosted() ) {
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
						} elseif( $wgRequest->getVal( 'grant' ) && $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'token' ), 'makebot' ) ) {
							# Grant the flag
							$user->addGroup( 'bot' );
							$this->addLogItem( 'grant', $wgUser, $user );
							$wgOut->addWikiText( wfMsg( 'makebot-granted', $user->getName() ) );
						} elseif( $wgRequest->getVal( 'revoke' ) && $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'token' ), 'makebot' ) ) {
							# Revoke the flag
							$user->removeGroup( 'bot' );
							$this->addLogItem( 'revoke', $wgUser, $user );
							$wgOut->addWikiText( wfMsg( 'makebot-revoked', $user->getName() ) );
						}
						# Show log entries
						$this->showLogEntries( $user );
					} else {
						# Doesn't exist
						$wgOut->addWikiText( wfMsg( 'nosuchusershort', htmlspecialchars( $this->target ) ) );
					}
				} else {
					# Invalid username
					$wgOut->addWikiText( wfMsg( 'noname' ) );
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
	
		/**
		 * Add logging entries for the specified action
		 * @param $type Either grant or revoke
		 * @param $initiator User performing the action
		 * @param $target User receiving the action
		 */
		function addLogItem( $type, &$initiator, &$target ) {
			$log = new LogPage( 'makebot' );
			$targetPage = $target->getUserPage();
			$log->addEntry( $type, $targetPage, '' );
		}
		
		/**
		 * Show the bot status log entries for the specified user
		 * @param $user User to show the log for
		 */
		function showLogEntries( &$user ) {
			global $wgOut;
			$title = $user->getUserPage();
			$wgOut->addHtml( wfElement( 'h2', NULL, htmlspecialchars( LogPage::logName( 'makebot' ) ) ) );
			$logViewer = new LogViewer( new LogReader( new FauxRequest( array( 'page' => $title->getPrefixedText(), 'type' => 'makebot' ) ) ) );
			$logViewer->showList( $wgOut );
		}
	
	}
	
} else {

	echo( "This file is an extension to the MediaWiki software and cannot be executed standalone.\n" );
	die( -1 );

}