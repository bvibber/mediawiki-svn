<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Get a random page from the set of pages whos talk or subjectpage links to a
 * given page, can be used like Special:AdvancedRandom/Template:Featured/Talk
 * to get a random featured article or like
 * Special:AdvancedRandom/Template:Delete to get a random speedy deletion
 * candidate.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfAdvancedRandom';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'AdvancedRandom',
	'description' => 'Get a random page from whos talk or subjectpage ' .
	                 'links to a given page, can be used like ' .
			 '[[Special:AdvancedRandom/Template:Featured/Talk]] ' .
			 'to get a random featured article or like ' .
			 '[[Special:AdvancedRandom/Template:GFDL/Image]] to ' .
			 'get a random GFDL file',
	'author' => 'Ævar Arnfjörð Bjarmason'
);
	

function wfAdvancedRandom() {
	global $IP, $wgMessageCache;

	$wgMessageCache->addMessage( 'advancedrandom', 'Advanced random' );
	
	require_once "$IP/includes/SpecialPage.php";
	class SpecialAdvancedRandom extends SpecialPage {
		/**
		 * Constructor
		 */
		function SpecialAdvancedRandom() {
			SpecialPage::SpecialPage( 'AdvancedRandom' );
		}

		/**
		 * main()
		 */
		function execute( $par = null ) {
			global $wgOut;

			$fname = 'SpecialAdvancedRandom::execute';

			wfProfileIn( $fname );

			list( $page, $namespace ) = $this->extractParamaters( $par );

			$ft = Title::newFromText( $page );
			if ( is_null( $ft ) ) {
				$mainpage = $this->mainpage();
				$this->redirect( $mainpage );
				wfProfileOut( $fname );
				return;
			}
			
			$rand = wfRandom();
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->selectRow(
				array( 'page', 'pagelinks' ),
				array( 'page_namespace', 'page_title', 'page_random' ),
				array(
					'page_id = pl_from',
					'pl_namespace' => $ft->getNamespace(),
					'page_namespace' => $namespace,
					'pl_title' => $ft->getDBkey(),
					"page_random > $rand"
				),
				$fname,
				array(
					'ORDER BY' => 'page_random',
					'USE INDEX' => array(
						'page' => 'page_random',
					)
				)
			);

			$title =& Title::makeTitle( Namespace::getSubject( $namespace ), $res->page_title );
			if ( is_null( $title ) || $title->getText() == '' ) {
				$mainpage = $this->mainpage();
				$title = $mainpage;
			}
			
			$this->redirect( $title );
			wfProfileOut( $fname );
		}

		/**
		 * Redirect to a given page
		 *
		 * @access private
		 *
		 * @param object $title Title object
		 */
		function redirect( &$title ) {
			global $wgOut;

			$wgOut->redirect( $title->getFullUrl() );
		}
		
		/**
		 * Parse the page and namespace parts of the input and return them
		 *
		 * @access private
		 *
		 * @param string $par
		 * @return array
		 */
		function extractParamaters( $par ) {
			global $wgContLang;

			wfSuppressWarnings();
			list( $page, $namespace ) = explode( '/', $par, 2 );
			wfRestoreWarnings();

			// str*cmp sucks, casts null to ''
			if ( isset( $namespace ) )
				$namespace = $wgContLang->getNsIndex( $namespace );

			if ( is_null( $namespace ) || $namespace === false )
				$namespace = NS_MAIN;

			return array( $page, $namespace );
		}

		/**
		 * Return a mainpage title object
		 *
		 * @return object
		 */
		function mainpage() {
			return Title::newFromText( wfMsgForContent( 'mainpage' ) );
		}
	}
	
	SpecialPage::addPage( new SpecialAdvancedRandom );
}
