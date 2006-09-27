<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialDatasearch';

function wfSpecialDatasearch() {
	class SpecialDatasearch extends SpecialPage {
		function SpecialDatasearch() {
			SpecialPage::SpecialPage('Datasearch');
		}
		
		function execute( $par ) {
		global
			$wgOut, $wgTitle;

			$spelling = ltrim($_GET['search-text']);

			//possible to make a difference between Go and Search, now both have the same result
			$go = $_GET['go'];
			$fulltext = $_GET['fulltext'];
			
			$wgOut->addHTML('<h1>Words matching <i>'. $spelling . '</i> and associated meanings</h1>');
			$wgOut->addHTML('<p>Showing only a maximum of 100 matches.</p>');
			$wgOut->addHTML($this->searchText($spelling));
		}
		
		function searchText($text) {
			require_once("Search.php");			
			
			$dbr = &wfGetDB(DB_SLAVE);
			
			$sql = "SELECT INSTR(LCASE(uw_expression_ns.spelling), LCASE(". $dbr->addQuotes("$text") .")) as position, uw_syntrans.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS spelling, uw_expression_ns.language_id AS language_id ".
					"FROM uw_expression_ns, uw_syntrans ".
		            "WHERE uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_syntrans.endemic_meaning=1 " .
		            " AND " . getLatestTransactionRestriction('uw_syntrans').
					" AND spelling LIKE " . $dbr->addQuotes("%$text%") .
					" ORDER BY position ASC, uw_expression_ns.spelling ASC limit 100";
			
			$queryResult = $dbr->query($sql);
			list($relation, $editor) = getDefinedMeaningAsRelation($queryResult);
			return $editor->view(new IdStack("expression"), $relation);
		}			
	}
	
	SpecialPage::addPage(new SpecialDatasearch());
}

?>