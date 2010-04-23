<?php
/**
 * Acts as the primary interface between the world and the parser.
 * mStartRule - the first rule to use while parsing
 * mRules - The list of rules to use while parsing
 * mDom - Used to create Dom objects and get's returned at the end of parsing
 * mIter - Keeps track of how many times the parser recurses to stop endless loops
 */
class ParseEngine {
	const maxIter = 4096;
	private $mGrammar;

	function __construct( $grammar ) {
		$xpath = new DOMXPath( $grammar );
		$rootRules = $xpath->query( "/grammar/parseObject | /grammar/rule/parseObject" );
		foreach ( $rootRules as $child ) {
			$this->pushTags( $child, NULL );
		}
		$this->mGrammar = $grammar;
	}

	function parse( $text ) {
		wfDebugLog( "ParseEngine", "==========Start Parse Engine==========\n" );
		$xpath = new DOMXPath( $this->mGrammar );
		$rootAssign = $xpath->query( "/grammar/parseObject" )->item( 0 );
		$doc = new DOMDocument();
		if ( ! $this->parseRec( $rootAssign, "", "", $iter, $text, $doc ) ) {
			$doc = NULL;
		}
		return $doc;
	}

	static function unparse( $inNodes ) {
		$retStr = "";
		foreach ( $inNodes as $child ) {
			if ( $child instanceof DOMText ) {
				$retStr .= $child->data;
			} else {
				$retStr .= $child->getAttribute( "tag" ) . self::unparse( $child->childNodes );
			}
		}
		return $retStr;
	}

	private function parseRec( $rule, $replaceStr, $saveTags, &$iter, &$text, &$outNode ) {
		$iter ++;
		if ( $iter > ParseEngine::maxIter ) {
			throw new MWException( "Parser iterated too many times. Probable loop in grammar." );
		}
		$rule = $rule->firstChild;
		if ( $rule->nodeName == "assignment" || $rule->nodeName == "reference" || $rule->nodeName == "text" ) {
			$saveTags = str_replace( "~r", preg_quote( $replaceStr, "/" ), $saveTags );
			$newTags = $rule->getAttribute( "saveTags" );
			if ( $saveTags == "" ) {
				$saveTags = $newTags;
			} elseif ( $newTags != "" ) {
				$saveTags .= "|" . $newTags;
			}
		}
		$dom = $outNode->ownerDocument == NULL ? $outNode : $outNode->ownerDocument;
		$xpath = new DOMXPath( $rule->ownerDocument );
		$childRules = $xpath->query( "parseObject", $rule );
		$retCode = TRUE;
		if ( $rule->nodeName == "assignment" ) {
			$patterns = $xpath->query( "pattern", $rule );
			$tag = "";
			if ( $patterns->length > 0 ) {
				$pattern = str_replace( "~r", $replaceStr, $patterns->item( 0 )->getAttribute( "tag" ) );
				$retCode = preg_match( "/^$pattern/s", $text, $matches );
				if ( $retCode ) {
					$tag = $matches[0];
					if ( isset( $matches[1] ) ) {
						$replaceStr = $matches[1];
					}
				}
			}
			if ( $retCode ) {
				$newText = $text;
				$newElement = $dom->createElement( $rule->getAttribute( "tag" ) );
				if ( $tag != "" ) {
					$newText = substr( $newText, strlen( $tag ) );
					$newElement->setAttribute( "tag", $tag );
				}
				$retCode = $childRules->length <= 0 || $this->parseRec( $childRules->item( 0 ), $replaceStr, $saveTags, $iter, $newText, $newElement );
				if ( $retCode ) {
					$outNode->appendChild( $newElement );
					$text = $newText;
				}
			}
		} elseif ( $rule->nodeName == "sequence" ) {
			$pushInd = $rule->getAttribute( "pushInd" );
			if ( $pushInd > 0 ) {
				$saveText = $text;
				$saveNode = $outNode->cloneNode( TRUE );
			}
			foreach ( $childRules as $i => $child ) {
				$pushTags = $i >= $pushInd ? $saveTags : "";
				$retCode = $this->parseRec( $child, $replaceStr, $pushTags, $iter, $text, $outNode );
				if ( ! $retCode ) {
					if ( $i > 0 ) {
						$text = $saveText;
						$outNode = $saveNode;
					}
					break;
				}
			}
		} elseif ( $rule->nodeName == "choice" ) {
			foreach ( $childRules as $child ) {
				$retCode = $this->parseRec( $child, $replaceStr, $saveTags, $iter, $text, $outNode );
				if ( $retCode ) {
					break;
				}
			}
			$retCode |= $rule->hasAttribute( "tag" );
		} elseif ( $rule->nodeName == "reference" ) {
			$childRule = $rule->getAttribute( "tag" );
			wfDebugLog( "ParseEngine", "Entering $childRule\n" );
			$varNode = $xpath->query( "pattern", $rule );
			if ( $varNode->length > 0 ) {
				$replaceStr = str_replace( "~r", $replaceStr, $varNode->item( 0 )->getAttribute( "tag" ) );
			}
			$refRule = $xpath->query( "/grammar/rule[@tag='$childRule']/parseObject" )->item( 0 );
			$retCode = $this->parseRec( $refRule, $replaceStr, $saveTags, $iter, $text, $outNode );
			wfDebugLog( "ParseEngine", "Exiting $childRule, Return Code - $retCode\n" );
			wfDebugLog( "ParseEngine", "text - $text\n" );
		} elseif ( $rule->nodeName == "text" ) {
			$tagSearch = $rule->getAttribute( "childTags" );
			if ( $tagSearch == "" ) {
				$tagSearch = $saveTags;
			} elseif ( $saveTags != "" ) {
				$tagSearch .= "|" . $saveTags;
			}
			$childNode = $childRules->length <= 0 ? NULL : $childRules->item( 0 );
			while ( $text != "" && ( $saveTags == "" || ! preg_match( "/^($saveTags)/s", $text ) ) ) {
				$offset = $childNode != NULL && $this->parseRec( $childNode, $replaceStr, "", $iter, $text, $outNode ) ? 0 : 1;
				if ( preg_match( "/$tagSearch/s", $text, $matches, PREG_OFFSET_CAPTURE, $offset ) ) {
					if ( $matches[0][1] > 0 ) {
						$outNode->appendChild( $dom->createTextNode( substr( $text, 0, $matches[0][1] ) ) );
						$text = substr( $text, $matches[0][1] );
					}
				} else {
					$outNode->appendChild( $dom->createTextNode( $text ) );
					$text = "";
				}
			}
		}
		return $retCode;
	}

	private function pushTags( $rule, $tagStr ) {
		$rule = $rule->firstChild;
		$xpath = new DOMXPath( $rule->ownerDocument );
		$childRules = $xpath->query( "parseObject", $rule );
		if ( $rule->nodeName == "sequence" ) {
			$pushInd = 0;
			for ( $i = $childRules->length - 1; $i >= 0; $i -- ) {
				$this->pushTags( $childRules->item( $i ), $tagStr );
				if ( $i > 0 ) {
					if ( $this->pullTags( $childRules->item( $i ), $iter, $childTag ) ) {
						if ( $tagStr == "" ) {
							$tagStr = $childTag;
						} elseif ( $childTag != "" ) {
							$tagStr .= "|" . $childTag;
						}
					} else {
						if ( $pushInd < $i ) {
							$pushInd = $i;
						}
						$tagStr = $childTag;
					}
				}
			}
			$rule->setAttribute( "pushInd", $pushInd );
		} else {
			if ( $rule->nodeName != "choice" ) {
				$rule->setAttribute( "saveTags", $tagStr );
				$tagStr = NULL;
				if ( $rule->nodeName == "text" ) {
					$childTags = "";
					foreach ( $childRules as $child ) {
						if ( $childTags != "" ) {
							$childTags .= "|";
						}
						$this->pullTags( $child, $iter, $childTag );
						$childTags .= $childTag;
					}
					$rule->setAttribute( "childTags", $childTags );
				}
			}
			foreach ( $childRules as $child ) {
				$this->pushTags( $child, $tagStr );
			}
		}
	}

	private function pullTags( $rule, &$iter, &$childTags ) {
		$iter ++;
		if ( $iter > ParseEngine::maxIter ) {
			throw new MWException( "Collecter iterated too many times. Probable loop in grammar." );
		}
		$rule = $rule->firstChild;
		$xpath = new DOMXPath( $rule->ownerDocument );
		$childTags = "";
		$failSafe = TRUE;
		if ( $rule->nodeName == "assignment" ) {
			$patterns = $xpath->query( "pattern", $rule );
			if ( $patterns->length > 0 ) {
				$childTags = $patterns->item( 0 )->getAttribute( "tag" );
			}
			$failSafe = FALSE;
		} elseif ( $rule->nodeName == "choice" || $rule->nodeName == "sequence" ) {
			$childRules = $xpath->query( "parseObject", $rule );
			$failSafe = $rule->nodeName == "sequence";
			foreach ( $childRules as $child ) {
				$failSafe = $this->pullTags( $child, $iter, $newTags );
				if ( $childTags == "" ) {
					$childTags = $newTags;
				} elseif ( $newTags != "" ) {
					$childTags .= "|" . $newTags;
				}
				if ( ( $failSafe && $rule->nodeName == "choice" ) || ( ! $failSafe && $rule->nodeName == "sequence" ) ) {
					break;
				}
			}
			$failSafe |= $rule->hasAttribute( "tag" );
		} elseif ( $rule->nodeName == "reference" ) {
			$refRule = $xpath->query( "/grammar/rule[@tag='{$rule->getAttribute("tag")}']/parseObject" )->item( 0 );
			$failSafe = $this->pullTags( $refRule, $iter, $childTags );
		}
		return $failSafe;
	}
}

