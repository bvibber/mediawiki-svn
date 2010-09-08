<?php

/**
 * This interface defines what a sentence detector should be able to do, which is
 * adding portions of wikitext and adding the detected sentences as pieces to an
 * InlineEditorText object.
 */
interface ISentenceDetection {
	/**
	 * Add a bit of wikitext to the object. The wikitext has to be split in sentences while
	 * remembering the position of each sentence.
	 * @param $text String
	 * @param $position Integer
	 */
	public function addWikiText( $text, $position );

	/**
	 * All the sentences and their offsets have to be added as pieces to $inlineEditorText.
	 * @param $inlineEditorText InlineEditorText Object the pieces should be added to
	 * @param $class String Class name of the piece
	 * @param $inline Boolean whether the pieces are inline or not
	 */
	public function addPiecesToText( InlineEditorText &$inlineEditorText, $class, $inline );
}
