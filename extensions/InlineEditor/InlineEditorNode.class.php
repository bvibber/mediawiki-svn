<?php
/**
 * This class wraps an InlineEditorMarking to be a part of a tree spanning the
 * wikitext. Inherits from InlineEditorRoot, where most of the logic is.
 */
class InlineEditorNode extends InlineEditorRoot {
	protected $marking; /// < marking this nodes wraps
	protected $parent;  /// < parent node, either an InlineEditorNode or InlineEditorRoot
	
	/**
	 * @param $wiki String Reference to the original wikitext
	 * @param $marking InlineEditorMarking Marking to wrap in the tree
	 */
	public function __construct( &$wiki, InlineEditorMarking $marking ) {
		parent::__construct( &$wiki );
		$this->marking  = $marking;
	}
	
	/**
	 * Get the start position from the corresponding marking.
	 * @return int
	 */
	public function getStart() {
		return $this->marking->getStart();
	}
	
	/**
	 * Get the end position from the corresponding marking.
	 * @return int
	 */
	public function getEnd() {
		return $this->marking->getEnd();
	}
	
	/**
	 * Get the id from the corresponding marking.
	 * @return string
	 */
	public function getId() {
		return $this->marking->getId();
	}
	
	/**
	 * Get the corresponding marking.
	 * @return InlineEditorMarking
	 */
	public function getMarking() {
		return $this->marking;
	}
	
	/**
	 * Get the parent node or root.
	 * @return InlineEditorPiece
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Render the start tag by calling the corresponding marking.
	 * @return string HTML
	 */
	public function renderStartTag() {
		return $this->marking->renderStartTag();
	}
	
	/**
	 * Render the end tag by calling the corresponding marking.
	 * @return string HTML
	 */
	public function renderEndTag() {
		return $this->marking->renderEndTag();
	}
}