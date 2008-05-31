<?php
/**
 * @author Daniel Friesen <dan_the_man@telus.net>
 * @copyright Copyright © 2008 Daniel Friesen
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/* Overall load states for extensions */
define('EXTENSION_LOAD_INCOMPLETE',0);
define('EXTENSION_LOAD_UNDERGOING',1);
define('EXTENSION_LOAD_COMPLETE',2);

class Extension {
	
	static $mLoadState = EXTENSION_LOAD_INCOMPLETE;
	static $mLoadExtensions = array();
	
	/**
	 * Que an extension into loading. (Used in Setup.php, don't use elsewhere)
	 * 
	 * @param String $extension Name of extension to load
	 * @return bool If the extension could be loaded
	 */
	static function StartLoadExtension( $extension, $config = true ) {
		# Halt any invalid input
		if( !is_string($extension) || is_numeric($extension) {
			wfDebug(__METHOD__.': Invalid extension input'. var_export($extension,true));
			return false;
		}
		if( self::$mLoadState == EXTENSION_LOAD_INCOMPLETE ) {
			wfDebug(__METHOD__.': Extension loading complete, cannot load '.$extension);
			return false;
		}
		if( isset(self::$mLoadExtensions[$extension]) ) {
			wfDebug(__METHOD__.': Duplicate extension name '.$extension);
			return false;
		}
		# Don't load extensions disabled in this way
		if( $config == false ) return false;
		
		self::$mLoadExtensions[$extension] = $config;
		
		return true;
	}
	
	static function DoLoadExtensions() {
		global $wgExtensions, $wgExtensionPaths;
		if( self::$mLoadState != EXTENSION_LOAD_INCOMPLETE ) return false;
		self::$mLoadState = EXTENSION_LOAD_UNDERGOING;
		
		// TODO: Split this into three loops.
		//   First one loads extension classes
		//   Second one orders and adds extensions acording to any dependencies extensions might have
		//   And then the third actually initializes the extensions
		foreach( self::$mLoadExtensions as $extension => $config ) {
			$ePath = null;
			foreach( $wgExtensionPaths as $path ) {
				$file = "$path/$extension/Extension.inc"
				if( !file_exists($file) ) continue;
				$ePath = $file
			}
			if( !$ePath ) {
				wfDebug(__METHOD__.': Could not find Extension.inc file for for '. $extension);
				continue;
			}
			
			# Load the extension's class if not in existence
			if( !class_exists($extension) ) require_once( $ePath );
			
			# If that class isn't loaded at this point, something is broken with the extension
			if( !class_exists($extension) ) {
				wfDebug(__METHOD__.': Broken Extension: Could not load extension class for '. $extension);
				continue;
			}
			# The class inside of Extension.inc must be a subclass of class Extension.
			if( !is_subclass_of( $extension, __CLASS__) ) {
				wfDebug(__METHOD__.': Broken Extension: Class '.$extension.' inside of Extension.inc is not subclass of Extension class');
				continue;
			}
			
			# Pre-Initialization
			$e = new $extension;
			# Set configuration options
			if( is_array($config) || is_object($config) ) {
				foreach( $config as $option => $value ) {
					$option = 'c' . ucfirst($option);
					$e->$option = $value;
				}
			}
			# Initialization
			if( method_exists($e, 'init') ) $e->init();
			# Add to list now
			$wgExtensions[$extension] = $e;
			
		}
		
		self::$mLoadState = EXTENSION_LOAD_COMPLETE;
		return true;
	}
	
#------------------------------------------------------------------------------
# Helper functions for extensions
#------------------------------------------------------------------------------
	
	/**
	 * Helper function wrapped arround wfMsg, uses extensions's prefix and loads messages on use
	 */
	function msg( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsg' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgNoTrans, uses extensions's prefix and loads messages on use
	 */
	function msgNoTrans( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgNoTrans' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgForContent, uses extensions's prefix and loads messages on use
	 */
	function msgForContent( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgForContent' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgForContentNoTrans, uses extensions's prefix and loads messages on use
	 */
	function msgForContentNoTrans( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgForContentNoTrans' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgNoDB, uses extensions's prefix and loads messages on use
	 */
	function msgNoDB( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgNoDB' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgNoDBForContent, uses extensions's prefix and loads messages on use
	 */
	function msgNoDBForContent( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgNoDBForContent' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgHtml, uses extensions's prefix and loads messages on use
	 */
	function msgHtml( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgHtml' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	/**
	 * Helper function wrapped arround wfMsgWikiHtml, uses extensions's prefix and loads messages on use
	 */
	function msgWikiHtml( $msg /*, ... */ ) {
		$args = func_get_args();
		array_unshift( $args, 'wfMsgWikiHtml' );
		return call_user_func_array( array( $this, '_msgHelper' ), $args );
	}
	
	/**
	 * Backend for wfMsg* helper functions
	 * @access private
	 */
	function _msgHelper( $func, $msg, /*, ... */ ) {
		$args = func_get_args();
		$func = array_shift($args);//Shift off $func
		$msg  = array_shift($args);//Shift off $msg
		$prefix = $this->getMessageData('prefix');
		if( $prefix ) $msg = "$prefix-$msg";
		array_unshift($args, $msg);// Return altered $msg to array
		# Dynamically load the messages when the extension calls the helpers
		wfLoadExtensionMessages($this->getMessageData('id'));
		return call_user_func_array( $func, $args );
	}
	
}