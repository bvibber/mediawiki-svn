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
				
				if ($collectionId > 0) 
					$collectionLabel = definedMeaningExpression(getCollectionMeaningId($collectionId));
				else
					$collectionLabel = "";
				
				$wgOut->addHTML(
					'<div class="option-panel">' .
						'<form method="GET" action="">'.
							'<table cellpadding="0" cellspacing="0">'.
								'<input type="hidden" name="title" value="'. $wgTitle->getNsText() . ':' . $wgTitle->getText() .'"/>'.
								'<tr><th>Search text:</th><td class="option-field">'. getTextBox("search-text", $_GET["search-text"]) . '</td></tr>'.
								'<tr><th>Collection:</th><td class="option-field">'. getSuggest("collection", "collection", $collectionId, $collectionLabel) . '</td></tr>'.
	//							'<tr><th>Show record life span:</th><td class="option-field">'. getCheckBox("show-record-life-span", $this->showRecordLifeSpan) . '</td></tr>'.
	//							'<tr><th>Show most recent version only:</th><td class="option-field">'. getCheckBox("show-most-recent-version-only", isset($_GET["show-most-recent-version-only"])) . '</td></tr>'.
								'<tr><th/><td>'. getSubmitButton("show", "Show"). '</td></tr>'.
							'</table>'.
						'</form>'.
					'</div>'
				);
							
				$wgOut->addHTML('<h1>Words matching <i>'. $spelling . '</i> and associated meanings</h1>');
				$wgOut->addHTML('<p>Showing only a maximum of 100 matches.</p>');
				$wgOut->addHTML($this->searchText($spelling, $collectionId));
			}
		}
		
		function searchText($text, $collectionId) {
			require_once("Search.php");			
			
			$dbr = &wfGetDB(DB_SLAVE);
			
			$sql = 
				"SELECT INSTR(LCASE(uw_expression_ns.spelling), LCASE(". $dbr->addQuotes("$text") .")) as position, uw_syntrans.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS spelling, uw_expression_ns.language_id AS language_id ".
				"FROM uw_expression_ns, uw_syntrans ";
					
			if ($collectionId > 0)
				$sql .= ", uw_collection_contents ";
				
			$sql .=
		    	"WHERE uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_syntrans.identical_meaning=1 " .
				" AND " . getLatestTransactionRestriction('uw_syntrans').
				" AND " . getLatestTransactionRestriction('uw_expression_ns').
				" AND spelling LIKE " . $dbr->addQuotes("%$text%");
				
			if ($collectionId > 0)
				$sql .= 
					" AND uw_collection_contents.member_mid=uw_syntrans.defined_meaning_id " .
					" AND uw_collection_contents.collection_id=" . $collectionId .
					" AND " . getLatestTransactionRestriction('uw_collection_contents');
			
			$sql .=
				" ORDER BY position ASC, uw_expression_ns.spelling ASC limit 100";
			
			$queryResult = $dbr->query($sql);
			list($relation, $editor) = getDefinedMeaningAsRelation($queryResult);
			return $editor->view(new IdStack("expression"), $relation);
		}			
	}
	
	SpecialPage::addPage(new SpecialDatasearch());
}

?>