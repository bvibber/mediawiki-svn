<?php

/**
 * A rule specifying how to parse the text.  
 * If the text matches mBeginTag then a ParseTree object is created with the appropriate info.
 * mName - The name to give the resultant ParseTree object
 * mBeginTag - the regular expression used to determine if this is the rule that should be used
 * mEndTag - If ParseTrees of this type are to have children, mEndTag specifies when all of the children are collected
 * mStopChars - extra characters that indicate markup
 * mChildRule - an extra rule to consider when collecting children, it is only used for situations covered by the HHP21 parser test
 * @ingroup Parser
 */
class ParseRule {
	private $mName, $mBeginTag, $mEndTag, $mStopChars, $mChildRule;

	function __construct($name, $beginTag, $endTag = NULL, $stopChars = '', $childRule = NULL) {
		$this->mName = $name;
		$this->mBeginTag = $beginTag;
		$this->mEndTag = $endTag;
		$this->mStopChars = $stopChars;
		$this->mChildRule = $childRule;
	}

	function parse(&$text, $parseList) {
		$retTree = NULL;

		if (preg_match($this->mBeginTag, $text, $matches)) {
			$text = substr($text, strlen($matches[0]));
			$children = array();
			if ($this->mEndTag != NULL) {
				$endTag = $this->mEndTag;
				foreach ($matches as $i => $crrnt) {
					$endTag = str_replace('~' . $i, $crrnt, $endTag);
				}
				while ($text != "" && ($endTag == NULL || ! preg_match($endTag, $text, $endMatches))) {
					if ($this->mChildRule != NULL) {
						$child = $this->mChildRule->parse($text, $parseList);
						if ($child != NULL) {
							$children[] = $child;
						}
					}
					$moreChildren = $parseList->parse($text, $this->mStopChars);
					$children = array_merge($children, $moreChildren);
				}
				if ($text != "") {
					$text = substr($text, strlen($endMatches[0]));
					$matches = array_merge($matches, $endMatches);
				}
			}
			$retTree = new ParseTree($this->mName, $matches, $children);
		}

		return $retTree;
	}
}

/**
 * Contains a list of rules to cycle through when creating a parse tree
 * mList - The list of rules
 * mStopChars - the characters used to find markup
 * @ingroup Parser
 */
class ParseList {
	private $mList, $mStopChars;

	function __construct($list, $stopChars) {
		$this->mList = $list;
		$this->mStopChars = $stopChars;
	}

	function parse(&$text, $stopChars) {
		$children = array();

		foreach ($this->mList as $crrnt) {
			$child = $crrnt->parse($text, $this);
			if ($child != NULL) {
				$children[] = $child;
				break;
			}
		}
		if ($child == NULL) {
			$children[] = $text[0];
			$text = substr($text, 1);
		}
		if (preg_match('/^[^' . $this->mStopChars . $stopChars . ']+/s', $text, $matches)) {
			$children[] = $matches[0];
			$text = substr($text, strlen($matches[0]));
		}

		return $children;
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

	static function createParseTree($text, $parseList) {
		wfProfileIn( __METHOD__ );

		$text = "~BOF" . $text;
		$root = new ParseRule("root", '/^/', '/^\Z/');
		$retTree = $root->parse($text, $parseList);

		wfProfileOut( __METHOD__ );
		return $retTree;
	}

	//this function will definitely need to be seperated into data and engine layers
	function printTree() {
		$retString = "";

		if ($this->mName == "hhp21") {
			$retString = htmlspecialchars($this->mMatches[0]);
		} elseif ($this->mName == "commentline") {
			$retString = htmlspecialchars($this->mMatches[1]) . "<comment>" . htmlspecialchars($this->mMatches[2]) . "</comment>";
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
		} elseif (($this->mName == "template" || $this->mName == "tplarg") && isset($this->mMatches[1])) {
			$inTitle = true;
			$foundEquals = false;
			$currentItem = "";
			$this->mChildren[] = '|';
			foreach ($this->mChildren as $crrnt) {
				if ($crrnt instanceof ParseTree) {
					$currentItem .= $crrnt->printTree();
				} elseif ($crrnt == '|') {
					if ($inTitle) {
						$retString .= "<title>" . $currentItem . "</title>";
						$inTitle = false;
					} else {
						if (! $foundEquals) {
							$retString .= "<part>";
						}
						$retString .= "<value>" . $currentItem . "</value></part>";
						$foundEquals = false;
					}
					$currentItem = "";
				} elseif ($crrnt == '=' && ! $inTitle && ! $foundEquals) {
					$retString .= "<part><name>" . $currentItem . "</name>";
					$foundEquals = true;
					$currentItem = "";
				} else {
					$currentItem .= htmlspecialchars($crrnt);
				}
			}
			$retString = "<" . $this->mName . ">" . $retString . "</" . $this->mName . ">";
		} else {
			foreach ($this->mChildren as $crrnt) {
				if ($crrnt instanceof ParseTree) {
					$retString .= $crrnt->printTree();
				} else {
					$retString .= htmlspecialchars($crrnt);
				}
			}
			if ($this->mName == "root") {
				$retString = "<" . $this->mName . ">" . $retString . "</" . $this->mName . ">";
			} elseif ($this->mName == "tplarg" || $this->mName == "template") {
				$retString = htmlspecialchars($this->mMatches[0]) . $retString;
			} elseif ($this->mName == "link") {
				$retString = htmlspecialchars($this->mMatches[0]) . $retString;
 				if (isset($this->mMatches[1])) {
					$retString .= htmlspecialchars($this->mMatches[1]);
				}
			} elseif ($this->mName == "h") {
				$retString = htmlspecialchars($this->mMatches[2]) . $retString;
				if (isset($this->mMatches[3])) {
					$retString = "<h>" . $retString . htmlspecialchars($this->mMatches[3]) . "</h>";
				}
				if ($this->mMatches[1] == "\n") {
					$retString = "\n" . $retString;
				}
			}
		}

		return $retString;
	}
}

