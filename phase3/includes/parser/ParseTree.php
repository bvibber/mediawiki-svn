<?php

/**
 * Interface for Parse Object each with a specialized task while parsing
 * @ingroup Parser
 */
abstract class ParseObject {
	protected $mName;

	function __construct($name) {
		$this->mName = $name;
	}

	// Does the parse task specific to each parse object
	abstract function parse(&$text, &$rules, $endTag = NULL);
}

/**
 * A rule specifying how to parse the text.  
 * If the text matches mBeginTag then a ParseTree object is created with the appropriate info.
 * mName - The name to give the resultant ParseTree object
 * mBeginTag - the regular expression used to determine if this is the rule that should be used
 * mEndTag - If ParseTrees of this type are to have children, mEndTag specifies when all of the children are collected
 * mChildRule - What Parse rule to use to gather children for this element
 * @ingroup Parser
 */
class ParseRule extends ParseObject {
	private $mBeginTag, $mChildRule, $mEndTag;

	function __construct($name, $beginTag, $childRule = NULL, $endTag = NULL) {
		parent::__construct($name);
		$this->mBeginTag = $beginTag;
		$this->mChildRule = $childRule;
		$this->mEndTag = $endTag;
	}

	function parse(&$text, &$rules, $endTag = NULL) {
		if (! preg_match($this->mBeginTag, $text, $matches)) {
			return NULL;
		}
		$text = substr($text, strlen($matches[0]));
		$child = NULL;
		if ($this->mChildRule != NULL) {
			if ($this->mEndTag != NULL) {
				$endTag = $this->mEndTag;
				foreach ($matches as $i => $crrnt) {
					$endTag = str_replace('~' . $i, $crrnt, $endTag);
				}
			}
			$child = $rules[$this->mChildRule]->parse($text, $rules, $endTag);
			if ($child == NULL) {
				return NULL;
			}
		}
		return new ParseTree($this->mName, $matches, array($child));
	}
}

/**
 * A rule specifying how to parse the text.  
 * If the text matches mBeginTag then a ParseTree object is created with the appropriate info.
 * mName - The name to give the resultant ParseTree object
 * mChildRule - What Parse rule to use to gather children for this element
 * mEndTag - If ParseTrees of this type are to have children, mEndTag specifies when all of the children are collected
 * mMinChildren - Minimum amount of children for this rule
 * mMaxChildren - Maximum amount of children for this rule, 0 means unlimited
 * @ingroup Parser
 */
class ParseQuant extends ParseObject {
	private $mChildRule, $mEndTag, $mMinChildren, $mMaxChildren;

	function __construct($name, $childRule, $endTag = NULL, $minChildren = 0, $maxChildren = 0) {
		parent::__construct($name);
		$this->mChildRule = $childRule;
		$this->mEndTag = $endTag;
		$this->mMinChildren = $minChildren;
		$this->mMaxChildren = $maxChildren;
	}

	function parse(&$text, &$rules, $endTag = NULL) {
		$endRegEx = $this->mEndTag;
		if ($this->mEndTag != NULL && $endTag != NULL) {
			$endRegEx = str_replace('~r', $endTag, $this->mEndTag);
		}
		$children = array();
		for ($i = 0; $i < $this->mMinChildren || (($endRegEx == NULL || ! preg_match($endRegEx, $text, $matches)) && 
			($this->mMaxChildren <= 0 || $i < $this->mMaxChildren)); $i ++) {
			$child = $rules[$this->mChildRule]->parse($text, $rules, $endTag);
			if ($child == NULL) {
				return NULL;
			}
			$children[] = $child;
		}
		if ($this->mEndTag != NULL) {
			if (! isset($matches[0])) {
				return NULL;
			}
			$text = substr($text, strlen($matches[0]));
		}
		return new ParseTree($this->mName, $matches, $children);
	}
}

/**
 * Contains a list of rules to cycle through when creating a parse tree
 * mList - The list of rules
 * @ingroup Parser
 */
class ParseList extends ParseObject {
	private $mList;

	function __construct($name, $list) {
		parent::__construct($name);
		$this->mList = $list;
	}

	function parse(&$text, &$rules, $endTag = NULL) {
		foreach ($this->mList as $crrnt) {
			$newText = $text;
			$child = $rules[$crrnt]->parse($newText, $rules, $endTag);
			if ($child != NULL) {
				$text = $newText;
				return new ParseTree($this->mName, NULL, array($child));
			}
		}
		return NULL;
	}
}

/**
 * Contains a sequence of rules all of which must pass
 * mName - The name to give the resultant ParseTree object
 * mList - The sequence of rules
 * @ingroup Parser
 */
class ParseSeq extends ParseObject {
	private $mList;

	function __construct($name, $list) {
		parent::__construct($name);
		$this->mList = $list;
	}

	function parse(&$text, &$rules, $endTag = NULL) {
		$children = array();
		foreach ($this->mList as $crrnt) {
			$child = $rules[$crrnt]->parse($text, $rules, $endTag);
			if ($child == NULL) {
				return NULL;
			}
			$children[] = $child;
		}
		return new ParseTree($this->mName, NULL, $children);
	}
}

/**
 * The parse tree of the data.
 * printTree translates the parse tree to xml, eventually this should be seperated into a data and engine layer.
 * mName - Indicates what ParseRule was used to create this node
 * mMatches - The text groups that were collected by the regular expressions used when creating this rule
 * mChildren - The child ParseTree nodes in this tree
 * @ingroup Parser
 */
class ParseTree {
	private $mName, $mMatches, $mChildren;	

	function __construct($name, $matches, $children) {
		$this->mName = $name;
		$this->mMatches = $matches;
		$this->mChildren = $children;
	}

	function getName() {
		return $this->mName;
	}

	static function createParseTree($text, $rules) {
		wfProfileIn( __METHOD__ );

		$text = "~BOF" . $text;
		$retTree = $rules["Root"]->parse($text, $rules);

		wfProfileOut( __METHOD__ );
		return $retTree;
	}

	//this function will definitely need to be seperated into data and engine layers
	function printTree() {
		$retString = "";

		if ($this->mName == "text") {
			$retString = htmlspecialchars($this->mMatches[0]);
		} elseif ($this->mName == "commentline") {
			$retString = "\n<comment>" . htmlspecialchars($this->mMatches[1]) . "</comment>";
		} elseif ($this->mName == "bof") {
			if (isset($this->mMatches[1])) {
				$retString = "<ignore>" . htmlspecialchars($this->mMatches[1]) . "</ignore>";
			}
		} elseif ($this->mName == "comment" || $this->mName == "ignore") {
			$retString = "<" . $this->mName . ">" . htmlspecialchars($this->mMatches[0]) . "</" . $this->mName . ">";
		} elseif ($this->mName == "ext") {
			$retString = "<name>" . htmlspecialchars($this->mMatches[1]) . "</name><attr>" . htmlspecialchars($this->mMatches[2]) . "</attr>";
			if (isset($this->mMatches[3])) {
				$retString .= "<inner>" . htmlspecialchars($this->mMatches[3]) . "</inner>";
			}
			if (isset($this->mMatches[4])) {
				$retString .= "<close>" . htmlspecialchars($this->mMatches[4]) . "</close>";
			}
			$retString = "<" . $this->mName . ">" . $retString . "</" . $this->mName . ">";
		} elseif ($this->mName == "link") {
			$retString = htmlspecialchars($this->mMatches[0]) . $this->mChildren[0]->printTree() . "]]";
		} elseif ($this->mName == "h") {
			$retString = "<h>" . htmlspecialchars($this->mMatches[2]) . $this->mChildren[0]->printTree() . 
				htmlspecialchars($this->mMatches[2]) . "</h>";
			if ($this->mMatches[1] == "\n") {
				$retString = "\n" . $retString;
			}
		} else {
			foreach ($this->mChildren as $crrnt) {
				$retString .= $crrnt->printTree();
			}
			if ($this->mName != "unnamed") {
				$retString = "<" . $this->mName . ">" . $retString . "</" . $this->mName . ">";
			}
		}

		return $retString;
	}
}

