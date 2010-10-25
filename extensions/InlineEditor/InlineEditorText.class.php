<?php
/***
 * This class handles all the parsing of markings and wikitext to HTML.
 * 
 * The process is divided in a few basic steps:
 * - when doing a preview, the wikitext is changed and markings are shifted
 * - have extensions add markings to this object based on the wikitext
 * - when doing a preview, markings are matched up with previous markings
 * - the markings are arranged in a tree structure
 * - the node to be rendered is determined (initially, this would be the root)
 * - when doing a preview, some markings are highlighted
 * - the node to be rendered is being rendered to marked wikitext, and then parsed
 * - when any extension objects to partial rendering (because of dependencies elsewhere
 *   in the text), the entire page is re-rendered
 */
class InlineEditorText implements Serializable {
	private $wikiOriginal; /// < original wikitext of the article
	private $article;      /// < article object for parsing
	private $markings;     /// < array of InlineEditorMarking objects
	private $previous;     /// < array of InlineEditorMarking objects before this edit
	private $editedPiece;  /// < InlineEditorMarking object to describe the range where edits occured
	private $changedNode;  /// < Node which should be rendered when doing a partial rendering
	private $root;         /// < Root of the tree where the markings are arranged in
	
	/**
	 * @param $article Article The article to work with.
	 */
	public function __construct( Article $article ) {
		$this->article = $article;
	}
	
	/**
	 * Load the original wikitext from $wikiText.
	 * @param $wikiText string
	 */
	public function loadFromWikiText( $wikiText ) {
		$this->wikiOriginal = $wikiText;
	}

	/**
	 * Get the original wikitext.
	 * @return string Original wikitext
	 */
	public function getWikiOriginal() {
		return $this->wikiOriginal;
	}
	
	/**
	 * Try to get a partial rendering of the page based on which part of the page has been edited.
	 * Before and after partial rendering hooks are called (InlineEditorPartialBeforeParse and 
	 * InlineEditorPartialAfterParse), to have the ability to terminate in the case something in
	 * the partial rendering has a dependency elsewhere on the page. In this case, the entire page 
	 * is rerendered.
	 * 
	 * @return array An array with the id of the object to replace, and the html to replace it with
	 */
	public function getPartialParserOutput() {
		$this->process();
		
		if( $this->changedNode != $this->root ) {
			$markedWiki = $this->changedNode->render();
			if( wfRunHooks( 'InlineEditorPartialBeforeParse', array( $markedWiki ) ) ) {
				$output = $this->parse( $markedWiki );
				if( wfRunHooks( 'InlineEditorPartialAfterParse', array( $output ) ) ) {
					return array( 'id' => $this->changedNode->getId(), 'html' => $output->getText() );
				}
			}
		}
		
		return array( 'id' => $this->root->getId(), 'html' => $this->parse( $this->root->render() )->getText() );
	}
	
	/**
	 * Get the full parser output.
	 * 
	 * Use this method for initially rendering the page, and use
	 * getPartialParserOutput() for subsequent renderings.  
	 * 
	 * @return ParserOutput
	 */
	public function getFullParserOutput() {
		$this->process();
		return $this->parse( $this->root->render() );
	}
	
	/**
	 * Get an array which maps ids to original wikitext.
	 * @return array
	 */
	public function getTexts() {
		$this->process();
		
		$texts = array();
		foreach( $this->markings as $marking ) {
			$texts[$marking->getId()] = substr( $this->wikiOriginal, $marking->getStart(), $marking->getLength() );
		}
		return $texts;
	}
	
	/**
	 * Add a marking to the list of markings. To be called by the different edit modes.
	 * @param $marking InlineEditorMarking
	 */
	public function addMarking( InlineEditorMarking $marking ) {
		if ( !$marking->isValid() ) return;
		$this->markings[$marking->getId()] = $marking;
	}
	
	/**
	 * Handle an edit by replacing the wikitext, and shifting all markings on and after it.
	 * @param $id string Id handle of the edit
	 * @param $text string Text to change to
	 */
	public function doEdit( $id, $text ) {
		// abort if the marking is not known, else use $editMarking for convenience 
		if( !isset( $this->markings[$id] ) ) return;
		$editMarking = $this->markings[$id];
		
		$start  = $editMarking->getStart();
		$end    = $editMarking->getEnd();
		
		// calculate the offset; the difference in length between the original text and new text
		$offset = strlen( $text ) - $editMarking->getLength();
		
		// replace the original text by the new text
		$this->wikiOriginal = substr( $this->wikiOriginal, 0, $start ) . $text . substr( $this->wikiOriginal, $end );
		
		// only shift markings when the length actually changed
		if( $offset != 0) {
			foreach( $this->markings as $id => $marking ) {
				// if the marking is strictly after the edited marking, shift both start and end positions
				if( $marking->getStart() >= $end ) {
					$marking->setStart( $marking->getStart() + $offset );
					$marking->setEnd( $marking->getEnd() + $offset );
				}
				// if the marking is contained in the edited marking, only move the end position
				elseif( $marking->getEnd() >= $start ) {
					$marking->setEnd( $marking->getEnd() + $offset );
				}
				
				// if the marking isn't valid anymore (length <= 0), remove it
				if( !$marking->isValid() ) unset( $this->markings[$id] );
			}
		}
		
		// add 'edited' and 'lastEdit' classes to the edited marking
		// 'edited' will stay (yellow highlight), 'lastEdit' will vanish after an animation
		$editMarking->addClasses( array( 'edited', 'lastEdit' ) );
		
		// store a copy of the edited marking to denote the range of changed wikitext
		// this range might grow when trying to match new markings against these previous markings
		$this->editedPiece = clone $editMarking;
		
		// store the markings as previous markings, and unset $this->markings
		$this->previous = $this->markings;
		unset( $this->markings );
		
		// remove all 'lastEdit' classes so that when copying previous markings to new markings,
		// no leftover animations will be present
		foreach( $this->previous as $marking ) {
			$marking->removeClass( 'lastEdit' );
		}
	}
	
	/**
	 * Have the wikitext marked by different extensions by calling the 'InlineEditorMark' hook.
	 * After that, tries to match previous markings against the new markings, and tries to preserve
	 * the previous markings, while growing $this->editedPiece if needed.
	 */
	protected function mark() {
		// abort if we already did markings
		if( isset( $this->markings ) ) return;
		
		// have the extensions mark the wikitext
		$this->markings = array();
		wfRunHooks( 'InlineEditorMark', array( &$this ) );
		
		// sort the markings while preserving the keys (ids)
		uasort( $this->markings, 'InlineEditorText::sortByStartAndLength' );
		
		// match up previous markings
		$this->matchPreviousMarkings();
	}
	
	/**
	 * Do all the main stuff: have markings (if needed), create the tree,
	 * find the changed node for the partial rendering, and apply highlights.
	 */
	protected function process() {
		if( isset( $this->root ) ) return; 
		
		$this->mark();
		$this->root = $this->buildTree( $this->markings );
		$this->changedNode = $this->findChangedNode();
		$this->applyLastEditHighlight();
	}
	
	/**
	 * Parse the marked wikitext using $this->article.
	 * @param $wikiMarked string Marked wikitext
	 * @return ParserOutput
	 */
	protected function parse( $wikiMarked ) {
		global $wgParser;
		
		// get the same parser options as usual, but remove [edit] links
		$parserOptions = clone $this->article->getParserOptions();
		$parserOptions->setEditSection( false );
		
		// run $wikiMarked through the parser and store the result
		return $wgParser->parse( $wikiMarked, $this->article->getTitle(),
			$parserOptions, true, true, $this->article->getRevIdFetched() );
	}
	
	/**
	 * Previous markings are moved into the current markings list to be able to only
	 * render a part of the page which is much faster. 
	 * 
	 * Match the previous markings by linearly running through two sorted lists;
	 * the current markings and the previous markings. Move one of the lists forward
	 * depending on the sorting conditions, which are start position (asc), length (desc)
	 * and class names (asc).
	 * Whenever a match is found, the previous marking is used to preserve the ids already
	 * present at the client so we don't have to rerender this piece. Whenever a mismatch
	 * occurs, $this->editedPiece grows to include the mismatch, because it needs to be
	 * rerendered.
	 */
	protected function matchPreviousMarkings() {
		// abort if there is nothing to match
		if( empty( $this->previous ) ) return;
		
		// sort the previous markings, while *rekeying* to natural numbers (0, 1, 2, ...)
		// this is nessicary to be able to run through the array using an integer pointer
		usort( $this->previous, 'InlineEditorText::sortByStartAndLength' );
		
		// point to the start of the previous markings list
		$indexPrevious = 0;
		$newMarkings = array();
		foreach( $this->markings as $marking ) {
			// no match found yet
			$foundMatch = false;
			
			// walk through the list of previous markings until the end is reached, we're past the current marking
			// or it has been matched with the current marking
			while( isset( $this->previous[$indexPrevious] ) ) {
				$previous = $this->previous[$indexPrevious];
				
				if( $previous->getStart() < $marking->getStart() ) {
					$indexPrevious++;
				}
				elseif( $previous->getStart() == $marking->getStart() && $previous->getLength() > $marking->getLength() ) {
					$indexPrevious++;
				}
				elseif( $previous->samePositionAs( $marking ) && strcmp( $previous->getClass(), $marking->getClass() ) < 0 ) {
					$indexPrevious++;
				}
				elseif( $marking->equals( $previous, array( 'edited', 'lastEdit') ) ) 
				{
					// a previous marking has been matched with a current marking
					// the previous marking will replace the current one
					$previous->setMatched( true );
					$newMarkings[$previous->getId()] = $previous;
					$foundMatch = true;
					$indexPrevious++;
					break;
				}
				else {
					// if we've moved past the current marking, break, mismatch, and go to the next current marking
					break;
				}
			}
			
			if( !$foundMatch ) {
				// a mismatch occured, so use the new marking and have $this->editedPiece grow to include the new marking
				$marking->setMatched( false );
				$newMarkings[$marking->getId()] = $marking;
				if( $marking->getStart() < $this->editedPiece->getStart() ) $this->editedPiece->setStart( $marking->getStart() );
				if( $marking->getEnd()   > $this->editedPiece->getEnd()   ) $this->editedPiece->setEnd(   $marking->getEnd()   );
			}
		}
		
		// replace $this->markings with the newly generated list and destroy the previous markings
		$this->markings = $newMarkings;
		unset( $this->previous );
	}
	
	/**
	 * Build a tree from an array of sorted (!) markings.
	 * 
	 * @param $markingsSorted array A sorted array of InlineEditorMarking objects.
	 * @return InlineEditorRoot
	 */
	protected function buildTree( array $markingsSorted ) {
		// create the root
		$root = new InlineEditorRoot( &$this->wikiOriginal );
		
		// $workingNode is the node we're trying to add children to
		// init it to the root node
		$workingNode = $root;
		
		foreach( $markingsSorted as $marking ) {
			// create a new node for this marking
			$node = new InlineEditorNode( &$this->wikiOriginal, $marking );
			
			// keep trying to add $node to $workingNode, move a level up if it fails
			while( true )
			{
				// try to add the node as a child to $working node
				if( $workingNode->addChild( $node ) ) {
					$workingNode = $node;
					break;
				}
				else {
					// if it doesn't work, try the parent of $workingNode
					if( $workingNode == $root ) {
						// if we're at the root, stop
						// this should *never happen*!
						break;
					}
					else {
						$workingNode = $workingNode->getParent();
					}
				}
			}
		}
		return $root;
	}
	
	/**
	 * Find the best (deepest) node that contains the edited text.
	 * @return InlineEditorPiece
	 */
	protected function findChangedNode() {
		// if there is no edited piece, just return the root
		if( !isset( $this->editedPiece ) ) return $this->root;
		
		// find the best (deepest) node that contains the edited text
		$changedNode = $this->root->findBestParent( $this->editedPiece );
		
		// go up as long as we're dealing with a node that hasn't been matched, which
		// can only occur when $this->editedPiece coincides with multiple unmatched markings
		// at exactly the same position - in that case the innermost node will be matched
		// whereas we'd like to return the outermost node
		while( $changedNode != $this->root && !$changedNode->getMarking()->getMatched() ) {
			$changedNode = $changedNode->getParent();
		}
		return $changedNode;
	}
	
	/**
	 * Add highlights to all (highest) markings contained in the edited text. 
	 */
	protected function applyLastEditHighlight() {
		// abort if there is no edited text
		if( !isset( $this->editedPiece ) ) return;
		
		// find the markings contained in $this->editedPiece and mark them
		$children = $this->root->findBestChildren( $this->editedPiece );
		foreach( $children as $child ) {
			// don't mark if somehow root appears, as it has no marking attached
			if( $child != $this->root ) {
				$child->getMarking()->addClasses( 'lastEdit edited' );
			}
		}
	}
	
	/**
	 * Sort function which sorts markings - in this particular order - on:
	 * - start position (asc)
	 * - length (desc)
	 * - class name (asc)
	 * @param $a InlineEditorMarking
	 * @param $b InlineEditorMarking
	 * @return int
	 */
	private static function sortByStartAndLength( $a, $b ) {
		if( $a->getStart() == $b->getStart() ) {
			if( $a->getLength() == $b->getLength() ) {
				return strcmp( $a->getClass(), $b->getClass() );
			}
			else {
				return ( $a->getLength() > $b->getLength() ? -1 : 1);
			}
		}
		else {
			return ( $a->getStart() < $b->getStart() ? -1 : 1 );
		}
	}
	
	/**
	 * Serialize by doing a normal serialization of the original wikitext, the markings
	 * and the unique identifier to guarantee unique ids across the session. The serialization
	 * is base64 encoded to make sure it won't be fragged up by javascript.
	 */
	public function serialize() {
		return base64_encode( serialize( array( 
			'wikiOriginal'  => $this->wikiOriginal,
			'markings'        => $this->markings,
			'uniqueIdState' => InlineEditorMarking::getUniqueIdState()
		) ) );
	}

	/**
	 * Unserialize, similar to serializing.
	 */
	public function unserialize( $string ) {
		$data = unserialize( base64_decode( $string ) );
		$this->wikiOriginal = $data['wikiOriginal'];
		$this->markings     = $data['markings'];
		InlineEditorMarking::setUniqueIdState( $data['uniqueIdState'] );
	}
	
	/**
	 * Initial state usable for the editor or API.
	 * @param $text InlineEditorText
	 * @return array Array containing the serialized object and an array of texts
	 */
	public static function initialState( InlineEditorText $text ) {
		return array( 'texts' => $text->getTexts(), 'object' => serialize( $text ) );
	}
	
	/**
	 * Subsequent state usable for the editor or API. 
	 * @param $text InlineEditorText
	 * @return array Array containing the serialized object, an array of texts, and 
	 * an array describing what html should be replaced
	 */
	public static function subsequentState( InlineEditorText $text ) {
		return array(
			'texts' => $text->getTexts(), 
			'partialHtml' => $text->getPartialParserOutput(),
			'object' => serialize( $text )
		);
	}
	
	/**
	 * Restore the InlineEditorText object from a request array and article.
	 * @param $request array
	 * @param $article Article
	 * @return InlineEditorText
	 */
	public static function restoreObject( array $request, Article $article ) {
		$text = unserialize( $request['object'] );
		$text->article = $article;
		return $text;
	}
}
