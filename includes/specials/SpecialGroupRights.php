<?php

#This file is part of MediaWiki.

#MediaWiki is free software: you can redistribute it and/or modify
#it under the terms of version 2 of the GNU General Public License
#as published by the Free Software Foundation.

#MediaWiki is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.

/**
 * Special page to allow managing groups
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}


class SpecialGroupRights extends SpecialPage
{
	function __construct() {
		parent::__construct('GroupRights', 'grouprights');
	}
	
	function getAvailableBackends( $user ) {
		global $wgRightsManagers;
		
		$availableBackends = array();
		
		foreach( $wgRightsManagers as $rmClass ) {
			$rm = new $rmClass;
			
			if ( $rm->canEditRights( $user ) ) {
				$availableBackends[] = $rmClass;
			}
		}
		
		return $availableBackends;
	}
	
	function userCanExecute( $user ) {
		return (bool) count($this->getAvailableBackends( $user ));
	}
	
	function getBackend() {
		static $backend = null;
		
		if ( !is_null($backend) )
			return $backend;
		
		return $backend = new $this->mBackend;
	}

	function execute( $subpage ) {
		global $wgRequest,$wgOut,$wgUser;
		
		if (!$this->userCanExecute($wgUser)) {
			$this->displayRestrictionError();
			return;
		}
		
		$wgOut->setPageTitle( wfMsg( 'grouprights' ) );
		$wgOut->setRobotPolicy( "noindex,nofollow" );
		$wgOut->setArticleRelated( false );
		$wgOut->enableClientCache( false );
		
		$availableBackends = $this->getAvailableBackends( $wgUser );
		$specifiedBackend = $wgRequest->getVal( 'backend' );
		
		if ( count($availableBackends) == 1 ) {
			$this->mBackend = $availableBackends[0];
		} elseif ( in_array( $specifiedBackend, $availableBackends ) ) {
			$this->mBackend = $specifiedBackend;
		} else {
			$this->showBackendSelector();
			return;
		}
		
		if ($subpage == '' ) {
			$subpage = $wgRequest->getVal( 'wpGroup' );
		}

		if ($subpage != '' && $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) )) {
			$this->doSubmit($subpage);
		} else if ($subpage != '') {
			$this->buildGroupView($subpage);
		} else {
			$this->buildMainView();
		}
	}
	
	function showBackendSelector() {
		global $wgUser, $wgOut;
		
		$wgOut->setSubTitle( wfMsg( 'grouprights-backendselect-subtitle' ) );
		$wgOut->addWikiMsg( 'grouprights-backendselect-text' );
		
		// Produce list.
		$sk = $wgUser->getSkin();
		$availableBackends = $this->getAvailableBackends( $wgUser );
		$list = '';
		foreach( $availableBackends as $backend ) {
			$text = wfMsg( "rights-backend-$backend" );
			$link = $sk->link( $this->getTitle(), $text, array(), array( 'backend' => $backend ) );
			$list .= Xml::tags( 'li', null, $link );
		}
		
		$list = Xml::tags( 'ul', null, $list );
		
		$wgOut->addHTML( $list );
	}
	
	function getAllGroups() {
		return $this->getBackend()->getAllGroups();
	}

	function buildMainView() {
		global $wgOut,$wgUser,$wgScript;
		$sk = $wgUser->getSkin();

		$groups = $this->getAllGroups();
		
		// Existing groups
		$html = Xml::openElement( 'fieldset' );
		$html .= Xml::element( 'legend', null, wfMsg( 'grouprights-existinggroup-legend' ) );
		
		$wgOut->addHTML( $html );

		if (count($groups)) {
			$wgOut->addWikiMsg( 'grouprights-grouplist' );
			$wgOut->addHTML( '<ul>' );

			foreach ($groups as $group) {
				$editLink = $sk->link( $this->getTitle( $group ), wfMsg( 'grouprights-editlink'), array(), array( 'backend' => $this->mBackend ) );
				$text = htmlspecialchars($group) ." ($editLink)";

				$wgOut->addHTML( "<li> $text </li>" );
			}
		} else {
			$wgOut->addWikiMsg( 'grouprights-nogroups' );
		}

		$wgOut->addHTML( Xml::closeElement( 'ul' ) . Xml::closeElement( 'fieldset' ) );

		// "Create a group" prompt
		$html = Xml::openElement( 'fieldset' ) . Xml::element( 'legend', null, wfMsg( 'grouprights-newgroup-legend' ) );
		$html .= wfMsgExt( 'grouprights-newgroup-intro', array( 'parse' ) );
		$html .= Xml::openElement( 'form', array( 'method' => 'post', 'action' => $wgScript, 'name' => 'grouprights-newgroup' ) );
		$html .= Xml::hidden( 'title',  $this->getTitle()->getPrefixedText() );
		$html .= Xml::hidden( 'backend', $this->mBackend );
		
		$fields = array( 'grouprights-newgroupname' => wfInput( 'wpGroup', 45 ) );
		
		$html .= wfBuildForm( $fields, 'grouprights-creategroup-submit' );
		$html .= Xml::closeElement( 'form' );
		$html .= Xml::closeElement( 'fieldset' );
		
		$wgOut->addHTML( $html );
	}
	
	function buildGroupView( $group ) {
		global $wgOut, $wgUser, $wgScript;
		
		$backend = $this->getBackend();
		
		$wgOut->setSubtitle( wfMsg( 'grouprights-subtitle', $group ) );
		
		$html = Xml::openElement( 'fieldset' ) . Xml::element( 'legend', null, wfMsg( 'grouprights-fieldset', $group ) );
		$html .= Xml::openElement( 'form', array( 'method' => 'post', 'action' => $this->getTitle( $group)->getLocalUrl(), 'name' => 'grouprights-newgroup' ) );
		$html .= Xml::hidden( 'wpGroup', $group );
		$html .= Xml::hidden( 'wpEditToken', $wgUser->editToken() );
		$html .= Xml::hidden( 'backend', $this->mBackend );
		
		$fields = array();
		
		$fields['grouprights-editgroup-name'] = $group;
		$fields['grouprights-editgroup-display'] = wfMsgExt( 'grouprights-editgroup-display-edit', array( 'parseinline' ), $group, User::getGroupName( $group ) );
		$fields['grouprights-editgroup-member'] = wfMsgExt( 'grouprights-editgroup-member-edit', array( 'parseinline' ), $group, User::getGroupMember( $group ) );
		$fields['grouprights-editgroup-members'] = wfMsgExt( 'grouprights-editgroup-members-link', array( 'parseinline' ), $group, User::getGroupMember( $group ) );
		
		// Allow backends to add extras here
		$backend->doExtraGroupForm( $fields, $group );
		
		$fields['grouprights-editgroup-perms'] = $this->buildCheckboxes($group);
		
		$changeable = $backend->getChangeableGroups( array($group) );
		
		foreach( $changeable as $type => $groups ) {
			$changeable[$type] = implode( ', ', $groups );
		}
		
		$fields['grouprights-editgroup'] = '';

		$editTypes = array( 'add', 'remove', 'add-self', 'remove-self' );
		foreach( $editTypes as $type ) {
			$fields["grouprights-editgroup-$type"] = wfInput( "wpGroupChange-$type", 45, $changeable[$type] );
		}
		
		$fields['grouprights-editgroup-reason'] = wfInput( 'wpReason', 45 );
		
		$html .= wfBuildForm( $fields, 'grouprights-editgroup-submit' );
		
		$html .= Xml::closeElement( 'form' );
		$html .= Xml::closeElement( 'fieldset' );
		
		$wgOut->addHTML( $html );
		
		$backend->showGroupLogFragment( $group, $wgOut );
	}
	
	function rightEditable( $group, $right ) {
		return $this->getBackend()->rightEditable( $group, $right );
	}

	function buildCheckboxes( $group ) {
		
		$rights = User::getAllRights();
		$assignedRights = $this->getAssignedRights( $group );
		
		sort($rights);
		
		$checkboxes = array();
		
		foreach( $rights as $right ) {
			# Build a checkbox.
			$checked = in_array( $right, $assignedRights );
			$attribs = array();
			if ( !$this->rightEditable( $group, $right ) ) {
				$attribs['disabled'] = 'disabled';
			}
			
			$checkbox = Xml::checkLabel( User::getRightDescription( $right ), 
				"wpRightAssigned-$right", "wpRightAssigned-$right", $checked, $attribs );
			
			$checkboxes[] = "<li>$checkbox</li>";
		}
		
		$count = count($checkboxes);
		
		$firstCol = round($count/2);
		
		$checkboxes1 = array_slice($checkboxes, 0, $firstCol);
		$checkboxes2 = array_slice($checkboxes, $firstCol );
		
		$html = '<table><tbody><tr><td><ul>';
		
		foreach( $checkboxes1 as $cb ) {
			$html .= $cb;
		}
		
		$html .= '</ul></td><td><ul>';
		
		foreach( $checkboxes2 as $cb ) {
			$html .= $cb;
		}
		
		$html .= '</ul></td></tr></tbody></table>';
		
		return $html;
	}
	
	function getAssignedRights( $group ) {
		return $this->getBackend()->getGroupPermissions( array($group) );
	}
	
	function beginTransaction() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
	}
	
	function endTransaction() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->commit();
	}
	
	function doSubmit( $group ) {
		global $wgRequest,$wgOut,$wgScript,$wgUser;
		
		$newRights = array();
		$addRights = array();
		$removeRights = array();
		$oldRights = $this->getAssignedRights( $group );
		$allRights = User::getAllRights();
		
		$reason = $wgRequest->getVal( 'wpReason', '' );
		
		foreach ($allRights as $right) {
			$alreadyAssigned = in_array( $right, $oldRights );
			
			if ($wgRequest->getCheck( "wpRightAssigned-$right" )) {
				$newRights[] = $right;
			}
			
			if (!$alreadyAssigned && $wgRequest->getCheck( "wpRightAssigned-$right" )) {
				$addRights[] = $right;
			} else if ($alreadyAssigned && !$wgRequest->getCheck( "wpRightAssigned-$right" ) ) {
				$removeRights[] = $right;
			} # Otherwise, do nothing.
		}
		
		$backend = $this->getBackend();
		
		// Assign the rights.
		if (count($addRights)>0)
			$backend->assignRights( $group, $addRights );
		if (count($removeRights)>0)
			$backend->revokeRights( $group, $removeRights );
		
		// Log it
		if (!(count($addRights)==0 && count($removeRights)==0))
			$backend->addGroupLogEntry( $group, $addRights, $removeRights, $reason, $wgUser );
			
		// Changeable groups
		$changeableGroups = $backend->getChangeableGroups( array( $group ) );
		$newChangeableGroups = array();
		
		$editTypes = array( 'add' => 'addable', 'remove' => 'removable', 'add-self' => 'addself', 'remove-self' => 'removeself' );
		foreach( $editTypes as $type => $var ) {
			$newChangeableGroups[$var] = array_map( 'trim', explode( ',', $wgRequest->getVal( "wpGroupChange-$type" ) ) );
		}
		extract($newChangeableGroups);
		$backend->setChangeableGroups( $group, $addable, $removable, $addself, $removeself );

		// Clear the cache
		$backend->invalidateGroupCache( $group );
		
		// Do extra stuff.
		$backend->doExtraGroupSubmit( $group, $reason, $wgRequest);
		
		// Display success
		$sk = $wgUser->getSkin();
		$wgOut->setSubTitle( wfMsg( 'grouprights-editgroup-success' ) );
		$wgOut->addWikiMsg( 'grouprights-editgroup-success-text', $group );
		$wgOut->addHTML( $sk->link( $this->getTitle( ), wfMsg( 'grouprights-return' ), array(), array( 'backend' => $this->mBackend ) ) );
	}
}
