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

	/**
	 * Produce the editor for new user messages.
	 * @returns User
	 */
	static function fetchEditor() {
		// Create a user object for the editing user and add it to the
		// database if it is not there already
		$editor = User::newFromName( wfMsgForContent( 'newusermessage-editor' ) );
		if ( !$editor->isLoggedIn() ) {
			$editor->addToDatabase();
		}

		return $editor;
	}

	/**
	 * Produce a (possibly random) signature.
	 * @returns String
	 */
	static function fetchSignature() {
		$signatures = wfMsgForContent( 'newusermessage-signatures' );
		$signature = '';

		if ( !wfEmptyMsg( 'newusermessage-signatures', $signatures ) ) {
			$pattern = '/^\* ?(.*?)$/m';
			preg_match_all( $pattern, $signatures, $signatureList, PREG_SET_ORDER );
			if ( count( $signatureList ) > 0 ) {
				$rand = rand( 0, count( $signatureList ) - 1 );
				$signature = $signatureList[$rand][1];
			}
		}

		return $signature;
	}

	/**
	 * Produce a subject for the message.
	 * @returns String
	 */
	static function fetchSubject() {
		$subject = '';
		if ( wfRunHooks( 'SetupNewUserMessageSubject', array( &$subject ) ) ) {
			$subject = wfMsg( 'newusermessage-template-subject' );
		}

		return $subject;
	}

	/**
	 * Produce the text of the message.
	 * @returns String
	 */
	static function fetchText() {
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
		return $text;
	}

	/**
	 * Produce the flags to set on Article::doEdit
	 * @returns Int
	 */
	static function fetchFlags() {
		global $wgNewUserMinorEdit, $wgNewUserSuppressRC;

		$flags = EDIT_NEW;
		if ( $wgNewUserMinorEdit ) $flags = $flags | EDIT_MINOR;
		if ( $wgNewUserSuppressRC ) $flags = $flags | EDIT_SUPPRESS_RC;

		return $flags;
	}

	/**
	 * Take care of substition on the string in a uniform manner
	 * @param $str String
	 * @param $user User
	 * @param $preparse if provided, then preparse the string using a Parser
	 * @returns String
	 */
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
	 * Add the message if the users talk page does not already exist
	 * @param $user User object
	 */
	static function createNewUserMessage( $user ) {
		$talk = $user->getTalkPage();

		if ( !$talk->exists() ) {
			wfLoadExtensionMessages( 'NewUserMessage' );

			$subject = self::fetchSubject();
			$text = self::fetchText();
			$signature = self::fetchSignature();
			$editSummary = wfMsgForContent( 'newuseredit-summary' );
			$editor = self::fetchEditor();
			$flags = self::fetchFlags();

			// Add (any) content to [[MediaWiki:Newusermessage-substitute]] to substitute the welcome template.
			$substitute = wfMsgForContent( 'newusermessage-substitute' );

			if ( $substitute ) {
				$subject = self::substString( $subject, $user, "preparse" );
				$text = self::substString( $text, $user );
			}

			return $user->leaveUserMessage( $subject, $text, $signature, $editSummary, $editor, $flags );
		}
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
