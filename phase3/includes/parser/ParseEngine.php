<?php
/**
 * Acts as the primary interface between the world and the parser.
 * mRules - The list of rules to use while parsing
 * mStartRule - the first rule to use while parsing
 * mDom - Used to create Dom objects and get's returned at the end of parsing
 */
class ParseEngine {
	const maxIter = 8192;
	private $mRules, $mStartRule, $mDom, $mIter;

	function __construct($rules, $startRule) {
		$this->mRules = $rules;
		$this->mStartRule = $startRule;
	}

	function parse(&$text) {
		global $wgDebugParserLog;
		if ($wgDebugParserLog != '') {
			wfErrorLog("==========Start Parsing==========\n", $wgDebugParserLog);
		}
		$this->mIter = 0;
		$this->mDom = new DOMDocument();
		if (! $this->callParser($this->mStartRule, $text, $children, NULL)) {
			throw new MWException("Parser rejected text.");
		}
		$this->mDom->appendChild($children[0]);
		$this->mDom->normalizeDocument();
		if ($wgDebugParserLog != '') {
			wfErrorLog("XML - {$this->mDom->saveXML()}\n", $wgDebugParserLog);
		}
		return $this->mDom;
	}

	function callParser($child, &$text, &$children, $replaceStr) {
		$childName = get_class($child);
		if (is_string($child)) {
			$childName = $child;
			$child = $this->mRules[$childName];
		}
		global $wgDebugParserLog;
		if ($wgDebugParserLog != '') {
			wfErrorLog("Entering $childName\n", $wgDebugParserLog);
		}
		$this->mIter ++;
		if ($this->mIter > ParseEngine::maxIter) {
			throw new MWException("Parser iterated too many times. Probable loop in grammar.");
		}
		$retCode = $child->parse($text, $this, $this->mDom, $children, $replaceStr);
		if ($wgDebugParserLog != '') {
			wfErrorLog("Exiting $childName, Return Code - $retCode\n", $wgDebugParserLog);
			wfErrorLog("Text - $text\n", $wgDebugParserLog);
		}
		return $retCode;
	}
}


// Interface for Parse objects each with a specialized task while parsing
interface ParseObject {
	// Does the parse task specific to each parse object
	function parse(&$text, &$engine, &$dom, &$children, $replaceStr);
}

/**
 * Deals with pattern matching and saving strings from the text.
 * mMatchPat - the regular expression used to determine if this is the rule that should be used
 */
class ParsePattern implements ParseObject {
	private $mMatchPat;

	function __construct($matchPat) {
		$this->mMatchPat = $matchPat;
	}

	function parse(&$text, &$engine, &$dom, &$children, $replaceStr) {
		$regEx = $this->mMatchPat;
		if ($replaceStr != NULL) {
			$regEx = str_replace('~r', $replaceStr, $regEx);
		}
		if (! preg_match($regEx, $text, $matches)) {
			return FALSE;
		}
		$text = substr($text, strlen($matches[0]));
		$children = array();
		if (isset($matches[1])) {
			$children[] = $dom->createTextNode($matches[1]);
		}
		return TRUE;
	}
}

/**
 * Deals with cases where a rule can be matched multiple or 0 times.
 * mChildRule - What Parse rule to quantify
 * mMinChildren - Minimum amount of children for this rule
 * mMaxChildren - Maximum amount of children for this rule, 0 means unlimited
 */
class ParseQuant implements ParseObject {
	private $mChildRule, $mMinChildren, $mMaxChildren;

	function __construct($childRule, $minChildren = 0, $maxChildren = 0) {
		$this->mChildRule = $childRule;
		$this->mMinChildren = $minChildren;
		$this->mMaxChildren = $maxChildren;
	}

	function parse(&$text, &$engine, &$dom, &$children, $replaceStr) {
		$children = array();
		for ($i = 0; $this->mMaxChildren <= 0 || $i < $this->mMaxChildren; $i ++) {
			if (! $engine->callParser($this->mChildRule, $text, $retChildren, $replaceStr)) {
				if ($i < $this->mMinChildren)  {
					return FALSE;
				}
				break;
			}
			$children = array_merge($children, $retChildren);
		}
		return TRUE;
	}
}

/**
 * Cycles throug array of rules until it finds one that succeeds
 * mList - The list of rules
 * mMatchChar - This is a shortcut. If the starting char of the text is different then parse will return FALSE.
 */
class ParseChoice implements ParseObject {
	private $mList;

	function __construct() {
		$this->mList = $args = func_get_args();
	}

	function parse(&$text, &$engine, &$dom, &$children, $replaceStr) {
		foreach ($this->mList as $crrnt) {
			$newText = $text;
			if ($engine->callParser($crrnt, $newText, $children, $replaceStr)) {
				$text = $newText;
				return TRUE;
			}
		}
		return FALSE;
	}
}

/**
 * Contains a sequence of rules all of which must pass
 * mList - The sequence of rules
 * mReplaceStr - A string used to determine the close tag of bracketed markup
 * mSaveStr - Boolean specifying wheter to pull mReplaceStr from text
 */
class ParseSeq implements ParseObject {
	private $mList, $mReplaceStr, $mSaveStr;

	function __construct($list, $replaceStr = NULL, $saveStr = FALSE) {
		$this->mList = $list;
		$this->mReplaceStr = $replaceStr;
		$this->mSaveStr = $saveStr;
	}

	function parse(&$text, &$engine, &$dom, &$children, $replaceStr) {
		if ($this->mReplaceStr != NULL) {
			if ($replaceStr != NULL) {
				$replaceStr = str_replace('~r', $replaceStr, $this->mReplaceStr);
			} else {
				$replaceStr = $this->mReplaceStr;
			}	
		}
		$children = array();
		foreach ($this->mList as $i => $crrnt) {
			if (! $engine->callParser($crrnt, $text, $retChildren, $replaceStr)) {
				return FALSE;
			}
			if ($i == 0 && $this->mSaveStr && isset($retChildren[0]) && $retChildren[0] instanceof DOMText) {
				$replaceStr = $retChildren[0]->wholeText;
			} else {
				$children = array_merge($children, $retChildren);
			}
		}
		return TRUE;
	}
}

/**
 * Creates a Dom element
 * mName - The name to give the resultant ParseTree object
 * mAttrName - name of an attribute to add to the element
 * mAttrValue - value of the attribute
 */
class ParseAssign implements ParseObject {
	private $mName, $mChildRule, $mAttrName, $mAttrValue;

	function __construct($name, $childRule, $attrName = NULL, $attrValue = NULL) {
		$this->mName = $name;
		$this->mChildRule = $childRule;
		$this->mAttrName = $attrName;
		$this->mAttrValue = $attrValue;
	}

	function parse(&$text, &$engine, &$dom, &$children, $replaceStr) {
		if (! $engine->callParser($this->mChildRule, $text, $retChildren, $replaceStr)) {
			return FALSE;
		}
		$retNode = $dom->createElement($this->mName);
		foreach ($retChildren as $child) {
			$retNode->appendChild($child);
		}
		if ($this->mAttrName != NULL && $this->mAttrValue != NULL) {
			$retNode->setAttribute($this->mAttrName, $this->mAttrValue);
		}
		$children = array($retNode);
		return TRUE;
	}
}

