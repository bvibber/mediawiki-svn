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
		global $wgOut, $wgJsMimeType, $egStoryboardScriptPath, $wgScriptPath;
		
		$wgOut->addStyle($egStoryboardScriptPath . '/tags/Storyboard/storyboard.css');		
		$wgOut->includeJQuery();
		$wgOut->addScriptFile($egStoryboardScriptPath . '/tags/Storyboard/jquery.ajaxscroll.js');

		$output = <<<EOT
<div class="ajaxscroll" id="storyboard" style="height: 400px; width: 80%;">
<script type="$wgJsMimeType"> /*<![CDATA[*/
jQuery(function(){ jQuery('#storyboard').ajaxScroll({ updateBatch: updateStoryboard, batchSize: 5, batchNum: 2 }); });
function updateStoryboard(obj){ obj.load('$wgScriptPath/api.php?action=query&list=stories&stcontinue=' + obj.attr('offset') + '&stlimit=5&format=json'); }
/*]]>*/ </script>
EOT;

	return array($output, 'noparse' => 'true', 'isHTML' => 'true');
	}
	
}



