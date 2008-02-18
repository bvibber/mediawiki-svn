<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page Randomly shows one of the root pages (in contrast to subpages) from ns=0
 *
 * @addtogroup Extensions
 *
 * @author Hojjat (aka Huji) <huji.huji@gmail.com>
 * @copyright Copyright © 2008, Hojjat (aka Huji)
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfRandomrootpage';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Random root page',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RandomRootPage',
	'description' => '[[Special:Randomrootpage|Special page]] which fetches a random root page',
	'descriptionmsg' => 'Randomrootpage-desc',
	'author' => 'Hojjat (aka Huji)'
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Randomrootpage'] = $dir . 'Randomrootpage.i18n.php';

function wfRandomrootpage () {
	wfLoadExtensionMessages( 'Randomrootpage' );
	global $IP, $wgMessageCache;
	require_once "$IP/includes/SpecialPage.php";
	class Randomrootpage extends SpecialPage {

		/**
		 * Constructor
		 */
		public function Randomrootpage() {
			SpecialPage::SpecialPage( 'Randomrootpage' );
			$this->includable( false );
		}
		
		private $namespace = NS_MAIN;  // namespace to select pages from
	
		public function getNamespace() {
			return $this->namespace;
		}
		
		public function setNamespace ( $ns ) {
			if( $ns < NS_MAIN ) $ns = NS_MAIN;
			$this->namespace = $ns;
		}
		
		// Don't select redirects
		public function isRedirect(){
			return false;
		}
		
		public function execute( $par ) {
			global $wgOut, $wgContLang;
	
			if ($par)
				$this->setNamespace( $wgContLang->getNsIndex( $par ) );
	
			$title = $this->getRandomTitle();
	
			if( is_null( $title ) ) {
				$this->setHeaders();
				$wgOut->addWikiMsg( strtolower( $this->mName ) . '-nopages' );
				return;
			}
	
			$query = $this->isRedirect() ? 'redirect=no' : '';
			$wgOut->redirect( $title->getFullUrl( $query ) );
		}
	
	
		/**
		 * Choose a random title.
		 * @return Title object (or null if nothing to choose from)
		 */
		public function getRandomTitle() {
			$randstr = wfRandom();
			$row = $this->selectRandomPageFromDB( $randstr );
	
			/* If we picked a value that was higher than any in
			 * the DB, wrap around and select the page with the
			 * lowest value instead!  One might think this would
			 * skew the distribution, but in fact it won't cause
			 * any more bias than what the page_random scheme
			 * causes anyway.  Trust me, I'm a mathematician. :)
			 */
			if( !$row )
				$row = $this->selectRandomPageFromDB( "0" );
	
			if( $row )
				return Title::makeTitleSafe( $this->namespace, $row->page_title );
			else
				return null;
		}
	
		private function selectRandomPageFromDB( $randstr ) {
			global $wgExtraRandompageSQL;
			$fname = 'RandomPage::selectRandomPageFromDB';
	
			$dbr = wfGetDB( DB_SLAVE );
	
			$use_index = $dbr->useIndexClause( 'page_random' );
			$page = $dbr->tableName( 'page' );
	
			$ns = (int) $this->namespace;
			$redirect = $this->isRedirect() ? 1 : 0;
	
			$extra = $wgExtraRandompageSQL ? "AND ($wgExtraRandompageSQL)" : "";
			$sql = "SELECT page_title
				FROM $page $use_index
				WHERE page_namespace = $ns
				AND page_is_redirect = $redirect
				AND page_random >= $randstr
				AND page_title NOT LIKE '%/%'
				$extra
				ORDER BY page_random";
	
			$sql = $dbr->limitResult( $sql, 1, 0 );
			$res = $dbr->query( $sql, $fname );
			return $dbr->fetchObject( $res );
		}
	}

	SpecialPage::addPage( new Randomrootpage );
}