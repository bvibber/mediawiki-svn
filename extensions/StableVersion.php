<?
# Run the following SQL on your database prior to use :
#
# ALTER TABLE page ADD page_stable INT( 8 ) UNSIGNED NOT NULL DEFAULT '0' ;

if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['StableVersion'][] = array(
        'name' => 'Stable version',
        'description' => 'An extension to allow the marking of a stable version.',
        'author' => 'Magnus Manske'
);
$wgExtensionFunctions[] = 'wfStableVersion' ;
$wgHooks['ArticleViewHeader'][] = 'wfStableVersionHeaderHook' ;
$wgHooks['ArticlePageDataBefore'][] = 'wfStableVersionArticlePageDataBeforeHook' ;
$wgHooks['ArticlePageDataAfter'][] = 'wfStableVersionArticlePageDataAfterHook' ;

function wfStableVersionAddCache () {
	global $wgMessageCache , $wgStableVersionAddCache ;
	if ( $wgStableVersionAddCache ) return ;
	$wgStableVersionAddCache = true ;
	$wgMessageCache->addMessages(
		array(
			'staticversion_this_is_stable' => 'This is the stable version of this article. You can also look at the <a href="$1">latest draft version</a>.',
			'staticversion_this_is_draft_no_stable' => 'You are looking at a draft version of this article; there is no stable version of this article yet.',
			'staticversion_this_is_draft' => 'This is a draft version of this article. You can also look at the <a href="$1">stable version</a>.',
			'staticversion_reset_stable_version' => 'Click <a href="$1">here</a> to remove this as stable version!',
			'staticversion_set_stable_version' => 'Click <a href="$1">here</a> to set this as stable version!',
			'staticversion_set_ok' => 'The stable version has been successfully set.',
			'staticversion_reset_ok' => 'The stable version has been successfully removed. This article has no stable version right now.',
			'staticversion_return' => 'Return to <a href="$1">$2</a>',
			'staticversion_set_log' => '#$1 is now stable version.',
			'staticversion_reset_log' => 'Stable version has been removed.',
			'staticversion_log' => 'Stable version management',
			'staticversionpage' => 'Stable version log',
#			'staticversionpagetext' => 'Below is a list of page moved.',
		)
	);
}

# Adds query for stable version
function wfStableVersionArticlePageDataBeforeHook ( $a , &$b ) {
	$b[] = "page_stable" ;
}

# Adds new variable "mStable" to the article
function wfStableVersionArticlePageDataAfterHook ( &$a , $b ) {
	$a->mStable = $b->page_stable ;
}

# Decides wether a user can set the stable version
function wfStableVersionCanChange () {
	global $wgUser ;
	return true ; # Dummy, everyone can set stable versions
}

# Generates the little header line
function wfStableVersionHeaderHook ( $a ) {
	global $wgOut , $wgArticle , $wgTitle ;
	wfStableVersionAddCache () ;
	$st = "" ; # Subtitle
	
	if ( $wgArticle->getRevIdFetched() == $wgArticle->mStable ) { # This is the stable version
		$url = $wgTitle->getFullURL () ;
		$st = wfMsg ( 'staticversion_this_is_stable' , $url ) ;
	} else if ( $wgArticle->mStable == "0" ) { # There is no spoon, er, stable version
		$st = wfMsg ( 'staticversion_this_is_draft_no_stable' ) ;
	} else { # This is not the stable version, recommend it
		$url = $wgTitle->getFullURL ( "oldid=" . $wgArticle->mStable ) ;
		$st = wfMsg ( 'staticversion_this_is_draft' , $url ) ;
	}
	
	if ( wfStableVersionCanChange() ) { # This user may alter the stable version info
		$st .= " " ;
		$sp = Title::newFromText ( "Special:StableVersion" ) ;
		if ( $wgArticle->getRevIdFetched() == $wgArticle->mStable ) { # This is the stable version - reset?
			$url = $sp->getFullURL ( "id=" . $wgArticle->getID() . "&mode=reset" ) ;
			$st .= wfMsg ( 'staticversion_reset_stable_version' , $url ) ;
		} else {
			$url = $sp->getFullURL ( "&id=" . $wgArticle->getID() . "&mode=set&revision=" . $wgArticle->getRevIdFetched() ) ;
			$st .= wfMsg ( 'staticversion_set_stable_version' , $url ) ;
		}
	}

	if ( $st == "" ) return ;
	$st = $wgOut->getSubtitle() . "<br/>" . $st ;
	$wgOut->setSubtitle ( $st ) ;
}

# The special page
function wfStableVersion() {
	global $IP, $wgMessageCache;

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
			global $wgOut , $wgRequest ;
			wfStableVersionAddCache () ;
			$mode = $wgRequest->getText('mode', "") ;
			if ( $mode != 'set' && $mode != 'reset' ) return ; # Should be error (wrong call)
			$id = $wgRequest->getText ( 'id', "0" ) ;
			if ( $id == "0" ) return ; # Should be error (wrong call)
			if ( !wfStableVersionCanChange() ) return ; # Should be error (not allowed)
			if ( $mode == 'set' ) { # Set
				$newstable = $wgRequest->getText ( 'revision', "0" ) ;
				$out = wfMsg ( 'staticversion_set_ok' ) ;
				$act = wfMsg ( 'staticversion_set_log' , $newstable ) ;
			} else { # Reset
				$newstable = "0" ;
				$out = wfMsg ( 'staticversion_reset_ok' ) ;
				$act = wfMsg ( 'staticversion_reset_log' ) ;
			}

			$conditions = array( 'page_id' => $id );
			$fname = "SpecialStableVersion:execute" ;
			$dbw = wfGetDB( DB_MASTER );
			$dbw->update( 'page',
				array( /* SET */
					'page_stable'      => $newstable,
				),
				$conditions,
				$fname );

			$t = Title::newFromID ( $id ) ;
			$url = $t->getFullURL ( "&id=" . $id . "&mode=set&revision=" . $newstable ) ;
			$out = "<p>{$out}</p><p>" . wfMsg ( 'staticversion_return' , $url , $t->getFullText() ) . "</p>" ;

			# Logging (BROKEN!)
			$log = new LogPage( 'stableversion' );
			$log->addEntry( wfMsg('staticversion_log') , $t , $act );


			$this->setHeaders();
			$wgOut->addHtml( $out );
		}
	} # end of class

	SpecialPage::addPage( new SpecialStableVersion );
}


?>
