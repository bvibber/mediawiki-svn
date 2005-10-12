<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A special page querypage extension (the first querypage extension) that
 * lists cross-namespace links that shouldn't exist on Wikimedia projects.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfSpecialCrossNamespaceLinks';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Cross-namespace links',
	'description' => 'lists links across namespaces that shouldn\'t exist on Wikimedia projects',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

function wfSpecialCrossNamespaceLinks() {
	global $wgMessageCache, $wgHooks, $IP;
	
	$wgMessageCache->addMessages(
		array(
			'crossnamespacelinks' => 'Cross-namespace links',
			'crossnamespacelinkstext' => '$1: $2 links to $3'
		)
	);

	$wgHooks['wgQueryPages'][] = 'wfSpecialCrossNamespaceLinksHook';

	require_once "$IP/includes/SpecialPage.php";
	require_once "$IP/includes/QueryPage.php";
	class CrossNamespaceLinks extends SpecialPage {
		/**
		 * Constructor
		 */
		function CrossNamespaceLinks() {
			$this->name( 'CrossNamespaceLinks' );
			$this->file( __FILE__ );
		}

		/**
		 * main()
		 */
		function execute( $par = null ) {
			$this->setHeaders();
			list( $limit, $offset ) = wfCheckLimits();
			
			$cnl = new CrossNamespaceLinksPage();
			
			$cnl->doQuery( $offset, $limit );
		}
	}

	class CrossNamespaceLinksPage extends QueryPage {
		/**
		 * An inclusive list of namespaces that are acceptable for a
		 * page in the main namespace on Wikimedia sites (Wikipedia) to
		 * link to.
		 *
		 * NS_IMAGE is not included because it doesn't appear in the
		 * pagelinks table but in imagelinks.
		 *
		 * @access private
		 * @var array
		 */
		var $namespaces = array(
			NS_MAIN,
			// Many templates e.g. the deletion template and other
			// templates that indicate that there's something wrong
			// with the article link to the talk page.
			NS_TALK,
			NS_TEMPLATE,
			NS_CATEGORY
		);

		function getName() { return 'CrossNamespaceLinks'; }
		function isExpensive() { return true; }
		function isSyndicated() { return false; }
		
		function getSQL() {
			$dbr =& wfGetDB( DB_SLAVE );
			extract( $dbr->tableNames( 'page', 'pagelinks' ) );
			$namespaces = implode( ',', $this->namespaces );
			return
				"
				SELECT
					'CrossNamespaceLinks' as type,
					COUNT(*) as namespace,
					page_title as title,
					pl_namespace as value
				FROM $page
				LEFT JOIN $pagelinks ON page_id = pl_from
				WHERE page_is_redirect = 0 AND page_namespace = " . NS_MAIN . " AND pl_namespace NOT IN ($namespaces)
				GROUP BY page_title
				";
		}

		function sortDescending() { return false; }

		function formatResult( $skin, $result ) {
			global $wgContLang, $wgLang;

			$nt = Title::makeTitle( NS_MAIN, $result->title );
			$text = $wgContLang->convert( $nt->getPrefixedText() );

			$plink = $skin->makeKnownLink( $nt->getPrefixedText(), htmlspecialchars( $text ) );

			return wfMsgHtml( 'crossnamespacelinkstext', $plink, $result->namespace, htmlspecialchars( $wgLang->getNsText( $result->value ) ) );
		}
		
	}
	
	SpecialPage::addPage( new CrossNamespaceLinks );
}

function wfSpecialCrossNamespaceLinksHook( &$QueryPages ) {
	$QueryPages[] = array(
		'CrossNamespaceLinksPage',
		'CrossNamespaceLinks',
		false
	);

	return true;
}
