<?php

if (!defined('MEDIAWIKI'))
	die();

## Simple class for caching configuration data which needs to be retrieved per-pageview.

class ConfigurationCache {
	static $data = false;
	static $dirty = false;

	static function load() {
		if (self::$data !== false) {
			return;
		}
		
		global $wgMemc;
		
		self::$data = $wgMemc->get( wfMemcKey( 'configuration-cache' ) );
		self::$dirty = false;
		
		if ( !is_array( self::$data ) ) {
			self::$data = array();
		}
	}
	
	static function get( $key ) {
		self::load();
		
		return @self::$data[$key];
	}
	
	static function set( $key, $value ) {
		self::load();
		
		self::$data[$key] = $value;
		self::$dirty = true;
	}
	
	static function delete( $key ) {
		self::load();
		
		unset( self::$data[$key] );
		self::$dirty = true;
	}
	
	static function save(  ) {
		if (!self::$dirty) {
			return;
		}
		
		global $wgMemc;
		
		$wgMemc->set( wfMemcKey( 'configuration-cache' ), self::$data, 86400 );
	}
}