<?
/**
* Run the following SQL on your database prior to use :
*
NONO! * ALTER TABLE page ADD page_stable INT( 8 ) UNSIGNED NOT NULL DEFAULT '0' ;

CREATE TABLE stableversions (
  sv_page_id int(8) unsigned NOT NULL default '0',
  sv_page_rev int(8) unsigned NOT NULL default '0',
  sv_type tinyint(2) unsigned NOT NULL default '0',
  sv_user int(8) unsigned NOT NULL default '0',
  sv_date varchar(14) NOT NULL default '',
  sv_cache mediumblob NOT NULL,
  KEY sv_page_id (sv_page_id,sv_page_rev,sv_type)
) TYPE=InnoDB;

*/

if (!defined('MEDIAWIKI')) die();

/**@+ Version type constants */
define( 'SV_TYPE_UNDEFINED',  0 );
define( 'SV_TYPE_STABLE',     1 );
define( 'SV_TYPE_STABLE_CANDIDATE', 2 );
/**@-*/


$wgExtensionCredits['StableVersion'][] = array(
        'name' => 'Stable version',
        'description' => 'An extension to allow the marking of a stable version.',
        'author' => 'Magnus Manske'
);

$wgAvailableRights[] = 'stableversion';
$wgExtensionFunctions[] = 'wfStableVersion' ;
$wgHooks['ArticleViewHeader'][] = 'wfStableVersionHeaderHook' ;
$wgHooks['ArticlePageDataBefore'][] = 'wfStableVersionArticlePageDataBeforeHook' ;
$wgHooks['ArticlePageDataAfter'][] = 'wfStableVersionArticlePageDataAfterHook' ;

# BEGIN logging functions
$wgHooks['LogPageValidTypes'][] = 'wfStableVersionAddLogType';
$wgHooks['LogPageLogName'][] = 'wfStableVersionAddLogName';
$wgHooks['LogPageLogHeader'][] = 'wfStableVersionAddLogHeader';
$wgHooks['LogPageActionText'][] = 'wfStableVersionAddActionText';

function wfStableVersionAddLogType( &$types ) {
	if ( !in_array( 'stablevers', $types ) )
		$types[] = 'stablevers';
	return true;
}

function wfStableVersionAddLogName( &$names ) {
	$names['stablevers'] = 'stableversion_logpage';
	return true;
}

function wfStableVersionAddLogHeader( &$headers ) {
	$headers['stablevers'] = 'stableversion_logpagetext';
	return true;
}

function wfStableVersionAddActionText( &$actions ) {
	$actions['stablevers/stablevers'] = 'stableversion_logentry';
	return true;
}
# END logging functions


# Text adding function
function wfStableVersionAddCache () {
	global $wgMessageCache , $wgStableVersionAddCache ;
	if ( $wgStableVersionAddCache ) return ;
	$wgStableVersionAddCache = true ;
	$wgMessageCache->addMessages(
		array(
			'stableversion_this_is_stable' => 'This is the stable version of this article. You can also look at the <a href="$1">latest draft version</a>.',
			'stableversion_this_is_draft_no_stable' => 'You are looking at a draft version of this article; there is no stable version of this article yet.',
			'stableversion_this_is_draft' => 'This is a draft version of this article. You can also look at the <a href="$1">stable version</a>.',
			'stableversion_reset_stable_version' => 'Click <a href="$1">here</a> to remove this as stable version!',
			'stableversion_set_stable_version' => 'Click <a href="$1">here</a> to set this as stable version!',
			'stableversion_set_ok' => 'The stable version has been successfully set.',
			'stableversion_reset_ok' => 'The stable version has been successfully removed. This article has no stable version right now.',
			'stableversion_return' => 'Return to <a href="$1">$2</a>',
			
			'stableversion_reset_log' => 'Stable version has been removed.',
			'stableversion_logpage' => 'Stable version log',
			'stableversion_logpagetext' => 'This is a log of changes to stable versions',
			'stableversion_logentry' => '',
			'stableversion_log' => 'Revision #$1 is now the stable version.',
			'stableversion_before_no' => 'There was no stable revision before.',
			'stableversion_before_yes' => 'The last stable revision was #$1.',
		)
	);
}

/**
* Adds query for stable version
* @param $article (not used)
* @param $fields Fields for query
*/
function wfStableVersionArticlePageDataBeforeHook ( &$article , &$fields ) {
	return true ;
#	$fields[] = "page_stable" ;
#	$fields[] = "page_stable_cache" ;
}

/**
* Adds new variables "mStable" and "mStableCache" to the article
* @param $article The article
* @param $fields Query result object
*/
function wfStableVersionArticlePageDataAfterHook ( &$article , $fields ) {
	$dbr =& wfGetDB( DB_SLAVE );
	$fname = "wfStableVersionArticlePageDataAfterHook" ;
	$title = $article->getTitle() ;
	
	# No stable versions of a non-existing article
	if ( !$title->exists() ) return ;

	$res = $dbr->select(
			/* FROM   */ 'stableversions',
			/* SELECT */ '*',
			/* WHERE  */ array( 'sv_page_id' => $title->getArticleID() , 'sv_page_rev' => $article->mRevision ) ,
			array ( "ORDER BY" => "sv_page_rev DESC" )
	);
	
	$article->mIsStable = false ;
	$article->mLastStable = 0 ;
	while ( $o = $dbr->fetchObject( $res ) ) {
		if ( $o->sv_type == SV_TYPE_STABLE ) {
			# Stable version
			if ( $o->sv_page_id == $title->getArticleID() )
				$article->mIsStable = true ;
			if ( $article->mLastStable == 0 )
				$article->mLastStable = $o->sv_page_rev ;
		}
	}
	$dbr->freeResult( $res );

	return true ;
}

/**
* Decides wether a user can set the stable version
* @return bool (always TRUE by default, for testing)
*/
function wfStableVersionCanChange () {
	return true ; # Dummy, everyone can set stable versions
	global $wgUser ;
	if ( !$wgUser->isAllowed( 'stableversion' ) ) {
		$wgOut->permissionRequired( 'stableversion' );
		return false ;
	}
	return true ;
}

/**
* Generates the little header line
* @param $article The article
*/
function wfStableVersionHeaderHook ( &$article ) {
	global $wgOut , $wgTitle ;
	wfStableVersionAddCache () ;
	$st = "" ; # Subtitle
	
	if ( $article->mIsStable ) { # This is the stable version
		$url = $wgTitle->getFullURL () ;
		$st = wfMsg ( 'stableversion_this_is_stable' , $url ) ;
	} else if ( $article->mLastStable == "0" ) { # There is no spoon, er, stable version
		$st = wfMsg ( 'stableversion_this_is_draft_no_stable' ) ;
	} else { # This is not the stable version, recommend it
		$url = $wgTitle->getFullURL ( "oldid=" . $article->mLastStable ) ;
		$st = wfMsg ( 'stableversion_this_is_draft' , $url ) ;
	}
	
	if ( wfStableVersionCanChange() ) { # This user may alter the stable version info
		$st .= " " ;
		$sp = Title::newFromText ( "Special:StableVersion" ) ;
		if ( $article->getRevIdFetched() == $article->mLastStable ) { # This is the stable version - reset?
			$url = $sp->getFullURL ( "id=" . $article->getID() . "&mode=reset" ) ;
			$st .= wfMsg ( 'stableversion_reset_stable_version' , $url ) ;
		} else {
			$url = $sp->getFullURL ( "id=" . $article->getID() . "&mode=set&revision=" . $article->getRevIdFetched() ) ;
			$st .= wfMsg ( 'stableversion_set_stable_version' , $url ) ;
		}
	}

	$st = $wgOut->getSubtitle() . "<div id='stable_version_header'>" . $st . "</div>" ;
	$wgOut->setSubtitle ( $st ) ;
	return true ;
}

# The special page
function wfStableVersion() {
	global $IP, $wgMessageCache;
	wfStableVersionAddCache () ;

	$wgMessageCache->addMessage( 'stableversion', 'Stable Version' );

	require_once "$IP/includes/SpecialPage.php";

	class SpecialStableVersion extends SpecialPage {
		/**
		* Constructor
		*/
		function SpecialStableVersion() {
			SpecialPage::SpecialPage( 'StableVersion' );
			$this->includable( true );
		}
	
		/**
		* main()
		*/
		function execute( $par = null ) {
			global $wgOut , $wgRequest , $wgUser ;
			
			# Sanity checks
			$mode = $wgRequest->getText('mode', "") ;
			if ( $mode != 'set' && $mode != 'reset' ) return ; # Should be error (wrong call)
			$id = $wgRequest->getText ( 'id', "0" ) ;
			if ( $id == "0" ) return ; # Should be error (wrong call)
			if ( !wfStableVersionCanChange() ) return ; # Should be error (not allowed)

			# OK, now do business
			$t = Title::newFromID ( $id ) ;

			if ( $mode == 'set' ) { # Set new version as stable
				$newstable = $wgRequest->getText ( 'revision', "0" ) ;
				$out = wfMsg ( 'stableversion_set_ok' ) ;
				$url = $t->getFullURL ( "oldid=" . $newstable ) ;
				$act = wfMsg ( 'stableversion_log' , $newstable ) ;
			} else { # Reset stable version
				$newstable = "0" ;
				$out = wfMsg ( 'stableversion_reset_ok' ) ;
				$url = $t->getFullURL () ;
				$act = wfMsg ( 'stableversion_reset_log' ) ;
			}
			
			# Get old stable version
			$dbr =& wfGetDB( DB_SLAVE );
			$fname = "SpecialStableVersion:execute" ;
			$row = $dbr->selectRow( 'page', array( 'page_stable' ),
				array( 'page_id' => $id ), $fname );
			$oldstable = $row->page_stable ;
			if ( $oldstable == 0 ) $before = wfMsg ( 'stableversion_before_no' ) ;
			else $before = wfMsg ( 'stableversion_before_yes' , $oldstable ) ;
			$act .= " " . $before ;
			
			$type = SV_TYPE_STABLE ;

			$dbw =& wfGetDB( DB_MASTER );
			$dbw->begin () ;
			
			# Delete this just in case it was already set
			$conditions = array ( 'sv_page_id' => $id , 'sv_page_rev' => $newstable ) ;
			$dbw->delete ( 'stableversions' , $conditions , $fname ) ;
			
			$values = array (
				'sv_page_id' => $id,
				'sv_page_rev' => $newstable,
				'sv_type' => $type,
				'sv_user' => $wgUser->getID(),
				'sv_date' => "12345678123456" ,
				'sv_cache' => "",
			) ;
			$dbw->insert( 'stableversions',
				$values ,
				$fname );
			$dbw->commit () ;

			$out = "<p>{$out}</p><p>" . wfMsg ( 'stableversion_return' , $url , $t->getFullText() ) . "</p>" ;
			$act = "[[" . $t->getText() . "]] : " . $act ;

			# Logging
			$log = new LogPage( 'stablevers' );
			$log->addEntry( 'stablevers', $t , $act );

			$this->setHeaders();
			$wgOut->addHtml( $out );
		}
	} # end of class

	SpecialPage::addPage( new SpecialStableVersion );
}


?>
