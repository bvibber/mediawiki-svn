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
		
		$width = StoryboardUtils::getDimension( $args, 'width', $egStoryboardWidth );
		$height = StoryboardUtils::getDimension( $args, 'height', $egStoryboardHeight );

		$output = Html::element( 'div', array(
				'class' => 'storyboard',
				'style' => "height: $height; width: $width;"
			)
		);
		return array( $output, 'noparse' => 'true', 'isHTML' => 'true' );
	}
	
}



