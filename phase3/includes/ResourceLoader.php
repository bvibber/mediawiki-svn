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
		'wikibits' => array(
			'script' => 'skins/common/wikibits.js',
			'loader' => 'skins/common/loader.js',
		),
	);
	
	private $scripts = array();
	private $styles = array();
	private $loadedModules = array();
	
	private $useJSMin = true;
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
		$this->loadedModules[] = $module;
		$this->scripts[] = self::$modules[$module]['script'];
		if ( isset( self::$modules[$module]['style'] ) ) {
			$this->styles[] = self::$modules[$module]['script'];
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
		
	private function getStyleJS( $styles ) {
		$retval = '';
		foreach ( $styles as $style ) {
			// TODO: file_get_contents() errors?
			// TODO: CACHING!
			$css = file_get_contents( $style );
			if ( $this->useCSSJanus ) {
				$css = $this->cssJanus( $css );
			}
			if ( $this->useCSSMin ) {
				$css = $this->cssMin( $css );
			}
			$escCss = Xml::escapeJsString( $css );
			$retval .= "\$j( 'head' ).append( '<style>$escCSS</style>' );\n";
		}
		return $retval;
	}
	
	private function getMessagesJS( $modules ) {
		$blobs = array();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'msg_resource', 'msg_blob',
			array( 'msg_resource' => $modules, 'msg_lang' => $this->lang ),
			__METHOD__
		);
		foreach ( $res as $row ) {
			$blobs[] = $row->msg_blob;
		}
		return "mw.addMessages( {\n" . implode( ",\n", $blobs ) . "\n} );";
	}
	
	public function getOutput() {
		$this->scripts = array_unique( $this->scripts );
		$this->styles = array_unique( $this->styles );
		$this->loadedModules = array_unique( $this->loadedModules );
		$retval = '';
		
		/*
		 * Skin::makeGlobalVariablesScript needs to be modified so that we still output the globals for now, but also
		 * put them into the initial payload like this:
		 * 
		 * 		// Sets the inital configuration
		 * 		mw.config.set( { 'name': 'value', ... } );
		 * 
		 * Also, the naming of these variables is horrible and sad, hopefully this can be worked on
		 */
		
		foreach ( $this->scripts as $script ) {
			// TODO: file_get_contents() errors?
			// TODO: CACHING!
			$retval .= file_get_contents( $script );
		}
		$retval .= $this->getStyleJS( $this->styles );
		$retval .= $this->getMessagesJS( $this->loadedModules );
		
		if ( $this->useJSMin ) {
			$retval = $this->jsMin( $retval );
		}
		return $retval;
	}
	
	public function getLoaderJS() {
		$retval = '';
		foreach ( self::$modules as $name => $module ) {
			// TODO: file_get_contents() errors?
			// TODO: CACHING!
			$retval .= file_get_contents( $module['loader'] );
		}
		// FIXME: Duplicated; centralize in doJSTransforms() or something?
		if ( $this->useJSMin ) {
			$retval = $this->jsMin( $retval );
		}
		return $retval;
	}
	
	public function jsMin( $js ) {
		// TODO: Implement
		return $js;
	}
	
	public function cssMin( $css ) {
		// TODO: Implement
		return $css;
	}
	
	public function cssJanus( $css ) {
		// TODO: Implement
		return $css;
	}
}

class MessageBlobStore {
	/**
	 * Get the message blobs for a set of modules
	 * @param $lang string Language code
	 * @param $modules array Array of module names
	 * @return array An array of incomplete JSON objects (i.e. without the {} ) with messages keys and their values.
	 */
	public static function get( $lang, $modules ) {
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
		return implode( ",\n", $blobs );
	}
	
	public static function set( $lang, $module, $blob ) {
		$dbw = wfGetDb( DB_MASTER );
		// TODO: Timestamp stuff to handle concurrency
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