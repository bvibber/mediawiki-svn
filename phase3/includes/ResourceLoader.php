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
 * @author Roan Kattouw, Trevor Parscal
 */

/*
 * Dynamic JavaScript and CSS resource loading system
 * 
 * @example
 * 	// Registers a module with the resource loading system
 * 	ResourceLoader::register( 'foo', array(
 * 		// At minimum you must have a script file
 * 		'script' => 'resources/foo/foo.js',
 * 		// Optionally you can have a style file as well
 * 		'style' => 'resources/foo/foo.css',
 * 		// Only needed if you are doing something fancy with your loader, otherwise one will be generated for you
 * 		'loader' => 'resources/foo/loader.js',
 * 		// If you need any localized messages brought into the JavaScript environment, list the keys here
 * 		'messages' => array( 'foo-hello', 'foo-goodbye' ),
 * 		// Base-only scripts are special scripts loaded in the base-package
 * 		'base' => false,
 * 		// Debug-only scripts are special scripts that are only loaded when requested and while in debug mode
 * 		'debug' => false,
 * 	) );
 * @example
 * 	// Responds to a resource loading request
 * 	ResourceLoader::respond( $wgRequest );
 */
class ResourceLoader {
	
	/* Protected Static Members */
	
	protected static $modules = array();
	
	/* Protected Static Methods */
	
	/**
	 * Runs text through a filter, caching the filtered result for future calls
	 * 
	 * @param string $filter name of filter to run
	 * @param string $data text to filter, such as JavaScript or CSS text
	 * @param string $file path to file being filtered, (optional: only required for CSS to resolve paths)
	 * @return string filtered data
	 */
	protected static function filter( $filter, $data, $file = null ) {
		global $wgMemc;
		$key = wfMemcKey( 'resourceloader', $filter, md5( $data ) );
		$cached = $wgMemc->get( $key );
		if ( $cached !== false && $cached !== null ) {
			return $cached;
		}
		switch ( $filter ) {
			case 'minify-js':
				$result = JSMin::minify( $data );
				break;
			case 'minify-css':
				$result = Minify_CSS::minify( $data, array( 'currentDir' => dirname( $file ), 'docRoot' => '.' ) );
				break;
			case 'flip-css':
				$cssJanus = new CSSJanus();
				$result = $cssJanus::transform( $data, true, false );
				break;
			case 'strip-debug':
				$result = preg_replace( '/\n\s*mw\.log\(([^\)]*\))*\s*[\;\n]/U', "\n", $data );
				break;
			default:
				// Don't cache anything, just pass right through
				return $data;
		}
		$wgMemc->set( $key, $result );
		return $result;
	}
	/**
	 * Get a list of JSON encoded message lists for a set of modules, automatically caching JSON blobs in the database
	 * 
	 * @param string $lang language code
	 * @param array $modules module names
	 * @return array JSON objects containing message keys and values for each module, keyed by module name
	 */
	protected static function messages( $lang, $modules ) {
		// TODO: Invalidate blob when module touched
		if ( empty( $modules ) ) {
			return array();
		}
		// Try getting from the DB first
		$blobs = array();
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select(
			'msg_resource',
			array( 'mr_blob', 'mr_resource' ),
			array( 'mr_resource' => $modules, 'mr_lang' => $lang ),
			__METHOD__
		);
		foreach ( $result as $row ) {
			$blobs[$row->mr_resource] = $row->mr_blob;
		}
		// Generate blobs for any missing modules and store them in the DB
		$missing = array_diff( $modules, array_keys( $blobs ) );
		foreach ( $missing as $module ) {
			// Build message blob for module messages
			$messages = isset( static::$modules[$module]['messages'] ) ?
				array_keys( static::$modules[$module]['messages'] ) : false;
			if ( $messages ) {
				foreach ( ResourceLoader::$modules[$module]['messages'] as $key ) {
					$messages[$encKey] = wfMsgExt( $key, array( 'language' => $lang ) );
				}
				$blob = json_encode( $messages );
				// Store message blob
				$dbw = wfGetDb( DB_MASTER );
				$dbw->replace(
					'msg_resource',
					array( array( 'mr_lang', 'mr_resource' ) ),
					array( array(
						'mr_lang' => $lang,
						'mr_module' => $module,
						'mr_blob' => $blob,
						'mr_timestamp' => wfTimestampNow(),
					) )
				);
				// Add message blob to result
				$blobs[$module] = $blob;
			}
		}
		return $blobs;
	}
	
	/* Static Methods */
	
	/**
	 * Registers a module with the ResourceLoader system
	 * 
	 * @param mixed $module string of name of module or array of name/options pairs
	 * @param array $options module options (optional when using multiple-registration calling style)
	 * @return boolean false if there were any errors, in which case one or more modules were not registered
	 * 
	 * $options format:
	 * 	array(
	 * 		'script' => [string: path to file],
	 * 		'style' => [string: path to file, optional],
	 * 		'loader' => [string: path to file, optional],
	 * 		'messages' => [array: message keys, optional],
	 * 		'base' => [boolean: include in base package only, optional],
	 * 		'debug' => [boolean: include in debug mode only, optional],
	 * 	)
	 * 
	 * @todo We need much more clever error reporting, not just in detailing what happened, but in bringing errors to
	 * the client in a way that they can easily see them if they want to, such as by using FireBug
	 */
	public static function register( $module, $options = array() ) {
		// Allow multiple modules to be registered in one call
		if ( is_array( $module ) && empty( $options ) ) {
			$success = true;
			foreach ( $module as $name => $options ) {
				if ( !static::register( $name, $options ) ) {
					$success = false;
				}
			}
			return $success;
		}
		// Disallow duplicate registrations
		if ( isset( static::$modules[$module] ) ) {
			// A module has already been registered by this name
			return false;
		}
		// Always include a set of default options in each registration - more data, less isset() checks
		$options = array_merge( array(
			'script' => null,
			'style' => null,
			'messages' => null,
			'loader' => null,
			'base' => false,
			'debug' => false,
		), $options );
		// Validate script option
		if ( !is_string( $options['script'] ) ) {
			// Module does not include a script
			return false;
		}
		if ( !file_exists( $options['script'] ) ) {
			// Script file does not exist
			return false;
		}
		if ( $options['loader'] !== null && !file_exists( $options['loader'] ) ) {
			// Loader file does not exist
			return false;
		}
		if ( $options['style'] !== null && !file_exists( $options['style'] ) ) {
			// Style file does not exist
			return false;
		}
		static::$modules[$module] = $options;
	}
	/*
	 * Outputs a response to a resource load-request, including a content-type header
	 * 
	 * @param array $modules module names to include in the request
	 * @param array $options options which affect the content of the response (optional)
	 * 
	 * $options format:
	 * 	array(
	 * 		'user' => [boolean: true for logged in, false for anon, optional, state of current user by default],
	 * 		'lang' => [string: language code, optional, code of default language by default],
	 * 		'skin' => [string: name of skin, optional, name of default skin by default],
	 * 		'dir' => [string: 'ltr' or 'rtl', optional, direction of lang by default],
	 * 		'base' => [boolean: true to include base-only scripts, optional, false by default],
	 * 		'debug' => [boolean: true to include debug-only scripts, optional, false by default],
	 * 	)
	 */
	public static function respond( WebRequest $request ) {
		global $wgUser, $wgLang, $wgDefaultSkin;
		// Fallback on system settings
		$parameters = array_merge( array(
			'user' => $request->getBool( 'user', $wgUser->isLoggedIn() ),
			'lang' => $request->getVal( 'lang', $wgLang->getCode() ),
			'skin' => $request->getVal( 'skin', $wgDefaultSkin ),
			'base' => $request->getBool( 'base' ),
			'debug' => $request->getBool( 'debug' ),
		) );
		$modules = explode( '|', $request->getVal( 'modules' ) );
		// Get the direction from the requested language
		if ( !isset( $parameters['dir'] ) ) {
			$lang = $wgLang->factory( $parameters['lang'] );
			$parameters['dir'] = $lang->getDir();
		}
		// Optionally include all base-only scripts
		$base = array();
		if ( $parameters['base'] ) {
			foreach ( static::$modules as $name => $options ) {
				if ( $options['base'] ) {
					// Only include debug scripts in debug mode
					if ( $options['debug'] ) {
						if ( $parameters['debug'] ) {
							$base[] = $name;
						}
					} else {
						$base[] = $name;
					}
				}
			}
		}
		// Include requested modules which have been registered - ignoring any which have not been
		$other = array();
		foreach ( static::$modules as $name => $options ) {
			if ( in_array( $name, $modules ) && !in_array( $name, $base )) {
				$other[] = $name;
			}
		}
		// Use output buffering
		ob_start();
		// Optionally include base modules
		if ( $parameters['base'] ) {
			// Base modules
			foreach ( $base as $module ) {
				readfile( static::$modules[$module]['script'] );
			}
			// All module loaders - keep track of which loaders have been included to prevent multiple modules with a
			// single loader causing the loader to be included more than once
			$loaders = array();
			foreach ( self::$modules as $name => $options ) {
				if ( $options['loader'] !== null && !in_array( $options['loader'], $loaders ) ) {
					readfile( $options['loader'] );
					$loaders[] = $options['loader'];
				}
			}
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
		
		// Other modules
		$blobs = static::messages( $parameters['lang'], $other );
		foreach ( $other as $module ) {
			// Script
			$script = file_get_contents( static::$modules[$module]['script'] );
			if ( !$parameters['debug'] ) {
				$script = static::filter( 'strip-debug', $script );
			}
			// Style
			$style = static::$modules[$module]['style'] ? file_get_contents( static::$modules[$module]['style'] ) : '';
			if ( $style !== '' ) {
				if ( $parameters['dir'] == 'rtl' ) {
					$style = static::filter( 'flip-css', $style );
				}
				$style = Xml::escapeJsString(
					static::filter( 'minify-css', $style, static::$modules[$module]['style'] )
				);
			}
			// Messages
			$messages = isset( $blobs[$module] ) ? $blobs[$module] : '{}';
			// Output
			echo "mw.loader.implement(\n'{$module}', function() { {$script} }, '{$style}', {$messages}\n);\n";
		}
		// Set headers -- when we support CSS only mode, this might change!
		header( 'Content-Type: text/javascript' );
		// Final processing
		if ( $parameters['debug'] ) {
			ob_end_flush();
		} else {
			echo static::filter( 'minify-js', ob_get_clean() );
		}
	}
}