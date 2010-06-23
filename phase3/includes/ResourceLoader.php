<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author Roan Kattouw
 *
 */

/**
 * TODO: Class description
 */
class ResourceLoader {
	/**
	 * List of core scripts to include if the "core" module is specified - it's like a bucket
	 */
	private static $coreScripts = array(
		'jquery' => 'resources/core/jquery-1.4.2.min.js',
		'mw' => 'resources/core/mw.js',
	);
	/**
	 * List of modules.
	 * 
	 * Format:
	 *	'modulename' => array(
	 * 		'script' => 'resources/foo/bar.js',
	 * 		'loader' => 'resources/foo/loader.js',
	 * 		'style' => 'resources/foo/bar.css',
	 * 		'messages' => array( 'messagekey1', 'messagekey2' )
	 * 	);
	 * 'script' and 'loader' are mandatory.
	 */
	public static $modules = array(
		'test' => array(
			'script' => 'resources/test/test.js',
			'loader' => 'resources/test/loader.js',
		),
		'foo' => array(
			'script' => 'resources/test/foo.js',
			'loader' => 'resources/test/loader.js',
		),
		'bar' => array(
			'script' => 'resources/test/bar.js',
			'loader' => 'resources/test/loader.js',
		),
		'buz' => array(
			'script' => 'resources/test/baz.js',
			'loader' => 'resources/test/loader.js',
		),
		'baz' => array(
			'script' => 'resources/test/buz.js',
			'loader' => 'resources/test/loader.js',
		),
		'wikibits' => array(
			'script' => 'skins/common/wikibits.js',
			'loader' => 'skins/common/loader.js',
		),
	);
	
	private $loadedModules = array();
	private $includeCore = false;
	
	private $useJSMin = false;
	private $useCSSMin = true;
	private $useCSSJanus = true;
	
	private $lang;
	
	public function __construct( $lang ) {
		$this->lang = $lang;
	}
	
	/**
	 * Add a module to the output. This includes the module's
	 * JS itself, its style and its messages.
	 * @param $module string Module name
	 */
	public function addModule( $module ) {
		if ( $module == 'core' ) {
			$this->includeCore = true;
		} else if ( isset( self::$modules[$module] ) ) {
			$this->loadedModules[] = $module;
		} else {
			// We have a problem, they've asked for something we don't have!
		}
	}
	
	public function setUseJSMin( $use ) {
		$this->useJSMin = $use;
	}
	
	public function setUseCSSMin( $use ) {
		$this->useCSSMin = $use;
	}
	
	public function setUseCSSJanus( $use ) {
		$this->useCSSJanus = $use;
	}
	
	public function getOutput() {
		// Because these are keyed by module, in the case that more than one module asked for the same script only the
		// first will end up being registered - the client loader can't handle multiple modules per implementation yet,
		// so this is fine, but causes silent failure it strange abusive cases
		$this->loadedModules = array_unique( $this->loadedModules );
		$retval = '';
		
		if ( $this->includeCore ) {
			// TODO: file_get_contents() errors?
			// TODO: CACHING!
			foreach ( self::$coreScripts as $script ) {
				if ( file_exists( $script ) ) {
					$retval .= file_get_contents( $script );
				}
			}
			$retval .= $this->getLoaderJS();
		}
		
		/*
		 * Skin::makeGlobalVariablesScript needs to be modified so that we still output the globals for now, but also
		 * put them into the initial payload like this:
		 * 
		 * 		// Sets the inital configuration
		 * 		mw.config.set( { 'name': 'value', ... } );
		 * 
		 * Also, the naming of these variables is horrible and sad, hopefully this can be worked on
		 */
		
		// Get messages in one go
		$blobs = MessageBlobStore::get( $this->lang, $this->loadedModules );
		
		// TODO: file_get_contents() errors?
		// TODO: CACHING!
		foreach ( $this->loadedModules as $module ) {
			$mod = self::$modules[$module];
			$script = $style = '';
			$messages = isset( $blobs[$module] ) ? $blobs[$module] : '';
			if ( file_exists( $mod['script'] ) ) {
				$script = file_get_contents( $mod['script'] );
			}
			if ( isset( $mod['style'] ) && file_exists( $mod['style'] ) ) {
				$css = file_get_contents( $mod['style'] );
				if ( $this->useCSSJanus ) {
					$css = $this->cssJanus( $css );
				}
				if ( $this->useCSSMin ) {
					$css = $this->cssMin( $css, $mod['style'] );
				}
				$style = Xml::escapeJsString( $css );
			}
			
			$retval .= "mw.loader.implement( '$module', function() { $script }, '$style', { $messages } );\n";
		}
		
		if ( $this->useJSMin ) {
			$retval = $this->jsMin( $retval );
		}
		return $retval;
	}
	
	public function getLoaderJS() {
		$retval = '';
		// Only add each file once (just in case there are multiple modules in a single loader, which is common)
		$loaders = array();
		foreach ( self::$modules as $name => $module ) {
			// TODO: file_get_contents() errors?
			// TODO: CACHING!
			if ( !in_array( $module['loader'], $loaders ) && file_exists( $module['loader'] ) ) {
				$retval .= file_get_contents( $module['loader'] );
				$loaders[] = $module['loader'];
			}
		}
		// FIXME: Duplicated; centralize in doJSTransforms() or something?
		if ( $this->useJSMin ) {
			$retval = $this->jsMin( $retval );
		}
		return $retval;
	}
	
	public function jsMin( $js ) {
		global $wgMemc;
		$key = wfMemcKey( 'resourceloader', 'jsmin', md5( $js ) );
		$cached = $wgMemc->get( $key );
		if ( $cached !== false ) {
			return $cached;
		}
		$retval = JSMin::minify( $js );
		$wgMemc->set( $key, $retval );
		return $retval;
	}
	
	public function cssMin( $css, $file ) {
		global $wgMemc;
		$key = wfMemcKey( 'resourceloader', 'cssmin', md5( $css ) );
		$cached = $wgMemc->get( $key );
		if( $cached !== false ) {
			return $cached;
		}
		// TODO: Test how well this path rewriting stuff works with various setups
		$retval = Minify_CSS::minify( $css, array( 'currentDir' => dirname( $file ), 'docRoot' => '.' ) ); 
		$wgMemc->set( $key, $retval );
		return $retval;
	}
	
	public function cssJanus( $css ) {
		global $wgMemc;
		$key = wfMemcKey( 'resourceloader', 'cssjanus', md5( $css ) );
		$cached = $wgMemc->get( $key );
		if ( $cached !== false ) {
			return $cached;
		}
		$retval = $css; // TODO: Actually flip
		$wgMemc->set( $key, $retval );
		return $retval;
	}
}

class MessageBlobStore {
	/**
	 * Get the message blobs for a set of modules
	 * @param $lang string Language code
	 * @param $modules array Array of module names
	 * @return array An array of incomplete JSON objects (i.e. without the {} ) containing messages keys and their values. Array keys are module names.
	 */
	public static function get( $lang, $modules ) {
		// TODO: Invalidate blob when module touched
		if ( !count( $modules ) ) {
			return array();
		}
		// Try getting from the DB first
		$blobs = self::getFromDB( $lang, $modules );
		
		// Generate blobs for any missing modules and store them in the DB
		$missing = array_diff( $modules, array_keys( $blobs ) );
		foreach ( $missing as $module ) {
			$blob = self::generateMessageBlob( $lang, $module );
			if ( $blob ) {
				self::set( $lang, $module, $blob );
				$blobs[$module] = $blob;
			}
		}
		return $blobs;
	}
	
	/**
	 * Set the message blob for a given module in a given language
	 * @param $lang string Language code
	 * @param $module string Module name
	 * @param $blob string Incomplete JSON object, see get()
	 */
	public static function set( $lang, $module, $blob ) {
		$dbw = wfGetDb( DB_MASTER );
		$dbw->replace( 'msg_resource', array( array( 'mr_lang', 'mr_resource' ) ),
			array( array(
				'mr_lang' => $lang,
				'mr_module' => $module,
				'mr_blob' => $blob,
				'mr_timestamp' => wfTimestampNow(),
			) )
		);
	}
	
	private static function getFromDB( $lang, $modules ) {
		$retval = array();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'msg_resource', array( 'mr_blob', 'mr_resource' ),
			array( 'mr_resource' => $modules, 'mr_lang' => $this->lang ),
			__METHOD__
		);
		foreach ( $res as $row ) {
			$retval[$row->mr_resource] = $row->mr_blob;
		}
		return $retval;
	}
	
	private static function generateMessageBlob( $lang, $module ) {
		if ( !isset ( ResourceLoader::$modules[$module]['messages'] ) ) {
			return false;
		}
		$messages = array();
		foreach ( ResourceLoader::$modules[$module]['messages'] as $key ) {
			$encKey = Xml::escapeJsString( $key );
			$encValue = Xml::escapeJsString( wfMsg( $key ) ); // TODO: Use something rawer than wfMsg()?
			$messages[] = "'$encKey': '$encValue'";
		}
		return implode( ",", $messages );
	}
}