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
	
	function __construct( $name = null ) {
		$this->name = $name;
		$this->props = array();
		$this->changed = array();
	}
	function set( $name, $value ) {
		$this->changed[] = $name;
		$this->props[$name] = $value;
	}
	
	function commit() {
		global $wgAuth;
		$dbw = $wgAuth->getDB( DB_WRITE );
		
		$insert = array();
		$timestamp = $dbw->timestamp();
		
		foreach ( $this->props as $name => $value ) {
			$insert[] = array( 
				'up_timestamp' => $timestamp,
				'up_user' => $this->name,
				'up_name' => $name,
				'up_value' => $value 
			);
		}
		$dbw->insert( 'user_props', $insert, __METHOD__ );
	}
}