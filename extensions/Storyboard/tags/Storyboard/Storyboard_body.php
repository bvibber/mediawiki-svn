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

	/**
	 * Renders the storyboard tag.
	 * 
	 * @param $input
	 * @param array $args
	 * @param Parser $parser
	 * @param $frame
	 * 
	 * @return array
	 */
	public static function render( $input, array $args, Parser $parser, $frame ) {
		global $wgJsMimeType, $wgScriptPath, $wgStylePath, $wgStyleVersion, $wgContLanguageCode;
		global $egStoryboardScriptPath, $egStoryboardWidth, $egStoryboardHeight;
		
		// TODO: Combine+minfiy JS files, add switch to use combined+minified version
		$parser->getOutput()->addHeadItem(
			<<<EOT
			<link rel="stylesheet" href="$egStoryboardScriptPath/storyboard.css?$wgStyleVersion" />
			<script type="$wgJsMimeType" src="$wgStylePath/common/jquery.min.js?$wgStyleVersion"></script>
			<script type="$wgJsMimeType" src="$egStoryboardScriptPath/jquery/jquery.ajaxscroll.js?$wgStyleVersion"></script>
			<script type="$wgJsMimeType" src="$egStoryboardScriptPath/tags/Storyboard/storyboard.js?$wgStyleVersion"></script>
EOT
		);
		
		$width = StoryboardUtils::getDimension( $args, 'width', $egStoryboardWidth );
		$height = StoryboardUtils::getDimension( $args, 'height', $egStoryboardHeight );

		$languages = Language::getLanguageNames();
		
		if ( array_key_exists( 'language', $args ) && array_key_exists( $args['language'], $languages )  ) {
			$language = $args['language'];
		} else {
			$language = $wgContLanguageCode;
		}

		$parser->getOutput()->addHeadItem(
			Html::inlineScript( "var storyboardLanguage = '$language';" )
		);
		
		$output = Html::element( 'div', array(
				'class' => 'storyboard',
				'style' => "height: $height; width: $width;"
			)
		);
		
		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
}



