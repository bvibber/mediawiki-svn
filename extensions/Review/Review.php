<?
/*
To activate, enter
	include ( "extensions/Review/Review.php" ) ;
in your LocalSettings.php

To activate a different CSS stylesheet, enter something like
	define( 'REVIEW_CSS' , 'http://yourhost.com/name/wiki/extensions/Tasks/tasks.css' );
*before* the "include" statement.
*/


if( !defined( 'MEDIAWIKI' ) ) die();
if( !defined( 'REVIEW_CSS' ) ) define('REVIEW_CSS', $wgScriptPath.'/extensions/Review/review.css' );

$wgExtensionCredits['Review'][] = array(
        'name' => 'Review',
        'description' => 'The resurrected validation feature.',
        'author' => 'Magnus Manske'
);

$wgExtensionFunctions[] = 'wfReviewExtensionFunction';

# Hooks
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfReviewExtensionAfterToolbox';

# Global variables
$wgReviewExtensionInitMessages = false ;
$wgReviewExtensionTopics = array () ;

# ______________________________________________________________________________
# Functions

/**
* Does this extension apply to this namespace?
*/
function wfReviewExtensionDoesNamespaceApply ( $namespace ) {
	if ( $namespace == 0 ) return true ;
	return false ;
}

/**
* Initialize messages for this extension
*/
function wfReviewExtensionInitMessages () {
	global $wgUserTogglesEn, $wgDefaultUserOptions, $wgReviewExtensionTopics, $wgReviewExtensionInitMessages, $wgOut;
	if( $wgReviewExtensionInitMessages ) {
		# Been there, done that
		return;
	}
	$wgReviewExtensionInitMessages = true;
#	$wgUserTogglesEn[] = "show_task_comments" ;

	# Set the CSS
	$wgOut->addLink(array(
		'rel'	=> 'stylesheet',
		'type'	=> 'text/css',
		'media'	=> 'screen,projection',
		'href'	=> REVIEW_CSS,
	));


	// Default language is english
	require_once('language/en.php');

	global $wgLanguageCode;
	$filename = 'language/' . addslashes($wgLanguageCode) . '.php' ;
	// inclusion might fail :p
	include( $filename );

	# Now parsing the topics
	$s = explode ( "\n" , wfMsg ( 'review_topics' ) ) ;
	$wgReviewExtensionTopics = array () ;
	foreach ( $s AS $v ) {
		$v = explode ( ':' , trim ( $v ) ) ;
		if ( count ( $v ) != 5 ) continue ; # Some other line, ignore it
		$x = "" ;
		$x->key = trim ( array_shift ( $v ) ) ;
		$x->name = trim ( array_shift ( $v ) ) ;
		$x->range = (int) trim ( array_shift ( $v ) ) ;
		$x->left = trim ( array_shift ( $v ) ) ;
		$x->right = trim ( array_shift ( $v ) ) ;
		$wgReviewExtensionTopics[$x->key] = $x ;
	}
}

/**
* Generate the radio fields for the form
* @param $topic Topic object (one topic only)
* @return HTML string with radio fields
*/
function wfReviewExtensionGetTopicForm ( $topic ) {
	# Dummy value
	if ( !isset ( $topic->value ) )
		$topic->value = 0 ;
	
	$tkey = "review_topic[" . $topic->key . "]" ;
	$ret = "" ;
	$ret .= '<input id="review_radio_no_opinion" type="radio" name="' . $tkey . '" value="0"' ;
	$ret .= $topic->value == 0 ? " checked" : "" ;
	$ret .= '/>&nbsp;' ;
	if ( $topic->range == 2 ) { # Yes/No
		$ret .= '<input type="radio" name="' . $tkey . '" value="1" id="review_radio_1_of_2"' ;
		$ret .= $topic->value == 1 ? " checked" : "" ;
		$ret .= '>' . $topic->left . '</input> ' ;
		$ret .= '<input type="radio" name="' . $tkey . '" value="2" id="review_radio_2_of_2"' ;
		$ret .= $topic->value == 2 ? " checked" : "" ;
		$ret .= '>' . $topic->right . '</input>' ;
	} else { # Range
		for ( $a = 1 ; $a <= $topic->range ; $a++ ) {
			$ret .= '<input type="radio" name="' . $tkey . '" value="' . $a . '"' ;
			$ret .= " id='review_radio_" . $a . "_of_" . $topic->range . "'" ; # This doesn't show for some weird reason...
			$ret .= $topic->value == $a ? " checked" : "" ;
			$ret .= '/>' ;
		}
	}
	return $ret ;
}

/**
* Sets the search condition for the WHERE clause in an SQL query
* @param $user User object (will not be changed by this function)
* @param $conds Conditions array, will be extended to include the condition
*/
function wfReviewExtensionSetUserCondition ( &$user , &$conds ) {
	if ( $user->getID() == 0 ) {
		# Anon
		$conds['val_ip'] = $user->getName() ;
	} else {
		# User with account
		$conds['val_user'] = $user->getID() ;
	}
}

/**
* Returns the ratings for a user for a specific page
* @param $title Title object (will not be changed by this function)
* @param $user User object (will not be changed by this function)
* @param $revision If set, the function fill return only the ratings for that revision
* @return array [revision] => ( array [topic number] => object with data )
*/
function wfReviewExtensionGetUserRatingsForPage ( &$title , &$user , $revision = "" ) {
	$ret = array () ;
	if ( !$title->exists() ) return $ret ; # No such page

	$fname = 'wfReviewExtensionGetUserRatingsForPage' ;
	$dbr =& wfGetDB( DB_SLAVE );
	$conds = array () ;
	$conds['val_page'] = $title->getArticleID() ;
	wfReviewExtensionSetUserCondition ( $user , $conds ) ;

	# Search for a special revision?
	if ( $revision != "" )
		$conds['val_revision'] = $revision ;

	# Query
	$res = $dbr->select(
			/* FROM   */ 'validate',
			/* SELECT */ '*',
			/* WHERE  */ $conds,
			$fname
	);

	while ( $line = $dbr->fetchObject( $res ) ) {
		# Create the revision array, if necessary
		if ( !isset ( $ret[$line->val_revision] ) )
			$ret[$line->val_revision] = array () ;

		# Store the data
		$ret[$line->val_revision][$line->val_type] = $line ;
	}
	return $ret ;
}

/**
* Pre-fills values into a topics list
* @param $topics Topics array (array [topic number] => topic data object)
* @param $ratings Rating array (array [
*/
function wfReviewExtensionPresetForm ( &$topics , &$ratings ) {
	$tk = array_keys ( $topics ) ;
	foreach ( $tk AS $key ) {
		if ( isset ( $ratings[$key] ) ) {
			# User rating exists
			$topics[$key]->value = $ratings[$key]->val_value ;
		} else {
			# Dummy value
			$topics[$key]->value = 0 ;
		}
	}
}

/**
* Checks for form data, integrates them and stores them in the database
* @param $ratings Array of ratings for this article from this user, read from database
* @param $merge_others can be called with "false" to prevent merging with other ratings
*/
function wfReviewExtensionReadLastForm ( &$ratings , $merge_others = true ) {
	global $wgUser, $wgRequest , $wgTitle ;
	
	# Was there a form?
	if ( $wgRequest->getText ( 'save_review' , "" ) == "" )
		return ;

	$fname = 'wfReviewExtensionReadLastForm' ;
	$dbw =& wfGetDB( DB_MASTER );
	$user_ip = $wgUser->getID() == 0 ? $wgUser->getName() : "" ;

	# Read form values
	$oldrev = $wgRequest->getInt ( 'review_oldid' ) ;
	$topics = $wgRequest->getArray ( 'review_topic' ) ;
	$comments = $wgRequest->getArray ( 'review_comment' ) ;
	
	# Sort revisions, latest first
	krsort ( $ratings ) ;

	# Finding old values
	$old = array () ;
	if ( $merge_others ) {
		foreach ( $ratings AS $revision => $rev_data ) {
			if ( $revision == $oldrev ) continue ;
			foreach ( $rev_data AS $key => $value ) {
				if ( isset ( $old[$key] ) ) continue ;
				$old[$key] = $value ;
			}
		}
	}

	# Merging
	$new_data = array () ;
	foreach ( $topics AS $key => $value ) {
		if ( $value > 0 ) {
			# Already set a value
			$new_data[$key] = "" ;
			$new_data[$key]->val_user = $wgUser->getID() ;
			$new_data[$key]->val_page = $wgTitle->getArticleID() ;
			$new_data[$key]->val_revision = $oldrev ;
			$new_data[$key]->val_type = $key ;
			$new_data[$key]->val_value = $value ;
			$new_data[$key]->val_comment = isset ( $comments[$key] ) ? $comments[$key] : "" ;
			$new_data[$key]->val_ip = $user_ip ;
			continue ;
		}
		if ( !$merge_others ) continue ; # No merging
		if ( !isset ( $old[$key] ) ) continue ; # No old value either
		# Set old value
		$new_data[$key] = $old[$key] ;
	}

	if ( $merge_others ) {
		# Remove old ratings
		$ratings = array () ;
		$ratings[$oldrev] = $new_data ;

		# Delete *all* old ratings from the database
		$conds = array () ;
		$conds['val_page'] = $wgTitle->getArticleID() ;
		wfReviewExtensionSetUserCondition ( $wgUser , $conds ) ;
		$dbw->delete ( 'validate' , $conds , $fname ) ;
	} else {
		# Just replace the ones for this revision
		$ratings[$oldrev] = $new_data ;
	
		# Delete old ratings for this revision from the databasewfReviewExtensionPresetForm
		$conds = array () ;
		$conds['val_page'] = $wgTitle->getArticleID() ;
		$conds['val_revision'] = $oldrev ;
		wfReviewExtensionSetUserCondition ( $wgUser , $conds ) ;
		$dbw->delete ( 'validate' , $conds , $fname ) ;
}

	# Insert new ratings into the database
	if ( count ( $new_data ) > 0 ) $dbw->begin () ;
	foreach ( $new_data AS $key => $value ) {
		$data = array (
			'val_user' => $value->val_user ,
			'val_page' => $value->val_page ,
			'val_revision' => $value->val_revision ,
			'val_type' => $value->val_type ,
			'val_value' => $value->val_value ,
			'val_comment' => $value->val_comment ,
			'val_ip' => $value->val_ip ,
		) ;
		$dbw->insert ( 'validate' , $data ) ;
	}
	if ( count ( $new_data ) > 0 ) $dbw->commit() ;
}

/**
* Display in sidebar
* @param $tpl The used template
*/
function wfReviewExtensionAfterToolbox( &$tpl ) {
	global $wgTitle, $wgUser , $wgReviewExtensionTopics, $wgArticle, $action;

	# Do we care?
	if( !wfReviewExtensionDoesNamespaceApply ( $wgTitle->getNamespace() ) )
		return ;
	if ( $wgUser->isBlocked() )
		return ;
	if ( $action != "view" )
		return ;

	# Initialize
	$skin =& $wgUser->getSkin() ;
	$revision = $wgArticle->getRevIdFetched() ;
	wfReviewExtensionInitMessages () ;
	$ratings = wfReviewExtensionGetUserRatingsForPage ( $wgTitle , $wgUser ) ;
	wfReviewExtensionReadLastForm ( $ratings ) ;
	if ( !isset ( $ratings[$revision] ) ) # Construct blank dummy, if necessary
		$ratings[$revision] = array () ;
	wfReviewExtensionPresetForm ( $wgReviewExtensionTopics , $ratings[$revision] ) ;

?>

			</ul>
		</div>
	</div>
	<div class="portlet" id="p-tasks">
		<h5>
<?php
	$tpl->msg('review_sidebar_title')
?>
		</h5>
		<div class="pBody">
			<form method='post' id="review_sidebar">
<?php
	print wfMsgForContent ( 'review_your_review' ) . "<br/>" ;
	foreach( $wgReviewExtensionTopics as $topic ) {
?>
			<a id="review_sidebar_link" href="
<?php
	$topic_title = Title::makeTitleSafe( NS_MEDIAWIKI, wfMsgForContent('review_topic_page').'#'.$topic->name );
	print $topic_title->escapeLocalURL();
?>
				">
<?php
	echo $topic->name ;
?>
				</a>
<?php
	if ( $topic->range > 2 )
		print "<small> (" . $topic->left . "&rarr;" . $topic->right . ")</small><br/>" ;
	echo "<div id='review_sidebar_range'>" . wfReviewExtensionGetTopicForm ( $topic ) . "</div>" ;
?>
<?php
	}
	print "<input type='hidden' name='review_oldid' value='{$revision}'/>" ;
	print "<div style='text-align:right'><input type='submit' name='save_review' value='" . wfMsgForContent('review_save') . "'/></div>" ;
	print "<div id='review_sidebar_note'>" ;
	print wfMsgForContent ( 'review_sidebar_explanation' ) ;
	if ( count ( $ratings ) > 1 ) {
		print " " . wfMsgForContent ( 'review_sidebar_you_have_other_reviews_for_this_article' ) ;
	}
	print "<br/>" ;
	$stat_title = Title::makeTitleSafe( NS_SPECIAL, "Review" );
	$link = $skin->makeLinkObj( $stat_title, wfMsgHTML( 'review_page_link' ), "mode=view_page_statistics&page_id=".$wgTitle->getArticleID() );
	$out = str_replace ( "$1" , $link , wfMsg ( 'review_sidebar_final' ) ) ;
	print $out ;
?>
	</div></form>
	<ul>
<?php

}


# ____________________________________________________________________
# Class / Special Page

function wfReviewExtensionFunction () {
	global $IP, $wgMessageCache;
	wfReviewExtensionInitMessages();

	// FIXME : i18n
	$wgMessageCache->addMessage( 'review', 'Review' );

	require_once( "$IP/includes/SpecialPage.php" );

	/**
	* Constructor
	*/
	class SpecialReview extends SpecialPage {
	
		function SpecialReview() {
			SpecialPage::SpecialPage( 'Review' );
			$this->includable( true );
			wfReviewExtensionInitMessages();
		}

		/**
		* Returns the reviewed revision numbers for the page
		* @param $title The page title
		* @return Array[] => val_revision
		*/
		function get_reviewed_revisions ( $title ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(
					/* FROM   */ 'validate',
					/* SELECT */ 'DISTINCT val_revision',
					/* WHERE  */ array ( 'val_page' => $title->getArticleID() ),
					$fname
			);
			$ret = array () ;
			while ( $line = $dbr->fetchObject( $res ) ) {
				$ret[] = $line->val_revision ;
			}
			return $ret ;
		}

		/**
		* Returns all review data for a single revision of a page
		* @param $title The page
		* @param $revision The revision ID
		* @return array of objects with one review each
		*/
		function get_reviews_for_revision ( $title , $revision ) {
			$conds = array (
				'val_page' => $title->getArticleID() ,
				'val_revision' => $revision ,
			) ;
		
			# Query
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(
					/* FROM   */ 'validate',
					/* SELECT */ '*',
					/* WHERE  */ $conds,
					$fname
			);

			$ret = array() ;
			while ( $line = $dbr->fetchObject( $res ) ) {
				$ret[] = $line ;
			}
			return $ret ;
		}

		/**
		* Sum up the review data for a single revision and add it to $statistics
		* @param $title The page
		* @param $revision The revision ID
		* @param $reviews The array of reviews
		* @param $statistics The array of overall statistics
		* @return Data for this revision, as an array of objects (same nomenclature as for $statistics)
		*/
		function analyze_review_data ( $title , $revision , &$reviews , &$statistics ) {
			global $wgReviewExtensionTopics ;

			# Read data
			$data = array () ;
			foreach ( $reviews AS $review ) {
				$type = $review->val_type ;
				if ( !isset ( $data[$type] ) ) {
					# Set dummy values
					$data[$type] = "" ;
					$data[$type]->total_count = 0 ;
					$data[$type]->anon_count = 0 ;
					$data[$type]->sum = 0 ;
					$data[$type]->max = $wgReviewExtensionTopics[$type]->range ;
				}
				$data[$type]->total_count++ ;
				if ( $review->val_user == 0 )
					$data[$type]->anon_count++ ;
				$data[$type]->sum += $review->val_value ;
			}

			# Add data to overall statistics
			foreach ( $data AS $type => $v ) {
				if ( !isset ( $statistics[$type] ) ) {
					$statistics[$type] = $v ;
					continue ;
				}
				$statistics[$type]->total_count += $v->total_count ;
				$statistics[$type]->anon_count += $v->anon_count ;
				$statistics[$type]->sum += $v->sum ;
			}
			return $data ;
		}

		/**
		* Returns a HTML table row for the statistics of a revision
		* @param $title The page
		* @param $revision The revision ID (or -1 for table header, 0 for total statistics)
		* @param $data The data for this revision
		* @return HTML table row
		*/
		function get_revision_statistics_row ( $title , $revision , &$data ) {
			global $wgReviewExtensionTopics , $wgUser ;
			$skin =& $wgUser->getSkin() ;

			# Row header
			$ret = "<tr><th id='review_statistics_table_header' align='left'>" ;
			if ( $revision == -1 ) {
				# Table headers
				$ret .= wfMsgForContent ( 'review_statistics_left_corner' ) ;
			} else if ( $revision == 0 ) {
				# Total statistics
				$ret .= wfMsgForContent ( 'review_total_statistics' ) ;
			} else {
				# Individual revision
				$version_link = $skin->makeLinkObj ( $title , wfMsgForContent('review_version_link',$revision) , "oldid={$revision}" ) ;
				$ret .= $version_link ;
			}
			$ret .= "</th>" ;

			foreach ( $wgReviewExtensionTopics AS $type => $topic ) {
				if ( $revision == -1 ) {
					# Table header row
					$ret .= "<th id='review_statistics_table_header'>" ;
					$ret .= $topic->name ;
					$ret .= "</th>" ;
				} else {
					$ret .= "<td id='review_statistics_table_cell'>" ;
					if ( $data[$type]->total_count > 0 ) {
						$average = $data[$type]->sum / $data[$type]->total_count ;
						$ret .= "<div id='" ;
						$ret .= "review_radio_" . $average . "_of_" . $data[$type]->max ;
						$ret .= "'>" ;
						$ret .= wfMsgForContent ( 'review_statistic_cell' ,
										$average ,
										$data[$type]->max ,
										$data[$type]->total_count ,
										$data[$type]->total_count - $data[$type]->anon_count ,
										$data[$type]->anon_count
						) ;
						$ret .= "</div>" ;
					} else {
						$ret .= "&mdash;" ;
					}
					$ret .= "</td>" ;
				}
			}
			$ret .= "</tr>\n" ;
			return $ret ;
		}

		/**
		* Special page main function
		*/
		function execute( $par = null ) {
			global $wgRequest , $wgOut , $wgUser ;
			wfReviewExtensionInitMessages () ;

			$out = "" ;
			$skin =& $wgUser->getSkin () ;
			$mode = $wgRequest->getText ( 'mode' , "" ) ;
			$page_id = $wgRequest->getInt ( 'page_id' , 0 ) ;
			$error = false ;
			
			if ( $page_id == 0 ) {
				$title = new Title ;
				$error = true ;
			} else {
				$title = Title::newFromID ( $page_id ) ;
			}

			if ( $error ) {
				# Do nothing
			} else if ( $mode == 'view_page_statistics' ) {
				$revisions = $this->get_reviewed_revisions ( $title ) ;
				arsort ( $revisions ) ; # Newest first
				if ( count ( $revisions ) == 0 ) {
					$out .= wfMsgForContent ( 'review_no_reviews_for_page' , $skin->makeLinkObj( $title ) ) ;
				} else {
					# Load review data for each version separately to avoid memory apocalypse
					$statistics = array() ;
					$out .= "<table id='review_statistics_table'>\n" ;
					$out .= $this->get_revision_statistics_row ( $title , -1 , $statistics ) ;
					$out2 = "" ;
					foreach ( $revisions AS $revision ) {
						$reviews = $this->get_reviews_for_revision ( $title , $revision ) ;
						$data = $this->analyze_review_data ( $title , $revision , $reviews , $statistics ) ;
						$out2 .= $this->get_revision_statistics_row ( $title , $revision , $data ) ;
					}
					$out .= $this->get_revision_statistics_row ( $title , 0 , $statistics ) ;
					$out .= $out2 ;
					$out .= "</table>\n" ;
				}
			} else {
				$error = true ;
			}
		
			$this->setHeaders();
			if ( $error ) {
				$wgOut->addHtml( wfMsgForContent ( 'review_error' ) );
			} else {
				$wgOut->setPageTitle ( wfMsgForContent ( 'review_for_page' , $title->getPrefixedText() ) ) ;
				$wgOut->addHtml( $out );
			}
		}
	} # end of class SpecialReview

	SpecialPage::addPage(new SpecialReview);
}

?>