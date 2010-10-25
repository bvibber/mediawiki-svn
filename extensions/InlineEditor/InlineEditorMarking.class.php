<?php
/**
 * This class provides a way of setting markings that apply to wikitext, and store some basic information
 * on them, like a list of classnames, an id which is automatically generated, whether or not it is inline,
 * and whether or not it has been matched before. 
 */
class InlineEditorMarking extends InlineEditorPiece {
	const defaultClasses = 'notEditing'; // default classes; unfortunately we cannot use an array here 
	protected static $lastId = 0; /// < counter which is used to generate unique ids

	protected $start;     /// < start position of the marking in the wikitext
	protected $end;       /// < end position of the marking in the wikitext
	protected $classes;   /// < class(es) attached to the marking, usually identifies the edit mode
	protected $inline;    /// < whether or not the marking is inline or not
	protected $id;        /// < id in the original text; this will be unique even when calculating new ids!
	protected $matched;   /// < bool whether or not this marking has been matched with a previous marking (default: true)

	/**
	 * @param $start   int Start of the marking, offset in number of characters from the begin of the wikitext
	 * @param $end     int End of the marking, offset in number of characters from the begin of the wikitext
	 * @param $classes mixed Class(es) the marking should be labeled with, can be either a string or an array of strings 
	 * @param $inline  bool Whether the marking is inline or not
	 */
	function __construct( $start, $end, $classes, $inline ) {
		$this->start    = $start;
		$this->end      = $end;
		$this->inline   = $inline;
		$this->id       = self::uniqueId();
		$this->matched  = true;
		
		$this->classes  = array();
		$this->addClasses( $classes );
	}
	
	/**
	 * Get the start position in the original wikitext.
	 * @return int
	 */
	public function getStart() { 
		return $this->start; 
	}
	
	/**
	 * Get the end position in the original wikitext.
	 * @return int
	 */
	public function getEnd() { 
		return $this->end; 
	}
	
	/**
	 * Move the start position. The caller should check the validity using isValid().
	 * @param $pos int
	 */
	public function setStart( $pos ) {
		$this->start = $pos;
	}
	
	/**
	 * Move the end position. The caller should check the validity using isValid().
	 * @param $pos int
	 */
	public function setEnd( $pos ) {
		$this->end = $pos;
	}
	
	/**
	 * Get an array of classes that have been assigned to this marking.
	 * @return array
	 */
	public function getClasses() {
		return $this->classes;
	}
	
	/**
	 * Get a space separated list of the classes that have been assigned to this marking.
	 * @return string
	 */
	public function getClass() { 
		return implode( ' ', $this->classes );
	}
	
	/**
	 * Check whether or not a certain class has been assigned to this marking.
	 * @param $class string
	 * @return bool
	 */
	public function hasClass( $class ) {
		return in_array( $class, $this->classes );
	}

	/**
	 * Get the unique ID of this marking.
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Add a class to the list of assigned classes.
	 * @param $class string
	 */
	public function addClass( $class ) {
		$this->addClasses( $class );
	}
	
	/**
	 * Add classes to the list of assigned classes.
	 * @param $classes mixed Either a space separated string or an array.
	 */
	public function addClasses( $classes ) {
		// convert space separated to an array
		if( !is_array($classes) ) $classes = explode( ' ', $classes );
		
		// exclude the default classes that are always included
		$classes = array_diff( $classes, self::getDefaultClassesArray() );
		
		// merge with the current classes and remove duplicates
		$this->classes = array_unique( array_merge( $this->classes, $classes ) );
	}
	
	/**
	 * Remove a class from the list of assigned classes.
	 * @param $class string
	 */
	public function removeClass( $class ) {
		$this->removeClasses( $class );
	}
	
	/**
	 * Remove classes from the list of assigned classes.
	 * @param $classes mixed Either a space separated string or an array.
	 */
	public function removeClasses( $classes ) {
		// convert space separated to an array
		if( !is_array($classes) ) $classes = explode( ' ', $classes );
		
		// exclude the default classes that are always included
		$classes = array_diff( $classes, self::getDefaultClassesArray() );
		
		// save the difference between the existing classes and the classes we're removing
		$this->classes = array_diff( $this->classes, $classes );
	}
	
	/**
	 * Get whether or not this marking has been matched with a previous marking.
	 * Default value is true.
	 * @return bool
	 */
	public function getMatched() {
		return $this->matched;
	}
	
	/**
	 * Set whether or not this marking has been matched with a previous marking.
	 * @param $value bool
	 */
	public function setMatched( $value ) {
		$this->matched = $value;
	}
	
	/**
	 * Overrides InlineEditorPiece to be able to check for equality between markings,
	 * where also classes are compared. This also adds the ability to ignore certain
	 * classes when comparing (such as classes for what has been edited, etc.)
	 * @param $piece InlineEditorPiece Piece or marking to compare with
	 * @param $ignoreClasses array Array of classes that should be ignored
	 * @return bool 
	 */
	public function equals( InlineEditorPiece $piece, array $ignoreClasses = array() ) {
		if( is_a( $piece, 'InlineEditorMarking' ) ) {
			$classesA = array_diff( $this->classes, $ignoreClasses );
			$classesB = array_diff( $piece->classes, $ignoreClasses );
			$diffA    = array_diff( $classesA, $classesB);
			$diffB    = array_diff( $classesB, $classesA);
			return ( parent::equals( $piece ) && empty( $diffA ) && empty( $diffB ) );
		}
		else {
			return parent::equals( $piece );
		}
	}
	
	/**
	 * Render the open tag with classes and id. Depending on $this->inline there will be
	 * a newline after the tag, or an extra 'inline' class.
	 * @return string HTML
	 */
	public function renderStartTag() {
		$attribs = array( 'class' => $this->getFullClass(), 'id' => $this->id );
		return HTML::openElement( 'div', $attribs ) . $this->getNewline();
	}

	/**
	 * Render the close tag (</div>) with an extra newline before it if !$this->inline.
	 * @return string HTML
	 */
	public function renderEndTag() {
		return $this->getNewline() . '</div>';
	}
	
	/**
	 * Get the full class string to render. Includes the default classes
	 * for more convenient CSS, and depending on $this->inline an extra 'inline' class.
	 * @return string Space separated classes
	 */
	protected function getFullClass() {
		return $this->getClass() . ' ' . self::defaultClasses . ($this->inline ? ' inline' : '');
	}
	
	/**
	 * Get a newline or not based on $this->inline.
	 * @return string Empty string or single newline character
	 */
	protected function getNewline() {
		return $this->inline ? '' : "\n";
	}
	
	/**
	 * Get an array version of the default classes that should always be included
	 * @return array
	 */
	protected static function getDefaultClassesArray() { 
		return explode( ' ', self::defaultClasses );
	}

	/**
	 * Get a unique id by using self::$lastId and incrementing it.
	 * @return string
	 */
	protected static function uniqueId() {
		return 'inline-editor-' . self::$lastId++;
	}
	
	/**
	 * Returns the $lastId variable in order to preserve it across requests.
	 * @return int Store this integer and pass it to setUniqueIdState() later on
	 */
	public static function getUniqueIdState() {
		return self::$lastId;
	}
	
	/**
	 * Set the $lastId variable in order to preserve it across requests.
	 * @param $state int State aquired by getUniqueIdState()
	 */
	public static function setUniqueIdState( $state ) {
		if( $state > self::$lastId ) self::$lastId = $state;
	}
}
