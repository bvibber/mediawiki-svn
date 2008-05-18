<?php
/*
* AuthorProtect extension by Ryan Schmidt
* See http://www.mediawiki.org/wiki/Extension:AuthorProtect for more details
*/

if(!defined('MEDIAWIKI')) {
	echo("This file is an extension to MediaWiki and cannot be run externally");
	die(1);
}

$wgExtensionCredits['other'][] = array(
	'name' => 'Author Protect',
	'author' => 'Ryan Schmidt',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AuthorProtect',
	'version' => '1.0',
	'description'    => 'Allows the author of a page to protect it from other users',
	'descriptionmsg' => 'authorprotect-desc',
);

$wgAvailableRights[] = 'author'; //dynamically assigned to the author of a page, but can be set w/ GroupPermissions too
$wgAvailableRights[] = 'authorprotect'; //users without this right cannot protect pages they author
$wgExtensionMessagesFiles['AuthorProtect'] = dirname(__FILE__) . '/AuthorProtect.i18n.php';
$wgGroupPermissions['sysop']['author'] = true; //sysops can edit every page despite author protection
$wgGroupPermissions['user']['authorprotect'] = true; //registered users can protect pages they author
$wgHooks['SkinTemplateContentActions'][] = 'efMakeContentAction';
$wgHooks['UnknownAction'][] = 'efAuthorProtectForm';
$wgHooks['userCan'][] = 'efAuthorProtectDelay';

function efAuthorProtectDelay($title, &$user, $action, $result) {
	$user->mRights = null;
	$wgHooks['UserGetRights'][] = 'efAssignAuthor';
	$user->getRights(); //delay hook execution for compatibility w/ ConfirmAccount
	$act = ( $action == '' || $action == 'view' ) ? 'edit' : $action;
	if( userIsAuthor() && isAuthorProtected($title, $act) ) {
		$result = true;
		return false;
	}
	$result = null;
	return true;
}

function efAssignAuthor(&$user, &$aRights) {
	//don't assign author to anons... messes up logging stuff.
	//plus it's all user_id based so it is impossible to differentiate one anon from another
	if( userIsAuthor() && $user->isLoggedIn() ) {
		$aRights[] = 'author';
		$aRights = array_unique($aRights);
	}
	return true;
}

function efMakeContentAction(&$cactions) {
	global $wgUser, $wgRequest, $wgTitle;
	if( userIsAuthor() && $wgUser->isAllowed('authorprotect') && !$wgUser->isAllowed('protect') ) {
		$action = $wgRequest->getText('action');
		$cactions['authorprotect'] = array(
			'class' => $action == 'authorprotect' ? 'selected' : false,
			'text' => wfMsg(protectMessage($wgTitle)),
			'href' => $wgTitle->getLocalUrl('action=authorprotect'),
		);
	}
	return true;
}

function efAuthorProtectForm($action, &$article) {
	if($action == 'authorprotect') {
		wfLoadExtensionMessages('AuthorProtect');
		global $wgOut, $wgUser, $wgRequest, $wgRestrictionTypes;
		if($wgUser->isAllowed('authorprotect')) {
			if(userIsAuthor()) {
				$wgOut->setPageTitle(wfMsg('authorprotect'));
				if( !$wgRequest->wasPosted() ) {
					$wgOut->addHTML(makeProtectForm());
				} else {
					if( !$wgUser->matchEditToken( $wgRequest->getText('wpToken') ) ) {
						$wgOut->setPageTitle(wfMsg('errorpagetitle'));
						$wgOut->addWikiText(wfMsg('sessionfailure'));
						return;
					}
					$restrictions = array();
					foreach( $wgRestrictionTypes as $type ) {
						if( $wgRequest->getCheck("check-{$type}") )
							$restrictions[$type] = 'author';
					}
					$success = doProtect( $restrictions, $wgRequest->getText('wpReason'), $wgRequest->getText('wpExpiryTime') );
					if($success) {
						$wgOut->addWikiText(wfMsg('authorprotect-success'));
					} else {
						$wgOut->addWikiText(wfMsg('authorprotect-failure'));
					}
				}
			} else {
				$wgOut->setPageTitle(wfMsg('errorpagetitle'));
				$wgOut->addWikiText(wfMsg('authorprotect-notauthor'));
			}
		} else {
			$wgOut->permissionRequired('authorprotect');
		}
		return false; //still continues hook processing, but doesn't throw an error message
	}
	return true; //unknown action, so state that the action doesn't exist
}

function makeProtectForm() {
	global $wgRestrictionTypes, $wgTitle, $wgUser;
	$token = $wgUser->editToken();
	$form = '<p>' . wfMsg('authorprotect-intro') . '</p>';
	$form .= wfOpenElement( 'form', array( 'method' => 'post', 'action' => $wgTitle->getLocalUrl('action=authorprotect') ) );
	foreach( $wgRestrictionTypes as $type ) {
		$checked = in_array( 'author', $wgTitle->getRestrictions( $type ) );
		$array = array( 'type' => 'checkbox', 'name' => 'check-' . $type, 'value' => $type );
		if($checked)
			$array = array_merge( $array, array( 'checked' => 'checked' ) );
		$form .= wfElement( 'input', $array );
		$form .= ' ' . wfMsg('authorprotect-' . $type) . wfElement( 'br' );
	}
	$form .= wfElement( 'br' ) . wfElement( 'label', array( 'for' => 'wpExpiryTime' ), wfMsg('authorprotect-expiry') ) . ' ';
	$form .= wfElement( 'input', array( 'type' => 'text', 'name' => 'wpExpiryTime' ) ) . wfElement( 'br' );
	$form .= wfElement( 'br' ) . wfElement( 'label', array( 'for' => 'wpReason' ), wfMsg('authorprotect-reason') ) . ' ';
	$form .= wfElement( 'input', array( 'type' => 'text', 'name' => 'wpReason' ) );
	$form .= wfElement( 'br' ) . wfElement( 'input', array( 'type' => 'hidden', 'name' => 'wpToken', 'value' => $token ) );
	$form .= wfElement( 'br' ) . wfElement( 'input', array( 'type' => 'submit', 'name' => 'wpConfirm', 'value' => wfMsg( 'authorprotect-confirm' ) ) );
	$form .= wfCloseElement( 'form' );
	return $form;
}

function userIsAuthor() {
	global $wgTitle, $wgUser, $wgDBPrefix;
	$id = $wgTitle->getArticleId();
	$dbr = wfGetDb(DB_SLAVE); //grab the slave for reading
	$res = $dbr->query( "SELECT `rev_user` FROM `{$wgDBPrefix}revision` WHERE rev_page={$id} LIMIT 1", __METHOD__ );
	$row = $dbr->fetchRow($res);
	return $wgUser->getID() == $row['rev_user'];
}

function protectMessage($title) {
	global $wgRestrictionTypes;
	foreach( $wgRestrictionTypes as $type ) {
		if( in_array( 'author', $title->getRestrictions( $type ) ) )
			return 'unprotect';
	}
	return 'protect';
}

// do the protection, copied from Article.php's updateRestrictions then modified
// so that it isn't so picky about having the 'protect' right.
function doProtect( $limit = array(), $reason = '', $expiry = null ) {
	global $wgUser, $wgRestrictionTypes, $wgContLang, $wgTitle;

	$id = $wgTitle->getArticleID();
	if( wfReadOnly() || $id == 0 ) {
		return false;
	}

	// Take this opportunity to purge out expired restrictions
	Title::purgeExpiredRestrictions();

	# FIXME: Same limitations as described in ProtectionForm.php (line 37);
	# we expect a single selection, but the schema allows otherwise.
	$current = array();
	foreach( $wgRestrictionTypes as $action )
		$current[$action] = implode( '', $wgTitle->getRestrictions( $action ) );

	$current = Article::flattenRestrictions( $current );
	$updated = Article::flattenRestrictions( $limit );

	$changed = ( $current != $updated );
	$changed = $changed || ($wgTitle->areRestrictionsCascading() != $cascade);
	$changed = $changed || ($wgTitle->mRestrictionsExpiry != $expiry);
	$protect = ( $updated != '' );

	# If nothing's changed, do nothing
	if( $changed ) {
		global $wgGroupPermissions;

		$dbw = wfGetDB( DB_MASTER );

		$encodedExpiry = Block::encodeExpiry($expiry, $dbw );

		$expiry_description = '';
		if ( $encodedExpiry != 'infinity' ) {
			$expiry_description = ' (' . wfMsgForContent( 'protect-expiring', $wgContLang->timeanddate( $expiry, false, false ) ).')';
		}

		# Prepare a null revision to be added to the history
		$modified = $current != '' && $protect;
		if ( $protect ) {
			$comment_type = $modified ? 'modifiedarticleprotection' : 'protectedarticle';
		} else {
			$comment_type = 'unprotectedarticle';
		}
		$comment = $wgContLang->ucfirst( wfMsgForContent( $comment_type, $wgTitle->getPrefixedText() ) );

		if( $reason )
			$comment .= ": $reason";
		if( $protect )
			$comment .= " [$updated]";
		if ( $expiry_description && $protect )
			$comment .= "$expiry_description";

		# Update restrictions table
		foreach( $limit as $action => $restrictions ) {
			if ($restrictions != '' ) {
				$dbw->replace( 'page_restrictions', array(array('pr_page', 'pr_type')),
					array( 'pr_page' => $id, 'pr_type' => $action
						, 'pr_level' => $restrictions, 'pr_cascade' => 0
						, 'pr_expiry' => $encodedExpiry ), __METHOD__  );
			} else {
				$dbw->delete( 'page_restrictions', array( 'pr_page' => $id,
					'pr_type' => $action ), __METHOD__ );
			}
		}

		# Insert a null revision
		$nullRevision = Revision::newNullRevision( $dbw, $id, $comment, true );
		$nullRevId = $nullRevision->insertOn( $dbw );

		# Update page record
		$dbw->update( 'page',
			array( /* SET */
				'page_touched' => $dbw->timestamp(),
				'page_restrictions' => '',
				'page_latest' => $nullRevId
			), array( /* WHERE */
				'page_id' => $id
			), 'Article::protect'
		);
		# Update the protection log
		$log = new LogPage( 'protect' );

		if( $protect ) {
			$log->addEntry( $modified ? 'modify' : 'protect', $wgTitle, trim( $reason . " [$updated]$expiry_description" ) );
		} else {
			$log->addEntry( 'unprotect', $wgTitle, $reason );
		}
	} # End "changed" check

	return true;
}

function isAuthorProtected($title, $action) {
	$rest = $title->getRestrictions($action);
	return in_array('author', $rest);
}