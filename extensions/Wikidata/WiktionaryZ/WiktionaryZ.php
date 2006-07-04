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

function addDefinedMeaningAlternativeDefinitionTranslation($alternativeDefinitionId, $revisionId, $languageId, $text) {
	addTranslatedDefinition($alternativeDefinitionId, $languageId, $text, $revisionId);
}

function createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $revisionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM translated_content");
	$setId = $dbr->fetchObject($queryResult)->max_id + 1;
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_alt_meaningtexts (set_id, meaning_mid, meaning_text_tcid, is_latest_set, first_set, revision_id) " .
			    "VALUES ($setId, $definedMeaningId, $translatedContentId, 1, $setId, $revisionId)");
}

function addDefinedMeaningAlternativeDefinition($definedMeaningId, $revisionId, $languageId, $text) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $revisionId);
	addDefinedMeaningAlternativeDefinitionTranslation($translatedContentId, $revisionId, $languageId, $text);
}

function removeTranslatedDefinition($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE tc, t FROM translated_content AS tc, text AS t WHERE tc.set_id=$definitionId AND tc.language_id=$languageId AND tc.is_latest_set=1 AND tc.text_id=t.old_id");
}

function removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE am, tc, t FROM uw_alt_meaningtexts AS am, translated_content AS tc, text AS t WHERE am.meaning_mid=$definedMeaningId AND am.meaning_text_tcid=$definitionId AND am.is_latest_set=1 AND tc.set_id=$definitionId AND tc.is_latest_set=1 AND tc.text_id=t.old_id");
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
		$result = $dbr->fetchObject($queryResult)->max_set_id + 1;
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

	public function add($keyPath, $tuple) {
		global
			$languageAttribute, $textAttribute;
		
		$languageId = $tuple->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);
		
		if ($text != "") 
			addDefinedMeaningDefinition($this->definedMeaningId, $this->revisionId, $languageId, $text);
	}
	
	public function remove($keyPath) {
		global
			$languageAttribute;
			
		$languageId = $keyPath->peek(0)->getAttributeValue($languageAttribute);
		removeDefinedMeaningDefinition($this->definedMeaningId, $languageId);
	}
	
	public function update($keyPath, $tuple) {
		global
			$languageAttribute, $textAttribute;
			
		updateDefinedMeaningDefinition($this->definedMeaningId, $keyPath->peek(0)->getAttributeValue($languageAttribute), $tuple->getAttributeValue($textAttribute));
	}
}

class DefinedMeaningAlternativeDefinitionsController {
	protected $definedMeaningId;
	protected $revisionId;
	
	public function __construct($definedMeaningId, $revisionId) {
		$this->definedMeaningId = $definedMeaningId;
		$this->revisionId = $revisionId;
	}

	public function add($keyPath, $tuple)  {
		global
			$alternativeDefinitionAttribute, $languageAttribute, $textAttribute;
			
		$alternativeDefinition = $tuple->getAttributeValue($alternativeDefinitionAttribute);
		
		if ($alternativeDefinition->getTupleCount() > 0) {	
			$definitionTuple = $alternativeDefinition->getTuple(0);
			
			$languageId = $definitionTuple->getAttributeValue($languageAttribute);
			$text = $definitionTuple->getAttributeValue($textAttribute);
			
			if ($text != '')
				addDefinedMeaningAlternativeDefinition($this->definedMeaningId, $this->revisionId, $languageId, $text);
		}
	}
	
	public function remove($keyPath) {
		global
			$definitionIdAttribute;
			
		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		removeDefinedMeaningAlternativeDefinition($this->definedMeaningId, $definitionId);
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningAlternativeDefinitionController implements PageElementController {
	protected $revisionId;
	
	public function __construct($revisionId) {
		$this->revisionId = $revisionId;
	}

	public function add($keyPath, $tuple) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute;

		$definitionId = $keyPath->peek(0)->getAttributeValue($definitionIdAttribute);
		$languageId = $tuple->getAttributeValue($languageAttribute);
		$text = $tuple->getAttributeValue($textAttribute);

		if ($text != "")
			addDefinedMeaningAlternativeDefinitionTranslation($definitionId, $this->revisionId, $languageId, $text);
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
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($keyPath, $tuple) {
		global
			$expressionAttribute, $languageAttribute, $spellingAttribute, $identicalMeaningAttribute;

		$expressionTuple = $tuple->getAttributeValue($expressionAttribute);
		$languageId = $expressionTuple->getAttributeValue($languageAttribute);		
		$spelling = $expressionTuple->getAttributeValue($spellingAttribute);
		$identicalMeaning = $tuple->getAttributeValue($identicalMeaningAttribute);

		if ($spelling != '') {
			$expression = findOrCreateExpression($spelling, $languageId);
			$expression->assureIsBoundToDefinedMeaning($this->definedMeaningId, $identicalMeaning);
		}
	}
	
	public function remove($keyPath) {
		global
			$expressionIdAttribute;
		
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		removeSynonymOrTranslation($this->definedMeaningId, $expressionId);		
	}
	
	public function update($keyPath, $tuple) {
		global
			$expressionIdAttribute, $identicalMeaningAttribute;
			
		$expressionId = $keyPath->peek(0)->getAttributeValue($expressionIdAttribute);
		$identicalMeaning = $tuple->getAttributeValue($identicalMeaningAttribute);
		updateSynonymOrTranslation($this->definedMeaningId, $expressionId, $identicalMeaning);
	}
}

class DefinedMeaningRelationController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($keyPath, $tuple) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
		
		$relationTypeId = $tuple->getAttributeValue($relationTypeAttribute);
		$otherDefinedMeaningId = $tuple->getAttributeValue($otherDefinedMeaningAttribute);
		  
		if ($relationTypeId != 0 && $otherDefinedMeaningId != 0)
			addRelation($this->definedMeaningId, $relationTypeId, $otherDefinedMeaningId);
	}	

	public function remove($keyPath) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
			
		$tuple = $keyPath->peek(0);	
		removeRelation($this->definedMeaningId, $tuple->getAttributeValue($relationTypeAttribute), $tuple->getAttributeValue($otherDefinedMeaningAttribute));
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningAttributeController implements PageElementController {
	protected $definedMeaningId;
	
	public function __construct($definedMeaningId) {
		$this->definedMeaningId = $definedMeaningId;
	}
	
	public function add($keyPath, $tuple) {
		global
			$attributeAttribute;
		
		$attributeId = $tuple->getAttributeValue($attributeAttribute);
		  
		if ($attributeId != 0)
			addRelation($this->definedMeaningId, 0, $attributeId);
	}	

	public function remove($keyPath) {
		global
			$attributeAttribute;
			
		removeRelation($this->definedMeaningId, 0, $keyPath->peek(0)->getAttributeValue($attributeAttribute));
	}
	
	public function update($keyPath, $tuple) {
	}
}

class DefinedMeaningCollectionController implements PageElementController {
	protected $definedMeaningId;
	protected $revisionId;
	
	public function __construct($definedMeaningId, $revisionId) {
		$this->definedMeaningId = $definedMeaningId;
		$this->revisionId = $revisionId;
	}
	
	public function add($keyPath, $tuple) {
		global
			$collectionAttribute, $sourceIdentifierAttribute;
		
		$collectionId = $tuple->getAttributeValue($collectionAttribute);
		$internalId = $tuple->getAttributeValue($sourceIdentifierAttribute);
		
		if ($internalId != "")
			addDefinedMeaningToCollection($this->definedMeaningId, $collectionId, $internalId, $this->revisionId);
	}	

	public function remove($keyPath) {
		global
			$collectionAttribute;
			
		removeDefinedMeaningFromCollection($this->definedMeaningId, $keyPath->peek(0)->getAttributeValue($collectionAttribute));
	}
	
	public function update($keyPath, $tuple) {
		global
			$collectionAttribute, $sourceIdentifierAttribute;
		
		$collectionId = $keyPath->peek(0)->getAttributeValue($collectionAttribute);
		$sourceId = $tuple->getAttributeValue($sourceIdentifierAttribute);
		
		if ($sourceId != "")
			updateDefinedMeaningInCollection($this->definedMeaningId, $collectionId, $sourceId);
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
		//addWikiDataBlock($pageElement->getCaption(), getRelationAsHTMLTable($pageElement->getDisplayRelation()));
		addWikiDataBlock($pageElement->getCaption(), $pageElement->getViewer()->view($pageElement->getId(), new TupleStack(), $pageElement->getRelation()));
	}
	
	function editPageElement($pageElement) {
		global
			$identicalMeaningAttribute;	

		addWikiDataBlock($pageElement->getCaption(), $pageElement->getEditor()->edit($pageElement->getId(), new TupleStack(), $pageElement->getRelation())); 
//		getRelationAsEditHTML($pageElement->getRelation(), $pageElement->getDisplayRelation(), 
//														$pageElement->getId(),
//														$pageElement->repeatInput(), 
//														$pageElement->allowAdd(), $pageElement->allowRemove(), $pageElement->updatableHeading()));
	}
	
//	function getInputTuple($inputId, $heading, $postFix = "") {
//		$result = new ArrayTuple($heading);
//		
//		foreach($heading->attributes as $attribute) {
//			$type = $attribute->type;
//			
//			if (is_a($type, TupleType))
//				$result->setAttributeValue($attribute, $this->getInputTuple($inputId . $attribute->id . '-', $type->getHeading(), $postFix));
//			else
//				$result->setAttributeValue($attribute, getInputFieldValueForType($inputId . $attribute->id . $postFix, $type));
//		}
//		
//		return $result;
//	}
//	
//	function addRowForPageElement($pageElement, $postFix) {
//		$addId = "add-".$pageElement->getId()."-";
//		$heading = $pageElement->getDisplayRelation()->getHeading();
//		$addTuple = $this->getInputTuple($addId, $heading, $postFix);
//		
//		$pageElement->getController()->add($addTuple);
//	}
	
	function savePageElement($pageElement) {
		global
			$wgRequest;

		$pageElement->getEditor()->save($pageElement->getId(), new TupleStack(), $pageElement->getRelation());		
//		$controller = $pageElement->getController();
//
//		if ($controller) {
//			if ($pageElement->allowAdd()) {
//				$addId = "add-" . $pageElement->getId();
//				$rowCount = $wgRequest->getInt($addId . '-RC');
//			
//				$this->addRowForPageElement($pageElement, "");
//					
//				for ($i = 2; $i <= $rowCount; $i++) 
//					$this->addRowForPageElement($pageElement, '-' . $i);
//			}
//				
//			$relation = $pageElement->getRelation();
//			$key = $relation->getKey();
//
//			if ($pageElement->allowRemove()) {
//				$removeId = "remove-".$pageElement->getId()."-";
//				
//				for ($i = 0; $i < $relation->getTupleCount(); $i++) {
//					$tuple = $relation->getTuple($i);
//					
//					if ($wgRequest->getCheck($removeId . getTupleKeyName($tuple, $key)))
//						$controller->remove($tuple);				
//				}
//			}
//
//			$updatableHeading = $pageElement->updatableHeading();
//						
//			if (count($updatableHeading->attributes) > 0) {
//				$displayRelation = $pageElement->getDisplayRelation();
//				$updateId = "update-".$pageElement->getId()."-";	
//	
//				for ($i = 0; $i < $relation->getTupleCount(); $i++) {
//					$tuple = $relation->getTuple($i);
//					$tupleKeyName = getTupleKeyName($tuple, $key);
//					$updatedTuple = $this->getInputTuple($updateId . $tupleKeyName . '-', $updatableHeading);
//					
//					if (!equalTuples($updatableHeading, $displayRelation->getTuple($i), $updatedTuple))
//						$controller->update($tuple, $updatedTuple);
//				}
//			}
//		}				
	}
	
	function getDefinedMeaningDefinitionPageElement($definedMeaningId, $revisionId) {
		global
			$languageAttribute, $textAttribute;
		
		$relation = $this->getDefinedMeaningDefinitionRelation($definedMeaningId);
		
		$editor = new TableEditor(null, true, true, false, new DefinedMeaningDefinitionController($definedMeaningId, $revisionId));
		$editor->addEditor(new LanguageEditor($languageAttribute, false, true));
		$editor->addEditor(new TextEditor($textAttribute, true, true));
		
		return new DefaultPageElement("definition-$definedMeaningId", "Definition", 
										$relation, 
										$editor, $editor);
	}
	
	function getDefinedMeaningAlternativeDefinitionsPageElement($definedMeaningId, $revisionId) {
		global
			$definitionIdAttribute, $alternativeDefinitionAttribute, $languageAttribute, $textAttribute;
					
		$relation = $this->getAlternativeDefinitionRelation($definedMeaningId);
		
		$alternativeDefinitionEditor = new TableEditor($alternativeDefinitionAttribute, true, true, true, new DefinedMeaningAlternativeDefinitionController($revisionId));
		$alternativeDefinitionEditor->addEditor(new LanguageEditor($languageAttribute, false, true)); 
		$alternativeDefinitionEditor->addEditor(new TextEditor($textAttribute, true, true)); 
				
		$editor = new TableEditor(null, true, true, false, new DefinedMeaningAlternativeDefinitionsController($definedMeaningId, $revisionId));
		$editor->addEditor($alternativeDefinitionEditor);
		
		return new DefaultPageElement("alternative-definitions", "Alternative definitions", 
										$relation, 
										$editor, $editor);
	}
	
	function getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId) {
		global
			$identicalMeaningAttribute, $expressionIdAttribute, $expressionAttribute, $languageAttribute, $spellingAttribute;
		
		$relation = $this->getSynonymAndTranslationRelation($definedMeaningId, $expressionId);

		$expressionEditor = new TupleTableCellEditor($expressionAttribute);
		$expressionEditor->addEditor(new LanguageEditor($languageAttribute, false, true));
		$expressionEditor->addEditor(new SpellingEditor($spellingAttribute, false, true));
			
		$tableEditor = new TableEditor(null, true, true, false, new SynonymTranslationController($definedMeaningId));
		$tableEditor->addEditor($expressionEditor);
		$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, true, true, true));
		
		return new DefaultPageElement("synonym-translation-$definedMeaningId", "Translations and synonyms", 
										$relation, 
										$tableEditor, $tableEditor);
	}
	
	function getDefinedMeaningRelationsPageElement($definedMeaningId) {
		global
			$relationTypeAttribute, $otherDefinedMeaningAttribute;
		
		$relation = $this->getDefinedMeaningRelationsRelation($definedMeaningId);
		
		$editor = new TableEditor(null, true, true, false, new DefinedMeaningRelationController($definedMeaningId));
		$editor->addEditor(new RelationTypeEditor($relationTypeAttribute, false, true));
		$editor->addEditor(new DefinedMeaningEditor($otherDefinedMeaningAttribute, false, true));
		
		return new DefaultPageElement("defined-meaning-relation-$definedMeaningId", "Relations", 
										$relation, 
										$editor, $editor);
	}
	
	function getDefinedMeaningAttributesPageElement($definedMeaningId) {
		global
			$attributeAttribute;
			
		$relation = $this->getDefinedMeaningAttributesRelation($definedMeaningId);
		
		$editor = new TableEditor(null, true, true, false, new DefinedMeaningAttributeController($definedMeaningId));
		$editor->addEditor(new AttributeEditor($attributeAttribute, false, true));

		return new DefaultPageElement("defined-meaning-attribute-$definedMeaningId", "Attributes", 
										$relation, 
										$editor, $editor);
	}
	
	function getDefinedMeaningCollectionsPageElement($definedMeaningId, $revisionId) {
		global
			$collectionAttribute, $sourceIdentifierAttribute;
		
		$relation = $this->getDefinedMeaningCollectionsRelation($definedMeaningId);
		
		$editor = new TableEditor(null, true, true, false, new DefinedMeaningCollectionController($definedMeaningId, $revisionId));
		$editor->addEditor(new CollectionEditor($collectionAttribute, false, true));
		$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, true, true));

		return new DefaultPageElement("defined-meaning-collection-$definedMeaningId", "Collection membership", 
										$relation, 
										$editor, $editor);
	}
	
	function getDefinedMeaningPageElements($definedMeaningId, $expressionId) {
		$revisionId = getRevisionForExpressionId($expressionId);
		
		$result = array();
		$result[] = $this->getDefinedMeaningDefinitionPageElement($definedMeaningId, $revisionId);
		$result[] = $this->getDefinedMeaningAlternativeDefinitionsPageElement($definedMeaningId, $revisionId);
		$result[] =	$this->getSynonymsAndTranslationsPageElement($definedMeaningId, $expressionId);
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
	
	function getAlternativeDefinitionRelation($definedMeaningId) {
		global
			$definitionIdAttribute, $languageAttribute, $textAttribute, $alternativeDefinitionAttribute;
		
		$alternativeDefinitionHeading = new Heading($languageAttribute, $textAttribute);
		$relation = new ArrayRelation(new Heading($definitionIdAttribute, $alternativeDefinitionAttribute), new Heading($definitionIdAttribute));
		
		$alternativeDefinitions = $this->getAlternativeDefinitions($definedMeaningId);
		
		foreach($alternativeDefinitions as $alternativeDefinition)
			$relation->addTuple(array($alternativeDefinition, $this->getDefinedMeaningAlternativeDefinitionRelation($alternativeDefinition)));
		
		return $relation;
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
	
	function getDefinedMeaningCollectionsRelation($definedMeaningId) {
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
