<?php

class Expression {
	public $id;
	public $spelling;
	public $languageId;
	public $pageId;
	
	function __construct($id, $spelling, $languageId) {
		$this->id = $id;
		$this->spelling = $spelling;
		$this->languageId = $languageId;
	}
	
	function getPageTitle() {
		return str_replace(' ', '_', $this->spelling);
	}
	
	function createNewInDatabase() {
		$this->pageId = $this->createPage();
		createInitialRevisionForPage($this->pageId, 'Created by adding expression');
	}
	
	function createPage() {
		return createPage(16, $this->getPageTitle(), $this->languageId);
	}
	
	function isBoundToDefinedMeaning($definedMeaningId) {
		return expressionIsBoundToDefinedMeaning($definedMeaningId, $this->id);
	}

	function bindToDefinedMeaning($definedMeaningId, $endemicMeaning) {
		createSynonymOrTranslation($definedMeaningId, $this->id, $endemicMeaning);	
	}
	
	function assureIsBoundToDefinedMeaning($definedMeaningId, $endemicMeaning) {
		if (!$this->isBoundToDefinedMeaning($definedMeaningId)) 
			$this->bindToDefinedMeaning($definedMeaningId, $endemicMeaning);		
	}
}

function getExpressionId($spelling, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = 'select expression_id from uw_expression_ns where spelling=binary '. $dbr->addQuotes($spelling) . ' and language_id=' . $languageId;
	$queryResult = $dbr->query($sql);
	$expression = $dbr->fetchObject($queryResult);
	return $expression->expression_id;
}	

function createExpressionId($spelling, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$spelling = $dbr->addQuotes($spelling);
	$sql = "insert into uw_expression_ns(spelling,language_id) values($spelling, $languageId)";
	$dbr->query($sql);
	$expressionId = $dbr->insertId();
	 
	return $expressionId;		
}

function createPage($namespace, $title, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$title = $dbr->addQuotes($title);
	$timestamp = $dbr->timestamp(); 
	
	$sql = "insert into page(page_namespace,page_title,page_is_new,page_title_language_id,page_touched) ".
	       "values($namespace, $title, 1, $languageId, $timestamp)";
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function setPageLatestRevision($pageId, $latestRevision) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "update page set page_latest=$latestRevision where page_id=$pageId";	
	$dbr->query($sql);
}

function createInitialRevisionForPage($pageId, $comment) {
	global
		$wgUser;
		
	$dbr = &wfGetDB(DB_MASTER);
	$userId = $wgUser->getID();	
	$userName = $dbr->addQuotes($wgUser->getName());
	$comment = $dbr->addQuotes($comment);
	$timestamp = $dbr->timestamp();
	
	$sql = "insert into revision(rev_page,rev_comment,rev_user,rev_user_text,rev_timestamp) ".
	        "values($pageId, $comment, $userId, $userName, $timestamp)";
	$dbr->query($sql);

	$revisionId = $dbr->insertId();
	setPageLatestRevision($pageId, $revisionId); 
	
	return $revisionId;
}
	
function findExpression($spelling, $languageId) {
	if ($expressionId = getExpressionId($spelling, $languageId)) 
		return new Expression($expressionId, $spelling, $languageId);
	else
		return null;	
}

function createExpression($spelling, $languageId) {
	$expression = new Expression(createExpressionId($spelling, $languageId), $spelling, $languageId);
	$expression->createNewInDatabase();
	return $expression;
}

function findOrCreateExpression($spelling, $languageId) {
	if ($expression = findExpression($spelling, $languageId))
		return $expression;
	else
		return createExpression($spelling, $languageId);
}

function createSynonymOrTranslation($definedMeaningId, $expressionId, $endemicMeaning) {
	$dbr = &wfGetDB(DB_MASTER);
	$endemicMeaningInteger = (int) $endemicMeaning;	
	$sql = "insert into uw_syntrans(defined_meaning_id,expression_id,endemic_meaning) ".
	       "values($definedMeaningId, $expressionId, $endemicMeaningInteger)";
	$queryResult = $dbr->query($sql);
}

function expressionIsBoundToDefinedMeaning($definedMeaningId, $expressionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT expression_id FROM uw_syntrans WHERE expression_id=$expressionId AND defined_meaning_id=$definedMeaningId LIMIT 1");
	
	return $dbr->numRows($queryResult) > 0;
}

function addSynonymOrTranslation($spelling, $languageId, $definedMeaningId, $endemicMeaning) {
	$expression = findOrCreateExpression($spelling, $languageId);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $endemicMeaning);
}
	
function getMaximum($field, $table) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select max($field) as maximum from $table";
	$queryResult = $dbr->query($sql);
	
	if ($maximum = $dbr->fetchObject($queryResult))
		return $maximum->maximum;
	else
		return 0;
}

function relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_meaning_relations WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId");
	
	return $dbr->numRows($queryResult) > 0;
}

function createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "insert into uw_meaning_relations(meaning1_mid, meaning2_mid, relationtype_mid) " .
			"values($definedMeaning1Id, $definedMeaning2Id, $relationTypeId)";
	$dbr->query($sql);
}

function addNewRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
}

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	if (!relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) 
		createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
}

function removeRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("DELETE FROM uw_meaning_relations WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND ".
				"relationtype_mid=$relationTypeId LIMIT 1");
}

function removeSynonymOrTranslation($definedMeaningId, $expressionId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("DELETE FROM uw_syntrans WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId LIMIT 1");
}

function updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	$dbr =& wfGetDB(DB_MASTER);
	$identicalMeaningInteger = (int) $identicalMeaning;
	$dbr->query("UPDATE uw_syntrans SET endemic_meaning=$identicalMeaningInteger WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId LIMIT 1");	
}

function updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning dm, translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE dm.defined_meaning_id=$definedMeaningId ".
									"AND tc.set_id=dm.meaning_text_tcid AND tc.language_id=$languageId AND tc.text_id=t.old_id");	
}

function updateTranslatedText($textId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE ".
									"tc.set_id=$textId AND tc.language_id=$languageId AND tc.text_id=t.old_id");	
}
 
function createText($text) {
	$dbr = &wfGetDB(DB_MASTER);
	$text = $dbr->addQuotes($text);
	$sql = "insert into text(old_text) values($text)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function createTranslatedContent($setId, $languageId, $textId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "insert into translated_content(set_id,language_id,text_id) values($setId, $languageId, $textId)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function translatedTextExists($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT set_id FROM translated_content WHERE set_id=$definitionId AND language_id=$languageId");

	return $dbr->numRows($queryResult) > 0;	
}

function addTranslatedText($definitionId, $languageId, $definition) {
	$textId = createText($definition);
	createTranslatedContent($definitionId, $languageId, $textId);
}

function addTranslatedTextIfNotPresent($definitionId, $languageId, $definition) {
	if (!translatedTextExists($definitionId, $languageId)) 	
		addTranslatedText($definitionId, $languageId, $definition);
}

function getDefinedMeaningDefinitionId($definedMeaningId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=$definedMeaningId");

	return $dbr->fetchObject($queryResult)->meaning_text_tcid;
}

function updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning SET meaning_text_tcid=$definitionId WHERE defined_meaning_id=$definedMeaningId");
}

function newTranslatedContentId() {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM translated_content");
	
	return $dbr->fetchObject($queryResult)->max_id + 1;
}

function addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = newTranslatedContentId();		
	addTranslatedText($definitionId, $languageId, $text);
	updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId);
}

function addDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId == 0)
		addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text);
	else 
		addTranslatedTextIfNotPresent($definitionId, $languageId, $text);
}

function createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM uw_alt_meaningtexts");
	$setId = $dbr->fetchObject($queryResult)->max_id + 1;
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_alt_meaningtexts (set_id, meaning_mid, meaning_text_tcid) " .
			    "VALUES ($setId, $definedMeaningId, $translatedContentId)");
}

function addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function removeTranslatedText($textId, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE tc, t FROM translated_content AS tc, text AS t WHERE tc.set_id=$textId AND tc.language_id=$languageId AND tc.text_id=t.old_id");
}

function removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE am, tc, t FROM uw_alt_meaningtexts AS am, translated_content AS tc, text AS t WHERE am.meaning_mid=$definedMeaningId AND am.meaning_text_tcid=$definitionId AND tc.set_id=$definitionId AND tc.text_id=t.old_id");
}

function removeDefinedMeaningDefinition($definedMeaningId, $languageId) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		removeTranslatedText($definitionId, $languageId);
}

function definedMeaningInCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId");
	
	return $dbr->numRows($queryResult) > 0;
}

function addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_collection_contents(collection_id, member_mid, internal_member_id) " .
					"VALUES ($collectionId, $definedMeaningId, ". $dbr->addQuotes($internalId) .")");
}

function addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionId, $internalId) {
	if (!definedMeaningInCollection($definedMeaningId, $collectionId))
		addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId);
}

function getDefinedMeaningFromCollection($collectionId, $internalMemberId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid FROM uw_collection_contents WHERE collection_id=$collectionId AND internal_member_id=". $dbr->addQuotes($internalMemberId));
	
	if ($definedMeaningObject = $dbr->fetchObject($queryResult)) 
		return $definedMeaningObject->member_mid;
	else
		return null;
}

function removeDefinedMeaningFromCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId");	
}

function updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $internalId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_collection_contents SET internal_member_id=".$dbr->addQuotes($internalId) . 
				" WHERE collection_id=$collectionId AND member_mid=$definedMeaningId");	
}


function bootstrapCollection($collection, $languageId, $collectionType){
	$expression = findOrCreateExpression($collection, $languageId);
	$definedMeaningId = addDefinedMeaning($expression->id);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, true);
	addDefinedMeaningDefinition($definedMeaningId, $languageId, $collection);
	return addCollection($definedMeaningId, $collectionType);
}

function getCollectionMeaningId($collectionId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_mid FROM uw_collection_ns WHERE collection_id=$collectionId");
	
	return $dbr->fetchObject($queryResult)->collection_mid;	
}

function addCollection($definedMeaningId, $collectionType) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "INSERT INTO uw_collection_ns(collection_mid, collection_type) VALUES($definedMeaningId, '$collectionType')";
	$queryResult = $dbr->query($sql);
	
	return $dbr->insertId($queryResult);	
}

function addDefinedMeaning($definingExpressionId){
	$dbr = &wfGetDB(DB_MASTER);

	$sql = "INSERT INTO uw_defined_meaning(expression_id) values($definingExpressionId)";
	$queryResult = $dbr->query($sql);
	$meaningId = $dbr->insertId($queryResult);

	return $meaningId;
}

function createNewDefinedMeaning($definingExpressionId, $languageId, $text) {
	$definedMeaningId = addDefinedMeaning($definingExpressionId);
	createSynonymOrTranslation($definedMeaningId, $definingExpressionId, true);
	addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text);
	
	return $definedMeaningId;
}

function createDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $translatedContentId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_dm_text_attribute_values (defined_meaning_id, attribute_mid, value_tcid) " .
			    "VALUES ($definedMeaningId, $attributeId, $translatedContentId)");
}

function addDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $languageId, $text) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $translatedContentId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function removeDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $textId) {
	$dbr = &wfGetDB(DB_MASTER);
	
	$dbr->query("DELETE tc, t FROM translated_content AS tc, text AS t " .
				"WHERE tc.set_id=$textId AND tc.text_id=t.old_id");

	$dbr->query("DELETE FROM uw_dm_text_attribute_values " .
				"WHERE defined_meaning_id=$definedMeaningId AND attribute_mid=$attributeId AND value_tcid=$textId");	
}

function getDefinedMeaningDefinitionForLanguage($definedMeaningId, $languageId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM uw_defined_meaning as dm, translated_content as tc, text as t ".
								"WHERE dm.defined_meaning_id=$definedMeaningId AND " .
								"      dm.meaning_text_tcid=tc.set_id AND tc.language_id=$languageId AND " .
								"      t.old_id=tc.text_id");	
	
	if ($definition = $dbr->fetchObject($queryResult)) 
		return $definition->old_text;
	else	
		return "";
}

function getDefinedMeaningDefinitionForAnyLanguage($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM uw_defined_meaning as dm, translated_content as tc, text as t ".
								"WHERE dm.defined_meaning_id=$definedMeaningId AND " .
								"      dm.meaning_text_tcid=tc.set_id AND " .
								"      t.old_id=tc.text_id LIMIT 1");	
	
	if ($definition = $dbr->fetchObject($queryResult)) 
		return $definition->old_text;
	else	
		return "";
}

function getDefinedMeaningDefinition($definedMeaningId) {
	global
		$wgUser;
		
	$userLanguageId = getLanguageIdForCode($wgUser->getOption('language'));
	
	if ($userLanguageId > 0)
		$result = getDefinedMeaningDefinitionForLanguage($definedMeaningId, $userLanguageId);
	else
		$result = "";
	
	if ($result == "") {
		$result = getDefinedMeaningDefinitionForLanguage($definedMeaningId, 85);
		
		if ($result == "")
			$result = getDefinedMeaningDefinitionForAnyLanguage($definedMeaningId);
	}
	
	return $result;
}

function getCollectionContents($collectionId) {
	$dbr = & wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid,internal_member_id from uw_collection_contents " .
			                   "WHERE collection_id=$collectionId");
	$collectionContents = array();			                   
	while ($collectionEntry = $dbr->fetchObject($queryResult)) {
		$collectionContents[$collectionEntry->internal_member_id] = $collectionEntry->member_mid;
	}
	return $collectionContents;
} 

?>