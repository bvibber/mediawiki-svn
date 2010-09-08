<?php

/**
 * Simple editor for templates.
 */
class TemplateEditor {
	/**
	 * This function hooks into InlineEditorMark and marks the media.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();

		$matches = array();
		preg_match_all( '/^(\{\{.*?\}\})/ms', $text, $matches, PREG_OFFSET_CAPTURE );

		foreach ( $matches[0] as $match ) {
			$start = $match[1];
			$end   = $start + strlen( $match[0] );
			$inlineEditorText->addPiece( new InlineEditorPiece( $start, $end, 'templateEditorElement templateEditorElementNotEditing', false ) );
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
			'templateEditor',
			 wfMsgExt( 'template-editor-editmode-caption', 'parseinline' ),
			 wfMsgExt( 'template-editor-editmode-description', 'parseinline' )
		);

		$output->addExtensionStyle( "$wgExtensionAssetsPath/InlineEditor/TemplateEditor/TemplateEditor.css?0" );
		$output->addScriptFile( "$wgExtensionAssetsPath/InlineEditor/TemplateEditor/jquery.inlineEditor.editors.templateEditor.js?0" );

		return true;
	}
}
