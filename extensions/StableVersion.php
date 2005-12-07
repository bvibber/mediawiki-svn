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
			$mode = $wgRequest->getText('mode', "") ;
			if ( $mode != 'set' && $mode != 'reset' ) return ; # Should be error (wrong call)
			$id = $wgRequest->getText ( 'id', "0" ) ;
			if ( $id == "0" ) return ; # Should be error (wrong call)
			if ( !wfStableVersionCanChange() ) return ; # Should be error (not allowed)
			if ( $mode == 'set' ) { # Set
				$newstable = $wgRequest->getText ( 'revision', "0" ) ;
				$out = wfMsg ( 'staticversion_set_ok' ) ;
			} else { # Reset
				$newstable = "0" ;
				$out = wfMsg ( 'staticversion_reset_ok' ) ;
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

			$this->setHeaders();
			$wgOut->addHtml( $out );
		}
	} # end of class

	SpecialPage::addPage( new SpecialStableVersion );
}


?>
