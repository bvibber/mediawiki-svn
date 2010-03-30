<?php

/**
 * @ingroup Parser
 */
class Preprocessor {
	private $mParser, $memoryLimit;

	const CACHE_VERSION = 1;

	function __construct( $parser ) {
		$this->mParser = $parser;
		$mem = ini_get( 'memory_limit' );
		$this->memoryLimit = false;
		if ( strval( $mem ) !== '' && $mem != -1 ) {
			if ( preg_match( '/^\d+$/', $mem ) ) {
				$this->memoryLimit = $mem;
			} elseif ( preg_match( '/^(\d+)M$/i', $mem, $m ) ) {
				$this->memoryLimit = $m[1] * 1048576;
			}
		}
	}

	function memCheck() {
		if ( $this->memoryLimit === false ) {
			return;
		}
		$usage = memory_get_usage();
		if ( $usage > $this->memoryLimit * 0.9 ) {
			$limit = intval( $this->memoryLimit * 0.9 / 1048576 + 0.5 );
			throw new MWException( "Preprocessor hit 90% memory limit ($limit MB)" );
		}
		return $usage <= $this->memoryLimit * 0.8;
	}

	/**
	 * Preprocess some wikitext and return the document tree.
	 * This is the ghost of Parser::replace_variables().
	 *
	 * @param string $text The text to parse
	 * @param integer flags Bitwise combination of:
	 *          Parser::PTD_FOR_INCLUSION    Handle <noinclude>/<includeonly> as if the text is being
	 *                                     included. Default is to assume a direct page view.
	 *
	 * The generated DOM tree must depend only on the input text and the flags.
	 * The DOM tree must be the same in OT_HTML and OT_WIKI mode, to avoid a regression of bug 4899.
	 *
	 * Any flag added to the $flags parameter here, or any other parameter liable to cause a
	 * change in the DOM tree for a given text, must be passed through the section identifier
	 * in the section edit link and thus back to extractSections().
	 *
	 * The output of this function is currently only cached in process memory, but a persistent
	 * cache may be implemented at a later date which takes further advantage of these strict
	 * dependency requirements.
	 *
	 * @private
	 */
	function preprocessToObj( $text, $flags = 0 ) {
		wfProfileIn( __METHOD__ );
		global $wgMemc, $wgPreprocessorCacheThreshold;
		
		$xml = false;
		$cacheable = strlen( $text ) > $wgPreprocessorCacheThreshold;
		if ( $cacheable ) {
			wfProfileIn( __METHOD__.'-cacheable' );

			$cacheKey = wfMemcKey( 'preprocess-xml', md5($text), $flags );
			$cacheValue = $wgMemc->get( $cacheKey );
			if ( $cacheValue ) {
				$version = substr( $cacheValue, 0, 8 );
				if ( intval( $version ) == self::CACHE_VERSION ) {
					$xml = substr( $cacheValue, 8 );
					// From the cache
					wfDebugLog( "Preprocessor", "Loaded preprocessor XML from memcached (key $cacheKey)" );
				}
			}
		}
		$dom = false;
		if ( $xml === false ) {
			if ( $cacheable ) {
				wfProfileIn( __METHOD__.'-cache-miss' );
			}
			$dom = $this->mParser->parse($text);
			if ( $cacheable ) {
				$cacheValue = sprintf( "%08d", self::CACHE_VERSION ) . $dom->saveXML();
				$wgMemc->set( $cacheKey, $cacheValue, 86400 );
				wfProfileOut( __METHOD__.'-cache-miss' );
				wfDebugLog( "Preprocessor", "Saved preprocessor XML to memcached (key $cacheKey)" );
			}
		} else {
			wfProfileIn( __METHOD__.'-loadXML' );
			$dom = new DOMDocument;
			wfSuppressWarnings();
			$result = $dom->loadXML( $xml );
			wfRestoreWarnings();
			if ( !$result ) {
				// Try running the XML through UtfNormal to get rid of invalid characters
				$xml = UtfNormal::cleanUp( $xml );
				$result = $dom->loadXML( $xml );
				if ( !$result ) {
					throw new MWException( __METHOD__.' generated invalid XML' );
				}
			}
			wfProfileOut( __METHOD__.'-loadXML' );
		}
		if ( $cacheable ) {
			wfProfileOut( __METHOD__.'-cacheable' );
		}
		wfProfileOut( __METHOD__ );
		return $dom;
	}
}

/**
 * An expansion frame, used as a context to expand the result of preprocessToObj()
 * @ingroup Parser
 */
class PPFrame {
	const NO_ARGS = 1;
	const NO_TEMPLATES = 2;
	const STRIP_COMMENTS = 4;
	const NO_IGNORE = 8;
	const RECOVER_COMMENTS = 16;

	const RECOVER_ORIG = 27; // = 1|2|8|16 no constant expression support in PHP yet

	protected $parser, $title, $titleCache;

	/**
	 * Hashtable listing templates which are disallowed for expansion in this frame,
	 * having been encountered previously in parent frames.
	 */
	protected $loopCheckHash;

	/**
	 * Recursion depth of this frame, top = 0
	 * Note that this is NOT the same as expansion depth in expand()
	 */
	protected $depth;


	/**
	 * Construct a new preprocessor frame.
	 * @param Preprocessor $parser The parent parser
	 */
	function __construct( $parser ) {
		$this->parser = $parser;
		$this->title = $this->parser->mTitle;
		$this->titleCache = array( $this->title ? $this->title->getPrefixedDBkey() : false );
		$this->loopCheckHash = array();
		$this->depth = 0;
	}

	function __get($var) {
		$retVal = NULL;
		if ($var = "depth") {
			return $depth;
		}
		return $retVal;
	}
	/**
	 * Create a new child frame
	 * $args is optionally a multi-root PPNode or array containing the template arguments
	 */
	function newChild( $args = false, $title = false ) {
		$namedArgs = array();
		$numberedArgs = array();
		if ( $title === false ) {
			$title = $this->title;
		}
		if ($args !== false) {
			$xpath = false;
			$index = 1;
			foreach ( $args as $arg ) {
				if ( !$xpath ) {
					$xpath = new DOMXPath( $arg->ownerDocument );
				}
				$first = $xpath->query( 'first', $arg )->item(0)->textContent;
				$value = $xpath->query( 'value', $arg );
				if ($value->length <= 0) {
					// Numbered parameter
					$numberedArgs[$index] = $first;
					$index ++;
				} else {
					// Named parameter
					$namedArgs[trim($first)] = $value->item( 0 )->textContent;
				}
			}
		}
		return new PPTemplateFrame( $this, $numberedArgs, $namedArgs, $title );
	}

	function expand( $root, $flags = 0 ) {
		static $expansionDepth = 0;
		if ( is_string( $root ) ) {
			return $root;
		}

		if ( ++$this->parser->mPPNodeCount > $this->parser->mOptions->mMaxPPNodeCount )
		{
			return '<span class="error">Node-count limit exceeded</span>';
		}

		if ( $expansionDepth > $this->parser->mOptions->mMaxPPExpandDepth ) {
			return '<span class="error">Expansion depth limit exceeded</span>';
		}
		wfProfileIn( __METHOD__ );
		++$expansionDepth;

		if ( $root instanceof DOMDocument ) {
			$root = $root->documentElement;
		}
		if (! $root instanceof DOMElement ) {
			throw new MWException( __METHOD__.': Invalid parameter type' );
		}
//print("UpdIn - {$root->ownerDocument->saveXML()}\n");
		PPFrame::updateIncTags($root, $flags);

print("ParseIn - {$root->ownerDocument->saveXML()}\n");
		$headingIndex = 1;
		$this->expandRec($root->childNodes, $flags, $headingIndex);
		$output = $root->textContent;
print("ParseOut - {$output}\n");

		--$expansionDepth;
		wfProfileOut( __METHOD__ );
		return $output;
	}

	private function expandRec($contextNode, $flags, &$headingIndex) {
		if ($contextNode instanceof DOMNodeList) {
			for ($i = 0; $i < $contextNode->length; $i ++) {
				$child = $contextNode->item($i);
				if ($child instanceof DOMElement) {
					$this->expandRec($child, $flags, $headingIndex);
					$i --;
				}
			}
		} else {
print("ParseRecIn - {$contextNode->nodeName}\n");
			if (($contextNode->nodeName == 'template' || $contextNode->nodeName == 'tplarg') && ! ($flags & self::NO_ARGS)) {
				foreach ($contextNode->childNodes as $child) {
					if ($child->nodeName == "part") {
						foreach ($child->childNodes as $partChild) {
							$this->expandRec($partChild->childNodes, $flags, $headingIndex);
						}
					} else {
						$this->expandRec($child->childNodes, $flags, $headingIndex);
					}
				}
				if ( $contextNode->nodeName == 'template' ) {
					$this->parser->braceSubstitution($contextNode, $this);
				} else {
					$this->parser->argSubstitution($contextNode, $this);
				}
			} elseif ( $contextNode->nodeName == 'comment' ) {
				$comment = $contextNode->getAttribute("startTag");
				# HTML-style comment
				# Remove it in HTML, pre+remove and STRIP_COMMENTS modes
				if ( $this->parser->ot['html']
					|| ( $this->parser->ot['pre'] && $this->parser->mOptions->getRemoveComments() )
					|| ( $flags & self::STRIP_COMMENTS ) )
				{
					if ($comment[0] == "\n" || $comment[strlen($comment) - 1] == "\n") {
						$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode("\n"), $contextNode);
					} else {
						$contextNode->parentNode->removeChild($contextNode);
					}
				}
				# Add a strip marker in PST mode so that pstPass2() can run some old-fashioned regexes on the result
				# Not in RECOVER_COMMENTS mode (extractSections) though
				elseif ( $this->parser->ot['wiki'] && ! ( $flags & self::RECOVER_COMMENTS ) ) {
					$outText = $this->parser->insertStripItem($contextNode->getAttribute("startTag"));
					$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($outText), $contextNode);
				}
				# Recover the literal comment in RECOVER_COMMENTS and pre+no-remove
				else {
					$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($comment), $contextNode);
				}
			} elseif ($contextNode->nodeName == 'ignore') {
				# Output suppression used by <includeonly> etc.
				# OT_WIKI will only respect <ignore> in substed templates.
				# The other output types respect it unless NO_IGNORE is set.
				# extractSections() sets NO_IGNORE and so never respects it.
				if (($this instanceof PPTemplateFrame || ! $this->parser->ot['wiki']) && ! ($flags & self::NO_IGNORE)) {
					$contextNode->parentNode->removeChild($contextNode);
				} else {
					$outText = ParseEngine::unparse($contextNode);
					$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($outText), $contextNode);
				}
			} elseif ( $contextNode->nodeName == 'xmltag' ) {
				foreach ($contextNode->childNodes as $child) {
					$this->expandRec($child->childNodes, $flags, $headingIndex);
				}
				$tagName = substr($contextNode->getAttribute("startTag"), 1);
				$isStripTag = false;
				foreach ($this->parser->getStripList() as $stripTag) {
					$isStripTag = $tagName == $stripTag;
					if ($isStripTag) {
						break;
					}
				}
				if ($isStripTag) {
					$outText = $this->parser->extensionSubstitution($contextNode, $this);
				} else {
					$outText = ParseEngine::unparse($contextNode);
				}
				$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($outText), $contextNode);
			} elseif ($contextNode->nodeName == 'h' && $contextNode->parentNode->nodeName == 'root' && $this->parser->ot['html']) {
				# Insert a heading marker only for <h> children of <root>
				# This is to stop extractSections from going over multiple tree levels
				# Insert heading index marker
				$this->expandRec($contextNode->childNodes, $flags, $headingIndex);
				$titleText = $this->title->getPrefixedDBkey();
				$this->parser->mHeadings[] = array( $titleText, $headingIndex );
				$serial = count( $this->parser->mHeadings ) - 1;
				$marker = "{$this->parser->mUniqPrefix}-h-$serial-" . Parser::MARKER_SUFFIX;
				$this->parser->mStripState->general->setPair( $marker, '' );
				$outText = $contextNode->getAttribute("startTag") . $marker . $contextNode->firstChild->wholeText . 
					$contextNode->getAttribute("endTag");
				$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($outText), $contextNode);
				$headingIndex ++;
			} else {
				$this->expandRec($contextNode->childNodes, $flags, $headingIndex);
				$outText = ParseEngine::unparse($contextNode);
				$contextNode->parentNode->replaceChild($contextNode->ownerDocument->createTextNode($outText), $contextNode);
			}
print("ParseRecOut - {$contextNode->ownerDocument->saveXML()}\n");
		}
	}

	static function updateIncTags($root, $flags = 0) {
		if ( $root instanceof DOMDocument ) {
			$root = $root->documentElement;
		}
		$parent = $root;
		if ($parent instanceof DOMNodeList) {
			$parent = $parent->item(0)->parentNode;
		}
		$xpath = new DOMXPath( $parent->ownerDocument );
		$forInclusion = $flags & Parser::PTD_FOR_INCLUSION;
		$ignoreRest = $forInclusion && $xpath->query("xmltag[@startTag='<onlyinclude']", $parent)->length > 0;
		$children = array();
		$ind = -1;
		while ($parent->hasChildNodes()) {
			$child = $parent->firstChild;
			$parent->removeChild($child);
			$tagName = $child instanceof DOMElement ? substr($child->getAttribute("startTag"), 1) : "";
			if ($tagName != "onlyinclude" && $ignoreRest) {
				if ($ind < 0 || $children[$ind]->nodeName != "ignore") {
					$children[] = $parent->ownerDocument->createElement("ignore");
					$ind ++;
				}
				$children[$ind]->appendChild($child);
			} elseif ($tagName == "includeonly" || $tagName == "noinclude" || $tagName == "onlyinclude") { 
				$leftTag = $parent->ownerDocument->createTextNode("<$tagName>");
				$rightTag = $parent->ownerDocument->createTextNode("</$tagName>");
				$inner = $child->lastChild;
				if (($tagName == "includeonly" && ! $forInclusion) || ($tagName == "noinclude" && $forInclusion)) {
					$children[] = $parent->ownerDocument->createElement("ignore");
					$ind ++;
					$children[$ind]->appendChild($leftTag);
					while ($inner->hasChildNodes()) {
						$gChild = $inner->firstChild;
						$inner->removeChild($gChild);
						$children[$ind]->appendChild($gChild);
					}
					$children[$ind]->appendChild($rightTag);
				} else {
					$children[] = $parent->ownerDocument->createElement("ignore");
					$ind ++;
					$children[$ind]->appendChild($leftTag);
					while ($inner->hasChildNodes()) {
						$children[] = $inner->firstChild;
						$ind ++;
						$inner->removeChild($inner->firstChild);
					}
					$children[] = $parent->ownerDocument->createElement("ignore");
					$ind ++;
					$children[$ind]->appendChild($rightTag);
				}
			} else {
				$children[] = $child;
				$ind ++;
			}
		}
		foreach ($children as $child) {
			$parent->appendChild($child);
		}
	}

	function __toString() {
		return 'frame{}';
	}

	function getPDBK( $level = false ) {
		if ( $level === false ) {
			return $this->title->getPrefixedDBkey();
		} else {
			return isset( $this->titleCache[$level] ) ? $this->titleCache[$level] : false;
		}
	}

	function getArguments() {
		return array();
	}

	function getNumberedArguments() {
		return array();
	}

	function getNamedArguments() {
		return array();
	}

	/**
	 * Returns true if there are no arguments in this frame
	 */
	function isEmpty() {
		return true;
	}

	function getArgument( $name ) {
		return false;
	}

	/**
	 * Returns true if the infinite loop check is OK, false if a loop is detected
	 */
	function loopCheck( $title ) {
		return !isset( $this->loopCheckHash[$title->getPrefixedDBkey()] );
	}

	/**
	 * Return true if the frame is a template frame
	 */
	function isTemplate() {
		return false;
	}
}

/**
 * Expansion frame with template arguments
 * @ingroup Parser
 */
class PPTemplateFrame extends PPFrame {
	private $numberedArgs, $namedArgs, $parent, $numberedExpansionCache, $namedExpansionCache;

	function __construct( $parent = false, $numberedArgs = array(), $namedArgs = array(), $title = false ) {
		PPFrame::__construct( $parent->parser );
		$this->parent = $parent;
		$this->numberedArgs = $numberedArgs;
		$this->namedArgs = $namedArgs;
		$this->title = $title;
		$pdbk = $title ? $title->getPrefixedDBkey() : false;
		$this->titleCache = $parent->titleCache;
		$this->titleCache[] = $pdbk;
		$this->loopCheckHash = /*clone*/ $parent->loopCheckHash;
		if ( $pdbk !== false ) {
			$this->loopCheckHash[$pdbk] = true;
		}
		$this->depth = $parent->depth + 1;
		$this->numberedExpansionCache = $this->namedExpansionCache = array();
	}

	function __toString() {
		$s = 'tplframe{';
		$first = true;
		$args = $this->numberedArgs + $this->namedArgs;
		foreach ( $args as $name => $value ) {
			if ( $first ) {
				$first = false;
			} else {
				$s .= ', ';
			}
			$s .= "\"$name\":\"" .
				str_replace( '"', '\\"', $value->ownerDocument->saveXML( $value ) ) . '"';
		}
		$s .= '}';
		return $s;
	}
	/**
	 * Returns true if there are no arguments in this frame
	 */
	function isEmpty() {
		return !count( $this->numberedArgs ) && !count( $this->namedArgs );
	}

	function getArguments() {
		$arguments = array();
		foreach ( array_merge(
				array_keys($this->numberedArgs),
				array_keys($this->namedArgs)) as $key ) {
			$arguments[$key] = $this->getArgument($key);
		}
		return $arguments;
	}
	
	function getNumberedArguments() {
		$arguments = array();
		foreach ( array_keys($this->numberedArgs) as $key ) {
			$arguments[$key] = $this->getArgument($key);
		}
		return $arguments;
	}
	
	function getNamedArguments() {
		$arguments = array();
		foreach ( array_keys($this->namedArgs) as $key ) {
			$arguments[$key] = $this->getArgument($key);
		}
		return $arguments;
	}

	function getNumberedArgument( $index ) {
		if ( !isset( $this->numberedArgs[$index] ) ) {
			return false;
		}
		if ( !isset( $this->numberedExpansionCache[$index] ) ) {
			# No trimming for unnamed arguments
			$this->numberedExpansionCache[$index] = $this->parent->expand( $this->numberedArgs[$index], self::STRIP_COMMENTS );
		}
		return $this->numberedExpansionCache[$index];
	}

	function getNamedArgument( $name ) {
		if ( !isset( $this->namedArgs[$name] ) ) {
			return false;
		}
		if ( !isset( $this->namedExpansionCache[$name] ) ) {
			# Trim named arguments post-expand, for backwards compatibility
			$this->namedExpansionCache[$name] = trim(
				$this->parent->expand( $this->namedArgs[$name], self::STRIP_COMMENTS ) );
		}
		return $this->namedExpansionCache[$name];
	}

	function getArgument( $name ) {
		$text = $this->getNumberedArgument( $name );
		if ( $text === false ) {
			$text = $this->getNamedArgument( $name );
		}
		return $text;
	}

	/**
	 * Return true if the frame is a template frame
	 */
	function isTemplate() {
		return true;
	}
}

/**
 * Expansion frame with custom arguments
 * @ingroup Parser
 */
class PPCustomFrame extends PPFrame {
	private $args;

	function __construct( $args ) {
		PPFrame::__construct(  );
		$this->args = $args;
	}

	function __toString() {
		$s = 'cstmframe{';
		$first = true;
		foreach ( $this->args as $name => $value ) {
			if ( $first ) {
				$first = false;
			} else {
				$s .= ', ';
			}
			$s .= "\"$name\":\"" .
				str_replace( '"', '\\"', $value->__toString() ) . '"';
		}
		$s .= '}';
		return $s;
	}

	function isEmpty() {
		return !count( $this->args );
	}

	function getArgument( $index ) {
		if ( !isset( $this->args[$index] ) ) {
			return false;
		}
		return $this->args[$index];
	}
}

