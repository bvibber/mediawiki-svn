<?php
/**
 * CategoryWatch extension
 * - Extends watchlist functionality to include notification about membership changes of watched categories
 *
 * See http://www.mediawiki.org/Extension:CategoryWatch for installation and usage details
 * See http://www.organicdesign.co.nz/Extension_talk:CategoryWatch for development notes and disucssion
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
 * @copyright © 2008 Aran Dunkley
 * @licence GNU General Public Licence 2.0 or later
 */

if ( !defined('MEDIAWIKI' ) ) die( 'Not an entry point.' );

define( 'CATEGORYWATCH_VERSION', '1.1.0, 2009-04-21' );

$wgCategoryWatchNotifyEditor = true;
$wgCategoryWatchUseAutoCat   = false;

$wgExtensionFunctions[] = 'wfSetupCategoryWatch';
$wgExtensionCredits['other'][] = array(
	'name'           => 'CategoryWatch',
	'author'         => '[http://www.organicdesign.co.nz/User:Nad User:Nad]',
	'description'    => 'Extends watchlist functionality to include notification about membership changes of watched categories',
	'descriptionmsg' => 'categorywatch-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:CategoryWatch',
	'version'        => CATEGORYWATCH_VERSION,
);

$wgExtensionMessagesFiles['CategoryWatch'] =  dirname(__FILE__) . '/CategoryWatch.i18n.php';

class CategoryWatch {

	function __construct() {
		global $wgHooks;
		$wgHooks['ArticleSave'][] = $this;
		$wgHooks['ArticleSaveComplete'][] = $this;
	}

	/**
	 * Get a list of categories before article updated
	 */
	function onArticleSave( &$article, &$user, &$text ) {
		global $wgCategoryWatchUseAutoCat;
		
		$this->before = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$cl   = $dbr->tableName( 'categorylinks' );
		$id   = $article->getID();
		$res  = $dbr->select( $cl, 'cl_to', "cl_from = $id", __METHOD__, array( 'ORDER BY' => 'cl_sortkey' ) );
		while ( $row = $dbr->fetchRow( $res ) ) $this->before[] = $row[0];
		$dbr->freeResult( $res );

		# If using the automatically watched category feature, ensure that all users are watching it
		if ( $wgCategoryWatchUseAutoCat ) {
			$dbr = wfGetDB( DB_SLAVE );

			# Find all users not watching the autocat
			$like = str_replace( ' ', '_', trim( wfMsg( 'categorywatch-autocat', '' ) ) );
			$utbl = $dbr->tableName( 'user' );
			$wtbl = $dbr->tableName( 'watchlist' );
			$sql = "SELECT user_id FROM $utbl LEFT JOIN $wtbl ON user_id=wl_user AND wl_title LIKE '%$like%' WHERE wl_user IS NULL";
			$res = $dbr->query( $sql );
			
			# Insert an entry into watchlist for each
			while ( $row = $dbr->fetchRow( $res ) ) {
				$uname = User::newFromId( $row[0] )->getName();
				$wl_title = str_replace( ' ', '_', wfMsg( 'categorywatch-autocat', $uname ) );
				$dbr->insert( $wtbl, array( 'wl_user' => $row[0], 'wl_namespace' => NS_CATEGORY, 'wl_title' => $wl_title ) );
			}
			$dbr->freeResult( $res );
		}		

		return true;
	}

	/**
	 * Find changes in categorisation and send messages to watching users
	 */
	function onArticleSaveComplete( &$article, &$user, &$text ) {

		# Get cats after update
		$this->after = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$cl   = $dbr->tableName( 'categorylinks' );
		$id   = $article->getID();
		$res  = $dbr->select( $cl, 'cl_to', "cl_from = $id", __METHOD__, array( 'ORDER BY' => 'cl_sortkey' ) );
		while ( $row = $dbr->fetchRow( $res ) ) $this->after[] = $row[0];
		$dbr->freeResult( $res );

		# Get list of added and removed cats
		$add = array_diff( $this->after, $this->before );
		$sub = array_diff( $this->before, $this->after );

		# Notify watchers of each cat about the addition or removal of this article
		if ( count( $add ) > 0 || count( $sub ) > 0 ) {
			$page = $article->getTitle()->getText();
			if ( count( $add ) == 1 && count( $sub ) == 1 ) {
				$add = array_shift( $add );
				$sub = array_shift( $sub );

				$title   = Title::newFromText( $add, NS_CATEGORY );
				$message = wfMsg( 'categorywatch-catmovein', $page, $add, $sub );
				$this->notifyWatchers( $title, $user, $message );

				#$title   = Title::newFromText( $sub, NS_CATEGORY );
				#$message = wfMsg( 'categorywatch-catmoveout', $page, $sub, $add );
				#$this->notifyWatchers( $title, $user, $message );
			}
			else {

				foreach ($add as $cat) {
					$title   = Title::newFromText( $cat, NS_CATEGORY );
					$message = wfMsg( 'categorywatch-catadd', $page, $cat );
					$this->notifyWatchers( $title, $user, $message );
				}

				#foreach ( $sub as $cat ) {
				#	$title   = Title::newFromText( $cat, NS_CATEGORY );
				#	$message = wfMsg( 'categorywatch-catsub', $page, $cat );
				#	$this->notifyWatchers( $title, $user, $message );
				#}
			}
		}

		return true;
	}

	function notifyWatchers( &$title, &$editor, &$message ) {
		global $wgLang, $wgEmergencyContact, $wgNoReplyAddress, $wgCategoryWatchNotifyEditor;

		# Get list of users watching this category
		$dbr = wfGetDB( DB_SLAVE );
		$conds = array( 'wl_title' => $title->getDBkey(), 'wl_namespace' => $title->getNamespace() );
		if ( !$wgCategoryWatchNotifyEditor) $conds[] = 'wl_user <> ' . intval( $editor->getId() );
		$res = $dbr->select( 'watchlist', array( 'wl_user' ), $conds, __METHOD__ );

		# Wrap message with common body and send to each watcher
		$page    = $title->getText();
		$from    = new MailAddress( $wgEmergencyContact, 'WikiAdmin' );
		$replyto = new MailAddress( $wgNoReplyAddress );
		foreach ( $res as $row ) {
			$watchingUser = User::newFromId( $row->wl_user );
			if ( $watchingUser->getOption( 'enotifwatchlistpages' ) && $watchingUser->isEmailConfirmed() ) {
				
				$to = new MailAddress( $watchingUser );
				$timecorrection = $watchingUser->getOption( 'timecorrection' );
				$editdate =	$wgLang->timeanddate( wfTimestampNow(), true, false, $timecorrection );
				$editdat1 =	$wgLang->date( wfTimestampNow(), true, false, $timecorrection );
				$edittim2 =	$wgLang->time( wfTimestampNow(), true, false, $timecorrection );
				$body = wfMsg(
					'categorywatch-emailbody',
					$watchingUser->getName(),
					$page,
					$editdate,
					$editor->getName(),
					$message,
					$editdat1,
					$edittim2
				);

				$subject = wfMsgForContent( 'enotif_subject' );
				$body    = wfMsgForContent( 'enotif_body' );
				$keys    = array();

				if ( $this->oldid ) {
					$difflink = $this->title->getFullUrl( 'diff=0&oldid=' . $this->oldid );
					$keys['$NEWPAGE'] = wfMsgForContent( 'enotif_lastvisited', $difflink );
					$keys['$OLDID']   = $this->oldid;
					$keys['$CHANGEDORCREATED'] = wfMsgForContent( 'changed' );
				} else {
					$keys['$NEWPAGE'] = wfMsgForContent( 'enotif_newpagetext' );
					# clear $OLDID placeholder in the message template
					$keys['$OLDID']   = '';
					$keys['$CHANGEDORCREATED'] = wfMsgForContent( 'created' );
				}

				if ($wgEnotifImpersonal && $this->oldid)
					$keys['$NEWPAGE'] = wfMsgForContent( 'enotif_lastdiff', $this->title->getFullURL( "oldid={$this->oldid}&diff=prev" ) );

				$body = strtr( $body, $keys );
				$pagetitle = $this->title->getPrefixedText();
				$keys['$PAGETITLE']          = $pagetitle;
				$keys['$PAGETITLE_URL']      = $this->title->getFullUrl();

				$keys['$PAGEMINOREDIT']      = $medit;
				$keys['$PAGESUMMARY']        = $summary;

				$subject = strtr( $subject, $keys );

				# Reveal the page editor's address as REPLY-TO address only if
				# the user has not opted-out and the option is enabled at the
				# global configuration level.
				$editor = $this->editor;
				$name    = $wgEnotifUseRealName ? $editor->getRealName() : $editor->getName();
				$adminAddress = new MailAddress( $wgPasswordSender, 'WikiAdmin' );
				$editorAddress = new MailAddress( $editor );
				if( $wgEnotifRevealEditorAddress
					&& ( $editor->getEmail() != '' )
					&& $editor->getOption( 'enotifrevealaddr' ) ) {
					if( $wgEnotifFromEditor ) {
						$from    = $editorAddress;
					} else {
						$from    = $adminAddress;
						$replyto = $editorAddress;
					}
				} else {
					$from    = $adminAddress;
					$replyto = new MailAddress( $wgNoReplyAddress );
				}

				if( $editor->isIP( $name ) ) {
					#real anon (user:xxx.xxx.xxx.xxx)
					$utext = wfMsgForContent('enotif_anon_editor', $name);
					$subject = str_replace('$PAGEEDITOR', $utext, $subject);
					$keys['$PAGEEDITOR']       = $utext;
					$keys['$PAGEEDITOR_EMAIL'] = wfMsgForContent( 'noemailtitle' );
				} else {
					$subject = str_replace('$PAGEEDITOR', $name, $subject);
					$keys['$PAGEEDITOR']          = $name;
					$emailPage = SpecialPage::getSafeTitleFor( 'Emailuser', $name );
					$keys['$PAGEEDITOR_EMAIL'] = $emailPage->getFullUrl();
				}
				$userPage = $editor->getUserPage();
				$keys['$PAGEEDITOR_WIKI'] = $userPage->getFullUrl();
				$body = strtr( $body, $keys );
				$body = wordwrap( $body, 72 );

				if ( function_exists( 'userMailer' ) ) {
					userMailer(
						$to,
						$from,
						wfMsg( 'categorywatch-emailsubject', $page ),
						$body,
						$replyto
					);
				}
				else {
					UserMailer::send(
						$to,
						$from,
						wfMsg( 'categorywatch-emailsubject', $page ),
						$body,
						$replyto
					);
				}
			}
		}

		$dbr->freeResult( $res );
	}

	/**
	 * Needed in some versions to prevent Special:Version from breaking
	 */
	function __toString() { return __CLASS__; }
}

function wfSetupCategoryWatch() {
	global $wgCategoryWatch;

	wfLoadExtensionMessages( 'CategoryWatch' );

	# Instantiate the CategoryWatch singleton now that the environment is prepared
	$wgCategoryWatch = new CategoryWatch();

}
