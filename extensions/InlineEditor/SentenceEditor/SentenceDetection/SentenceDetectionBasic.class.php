<?php

/**
 * Basic implementation of sentence splitting. Not recommended for actual use, but does the job
 * for a simple demo.
 */
class SentenceDetectionBasic implements ISentenceDetection {
	private $wikiTexts;

	function __construct() {
		$this->wikiTexts = array();
	}

	public function addWikiText( $text, $offset ) {
		$this->wikiTexts[] = array( 'text' => $text, 'offset' => $offset );
	}

	public function addMarkingsToText( InlineEditorText &$inlineEditorText, $class, $inline ) {
		foreach ( $this->wikiTexts as $wikiText ) {
			$sentences =  preg_split( "/(?<!\..|\...|\....)([\?\!\.]+)\s(?!.\.|..\.|...\.)/u", $wikiText['text'], -1,
				PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_DELIM_CAPTURE );

			foreach ( $sentences as $index => $sentence ) {
				if ( $index % 2 == 0 ) {
					if ( isset( $sentences[$index + 1] ) ) {
						$sentence[0] .= $sentences[$index + 1][0];
					}
					$start = $wikiText['offset'] + $sentence[1];
					$end   = $start + strlen( $sentence[0] );
					$inlineEditorText->addMarking( new InlineEditorMarking( $start, $end, $class, $inline ) );
				}
			}
		}
	}
}
