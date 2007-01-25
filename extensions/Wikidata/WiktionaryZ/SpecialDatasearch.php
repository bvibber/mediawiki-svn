<?php

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialDatasearch';

function wfSpecialDatasearch() {
	class SpecialDatasearch extends SpecialPage {
		protected $externalIdentifierAttribute;
		protected $collectionAttribute;
		protected $collectionMemberAttribute;
		protected $spellingAttribute;
		protected $languageAttribute;
		
		protected $expressionStructure;
		protected $expressionAttribute;
		
		protected $definedMeaningAttribute;
		protected $definitionAttribute;
		
		protected $meaningStructure;
		protected $meaningAttribute;
		
		function SpecialDatasearch() {
			SpecialPage::SpecialPage('Datasearch');

			require_once("forms.php");
			require_once("type.php");
			require_once("Expression.php");
			require_once("WiktionaryZAttributes.php");
			require_once("WiktionaryZRecordSets.php");
			require_once("WiktionaryZEditors.php");

			initializeWiktionaryZAttributes(false, false);

			global
				$definedMeaningReferenceType;
			
			$this->spellingAttribute = new Attribute("found-word", "Found word", "short-text");
			$this->languageAttribute = new Attribute("language", "Language", "language");
			
			$this->expressionStructure = new Structure($this->spellingAttribute, $this->languageAttribute);
			$this->expressionAttribute = new Attribute("expression", "Expression", new RecordType($this->expressionStructure));
			
			$this->definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", $definedMeaningReferenceType);
			$this->definitionAttribute = new Attribute("definition", "Definition", "definition");
			
			$this->meaningStructure = new Structure($this->definedMeaningAttribute, $this->definitionAttribute);
			$this->meaningAttribute = new Attribute("meaning", "Meaning", new RecordSetType($this->meaningStructure));

			$this->externalIdentifierAttribute = new Attribute("external-identifier", "External identifier", "short-text");
			$this->collectionAttribute = new Attribute("collection", "Collection", $definedMeaningReferenceType);
			$this->collectionMemberAttribute = new Attribute("collection-member", "Collection member", $definedMeaningReferenceType);
		}
		
		function execute( $par ) {
			global
				$wgOut, $wgTitle, $wgRequest;

			$spelling = ltrim($_GET['search-text']);
			
			if (isset($_GET['go'])) {
				global
					$wgScript;

				$wgOut->redirect($wgScript . '/WiktionaryZ:' . $spelling);
			}
			else {			
				$fulltext = $_GET['fulltext'];
				$collectionId = $wgRequest->getInt("collection");
				$languageId = $wgRequest->getInt("language");
				$withinWords = $wgRequest->getBool('within-words');
				$withinExternalIdentifiers = $wgRequest->getBool('within-external-identifiers');
				
				if (!$withinWords && !$withinExternalIdentifiers)
					$withinWords = true;
				
				$languageName = languageIdAsText($languageId); 

				$wgOut->addHTML(getOptionPanel(array(
					'Search text' => getTextBox('search-text', $_GET['search-text']),
					'Language' => getSuggest('language', "language", array(), $languageId, $languageName),
					'Collection' => getSuggest('collection', 'collection', array(), $collectionId, collectionIdAsText($collectionId)),
					'Within words' => getCheckBox('within-words', $withinWords),
					'Within external identifiers' => getCheckBox('within-external-identifiers', $withinExternalIdentifiers)
				)));
							
				if ($withinWords) {
					if ($languageId != 0)
						$languageText = " in <i>" . $languageName . "</i> ";
					else
						$languageText = " ";
						
					$wgOut->addHTML('<h1>Words' . $languageText . 'matching <i>'. $spelling . '</i> and associated meanings</h1>');
					$wgOut->addHTML('<p>Showing only a maximum of 100 matches.</p>');
				
					$wgOut->addHTML($this->searchWords($spelling, $collectionId, $languageId));
				}
				
				if ($withinExternalIdentifiers) {
					$wgOut->addHTML('<h1>External identifiers matching <i>'. $spelling . '</i></h1>');
					$wgOut->addHTML('<p>Showing only a maximum of 100 matches.</p>');

					$wgOut->addHTML($this->searchExternalIdentifiers($spelling, $collectionId));
				}
			}
		}
		
		function getSpellingRestriction($spelling, $tableColumn) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			if (trim($spelling) != '')
				return " AND " . $tableColumn . " LIKE " . $dbr->addQuotes("%$spelling%");
			else
				return "";
		}
		
		function getSpellingOrderBy($spelling) {
			if (trim($spelling) != '')
				return "position ASC, ";
			else
				return "";
		}
		
		function getPositionSelectColumn($spelling, $tableColumn) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			if (trim($spelling) != '')
				return "INSTR(LCASE(" . $tableColumn . "), LCASE(". $dbr->addQuotes("$spelling") .")) as position, ";
			else
				return "";
		}
		
		function searchWords($text, $collectionId, $languageId) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			$sql = 
				"SELECT ". $this->getPositionSelectColumn($text, "uw_expression_ns.spelling") ." uw_syntrans.defined_meaning_id AS defined_meaning_id, uw_expression_ns.spelling AS spelling, uw_expression_ns.language_id AS language_id ".
				"FROM uw_expression_ns, uw_syntrans ";
					
			if ($collectionId > 0)
				$sql .= ", uw_collection_contents ";
				
			$sql .=
		    	"WHERE uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_syntrans.identical_meaning=1 " .
				" AND " . getLatestTransactionRestriction('uw_syntrans').
				" AND " . getLatestTransactionRestriction('uw_expression_ns').
				$this->getSpellingRestriction($text, 'spelling');
				
			if ($collectionId > 0)
				$sql .= 
					" AND uw_collection_contents.member_mid=uw_syntrans.defined_meaning_id " .
					" AND uw_collection_contents.collection_id=" . $collectionId .
					" AND " . getLatestTransactionRestriction('uw_collection_contents');
					
			if ($languageId > 0)
				$sql .= 
					" AND uw_expression_ns.language_id=$languageId";
			
			$sql .=
				" ORDER BY " . $this->getSpellingOrderBy($text) . "uw_expression_ns.spelling ASC limit 100";
			
			$queryResult = $dbr->query($sql);
			$recordSet = $this->getWordsSearchResultAsRecordSet($queryResult);
			$editor = $this->getWordsSearchResultEditor(); 
			
			return $editor->view(new IdStack("words"), $recordSet);
		}	
		
		function getWordsSearchResultAsRecordSet($queryResult) {
			global
				$idAttribute;
		
			$dbr =& wfGetDB(DB_SLAVE);
			$recordSet = new ArrayRecordSet(new Structure($idAttribute, $this->expressionAttribute, $this->meaningAttribute), new Structure($idAttribute));
			
			while ($row = $dbr->fetchObject($queryResult)) {
				$expressionRecord = new ArrayRecord($this->expressionStructure);
				$expressionRecord->setAttributeValue($this->spellingAttribute, $row->spelling);
				$expressionRecord->setAttributeValue($this->languageAttribute, $row->language_id);
				
				$meaningRecord = new ArrayRecord($this->meaningStructure);
				$meaningRecord->setAttributeValue($this->definedMeaningAttribute, getDefinedMeaningReferenceRecord($row->defined_meaning_id));
				$meaningRecord->setAttributeValue($this->definitionAttribute, getDefinedMeaningDefinition($row->defined_meaning_id));
		
				$recordSet->addRecord(array($row->defined_meaning_id, $expressionRecord, $meaningRecord));
			}
			
			return $recordSet;			
		}
		
		function getWordsSearchResultEditor() {
			$expressionEditor = new RecordTableCellEditor($this->expressionAttribute);
			$expressionEditor->addEditor(new SpellingEditor($this->spellingAttribute, new SimplePermissionController(false), false));
			$expressionEditor->addEditor(new LanguageEditor($this->languageAttribute, new SimplePermissionController(false), false));
		
			$meaningEditor = new RecordTableCellEditor($this->meaningAttribute);
			$meaningEditor->addEditor(new DefinedMeaningReferenceEditor($this->definedMeaningAttribute, new SimplePermissionController(false), false));
			$meaningEditor->addEditor(new TextEditor($this->definitionAttribute, new SimplePermissionController(false), false, true, 75));
		
			$editor = createTableViewer(null);
			$editor->addEditor($expressionEditor);
			$editor->addEditor($meaningEditor);
			
			return $editor;
		}
		
		function searchExternalIdentifiers($text, $collectionId) {
			$dbr = &wfGetDB(DB_SLAVE);
			
			$sql = 
				"SELECT ". $this->getPositionSelectColumn($text, 'uw_collection_contents.internal_member_id') ." uw_collection_contents.member_mid AS member_mid, uw_collection_contents.internal_member_id AS external_identifier, uw_collection_ns.collection_mid AS collection_mid ".
				"FROM uw_collection_contents, uw_collection_ns ";
					
			$sql .=
		    	"WHERE uw_collection_ns.collection_id=uw_collection_contents.collection_id " .
				" AND " . getLatestTransactionRestriction('uw_collection_ns').
				" AND " . getLatestTransactionRestriction('uw_collection_contents').
				$this->getSpellingRestriction($text, 'uw_collection_contents.internal_member_id');
				
			if ($collectionId > 0)
				$sql .= 
					" AND uw_collection_ns.collection_id=$collectionId ";
			
			$sql .=
				" ORDER BY " . $this->getSpellingOrderBy($text) . "uw_collection_contents.internal_member_id ASC limit 100";
			
			$queryResult = $dbr->query($sql);
			$recordSet = $this->getExternalIdentifiersSearchResultAsRecordSet($queryResult);
			$editor = $this->getExternalIdentifiersSearchResultEditor();

			return $editor->view(new IdStack("external-identifiers"), $recordSet);
		}		
		
		function getExternalIdentifiersSearchResultAsRecordSet($queryResult) {
			$dbr =& wfGetDB(DB_SLAVE);
		
			$externalIdentifierMatchStructure = new Structure($this->externalIdentifierAttribute, $this->collectionAttribute, $this->collectionMemberAttribute);			
			$recordSet = new ArrayRecordSet($externalIdentifierMatchStructure, new Structure($this->externalIdentifierAttribute));
			
			while ($row = $dbr->fetchObject($queryResult)) { 
				$record = new ArrayRecord($this->externalIdentifierMatchStructure);
				$record->setAttributeValue($this->externalIdentifierAttribute, $row->external_identifier);
				$record->setAttributeValue($this->collectionAttribute, $row->collection_mid);
				$record->setAttributeValue($this->collectionMemberAttribute, $row->member_mid);
				
				$recordSet->add($record);
			}
			
			expandDefinedMeaningReferencesInRecordSet($recordSet, array($this->collectionAttribute, $this->collectionMemberAttribute));

			return $recordSet;
		}
		
		function getExternalIdentifiersSearchResultEditor() {
			$editor = createTableViewer(null);
			$editor->addEditor(createShortTextViewer($this->externalIdentifierAttribute));
			$editor->addEditor(createDefinedMeaningReferenceViewer($this->collectionMemberAttribute));
			$editor->addEditor(createDefinedMeaningReferenceViewer($this->collectionAttribute));
		
			return $editor;		
		}
	}
	
	SpecialPage::addPage(new SpecialDatasearch());
}

?>