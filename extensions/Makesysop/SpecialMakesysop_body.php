<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "MakeSysop extension\n";
	exit( 1 );
}

class MakeSysopPage extends SpecialPage {
	function __construct() {
		parent::__construct( 'Makesysop', 'makesysop' );
		wfLoadExtensionMessages( 'Makesysop' );
	}

	function execute( $subpage ) {
		global $wgUser, $wgOut, $wgRequest;

		if ( $wgUser->isBlocked() ) {
			$wgOut->blockedPage();
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		if ( !$wgUser->isAllowed( 'makesysop' ) ) {
			$this->displayRestrictionError();
			return;
		}

		$this->setHeaders();

		if( $wgUser->isAllowed( 'userrights' ) ) {
			$f = new MakesysopStewardForm( $wgRequest );
			$f->execute();
		} else {
			$f = new MakesysopForm( $wgRequest );
			if ( $f->mSubmit ) {
				$f->doSubmit();
			} else {
				$f->showForm( '' );
			}
		}
	}
}

/**
 *
 * @addtogroup SpecialPage
 */
class MakesysopForm {
	var $mTarget, $mAction, $mRights, $mUser, $mReason, $mSubmit, $mSetBureaucrat;

	function MakesysopForm( &$request ) {
		global $wgUser;

		$this->mAction = $request->getText( 'action' );
		$this->mRights = $request->getVal( 'wpRights' );
		$this->mUser = $request->getText( 'wpMakesysopUser' );
		$this->mReason = $request->getText( 'wpMakesysopReason' );
		$this->mSubmit = $request->getBool( 'wpMakesysopSubmit' ) &&
			$request->wasPosted() &&
			$wgUser->matchEditToken( $request->getVal( 'wpEditToken' ) );		
		$this->mSetBureaucrat = $request->getBool( 'wpSetBureaucrat' );
	}

	function showForm( $err = '') {
		global $wgOut, $wgUser, $wgLang;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$wgOut->addWikiText( wfMsg( "makesysoptext" ) );

		$titleObj = Title::makeTitle( NS_SPECIAL, "Makesysop" );
		$action = $titleObj->escapeLocalURL( "action=submit" );

		if ( "" != $err ) {
			$wgOut->setSubtitle( wfMsg( "formerror" ) );
			$wgOut->addHTML( "<p class='error'>{$err}</p>\n" );
		}
		$namedesc = wfMsg( "makesysopname" );
		if ( !is_null( $this->mUser ) ) {
			$encUser = htmlspecialchars( $this->mUser );
		} else {
			$encUser = "";
		}

		$reason = htmlspecialchars( wfMsg( "userrights-reason" ) );
		$makebureaucrat = wfMsg( "setbureaucratflag" );
		$mss = wfMsg( "set_user_rights" );

		$wgOut->addHTML(
			Xml::openElement( 'form', array( 'method' => 'post', 'action' => $action, 'id' => 'makesysop' ) ) .
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', array(), wfMsg( 'makesysoptitle' ) ) .
			"<table border='0'>
			<tr>
				<td align='right'>$namedesc</td>
				<td align='left'>" . Xml::input( 'wpMakesysopUser', 40, $encUser ) . "</td>
			</tr><tr>
				<td align='right'>$reason</td>
				<td align='left'>" . Xml::input( 'wpMakesysopReason', 40, $this->mReason, array( 'maxlength' => 255 ) ) . "</td>
			</tr><tr>
				<td>&nbsp;</td>
				<td align='left'>" . Xml::checkLabel( $makebureaucrat, 'wpSetBureaucrat', 'wpSetBureaucrat', $this->mSetBureaucrat ) . "</td>
			</tr><tr>
				<td>&nbsp;</td>
				<td align='left'>" . Xml::submitButton( $mss, array( 'name' => 'wpMakesysopSubmit' ) ) . "</td>
			</tr>
			</table>" .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' ) . "\n"
		);
	}

	function doSubmit() {
		global $wgOut, $wgUser, $wgLang;
		global $wgDBname, $wgMemc, $wgLocalDatabases, $wgSharedDB;

		$fname = 'MakesysopForm::doSubmit';

		$dbw =& wfGetDB( DB_MASTER );
		$user_groups = $dbw->tableName( 'user_groups' );
		$usertable   = $dbw->tableName( 'user' );

		$username = $this->mUser;

		// Clean up username
		$t = Title::newFromText( $username );
		if ( !$t ) {
			$this->showFail();
			return;
		}
		$username = $t->getText();

		if ( $username{0} == "#" ) {
			$id = intval( substr( $username, 1 ) );
		} else {
			$id = $dbw->selectField( $usertable, 'user_id', array( 'user_name' => $username ), $fname );
		}
		if ( !$id ) {
			$this->showFail();
			return;
		}

		$sql = "SELECT ug_user,ug_group FROM $user_groups WHERE ug_user=$id FOR UPDATE";
		$res = $dbw->query( $sql, $fname );

		$row = false;
		$oldGroups = array();
		while ( $row = $dbw->fetchObject( $res ) ) {
			$oldGroups[] = $row->ug_group;
		}
		$dbw->freeResult( $res );
		$newGroups = $oldGroups;

		$wasSysop = in_array( 'sysop', $oldGroups );
		$wasBureaucrat = in_array( 'bureaucrat', $oldGroups );

		$addedGroups = array();
		if ( ( $this->mSetBureaucrat ) && ( $wasBureaucrat ) ) {
			$this->showFail( 'already_bureaucrat' );
			return;
		} elseif ( ( !$this->mSetBureaucrat ) && ( $wasSysop ) ) {
			$this->showFail( 'already_sysop' );
			return;
		} elseif ( !$wasSysop ) {
			$dbw->insert( $user_groups, array( 'ug_user' => $id, 'ug_group' => 'sysop' ), $fname );
			$addedGroups[] = "sysop";
		}
		if ( ( $this->mSetBureaucrat ) && ( !$wasBureaucrat ) ) {
			$dbw->insert( $user_groups, array( 'ug_user' => $id, 'ug_group' => 'bureaucrat' ), $fname );
			$addedGroups[] = "bureaucrat";
		}

		if ( function_exists( 'wfMemcKey' ) ) {
			$wgMemc->delete( wfMemcKey( 'user', 'id', $id ) );
		} else {
			$wgMemc->delete( "$wgDBname:user:id:$id" );
		}

		$newGroups = array_merge($newGroups, $addedGroups);

		$log = new LogPage( 'rights' );
		$log->addEntry( 'rights', Title::makeTitle( NS_USER, $username ), $this->mReason,
			array( $this->makeGroupNameList( $oldGroups ), $this->makeGroupNameList( $newGroups ) ) );

		$this->showSuccess();
	}

	function showSuccess() {
		global $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$text = wfMsg( "makesysopok", $this->mUser );
		if ( $this->mSetBureaucrat ) {
			$text .= "<br />" . wfMsg( "makebureaucratok", $this->mUser );
		}
		$text .= "\n\n";
		$wgOut->addWikiText( $text );
		$this->showForm();

	}

	function showFail( $msg = 'set_rights_fail' ) {
		global $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$this->showForm( wfMsg( $msg, $this->mUser ) );
	}

	function makeGroupNameList( $ids ) {
		return implode( ', ', $ids );
	}

}

/**
 *
 * @addtogroup SpecialPage
 */
class MakesysopStewardForm extends UserrightsForm {
	function MakesysopStewardForm( $request ) {
		$this->mPosted = $request->wasPosted();
		$this->mRequest =& $request;
		$this->mName = 'userrights';
		$this->mReason = $request->getText( 'user-reason' );
		$titleObj = Title::makeTitle( NS_SPECIAL, 'Makesysop' );
		$this->action = $titleObj->escapeLocalURL();

		$this->db = null;
	}

	/** @see UserrightsForm::saveUserGroups in MediaWiki */
	function saveUserGroups( $username, $removegroup, $addgroup, $reason = '') {
		global $wgOut;
		$split = $this->splitUsername( $username );
		if( WikiError::isError( $split ) ) {
			$wgOut->addWikiText( wfMsg( 'makesysop-nodatabase', $split->getMessage() ) );
			return;
		}

		list( $database, $name ) = $split;
		$this->db =& $this->getDB( $database );
		$userid = $this->getUserId( $database, $name );

		if( $userid == 0) {
			$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			return;
		}

		$oldGroups = $this->getUserGroups( $database, $userid );
		$newGroups = $oldGroups;
		// remove then add groups		
		if(isset($removegroup)) {
			$newGroups = array_diff($newGroups, $removegroup);
			foreach( $removegroup as $group ) {
				$this->removeUserGroup( $database, $userid, $group );
			}
		}
		if(isset($addgroup)) {
			$newGroups = array_merge($newGroups, $addgroup);
			foreach( $addgroup as $group ) {
				$this->addUserGroup( $database, $userid, $group );
			}
		}
		$newGroups = array_unique( $newGroups );

		// Ensure that caches are cleared
		$this->touchUser( $database, $userid );

		wfDebug( 'oldGroups: ' . print_r( $oldGroups, true ) );
		wfDebug( 'newGroups: ' . print_r( $newGroups, true ) );

		$log = new LogPage( 'rights' );
		$log->addEntry( 'rights', Title::makeTitle( NS_USER, $username ), $this->mReason, array( $this->makeGroupNameList( $oldGroups ),
			$this->makeGroupNameList( $newGroups ) ) );
	}

	/**
	 * Edit user groups membership
	 * @param string $username Name of the user.
	 */
	function editUserGroupsForm($username) {
		global $wgOut, $wgUser;
		
		$split = $this->splitUsername( $username );
		if( WikiError::isError( $split ) ) {
			$wgOut->addWikiText( wfMsg( 'makesysop-nodatabase', $split->getMessage() ) );
			return;
		}

		list( $database, $name ) = $split;
		$this->db =& $this->getDB( $database );
		$userid = $this->getUserId( $database, $name );

		if( $userid == 0) {
			$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			return;
		}

		$groups = $this->getUserGroups( $database, $userid );
		
		$this->showEditUserGroupsForm( $username, $groups );
	}

	function splitUsername( $username ) {
		$parts = explode( '@', $username );
		if( count( $parts ) < 2 ) {
			return array( '', $username );
		}
		list( $name, $database ) = $parts;

		global $wgLocalDatabases;
		return array_search( $database, $wgLocalDatabases ) !== false
			? array( $database, $name )
			: new WikiError( 'Bogus database suffix "' . wfEscapeWikiText( $database ) . '"' );
	}

	/**
	 * Open a database connection to work on for the requested user.
	 * This may be a new connection to another database for remote users.
	 * @param string $database
	 * @return Database
	 */
	function &getDB( $database ) {
		if( $database == '' ) {
			$db =& wfGetDB( DB_MASTER );
		} else {
			global $wgDBuser, $wgDBpassword;
			$server = $this->getMaster( $database );
			$db = new Database( $server, $wgDBuser, $wgDBpassword, $database );
		}
		return $db;
	}
	
	/**
	 * Return the master server to connect to for the requested database.
	 */
	function getMaster( $database ) {
		global $wgDBserver, $wgAlternateMaster;
		if( isset( $wgAlternateMaster[$database] ) ) {
			return $wgAlternateMaster[$database];
		}
		return $wgDBserver;
	}

	function getUserId( $database, $name ) {
		if( $name === '' )
			return 0;
		return ( $name{0} == "#" )
			? IntVal( substr( $name, 1 ) )
			: IntVal( $this->db->selectField( 'user',
				'user_id',
				array( 'user_name' => $name ),
				'MakesysopStewardForm::getUserId' ) );
	}

	function getUserGroups( $database, $userid ) {
		$res = $this->db->select( 'user_groups',
			array( 'ug_group' ),
			array( 'ug_user' => $userid ),
			'MakesysopStewardForm::getUserGroups' );
		$groups = array();
		while( $row = $this->db->fetchObject( $res ) ) {
			$groups[] = $row->ug_group;
		}
		return $groups;
	}

	function addUserGroup( $database, $userid, $group ) {
		$this->db->insert( 'user_groups',
			array(
				'ug_user' => $userid,
				'ug_group' => $group,
			),
			'MakesysopStewardForm::addUserGroup',
			array( 'IGNORE' ) );
	}

	function removeUserGroup( $database, $userid, $group ) {
		$this->db->delete( 'user_groups',
			array(
				'ug_user' => $userid,
				'ug_group' => $group,
			),
			'MakesysopStewardForm::addUserGroup' );
	}

	function touchUser( $database, $userid ) {
		$this->db->update( 'user',
			array( 'user_touched' => $this->db->timestamp() ),
			array( 'user_id' => $userid ),
			'MakesysopStewardForm::touchUser' );
		
		global $wgMemc;
		if ( function_exists( 'wfForeignMemcKey' ) ) {
			$key = wfForeignMemcKey( $database, false, 'user', 'id', $userid );
		} else {
			$key = "$database:user:id:$userid";
		}
		$wgMemc->delete( $key );
	}
}


