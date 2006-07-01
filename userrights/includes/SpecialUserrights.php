<?php
/**
 * Provide an administration interface
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/** */
require_once('HTMLForm.php');

/** Entry point */
function wfSpecialUserrights() {
	global $wgRequest;
	$form = new UserrightsForm($wgRequest);
	$form->execute();
}

/**
 * A class to manage user levels rights.
 * @package MediaWiki
 * @subpackage SpecialPage
 */
class UserrightsForm extends HTMLForm {
	var $mPosted, $mRequest, $mSaveprefs;
	/** Escaped local url name*/
	var $action;

	/** Constructor*/
	function UserrightsForm ( &$request ) {
		$this->mPosted = $request->wasPosted();
		$this->mRequest =& $request;
		$this->mName = 'userrights';

		$titleObj = Title::makeTitle( NS_SPECIAL, 'Userrights' );
		$this->action = $titleObj->escapeLocalURL();
		
		$this->db = null;
		$this->user = null;
		$this->id = null;
	}

	/**
	 * Manage forms to be shown according to posted data.
	 * Depending on the submit button used, call a form or a save function.
	 */
	function execute() {
		// show the general form
		$this->switchForm();
		if( $this->mPosted ) {
			// show some more forms
			if( $this->mRequest->getCheck( 'ssearchuser' ) ) {
				$this->editUserGroupsForm( $this->mRequest->getVal( 'user-editname' ) );
			}

			// save settings
			if( $this->mRequest->getCheck( 'saveusergroups' ) ) {
				global $wgUser;
				$username = $this->mRequest->getVal( 'user-editname' );
				$reason = $this->mRequest->getVal( 'reason' );
				if( $wgUser->matchEditToken( $this->mRequest->getVal( 'wpEditToken' ), $username ) ) {
					$this->saveUserGroups( $username,
						$this->mRequest->getArray( 'member' ),
						$this->mRequest->getArray( 'available' ),
						$reason );
				}
			}
		}
	}

	/**
	 * Save user groups changes in the database.
	 * Data comes from the editUserGroupsForm() form function
	 *
	 * @param string $username Username to apply changes to.
	 * @param array $removegroup id of groups to be removed.
	 * @param array $addgroup id of groups to be added.
	 * @param string $reason the reason of changing the permissions.
	 */
	function saveUserGroups( $username, $removegroup, $addgroup, $reason ) {
		global $wgOut;
		
		$split = $this->splitUsername( $username );
		list( $database, $name ) = $split;
		if( $name == '' ) {
			$wgOut->addWikiText( wfMsg( 'userrights-nodatabase', $database ) );
			return;
		}
		if( $database == '' ) {
			$this->user = User::newFromName( $name );
			if( !isset( $this->user ) ) {
				$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			}
		} else {
			$this->db =& $this->getDB( $database );
		}
		$this->id = $this->getUserId( $name );

		if( $this->id == 0) {
			$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			return;
		}

		$oldGroups = $this->getUserGroups();
		$newGroups = $oldGroups;
		# Remove then add groups
		if(isset($removegroup)) {
			$newGroups = array_diff($newGroups, $removegroup);
			foreach( $removegroup as $group ) {
				$this->removeUserGroup( $group );
			}
		}
		if(isset($addgroup)) {
			$newGroups = array_merge($newGroups, $addgroup);
			foreach( $addgroup as $group ) {
				$this->addUserGroup( $group );
			}
		}
		$newGroups = array_unique( $newGroups );
		
		// Ensure that caches are cleared
		if( isset( $this->db ) ) {
			$this->touchUser( $database, $this->id );
		}
		
		wfDebug( 'oldGroups: ' . print_r( $oldGroups, true ) );
		wfDebug( 'newGroups: ' . print_r( $newGroups, true ) );

		wfRunHooks( 'UserRights', array( &$u, $addgroup, $removegroup ) );	
		$log = new LogPage( 'rights' );
		$log->addEntry( 'rights', Title::makeTitle( NS_USER, $username ), $reason, array( $this->makeGroupNameList( $oldGroups ),
			$this->makeGroupNameList( $newGroups ) ) );
	}

	function makeGroupNameList( $ids ) {
		return implode( ', ', $ids );
	}

	/**
	 * The entry form
	 * It allows a user to look for a username and edit its groups membership
	 */
	function switchForm() {
		global $wgOut;

		// user selection
		$wgOut->addHTML( "<form name=\"uluser\" action=\"$this->action\" method=\"post\">\n" );
		$wgOut->addHTML( $this->fieldset( 'lookup-user',
				$this->textbox( 'user-editname' ) .
				wfElement( 'input', array(
					'type'  => 'submit',
					'name'  => 'ssearchuser',
					'value' => wfMsg( 'editusergroup' ) ) )
		));
		$wgOut->addHTML( "</form>\n" );
	}

	/**
	 * Edit user groups membership
	 * @param string $username Name of the user.
	 */
	function editUserGroupsForm($username) {
		global $wgOut, $wgUser;
		
		$split = $this->splitUsername( $username );
		list( $database, $name ) = $split;
		if( $name == '' ) {
			$wgOut->addWikiText( wfMsg( 'userrights-nodatabase', $database ) );
			return;
		}
		if( $database == '' ) {
			$this->user = User::newFromName( $name );
			if( !isset( $this->user ) ) {
				$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			}
		} else {
			$this->db =& $this->getDB( $database );
		}
		$this->id = $this->getUserId( $name );

		if( $this->id == 0) {
			$wgOut->addWikiText( wfMsg( 'nosuchusershort', wfEscapeWikiText( $username ) ) );
			return;
		}

		$groups = $this->getUserGroups();

		$wgOut->addHTML( "<form name=\"editGroup\" action=\"$this->action\" method=\"post\">\n".
			wfElement( 'input', array(
				'type'  => 'hidden',
				'name'  => 'user-editname',
				'value' => $username ) ) .
			wfElement( 'input', array(
				'type'  => 'hidden',
				'name'  => 'wpEditToken',
				'value' => $wgUser->editToken( $username ) ) ) .
			$this->fieldset( 'editusergroup',
			$wgOut->parse( wfMsg('editing', $username ) ) .
			'<table border="0" align="center"><tr><td>'.
			HTMLSelectGroups('member', $this->mName.'-groupsmember', $groups,true,6).
			'</td><td>'.
			HTMLSelectGroups('available', $this->mName.'-groupsavailable', $groups,true,6,true).
			'</td></tr></table>'."\n".
			$this->textbox( 'reason', '', 50 ).
			$wgOut->parse( wfMsg('userrights-groupshelp') ) .
			wfElement( 'input', array(
				'type'  => 'submit',
				'name'  => 'saveusergroups',
				'value' => wfMsg( 'saveusergroups' ) ) )
			));
		$wgOut->addHTML( "</form>\n" );
	}
	
	/**
	 * Split the user name to database and user name, if applicable.
	 * @param string $username
	 * @return array of the database and user name, some parts may be
	   empty (no database if not specified, no user name if database
	   is not exist)
	 */
	function splitUsername( $username ) {
		global $wgLocalDatabases;
		$parts = explode( '@', $username );
		if( count( $parts ) < 2 ) {
			return array( '', $username );
		} elseif( in_array( $parts[1], $wgLocalDatabases ) ) {
			return array( $parts[1], $parts[0] );
		} else {
			return array( $parts[1], '' );
		}
	}
	
	/**
	 * Open a database connection to work on for the requested user.
	 * This may be a new connection to another database for remote users.
	 * @param string $database
	 * @return Database
	 */
	function &getDB( $database ) {
		global $wgDBname;
		if( $database == '' ) {
			$db = null;
		} else {
			global $wgDBuser, $wgDBpassword;
			$server = $this->getMaster( $database );
			$db =& new Database( $server, $wgDBuser, $wgDBpassword, $database );
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
	
	function getUserId( $name ) {
		if( isset( $this->db ) ) {
			return ( $name{0} == "#" )
				? IntVal( substr( $name, 1 ) )
				: IntVal( $this->db->selectField( 'user',
					'user_id',
					array( 'user_name' => $name ),
					'UserrightsForm::getUserId' ) );
		} else {
			return $this->user->getID();
		}
	}
	
	function getUserGroups() {
		if( isset( $this->db ) ) {
			$res = $this->db->select( 'user_groups',
				array( 'ug_group' ),
				array( 'ug_user' => $this->id ),
				'UserrightsForm::getUserGroups' );
			$groups = array();
			while( $row = $this->db->fetchObject( $res ) ) {
				$groups[] = $row->ug_group;
			}
			return $groups;
		} else {
			return $this->user->getGroups();
		}
	}
	
	function addUserGroup( $group ) {
		if( isset( $this->db ) ) {
			$this->db->insert( 'user_groups',
				array(
					'ug_user' => $this->id,
					'ug_group' => $group,
				),
				'UserrightsForm::addUserGroup',
				array( 'IGNORE' ) );
		} else {
			return $this->user->addGroup( $group );
		}
	}
	
	function removeUserGroup( $group ) {
		if( isset( $this->db ) ) {
			$this->db->delete( 'user_groups',
				array(
					'ug_user' => $this->id,
					'ug_group' => $group,
				),
				'UserrightsForm::addUserGroup' );
		} else {
			return $this->user->removeGroup( $group );
		}
	}
	
	function touchUser( $database, $userid ) {
		$this->db->update( 'user',
			array( 'user_touched' => $this->db->timestamp() ),
			array( 'user_id' => $this->id ),
			'UserrightsForm::touchUser' );
		
		global $wgMemc;
		$key = "$database:user:id:$userid";
		$wgMemc->delete( $key );
	}
} // end class UserrightsForm
?>
