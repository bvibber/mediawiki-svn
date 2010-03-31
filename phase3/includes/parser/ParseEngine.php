<?php
/**
 * Acts as the primary interface between the world and the parser.
 * mStartRule - the first rule to use while parsing
 * mRules - The list of rules to use while parsing
 * mDom - Used to create Dom objects and get's returned at the end of parsing
 * mIter - Keeps track of how many times the parser recurses to stop endless loops
 */
class ParseEngine {
	const maxIter = 2048;
	private $mGrammar, $mTextPats;

	function __construct($grammarFile) {
		global $IP;
		$this->mGrammar = new DOMDocument();
		if (! $this->mGrammar->load("$IP/$grammarFile", LIBXML_NOBLANKS)) {
			throw new MWException("Failed to load $grammarFile.");
		}
		foreach ($this->mGrammar->documentElement->childNodes as $crrnt) {
			$this->pushTags($crrnt, NULL);
		}
	}

	function parse($text) {
//print("Text: $text\n");
//foreach (debug_backtrace() as $func) print("{$func["function"]}:{$func["file"]}:{$func["line"]}\n");
		global $wgDebugParserLog;
		if ($wgDebugParserLog != '') {
			wfErrorLog("==========Start Parsing==========\n", $wgDebugParserLog);
		}
		$doc = new DOMDocument();
		$rule = $this->mGrammar->documentElement;
		$rootTag = $doc->createElement($rule->getAttribute("rootTag"));
		$doc->appendChild($rootTag);
		$xpath = new DOMXPath($this->mGrammar);
		$startRule = $xpath->query("/Grammar/*[@name='{$rule->getAttribute("startRule")}']")->item(0);
		$iter = 0;
		if (! $this->parseRec($startRule, "", $saveTags, $iter, $text, $rootTag)) {
			throw new MWException("Failed to parse the given text.");
		}
		$doc->normalizeDocument();
		if ($wgDebugParserLog != '') {
			wfErrorLog("XML - {$doc->saveXML()}\n", $wgDebugParserLog);
		}
		return $doc;
	}

	private function parseRec($rule, $replaceStr, $saveTags, &$iter, &$text, &$outNode) {
		global $wgDebugParserLog;
		if ($wgDebugParserLog != '') {
			wfErrorLog("Entering {$rule->nodeName}, {$rule->getAttribute("name")}\n", $wgDebugParserLog);
		}
		$iter ++;
		if ($iter > ParseEngine::maxIter) {
			throw new MWException("Parser iterated too many times. Probable loop in grammar.");
		}
		if ($rule->nodeName == "Assignment" || $rule->nodeName == "Reference" || $rule->nodeName == "Text") {
			$saveTags = str_replace("~r", preg_quote($replaceStr, "/"), $saveTags);
			$newTags = $rule->getAttribute("saveTags");
			if ($saveTags == "") {
				$saveTags = $newTags;
			} elseif ($newTags != "") {
				$saveTags .= "|" . $newTags;
			}
		}
		$dom = $outNode->ownerDocument;
		$retCode = FALSE;
		if ($rule->nodeName == "Assignment") {
			$startPat = $rule->getAttribute("tag");
			$tag = NULL;
			if ($rule->getAttribute("regex") != NULL) {
				if (preg_match("/^$startPat/s", $text, $matches)) {
					$tag = $matches[0];
					if (isset($matches[1])) {
						$replaceStr = $matches[1];
					}
				}
			} elseif ($startPat != NULL && strncmp($startPat, $text, strlen($startPat)) == 0) {
				$tag = $startPat;
			}
			if ($tag != NULL || $startPat == NULL) {
				$newText = $text;
				$newElement = $dom->createElement($rule->getAttribute("tagName"));
				if ($tag != NULL) {
					$newText = substr($newText, strlen($tag));
					$newElement->setAttribute("tag", $tag);
				}
				$retCode = $rule->firstChild == NULL || $this->parseRec($rule->firstChild, $replaceStr, $saveTags, $iter, $newText, $newElement);
				if ($retCode) {
					$outNode->appendChild($newElement);
					$text = $newText;
				}
			}
		} elseif ($rule->nodeName == "Sequence") {
			$saveText = $text;
			$saveNode = $outNode->cloneNode(TRUE);
			$pushInd = $rule->getAttribute("pushInd");
			foreach ($rule->childNodes as $i => $crrnt) {
				$pushTags = $i >= $pushInd ? $saveTags : "";
				$retCode = $this->parseRec($crrnt, $replaceStr, $pushTags, $iter, $text, $outNode);
				if (! $retCode) {
					$text = $saveText;
					$outNode = $saveNode;
					break;
				}
			}
		} elseif ($rule->nodeName == "Choice") {
			foreach ($rule->childNodes as $crrnt) {
				$retCode = $this->parseRec($crrnt, $replaceStr, $saveTags, $iter, $text, $outNode);
				if ($retCode) {
					break;
				}
			}
			$retCode |= $rule->getAttribute("failSafe") != NULL;
		} elseif ($rule->nodeName == "Reference") {
			$varAttr = $rule->getAttribute("var");
			$newVar = $varAttr == NULL ? $replaceStr : str_replace("~r", $replaceStr, $varAttr);
			$xpath = new DOMXPath($this->mGrammar);
			$refRule = $xpath->query("/Grammar/*[@name='{$rule->getAttribute("name")}']")->item(0);
			$retCode = $this->parseRec($refRule, $newVar, $saveTags, $iter, $text, $outNode);
		} elseif ($rule->nodeName == "Text") {
			$tagSearch = $rule->getAttribute("childTags");
			if ($saveTags != "") {
				$tagSearch .= "|" . $saveTags;
			}
			while ($text != "" && ($saveTags == "" || ! preg_match("/^($saveTags)/s", $text))) {
				$offset = 1;
				foreach ($rule->childNodes as $crrnt) {
					if ($this->parseRec($crrnt, $replaceStr, "", $iter, $text, $outNode)) {
						$offset = 0;
						break;
					}
				}
				if (preg_match("/$tagSearch/s", $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
					if ($matches[0][1] > 0) {
						$outNode->appendChild($dom->createTextNode(substr($text, 0, $matches[0][1])));
						$text = substr($text, $matches[0][1]);
					}
				} else {
					$outNode->appendChild($dom->createTextNode($text));
					$text = "";
				}
			}
			$retCode = true;
		}
		if ($wgDebugParserLog != '') {
			wfErrorLog("Exiting {$rule->nodeName}, Return Code - $retCode\n", $wgDebugParserLog);
			wfErrorLog("Text - $text\n", $wgDebugParserLog);
		}
		return $retCode;
	}

	private function pushTags($rule, $tagStr) {
		$iter = 0;
		if ($rule->nodeName == "Sequence") {
			$pushInd = $rule->childNodes->length - 1;
			$shouldPush = true;
			for ($child = $rule->lastChild; $child != NULL; $child = $child->previousSibling) {
				$this->pushTags($child, $tagStr);
				if ($child->previousSibling != NULL) {
					if ($this->pullTags($child, $iter, $childTag)) {
						if ($shouldPush) {
							$pushInd --;
						}
						if ($tagStr == "") {
							$tagStr = $childTag;
						} elseif ($childTag != "") {
							$tagStr .= "|" . $childTag;
						}
					} else {
						$shouldPush = false;
						$tagStr = $childTag;
					}
				}
			}
			$rule->setAttribute("pushInd", $pushInd);
		} else {
			if ($rule->nodeName != "Choice") {
				$rule->setAttribute("saveTags", $tagStr);
				$tagStr = NULL;
				if ($rule->nodeName == "Text") {
					$childTags = "";
					foreach ($rule->childNodes as $crrnt) {
						if ($childTags != "") {
							$childTags .= "|";
						}
						$this->pullTags($crrnt, $iter, $childTag);
						$childTags .= $childTag;
					}
					$rule->setAttribute("childTags", $childTags);
				}
			}
			foreach ($rule->childNodes as $crrnt) {
				$this->pushTags($crrnt, $tagStr);
			}
		}
	}

	private function pullTags($rule, &$iter, &$childTags) {
		$iter ++;
		if ($iter > ParseEngine::maxIter) {
			throw new MWException("Collecter iterated too many times. Probable loop in grammar.");
		}
		$childTags = "";
		$failSafe = TRUE;
		if ($rule->nodeName == "Assignment") {
			$childTags = $rule->getAttribute("tag");
			if ($rule->nodeName != "Assignment" || $rule->getAttribute("regex") == NULL) {
				$childTags = preg_quote($childTags, "/");
			}
			$failSafe = FALSE;
		} elseif ($rule->nodeName == "Choice" || $rule->nodeName == "Sequence") {
			$failSafe = $rule->nodeName == "Sequence";
			foreach ($rule->childNodes as $child) {
				$failSafe = $this->pullTags($child, $iter, $newTags);
				if ($childTags == "") {
					$childTags = $newTags;
				} elseif ($newTags != "") {
					$childTags .= "|" . $newTags;
				}
				if (($failSafe && $rule->nodeName == "Choice") || (! $failSafe && $rule->nodeName == "Sequence")) {
					break;
				}
			}
			$failSafe |= $rule->nodeName == "Choice" && $rule->getAttribute("failSafe") != NULL;
		} elseif ($rule->nodeName == "Reference") {
			$xpath = new DOMXPath($this->mGrammar);
			$refRule = $xpath->query("/Grammar/*[@name='{$rule->getAttribute("name")}']")->item(0);
			$failSafe = $this->pullTags($refRule, $iter, $childTags);
		}
		return $failSafe;
	}
}

