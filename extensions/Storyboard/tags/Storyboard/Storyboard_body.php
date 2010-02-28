<?php

/**
 * File holding the rendering function for the Storyboard tag.
 *
 * @file Storyboard_body.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class TagStoryboard {
	
	public static function render( $input, $args, $parser, $frame ) {
		global $wgOut, $wgJsMimeType, $wgScriptPath, $egStoryboardScriptPath, $egStoryboardWidth, $egStoryboardHeight;
		
		$wgOut->addStyle($egStoryboardScriptPath . '/tags/Storyboard/storyboard.css');		
		$wgOut->includeJQuery();
		$wgOut->addScriptFile($egStoryboardScriptPath . '/tags/Storyboard/jquery.ajaxscroll.js');
		
		$widthGiven = array_key_exists('width', $args)
			&& (is_numeric($args['width'])
				|| (strlen($args['width']) > 1
					&& is_numeric(substr($args['width'], 0, strlen($args['width']) - 1))
					&& substr($args['width'], strlen($args['width']) == '%'
					)
				)
			);
		$width = $widthGiven ? $args['width'] : $egStoryboardWidth;
		
		$heightGiven = array_key_exists('height', $args)
			&& (is_numeric($args['height'])
				|| (strlen($args['height']) > 1
					&& is_numeric(substr($args['height'], 0, strlen($args['height']) - 1))
					&& substr($args['height'], strlen($args['height']) == '%'
					)
				)
			);
		$height = $heightGiven ? $args['height'] : $egStoryboardHeight;
		
		TagStoryboard::addPxWhenNeeded($width);
		TagStoryboard::addPxWhenNeeded($height);
		
		$output = <<<EOT
<div class="ajaxscroll" id="storyboard" style="height: $height; width: $width;">
<script type="$wgJsMimeType"> /*<![CDATA[*/
jQuery(function(){
	jQuery('#storyboard').ajaxScroll({
		updateBatch: updateStoryboard,
		batchSize: 5,
		batchNum: 1
	});
});
function updateStoryboard(obj){
	jQuery.getJSON('$wgScriptPath/api.php',
		{
			'action': 'query',
			'list': 'stories',
			'stcontinue': obj.attr( 'offset' ),
			'stlimit': '5',
			'format': 'json'
		},
		function( data ) {
			// TODO: use data to create stories html
		}
	);
}
/*]]>*/ </script>
EOT;

	return array($output, 'noparse' => 'true', 'isHTML' => 'true');
	}
	
	/**
	 * Adds 'px' to a width or height value when it's not there yet, and it's not a percentage. 
	 * @param string $value
	 */
	private static function addPxWhenNeeded(&$value){
		$hasPx = strrpos( $value, 'px' ) === strlen( $value ) - 2;
		$hasPercent = strrpos( $value, '%' ) === strlen( $value ) - 1;
    	if (!$hasPx && !$hasPercent) $value .= 'px';
	}
	
}



