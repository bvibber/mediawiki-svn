<?php

/**
 * This 'editor' just shows some description with a link to the full/traditional editor.
 */
class FullEditor {
	public static function defineEditors( &$editor, &$output ) {
		$editor->addEditMode(
			'FullEditor',
			 wfMsgExt( 'fulleditor-editmode-caption', 'parseinline' ),
			 wfMsgExt( 'fulleditor-editmode-description', 'parseinline' )
			 . '<br /><a class="fulleditor" href="' . $editor->getArticle()->getTitle()->getLocalURL( 'action=edit&fulleditor=1' ) . '">'
			 . wfMsgExt( 'fulleditor-editmode-description-link', 'parseinline' )
			 . '</a>'
		);
		return true;
	}
}
