<?php
/**
 * Simple editor for references. 
 */
class ReferenceEditor {
	
	/**
	 * This function hooks into InlineEditorMark and marks the references.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();
		
		$matches = array();
		preg_match_all( '/<ref[^\/]*?>.*?<\/ref>|<ref.*?\/>/is', $text, $matches, PREG_OFFSET_CAPTURE );
		
		foreach( $matches[0] as $match ) {
			$start = $match[1];
			$end   = $start + strlen( $match[0] );
			$inlineEditorText->addPiece( new InlineEditorPiece( $start, $end, 'referenceEditorElement', true ) );
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
			'referenceEditor', 
			 wfMsgExt( 'reference-editor-editmode-caption', 'parseinline' ), 
			 wfMsgExt( 'reference-editor-editmode-description','parseinline' )
		);
		
		$output->addExtensionStyle( "$wgExtensionAssetsPath/InlineEditor/ReferenceEditor/ReferenceEditor.css?0");
		$output->addScriptFile( "$wgExtensionAssetsPath/InlineEditor/ReferenceEditor/jquery.inlineEditor.editors.referenceEditor.js?0" );
		
		return true;
	}
}