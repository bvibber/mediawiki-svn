<?
#(c) Joerg Baach, Aaron Schulz 2007 GPL
/*

Possible Hooks
--------------

'BeforePageDisplay': Called just before outputting a page (all kinds of,
		     articles, special, history, preview, diff, edit, ...)
		     Can be used to set custom CSS/JS
$out: OutputPage object


'OutputPageBeforeHTML': a page has been processed by the parser and
the resulting HTML is about to be displayed.  
$parserOutput: the parserOutput (object) that corresponds to the page 
$text: the text that will be displayed, in HTML (string)

*/

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "FlaggedRevs extension\n";
    exit( 1 );
}

$wgExtensionFunctions[] = 'efLoadReviewMessages';

# Internationilization
function efLoadReviewMessages() {
	global $wgMessageCache, $RevisionreviewMessages;
	require( dirname( __FILE__ ) . '/FlaggedRevsPage.i18n.php' );
	foreach ( $RevisionreviewMessages as $lang => $langMessages ) {
		$wgMessageCache->addMessages( $langMessages, $lang );
	}
}

# Revision tagging can slow development...
# For example, the main user base may become complacent,
# treating flagged pages as "done",
# or just be too damn lazy to always click "current".
# We may just want non-user visitors to see reviewd pages by default.
$wgFlaggedRevsAnonOnly = true;

$wgAvailableRights[] = 'review';
# Define our reviewer class
$wgGroupPermissions['reviewer']['rollback']    = true;
$wgGroupPermissions['reviewer']['patrol']      = true;
$wgGroupPermissions['reviewer']['review']      = true;
# Add review log
$wgLogTypes[] = 'review';
$wgLogNames['review'] = 'review-logpage';
$wgLogHeaders['review'] = 'review-logpagetext';
$wgLogActions['review/approve']  = 'review-logentrygrant';
$wgLogActions['review/unapprove'] = 'review-logentryrevoke';

class FlaggedRevs {
	/* 50MB allows fixing those huge pages */
	const MAX_INCLUDE_SIZE = 50000000;

	function __construct() {
    	$this->dimensions = array( 'accuracy' => array( 0=>'acc-0',
                                                1=>'acc-1',
                                                2=>'acc-2',
                                                3=>'acc-3'),
									'depth'   => array( 0=>'depth-0',
                                            	1=>'depth-1',
                                             	2=>'depth-2',
											 	3=>'depth-3'),
									'style'   => array( 0=>'style-0',
                                            	1=>'style-1',
                                             	2=>'style-2',
											 	3=>'style-3') );
	}
	
    function pageOverride() {
    	global $wgFlaggedRevsAnonOnly, $wgUser;
    	return !( $wgFlaggedRevsAnonOnly && !$wgUser->isAnon() );
    }
	
    function getFlaggedRevision( $rev_id ) {
 		$db = wfGetDB( DB_SLAVE );
 		// select a row, this should be unique
		$result = $db->select( 'flaggedrevs', array('*'), array('fr_rev_id' => $rev_id) );
		if ( $row = $db->fetchObject($result) ) {
			return $row;
		}
        return NULL;
    }
    
    function getFlaggedRevText( $rev_id ) {
 		$db = wfGetDB( DB_SLAVE );
 		// select a row, this should be unique
		$result = $db->select( 'flaggedcache', array('fc_cache'), array('fc_rev_id' => $rev_id) );
		if ( $row = $db->fetchObject($result) ) {
			return $row->fc_cache;
		}
        return NULL;
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

    function getFlagsForRevision( $rev_id ) {
    	// Set default blank flags
    	$flags = array( 'accuracy' => 0, 'depth' => 0, 'style' => 0 );

 		$db = wfGetDB( DB_SLAVE );
 		// select a row, this should be unique
		$result = $db->select( 'flaggedrevs', array('*'), array('fr_rev_id' => $rev_id) );
		if ( $row = $db->fetchObject($result) ) {
			$flags = array( 'accuracy' => $row->fr_acc, 'depth' => $row->fr_dep, 'style' => $row->fr_sty );
		}
        return $flags;
    }

    function getLatestFlaggedRev( $page_id ) {   
    	# Call getReviewedRevs(),
    	# this will prune things out if needed
		if ( $row = $this->getReviewedRevs($page_id) ) {
			return $row[0];
		}
        return NULL;
    }
    
    function getReviewedRevs( $page_id ) {   
		wfProfileIn( __METHOD__ );
		  
        $db = wfGetDB( DB_SLAVE ); 
        $rows = array();
        $result = $db->select(
			array('flaggedrevs', 'revision'),
			array('*'),
			array( 'fr_page_id' => $page_id, 'rev_id = fr_rev_id' ),
			__METHOD__ ,
			array('ORDER BY' => 'fr_rev_id DESC') );
		// Sorted from highest to lowest, so just take the first one if any
        while ( $row = $db->fetchObject( $result ) ) {
            // Purge deleted revs from flaggedrev table
            if ( $row->rev_deleted ) {
        		$cache_text = $this->expandText( $this->getFlaggedRevText( $row->fr_rev_id ) );
				$db->delete( 'flaggedrevs', array( 'fr_rev_id' => $row->rev_id ) );
				// Delete stable images if needed
				list($images,$thumbs) = $this->findLocalImages( $cache_text );
				$copies = $this->deleteStableImages( $images );
				// Update stable image table
				$this->removeStableImages( $row->rev_id, $copies );
				$this->deleteStableThumbnails( $thumbs );
				// Clear cache...
				$this->updatePage( Title::newFromID($page_id) );
            } else {
            	$rows[] = $row;
            }
        }
        return $rows;
    }
    
    function getUnreviewedRevCount( $page_id, $from_rev ) {   
		wfProfileIn( __METHOD__ );
		  
        $db = wfGetDB( DB_SLAVE );  
        $result = $db->select(
			array('revision'),
			array('rev_page'),
			array( 'rev_page' => $page_id, "rev_id > $from_rev" ),
			__METHOD__ ,
			array('ORDER BY' => 'rev_id DESC') );
		// Return count of revisions
        return $db->numRows($result);
    }

    function setPageContent( &$out ) {
        global $wgArticle, $wgRequest, $wgTitle, $wgOut, $action;
        // Only trigger on article view, not for protect/delete/hist
        // Talk pages cannot be validated
        if( !$wgArticle || !$out->isArticle() || !$wgTitle->isContentPage() || $action !='view' )
            return;
        // Find out revision id
        if( $wgArticle->mRevision )
        	$revid = $wgArticle->mRevision->mId;
        else
        	$revid = $wgArticle->getLatest();
		// Grab the ratings for this revision if any
        if ( !$revid ) return;
        $visible_id = $revid;
        
		// Set new body html text as that of now
		$flaghtml = ''; $newbodytext = $out->mBodytext;
		// Check the newest stable version
		$top_frev = $this->getLatestFlaggedRev( $wgArticle->getId() );
		if ( $wgRequest->getVal('diff') ) {
		// Do not clutter up diffs any further...
		} else if ( $top_frev ) {
			global $wgParser, $wgLang;
			// Parse the timestamp
			$time = $wgLang->timeanddate( wfTimestamp(TS_MW, $top_frev->fr_timestamp), true );
			// Grab the flags
			$flags = $this->getFlagsForRevision( $top_frev->fr_rev_id );
			# Looking at some specific old rev or if flagged revs override only for anons
			if( $wgRequest->getVal('oldid') || !$this->pageOverride() ) {
				if ( $revid==$top_frev->rev_id ) {
					$flaghtml = wfMsgExt('revreview-isnewest', array('parse'),$time);
				} else {
					# Our compare link should have a reasonable time-ordered old->new combination
					$oldid = ($revid > $top_frev->fr_rev_id) ? $top_frev->fr_rev_id : $revid;
					$diff = ($revid > $top_frev->fr_rev_id) ? $revid : $top_frev->fr_rev_id;
					$flaghtml = wfMsgExt('revreview-newest', array('parse'), $top_frev->fr_rev_id, $oldid, $diff, $time );
				}
            } # Viewing the page normally: override the page
			else {
				global $wgUser, $wgUploadDirectory, $wgUseSharedUploads, $wgUploadPath;
        		# We will be looking at the reviewed revision...
        		$visible_id = $top_frev->fr_rev_id;
        		$revs_since = $this->getUnreviewedRevCount( $wgArticle->getId(), $visible_id );
        		$flaghtml = wfMsgExt('revreview-replaced', array('parse'), $visible_id, $wgArticle->getLatest(), $revs_since, $time );		
				
				# Hack...temporarily change image directories
				# There is no nice option to set this for each parse
				# This lets the parser know where to look...
				$uploadPath = $wgUploadPath;
				$uploadDir = $wgUploadDirectory;
				$useSharedUploads = $wgUseSharedUploads;
				# Stable thumbnails need to have the right path
				$wgUploadPath = ($wgUploadPath) ? "{$uploadPath}/stable" : false;
				# Create <img> tags with the right url
				$wgUploadDirectory = "{$uploadDir}/stable";
				# Stable images are never stored at commons
				$wgUseSharedUploads = false;
				
				$parse_ops = ParserOptions::newFromUser($wgUser);
				# Don't show section-edit links
				# They can be old and misleading
				$parse_ops->setEditSection( false );
				# Parse the new body, wikitext -> html
				$text = $this->getFlaggedRevText( $top_frev->fr_rev_id );
       			$newbody = $wgParser->parse( $text, $wgTitle, $parse_ops );
       			$newbodytext = $newbody->getText();
       			
       			# Reset image directories
       			$wgUploadPath = $uploadPath;
       			$wgUploadDirectory = $uploadDir;
       			$wgUseSharedUploads = $useSharedUploads;
            }
            // Construct some tagging
            $flaghtml .= "<table align='center' cellspadding=\'0\'><tr>";
			foreach ( $this->dimensions as $quality => $value ) {
				$value = wfMsgHtml('revreview-' . $this->dimensions[$quality][$flags[$quality]]);
				$flaghtml .= "<td>&nbsp;<strong>" . wfMsgHtml("revreview-$quality") . "</strong>: $value&nbsp;</td>\n";    
            }
            $flaghtml .= '</tr></table>';
            // Should use CSS?
            $flaghtml = "<small>$flaghtml</small>";
            
            // Set the new body HTML! and place the tag on top
            $out->mBodytext = '<div class="mw-warning plainlinks">' . $flaghtml . '</div>' . $newbodytext;
        } else {
        	$flaghtml = wfMsgExt('revreview-noflagged', array('parse'));
        	$out->mBodytext = '<div class="mw-warning plainlinks">' . $flaghtml . '</div>' . $out->mBodytext;
        }
		// Override our reference ID for permalink/citation hooks
		$wgArticle->mRevision = Revision::newFromId( $visible_id );
        // Show review links for the VISIBLE revision
        // We cannot review deleted revisions
        if ( is_object($wgArticle->mRevision) && $wgArticle->mRevision->mDeleted ) return;
        // Add quick review links IF we did not override, otherwise, they might
        // review a revision that parses out newer templates/images than what they say
        // Note: overrides are never done when viewing with "oldid="
        if ( $visible_id==$revid || !$this->pageOverride() )
			$this->addQuickReview( $visible_id, false, $out );
    }
    
    function addToEditView( &$editform ) {
        global $wgRequest, $wgTitle, $wgOut;
        // Talk pages cannot be validated
        if ( !$editform->mArticle || !$wgTitle->isContentPage() )
            return;
        // Find out revision id
        if ( $editform->mArticle->mRevision )
        	$revid = $editform->mArticle->mRevision->mId;
        else
        	$revid = $editform->mArticle->getLatest();
		// Grab the ratings for this revision if any
        if ( !$revid ) return;
        
		// Set new body html text as that of now
		$flaghtml = '';
		// Check the newest stable version
		$top_frev = $this->getLatestFlaggedRev( $editform->mArticle->getId() );
		if ( is_object($top_frev) ) {
			global $wgParser, $wgLang;		
			$time = $wgLang->timeanddate( wfTimestamp(TS_MW, $top_frev->fr_timestamp), true );
			$flags = $this->getFlagsForRevision( $top_frev->fr_rev_id );
			# Looking at some specific old rev
			if( $wgRequest->getVal('oldid') ) {
				if ( $revid==$top_frev->rev_id ) {
					$flaghtml = wfMsgExt('revreview-isnewest', array('parse'),$time);
				} else {
					# Our compare link should have a reasonable time-ordered old->new combination
					$oldid = ($revid > $top_frev->fr_rev_id) ? $top_frev->fr_rev_id : $revid;
					$diff = ($revid > $top_frev->fr_rev_id) ? $revid : $top_frev->fr_rev_id;
					$flaghtml = wfMsgExt('revreview-newest', array('parse'), $top_frev->fr_rev_id, $oldid, $diff, $time );
				}
            } # Editing the page normally   
        	else {
				if ( $revid==$top_frev->rev_id )
					$flaghtml = wfMsgExt('revreview-isnewest', array('parse'), $time);
				else
					$flaghtml = wfMsgExt('revreview-newest', array('parse'), $top_frev->fr_rev_id, $top_frev->fr_rev_id, $revid, $time );
            }
            // Construct some tagging
            $flaghtml .= "<table align='center' cellpadding=\'0\'><tr>";
			foreach ( $this->dimensions as $quality => $value ) {
				$value = wfMsgHtml('revreview-' . $this->dimensions[$quality][$flags[$quality]]);
				$flaghtml .= "<td>&nbsp;<strong>" . wfMsgHtml("revreview-$quality") . "</strong>: $value&nbsp;</td>\n";    
            }
            $flaghtml .= '</tr></table>';
            // Should use CSS?
            $flaghtml = "<small>$flaghtml</small>";
        	$wgOut->addHTML( '<div class="mw-warning plainlinks">' . $flaghtml . '</div><br/>' );
        }
    }

    function addToDiff( &$diff, &$oldrev, &$newrev ) {
        $id = $newrev->getId();
        // We cannot review deleted edits
        if( $newrev->mDeleted ) return;
        $this->addQuickReview( $id, true );
    }
    
    function setCurrentTab( &$sktmp, &$content_actions ) {
    	global $wgRequest, $wgArticle, $action;
        // Only trigger on article view, not for protect/delete/hist
        // Talk pages cannot be validated
        if ( !$wgArticle || !$sktmp->mTitle->exists() || !$sktmp->mTitle->isContentPage() || $action !='view' )
            return;
        // If we are viewing a page normally, and it was overrode
        // change the edit tab to a "current revision" tab
        if ( !$wgRequest->getVal('oldid') ) {
        	$top_frev = $this->getLatestFlaggedRev( $wgArticle->getId() );
        	// Note that revisions may not be set to override for users
        	if ( is_object($top_frev) && $this->pageOverride() ) {
        		# Remove edit option altogether
        		unset( $content_actions['edit']);
        		unset( $content_actions['viewsource']);
				# Straighten out order
				$new_actions = array(); $counter = 0;
				foreach ( $content_actions as $action => $data ) {
					if ( $counter==1 ) {
        				# Set current rev tab AFTER the main tab is set
						$new_actions['current'] = array(
							'class' => '',
							'text' => wfMsg('currentrev'),
							'href' => $sktmp->mTitle->getLocalUrl( 'oldid=' . $wgArticle->getLatest() )
						);
					}
        			$new_actions[$action] = $data;
        			$counter++;
        		}
        		# Reset static array
        		$content_actions = $new_actions;
    		}
    	}
    }
    
    function addToPageHist( &$article ) {
    	$this->pageFlaggedRevs = array();
    	$rows = $this->getReviewedRevs( $article->getID() );
    	if ( !$rows ) return;
    	foreach( $rows as $row => $data ) {
    		$this->pageFlaggedRevs[] = $data->rev_id;
    	}
    }
    
    function addToHistLine( &$row, &$s ) {
    	if ( isset($this->pageFlaggedRevs) ) {
    		if ( in_array( $row->rev_id, $this->pageFlaggedRevs ) )
    			$s .= ' <small><strong>' . wfMsgHtml('revreview-hist') . '</strong></small>';
    	}
    }
        
    function addQuickReview( $id, $ontop=false, &$out=false ) {
		global $wgOut, $wgTitle, $wgUser, $wgScript;
        // We don't want two forms!
        if ( isset($this->formCount) && $this->formCount > 0 ) return;
        $this->formCount = 1;
		
		if ( !$wgUser->isAllowed( 'review' ) ) return; 

		$flags = $this->getFlagsForRevision( $id );
        
		$reviewtitle = SpecialPage::getTitleFor( 'Revisionreview' );
        $form = Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) );
		$form .= "<fieldset><legend>" . wfMsgHtml( 'revreview-flag', $id ) . "</legend>\n";
		$form .= wfHidden( 'title', $reviewtitle->getPrefixedText() );
        $form .= wfHidden( 'target', $wgTitle->getPrefixedText() );
        $form .= wfHidden( 'oldid', $id );
        foreach ( $this->dimensions as $quality => $levels ) {
            $form .= wfMsgHtml("revreview-$quality") . ": <select name='$quality'>\n";
            foreach ( $levels as $idx => $label ) {
                if ( $flags[$quality]==$idx )
                    $selected = 'selected';
                else
                    $selected = '';
                $form .= "<option value='$idx' $selected>" . wfMsgHtml("revreview-$label") . "</option>\n";    
            }
            $form .= "</select>\n";          
        }
		$form .= Xml::submitButton( wfMsgHtml( 'go' ) ) . "</fieldset>";
		$form .= Xml::closeElement( 'form' );
		// Hacks, to fiddle around with location a bit
		if( $ontop && $out ) {
			$out->mBodytext = $form . '<hr/>' . $out->mBodytext;
        } else if( $ontop ) {
			$wgOut->addHTML( $form );
		} else {
			$wgOut->addHTML( '<hr/>' . $form );
		}
    }
	
}

# Load expert promotion UI
include_once('SpecialMakevalidate.php');

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/FlaggedRevsPage.body.php', 'Revisionreview', 'Revisionreview' );

# Load approve/unapprove UI
$wgHooks['LoadAllMessages'][] = 'efLoadReviewMessages';

$flaggedrevs = new FlaggedRevs();
$wgHooks['BeforePageDisplay'][] = array($flaggedrevs, 'setPageContent');
$wgHooks['DiffViewHeader'][] = array($flaggedrevs, 'addToDiff');
$wgHooks['EditPage::showEditForm:initial'][] = array($flaggedrevs, 'addToEditView');
$wgHooks['SkinTemplateTabs'][] = array($flaggedrevs, 'setCurrentTab');
$wgHooks['PageHistoryBeforeList'][] = array($flaggedrevs, 'addToPageHist');
$wgHooks['PageHistoryLineEnding'][] = array($flaggedrevs, 'addToHistLine');
?>
