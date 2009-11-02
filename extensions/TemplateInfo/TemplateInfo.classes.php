<?php
/**
 * Classes for TemplateInfo extension
 *
 * @file
 * @ingroup Extensions
 */

// TemplateInfo class
class TemplateInfo {

	/**
	 * Display the contents of <templateinfo> in a nicely-formatted way.
	 */
	public function render( $input ) {
		$text = "<p>" . wfMsg('templateinfo-header') . "</p>\n";
		$text .= htmlentities($input);
		return $text;
	}
}
