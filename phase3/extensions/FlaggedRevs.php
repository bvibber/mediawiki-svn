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

    function getFlagsForRevision( $rev_id ) {
    	// Set default blank flags
    	$flags = array( 'accuracy' => 0, 'depth' => 0, 'style' => 0 );

 		$db = wfGetDB( DB_MASTER );
 		// select a row, this should be unique
		$result = $db->select( 'flaggedrevs', array('*'), array('fr_rev_id' => $rev_id) );
		if ( $row = $db->fetchObject($result) ) {
			$flags = array( 'accuracy' => $row->fr_acc, 'depth' => $row->fr_dep, 'style' => $row->fr_sty );
		}
        return $flags;
    }

    function getLatestRev( $page_id ) {   
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
            	$db->delete( 'flaggedrevs', array( 'fr_rev_id' => $this->oldid ) );
            }
            $rows[] = $row;
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
    
    function pageOverride() {
    	global $wgFlaggedRevsAnonOnly, $wgUser;
    	return !( $wgFlaggedRevsAnonOnly && !$wgUser->isAnon() );
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
		$top_frev = $this->getLatestRev( $wgArticle->getId() );
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
            } # Viewing the page normally
			else {
				global $wgUser, $wgUploadDirectory;
        		// We will be looking at the reviewed revision...
        		$visible_id = $top_frev->fr_rev_id;
        		$revs_since = $this->getUnreviewedRevCount( $wgArticle->getId(), $visible_id );
        		$flaghtml = wfMsgExt('revreview-replaced', array('parse'), $visible_id, $wgArticle->getLatest(), $revs_since, $time );		
				# Hack...temporarily change image dir
				# This lets the parser know where to look...
				$uploadDir = $wgUploadDirectory;
				$wgUploadDirectory = "{$wgUploadDirectory}/stable";
				$parse_ops = ParserOptions::newFromUser($wgUser);
				# Don't show section-edit links
				$parse_ops->setEditSection( false );
				// Parse the new body, wikitext -> html
       			$newbody = $wgParser->parse( $top_frev->fr_cache, $wgTitle, $parse_ops );
       			$newbodytext = $newbody->getText();
       			// Reset image dir
       			$wgUploadDirectory = $uploadDir;
            }
            // Construct some tagging
            $flaghtml .= "<table align='center' cellspadding=\'0\'><tr>";
			foreach ( $this->dimensions as $quality => $value ) {
				$value = wfMsgHtml('revreview-' . $this->dimensions[$quality][$flags[$quality]]);
				$flaghtml .= "<td>&nbsp;<strong>" . wfMsgHtml("revreview-$quality") . "</strong>: $value&nbsp;</td>\n";    
            }
            $flaghtml .= '</tr></table>';
            // Copy over the old body
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
		$top_frev = $this->getLatestRev( $editform->mArticle->getId() );
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
					$flaghtml = wfMsgExt('revreview-isnewest', array('parse'));
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
        	$top_frev = $this->getLatestRev( $wgArticle->getId() );
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
