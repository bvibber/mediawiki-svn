<?php

require_once('wikidata.php');
require_once('Expression.php');
require_once('forms.php');
require_once('attribute.php');
require_once('tuple.php');
require_once('relation.php');
require_once('type.php');
require_once('languages.php');

function getLatestRevisionForDefinedMeaning($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$sql = "SELECT revision_id from uw_defined_meaning where defined_meaning_id=$definedMeaningId and is_latest_ver=1 limit 1";
	$queryResult = $dbr->query($sql);
	
	return $dbr->fetchObject($queryResult)->revision_id;
}
	
function relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_meaning_relations WHERE set_id=$setId AND meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId AND is_latest_set=1");
	
	return $dbr->numRows($queryResult) > 0;
}

function getSetIdForDefinedMeaningRelations($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$sql = "SELECT set_id from uw_meaning_relations where meaning1_mid=$definedMeaningId and is_latest_set=1 limit 1";
	$queryResult = $dbr->query($sql);
			
	$setId = $dbr->fetchObject($queryResult)->set_id;
	
	if (!$setId) {
		$sql = "SELECT max(set_id) as max_id from uw_meaning_relations";
		$queryResult = $dbr->query($sql);
		$setId = $dbr->fetchObject($queryResult)->max_id + 1;
	}
	
	return $setId;		
}

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$setId = getSetIdForDefinedMeaningRelations($definedMeaning1Id);
	$revisionId = getLatestRevisionForDefinedMeaning($definedMeaning1Id);
	
	if (!relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) {
		$dbr =& wfGetDB(DB_MASTER);
		$sql = "insert into uw_meaning_relations(set_id, meaning1_mid, meaning2_mid, relationtype_mid, is_latest_set, first_set, revision_id) " .
				"values($setId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, 1, $setId, $revisionId)";
		$dbr->query($sql);
	}
}

function removeRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("delete from uw_meaning_relations where meaning1_mid=$definedMeaning1Id and meaning2_mid=$definedMeaning2Id and ".
				"relationtype_mid=$relationTypeId AND is_latest_set=1 LIMIT 1");
}

function removeSynonymOrTranslation($definedMeaningId, $expressionId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("delete from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id=$expressionId and ".
				"is_latest_set=1 LIMIT 1");
}

function updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	$dbr =& wfGetDB(DB_MASTER);
	$identicalMeaningInteger = (int) $identicalMeaning;
	$dbr->query("UPDATE uw_syntrans SET endemic_meaning=$identicalMeaningInteger WHERE defined_meaning_id=$definedMeaningId and expression_id=$expressionId and ".
				"is_latest_set=1 LIMIT 1");	
}

function updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning dm, translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE dm.defined_meaning_id=$definedMeaningId AND dm.is_latest_ver=1 ".
									"AND tc.set_id=dm.meaning_text_tcid AND tc.language_id=$languageId AND tc.text_id=t.old_id AND tc.is_latest_set=1");	
}

function updateDefinedMeaningAlternativeDefinition($alternativeDefinitionId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE ".
									"tc.set_id=$alternativeDefinitionId AND tc.language_id=$languageId AND tc.text_id=t.old_id AND tc.is_latest_set=1");	
}
 
function createText($text) {
	$dbr = &wfGetDB(DB_MASTER);
	$text = $dbr->addQuotes($text);
	$sql = "insert into text(old_text) values($text)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function createTranslatedContent($setId, $languageId, $textId, $revisionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "insert into translated_content(set_id,language_id,text_id,first_set,revision_id,is_latest_set) values($setId, $languageId, $textId, $setId, $revisionId, 1)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function translatedDefinitionExists($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM translated_content WHERE set_id=$definitionId AND language_id=$languageId AND is_latest_set=1");

	return $dbr->numRows($queryResult) > 0;	
}

function addTranslatedDefinition($definitionId, $languageId, $definition, $revisionId) {
	if (!translatedDefinitionExists($definitionId, $languageId, $revisionId)) {	
		$textId = createText($definition);
		createTranslatedContent($definitionId, $languageId, $textId, $revisionId);
	}
}

function getDefinedMeaningDefinitionId($definedMeaningId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=$definedMeaningId AND is_latest_ver=1");

	return $dbr->fetchObject($queryResult)->meaning_text_tcid;
}

function updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning SET meaning_text_tcid=$definitionId WHERE defined_meaning_id=$definedMeaningId AND is_latest_ver=1");
}

function newTranslatedContentId() {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM translated_content");
	
	return $dbr->fetchObject($queryResult)->max_id + 1;
}

function addDefinedMeaningDefinition($definedMeaningId, $revisionId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId == 0) {
		$definitionId = newTranslatedContentId();		
		addTranslatedDefinition($definitionId, $languageId, $text, $revisionId);
		updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId);
	}
	else 
		addTranslatedDefinition($definitionId, $languageId, $text, $revisionId);
}

function addDefinedMeaningAlternativeDefinition($alternativeDefinitionId, $revisionId, $languageId, $text) {
	addTranslatedDefinition($alternativeDefinitionId, $languageId, $text, $revisionId);
}

function removeTranslatedDefinition($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE tc, t FROM translated_content AS tc, text AS t WHERE tc.set_id=$definitionId AND tc.language_id=$languageId AND tc.is_latest_set=1 AND tc.text_id=t.old_id");
}

function removeDefinedMeaningDefinition($definedMeaningId, $languageId) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		removeTranslatedDefinition($definitionId, $languageId);
}

function definedMeaningInCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");
	
	return $dbr->numRows($queryResult) > 0;
}

function getCollectionSetId($collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT set_id FROM uw_collection_contents WHERE collection_id=$collectionId AND is_latest_set=1 LIMIT 1");
	$result = $dbr->fetchObject($queryResult)->set_id;
	
	if ($result == 0) {
		$queryResult = $dbr->query("SELECT max(set_id) as max_set_id FROM uw_collection_contents");
		$result = fetchObject($queryResult)->max_set_id;
	}
	
	return $result;
}

function addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId, $revisionId) {
	if (!definedMeaningInCollection($definedMeaningId, $collectionId)) {
		$setId = getCollectionSetId($collectionId);		
		$dbr = &wfGetDB(DB_MASTER);
		$dbr->query("INSERT INTO uw_collection_contents(set_id, collection_id, member_mid, is_latest_set, first_set, revision_id, internal_member_id) " .
						"VALUES ($setId, $collectionId, $definedMeaningId, 1, $setId, $revisionId, ". $dbr->addQuotes($internalId) .")");
	}
}

function removeDefinedMeaningFromCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");	
}

function updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $internalId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_collection_contents SET internal_member_id=".$dbr->addQuotes($internalId) . 
				" WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");	
}

class DefinedMeaningDefinitionController implements PageElementController {
	protected $definedMeaningId;
	protected $revisionId;
	
	public function __construct($definedMeaningId, $revisionId) {
		$this->definedMeaningId = $definedMeaningId;
		$this->revisionId = $revisionId;
	}

	public function add($values) {
		$languageId = $values[0];
		$text = $values[1];
		
		if ($text != "") 
			addDefinedMeaningDefinition($this->definedMeaningId, $this->revisionId, $languageId, $text);
	}
	
	public function remove($tuple) {
		global
			$languageAttribute;
			
		$languageId = $tuple->getAttributeValue($languageAttribute);
		removeDefinedMeaningDefinition($this->definedMeaningId, $languageId);
	}
	
	public function update($tuple, $updatedValues) {
		global
			$languageAttribute;
			
		updateDefinedMeaningDefinition($this->definedMeaningId, $tuple->getAttributeValue($languageAttribute), $updatedValues['text']);
	}
}

class DefinedMeaningAlternativeDefinitionController implements PageElementController {
	protected $alternativeDefinitionId;
	protected $revisionId;
	
	public function __construct($alternativeDefinitionId, $revisionId) {
		$this->alternativeDefinitionId = $alternativeDefinitionId;
		$this->revisionId = $revisionId;
	}

	public function add($values) {
		$languageId = $values[0];
		$text = $values[1];
		
		if ($text != "") 
			addDefinedMeaningAlternativeDefinition($this->alternativeDefinitionId, $this->revisionId, $languageId, $text);
	}
	
	public function remove($tuple) {
		global
			$languageAttribute;
			
		$languageId = $tuple->getAttributeValue($languageAttribute);
		removeTranslatedDefinition($this->alternativeDefinitionId, $languageId);
	}
	
	public function update($tuple, $updatedValues) {
		global
			$languageAttribute;
			
		updateDefinedMeaningAlternativeDefinition($this->alternativeDefinitionId, $tuple->getAttributeValue($languageAttribute), $updatedValues['text']);
	}
}

class SynonymTranslationController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$languageId = $values[0];		
		$spelling = $values[1];
		$endemicMeaning = $values[2];
		
		if ($spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($this->definedMeaningId, $endemicMeaning);
		}
	}
	
	public function remove($tuple) {
		global
			$expressionAttribute;
		
		$expressionId = $tuple->getAttributeValue($expressionAttribute);
		removeSynonymOrTranslation($this->definedMeaningId, $expressionId);		
	}
	
	public function update($tuple, $updatedValues) {
		global
			$expressionAttribute;
			
		$expressionId = $tuple->getAttributeValue($expressionAttribute);
		$identicalMeaning = $updatedValues['endemic-meaning'];
		updateSynonymOrTranslation($this->definedMeaningId, $expressionId, $identicalMeaning);
	}
}

class DefinedMeaningRelationController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$relationTypeId = $values[0];
		$otherDefinedMeaningId = $values[1];
		  
		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($this->definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}	

	public function remove($tuple) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
			
		removeRelation($this->definedMeaningId, $tuple->getAttributeValue($relationTypeAttribute), $tuple->getAttributeValue($otherDefinedMeaningAttribute));
	}
	
	public function update($tuple, $updatedValues) {
	}
}

class DefinedMeaningAttributeController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($values) {
		$attributeId = $values[0];
		  
		if ($attributeId != 0)
			addRelation($this->definedMeaningId, 0, $attributeId);
	}	

	public function remove($tuple) {
		global
			$attributeAttribute;
			
		removeRelation($this->definedMeaningId, 0, $tuple->getAttributeValue($attributeAttribute));
	}
	
	public function update($tuple, $updatedValues) {
	}
}

class DefinedMeaningCollectionController implements PageElementController {
	protected $definedMeaningId;
	protected $revisionId;
	
	public function __construct($definedMeaningId, $revisionId) {
		$this->definedMeaningId = $definedMeaningId;
		$this->revisionId = $revisionId;
	}
	
	public function add($values) {
		$collectionId = $values[0];
		$internalId = $values[1];
		
		if ($internalId != "")
			addDefinedMeaningToCollection($this->definedMeaningId, $collectionId, $internalId, $this->revisionId);
	}	

	public function remove($tuple) {
		global
			$collectionAttribute;
			
		removeDefinedMeaningFromCollection($this->definedMeaningId, $tuple->getAttributeValue($collectionAttribute));
	}
	
	public function update($tuple, $updatedValues) {
		global
			$collectionAttribute;
		
		$collectionId = $tuple->getAttributeValue($collectionAttribute);
		$internalId = $updatedValues["internal-id"];
		
		if ($internalId != "")
			updateDefinedMeaningInCollection($this->definedMeaningId, $collectionId, $internalId);
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

		$res=$dbr->query("SELECT * from uw_expression_ns WHERE spelling=BINARY ".$dbr->addQuotes($wgTitle->getText()));

		while($row=$dbr->fetchObject($res)) {
			$expressionId = $row->expression_id;
			$definedMeaningIds = $this->getDefinedMeaningsForExpression($expressionId);

			$wgOut->addHTML($skin->editSectionLink($wgTitle, $expressionId));
			$wgOut->addHTML("<h2><i>Spelling</i>: $row->spelling - <i>Language:</i> ".$wgLanguageNames[$row->language_id]. "</h2>");

			$wgOut->addHTML('<ul>');
			foreach($definedMeaningIds as $definedMeaningId) {
				$wgOut->addHTML($skin->editSectionLink($wgTitle, "$expressionId-$definedMeaningId"));
				$wgOut->addHTML("<li><h3>Defined meaning</h3>");
				
 				$wgOut->addHTML('<div class="wiki-data-blocks">');
 					$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);
 					
 					foreach($pageElements as $pageElement) 
 						$this->displayPageElement($pageElement);
				$wgOut->addHTML('</div>');

				$wgOut->addHTML('<div class="clear-float"/>');

				$wgOut->addHTML('</li>');
			}
			$wgOut->addHTML('</ul>');
		}

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}
	
	function displayPageElement($pageElement) {
		addWikiDataBlock($pageElement->getCaption(), getRelationAsHTMLTable($pageElement->getRelationModel()));
	}
	
	function editPageElement($pageElement) {
		global
			$identicalMeaningAttribute;	
	
		$addId = "add-".$pageElement->getId();
		$removeId = "remove-".$pageElement->getId()."-";
		$updateId = "update-".$pageElement->getId()."-";
		
		$addRow = array();
		
		if ($pageElement->allowAdd()) {
			foreach($pageElement->getRelationModel()->getHeading()->attributes as $attribute) {
				if ($attribute == $identicalMeaningAttribute)
					$value = true;
				else
					$value = "";
					
				$addRow = array_merge($addRow, getInputFieldsForAttribute($addId . "-", $attribute, $value));
			}
		}
		
		addWikiDataBlock($pageElement->getCaption(), getRelationAsEditHTML($pageElement->getRelationModel(), $addId, $removeId, $updateId,
														$addRow, $pageElement->repeatInput(), 
														$pageElement->allowAdd(), $pageElement->allowRemove(), $pageElement->updatableHeading()));
	}
	
	function addRowForPageElement($pageElement, $postFix) {
		$addId = "add-".$pageElement->getId();
		$attributes = $pageElement->getRelationModel()->getHeading()->attributes;
		
		$values = array();
		
		foreach($attributes as $attribute)
			$values = array_merge($values, getFieldValuesForAttribute($addId . "-", $attribute, $postFix));
		
		$pageElement->getController()->add($values);
	}
	
	function savePageElement($pageElement) {
		global
			$wgRequest;
		
		$controller = $pageElement->getController();

		if ($controller) {
			if ($pageElement->allowAdd()) {
				$addId = "add-" . $pageElement->getId();
				$rowCount = $wgRequest->getInt($addId . '-RC');
			
				$this->addRowForPageElement($pageElement, "");
					
				for ($i = 2; $i <= $rowCount; $i++) 
					$this->addRowForPageElement($pageElement, '-' . $i);
			}
				
			$relationModel = $pageElement->getRelationModel();
			$key = $relationModel->getKey();

			if ($pageElement->allowRemove()) {
				$removeId = "remove-".$pageElement->getId()."-";
				
				for ($i = 0; $i < $relationModel->getTupleCount(); $i++) {
					$tuple = $relationModel->getTuple($i);
					
					if ($wgRequest->getCheck($removeId . getTupleKeyName($tuple, $key)))
						$controller->remove($tuple);				
				}
			}
			
			$updatableAttributes = $pageElement->updatableHeading()->attributes;
			
			if (count($updatableAttributes) > 0) {
				$updateId = "update-".$pageElement->getId()."-";	
			
				for ($i = 0; $i < $relationModel->getTupleCount(); $i++) {
					$tuple = $relationModel->getTuple($i);
					$tupleKeyName = getTupleKeyName($tuple, $key);
					$updatedValues = array();
					
					foreach($updatableAttributes as $attribute) {
						$values = getFieldValuesForAttribute($updateId . $tupleKeyName . '-', $attribute, "");
						$value = $values[0];
						
						if ($value != $tuple->getAttributeValue($attribute)) 
							$updatedValues[$attribute->id] = $value;
					}
					
					if (count($updatedValues) > 0)
						$controller->update($tuple, $updatedValues);
				}
			}
		}				
	}
	
	function getDefinedMeaningDefinitionPageElement($definedMeaningId, $revisionId) {
		global
			$textAttribute;
		
		return new DefaultPageElement("definition-$definedMeaningId", "Definition", 
										$this->getDefinedMeaningDefinitionRelation($definedMeaningId), 
										true, true, new Heading(array($textAttribute)),
										false,
										new DefinedMeaningDefinitionController($definedMeaningId, $revisionId));
	}
	
	function getDefinedMeaningAlternativeDefinitionPageElement($alternativeDefinitionId, $revisionId) {
		global
			$textAttribute;
		
		return new DefaultPageElement("alternative-definition-$alternativeDefinitionId", "Alternative definition", 
										$this->getDefinedMeaningAlternativeDefinitionRelation($alternativeDefinitionId), 
										true, true, new Heading(array($textAttribute)),
										false,
										new DefinedMeaningAlternativeDefinitionController($alternativeDefinitionId, $revisionId));
	}
	
	function getDefinedMeaningAlternativeDefinitionsPageElements($definedMeaningId, $revisionId) {
		global	
			$textAttribute;
			
		$result = array();
		
		foreach($this->getAlternativeDefinitions($definedMeaningId) as $alternativeDefinitionId)
			$result[] = $this->getDefinedMeaningAlternativeDefinitionPageElement($alternativeDefinitionId, $revisionId);
		
		return $result;
	}
	
	function getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId) {
		global
			$identicalMeaningAttribute;
		
		return new DefaultPageElement("synonym-translation-$definedMeaningId", "Translations and synonyms", 
										$this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId), 
										true, true, new Heading(array($identicalMeaningAttribute)), 
										true,
										new SynonymTranslationController($definedMeaningId));
	}
	
	function getDefinedMeaningRelationsPageElement($definedMeaningId) {
		return new DefaultPageElement("defined-meaning-relation-$definedMeaningId", "Relations", 
										$this->getDefinedMeaningRelationsRelation($definedMeaningId), 
										true, true, new Heading(array()),
										false,
										new DefinedMeaningRelationController($definedMeaningId));
	}
	
	function getDefinedMeaningAttributesPageElement($definedMeaningId) {
		return new DefaultPageElement("defined-meaning-attribute-$definedMeaningId", "Attributes", 
										$this->getDefinedMeaningAttributesRelation($definedMeaningId), 
										true, true, new Heading(array()),
										false,
										new DefinedMeaningAttributeController($definedMeaningId));
	}
	
	function getDefinedMeaningCollectionsPageElement($definedMeaningId, $revisionId) {
		global
			$internalIdAttribute;
		
		return new DefaultPageElement("defined-meaning-collection-$definedMeaningId", "Collection membership", 
										$this->getDefinedMeaningCollectionsRelation($definedMeaningId), 
										true, true, new Heading(array($internalIdAttribute)),
										false,
										new DefinedMeaningCollectionController($definedMeaningId, $revisionId));
	}
	
	function getDefinedMeaningPageElements($definedMeaningId, $expressionId) {
		$revisionId = getRevisionForExpressionId($expressionId);
		
		$result = array();
		$result[] = $this->getDefinedMeaningDefinitionPageElement($definedMeaningId, $revisionId);
		$result = array_merge($result, $this->getDefinedMeaningAlternativeDefinitionsPageElements($definedMeaningId, $revisionId));
		$result[] = $this->getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId);
		$result[] = $this->getDefinedMeaningRelationsPageElement($definedMeaningId);
		$result[] = $this->getDefinedMeaningAttributesPageElement($definedMeaningId);
		$result[] = $this->getDefinedMeaningCollectionsPageElement($definedMeaningId, $revisionId);
		
		return $result;
	}
	
	function getAlternativeDefinitions($definedMeaningId) {
		$result = array();
		$dbr =& wfGetDB(DB_SLAVE);	
		$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_alt_meaningtexts WHERE meaning_mid=$definedMeaningId AND is_latest_set=1");
		
		while ($definitionId = $dbr->fetchObject($queryResult))
			$result[] = $definitionId->meaning_text_tcid;
			
		return $result;
	}
	
	function saveForm($sectionArguments) {
		global 
			$wgTitle, $wgUser;
		
		if (count($sectionArguments) == 0)
			$this->saveSpellingForm($wgTitle->getText());
		else {
			$expressionId = $sectionArguments[0];			
			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);
			$this->saveExpressionForm($expressionId, $definedMeaningIds);
		}

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
		$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);
		
		foreach($pageElements as $pageElement)
			$this->savePageElement($pageElement);	
	}

	function edit() {
		global 
			$wgOut, $wgTitle, $wgUser, $wgRequest;
		
		$sectionToEdit = $wgRequest->getText('section');
		
		if ($sectionToEdit != "")
			$sectionArguments = explode("-", $sectionToEdit);
		else
			$sectionArguments = array();
		
		if ($wgRequest->getText('save') != '')
			$this->saveForm($sectionArguments);
					
		$userlang = $wgUser->getOption('language');
		$skin = $wgUser->getSkin();

		$wgOut->addHTML("Your user interface language preference: <b>$userlang</b> - " . $skin->makeLink("Special:Preferences", "set your preferences"));
		$wgOut->addHTML('<form method="post" action="">');

		if (count($sectionArguments) == 0)
			$this->displaySpellingEditForm($wgTitle->getText());
		else {
			$definedMeaningIds = $this->getDefinedMeaningIdsForSectionArguments($sectionArguments);							
			$this->displayPartialEditForm($sectionArguments[0], $definedMeaningIds);
		}

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
			$this->displayDefinedMeaningEditForm($definedMeaningId, $expressionId, $this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId));
			$wgOut->addHTML('</li>');
		}
		$wgOut->addHTML('</ul>');
	}
	
	function displayDefinedMeaningEditForm($definedMeaningId, $expressionId, $synonymAndTranslationTable) {
		global
			$wgOut, $wgLanguageNames;
		
		$wgOut->addHTML("<h3>Defined meaning</h3>");
	 	$wgOut->addHTML('<div class="wiki-data-blocks">');
	 	$pageElements = $this->getDefinedMeaningPageElements($definedMeaningId, $expressionId);	
	 	
	 	foreach($pageElements as $pageElement) 
	 		$this->editPageElement($pageElement);
	 	
		$wgOut->addHTML('</div>');
		$wgOut->addHTML('<div class="clear-float"/>');
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

		$relation = new ArrayRelation(new Heading(array($languageAttribute, $textAttribute)), 
										new Heading(array($languageAttribute)));
										
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

		$relation = new ArrayRelation(new Heading(array($languageAttribute, $textAttribute)), 
										new Heading(array($languageAttribute)));
										
		$queryResult = $dbr->query("SELECT language_id, old_text FROM translated_content tc, text t WHERE ".
									"tc.set_id=$alternativeDefinitionId AND tc.text_id=t.old_id AND tc.is_latest_set=1");
									
		while ($translatedDefinition = $dbr->fetchObject($queryResult)) 
			$relation->addTuple(array($translatedDefinition->language_id, $translatedDefinition->old_text));
		
		return $relation;
	}
	
	function getSynonymAndTranslationRelation($definedMeaningId, $skippedExpressionId) {
		global
			$expressionAttribute, $identicalMeaningAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$heading = new Heading(array($expressionAttribute, $identicalMeaningAttribute));

		$relation = new ArrayRelation($heading, $heading);
		$queryResult = $dbr->query("SELECT expression_id, endemic_meaning FROM uw_syntrans WHERE defined_meaning_id=$definedMeaningId AND expression_id!=$skippedExpressionId");
	
		while($synonymOrTranslation = $dbr->fetchObject($queryResult)) 
			$relation->addTuple(array($synonymOrTranslation->expression_id, $synonymOrTranslation->endemic_meaning));
		
		return $relation;
	}
	
	function getDefinedMeaningRelationsRelation($definedMeaningId) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
			
		$heading = new Heading(array($relationTypeAttribute, $otherDefinedMeaningAttribute));
		$relation = new ArrayRelation($heading, $heading);
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT relationtype_mid, meaning2_mid from uw_meaning_relations where meaning1_mid=$definedMeaningId and relationtype_mid!=0 and is_latest_set=1 ORDER BY relationtype_mid");
			
		while($definedMeaningRelation = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($definedMeaningRelation->relationtype_mid, $definedMeaningRelation->meaning2_mid)); 
		
		return $relation;
	}
	
	function getDefinedMeaningCollectionsRelation($definedMeaningId) {
		global
			$collectionAttribute, $internalIdAttribute;
			
		$heading = new Heading(array($collectionAttribute, $internalIdAttribute));
		$relation = new ArrayRelation($heading, new Heading(array($collectionAttribute)));
		
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT collection_id, internal_member_id FROM uw_collection_contents WHERE member_mid=$definedMeaningId AND is_latest_set=1");
			
		while($collection = $dbr->fetchObject($queryResult))
			$relation->addTuple(array($collection->collection_id, $collection->internal_member_id)); 
		
		return $relation;
	}
	
	function getDefinedMeaningAttributesRelation($definedMeaningId) {
		global
			$attributeAttribute;
			
		$heading = new Heading(array($attributeAttribute));
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
