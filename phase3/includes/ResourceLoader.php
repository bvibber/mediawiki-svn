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
 * @author Trevor Parscal
 */

/*
 * Dynamic JavaScript and CSS resource loading system
 * 
 * @example
 * 	// Registers a module with the resource loading system
 * 	ResourceLoader::register( 'foo', array(
 * 		// Script or list of scripts to include when implementating the module (required)
 * 		'script' => 'resources/foo/foo.js',
 *		// List of scripts or lists of scripts to include based on the current language
 *		'locales' => array(
 *			'en-gb' => 'resources/foo/locales/en-gb.js',
 *		),
 * 		// Script or list of scripts to include only when in debug mode
 * 		'debug' => 'resources/foo/debug.js',
 * 		// If this module is going to be loaded before the mediawiki module is ready such as jquery or the mediawiki
 * 		// module itself, it can be included without special loader wrapping - this will also limit the module to not be
 * 		// able to specify needs, custom loaders, styles, themes or messages (any of the options below) - raw scripts
 * 		// get registered as 'ready' after the mediawiki module is ready, so they can be named as dependencies
 * 		'raw' => false,
 * 		// Modules or list of modules which are needed and should be used when generating loader code
 * 		'needs' => 'resources/foo/foo.js',
 * 		// Script or list of scripts which will cause loader code to not be generated - if you are doing something fancy
 * 		// with your dependencies this gives you a way to use custom registration code
 * 		'loader' => 'resources/foo/loader.js',
 * 		// Style-sheets or list of style-sheets to include
 * 		'style' => 'resources/foo/foo.css',
 *		// List of style-sheets or lists of style-sheets to include based on the skin - if no match is found for current
 *		// skin, 'default' is used - if default doesn't exist nothing is added
 *		'themes' => array(
 *			'default' => 'resources/foo/themes/default/foo.css',
 *			'vector' => 'resources/foo/themes/vector.foo.css',
 *		),
 * 		// List of keys of messages to include
 * 		'messages' => array( 'foo-hello', 'foo-goodbye' ),
 * 	) );
 * @example
 * 	// Responds to a resource loading request
 * 	ResourceLoader::respond( $wgRequest, $wgServer . $wgScriptPath . '/load.php' );
 */
class ResourceLoader {
	
	/* Protected Static Members */
	
	// List of modules and their options
	protected static $modules = array();
	
	/* Protected Static Methods */
	
	/**
	 * Runs text through a filter, caching the filtered result for future calls
	 * 
	 * @param {string} $filter name of filter to run
	 * @param {string} $data text to filter, such as JavaScript or CSS text
	 * @param {string} $file path to file being filtered, (optional: only required for CSS to resolve paths)
	 * @return {string} filtered data
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
				$result = CSSJanus::transform( $data, true, false );
				break;
			default:
				// Don't cache anything, just pass right through
				return $data;
		}
		$wgMemc->set( $key, $result );
		return $result;
	}
	/**
	 * Converts a multi-level array into a flat array containing the unique values of all leaf nodes
	 * 
	 * @param {array} $deep mutli-level array to flatten
	 * @param {array} $flat array to append flattened values to (used internally)
	 * @return {array} flattened array of leaf nodes
	 */
	protected static function flatten( $deep, $flat = array() ) {
		foreach ( $deep as $value ) {
			if ( is_array( $value ) ) {
				$flat = self::flatten( $value, $flat );
			} else {
				if ( $value ) {
					$flat[] = $value;
				}
			}
		}
		return array_unique( $flat );
	}
	/**
	 * Validates a file or list of files as existing
	 * 
	 * @param {mixed} string file name or array of any depth containing string file names as leaf nodes
	 * @throws {MWException} if one or more files do not exist
	 */
	protected static function validate( $files ) {
		if ( is_array( $files ) ) {
			$files = self::flatten( $files );
			foreach ( $files as $file ) {
				self::validate( $file );
			}
		} else {
			if ( !file_exists( $files ) ) {
				throw new MWException( 'File does not exist: ' . $files );
			}
		}
	}
	/**
	 * Reads a file or list of files and returns them as a string or outputs them into the current output buffer
	 * 
	 * @param {mixed} $files string file name or array of any depth containing string file names as leaf nodes
	 * @param {bool} $passthrough whether to return read data as a string or to output it directly to the current buffer
	 * @return {mixed} string of read data or null if $passthrough is true
	 */
	protected static function read( $files, $passthrough = false ) {
		if ( is_array( $files ) ) {
			$files = self::flatten( $files );
			$contents = '';
			foreach ( $files as $file ) {
				$contents .= self::read( $file );
			}
			return $contents;
		} else {
			if ( $passthrough ) {
				readfile( $files );
				echo "\n";
				return null;
			} else {
				return file_get_contents( $files ) . "\n";
			}
		}
	}
	
	/* Static Methods */
	
	/**
	 * Registers a module with the ResourceLoader system
	 * 
	 * @param {mixed} $module string of name of module or array of name/options pairs
	 * @param {array} $options module options (optional when using multiple-registration calling style)
	 * @return {boolean} false if there were any errors, in which case one or more modules were not registered
	 * 
	 * $options format:
	 * 	array(
	 * 		// Required module options
	 * 		'script' => 'dir/script.js' | array( 'dir/script1.js', 'dir/script2.js' ... ),
	 * 		// Optional module options
	 * 		'locales' => array(
	 * 			'[locale name]' => 'dir/locale.js' | '[locale name]' => array( 'dir/locale1.js', 'dir/locale2.js' ... )
	 * 			...
	 * 		),
	 * 		'debug' => 'dir/debug.js' | array( 'dir/debug1.js', 'dir/debug2.js' ... ),
	 * 		'raw' => true | false,
	 * 		// Non-raw module options
	 * 		'needs' => 'module' | array( 'module1', 'module2' ... )
	 * 		'loader' => 'dir/loader.js' | array( 'dir/loader1.js', 'dir/loader2.js' ... ),
	 * 		'style' => 'dir/file.css' | array( 'dir/file1.css', 'dir/file2.css' ... ),
	 * 		'themes' => array(
	 * 			'[skin name]' => 'dir/theme.css' | '[skin name]' => array( 'dir/theme1.css', 'dir/theme2.css' ... )
	 * 			...
	 * 		),
	 * 		'messages' => array( 'message1', 'message2' ... ),
	 * 	)
	 * 
	 * @todo We need much more clever error reporting, not just in detailing what happened, but in bringing errors to
	 * the client in a way that they can easily see them if they want to, such as by using FireBug
	 */
	public static function register( $module, $options = array() ) {
		// Allow multiple modules to be registered in one call
		if ( is_array( $module ) && empty( $options ) ) {
			foreach ( $module as $name => $options ) {
				self::register( $name, $options );
			}
			return;
		}
		// Disallow duplicate registrations
		if ( isset( self::$modules[$module] ) ) {
			// A module has already been registered by this name
			throw new MWException( 'Another module has already been registered as ' . $module );
		}
		// Always include a set of default options in each registration so we need not exaustively mark all options for
		// all modules when registering and also don't need to worry if the options are set or not later on
		$options = array_merge( array(
			'script' => null,
			'locales' => null,
			'raw' => false,
			// An empty array is used for needs to make json_encode output [] instead of null which is shorted and
			// results in easier to work with data on the client
			'needs' => array(),
			'loader' => null,
			'debug' => null,
			'style' => null,
			'themes' => null,
			'messages' => null,
		), $options );
		// Validate script option - which is required and must reference files that exist
		if ( !is_string( $options['script'] ) ) {
			throw new MWException( 'Module does not include a script: ' . $module );
		}
		// Validate options that reference files
		foreach ( array( 'script', 'locales', 'loader', 'debug', 'style', 'themes' ) as $option ) {
			if ( $options[$option] !== null ) {
				self::validate( $options[$option] );
			}
		}
		// Attach module
		self::$modules[$module] = $options;
	}
	/**
	 * Gets a map of all modules and their options
	 * 
	 * @return {array} list of modules and their options
	 */
	public static function getModules() {
		return self::$modules;
	}
	/*
	 * Outputs a response to a resource load-request, including a content-type header
	 * 
	 * @param {WebRequest} $request web request object to respond to
	 * @param {string} $server web-accessible path to script server
	 * 
	 * $options format:
	 * 	array(
	 * 		'user' => [boolean: true for logged in, false for anon, optional, state of current user by default],
	 * 		'lang' => [string: language code, optional, code of default language by default],
	 * 		'skin' => [string: name of skin, optional, name of default skin by default],
	 * 		'dir' => [string: 'ltr' or 'rtl', optional, direction of lang by default],
	 * 		'debug' => [boolean: true to include debug-only scripts, optional, false by default],
	 * 	)
	 */
	public static function respond( WebRequest $request, $server ) {
		global $wgUser, $wgLang, $wgDefaultSkin;
		// Fallback on system settings
		$parameters = array(
			'user' => $request->getVal( 'user', $wgUser->isLoggedIn() ),
			'lang' => $request->getVal( 'lang', $wgLang->getCode() ),
			'skin' => $request->getVal( 'skin', $wgDefaultSkin ),
			'debug' => $request->getVal( 'debug' ),
			'server' => $server,
		);
		// Mediawiki's WebRequest::getBool is a bit on the annoying side - we need to allow 'true' and 'false' values
		// to be converted to boolean true and false
		$parameters['user'] = $parameters['user'] === 'true' || $parameters['user'] === true ? true : false;
		$parameters['debug'] = $parameters['debug'] === 'true' || $parameters['debug'] === true ? true : false;
		// Get the direction from the requested language
		if ( !isset( $parameters['dir'] ) ) {
			$lang = $wgLang->factory( $parameters['lang'] );
			$parameters['dir'] = $lang->getDir();
		}
		// Build a list of requested modules excluding unrecognized ones which are collected into a list used to
		// register the unrecognized modules with error status later on
		$modules = array();
		$missing = array();
		foreach ( explode( '|', $request->getVal( 'modules' ) ) as $module ) {
			if ( isset( self::$modules[$module] ) ) {
				$modules[] = $module;
			} else {
				$missing[] = $module;
			}
		}
		// Use output buffering
		ob_start();
		// Output raw modules first and build a list of raw modules to be registered with ready status later on
		$ready = array();
		foreach ( $modules as $module ) {
			if ( self::$modules[$module]['raw'] ) {
				self::read( self::$modules[$module]['script'], true );
				if ( $parameters['debug'] && self::$modules[$module]['debug'] ) {
					self::read( self::$modules[$module]['debug'], true );
				}
				$ready[] = $module;
			}
		}
		// Special meta-information for the 'mediawiki' module
		if ( in_array( 'mediawiki', $modules ) ) {
			/*
			 * Skin::makeGlobalVariablesScript needs to be modified so that we still output the globals for now, but
			 * also put them into the initial payload like this:
			 * 
			 * // Sets the inital configuration
			 * mw.config.set( { 'name': 'value', ... } );
			 * 
			 * Also, the naming of these variables is horrible and sad, hopefully this can be worked on
			 */
			echo "mw.config.set( " . json_encode( $parameters ) . " );\n";
			// Generate list of registrations and collect all loader scripts
			$loaders = array();
			$registrations = array();
			foreach ( self::$modules as $name => $options ) {
				if ( $options['loader'] ) {
					$loaders[] = $options['loader'];
				} else {
					if ( empty( $options['needs'] ) && !in_array( $name, $ready ) && !in_array( $name, $missing ) ) {
						$registrations[$name] = $name;
					} else {
						$registrations[$name] = array( $name, $options['needs'] );
						if ( in_array( $name, $ready ) ) {
							$registrations[$name][] = 'ready';
						} else if ( in_array( $name, $missing ) ) {
							$registrations[$name][] = 'missing';
						}
					}
				}
			}
			// Include loaders
			self::read( $loaders, true );
			// Register modules without loaders
			echo "mw.loader.register( " . json_encode( array_values( $registrations ) ) . " );\n";
		}
		// Output non-raw modules
		$blobs = MessageBlobStore::get( $modules, $parameters['lang'] );
		foreach ( $modules as $module ) {
			if ( !self::$modules[$module]['raw'] ) {
				// Script
				$script = self::read( self::$modules[$module]['script'] );
				// Debug
				if ( $parameters['debug'] && self::$modules[$module]['debug'] ) {
					$script .= self::read( self::$modules[$module]['debug'] );
				}
				// Locale
				if ( isset( self::$modules[$module]['locales'][$parameters['lang']] ) ) {
					$script .= self::read( self::$modules[$module]['locales'][$parameters['lang']] );
				}
				// Style
				$style = self::$modules[$module]['style'] ? self::read( self::$modules[$module]['style'] ) : '';
				// Theme
				if ( isset( self::$modules[$module]['themes'][$parameters['skin']] ) ) {
					$style .= self::read( self::$modules[$module]['themes'][$parameters['skin']] );
				} else if ( isset( self::$modules[$module]['themes']['default'] ) ) {
					$style .= self::read( self::$modules[$module]['themes']['default'] );
				}
				if ( $style !== '' ) {
					if ( $parameters['dir'] == 'rtl' ) {
						$style = self::filter( 'flip-css', $style );
					}
					$style = Xml::escapeJsString(
						$parameters['debug'] ?
							$style : self::filter( 'minify-css', $style, self::$modules[$module]['style'] )
					);
				}
				// Messages
				$messages = isset( $blobs[$module] ) ? $blobs[$module] : '{}';
				// Output
				echo "mw.loader.implement( '{$module}', function() {\n{$script}\n}, '{$style}', {$messages} );\n";
			}
		}
		// Set headers -- when we support CSS only mode, this might change!
		header( 'Content-Type: text/javascript' );
		// Final processing
		if ( $parameters['debug'] ) {
			ob_end_flush();
		} else {
			echo self::filter( 'minify-js', ob_get_clean() );
		}
	}
}

// FIXME: Temp hack
require_once "$IP/resources/Resources.php";