<?php

/**
 * Simple editor for media (images, video, sound).
 */
class MediaEditor {
	/**
	 * This function hooks into InlineEditorMark and marks the media.
	 * @param $inlineEditorText InlineEditorText
	 */
	public static function mark( &$inlineEditorText ) {
		// get the original wikitext
		$text = $inlineEditorText->getWikiOriginal();

		$matches = array();
		preg_match_all( '/^\s*(\[\[(.*:.*)\]\])\s*$/m', $text, $matches, PREG_OFFSET_CAPTURE );

		foreach ( $matches[1] as $id => $match ) {
			$link = $matches[2][$id][0];
			$firstPipe = strpos( $link, '|' );
			if ( $firstPipe !== false ) {
				$url = substr( $link, 0, $firstPipe );
			}
			else {
				$url = $link;
			}

			$title = Title::newFromText( $url );
			$namespace = $title->getNamespace();

			if ( $namespace == NS_FILE ) {
				$start = $match[1];
				$end   = $start + strlen( $match[0] );
				$inlineEditorText->addPiece( new InlineEditorPiece( $start, $end, 'mediaEditorElement', false ) );
			}
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
			'mediaEditor',
			 wfMsgExt( 'media-editor-editmode-caption', 'parseinline' ),
			 wfMsgExt( 'media-editor-editmode-description', 'parseinline' )
		);

		$output->addExtensionStyle( "$wgExtensionAssetsPath/InlineEditor/MediaEditor/MediaEditor.css?0" );
		$output->addScriptFile( "$wgExtensionAssetsPath/InlineEditor/MediaEditor/jquery.inlineEditor.editors.mediaEditor.js?0" );

		return true;
	}
}
