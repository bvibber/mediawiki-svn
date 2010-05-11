<?php
/** Extension:NewUserMessage
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author [http://www.organicdesign.co.nz/nad User:Nad]
 * @license GNU General Public Licence 2.0 or later
 * @copyright 2007-10-15 [http://www.organicdesign.co.nz/nad User:Nad]
 * @copyright 2009 Siebrand Mazeland
 */

if ( !defined( 'MEDIAWIKI' ) )
	die( 'Not an entry point.' );

class NewUserMessage {
	/*
	 * Add the template message if the users talk page does not already exist
	 */
	static function createNewUserMessage( $user ) {
		$talk = $user->getTalkPage();

		if ( !$talk->exists() ) {
			global $wgUser;

			wfLoadExtensionMessages( 'NewUserMessage' );

			$editSummary = wfMsgForContent( 'newuseredit-summary' );

			// Create a user object for the editing user and add it to the
			// database if it is not there already
			$editor = User::newFromName( wfMsgForContent( 'newusermessage-editor' ) );
			if ( !$editor->isLoggedIn() ) {
				$editor->addToDatabase();
			}

			$signatures = wfMsgForContent( 'newusermessage-signatures' );
			$signature = null;

			if ( !wfEmptyMsg( 'newusermessage-signatures', $signatures ) ) {
				$pattern = '/^\* ?(.*?)$/m';
				preg_match_all( $pattern, $signatures, $signatureList, PREG_SET_ORDER );
				if ( count( $signatureList ) > 0 ) {
					$rand = rand( 0, count( $signatureList ) - 1 );
					$signature = $signatureList[$rand][1];
				}
			}

			// Add (any) content to [[MediaWiki:Newusermessage-substitute]] to substitute the welcome template.
			$substitute = wfMsgForContent( 'newusermessage-substitute' );

			if ( wfRunHooks( 'CreateNewUserMessage', array( $user, $editor, $editSummary, $substitute, $signature ) ) ) {
				$templateTitleText = wfMsg( 'newusermessage-template' );
				$templateTitle = Title::newFromText( $templateTitleText );
				if ( !$templateTitle ) {
					wfDebug( __METHOD__ . ": invalid title in newusermessage-template\n" );
					return true;
				}

				if ( $templateTitle->getNamespace() == NS_TEMPLATE ) {
					$templateTitleText = $templateTitle->getText();
				}

				$realName = $user->getRealName();
				$name = $user->getName();
				$article = new Article( $talk );

				if ( $substitute ) {
					$text = "{{subst:{$templateTitleText}|$name|$realName}}";
				} else {
					$text = "{{{$templateTitleText}|$name|$realName}}";
				}

				if ( $signature ) {
					$text .= "\n-- {$signature} ~~~~~";
				}

				self::writeWelcomeMessage( $user, $article,  $text, $editSummary, $editor );
			}
		}
		return true;
	}

	static function createNewUserMessageAutoCreated( $user ) {
		global $wgNewUserMessageOnAutoCreate;

		if ( $wgNewUserMessageOnAutoCreate ) {
			NewUserMessage::createNewUserMessage( $user );
		}

		return true;
	}

	static function onUserGetReservedNames( &$names ) {
		wfLoadExtensionMessages( 'NewUserMessage' );
		$names[] = 'msg:newusermessage-editor';
		return true;
	}

	/**
	 * Create a page with text
	 * @param $user User object: user that was just created
	 * @param $article Article object: the article where $text is to be put
	 * @param $text String: text to put in $article
	 * @param $summary String: edit summary text
	 * @param $editor User object: user that will make the edit
	 */
	public static function writeWelcomeMessage( $user, $article, $text, $summary, $editor ) {
		global $wgNewUserMinorEdit, $wgNewUserSuppressRC;

		wfLoadExtensionMessages( 'NewUserMessage' );

		$flags = EDIT_NEW;
		if ( $wgNewUserMinorEdit ) $flags = $flags | EDIT_MINOR;
		if ( $wgNewUserSuppressRC ) $flags = $flags | EDIT_SUPPRESS_RC;

		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		$good = true;

		try {

			$article->doEdit( $text, $summary, $flags, false, $editor );
		} catch ( DBQueryError $e ) {
			$good = false;
		}

		if ( $good ) {
			// Set newtalk with the right user ID
			$user->setNewtalk( true );
			$dbw->commit();
		} else {
			// The article was concurrently created
			wfDebug( __METHOD__ . ": the article has already been created despite !\$talk->exists()\n" );
			$dbw->rollback();
		}
	}

	/**
	 * Returns the text contents of a template page set in given key contents
	 * Returns empty string if no text could be retrieved.
	 * @param $key String: message key that should contain a template page name
	 */
	public static function getTextForPageInKey( $key ) {
		$templateTitleText = wfMsgForContent( $key );
		$templateTitle = Title::newFromText( $templateTitleText );

		// Do not continue if there is no valid subject title
		if ( !$templateTitle ) {
			wfDebug( __METHOD__ . ": invalid title in " . $key . "\n" );
			return '';
		}

		// Get the subject text from the page
		if ( $templateTitle->getNamespace() == NS_TEMPLATE ) {
			return $templateTitle->getText();
		} else {
			// There is no subject text
			wfDebug( __METHOD__ . ": " . $templateTitleText . " must be in NS_TEMPLATE\n" );
			return '';
		}
	}
}
