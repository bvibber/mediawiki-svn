<?php
/**
 * This class provides a rootnode for a tree which provides a good structure for markings.
 * It is closely connected to the wikitext, and should be recreated whenever a marking or
 * wikitext changes. InlineEditorNode inherits from this class.
 */
class InlineEditorRoot extends InlineEditorPiece {
	protected $wiki;     /// < reference to the original wikitext
	protected $children; /// < array of children (InlineEditorNode)
	protected $isSorted; /// < bool whether or not the children are sorted
	protected $lastEnd;  /// < largest endposition of children, to verify during adding the children are sortd
	
	/**
	 * @param $wiki String Reference to the original wikitext
	 */
	public function __construct( &$wiki ) {
		$this->wiki = &$wiki;
		$this->children = array();
		$this->lastEnd = 0;
		$this->isSorted = true;
	}
	
	/**
	 * Always span the entire text.
	 * @return int
	 */
	public function getStart() {
		return 0;
	}
	
	/**
	 * Always span the entire text.
	 * @return int
	 */
	public function getEnd() {
		return strlen( $this->wiki );
	}
	
	/**
	 * Always returns 'inline-editor-root', the outermost div.
	 * @return string
	 */
	public function getId() {
		return 'inline-editor-root';
	}
	
	/**
	 * Get an array of children of type InlineEditorNode.
	 * @return array
	 */
	public function getChildren() {
		return $this->children;
	}
	
	/**
	 * Add a node to the list of children.
	 * 
	 * Checks whether or not the child can be added. Returns false if it cannot add the
	 * child, and true when it can. The calling class is responsible to add the node to
	 * the innermost node, this will not be done by the function.
	 * 
	 * It is recommended to add nodes from left to right, as this gives the best performance.
	 * 
	 * @param $child InlineEditorNode
	 * @return bool
	 */
	public function addChild( InlineEditorNode $child ) {
		// if we cannot contain the child, we cannot add it
		if( !$this->canContain( $child ) ) return false;
		
		// if the start is before the largest endpoint, check all children for overlap
		if( $child->getStart() < $this->lastEnd) {
			foreach( $this->children as $otherChild ) {
				if( $child->hasOverlap( $otherChild ) ) return false;
			}
			
			// if there is no overlap, we're sure that the list isn't sorted anymore 
			$this->isSorted = false;
		}
		
		// add the child and set the parent of the child to $this
		$this->children[$child->getStart()] = $child;
		$child->parent = $this;
		
		// move $this->lastEnd if needed
		if( $child->getEnd() > $this->lastEnd ) $this->lastEnd = $child->getEnd();
		
		return true;
	}
	
	/**
	 * Find the node (or root) with the smallest length still able to contain $piece.
	 * @param $piece InlineEditorPiece
	 * @return InlineEditorPiece
	 */
	public function findBestParent( InlineEditorPiece $piece ) {
		// if we cannot contain the piece, return false
		if( !$this->canContain( $piece ) ) return false;
		
		// sorted children is a precondition for the algoritm
		$this->sort();
		
		foreach( $this->children as $start => $child ) {
			// if we've move past the end of the piece, stop
			if( $piece->getEnd() < $start ) break;
			
			// try to fit the piece to this child
			if( $piece->getStart() >= $start ) {
				$fit = $child->findBestParent( $piece );
				// if we found a child that fits the piece, return it
				if( $fit !== false ) {
					return $fit;
				}
			}
		}
		
		// if we cannot find a suitable child, but we can contain it in this piece, return $this
		return $this;
	}
	
	/**
	 * Find the highest level of children that can be fit into a certain piece.
	 * This will return an array of nodes that are best fit.
	 * @param $piece InlineEditorPiece
	 * @return array
	 */
	public function findBestChildren( InlineEditorPiece $piece ) {
		// try to find a parent that fits $piece (which can very well be $this!)
		$parent = $this->findBestParent( $piece );
		
		// if we cannot find a parent, return false
		if( !$parent ) return false;
		
		// if the piece can contain the entire parent piece, just return that piece
		if( $piece->canContain( $parent ) ) return array( $parent );
		
		// sorting is a precondition of the algoritm
		$this->sort();
		
		$children = array();
		foreach( $parent->children as $start => $child ) {
			// if we've moved past the end of the piece, stop
			if( $start > $piece->getEnd() ) break;
			
			// add the child to the list if it can be contained in the piece
			if( $piece->canContain( $child ) ) {
				$children[] = $child;
			}
		}
		return $children;
	}
	
	/**
	 * Render the start tag, in this case always a div with id="inline-editor-root".
	 * @return string HTML
	 */
	protected function renderStartTag() {
		return '<div id="inline-editor-root">' . "\n";
	}
	
	/**
	 * Render the end tag, in this case always a closing div.
	 * @return string HTML
	 */
	protected function renderEndTag() {
		return "\n</div>";
	}
	
	/**
	 * Render the entire tag, with recursion on the children.
	 * @return string HTML
	 */
	public function render() {
		$this->sort();
		$lastPos = $this->getStart();
		$output  = $this->renderStartTag();
		foreach( $this->children as $child ) {
			$output .= substr( $this->wiki, $lastPos, $child->getStart() - $lastPos );
			$output .= $child->render();
			$lastPos = $child->getEnd();
		}
		
		$output .= substr( $this->wiki, $lastPos, $this->getEnd() - $lastPos );
		$output .= $this->renderEndTag();
		
		return $output;
	}
	
	/**
	 * Sort the children by start position (key).
	 */
	protected function sort() {
		if( $this->isSorted ) return;
		ksort( $this->children );
		$this->isSorted = true;
	}
}