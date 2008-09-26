<?php

class SpecialAccountManager extends SpecialPage {
	function __construct() {
		parent::__construct( 'AccountManager', 'accountmanager', false );
	}
	
	function execute() {
		global $wgUser;
		if( !$this->userCanExecute( $wgUser ) )
			return $this->displayRestrictionError();

		$this->users = UserProps::fetchAllUsers();
		if( $this->processData() === true)
			$this->showSuccess();
		$this->constructForm();
	}

	function showSuccess() {
		global $wgOut;
		$wgOut->addHTML( Xml::element('p', array(), 'Your changes have been successfully updated' ) );
	}

	function constructForm() {
		global $wgOut, $wgScript;
		global $wgUserProperties, $wgActivityModes;

		// TODO: wfMsg etc.
		$wgOut->addHTML( Xml::openElement( 'form', array(
			'action' => $wgScript,
			'method' => 'post' )
		) );

		$wgOut->addHTML("<table id=\"userprops\" border=\"1\">\n\t<tr>".
			"<th>Username</th><th>Email</th><th>Active</th>");
		foreach( $wgUserProperties as $i ) 
			$wgOut->addHTML( Xml::element( 'th', null, $i ) );
		$wgOut->addHTML("</tr>\n\n");

		foreach( $this->users as $user ) {
			$name = $user->getName();
			$row = "\t<tr>";
			$row .= Xml::element( 'td', null, $name );
			$row .= "<td>".Xml::input( "am-{$name}-email", 40, $user->getEmail() )."</td>";
			$select = new XmlSelect( "am-{$name}-active" );
			$select->setDefault( $user->getActive() );
			foreach( $wgActivityModes as $key )
				$select->addOption( $key );
			$row .= "<td>".$select->getHTML()."</td>";

			$props = $user->getProps();
			foreach( $wgUserProperties as $key ) {
				$value = isset( $props[$key] ) ? $props[$key] : '';
				$row .= "<td>".Xml::input( 
						"am-{$name}-{$key}", 40, $value
					)."</td>";
			}
			$row .= "</tr>\n";
			$wgOut->addHTML( $row );
		}
		
		$wgOut->addHTML( "</table>\n" );
		$wgOut->addHTML( "<div id=\"userprops-submit\">\n".
			Xml::hidden( 'title', 'Special:AccountManager' ).
			Xml::hidden( 'action', 'submit' ).
			Xml::element( 'input', array(
				'type' => 'submit',
				'value' => 'Save changes'
			) ).
			"</div>\n</form>" 
		);
	}

	function processData() {
		global $wgRequest, $wgUserProperties;
		if( !$wgRequest->wasPosted() || $wgRequest->getVal('action') != 'submit' )
			return;

		$post = $wgRequest->getValues();
		foreach( $post as $key => $value ) {
			if( substr( $key, 0, 3 ) != 'am-' )
				continue;
			$parts = explode( '-', $key, 3 );
			if( count( $parts ) != 3 )
				continue;

			$username = strtolower( $parts[1] );
			$keyname = strtolower( $parts[2] );
		
			if( !isset( $this->users[$username] ) )
				continue;

			if( !in_array( $keyname, $wgUserProperties ) )
				continue;
			
			$this->users[$username]->set( $keyname, $value );
		}

		foreach( $this->users as $user )
			$user->update();
		return true;
	}
}

class UserProps {
	static function fetchAllUsers() {
		$users = array();
		$res = self::select();
		while( $row = $res->fetchObject() ) {
			if( !isset( $users[$row->pwd_name] ) )
				$users[$row->pwd_name] = new self( $row->pwd_name, $row->pwd_email );
			$users[$row->pwd_name]->setInternal($row->up_name, $row->up_value);
		}
		$res->free();
		return $users;
	}
	function __construct( $username, $email = null, $active = null ) {
		$this->username = $username;
		$this->props = array();
		$this->email = $email;
		$this->active = $active;
	}
	function getProps() {
		return $this->props;
	}
	function getName() {
		return $this->username;
	}
	function getEmail() {
		return $this->email;
	}
	function setEmail( $email ) {
		$this->email = $email;
	}
	function getActive() {
		return $this->active;
	}
	function setActive( $active ) {
		$this->active = $active; 
	}

	static function select($username = null) {
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		$join = is_null( $username ) ? 'RIGHT JOIN' : 'JOIN';
		$where = is_null( $username ) ? array() : array( 'up_user' => $username );

		return $dbr->select(
			array( 'user_props', 'passwd' ),
			array( 'up_name', 'up_value', 'pwd_name', 'pwd_email', 'pwd_active' ),
			$where, 
			__METHOD__,
			array( 'ORDER BY' => 'up_timestamp DESC', 'DISTINCT' ),
			array( 'passwd' => array( $join, 'pwd_name = up_user' ) )
		);
	}

	function set($name, $value) {
		wfDebug( "{$this->username}: $name => $value\n" );
		$this->props[$name] = $value;
	}
	function setInternal($name, $value) {
		if( is_null( $this->props ) ) {
			$this->props = array();
			$this->old_props = array();
		}
		$this->old_props[$name] = $this->props[$name] = $value;
	}

	function update() {
		$diff = array_diff_assoc($this->props, $this->old_props);
		if( !count( $diff ) ) return;

		
		global $wgAuth;
		$dbw = $wgAuth->getDB( DB_WRITE );
		$timestamp = $dbw->timestamp();

		if( isset( $diff['email'] ) || isset( $diff['active'] ) ) {
			if ( isset( $diff['email'] ) ) $this->email = $diff['email'];
			if ( isset( $diff['active'] ) ) $this->active = $diff['active'];
			$dbw->update( 'passwd', 
				array( 'pwd_email' => $this->email, 'pwd_active' => $this->active ), 
				array( 'pwd_user' => $this->username ),
				__METHOD__
			);
			// Put it into user_props as well for history
		}

		$insert = array();
		foreach( $diff as $key => $value )
			$insert[] = array(
				'up_timestamp' => $timestamp,
				'up_user' => $this->username,
				'up_name' => $key,
				'up_value' => $value,
			);

		$dbw->insert( 'user_props', $insert, __METHOD__ );
	}
}
