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
 */

// TODO: Class comment
// TODO: Add an interface to inherit from rather than having to subclass this class, or add an empty-returning superclass
class ResourceLoaderModule {
	private $scripts = array();
	private $styles = array();
	private $messages = array();
	private $dependencies = array();
	private $debugScripts = array();
	private $languageScripts = array();
	private $skinScripts = array();
	private $skinStyles = array();
	private $loaders = array();
	private $parameters = array();
	
	/* Public methods */
	
	/**
	 * Construct a new module from an options array.
	 *
	 * @param $options array Options array. If empty, an empty module will be constructed
	 * 
	 * $options format:
	 * 	array(
	 * 		// Required module options (mutually exclusive)
	 * 		'scripts' => 'dir/script.js' | array( 'dir/script1.js', 'dir/script2.js' ... ),
	 *		
	 * 		// Optional module options
	 * 		'languageScripts' => array(
	 * 			'[lang name]' => 'dir/lang.js' | '[lang name]' => array( 'dir/lang1.js', 'dir/lang2.js' ... )
	 * 			...
	 * 		),
	 * 		'skinScripts' => 'dir/skin.js' | array( 'dir/skin1.js', 'dir/skin2.js' ... ),
	 * 		'debugScripts' => 'dir/debug.js' | array( 'dir/debug1.js', 'dir/debug2.js' ... ),
	 * 
	 * 		// Non-raw module options
	 * 		'dependencies' => 'module' | array( 'module1', 'module2' ... )
	 * 		'loaderScripts' => 'dir/loader.js' | array( 'dir/loader1.js', 'dir/loader2.js' ... ),
	 * 		'styles' => 'dir/file.css' | array( 'dir/file1.css', 'dir/file2.css' ... ),
	 * 		'skinStyles' => array(
	 * 			'[skin name]' => 'dir/skin.css' | '[skin name]' => array( 'dir/skin1.css', 'dir/skin2.css' ... )
	 * 			...
	 * 		),
	 * 		'messages' => array( 'message1', 'message2' ... ),
	 * 	)
	 */
	public function __construct( $options = array() ) {
		foreach ( $options as $option => $value ) {
			switch ( $option ) {
				case 'scripts':
					$this->scripts = (array)$value;
					break;
				case 'styles':
					$this->styles = $value;
					break;
				case 'messages':
					$this->messages = $value;
					break;
				case 'dependencies':
					$this->dependencies = $value;
					break;
				case 'debugScripts':
					$this->debugScripts = (array)$value;
					break;
				case 'languageScripts':
					$this->languageScripts = (array)$value;
					break;
				case 'skinScripts':
					$this->skinScripts = (array)$value;
					break;
				case 'skinStyles':
					$this->skinStyles = (array)$value;
					break;
				case 'loaders':
					$this->loaders = (array)$value;
					break;
			}
		}
	}
	
	/**
	 * Add script files to this module. In order to be valid, a module
	 * must contain at least one script file.
	 * @param $scripts mixed Path to script file (string) or array of paths
	 */
	public function addScripts( $scripts ) {
		$this->scripts = array_merge( $this->scripts, (array)$scripts );
	}
	
	/**
	 * Add style (CSS) files to this module.
	 * @param $styles mixed Path to CSS file (string) or array of paths
	 */
	public function addStyles( $styles ) {
		$this->styles = array_merge( $this->styles, (array)$styles );
	}
	
	/**
	 * Add messages to this module.
	 * @param $messages mixed Message key (string) or array of message keys
	 */
	public function addMessages( $messages ) {
		$this->messages = array_merge( $this->messages, (array)$messages );
	}
	
	/**
	 * Add dependencies. Dependency information is taken into account when
	 * loading a module on the client side. When adding a module on the
	 * server side, dependency information is NOT taken into account and
	 * YOU are responsible for adding dependent modules as well. If you
	 * don't do this, the client side loader will send a second request
	 * back to the server to fetch the missing modules, which kind of
	 * defeats the point of using the resource loader in the first place.
	 *
	 * To add dependencies dynamically on the client side, use a custom
	 * loader (see addLoaders())
	 *
	 * @param $dependencies mixed Module name (string) or array of module names
	 */
	public function addDependencies( $dependencies ) {
		$this->dependencies = array_merge( $this->dependencies, (array)$dependencies );
	}
	
	/**
	 * Add debug scripts to the module. These scripts are only included
	 * in debug mode.
	 * @param $scripts mixed Path to script file (string) or array of paths
	 */
	public function addDebugScripts( $scripts ) {
		$this->debugScripts = array_merge( $this->debugScripts, (array)$scripts );
	}
	
	/**
	 * Add language-specific scripts. These scripts are only included for
	 * a given language.
	 * @param $lang string Language code
	 * @param $scripts mixed Path to script file (string) or array of paths
	 */
	public function addLanguageScripts( $lang, $scripts ) {
		$this->languageScripts = array_merge_recursive(
			$this->languageScripts,
			array( $lang => $scripts )
		);
	}

	/**
	 * Add skin-specific scripts. These scripts are only included for
	 * a given skin.
	 * @param $skin string Skin name, or 'default'
	 * @param $scripts mixed Path to script file (string) or array of paths
	 */
	public function addSkinScripts( $skin, $scripts ) {
		$this->skinScripts = array_merge_recursive(
			$this->skinScripts,
			array( $skin => $scripts )
		);
	}
	
	/**
	 * Add skin-specific CSS. These CSS files are only included for a
	 * given skin. If there are no skin-specific CSS files for a skin,
	 * the files defined for 'default' will be used, if any.
	 * @param $skin string Skin name, or 'default'
	 * @param $scripts mixed Path to CSS file (string) or array of paths
	 */
	public function addSkinStyles( $skin, $scripts ) {
		$this->skinStyles = array_merge_recursive(
			$this->skinStyles,
			array( $skin => $scripts )
		);
	}
	
	/**
	 * Add loader scripts. These scripts are loaded on every page and are
	 * responsible for registering this module using
	 * mediaWiki.loader.register(). If there are no loader scripts defined,
	 * the resource loader will register the module itself.
	 *
	 * Loader scripts are used to determine a module's dependencies
	 * dynamically on the client side (e.g. based on browser type/version).
	 * Note that loader scripts are included on every page, so they should
	 * be lightweight and use mediaWiki.loader.register()'s callback
	 * feature to defer dependency calculation.
	 * @param $scripts mixed Path to script file (string) or array of paths
	 */
	public function addLoaders( $scripts ) {
		$this->loaders = array_merge( $this->loaders, (array)$scripts );
	}
	
	/**
	 * Get the primary JS for this module. This is pulled from the
	 * script files added through addScripts()
	 * @return string JS
	 */
	public function getScript() {
		return self::concatScripts( $this->scripts );
	}
	
	/**
	 * Get the CSS for this module. This is pulled from the CSS files
	 * added through addStyles()
	 * @return string JS
	 */
	public function getStyle() {
		return self::concatStyles( $this->styles );
	}
	
	/**
	 * Get the messages added to this module with addMessages().
	 *
	 * To get a JSON blob with messages, use MessageBlobStore::get()
	 * @return array of message keys. Keys may occur more than once
	 */
	public function getMessages() {
		return $this->messages;
	}
	
	/**
	 * Get the dependencies added to this module with addDependencies()
	 * @return array of module names (strings)
	 */
	public function getDependencies() {
		return $this->dependencies;
	}
	
	/**
	 * Get the debug JS for this module. This is pulled from the script
	 * files added through addDebugScripts()
	 * @return string JS
	 */
	public function getDebugScript() {
		return self::concatScripts( $this->debugScripts );
	}
	
	/**
	 * Get the language-specific JS for a given language. This is pulled
	 * from the language-specific script files added through addLanguageScripts()
	 * @return string JS
	 */
	public function getLanguageScript( $lang ) {
		if ( !isset( $this->languageScripts[$lang] ) ) {
			return '';
		}
		return self::concatScripts( $this->languageScripts[$lang] );
	}
	
	/**
	 * Get the skin-specific JS for a given skin. This is pulled from the
	 * skin-specific JS files added through addSkinScripts()
	 * @return string JS
	 */
	public function getSkinScript( $skin ) {
		$scripts = array();
		if ( isset( $this->skinScripts[$skin] ) && count( $this->skinScripts[$skin] ) ) {
			$scripts = $this->skinScripts[$skin];
		} else if ( isset( $this->skinScripts['default'] ) ) {
			$scripts = $this->skinScripts['default'];
		}
		return self::concatScripts( $scripts );
	}
	
	/**
	 * Get the skin-specific CSS for a given skin. This is pulled from the
	 * skin-specific CSS files added through addSkinStyles()
	 * @return string CSS
	 */
	public function getSkinStyle( $skin ) {
		$styles = array();
		if ( isset( $this->skinStyles[$skin] ) && count( $this->skinStyles[$skin] ) ) {
			$styles = $this->skinStyles[$skin];
		} else if ( isset( $this->skinStyles['default'] ) ) {
			$styles = $this->skinStyles['default'];
		}
		return self::concatStyles( $styles );
	}
	
	/**
	 * Get the custom loader JS, if set. This is pulled from the script
	 * files added through addLoaders()
	 * @return mixed Loader JS (string) or false if no custom loader set
	 */
	public function getLoaderScript() {
		if ( count( $this->loaders ) == 0 ) {
			return false;
		}
		return self::concatScripts( $this->loaders );
	}
	
	/**
	 * Get the contents of a set of files and concatenate them, with
	 * newlines in between. Each file is used only once.
	 * @param $files array Array of file names
	 * @return string Concatenated contents of $files
	 */
	protected static function concatScripts( $files ) {
		return implode( "\n", array_map( 'file_get_contents', array_unique( (array) $files ) ) );
	}
	
	protected static function concatStyles( $files ) {
		return implode( "\n", array_map( array( 'ResourceLoaderModule', 'remapStyle' ), array_unique( (array) $files ) ) );
	}
	
	protected static function remapStyle( $file ) {
		return CSSMin::remap( file_get_contents( $file ), dirname( $file ) );
	}
}

class ResourceLoaderSiteJSModule extends ResourceLoaderModule {
	public function getSkinScript( $skin ) {
		return Skin::newFromKey( $skin )->generateUserJs();
	}
	
	// Dummy overrides to return emptyness
	public function getScript() { return ''; }
	public function getStyle() { return ''; }
	public function getMessages() { return array(); }
	public function getDependencies() { return array(); }
	public function getDebugScript() { return ''; }
	public function getLanguageScript( $lang ) { return ''; }
	public function getSkinStyle( $skin ) { return ''; }
	public function getLoaderScript() { return false; }
}
