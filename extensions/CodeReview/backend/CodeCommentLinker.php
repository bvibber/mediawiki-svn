<?php

abstract class CodeCommentLinker {

	function __construct( $repo ) {
		global $wgUser;
		$this->mSkin = $wgUser->getSkin();
		$this->mRepo = $repo;
	}

	function link( $text ) {
		# Catch links like http://www.mediawiki.org/wiki/Special:Code/MediaWiki/44245#c829
		# Ended by space or brackets (like those pesky <br /> tags)
		$text = preg_replace_callback( '/(^|[^\w[])(' . wfUrlProtocols() . ')([^ <>]+)(\b)/',
			array( $this, 'generalLink' ), $text );
		$text = preg_replace_callback( '/\br(\d+)\b/',
			array( $this, 'messageRevLink' ), $text );
		$text = preg_replace_callback( '/\bbug #?(\d+)\b/i',
			array( $this, 'messageBugLink' ), $text );
		return $text;
	}
	
	/*
	 * Truncate a valid HTML string with self-contained tags only
	 * Note: tries to fix broken HTML with MWTidy
	 * @TODO: cleanup and move to language.php
	 * @param string $text
	 * @param int $maxLen, (greater than zero)
	 * @param string $ellipsis
	 * @returns string
	 */
	function truncateHtml( $text, $maxLen, $ellipsis = '...' ) {
		global $wgLang;
		if( strlen($text) <= $maxLen ) {
			return $text; // string short enough even *with* HTML
		}
		$text = parser::tidy( $text ); // fix tags
		$displayLen = 0;
		$doTruncate = true; // truncated string plus '...' shorter than original?
		$tagType = 0; // 0-open, 1-close
		$bracketState = 0; // 1-tag start, 2-tag name, 3-tag params, 0-neither
		$entityState = 0; // 0-not entity, 1-entity
		$tag = $ret = $ch = $lastCh = '';
		$openTags = array();
		for( $pos = 0; $pos < strlen($text); $pos++ ) {
			$lastCh = $ch;
			$ch = $text[$pos];
			if ( $ch == '<' ) {
				self::onEndBracket( $tag, $tagType, $lastCh, $openTags ); // for bad HTML
				$entityState = 0; // for bad HTML
				$bracketState = 1; // tag started (checking for backslash)
			} elseif ( $ch == '>' ) {
				self::onEndBracket( $tag, $tagType, $lastCh, $openTags );
				$entityState = 0; // for bad HTML
				$bracketState = 0; // out of brackets
			} elseif ( $bracketState == 1 ) {
				if ( $ch == '/' ) {
					$tagType = 1; // close tag
				} else {
					$tagType = 0; // open tag
					$tag .= $ch;
				}
				$bracketState = 2; // building tag name
			} elseif ( $bracketState == 2 ) {
				if ( $ch != ' ' ) {
					$tag .= $ch;
				} else {
					$bracketState = 3; // name found (e.g. "<a href=...")
				}
			} elseif ( $bracketState == 0 ) {
				if ( $entityState ) {
					if ( $ch == ';' ) {
						$entityState = 0;
						$displayLen++; // entity is one char
					}
				} elseif ( $ch == '&' ) {
					$entityState = 1; // entity found, (e.g. "&nbsp;")
				} else {
					$displayLen++; // not in brackets
				}
			}
			$ret .= $ch;
			if( !$doTruncate ) continue;
			# Truncate if not in the middle of a bracket/entity...
			if ( $bracketState == 0 && $entityState == 0 && $displayLen >= $maxLen ) {
				$left = substr( $text, $pos + 1 ); // remaining string
				$left = StringUtils::delimiterReplace( '<', '>', '', $left ); // rm tags
				$left = StringUtils::delimiterReplace( '&', ';', '', $left ); // rm entities
				$doTruncate = ( strlen($left) > strlen($ellipsis) );
				if ( $doTruncate ) {
					# Hack: go one char over so truncate() will handle multi-byte chars
					$ret = $wgLang->truncate( $ret . 'x', strlen($ret), '' ) . $ellipsis;
					break;
				}
			}
		}
		self::onEndBracket( $tag, $lastCh, $tagType, $openTags ); // for bad HTML
		while ( count( $openTags ) > 0 ) {
			$ret .= '</' . array_pop($openTags) . '>'; // close open tags
		}
		return $ret;
	}
	
	protected function onEndBracket( &$tag, $tagType, $lastCh, &$openTags ) {
		$tag = ltrim( $tag );
		if( $tag != '' ) {
			if( $tagType == 0 && $lastCh != '/' ) {
				$openTags[] = $tag; // tag opened (didn't close itself)
			} else if( $tagType == 1 ) {
				if( $openTags && $tag == $openTags[count($openTags)-1] ) {
					array_pop( $openTags ); // tag closed
				}
			}
			$tag = '';
		}
	}

	function generalLink( $arr ) {
		$url = $arr[2] . $arr[3];
		// Re-add the surrounding space/punctuation
		return $arr[1] . $this->makeExternalLink( $url, $url ) . $arr[4];
	}

	function messageBugLink( $arr ) {
		$text = $arr[0];
		$bugNo = intval( $arr[1] );
		$url = $this->mRepo->getBugPath( $bugNo );
		if ( $url ) {
			return $this->makeExternalLink( $url, $text );
		} else {
			return $text;
		}
	}

	function messageRevLink( $matches ) {
		$text = $matches[0];
		$rev = intval( $matches[1] );

		$repo = $this->mRepo->getName();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );

		return $this->makeInternalLink( $title, $text );
	}

	abstract function makeExternalLink( $url, $text );

	abstract function makeInternalLink( $title, $text );
}

class CodeCommentLinkerHtml extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return $this->mSkin->makeExternalLink( $url, $text );
	}

	function makeInternalLink( $title, $text ) {
		return $this->mSkin->link( $title, $text );
	}
}

class CodeCommentLinkerWiki extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return "[$url $text]";
	}

	function makeInternalLink( $title, $text ) {
		return "[[" . $title->getPrefixedText() . "|$text]]";
	}
}
