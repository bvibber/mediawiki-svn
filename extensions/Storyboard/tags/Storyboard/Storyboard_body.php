<?php

/**
 * File holding the rendering function for the Storyboard tag.
 *
 * @file Storyboard_body.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 * @author Roan Kattouw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class TagStoryboard {

	public static function render( $input, $args, $parser, $frame ) {
		global $wgOut, $wgJsMimeType, $wgScriptPath, $egStoryboardScriptPath, $egStoryboardWidth, $egStoryboardHeight;
		
		$wgOut->addStyle( $egStoryboardScriptPath . '/tags/Storyboard/storyboard.css' );
		$wgOut->includeJQuery();
		// TODO: Combine+minfiy JS files, add switch to use combined+minified version
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/tags/Storyboard/jquery.ajaxscroll.js' );
		$wgOut->addScriptFile( $egStoryboardScriptPath . '/tags/Storyboard/storyboard.js' );
		
		$width = self::getDimension( $args, 'width', $egStoryboardWidth );
		$height = self::getDimension( $args, 'height', $egStoryboardHeight );

		$output = Html::element( 'div', array(
				'class' => 'storyboard',
				'style' => "height: $height; width: $width;"
			)
		);
		return array( $output, 'noparse' => 'true', 'isHTML' => 'true' );
	}
	
	/**
	 * Get the width or height from an arguments array, or use the default value if not specified or not valid
	 * @param $arr Array of arguments
	 * @param $name Key in $array
	 * @param $default Default value to use if $arr[$name] is not set or not valid
	 */
	private static function getDimension( $arr, $name, $default ) {
		$value = $default;
		if ( isset( $arr[$name] ) && preg_match( '/\d+(\.\d+)?%?/', $arr[$name] ) ) {
			$value = $arr[$name];
		}
		if ( !preg_match( '/(px|ex|em|%)$/', $value ) ) {
			$value .= 'px';
		}
		return $value;
	}
	
}



