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
	);
	
	private $scripts = array();
	private $styles = array();
	private $loadedModules = array();
	
	private $useJSMin = true;
	private $useCSSMin = true;
	private $useCSSJanus = true;
	
	
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
		return "mw.addMessages( {\n" .
			implode( ",\n", array_map( array( 'MessageBlobStore', 'get' ), $modules ) ) .
			"\n} );";
	}
	
	public function getOutput() {
		$this->scripts = array_unique( $this->scripts );
		$this->styles = array_unique( $this->styles );
		$this->loadedModules = array_unique( $this->loadedModules );
		$retval = '';
		
		foreach ( $this->scripts as $script ) {
			// TODO: file_get_contents() errors?
			$retval .= file_get_contents( $script );
		}
		$retval .= $this->getStyleJS( $this->styles );
		$retval .= $this->getMessagesJS( $this->loadedModules );
		
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
	 * Get the message blob for a module
	 * @param $module string Module name
	 * @return string An incomplete JSON object (i.e. without the {} ) with messages keys and their values.
	 */
	public static function get( $module ) {
		// TODO: Implement
		return '';
	}
}