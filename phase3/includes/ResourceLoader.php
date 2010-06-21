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
	
	private $mScripts = array();
	private $mStyles = array();
	private $mMessages = array();
	
	private $mUseJSMin = true;
	private $mUseCSSMin = true;
	private $mUseCSSJanus = true;
	
	
	/**
	 * Add a module to the output. This includes the module's
	 * JS itself, its style and its messages.
	 * @param $module string Module name
	 */
	public function addModule( $module ) {
		$this->mScripts[] = self::$modules[$module]['script'];
		if ( isset( $module['style'] ) ) {
			$this->mStyles[] = self::$modules[$module]['script'];
		}
		if ( isset( $module['messages'] ) ) {
			$this->mMessages = array_merge( $this->mMessages, self::$modules[$module]['messages'] );
		}
	}
	
	public function setUseJSMin( $use ) {
		$this->mUseJSMin = $use;
	}
	
	public function setUseCSSMin( $use ) {
		$this->mUseCSSMin = $use;
	}
	
	public function setUseCSSJanus( $use ) {
		$this->mUseCSSJanus = $use;
	}
		
	private function getStyleJS( $styles ) {
		$retval = '';
		foreach ( $styles as $style ) {
			// TODO: file_get_contents() errors?
			$css = file_get_contents( $style );
			if ( $this->mUseCSSJanus ) {
				$css = $this->cssJanus( $css );
			}
			if ( $this->mUseCSSMin ) {
				$css = $this->cssMin( $css );
			}
			$escCss = Xml::escapeJsString( $css );
			$retval .= "\$j( 'head' ).append( '<style>$escCSS</style>' );\n";
		}
		return $retval;
	}
	
	private function getMessagesJS( $messages ) {
		$msgs = array();
		foreach ( $messages as $message ) {
			$escKey = Xml::escapeJsString( $message );
			$escValue = Xml::escapeJsString( wfMsg( $message ) );
			$msgs[] = "'$escKey': '$escValue'";
		}
		return "mw.addMessages( {\n" . implode( ",\n", $msgs ) . "\n} );\n";
	}
	
	public function getOutput() {
		$this->mScripts = array_unique( $this->mScripts );
		$this->mStyles = array_unique( $this->mStyles );
		$this->mMessages = array_unique( $this->mMessages );
		$retval = '';
		
		foreach ( $this->mScripts as $script ) {
			// TODO: file_get_contents() errors?
			$retval .= file_get_contents( $script );
		}
		$retval .= $this->getStyleJS( $this->mStyles );
		$retval .= $this->getMessagesJS( $this->mMessages );
		
		if ( $this->mUseJSMin ) {
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
