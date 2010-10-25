<?php

/**
 * This interface defines what a sentence detector should be able to do, which is
 * adding portions of wikitext and adding the detected sentences as markings to an
 * InlineEditorText object.
 */
interface ISentenceDetection {
	/**
	 * Add a bit of wikitext to the object. The wikitext has to be split in sentences while
	 * remembering the position of each sentence.
	 * @param $text string The wikitext to add
	 * @param $position int The position in the original text of the wikitext
	 */
	public function addWikiText( $text, $position );

	/**
	 * All the sentences and their offsets have to be added as markings to $inlineEditorText.
	 * @param $inlineEditorText InlineEditorText Object the markings should be added to
	 * @param $class string Class name of the markings
	 * @param $inline bool Whether the markings are inline or not
	 */
	public function addMarkingsToText( InlineEditorText &$inlineEditorText, $class, $inline );
}
