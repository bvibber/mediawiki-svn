<?php

require_once('Expression.php');
require_once('wikidata.php');
require_once('forms.php');
require_once('attribute.php');
require_once('tuple.php');
require_once('relation.php');
require_once('type.php');
require_once('languages.php');
require_once('editor.php');
require_once('HTMLtable.php');

class DefinedMeaningDefinitionController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $languageAttribute, $textAttribute;
		
		$revisionId = getRevisionForExpressionId($keyPath->peek(1)->getAttributeValue($expressionIdAttribute));
		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $tuple->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);
		
		if ($text != "") 
			addDefinedMeaningDefinition($definedMeaningId, $revisionId, $languageId, $text);
	}
	
	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $languageAttribute;
			
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		removeDefinedMeaningDefinition($definedMeaningId, $languageId);
	}
	
	public function update($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $languageAttribute, $textAttribute;
		
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute); 
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);
		
		updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text);
	}
}

class DefinedMeaningAlternativeDefinitionsController {
	public function add($keyPath, $tuple)  {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute;

		$revisionId = getRevisionForExpressionId($keyPath->peek(1)->getAttributeValue($expressionIdAttribute));
		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);		
		$alternativeDefinition = $tuple->getAttributeValue($alternativeDefinitionAttribute);
		
		if ($alternativeDefinition->getTupleCount() > 0) {	
			$definitionTuple = $alternativeDefinition->getTuple(0);
			
			$languageId = $definitionTuple->getAttributeValue($languageAttribute);
			$text = $definitionTuple->getAttributeValue($textAttribute);
			
			if ($text != '')
				addDefinedMeaningAlternativeDefinition($definedMeaningId, $revisionId, $languageId, $text);
		}
	}
	
	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $definitionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);			
		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId);
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningAlternativeDefinitionController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$expressionIdAttribute, $definitionIdAttribute, $languageAttribute, $textAttribute;

		$revisionId = getRevisionForExpressionId($keyPath->peek(2)->getAttributeValue($expressionIdAttribute));
		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		$languageId = $tuple->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);

		if ($text != "")
			addDefinedMeaningAlternativeDefinitionTranslation($definitionId, $revisionId, $languageId, $text);
	}
	
	public function remove($keyPath) {
		global
			$definitionIdAttribute, $languageAttribute;
		
		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		
		removeTranslatedDefinition($definitionId, $languageId);
	}
	
	public function update($keyPath, $tuple) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(1)->getAttributeValue($definitionIdAttribute);
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);

		if ($text != "")
			updateDefinedMeaningAlternativeDefinition($definitionId, $languageId, $text);
	}
}

class SynonymTranslationController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$expressionTuple = $tuple->getAttributeValue($expressionAttribute);
		$languageId = $expressionTuple->getAttributeValue($languageAttribute);		
		$spelling = $expressionTuple->getAttributeValue($spellingAttribute);
		$identicalMeaning = $tuple->getAttributeValue($identicalMeaningAttribute);

		if ($spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $identicalMeaning);
		}
	}
	
	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $expressionIdAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);		
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		removeSynonymOrTranslation($definedMeaningId, $expressionId);		
	}
	
	public function update($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $expressionIdAttribute, $identicalMeaningAttribute;
			
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);		
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		$identicalMeaning = $tuple->getAttributeValue($identicalMeaningAttribute);
		updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning);
	}
}

class DefinedMeaningRelationController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;

		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);		
		$relationTypeId = $tuple->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $tuple->getAttributeValue($otherDefinedMeaningAttribute);
		  
		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}	

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;
			
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);	
		$tuple = $keyPath->peek(0);	
		$relationTypeId = $tuple->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $tuple->getAttributeValue($otherDefinedMeaningAttribute);
		
		removeRelation($definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningAttributeController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $attributeAttribute;
		
		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);
		$attributeId = $tuple->getAttributeValue($attributeAttribute);
		  
		if ($attributeId != 0)
			addRelation($definedMeaningId, 0, $attributeId);
	}	

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $attributeAttribute;
			
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);	
		$attributeId = $keyPath->peek(0)->getAttributeValue($attributeAttribute);	
		
		removeRelation($definedMeaningId, 0, $attributeId);
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningCollectionController implements PageElementController {
	public function add($keyPath, $tuple) {
		global
			$expressionIdAttribute, $definedMeaningIdAttribute, $collectionAttribute, $sourceIdentifierAttribute;

		$revisionId = getRevisionForExpressionId($keyPath->peek(1)->getAttributeValue($expressionIdAttribute));
		$definedMeaningId = $keyPath->peek(0)->getAttributeValue($definedMeaningIdAttribute);		
		$collectionId = $tuple->getAttributeValue($collectionAttribute);
		$internalId = $tuple->getAttributeValue($sourceIdentifierAttribute);
		
		if ($internalId != "")
			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId, $revisionId);
	}	

	public function remove($keyPath) {
		global
			$definedMeaningIdAttribute, $collectionAttribute;
		
		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);	
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionAttribute);
			
		removeDefinedMeaningFromCollection($definedMeaningId, $collectionId);
	}
	
	public function update($keyPath, $tuple) {
		global
			$definedMeaningIdAttribute, $collectionAttribute, $sourceIdentifierAttribute;

		$definedMeaningId = $keyPath->peek(1)->getAttributeValue($definedMeaningIdAttribute);		
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionAttribute);
		$sourceId = $tuple->getAttributeValue($sourceIdentifierAttribute);
		
		if ($sourceId != "")
			updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $sourceId);
	}
}

/**
 * Renders a content page from WiktionaryZ based on the GEMET database.
 * @package MediaWiki
 */
class WiktionaryZ {
	/* TODOs:
		use $dbr->select() instead of $dbr->query() wherever possible; it lets MediaWiki handle additional
		table prefixes and such.
	*/
	function view() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgLanguageNames;

		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		$dbr =& wfGetDB( DB_MASTER );

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));
/*		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);

			$wgOut->addHTML($skin->editSectionLink($wgTitle, $expressionId));
			$wgOut->addHTML("<h2><i>Spelling</i>: $row->spelling - <i>Language:</i> ".$wgLanguageNames[$row->language_id]. "</h2>");

//			$wgOut->addHTML('<ul>');
//			foreach($definedMeaningIds as $definedMeaningId) {
//				$wgOut->addHTML($skin->editSectionLink($wgTitle, "$expressionId-$definedMeaningId"));
//				$this->displayPageElement($this->getDefinedMeaningPageElement($definedMeaningId, $expressionId));
//
//				$wgOut->addHTML('</li>');
//			}

			$this->displayPageElement($this->getDefinedMeaningsPageElement($expressionId));

			$wgOut->addHTML('</ul>');
		}
*/
		$this->displayPageElement($this->getExpressionsPageElement($wgTitle->getText()));
		
		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}
	
	function displayPageElement($pageElement) {
//		addWikiDataBlock($pageElement->getId(), $pageElement->getCaption(), $pageElement->getViewer()->view(new IdStack($pageElement->getId()), $pageElement->getRelation()), false);
		global
			$wgOut;
			
		$wgOut->addHTML($pageElement->getViewer()->view(new IdStack($pageElement->getId()), $pageElement->getRelation()));
	}
	
	function editPageElement($pageElement) {
//		addWikiDataBlock($pageElement->getId(), $pageElement->getCaption(), $pageElement->getEditor()->edit(new IdStack($pageElement->getId()), $pageElement->getRelation()), false); 
		global
			$wgOut;
			
		$wgOut->addHTML($pageElement->getEditor()->edit(new IdStack($pageElement->getId()), $pageElement->getRelation()));
	}

	function savePageElement($pageElement) {
		global
			$wgRequest;

		$pageElement->getEditor()->save(new IdStack($pageElement->getId()), $pageElement->getRelation());		
	}
	
	function getDefinedMeaningDefinitionEditor() {
		global
			$definitionAttribute, $languageAttribute, $textAttribute;
		
		$editor = new TableEditor($definitionAttribute, true, true, false, new DefinedMeaningDefinitionController());
		$editor->addEditor(new LanguageEditor($languageAttribute, false, true));
		$editor->addEditor(new TextEditor($textAttribute, true, true));
		
		return $editor;
	}
	
	function getAlternativeDefinitionsEditor() {
		global
			$alternativeDefinitionsAttribute, $definitionIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute;

		$alternativeDefinitionEditor = new TableEditor($alternativeDefinitionAttribute, true, true, true, new DefinedMeaningAlternativeDefinitionController());
		$alternativeDefinitionEditor->addEditor(new LanguageEditor($languageAttribute, false, true)); 
		$alternativeDefinitionEditor->addEditor(new TextEditor($textAttribute, true, true)); 
				
		$editor = new TableEditor($alternativeDefinitionsAttribute, true, true, false, new DefinedMeaningAlternativeDefinitionsController());
		$editor->addEditor($alternativeDefinitionEditor);
		
		return $editor;
	}
	
	function getSynonymsAndTranslationsEditor() {
		global
			$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute;
		
		$expressionEditor = new TupleTableCellEditor($expressionAttribute);
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, false, true));
		$expressionEditor->addEditor(new SpellingEditor($spellingAttribute, false, true));
			
		$tableEditor = new TableEditor($synonymsAndTranslationsAttribute, true, true, false, new SynonymTranslationController());
		$tableEditor->addEditor($expressionEditor);
		$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, true, true, true));
		
		return $tableEditor;
	}

	function getDefinedMeaningRelationsEditor() {
		global
			$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute;
		
		$editor = new TableEditor($relationsAttribute, true, true, false, new DefinedMeaningRelationController());
		$editor->addEditor(new RelationTypeEditor($relationTypeAttribute, false, true));
		$editor->addEditor(new DefinedMeaningEditor($otherDefinedMeaningAttribute, false, true));
		
		return $editor;
	}
	
	function getDefinedMeaningAttributesEditor() {
		global
			$attributesAttribute, $attributeAttribute;
			
		$editor = new TableEditor($attributesAttribute, true, true, false, new DefinedMeaningAttributeController());
		$editor->addEditor(new AttributeEditor($attributeAttribute, false, true));

		return $editor;
	}
	
	function getDefinedMeaningCollectionMembershipEditor() {
		global
			$collectionMembershipAttribute, $collectionAttribute, $sourceIdentifierAttribute;
		
		$editor = new TableEditor($collectionMembershipAttribute, true, true, false, new DefinedMeaningCollectionController());
		$editor->addEditor(new CollectionEditor($collectionAttribute, false, true));
		$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, true, true));

		return $editor;
	}
	
	function getDefinedMeaningTuple($definedMeaningId, $revisionId, $expressionId) {
		global
			$definedMeaningAttribute, $definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $attributesAttribute, $collectionMembershipAttribute;
				
		$tuple = new ArrayTuple($definedMeaningAttribute->type->getHeading());
		$tuple->setAttributeValue($definitionAttribute, $this->getDefinedMeaningDefinitionRelation($definedMeaningId, $revisionId));
		$tuple->setAttributeValue($alternativeDefinitionsAttribute, $this->getAlternativeDefinitionsRelation($definedMeaningId));
		$tuple->setAttributeValue($synonymsAndTranslationsAttribute, $this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId));
		$tuple->setAttributeValue($relationsAttribute, $this->getDefinedMeaningRelationsRelation($definedMeaningId));
		$tuple->setAttributeValue($attributesAttribute, $this->getDefinedMeaningAttributesRelation($definedMeaningId));
		$tuple->setAttributeValue($collectionMembershipAttribute, $this->getDefinedMeaningCollectionMembershipRelation($definedMeaningId));
		
		return $tuple;
	}

	function getDefinedMeaningsRelation($expressionId) {
		global
			$definedMeaningIdAttribute, $definedMeaningAttribute;

		$revisionId = getRevisionForExpressionId($expressionId);
		$relation = new ArrayRelation(new Heading($definedMeaningIdAttribute, $definedMeaningAttribute), new Heading($definedMeaningIdAttribute));		
		
		$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);

		foreach($definedMeaningIds as $definedMeaningId) 
			$relation->addTuple(array($definedMeaningId, $this->getDefinedMeaningTuple($definedMeaningId, $revisionId, $expressionId)));
			
		return $relation;
	}
	
	function getExpressionsRelation($spelling) {
		global
			$expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $expressionMeaningsAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT expression_id, spelling, language_id from uw_expression_ns WHERE spelling=BINARY " . $dbr->addQuotes($spelling));
		$result = new ArrayRelation(new Heading($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute), new Heading($expressionIdAttribute));		
		$expressionHeading = new Heading($languageAttribute, $spellingAttribute);
	
		while($expression = $dbr->fetchObject($queryResult)) {
			$expressionTuple = new ArrayTuple($expressionHeading);
			$expressionTuple->setAttributeValue($languageAttribute, $expression->language_id);
			$expressionTuple->setAttributeValue($spellingAttribute, $expression->spelling);
			
			$result->addTuple(array($expression->expression_id, $expressionTuple, $this->getDefinedMeaningsRelation($expression->expression_id)));
		}
		
		return $result;
	}
	
	function getExpressionsPageElement($spelling) {
		global
			$expressionsAttribute, $definedMeaningAttribute, $expressionAttribute, $expressionMeaningsAttribute, $languageAttribute, $spellingAttribute;
			
		$definitionEditor = $this->getDefinedMeaningDefinitionEditor();
		$synonymsAndTranslationsEditor = $this->getSynonymsAndTranslationsEditor(); 
		
		$definedMeaningEditor = new TupleListEditor($definedMeaningAttribute, true, true, false, null);
		$definedMeaningEditor->addEditor($definitionEditor);
		$definedMeaningEditor->addEditor($this->getAlternativeDefinitionsEditor());
		$definedMeaningEditor->addEditor($synonymsAndTranslationsEditor);
		$definedMeaningEditor->addEditor($this->getDefinedMeaningRelationsEditor());
		$definedMeaningEditor->addEditor($this->getDefinedMeaningAttributesEditor());
		$definedMeaningEditor->addEditor($this->getDefinedMeaningCollectionMembershipEditor());
		
		$definedMeaningEditor->expandEditor($definitionEditor);
		$definedMeaningEditor->expandEditor($synonymsAndTranslationsEditor);
		
		$expressionMeaningsEditor = new RelationListEditor($expressionMeaningsAttribute, 3);
		$expressionMeaningsEditor->setCaptionEditor(new AttributeLabelViewer($definedMeaningAttribute));
		$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
		
		$expressionEditor = new TupleSpanEditor($expressionAttribute, ': ', ' - ');
		$expressionEditor->addViewer(new LanguageEditor($languageAttribute, false, false));
		$expressionEditor->addViewer(new TextEditor($spellingAttribute, false, false)); 
		
		$expressionsEditor = new RelationListEditor($expressionsAttribute, 2);
		$expressionsEditor->setCaptionEditor($expressionEditor);
		$expressionsEditor->setValueEditor($expressionMeaningsEditor);
		
		return new DefaultPageElement("expression", "Defined meaning",
										$this->getExpressionsRelation($spelling),
										$expressionsEditor, $expressionsEditor, true);
	}
	
	function getAlternativeDefinitions($definedMeaningId) {
		$result = array();
		$dbr =& wfGetDB(DB_SLAVE);	
		$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_alt_meaningtexts WHERE meaning_mid=$definedMeaningId AND is_latest_set=1");
		
		while ($definitionId = $dbr->fetchObject($queryResult))
			$result[] = $definitionId->meaning_text_tcid;
			
		return $result;
	}
	
	function getAlternativeDefinitionsRelation($definedMeaningId) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute, $alternativeDefinitionAttribute;
		
		$alternativeDefinitionHeading = new Heading($languageAttribute, $textAttribute);
		$relation = new ArrayRelation(new Heading($definitionIdAttribute, $alternativeDefinitionAttribute), new Heading($definitionIdAttribute));
		
		$alternativeDefinitions = $this->getAlternativeDefinitions($definedMeaningId);
		
		foreach($alternativeDefinitions as $alternativeDefinition)
			$relation->addTuple(array($alternativeDefinition, $this->getDefinedMeaningAlternativeDefinitionRelation($alternativeDefinition)));
		
		return $relation;
	}
	
	function saveForm() {
		global 
			$wgTitle, $wgUser;
		
//		if (count($sectionArguments) == 0)
//			$this->saveSpellingForm($wgTitle->getText());
//		else {
//			$expressionId = $sectionArguments[0];			
//			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);
//			$this->saveExpressionForm($expressionId, $definedMeaningIds);
//		}
		
		$this->savePageElement($this->getExpressionsPageElement($wgTitle->getText()));

		Title::touchArray(array($wgTitle));
		$now = wfTimestampNow();
		RecentChange::notifyEdit( $now, $wgTitle, false, $wgUser, 'Edited translations, synonyms, definition, or relations',
			0, $now, false, '', 0, 0, 0 );

	}

	function saveSpellingForm($spelling) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT expression_id FROM uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($spelling));

		while ($expression = $dbr->fetchObject($queryResult)) {
			$expressionId = $expression->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$this->saveExpressionForm($expressionId, $definedMeaningIds);			
		}
	}
	
	function saveExpressionForm($expressionId, $definedMeaningIds) {
		$synonymsAndTranslationIds = $this->getSynonymAndTranslationIds($definedMeaningIds, $expressionId);

		foreach($definedMeaningIds as $definedMeaningId) 
			$this->saveDefinedMeaningForm($expressionId, $definedMeaningId);
	}
	
	function saveDefinedMeaningForm($expressionId, $definedMeaningId) {
		$this->savePageElement($this->getDefinedMeaningPageElement($definedMeaningId, $expressionId));	
	}

	function edit() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgRequest;
		
//		$sectionToEdit = $wgRequest->getText('section');
//		
//		if ($sectionToEdit != "")
//			$sectionArguments = explode("-", $sectionToEdit);
//		else
//			$sectionArguments = array();
//		
		if ($wgRequest->getText('save') != '')
			$this->saveForm();
//					
//		$userlang = $wgUser->getOption('language');
		$skin = $wgUser->getSkin();

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));
		$wgOut->addHTML('<form method="post" action="">');

		$this->editPageElement($this->getExpressionsPageElement($wgTitle->getText()));
//		if (count($sectionArguments) == 0)
//			$this->displaySpellingEditForm($wgTitle->getText());
//		else {
//			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);							
//			$this->displayPartialEditForm($sectionArguments[0], $definedMeaningIds);
//		}

		$wgOut->addHTML(getSubmitButton("save", "Save"));
		$wgOut->addHTML('</form>');
	}
	
	function getDefinedMeaningIdsForSectionArguments($sectionArguments) {
		if (count($sectionArguments) >= 2) 
			return array($sectionArguments[1]);
		else
			return $this->getDefinedMeaningsForExpression($sectionArguments[0]);
	}
	
	function displaySpellingEditForm($spelling) {
		global
			$wgOut;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT expression_id, spelling, language_id FROM uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($spelling));

		while ($expression = $dbr->fetchObject($queryResult)) {
			$expressionId = $expression->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);
			$this->displayExpressionEditForm($expression->spelling, $expressionId, $expression->language_id, $definedMeaningIds);			
		}
	}
	
	function displayPartialEditForm($expressionId, $definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT spelling, language_id FROM uw_expression_ns WHERE expression_id=$expressionId");
		
		if ($expression = $dbr->fetchObject($queryResult))
			$this->displayExpressionEditForm($expression->spelling, $expressionId, $expression->language_id, $definedMeaningIds);
	}
	
	function displayExpressionEditForm($spelling, $expressionId, $languageId, $definedMeaningIds) {
		global
			$wgOut, $wgLanguageNames;
		
		$wgOut->addHTML("<h2><i>Spelling:</i>" . $spelling . " - <i>Language:</i> ".$wgLanguageNames[$languageId]."</h2>");

		$wgOut->addHTML('<ul>');
		foreach ($definedMeaningIds as $definedMeaningId) {
			$wgOut->addHTML('<li>');			
			$this->displayDefinedMeaningEditForm($definedMeaningId, $expressionId);
			$wgOut->addHTML('</li>');
		}
		$wgOut->addHTML('</ul>');
	}
	
	function displayDefinedMeaningEditForm($definedMeaningId, $expressionId) {
		$this->editPageElement($this->getDefinedMeaningPageElement($definedMeaningId, $expressionId));
	}
	
	function getDefinedMeaningsForExpression($expressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeanings = array();
		$queryResult = $dbr->query("SELECT defined_meaning_id FROM uw_syntrans WHERE expression_id=$expressionId");
		
		while($definedMeaning = $dbr->fetchObject($queryResult)) 
			$definedMeanings[] = $definedMeaning->defined_meaning_id;
			
		return $definedMeanings;
	}
	
	function getSynonymAndTranslationIds($definedMeaningIds, $skippedExpressionId) {
		$dbr =& wfGetDB(DB_SLAVE);
		$synonymAndTranslationIds = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$queryResult = $dbr->query("SELECT expression_id from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id!=$skippedExpressionId");
		
			while($synonymOrTranslation = $dbr->fetchObject($queryResult)) 
				$synonymAndTranslationIds[$definedMeaningId][] = $synonymOrTranslation->expression_id;
		}
			
		return $synonymAndTranslationIds;
	}

	function getDefinedMeaningDefinitionRelation($definedMeaningId) {
		global
			$languageAttribute, $textAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);

		$relation = new ArrayRelation(new Heading($languageAttribute, $textAttribute), 
										new Heading($languageAttribute));
										
		$queryResult = $dbr->query("SELECT language_id, old_text FROM uw_defined_meaning df, translated_content tc, text t WHERE df.defined_meaning_id=$definedMeaningId AND df.is_latest_ver=1 ".
									"AND tc.set_id=df.meaning_text_tcid AND tc.text_id=t.old_id AND tc.is_latest_set=1");
									
		while ($translatedDefinition = $dbr->fetchObject($queryResult)) 
			$relation->addTuple(array($translatedDefinition->language_id, $translatedDefinition->old_text));
		
		return $relation;
	}
	
	function getDefinedMeaningAlternativeDefinitionRelation($alternativeDefinitionId) {
		global
			$languageAttribute, $textAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);

		$relation = new ArrayRelation(new Heading($languageAttribute, $textAttribute), 
										new Heading($languageAttribute));
										
		$queryResult = $dbr->query("SELECT language_id, old_text FROM translated_content tc, text t WHERE ".
									"tc.set_id=$alternativeDefinitionId AND tc.text_id=t.old_id AND tc.is_latest_set=1");
									
		while ($translatedDefinition = $dbr->fetchObject($queryResult)) 
			$relation->addTuple(array($translatedDefinition->language_id, $translatedDefinition->old_text));
		
		return $relation;
	}
	
	function getSynonymAndTranslationRelation($definedMeaningId, $skippedExpressionId) {
		global
			$expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);

		$expressionHeading = $expressionAttribute->type->getHeading();
		$relation = new ArrayRelation(new Heading($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute), new Heading($expressionIdAttribute));
		$queryResult = $dbr->query("SELECT uw_expression_ns.expression_id, spelling, language_id, endemic_meaning FROM uw_syntrans, uw_expression_ns WHERE uw_syntrans.defined_meaning_id=$definedMeaningId AND uw_syntrans.expression_id!=$skippedExpressionId " .
									"AND uw_expression_ns.expression_id=uw_syntrans.expression_id AND uw_expression_ns.is_latest=1");

		while($synonymOrTranslation = $dbr->fetchObject($queryResult)) {
			$expressionTuple = new ArrayTuple($expressionHeading);
			$expressionTuple->setAttributeValuesByOrder(array($synonymOrTranslation->language_id, $synonymOrTranslation->spelling));

			$relation->addTuple(array($synonymOrTranslation->expression_id, $expressionTuple, $synonymOrTranslation->endemic_meaning));
		}
		
		return $relation;
	}
	
	function getDefinedMeaningRelationsRelation($definedMeaningId) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
			
		$heading = new Heading($relationTypeAttribute, $otherDefinedMeaningAttribute);
		$relation = new ArrayRelation($heading, $heading);
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0 and is_latest_set=1 ORDER BY relationtype_mid");
			
		while($definedMeaningRelation = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid)); 
		
		return $relation;
	}
	
	function getDefinedMeaningCollectionMembershipRelation($definedMeaningId) {
		global
			$collectionAttribute, $sourceIdentifierAttribute;
			
		$heading = new Heading($collectionAttribute, $sourceIdentifierAttribute);
		$relation = new ArrayRelation($heading, new Heading($collectionAttribute));
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT collection_id, internal_member_id FROM uw_collection_contents WHERE member_mid=$definedMeaningId AND is_latest_set=1");
			
		while($collection = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($collection->collection_id, $collection->internal_member_id)); 
		
		return $relation;
	}
	
	function getDefinedMeaningAttributesRelation($definedMeaningId) {
		global
			$attributeAttribute;
			
		$heading = new Heading($attributeAttribute);
		$relation = new ArrayRelation($heading, $heading);
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid=0 and is_latest_set=1");
			
		while($attribute = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($attribute->meaning2_mid)); 
		
		return $relation;
	}
	
	function getDefinedMeaningRelations($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
	    $definedMeaningRelations = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$relations = array();
			$queryResult = $dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0 and is_latest_set=1");
			
			while($definedMeaningRelation = $dbr->fetchObject($queryResult)) 
				$relations[$definedMeaningRelation->relationtype_mid][] = $definedMeaningRelation->meaning2_mid;
						
			$definedMeaningRelations[$definedMeaningId] = $relations;
		}
		
		return $definedMeaningRelations;
	}

	function getDefinedMeaningAttributes($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$definedMeaningAttributes = array();
		
		foreach($definedMeaningIds as $definedMeaningId) {
			$attributes = array();
			$queryResult = $dbr->query("SELECT * from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid=0 and is_latest_set=1");
			
			while($attribute = $dbr->fetchObject($queryResult)) 
				$attributes[] = $attribute->meaning2_mid;
			
			$definedMeaningAttributes[$definedMeaningId] = $attributes;
		}

		return $definedMeaningAttributes;	
	}
	
	function getExpressionForMeaningId($mid, $langcode) {
//		$dbr =& wfGetDB(DB_SLAVE);
//		$sql="SELECT spelling from uw_syntrans,uw_expression_ns where defined_meaning_id=".$mid." and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=".$langcode." limit 1";
//		$sp_res=$dbr->query($sql);
//		$sp_row=$dbr->fetchObject($sp_res);
//		return $sp_row->spelling;
		$expressions = $this->getExpressionsForDefinedMeaningIds(array($mid)); 
		return $expressions[$mid];
	}
	
	# Fixme, the following function only returns English expressions
	# Should be expressions in the language of preference, with an appropriate fallback scheme
	function getExpressionsForDefinedMeaningIds($definedMeaningIds) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT defined_meaning_id, spelling from uw_syntrans, uw_expression_ns where defined_meaning_id in (". implode(",", $definedMeaningIds) . ") and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.language_id=85 and uw_syntrans.endemic_meaning=1");
		$expressions = array();
		
		while ($expression = $dbr->fetchObject($queryResult)) 
			if (!array_key_exists($expression->defined_meaning_id, $expressions))
				$expressions[$expression->defined_meaning_id] = $expression->spelling;
		
		return $expressions;
	}
}

?>
