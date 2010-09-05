<?php

/**
 * Denotes the start and end of previously edited pieces in the wikitext.
 */
class InlineEditorPreviousMarking {
	private $start;     ///< start position of the piece in the wikitext
	private $end;       ///< end position of the piece in the wikitext
	private $edited;    ///< boolean whether or not (part of) the piece has been edited before
	private $lastEdit;  ///< boolean whether or not (part of) the piece was in the last edit
	
	// getters and setters for the private variables
	public function getStart()     { return $this->start; }
	public function getEnd()       { return $this->end; }
	public function getEdited()    { return $this->edited; }
	public function getLastEdit()  { return $this->lastEdit; }
	public function setEdited( $val )   { $this->edited   = $val; }
	public function setLastEdit( $val ) { $this->lastEdit = $val; }
	
	/**
	 * @param $start Integer Start of the piece, offset in number of characters from the begin of the wikitext
	 * @param $end Integer End of the piece, offset in number of characters from the begin of the wikitext
	 */
	function __construct($start, $end) {
		$this->start   = $start;
		$this->end     = $end;
	}
	
	/**
	 * Checks if an InlineEditorPiece overlaps *or touches* with this marking.
	 * @param $piece InlineEditorPiece
	 * @return Boolean
	 */
	public function inMarking( InlineEditorPiece $piece ) {
		$start = $piece->getStart();
		$end   = $piece->getEnd();
		return ($start >= $this->start && $start <= $this->end) || ($end >= $this->start && $end <= $this->end); 
	}
	
	/**
	 * Simple check to prevent invalid values.
	 * @return boolean
	 */
	public function isValid() {
		return $this->end > $this->start;
	}
}