<?php

/**
 * Denotes the start and end of an editiable piece in the wikitext, along with some properties.
 */
class InlineEditorPiece {
	private static $lastId = 0; /// < counter which is used to generate unique ids

	private $start;     /// < start position of the piece in the wikitext
	private $end;       /// < end position of the piece in the wikitext
	private $class;     /// < class(es) attached to the piece, usually identifies the edit mode
	private $inline;    /// < whether or not the piece is inline (span) or not (div)
	private $id;        /// < unique id of this piece
	private $edited;    /// < boolean whether or not (part of) the piece has been edited before
	private $lastEdit;  /// < boolean whether or not (part of) the piece was in the last edit

	// getter functions for most private variables
	public function getStart()     { return $this->start; }
	public function getEnd()       { return $this->end; }
	public function getClass()     { return $this->class; }
	public function getId()        { return $this->id; }
	public function getEdited()    { return $this->edited; }
	public function getLastEdit()  { return $this->lastEdit; }

	// setter functions for edited and lastEdit, as these are not set by the different editors,
	// but by InlineEditorText
	public function setEdited( $val )   { $this->edited   = $val; }
	public function setLastEdit( $val ) { $this->lastEdit = $val; }

	/**
	 * @param $start Integer Start of the piece, offset in number of characters from the begin of the wikitext
	 * @param $end Integer End of the piece, offset in number of characters from the begin of the wikitext
	 * @param $class String Class(es) the piece should be labeled with
	 * @param $inline Boolean Whether the piece is inline (span) or not (div), a div also adds newlines
	 */
	function __construct( $start, $end, $class, $inline ) {
		$this->start    = $start;
		$this->end      = $end;
		$this->class    = $class;
		$this->inline   = $inline;
		$this->id       = self::uniqueId();
		$this->edited   = false;
		$this->lastEdit = false;
	}

	/**
	 * Render the open tag, depending on $this->inline this is a <span> or a <div>.
	 * @return String HTML
	 */
	public function renderStartMarking() {
		$attribs = array( 'class' => $this->getFullClass(), 'id' => $this->id );
		return HTML::openElement( $this->getElement(), $attribs ) . $this->getNewline();
	}

	/**
	 * Render the close tag.
	 * @return String HTML
	 */
	public function renderEndMarking() {
		return $this->getNewline() . '</' . $this->getElement() . '>';
	}

	/**
	 * Simple check to prevent invalid values.
	 * @return boolean
	 */
	public function isValid() {
		return $this->end > $this->start;
	}

	/**
	 * Get the class that should be rendered, this may include 'edited' and 'lastEdit' for highlighting.
	 * @return String All the classes
	 */
	private function getFullClass() {
		return $this->class . ( $this->edited ? ' edited' : '' ) . ( $this->lastEdit ? ' lastEdit' : '' );
	}

	/**
	 * Get the element name based on $this->inline.
	 * @return String Element name
	 */
	private function getElement() {
		return $this->inline ? 'span' : 'div';
	}

	/**
	 * Get a newline or not based on $this->inline.
	 * @return String Empty string or single newline character
	 */
	private function getNewline() {
		return $this->inline ? '' : "\n";
	}

	/**
	 * Get a unique id by using self::$lastId and incrementing it.
	 * @return String
	 */
	private static function uniqueId() {
		return 'inline-editor-' . self::$lastId++;
	}
}
