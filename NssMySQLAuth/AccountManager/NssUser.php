<?php

class NssUser {
	function __construct( $name ) {
		$this->name = $name;
		
		$this->loaded = false;
		
		// Set default values to null
		$this->uid = $this->gid = $this->home = $this->active = $this->email = null; 
		$this->exists = false;
		
		$this->group = '';
		$this->properties = new NssProperties();	
	}
	
	function load() {
		if ( $this->loaded )
			return;
		
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		
		// Load the user existence from passwd
		$result = $dbr->select( 'passwd',
			array( 'pwd_uid', 'pwd_gid', 'pwd_home', 'pwd_active', 'pwd_email' ),
		 	array( 'pwd_name' => $this->name ),
		 	__METHOD__ 
		);
		$row = $result->fetchObject();
		$this->loadFromRow( $row );
	}
				
	function loadFromRow( $row ) {
		$this->exists = (bool)$row;
		if ($this->exists) {
			// Extract props from row
			$this->uid = $row->pwd_uid;
			$this->gid = $row->pwd_gid;
			$this->home = $row->pwd_home;
			$this->active = $row->pwd_active;
			$this->email = $row->pwd_email;
		
			$this->group = NssGroup::nameFromGid( $this->gid );
			$this->properties = NssProperties::forUser( $this->name );
		}
			
		$this->loaded = true;
	}
	
	function setHome( $home ) {
		$this->home = $home;
		$this->properties->set( 'home', $home );
	}
	function setActive( $active ) {
		$this->active = $active;
		$this->properties->set( 'active', $active );
	}
	function setEmail( $email ) { 
		$this->email = $email;
		$this->properties->set( 'email', $email );
	}
	
	function get( $name ) {
		switch ( $name ) {
			case 'username':
				return $this->name;
			case 'home':
				return $this->home;
			case 'active':
				return $this->active;
			case 'email':
				return $this->email;
			default:
				return $this->properties->get( $name );		
		}
	}
	function set( $name, $value ) {
		switch ( $name ) {
			case 'username':
				return;
			case 'home':
				$this->home = $value;
				break;
			case 'active':
				$this->active = $value;
				break;
			case 'email':
				$this->email = $value;
				break;
		}
		return $this->properties->set( $name, $value );
	}
	
	function commit() {
		global $wgAuth;
		$dbw = $wgAuth->getDB( DB_WRITE );
		
		$dbw->update( 'passwd',
			array( 'pwd_email' => $this->email, 'pwd_active' => $this->active ),
			array( 'pwd_name' => $this->name ),
			__METHOD__
		);
		
		$this->properties->commit();
		$dbw->immediateCommit();
	}
	
	public static function fetchNames() {
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		
		$res = $dbr->select( 'passwd', 'pwd_name', array(), __METHOD__ );
		
		$names = array();
		while ( $row = $res->fetchObject() )
			$names[] = $row->pwd_name;
		return $names;
	}
}
	