<?php

/**
 * Stores wikitext, pieces and previous markings, and generates HTML and JSON from them.
 * Can import wikitext and previous markings from JSON and from an original article.
 *
 * Computation path is as follows:
 *
 * First time editing the page:
 * wikitext from article => adding of new pieces => generating HTML (using parser) and JSON array
 *
 * Previewing the page:
 * JSON array with pieces + last edit diff => adding of new pieces
 * => computing overlap of pieces with previous markings => generating HTML (using parser) and JSON array
 * => generating combined JSON array
 *
 * Saving the page:
 * JSON array with pieces => new wikitext
 *
 */
class InlineEditorText {
	private $wikiOriginal;
	private $parserOutputMarked;
	private $wikiJsonArray;
	private $wikiJsonText;
	private $combinedJson;
	private $article;
	private $pieces;
	private $previousMarkings;

	/**
	 * @param $article Article The article to work with.
	 */
	function __construct( $article ) {
		$this->article = $article;
		$this->pieces = array();
		$this->previousMarkings = array();
	}

	/**
	 * Load the original wikitext from $wikiText.
	 * @param $wikiText String
	 */
	public function loadFromWikiText( $wikiText ) {
		$this->reset();
		$this->wikiOriginal = $wikiText;
	}

	/**
	 * Get the original wikitext.
	 * @return String Original wikitext
	 */
	public function getWikiOriginal() {
		return $this->wikiOriginal;
	}

	/**
	 * Get the HTML with <span> and <div> markings.
	 * @return String HTML
	 */
	public function getParserOutputMarked() {
		if ( !isset( $this->parserOutputMarked ) ) $this->process();
		return $this->parserOutputMarked;
	}

	/**
	 * Get JSON representation of the wikitext and markings.
	 * @return String JSON
	 */
	public function getWikiJson() {
		if ( !isset( $this->wikiJsonText ) ) $this->toJsonText();
		return $this->wikiJsonText;
	}

	/**
	 * Get a combined JSON representation of the wikitext with markings, and marked HTML.
	 * @return String JSON with HTML in it
	 */
	public function getCombinedJson() {
		if ( !isset( $this->combinedJson ) ) $this->toCombinedJson();
		return $this->combinedJson;
	}

	/**
	 * Add a marking piece to the list of pieces. To be called by the different edit modes.
	 * @param $piece InlineEditorPiece
	 */
	public function addPiece( InlineEditorPiece $piece ) {
		if ( isset( $this->pieces[$piece->getStart()] ) ) return;
		if ( !$piece->isValid() ) return;

		$this->pieces[$piece->getStart()] = $piece;
	}

	/**
	 * Reset the object.
	 */
	public function reset() {
		unset( $this->wikiOriginal );
		unset( $this->parserOutputMarked );
		unset( $this->wikiJsonArray );
		unset( $this->wikiJsonText );
		unset( $this->combinedJson );
		$this->pieces = array();
		$this->previousMarkings = array();
	}

	/**
	 * Load original wikitext and previous markings from JSON
	 * @param $json String JSON string from the client side editor
	 */
	public function loadFromJson( $json ) {
		// first of all, reset the object
		$this->reset();

		// decode the JSON to an associative array
		$input = FormatJson::decode( $json, true );

		// if the 'lastEdit' attribute is set, this contains the edit the user made
		// the original text is kept intact, but we don't need it, so just overwrite it with the new text
		if ( isset( $input['lastEdit'] ) ) {
			$input['originalWiki']['locations'][$input['lastEdit']['id']]['text'] = $input['lastEdit']['text'];
		}

		// convert the 'locations' array (by id) to the $pieces array (by position)
		$pieces = array();
		foreach ( $input['originalWiki']['locations'] as $id => $piece ) {
			$piece['id'] = $id;
			$pieces[$piece['pos']] = $piece;
		}

		// sort the $pieces array by position
		ksort( $pieces );

		// init loop variables
		$text = '';   // reconstructed wikitext
		$lastPos = 0; // position in the text of the previous piece
		$offset = 0;  // total amount of wikitext *inside* pieces so far

		// iterate on all pieces
		foreach ( $pieces as $pos => $piece ) {
			// calculate the length of the piece
			$length = strlen( $piece['text'] );

			// add the wikitext between the previous piece and this piece
			$text .= substr( $input['originalWiki']['text'], $lastPos, $pos - $lastPos );

			// add the wikitext of the piece itself
			$text .= $piece['text'];

			// if this piece was last edited or marked before, add it to previousMarkings
			if ( ( isset( $input['lastEdit'] ) && $input['lastEdit']['id'] == $piece['id'] )
				|| $piece['edited'] )
			{
				// because these positions are within the text *with* the pieces included,
				// the position has to be offsetted by exactly the total length of pieces so far
				$previousStart = $pos + $offset;
				$previousMarking = new InlineEditorPreviousMarking( $previousStart, $previousStart + $length );
				$previousMarking->setEdited( true );

				if ( isset( $input['lastEdit'] ) && $input['lastEdit']['id'] == $piece['id']  ) {
					$previousMarking->setLastEdit( true );
				}

				// last sanity check before adding the marking
				if ( $previousMarking->isValid() ) {
					$this->previousMarkings[$previousStart] = $previousMarking;
				}
			}

			// set variables for next round
			$lastPos = $pos;
			$offset += $length;
		}

		// add the text after the last piece
		$text .= substr( $input['originalWiki']['text'], $lastPos );

		// store it as the original wikitext
		$this->wikiOriginal = $text;
	}

	/**
	 * Encodes the wikiJsonArray to a JSON string and stores it.
	 */
	private function toJsonText() {
		if ( !isset( $this->wikiJsonArray ) ) $this->process();
		$this->wikiJsonText = FormatJson::encode( $this->wikiJsonArray );
	}

	/**
	 * Encodes both the wikiJsonArray and the parserOutputMarked to a JSON string and stores it.
	 */
	private function toCombinedJson() {
		if ( !isset( $this->wikiJsonArray ) || !isset( $this->parserOutputMarked ) ) $this->process();

		$combined = array( 'html' => $this->parserOutputMarked->getText(), 'wiki' => $this->wikiJsonArray );
		$this->combinedJson = FormatJson::encode( $combined );
	}

	/**
	 * Calls the InlineEditorMark hook to have pieces added, and calculates the JSON and marked HTML.
	 */
	private function process() {
		global $wgParser;

		// make sure the pieces are added by the edit modes
		wfRunHooks( 'InlineEditorMark', array( &$this ) );

		// init the basic variables
		$last          = 0;   // end position of the last piece in the original wikitext
		$offset        = 0;   // total amount of wikitext *inside* pieces so far
		$wikiMarked    = '';  // marked wikitext to run through the parser
		$jsonText      = '';  // wikitext in JSON, which has the pieces *removed*
		$jsonLocations = array(); // pieces with positions where to insert them in $jsonText

		// sort the pieces by start position
		ksort( $this->pieces );

		// make sure the 'edited' and 'lastEdit' properties on pieces are correctly set
		$this->applyPreviousMarkings();

		// iterate on all pieces
		foreach ( $this->pieces as $start => $piece ) {
			// do not allow overlap of pieces, so only take those with the lowest
			// starting position
			if ( $start < $last ) {
				unset( $this->pieces[$start] );
			}
			else {
				// get the end and calculate the length of the current piece
				$end = $piece->getEnd();
				$length = $end -$start;

				// get the text between last piece and this piece
				$prePieceText = substr( $this->wikiOriginal, $last, $start -$last );

				// get the text of the piece itself
				$pieceText    = substr( $this->wikiOriginal, $start, $length );

				// add the text between last piece and this piece, followed by
				// the text of the piece surrounded by markings
				$wikiMarked  .= $prePieceText
					. $piece->renderStartMarking()
					. $pieceText
					. $piece->renderEndMarking();

				// only add the text between pieces to the JSON text
				$jsonText    .= $prePieceText;

				// add the piece and position in $jsonText to the list of locations
				// note that because we're taking out exactly the length of text within pieces,
				// we can subtract this number from the start position of the piece, to obtain
				// the position within $jsonText
				$jsonLocations[$piece->getId()] = array(
					'text'   => $pieceText,
					'pos'    => $start - $offset,
					'edited' => ( $piece->getEdited() ? '1' : '0' )
				);

				// set variables for the next round
				$offset += $length;
				$last = $end;
			}
		}

		// get the text after the last piece and add it to both variables
		$postPieceText = substr( $this->wikiOriginal, $last );
		$wikiMarked   .= $postPieceText;
		$jsonText     .= $postPieceText;

		// store the JSON text and locations
		$this->wikiJsonArray = array( 'text' => $jsonText, 'locations' => $jsonLocations );

		// get the same parser options as usual, but remove [edit] links
		$parserOptions = clone $this->article->getParserOptions();
		$parserOptions->setEditSection( false );

		// run $wikiMarked through the parser and store the result
		$this->parserOutputMarked = $wgParser->parse( $wikiMarked, $this->article->getTitle(),
			$parserOptions, true, true, $this->article->getRevIdFetched() );
	}

	/**
	 * Checks if pieces have overlap with previous markings and marks them again with
	 * 'edited' or 'lastEdit'.
	 *
	 * A piece can have overlap with multiple previous markings, but not with many. To
	 * avoid checking all pieces against all markings, a smarter algorithm is employed.
	 *
	 * When both the pieces are sorted (*precondition*) and the previous markings are
	 * sorted (which is done in the function), both arrays can be walked. For the array of
	 * previous markings we define a $firstId and $lastId. The end of the first previous marking
	 * has to be at least on or past the start of the current piece. The start of the last
	 * previous marking has to be at least past the end of the current piece. This way we can
	 * increment $firstId and $lastId until this is true, and we have a smaller window to
	 * check.
	 *
	 * @return unknown_type
	 */
	private function applyPreviousMarkings() {
		// if there are no previous markings there is no point in checking
		if ( empty( $this->previousMarkings ) ) return;

		// start both at the beginning
		$firstId = 0;
		$lastId  = 0;

		// sort based on start position
		ksort( $this->previousMarkings );

		// use an array with as keys 0, 1, 2, etc.
		$previous = array_values( $this->previousMarkings );

		// interate over all pieces
		foreach ( $this->pieces as $start => $piece ) {
			$end = $piece->getEnd();

			// move the pointer of the first previous marking so it overlaps with the piece
			while ( isset( $previous[$firstId] ) && $previous[$firstId]->getEnd() < $start ) {
				$firstId++;
			}

			// if the pointer to the first previous marking has gone too far, there is nothing
			// to check for anymore: we're done
			if ( !isset( $previous[$firstId] ) ) return;

			// move the pointer of the last previous marking so it just doesn't overlap
			while ( isset( $previous[$lastId] ) && $previous[$lastId]->getStart() <= $end ) {
				$lastId++;
			}

			// check the (small) window of previous markings for overlap
			for ( $i = $firstId; $i < $lastId; $i++ ) {
				// this should always be true, but check anyway!
				if ( $previous[$i]->inMarking( $piece ) ) {
					if ( $previous[$i]->getEdited() )   $piece->setEdited( true );
					if ( $previous[$i]->getLastEdit() ) $piece->setLastEdit( true );
				}
			}
		}
	}
}
