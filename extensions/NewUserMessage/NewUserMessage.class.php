<?php
/** Extension:NewUserMessage
 *
 * @file
 * @ingroup Extensions
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
	 * Return the template name if it exists, or '' otherwise.
	 * @returns string
	 */
	static function fetchTemplateIfExists( $template ) {
		$text = Title::newFromText( $template );

		if ( !$text ) {
			wfDebug( __METHOD__ . ": '$template' is not a valid title.\n" );
			return '';
		} elseif ( $text->getNamespace() !== NS_TEMPLATE ) {
			wfDebug( __METHOD__ . ": '$template' is not a valid Template.\n" );
			return '';
		} elseif ( !$text->exists() ) {
			return '';
		}

		return $text->getText();
	}

	/**
	 * Produce a subject for the message.
	 * @returns String
	 */
	static function fetchSubject() {
		return self::fetchTemplateIfExists( wfMsg( 'newusermessage-template-subject' ) );
	}

	/**
	 * Produce the text of the message.
	 * @returns String
	 */
	static function fetchText() {
		return self::fetchTemplateIfExists( wfMsg( 'newusermessage-template-body' ) );
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
	 * @param $editor User
	 * @param $talk Article
	 * @param $preparse if provided, then preparse the string using a Parser
	 * @returns String
	 */
	static private function substString( $str, $user, $editor, $talk, $preparse = null ) {
		$realName = $user->getRealName();
		$name = $user->getName();

		// Add (any) content to [[MediaWiki:Newusermessage-substitute]] to substitute the
		// welcome template.
		$substitute = wfMsgForContent( 'newusermessage-substitute' );

		if ( $substitute ) {
			$str = "{{subst:{{$str}}}|realName=$realName|name=$name}}";
		} else {
			$str = "{{{$str}|realName=$realName|name=$name}}";
		}

		if ( $preparse ) {
			global $wgParser;

			$str = $wgParser->preSaveTransform($str, $talk, $editor, new ParserOptions );
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

			$subject = self::substString( $subject, $user, $editor, $talk, "preparse" );
			$text = self::substString( $text, $user, $editor, $talk );

			return $user->leaveUserMessage( $subject, $text, $signature, $editSummary,
				$editor, $flags );
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
