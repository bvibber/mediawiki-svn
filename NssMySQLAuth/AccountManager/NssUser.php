<?php

class NssUser {
	function __construct( $name ) {
		$this->name = $name;
		
		$this->loaded = false;
		
		// Set default values to null
		$this->uid = $this->gid = $this->home = $this->active = $this->email = null; 
		$this->exists = false;	}
	
	function load() {
		if ( $this->loaderd )
			return;
		
		global $wgAuth;
		$dbw = $wgAuth->getDB( DB_READ );
		
		// Load the user existence from passwd
		$result = $dbw->select( 'passwd',
			array( 'pwd_uid', 'pwd_gid', 'pwd_home', 'pwd_active', 'pwd_email' ),
		 	array( 'pwd_name', $this->name ),
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
			$this->gid = $row->pwd_home;
			$this->home = $row->pwd_home;
			$this->active = $row->pwd_active;
			$this->email = $row->pwd_home;
		
			$this->group = NssGroup::nameFromGid( $this->gid );
			$this->properties = NssProperties::forUser( $this->name );
		} else {
			$this->group = '';
			$this->properties = new NssProperties();
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
	function set( $name, $value ) {
		$this->properties->set( $name, $value );
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
}
	