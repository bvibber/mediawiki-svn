<?php

class IndexFunction {

	// Utility function to get the target pageid for a possible index-title
	static function getIndexTarget( Title $title ) {
		$ns = $title->getNamespace();
		$t = $title->getDBkey();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'indexes', 'in_from', 
			array( 'in_namespace' => $ns, 'in_title' => $t ),
			__METHOD__
		);
		return $res;
	}
	
	// Makes "Go" searches for an index title go directly to their target
	static function redirectSearch( $term, &$title ) {
		$title = Title::newFromText( $term );
		if ( is_null($title) ) {
			return true;
		}
		$res = self::getIndexTarget( $title );
		if ( $res->numRows() == 0 ) {
			return true;
		} elseif ( $res->numRows() > 1 ) {
			global $wgOut;
			$title = SpecialPage::getTitleFor( 'Index', $title->getPrefixedText() );
			$wgOut->redirect( $title->getLocalURL() );
			return true;
		}
		$res = $res->fetchRow();
		$title = Title::newFromId( $res );
		return false;
	}

	// Make indexes work like redirects
	static function doRedirect( &$title, &$request,& $ignoreRedirect, &$target, &$article ) {
		if ( $article->exists() ) {
			return true;
		}
		$res = self::getIndexTarget( $title );
		if ( $res->numRows() == 0 ) {
			return true;
		} elseif ( $res->numRows() > 1 ) {
			global $wgOut;
			$t = SpecialPage::getTitleFor( 'Index', $title->getPrefixedText() );
			$wgOut->redirect( $t->getLocalURL() );
			return true;
		} else {
			$res = $res->fetchRow();
			$redir = Title::newFromID( $res );
		}
		$target = $redir;
		$article->mIsRedirect = true;
		$ignoreRedirect = false;
		return true;
	}
	
	// Turn links to indexes into blue links
	static function blueLinkIndexes( $skin, $target, $options, &$text, &$attribs, &$ret ) {
		if ( in_array( 'known', $options ) ) {
			return true;
		}
		$res = self::getIndexTarget( $target );
		if ( $res->numRows() == 0 ) {
			return true;
		}
		$attribs['class'] = str_replace( 'new', 'mw-index', $attribs['class'] );
		$attribs['href'] = $target->getLinkUrl();
		$attribs['title'] = $target->getEscapedText();
		return true;
	}

	// Register the function name
	static function addIndexFunction( &$magicWords, $langCode ) {
		$magicWords['index-func'] = array( 0, 'index' );
		return true;
	}
	 
	// Function called to render the parser function
	// Output is an empty string unless there are errors
	static function indexRender( &$parser ) {
		if ( !isset($parser->mOutput->mIndexes) ) {
			$parser->mOutput->mIndexes = array();
		}		
		wfLoadExtensionMessages( 'IndexFunction' );
		static $indexCount = 0;
		static $indexes = array();
		$args = func_get_args();
		unset( $args[0] );
		if ( $parser->mOptions->getIsPreview() ) {
			# This is kind of hacky, but it seems that we only
			# know if its a preview during parse, not when its
			# done, which is when it matters for this
			$parser->mOutput->setProperty( 'preview', 1 );
		}
		$errors = array();
		$pageid = $parser->mTitle->getArticleID();
		foreach ( $args as $name ) {
			$t = Title::newFromText( $name );
			if( is_null( $t ) ) {
				$errors[] = wfMsg( 'indexfunc-badtitle', $name );
				continue;
			}
			$ns =  $t->getNamespace();
			$dbkey = $t->getDBkey();
			$entry = array( $ns, $dbkey );
			if ( in_array( $entry, $indexes ) ) {
				continue;
			} 
			if ( $t->exists() ) {
				$errors[] = wfMsg( 'indexfunc-index-exists', $name );
				continue;
			}
			$indexCount++;
			$parser->mOutput->mIndexes[$indexCount] =  $entry;
		}
		if ( !$errors ) {
			return '';
		}
		$out = Xml::openElement( 'ul', array( 'class'=>'error' ) );
		foreach( $errors as $e ) {
			$out .= Xml::element( 'li', null, $e );
		}
		$out .= Xml::closeElement( 'ul' );
		return $out;
	}

	// Called after parse, updates the index table
	static function doIndexes( &$out, $parseroutput ) {
		global $wgTitle;
		if ( !isset($parseroutput->mIndexes) ) {
			return true;
		}
		if ( $parseroutput->getProperty( 'preview' ) ) {
			return true;
		}
		$pageid = $wgTitle->getArticleID();
		$dbw = wfGetDB( DB_MASTER );
		$res = $dbw->select( 'indexes', 
			array( 'in_namespace', 'in_title' ),
			array( 'in_from' => $pageid ),
			__METHOD__
		);
		$current = array();
		foreach( $res as $row ) {
			$current[] = array( $row->in_namespace, $row->in_title );
		}
		$toAdd = wfArrayDiff2( $parseroutput->mIndexes, $current );
		$toRem = wfArrayDiff2( $current, $parseroutput->mIndexes );
		if ( true ) {
			$dbw->begin( __METHOD__ );
			if ( $toRem ) {
				$delCond = "in_from = $pageid AND (";
				$parts = array(); 
				# Looking at Database::delete, it seems to turn arrays into AND statements
				# but we need to chain together groups of ANDs with ORs
				foreach ( $toRem as $entry ) {
					$parts[] = "(in_namespace = " . $entry[0] . " AND in_title = " . $dbw->addQuotes($entry[1]) . ")";
				}
				$delCond .= implode( ' OR ', $parts ) . ")";
				$dbw->delete( 'indexes', array($delCond), __METHOD__ );
			}
			if ( $toAdd ) {
				$ins = array();
				foreach ( $toAdd as $entry ) {
					$ins[] = array( 'in_from' => $pageid, 'in_namespace' => $entry[0], 'in_title' => $entry[1] );
				}
				$dbw->insert( 'indexes', $ins, __METHOD__ );
			}
			$dbw->commit( __METHOD__ );
		}
		return true;
	}

	// When deleting a page, delete all rows from the index table that point to it
	static function onDelete( &$article, &$user, $reason, $id ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'indexes', array( 'in_from'=>$id ), __METHOD__ );
		return true;
	}

	// When creating an article, delete its title from the index table
	static function onCreate( &$article, &$user, &$text, &$summary, &$minoredit, &$watchthis, &$sectionanchor, &$flags, &$revision ) {
		$t = $article->mTitle;
		$ns = $t->getNamespace();
		$dbkey = $t->getDBkey();
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete( 'indexes', 
			array( 'in_namespace'=>$ns, 'in_title'=>$dbkey ),
			 __METHOD__ );
		return true;
	}

	// Show a warning when editing an index-title
	static function editWarning( $editpage ) {
		$t = $editpage->mTitle;
		$target = self::getIndexTarget( $t );
		if ( $target->numRows() != 1 ) { # FIXME
			return true;
		}
		$target = $target->fetchRow();
		$page = Title::newFromID( $target['in_from'] );
		wfLoadExtensionMessages( 'IndexFunction' );
		$warn = wfMsgExt( 'indexfunc-editwarn', array( 'parse' ), $page->getPrefixedText() );
		$editpage->editFormTextBeforeContent .= "<span class='error'>$warn</span>";
		return true;
	}
}

