<?php

class NssProperties {
	private static $users = array();	
	public static function forUser( $username ) {
		if ( isset( self::$users[$username] ) )
			return self::$users[$username];
		
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		$res = $dbr->select( 'user_props',
			array( 'up_name', 'up_value' ),
			array( 'up_user' => $username ),
			__METHOD__,
			array( 'ORDER BY' => 'up_timestamp ASC' )
			);
		
		$props = array();
		while ( $row = $res->fetchObject() ) {
			$props[$row->up_name] = $row->up_value;
		}
		
		$propObj = new self( $username );
		$propObj->props = $props;
		self::$users[$username] = $propObj;
		return $propObj;
	}
	public static function getAllUsers() {
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		$res = $dbr->select( 'user_props',
			array( 'up_user', 'up_name', 'up_value' ),
			array(),
			__METHOD__,
			array( 'ORDER BY' => 'up_timestamp ASC' )
			);		
		
		$users = array();
		while ( $row = $res->fetchObject() ) {
			if ( !isset( $users[$row->up_user] ) )
				$users[$row->up_user] = array();
			$users[$row->up_user][$row->up_name] = $row->up_value;
		}
		return $users;
	}
	
	function __construct( $name = null ) {
		$this->name = $name;
		$this->props = array();
		$this->changed = array();
	}
	
	function get( $name ) {
		return $this->props[$name];
	}
	function set( $name, $value ) {
		if ( $this->props[$name] == $value )
			return false;
		$this->changed[] = $name;
		$this->props[$name] = $value;
		return true;
	}
	
	function commit() {
		global $wgAuth;
		$dbw = $wgAuth->getDB( DB_WRITE );
		
		$insert = array();
		$timestamp = $dbw->timestamp();
		
		foreach ( $this->changed as $name ) {
			$insert[] = array( 
				'up_timestamp' => $timestamp,
				'up_user' => $this->name,
				'up_name' => $name,
				'up_value' => $this->props[$name] 
			);
		}
		$dbw->insert( 'user_props', $insert, __METHOD__ );
	}
	
	public static function getAll() { 
		global $wgUserProperties;
		return array_merge( array( 'username', 'email', 'active' ), $wgUserProperties ); 
	}
}