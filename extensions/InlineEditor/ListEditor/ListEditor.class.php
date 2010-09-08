<?php

/**
 * Simple editor for lists.
 */
class ListEditor {
	/**
	 * This function hooks into InlineEditorMark and marks the media.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();

		$matches = array();
		preg_match_all( '/(\n|^)(([*#;:].*(\n|$))+)/', $text, $matches, PREG_OFFSET_CAPTURE );

		foreach ( $matches[2] as $match ) {
			$start = $match[1];
			$end   = $start + strlen( $match[0] );

			// do not include the trailing newline
			if ( substr( $match[0], -1 ) == "\n" ) $end--;

			$inlineEditorText->addPiece( new InlineEditorPiece( $start, $end, 'listEditorElement', false ) );
		}

		return true;
	}

	/**
	 * Hooks into InlineEditorDefineEditors. Adds the option to to the list and adds CSS and JS files
	 * @param $editor InlineEditor
	 * @param $output OutputPage
	 */
	public static function defineEditors( &$editor, &$output ) {
		global $wgExtensionAssetsPath;

		$editor->addEditMode(
			'listEditor',
			 wfMsgExt( 'list-editor-editmode-caption', 'parseinline' ),
			 wfMsgExt( 'list-editor-editmode-description', 'parseinline' )
		);

		$output->addExtensionStyle( "$wgExtensionAssetsPath/InlineEditor/ListEditor/ListEditor.css?0" );
		$output->addScriptFile( "$wgExtensionAssetsPath/InlineEditor/ListEditor/jquery.inlineEditor.editors.listEditor.js?0" );

		return true;
	}
}
