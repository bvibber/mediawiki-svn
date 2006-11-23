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
				$wgOut, $wgTitle, $wgRequest;

			$spelling = ltrim($_GET['search-text']);
			
			require_once("forms.php");
			require_once("type.php");
			require_once("Expression.php");

			if (isset($_GET['go'])) {
				global
					$wgScript;

				$wgOut->redirect($wgScript . '/WiktionaryZ:' . $spelling);
			}
			else {			
				$fulltext = $_GET['fulltext'];
				$collectionId = $wgRequest->getInt("collection");
				
				$wgOut->addHTML(getOptionPanel(array(
					'Search text' => getTextBox('search-text', $_GET['search-text']),
					'Collection' => getSuggest('collection', 'collection', $collectionId, collectionIdAsText($collectionId))
				)));
							
				$wgOut->addHTML('<h1>Words matching <i>'. $spelling . '</i> and associated meanings</h1>');
				$wgOut->addHTML('<p>Showing only a maximum of 100 matches.</p>');
				$wgOut->addHTML($this->searchText($spelling, $collectionId));
			}
		}
		
		function getSpellingRestriction($spelling) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			if (trim($spelling) != '')
				return " AND spelling LIKE " . $dbr->addQuotes("%$spelling%");
			else
				return "";
		}
		
		function getSpellingOrderBy($spelling) {
			if (trim($spelling) != '')
				return "position ASC, ";
			else
				return "";
		}
		
		function getPositionSelectColumn($spelling) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			if (trim($spelling) != '')
				return "INSTR(LCASE(uw_expression_ns.spelling), LCASE(". $dbr->addQuotes("$spelling") .")) as position, ";
			else
				return "";
		}
		
		function searchText($text, $collectionId) {
			require_once("Search.php");			
			
			$dbr = &wfGetDB(DB_SLAVE);
			
			$sql = 
				"SELECT ". $this->getPositionSelectColumn($text) ." uw_syntrans.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS spelling, uw_expression_ns.language_id AS language_id ".
				"FROM uw_expression_ns, uw_syntrans ";
					
			if ($collectionId > 0)
				$sql .= ", uw_collection_contents ";
				
			$sql .=
		    	"WHERE uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_syntrans.identical_meaning=1 " .
				" AND " . getLatestTransactionRestriction('uw_syntrans').
				" AND " . getLatestTransactionRestriction('uw_expression_ns').
				$this->getSpellingRestriction($text);
				
			if ($collectionId > 0)
				$sql .= 
					" AND uw_collection_contents.member_mid=uw_syntrans.defined_meaning_id " .
					" AND uw_collection_contents.collection_id=" . $collectionId .
					" AND " . getLatestTransactionRestriction('uw_collection_contents');
			
			$sql .=
				" ORDER BY " . $this->getSpellingOrderBy($text) . "uw_expression_ns.spelling ASC limit 100";
			
			$queryResult = $dbr->query($sql);
			list($relation, $editor) = getSearchResultAsRecordSet($queryResult);
			return $editor->view(new IdStack("expression"), $relation);
		}			
	}
	
	SpecialPage::addPage(new SpecialDatasearch());
}

?>