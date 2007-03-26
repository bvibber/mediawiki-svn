<?php

#(c) Joerg Baach, Aaron Schulz, 2007 GPL

global $IP;
require_once( "$IP/includes/LogPage.php" );
require_once( "$IP/includes/SpecialLog.php" );

class Revisionreview extends SpecialPage
{
	/* 50MB allows fixing those huge pages */
	const MAX_INCLUDE_SIZE = 50000000;

    function Revisionreview() {
        SpecialPage::SpecialPage('Revisionreview', 'review');
    }

    function execute( $par ) {
        global $wgRequest, $wgOut;

		$this->setHeaders();
		// Our target page
		$this->target = $wgRequest->getText( 'target' );
		// Revision ID
		$this->oldid = $wgRequest->getIntOrNull( 'oldid' );
		// Log comment
		$this->comment = $wgRequest->getText( 'wpReason' );
		// Additional notes
		$this->notes = $wgRequest->getIntOrNull('wpNotes');
		// Get our accuracy/quality array
		$this->dimensions = array();
        $this->dimensions['accuracy'] = $wgRequest->getIntOrNull('accuracy');
        $this->dimensions['depth'] = $wgRequest->getIntOrNull('depth');
        $this->dimensions['style'] = $wgRequest->getIntOrNull('style');
		// Must be a valid page
		// No non-content pages
		$this->page = Title::newFromUrl( $this->target );
		if( is_null($this->page) || is_null($this->oldid) || !$this->page->isContentPage() ) {
			$wgOut->showErrorPage( $this->page, 'notargettitle', 'notargettext' );
			return;
		}
		if( $wgRequest->wasPosted() ) {
			$this->submit( $wgRequest );
		} else {
			$this->showRevision( $wgRequest );
		}
	}
	
	/**
	 * @param webrequest $request
	 */
	function showRevision( $request ) {
		global $wgOut, $wgUser, $wgTitle;
		
		$wgOut->addWikiText( wfMsgExt( 'revreview-selected', array('parsemag'), $this->page->getPrefixedText() ) );
		
		$this->skin = $wgUser->getSkin();
		$rev = Revision::newFromTitle( $this->page, $this->oldid );
		// Check if rev exists
		if( !isset( $rev ) ) {
			$wgOut->showErrorPage( 'internalerror', 'notargettitle', 'notargettext' );
			return;
		}
		// Do not mess with deleted revisions
		if ( $rev->mDeleted ) {
			$wgOut->showErrorPage( 'internalerror', 'badarticleerror' ); 
			return;
		}	
		$wgOut->addHtml( "<ul>" );
		$wgOut->addHtml( $this->historyLine( $rev ) );
		$wgOut->addHtml( "</ul>" );
		
		$wgOut->addWikiText( wfMsgHtml( 'revreview-text' ) );
		
		$this->accRadios = array(
			array( 'revreview-acc-0', 'wpAcc1', 0 ),
			array( 'revreview-acc-1', 'wpAcc2', 1 ),
			array( 'revreview-acc-2', 'wpAcc3', 2 ),
			array( 'revreview-acc-3', 'wpAcc4', 3 ) );
		$this->depthRadios = array(
			array( 'revreview-depth-0', 'wpDepth1', 0 ),
			array( 'revreview-depth-1', 'wpDepth2', 1 ),
			array( 'revreview-depth-2', 'wpDepth3', 2 ),
			array( 'revreview-depth-3', 'wpDepth4', 3 ) );
		$this->styleRadios = array(
			array( 'revreview-style-0', 'wpStyle1', 0 ),
			array( 'revreview-style-1', 'wpStyle2', 1 ),
			array( 'revreview-style-2', 'wpStyle3', 2 ),
			array( 'revreview-style-3', 'wpStyle4', 3 ) );
		$items = array(
			wfInputLabel( wfMsgHtml( 'revreview-log' ), 'wpReason', 'wpReason', 60 ),
			wfSubmitButton( wfMsgHtml( 'revreview-submit' ) ) );
		$hidden = array(
			wfHidden( 'wpEditToken', $wgUser->editToken() ),
			wfHidden( 'target', $this->page->getPrefixedText() ),
			wfHidden( 'oldid', $this->oldid ) );
		
		$action = $wgTitle->escapeLocalUrl( 'action=submit' );		
		$form = "<form name='revisionreview' action='$action' method='post'>";
		$form .= '<fieldset><legend>' . wfMsgHtml( 'revreview-legend' ) . '</legend><table><tr>';
		$form .= '<td><strong>' . wfMsgHtml( 'revreview-accuracy' ) . '</strong></td>';
		$form .= '<td width=\'25\'></td><td><strong>' . wfMsgHtml( 'revreview-depth' ) . '</strong></td>';
		$form .= '<td width=\'25\'></td><td><strong>' . wfMsgHtml( 'revreview-style' ) . '</strong></td>';
		$form .= '</tr><tr><td>';
		foreach( $this->accRadios as $item ) {
			list( $message, $name, $field ) = $item;
			$form .= "<div>" .
				Xml::radio( 'accuracy', $field, ($field==$this->dimensions['accuracy']) ) . ' ' . wfMsgHtml($message) .
				"</div>\n";
		}
		$form .= '<td width=\'25\'></td></td><td>';
		foreach( $this->depthRadios as $item ) {
			list( $message, $name, $field ) = $item;
			$form .= "<div>" .
				Xml::radio( 'depth', $field, ($field==$this->dimensions['depth']) ) . ' ' . wfMsgHtml($message) .
				"</div>\n";
		}
		$form .= '<td width=\'25\'></td></td><td>';
		foreach( $this->styleRadios as $item ) {
			list( $message, $name, $field ) = $item;
			$form .= "<div>" .
				Xml::radio( 'style', $field, ($field==$this->dimensions['style']) ) . ' ' . wfMsgHtml($message) .
				"</div>\n";
		}
		$form .= '</td></tr></table></fieldset>';
		
		$images = $this->findLocalImages( $this->expandText( $rev->getText() ) );
		if ( $images ) {
			$form .= wfMsg('revreview-images') . "\n";
			$form .= "<ul>";
			$imglist = '';
			foreach ( $images as $image ) {
				$imglist .= "<li>" . $this->skin->makeKnownLink( $image ) . "</li>\n";
			}
			$form .= $imglist;
			$form .= "</ul>\n";
		}
		
		$form .= "<fieldset><legend>" . wfMsgHtml( 'revreview-notes' ) . "</legend>" .
			"<textarea tabindex='1' name='wpNotes' id='wpNotes' rows='3' cols='80' style='width:100%'></textarea>" .	
			"</fieldset>";
		
		foreach( $items as $item ) {
			$form .= '<p>' . $item . '</p>';
		}
		
		foreach( $hidden as $item ) {
			$form .= $item;
		}
		$form .= '</form>';
		$wgOut->addHtml( $form );
	}
	
	/**
	 * @param Revision $rev
	 * @returns string
	 */
	function historyLine( $rev ) {
		global $wgContLang;
		$date = $wgContLang->timeanddate( $rev->getTimestamp() );
		
		$del = '';
		$difflink = '(' . $this->skin->makeKnownLinkObj( $this->page, wfMsgHtml('diff'), 
		'&diff=' . $rev->getId() . '&oldid=prev' ) . ')';
		
		$revlink = $this->skin->makeLinkObj( $this->page, $date, 'oldid=' . $rev->getId() );
	
		if ( $rev->isDeleted(Revision::DELETED_TEXT) ) {
			$revlink = '<span class="history-deleted">'.$revlink.'</span>';
			$del = ' <tt>' . wfMsgHtml( 'deletedrev' ) . '</tt>';
			if ( !$rev->userCan(Revision::DELETED_TEXT) ) {
				$revlink = '<span class="history-deleted">'.$date.'</span>';
			}
		}
		
		return
			"<li> $difflink $revlink " . $this->skin->revUserLink( $rev ) . " " . $this->skin->revComment( $rev ) . "$del</li>";
	}
	
	function submit( $request ) {
		global $wgOut, $wgTitle;

		$rev = Revision::newFromTitle( $this->page, $this->oldid );
		// Do not mess with deleted revisions
		if ( $rev->mDeleted ) {
			$wgOut->showErrorPage( 'internalerror', 'badarticleerror' ); 
			return;
		}	
		$approved = false;
		# If all values are set to zero, this has been unnapproved
		foreach( $this->dimensions as $quality => $value ) {
			if( $value ) $approved = true;
		}
		$success = ( $approved ) ? $this->approveRevision( $rev ) : $this->unapproveRevision( $rev );
		// Return to our page			
		if ( $success ) {
        	$wgOut->redirect( $this->page->escapeLocalUrl() );
		}
	}

	/**
	 * @param Revision $rev
	 * Adds or updates the flagged revision table for this page/id set
	 */
	function approveRevision( $rev=NULL ) {
		global $wgUser;
		
		if( is_null($rev) ) return false;

		wfProfileIn( __METHOD__ );
	
        $db = wfGetDB( DB_MASTER );
        $user = $wgUser->getId();
        $timestamp = wfTimestampNow();
        
        $cache_text = $this->expandText( $rev->getText() );
		// Add or update entry for this revision
 		$set = array(
 			'fr_page_id' => $rev->getPage(),
			'fr_rev_id' => $rev->getId(),
			'fr_cache' => $cache_text,
			'fr_acc' => $this->dimensions['accuracy'],
			'fr_dep' => $this->dimensions['depth'],
			'fr_sty' => $this->dimensions['style'],
			'fr_user' => $user,
			'fr_timestamp' => $timestamp,
			'fr_comment'=> $this->notes
		);
		$db->replace( 'flaggedrevs', array( 'fr_page_id', 'fr_rev_id' ), $set, __METHOD__ );
		// Update the article review log
		$this->updateLog( $this->page, $this->dimensions, $this->comment, $this->oldid, true );
		// Clone images to stable dir
		$images = $this->findLocalImages( $cache_text );
		$copies = $this->makeStableImages( $images );
		// Update stable image table
		$this->insertStableImages( $copies );
		// Clear cache...
		$this->updatePage( $this->page );
        return true;
    }

	/**
	 * @param Revision $rev
	 * Removes flagged revision data for this page/id set
	 */  
	function unapproveRevision( $rev=NULL ) {
		global $wgUser;
	
		if( is_null($rev) ) return false;
        $db = wfGetDB( DB_MASTER );
        $user = $wgUser->getId();
        $timestamp = wfTimestampNow();

		$cache_text = $this->expandText( $rev->getText() );
		$db->delete( 'flaggedrevs', array( 'fr_rev_id' => $rev->getId ) );
		// Update the article review log
		$this->updateLog( $this->page, $this->dimensions, $this->comment, $this->oldid, false );
		// Delete stable images if needed
		$images = $this->findLocalImages( $cache_text );
		$copies = $this->deleteStableImages( $images );
		// Update stable image table
		$this->removeStableImages( $copies );
		// Clear cache...
		$this->updatePage( $this->page );
        return true;
    }

	/**
	 * Touch the page's cache invalidation timestamp; this forces cached
	 * history views to refresh, so any newly hidden or shown fields will
	 * update properly.
	 * @param Title $title
	 */
	function updatePage( $title ) {
		$title->invalidateCache();
	}
  
	/**
	 * @param string $text
	 * @returns string
	 * All included pages are expanded out to keep this text frozen
	 */
    function expandText( $text ) {
    	global $wgParser, $wgTitle;
    	// Do not treat this as paring an article on normal view
    	// enter the title object as wgTitle
    	$options = new ParserOptions;
		$options->setRemoveComments( true );
		$options->setMaxIncludeSize( self::MAX_INCLUDE_SIZE );
		$output = $wgParser->preprocess( $text, $wgTitle, $options );
		return $output;
	}

	/**
	* Get all local image files and generate an array of them
	* @param string $s, wikitext
	* $output array, string titles
	*/	
    function findLocalImages( $s ) {
    	$fname = 'findLocalImages';
    	$imagelist = array();
    	
    	if ( !$s || !strval($s) ) return $imagelist;

		static $tc = FALSE;
		# the % is needed to support urlencoded titles as well
		if ( !$tc ) { $tc = Title::legalChars() . '#%'; }
		
		# split the entire text string on occurences of [[
		$a = explode( '[[', $s );
		
		# Ignore things that start with colons, they are image links, not images
		$e1_img = "/^([:{$tc}]+)(.+)$/sD";
		# Loop for each link
		for ($k = 0; isset( $a[$k] ); $k++) {
			if ( preg_match( $e1_img, $a[$k], $m ) ) { 
				# page with normal text or alt of form x or ns:x
				$nt = Title::newFromText( $m[1] );
				$ns = $nt->getNamespace();
				# add if this is an image
				if ( $ns == NS_IMAGE )
					$imagelist[] = $nt->getPrefixedText();
			}
		}
		return $imagelist;
    }

	/**
	* Showtime! Copy all used images to a stable directory
	* Get all local image files and generate an array of them
	* The future FileStore migration might effect this, not sure...
	* @param array $imagelist, list of string names
	* $output array, list of string names of images sucessfully cloned
	*/	   
    function makeStableImages( $imagelist ) {
    	global $wgUploadDirectory, $wgSharedUploadDirectory;
    	// All stable images are local, not shared
    	// Otherwise, we could have some nasty cross language/wiki conflicts
    	$stableDir = "$wgUploadDirectory/stable";
    	// Copy images to stable dir
    	$usedimages = array();
    	// We need valid input
    	if ( !is_array($imagelist) ) return $usedimages;
    	foreach ( $imagelist as $name ) {
    		// We want a clean and consistant title entry
			$nt = Title::newFromText( $name );
			if ( is_null($nt) ) {
			// If this title somehow doesn't work, ignore it
			// this shouldn't happen...
				continue;
			}
			$name = $nt->getDBkey();
    		$hash = wfGetHashPath($name);
    		$path = $wgUploadDirectory . $hash;
    		$sharedpath = $wgSharedUploadDirectory . $hash;
    		// Try local repository
    		if( is_dir($path) ) {
    			if( is_file("{$path}{$name}") ) {
    				// Check if our stable dir exists
    				// Make it if it doesn't
    				if( !is_dir($stableDir . $hash) ) {
    					wfMkdirParents($stableDir . $hash);
    				}
    				// Copy over our files
    				copy("{$path}{$name}","{$stableDir}{$hash}{$name}");
    				$usedimages[] = $name;
    			}
    		} // Try shared repository
			else if( is_dir($sharedpath) ) {
    			if( is_file("{$sharedpath}{$name}") ) {
    				// Check if our stable dir exists
    				// Make it if it doesn't
    				if( !is_dir($stableDir . $hash) ) {
    					wfMkdirParents($stableDir . $hash);
    				}
    				copy("{$path}{$name}","{$stableDir}{$hash}{$name}");
    				$usedimages[] = $name;
    			}
    		}
    	}
    	return $usedimages;
    }
    
	/**
	* Delete an a list of stable image files
	* @param array $imagelist, list of string names
	* $output array, list of string names of images to be deleted
	*/	   
    function deleteStableImages( $imagelist ) {
    	global $wgUploadDirectory, $wgSharedUploadDirectory;
    	// All stable images are local, not shared
    	// Otherwise, we could have some nasty cross language/wiki conflicts
    	$stableDir = "$wgUploadDirectory/stable";
    	// Copy images to stable dir
    	$deletedimages = array();
    	// We need valid input
    	if ( !is_array($imagelist) ) return $usedimages;
    	foreach ( $imagelist as $name ) {
    	    // We want a clean and consistant title entry
			$nt = Title::newFromText( $name );
			if ( is_null($nt) ) {
			// If this title somehow doesn't work, ignore it
			// this shouldn't happen...
				continue;
			}
			$name = $nt->getDBkey();
    		$hash = wfGetHashPath($name);
    		$path = $stableDir . $hash;
    		// Try the stable repository
    		if ( is_dir($path) ) {
    			if ( is_file("{$path}{$name}") ) {
    				// Delete!
    				delete("{$path}{$name}");
    				$deletedimages[] = $name;
    			}
    		}
    	}
    	return $deletedimages;
    }

	/**
	* Update the stable image usage table
	* Add some images if not redundant
	* @param array $imagelist, list of string names
	* $output bool, on succeed
	*/	     
    function insertStableImages( $revid, $imagelist ) {
		wfProfileIn( __METHOD__ );
		
		if ( !is_array($imagelist) ) return false;
		
        $db = wfGetDB( DB_MASTER );
        foreach( $imagelist as $name ) {
			// We want a clean and consistant title entry
			$nt = Title::newFromText( $name );
			if ( is_null($nt) ) {
			// If this title somehow doesn't work, ignore it
			// this shouldn't happen...
				continue;
			}
			$imagename = $nt->getDBkey();
			// Add image and the revision that uses it
 			$set = array(
				'fi_rev_id' => $revid,
				'fr_name' => $imagename,
			);
			// Add entries or replace any that have the same rev_id
			$db->replace( 'flaggedimages', array( 'fi_rev_id', 'fr_name' ), $set, __METHOD__ );	
		}
		return true;	
    }
    
	/**
	* Update the stable image usage table
	* Clean out unused images if needed
	* @param array $imagelist, list of string names
	* $output bool, on succeed
	*/	     
    function removeStableImages( $revid, $imagelist ) {
		wfProfileIn( __METHOD__ );
		
		if ( !is_array($imagelist) ) return false;
		$unusedimages = array();
        $db = wfGetDB( DB_MASTER );
        foreach( $imagelist as $name ) {
			// We want a clean and consistant title entry
			$nt = Title::newFromText( $name );
			if ( is_null($nt) ) {
			// If this title somehow doesn't work, ignore it
			// this shouldn't happen...
				continue;
			}
			$imagename = $nt->getDBkey();
 			$where = array(
				'fi_rev_id' => $revid,
				'fr_name' => $imagename,
			);
			// See how many revisions use this image total...
			$result = $db->select( 'flaggedimages', array('fi_id'), array( 'fi_name' => $imagename ) );
			// If only one, then delete the image
			// Since its about to be remove from that one
			if ( $db->numRows($result)==1 ) {
				$unusedimages[] = $imagename;
			}
			// Clear out this revision's entry
			$db->delete( 'flaggedimages', $where );
		}
		$this->deleteStableImages( $unusedimages );
		return true;
    }

	/**
	 * Record a log entry on the action
	 * @param Title $title
	 * @param array $dimensions
	 * @param string $comment
	 * @param int $revid
	 * @param bool $approve
	 */	
	function updateLog( $title, $dimensions, $comment, $oldid, $approve ) {
		$log = new LogPage( 'review' );
		// ID, accuracy, depth, style
		$params = array();
		$params[] = $oldid;
		foreach( $dimensions as $quality => $level ) {
			$params[] = $level;
		}
		// Append comment with action
		$action = wfMsgExt('review-logaction', array('parsemag'), $oldid );
		$comment = ($comment) ? "$action: $comment" : $action; 
			
		if ( $approve ) {
			$log->addEntry( 'approve', $title, $comment, $params );
		} else {
			$log->addEntry( 'unapprove', $title, $comment, $params );
		}
	}
}
?>
