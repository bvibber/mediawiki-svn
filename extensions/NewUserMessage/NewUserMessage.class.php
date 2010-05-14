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
	 * Add the message if the users talk page does not already exist
	 * @param $user User object
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

			self::setupAndLeaveMessage( $user, $editor, $editSummary, $substitute, $signature );
		}
		return true;
	}

	/**
	 * Take care of some housekeeping before leaving the actual message
	 * @param $user User the user object who's talk page is being created
	 * @param $editor User the user that we'll use to leave the message
	 * @param $editSummary String the edit summary
	 * @param $substitute Bool Template text needs substitution
	 * @param $signature String the signature
	 */
	static function setupAndLeaveMessage( $user, $editor, $editSummary, $substitute, $signature ) {
		$talk = $user->getTalkPage();
		$article = new Article( $talk );

		$subject = '';
		if ( wfRunHooks( 'SetupNewUserMessageSubject', array( &$subject ) ) ) {
			$subject = wfMsg( 'newusermessage-template-subject' );
		}

		$text = '';
		if ( wfRunHooks( 'SetupNewUserMessageBody', array( &$text ) ) ) {
			$text = wfMsg( 'newusermessage-template-body' );

			$template = Title::newFromText( $text );
			if ( !$template ) {
				wfDebug( __METHOD__ . ": invalid title in newusermessage-template-body\n" );
				return;
			}

			if ( $template->getNamespace() == NS_TEMPLATE ) {
				$text = $template->getText();
			}
		}

		if ( $substitute ) {
			$subject = self::substString( $subject, $user, "preparse" );
			$text = self::substString( $text, $user );
		}

		global $wgNewUserMinorEdit, $wgNewUserSuppressRC;

		$flags = EDIT_NEW;
		if ( $wgNewUserMinorEdit ) $flags = $flags | EDIT_MINOR;
		if ( $wgNewUserSuppressRC ) $flags = $flags | EDIT_SUPPRESS_RC;

		return $user->leaveUserMessage( $subject, $text, $signature, $editSummary, $editor, $flags );
	}

	static private function substString( $str, $user, $preparse = null ) {
		$realName = $user->getRealName();
		$name = $user->getName();

		$str = "{{subst:{{$str}}}|realName=$realName|name=$name}}";

		if ( $preparse ) {
			/* Create the final subject text.
			 * Always substituted and processed by parser to avoid awkward subjects
			 */
			$parser = new Parser;
			$parser->setOutputType( 'wiki' );
			$parserOptions = new ParserOptions;

			$str = $parser->preSaveTransform($str, $talk /* as dummy */,
				$editor, $parserOptions );
		}

		return $str;
	}


	/**
	 * Hook function to create a message on an auto-created user
	 * @param $user User object of the user
	 * @return bool
	 */
	static function createNewUserMessageAutoCreated( $user ) {
		global $wgNewUserMessageOnAutoCreate;

		if ( $wgNewUserMessageOnAutoCreate ) {
			NewUserMessage::createNewUserMessage( $user );
		}

		return true;
	}

	/**
	 * Hook function to provide a reserved name
	 * @param $names Array
	 */
	static function onUserGetReservedNames( &$names ) {
		wfLoadExtensionMessages( 'NewUserMessage' );
		$names[] = 'msg:newusermessage-editor';
		return true;
	}
}
