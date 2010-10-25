<?php

/**
 * Simple editor for sections.
 */
class SectionEditor {
	/**
	 * This function hooks into InlineEditorMark and marks the sections.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();

		$matches = array();
		preg_match_all( '/==+.+==+\s*\n/', $text, $matches, PREG_OFFSET_CAPTURE );
		
		$matches[0][] = array( '', strlen($text)+1 );
		
		$prevPos = 0;
		foreach ( $matches[0] as $match ) {
			$start   = $prevPos;
			$end     = $match[1]-1;
			$prevPos = $match[1];

			$inlineEditorText->addMarking( new InlineEditorMarking( $start, $end, 'sectionEditorElement', false ) );
		}

		return true;
	}

	/**
	 * Hooks into InlineEditorDefineEditors. Adds the option to to the list and adds CSS and JS files
	 * @param $editor InlineEditor
	 * @param $output OutputPage
	 */
	public static function defineEditors( &$editor, &$output ) {
		global $wgExtensionAssetsPath, $wgInlineEditorSectionEditorVisible;

		if( $wgInlineEditorSectionEditorVisible ) {
			$editor->addEditMode(
				'sectionEditor',
				 wfMsgExt( 'section-editor-editmode-caption', 'parseinline' ),
				 wfMsgExt( 'section-editor-editmode-description', 'parseinline' )
			);
	
			$output->addExtensionStyle( "$wgExtensionAssetsPath/InlineEditor/SectionEditor/SectionEditor.css?0" );
			$output->addScriptFile( "$wgExtensionAssetsPath/InlineEditor/SectionEditor/jquery.inlineEditor.editors.sectionEditor.js?0" );
		}
		
		return true;
	}
}
