<?php

/**
 * Interface for Parse Object each with a specialized task while parsing
 * @ingroup Parser
 */
interface ParseObject {
	// Does the parse task specific to each parse object
	function parse(&$text, &$rules);
}

/**
 * A rule specifying how to parse the text.  
 * If the text matches mBeginTag then a ParseTree object is created with the appropriate info.
 * mName - The name to give the resultant ParseTree object
 * mBeginTag - the regular expression used to determine if this is the rule that should be used
 * mEndTag - If ParseTrees of this type are to have children, mEndTag specifies when all of the children are collected
 * mChildRule - an extra rule to consider when collecting children, it is only used for situations covered by the HHP21 parser test
 * @ingroup Parser
 */
class ParseRule implements ParseObject {
	private $mName, $mBeginTag, $mEndTag, $mChildRule;

	function __construct($name, $beginTag, $endTag = NULL, $childRule = NULL) {
		$this->mName = $name;
		$this->mBeginTag = $beginTag;
		$this->mEndTag = $endTag;
		$this->mChildRule = $childRule;
	}

	function parse(&$text, &$rules) {
		if (! preg_match($this->mBeginTag, $text, $matches)) {
			return NULL;
		}
		$newText = substr($text, strlen($matches[0]));
		$children = array();
		if ($this->mChildRule != NULL && $this->mEndTag != NULL) {
			$endTag = $this->mEndTag;
			foreach ($matches as $i => $crrnt) {
				$endTag = str_replace('~' . $i, $crrnt, $endTag);
			}
			while (! preg_match($endTag, $newText, $endMatches)) {
				$child = $rules[$this->mChildRule]->parse($newText, $rules);
				if ($child == NULL) {
					return NULL;
				}
				$children[] = $child;
			}
			$newText = substr($newText, strlen($endMatches[0]));
			$matches = array_merge($matches, $endMatches);
		}
		$text = $newText;
		return new ParseTree($this->mName, $matches, $children);
	}
}

/**
 * Contains a list of rules to cycle through when creating a parse tree
 * mList - The list of rules
 * @ingroup Parser
 */
class ParseList implements ParseObject {
	private $mList;

	function __construct($list) {
		$this->mList = $list;
	}

	function parse(&$text, &$rules) {
		foreach ($this->mList as $crrnt) {
			$child = $rules[$crrnt]->parse($text, $rules);
			if ($child != NULL) {
				return $child;
			}
		}
		return NULL;
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
		} elseif (($this->mName == "template" || $this->mName == "tplarg") && isset($this->mMatches[1])) {
			$inTitle = true;
			$foundEquals = false;
			$currentItem = "";
			$this->mChildren[] = new ParseTree("pipe", NULL, NULL);
			foreach ($this->mChildren as $crrnt) {
				if ($crrnt instanceof ParseTree) {
					if ($crrnt->getName() == "pipe") {
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
					} elseif ($crrnt->getName() == "equals") {
						if (! $inTitle && ! $foundEquals) {
							$retString .= "<part><name>" . $currentItem . "</name>";
							$foundEquals = true;
							$currentItem = "";
						} else {
							$currentItem .= "=";
						}
					} else {
						$currentItem .= $crrnt->printTree();
					}
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

